<?php

function handleBilling($reference, $paymethod)
{
    if ($_SESSION["billinginfo"] != null && $_SESSION["billinginfo"] == true) {
        //send Mail with facture
        require_once 'vendor/autoload.php';
        require_once('Utils/maileren.php');
        $mailer = new MailerPerso;
        $mailer->sendBillingMail($reference, $paymethod);

        //register in DB

    }
}
function findPrice($origin, $destination, $journey_type, $pickupTime, $dropTime)
{
    //NOT USED
    // 1 single way algo with depart from zellik then origin then destination then back to BRU AIRPORT
    // 2 charing duration 0.15 cents per minutes
    // 3 charing a default tarrif if current tarif is less than 39 
    // 4 charing 5 euros extra if travel between 1h30 and 9h30
    // 5 charging 10e for 4 passengers
    // charging double for two way
    // adding 10e if pickup from airport


    $zelik = "Frans Timmermansstraat, 1731 Asse";
    $bru = "Brussels Airport Zaventem (BRU)";
    $bxl = "Tour du Midi, 1060 Saint-Gilles";

    list($distance_from_zelik, $duration_from_zellik) = calculateDistance($bxl, $origin);
    list($distance,  $duration) = calculateDistance($origin, $destination);
    list($distance_backto_zelik, $duration_backto_zellik) = calculateDistance($destination, $bxl);
    $total_distance = $distance_from_zelik +  $distance + $distance_backto_zelik;
    $total_duration = $duration_from_zellik + $duration + $duration_backto_zellik;
    $duration = gmdate("H:i", $duration);


    $_SESSION['duration'] = $duration;

    $_SESSION['distance'] = (int) ($distance / 1000);
    $total_distance = $total_distance / 1000; //changing to kms
    $price = ($total_distance * 0.495) + (($total_duration / 60) * 0.18); //chargin 0.18 cents per minutes 19/60
    $_SESSION['total_distance'] = $total_distance;
    $_SESSION['total_duration'] = $total_duration;
    //var_dump( $_SESSION['total_distance']);

    $pickupTimeInteger = str_replace(":", "", $pickupTime);

    if ($price < 45) {
        $price = 45;       
    }
    if ($price < 50 && $distance > 19999 && $distance < 24999) {
        $price=50;
    }
    if ($price < 55 && $distance >= 24999) {
        $price=55;
    }
    $origin_is_airport = isairport($origin);
    $dest_is_airport = isairport($destination);

    if (!$origin_is_airport && $price < 49 && ($pickupTimeInteger < "0930" && $pickupTimeInteger > "0130")) {
        $price = $price + 4; //adding extra for night time
    }
    // if ($origin_is_airport) {
    //     if (in_array(find_postalcode($destination), array('1500', '1560', '1600', '1620', '1650', '1180', '1190'))) {
    //         $price = $price + 7;
    //     }
    // } else {
    //     if (in_array(find_postalcode($origin), array('1500', '1560', '1600', '1620', '1650', '1180', '1190'))) {
    //         $price = $price + 7;
    //     }
    // }

    if ($journey_type == "two_way") {
        $_SESSION['volnumobli'] = true;
        $price = $price * 2;
    }

    if ($_SESSION["passengers"] > 4) {
        //$price=$price*1.30; //if passengers are 5
    }

    if (isairport($origin)) {
        if ($journey_type != "two_way") {
            $price = $price + 4;
        }

        $_SESSION['is_airport'] = true;
        $_SESSION['volnumobli'] = true;
    } else {
        $_SESSION['is_airport'] = false;
        //$_SESSION['volnumnobli']=true;
    }
    if ($_SESSION["extrastopcheck"]) {           
        $price=$price+20;
    }



    $_SESSION['price'] = $price;
    // must round to 2 decimals else stripe will give error 
    $_SESSION['price'] = round($_SESSION['price'], 2);

    // info msg to slack
    slack($_SESSION['price'], $origin, $destination, $journey_type, $pickupTime, "");

    return $_SESSION['price'];
}
function registerCalendar($origin, $dest, $date, $time, $phone, $email, $pay, $retDate, $retTime, $vehicule)
{
    try {
        //code...
        require_once 'vendor/autoload.php';
        require_once 'mycomposer/vendor/autoload.php';
        $client = new Google_Client();
        //The json file you got after creating the service account
        putenv('GOOGLE_APPLICATION_CREDENTIALS=mycomposer/abiding-ion-375518-957dd40c14fa.json');
        $client->useApplicationDefaultCredentials();
        $client->setApplicationName("Reservations");
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline');
        $service = new Google_Service_Calendar($client);
        $event = new Google_Service_Calendar_Event(array(
            'summary' => $vehicule.' Client: ' . $phone . ' ' . $email,
            'location' =>  $_SESSION['origin'],
            'description' => 'Reservation ' .  $origin . ' ---> ' . $dest . '   Paiement en ' . $pay,
            'start' => array(
                'dateTime' => date(DATE_ISO8601, strtotime($date . ' ' . $time))
            ),
            'end' => array(
                'dateTime' => date(DATE_ISO8601, strtotime($date . ' ' . $time))
            ),
        ));

        $calendarId = '313c81e6141a711edaeb8153985570ecd20213163073f83bbb0fb2430f72d00b@group.calendar.google.com';
        $event = $service->events->insert($calendarId, $event);

        //event saved now checking if return then save a second event
        if ($retDate != NULL) {
            $event2 = new Google_Service_Calendar_Event(array(
                'summary' => 'Client: ' . $phone . ' ' . $email,
                'location' =>  $_SESSION['origin'],
                'description' => 'Retour Reservation ' .  $dest . ' ---> ' . $origin . '\nPaiement en ' . $pay,
                'start' => array(
                    'dateTime' => date(DATE_ISO8601, strtotime($retDate . ' ' . $retTime))
                ),
                'end' => array(
                    'dateTime' => date(DATE_ISO8601, strtotime($retDate . ' ' . $retTime))
                ),
            ));

            $event = $service->events->insert($calendarId, $event2);
        }
    } catch (\Throwable $th) {
        //throw $th;
        error_log($th);
    }
}
function slack($price, $origin, $destination, $journey_type, $pickupTime, $page)
{
    $global_config = require('utils/const.php');
    $apikey = $global_config['google_api_key'];
    $whslack = $global_config['slack_webhook'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
    $curl_result;
    define('SLACK_WEBHOOK', $whslack);
    $msg2 = array('Origin' => $origin, "Destination" => $destination, "Price" => $price, "Type" => $journey_type, "Pickup" => $_SESSION['date'] . " " . $pickupTime, "IP"  => $ip, "City IP" => $details->city);

    $message = array('payload' => json_encode(array('text' => "\n" . $origin .  "\n\n =>   " . $page . " " . $price . "€     temps total " . gmdate("H:i", $_SESSION['total_duration']) . " min  distance" . round($_SESSION['distance'], 0)  . "KM   distance totale" . round($_SESSION['total_distance'], 0)  . "KM      " . $_SESSION['lang']  . "\n\n"  . $destination . "  "  .  " \n\n Pickup "  . $_SESSION['date'] . "   " . $pickupTime  . " " . " \n IP "   . $ip .  "  " . $details->city . "   " . $journey_type  . "\n \n _______________________________________ \n")));

    $c = curl_init(SLACK_WEBHOOK);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($c, CURLOPT_POST, true);
    curl_setopt($c, CURLOPT_POSTFIELDS, $message);
    $curl_result = curl_exec($c);
    curl_close($c);
}

function isairport($place)
{
    $global_config = require('utils/const.php');
    $apikey = $global_config['google_api_key'];
    //find place id first

    $myURL = "https://maps.googleapis.com/maps/api/geocode/json?";
    $options = array(
        "sensor" => "false",
        "address" => $place,
        "inputtype" => "textquery",
        "key" => $apikey
    );

    $myURL .= http_build_query($options, '', '&');
    $myData = file_get_contents($myURL);
    $details1 = json_decode($myData, TRUE);
    //var_dump($details1['candidates'][0]['place_id']);
    // var_dump($details1);
    $arr = $details1["results"][0]["address_components"];
    if ($arr == NULL) {
        return false;
    }
    $isairpot = false;

    foreach ($arr as $value) {
        if ($value["types"][0] == "airport") {
            return true;
        }
    }
    return false;
}
function in_bxl($place)
{
    $postcode = find_postalcode($place);
    // checking if postal code is inside bxl from 1000 till 1250 are bxl postal codes
    if ($postcode > 999 && $postcode < 1250) {
        return true;
    }
    return false;
}
function find_postalcode($place)
{
    $global_config = require('utils/const.php');
    $apikey = $global_config['google_api_key'];
    //find place id first

    $myURL = "https://maps.googleapis.com/maps/api/geocode/json?";
    $options = array(
        "sensor" => "false",
        "address" => $place,
        "inputtype" => "textquery",
        "key" => $apikey
    );

    $myURL .= http_build_query($options, '', '&');
    $myData = file_get_contents($myURL);
    $details1 = json_decode($myData, TRUE);
    //var_dump($details1['candidates'][0]['place_id']);
    // var_dump($details1);
    $arr = $details1["results"][0]["address_components"];
    $postcode = "";
    foreach ($arr as $value) {
        if ($value["types"][0] == "postal_code") {
            $postcode = $value["short_name"];
            break;
        }
    }
    $postcode = (int) $postcode;
    return $postcode;
}
function calculateDistance($origin, $destination)
{
    $global_config = require('utils/const.php');
    $apikey = $global_config['google_api_key'];
    $myURL = "https://maps.googleapis.com/maps/api/distancematrix/json?";
    $options = array(
        "sensor" => "false",
        "origins" => $origin,
        "destinations" => $destination,
        "language" => "en-EN",
        "key" => $apikey
    );

    $myURL .= http_build_query($options, '', '&');
    $myData = file_get_contents($myURL);
    $details = json_decode($myData, TRUE);
    //getPlaceID($destination);

    $distance = (int) $details['rows']['0']['elements']['0']['distance']['value'];
    $duration = (float) $details['rows']['0']['elements']['0']['duration']['value'];
    if ($distance == NULL || $distance == 0) {
        header("Location:" . $global_config['base_url']);
        die();
    }
    $duration_seconds = $duration;
    $duration = gmdate("H:i", $duration);
    $_SESSION['duration'] = $duration;
    return array($distance, $duration_seconds);
}