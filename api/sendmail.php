<?php

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('entering sendmail.php');
    // Read raw data from the request body
    $jsonData = file_get_contents('php://input');

    // Decode JSON data to associative array
    $data = json_decode($jsonData, true);

    $authcode = $data['authcode'] ?? null;
    
    if($authcode == null || $authcode != 'dsnf2323*#$'){
        http_response_code(404); // Method Not Allowed
        echo json_encode(['error' => 'Method Not authenticated ']);
    }

    // Extract parameters from the decoded JSON data
    $id = $data['id'] ?? null;
    $distance = $data['distance'] ?? null;
    $paymethod = $data['paymethod'] ?? null;
    $babyseat = $data['babyseat'] ?? null;
    $is_airport = $data['is_airport'] ?? null;
    $journey_type = $data['journey_type'] ?? null;
    $pickup_time_ret = $data['pickup_time_ret'] ?? null;
    $date_ret = $data['date_ret'] ?? null;
    $flight_num = $data['flight_num'] ?? null;
    $fname = $data['fname'] ?? null;
    $lname = $data['lname'] ?? null;
    $comments = $data['comments'] ?? null;
    $passengers = $data['passengers'] ?? null;
    $bags = $data['bags'] ?? null;
    $car_type = $data['car_type'] ?? null;
    $email = $data['email'] ?? null;
    $price = $data['price'] ?? null;
    $origin = $data['origin'] ?? null;
    $destination = $data['destination'] ?? null;
    $date = $data['date'] ?? null;
    $time = $data['time'] ?? null;
    $phone = $data['phone'] ?? null;

    // Include the file where sendMail3 function is defined
    
    // Call sendMail3 function with extracted parameters
    sendMail3($id, $distance, $paymethod, $babyseat, $is_airport, $journey_type,
        $pickup_time_ret, $date_ret, $flight_num, $fname, $lname, $comments, $passengers, $bags, $car_type,
        $email, $price, $origin, $destination, $date, $time, $phone);

    // Optionally, you can send a response back to the client indicating success or failure
    echo json_encode(['status' => 'success']);
} else {
    // Handle cases where the request method is not POST (optional)
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method Not Allowed']);
}


function sendMail3($id, $distance,$paymethod,$babyseat,$is_airport,$journey_type,$pickup_time_ret,$date_ret,$flight_num,$fname, $lname, $comments, $passengers,$bags,$car_type, $email, $price, $origin, $destination, $date, $time, $phone){
    require_once '../vendor/autoload.php';
    error_log('sending email 2 to: ' . $email);
    $book_id = " AIRPORTAXI-" . $id;
    $datetime = date("d-m-Y à H:i:s");
    $email_admin = "airportaxiofficial23@gmail.com";

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    //Recipients
    $mail->setFrom('info@airportaxiofficial.be', 'AirporTaxi Official');
    // $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    $mail->addAddress($email); // Name is optional
    $mail->addBcc("airportaxiofficial.be+a3b249f5bb@invite.trustpilot.com"); // trustpilot auto invite after 7 days
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    $mail->addBCC('info@airportaxiofficial.be');
    $mail->addBCC($email_admin);    
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Reservation AirporTaxi Official Turkey ' . $book_id;
    $partie_entete = "<html>\n<head>\n<title>Formulaire de réservation - Airportaxiofficial</title>\n<meta http-equiv=Content-Type content=text/html; charset=UTF-8>\n<style>body{font:400 15px Verdana,sans-serif;background:#f4f4f4}table{border:1px solid #777;width:80%;margin:15px auto 30px}table td{border-bottom:1px dotted #333;color:#036;padding:10px}h3{color:#fbc318}</style>\n</head>\n<body>\n";

    // Partie HTML de l'e-mail...
    $locationmsg="";
    // Texte envoyé uniquement au client...

    $message_client .= "<center><img src='https://airportaxiofficial.be/images/logo.png' alt='Airportaxiofficial' style='max-height:60px;'/></center><br /><br />";
    $message_client .= "<h3>Confirmation de votre réservation</h3>";
    $message_client .= "<hr />";
    $message_client .= "Bonjour  <b>" . $fname . " " . $lname . "</b><br /><br />";
    $message_client .= "Vous avez effectué une réservation sur notre site le <span style='text-decoration:underline;'>" . $datetime . "</span> et nous vous en remercions. Vous trouverez ci-dessous le détail de votre commande.<br /><br />";
    $message_client .= "Conservez précieusement votre référence, elle pourra vous être demandée : <b>" . $book_id . "</b><br /><br />";

    $message_admin .= "<center><img src='https://airportaxiofficial.be/images/logo.png' alt='Airportaxiofficial' style='max-height:60px;'/></center><br /><br />";
    $message_admin .= "<h3>Confirmation de votre réservation</h3>";
    $message_admin .= "<hr />";
    $message_admin .= "Bonjour <br /><br />";
    $message_admin .= "Une réservation avec la référence <b>" . $book_id . "</b> vient d'être effectuée sur votre site le <span style='text-decoration:underline;'>" . $datetime . "</span> par <b>" . $fname . " " . $lname . "</b>. Vous trouverez ci-dessous le détail de la commande.<br /><br />";


    $partie_html .= "<h3><i class='icon-info-circle'></i> Informations sur le trajet</h3>";
    $partie_html .= "<table>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Adresse de départ</td><td><b>" . $origin . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Adresse de destination</td><td><b>" . $destination . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Distance</td><td><b>" . $distance . " KM</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Date aller</td><td><b>" . $date . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;border-bottom:none;'>Horaire</td><td style='border-bottom:none;'><b>" . $time . "</b></td></tr>";
    $partie_html .= "</table>";

    $partie_html .= "<hr />";

    $partie_html .= "<h3><i class='icon-user'></i> Identité du client</h3>";
    $partie_html .= "<table>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Nom</td><td><b>" . $lname . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Prénom</td><td><b>" . $fname . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Email</td><td><b>" . $email . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;border-bottom:none;'>Téléphone</td><td style='border-bottom:none;'><b>" . $phone . "</b></td></tr>";
    $partie_html .= "</table>";

    $partie_html .= "<hr />";

    $partie_html .= "<h3><i class='icon-car'></i> Prestation demandée</h3>";
    $partie_html .= "<table>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Choix du véhicule</td><td><b>" . $car_type . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Nbre de passagers</td><td><b>" . $passengers . "</b></td></tr>";
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Bagages</td><td><b>" . $bags . "</b></td></tr>";

    if ($babyseat != "") {
        $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Siège bébé</td><td><b>" . $babyseat . "</b></td></tr>";
    }

    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Type de trajet</td><td><b>" . $journey_type . "</b></td></tr>";

    if ($date_ret != "") {
        $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Date retour</td><td><b>" . $date_ret . "</b></td></tr>";
    }
    if ($pickup_time_ret != "") {
        $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Horaire retour</td><td><b>" . $pickup_time_ret . "</b></td></tr>";
        
    }
    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Paiment En</td><td><b>" . $paymethod . " " . $price . "€</b></td></tr>";

    $partie_html .= "</table>";

    $partie_html .= "<hr />";

    $partie_html .= "<h3><i class='icon-question-circle'></i> Informations complémentaires</h3>";
    $partie_html .= "<table>";

    if ($flight_num != "") {
        $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Information Vol/Train</td><td'><b>" . $flight_num . "</b></td></tr>";
    }

    if ($comments != "") {
        $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>Message</td><td><b>" . nl2br($comments) . "</b></td></tr>";
    }
    if ($is_airport || $date_ret != "") {
        $partie_html .= "<tr><td style='width:35%;background:#cce7e7;'>INFO</td><td><b>" . nl2br($locationmsg) . "</b></td></tr>";        
    }


    $partie_html .= "<tr><td style='width:35%;background:#cce7e7;border-bottom:none;'>Conditions générales</td><td style='border-bottom:none;'>CGV acceptées (case cochée)</td></tr>";
    $partie_html .= "</table>";



    // Fin du message HTML
    $fin = "</body></html>\n\n";
    $boundary = md5(uniqid() . microtime());

    $sortie_pourLeClient = $partie_entete . $message_client . $partie_html . $fin;
    $sortiePourLadmin = $partie_entete . $message_admin . $partie_html . $fin;
    // Plain text version of message
    $body_client = "--$boundary\r\n" .
        "Content-Type: text/plain; charset=UTF-8\r\n" .
        "Content-Transfer-Encoding: base64\r\n\r\n";
    $body_client .= chunk_split(base64_encode(strip_tags($sortie_pourLeClient)));
    // HTML version of message
    $body_client .= "--$boundary\r\n" .
        "Content-Type: text/html; charset=UTF-8\r\n" .
        "Content-Transfer-Encoding: base64\r\n\r\n";
    $body_client .= chunk_split(base64_encode($sortie_pourLeClient));
    $body_client .= "--$boundary--";

    $body_admin = "--$boundary\r\n" .
        "Content-Type: text/plain; charset=UTF-8\r\n" .
        "Content-Transfer-Encoding: base64\r\n\r\n";
    $body_admin .= chunk_split(base64_encode(strip_tags($sortiePourLadmin)));
    // HTML version of message
    $body_admin .= "--$boundary\r\n" .
        "Content-Type: text/html; charset=UTF-8\r\n" .
        "Content-Transfer-Encoding: base64\r\n\r\n";
    $body_admin .= chunk_split(base64_encode($sortiePourLadmin));
    $body_admin .= "--$boundary--";

    //send to admin

    try {
        $mail->Body = $sortie_pourLeClient;
        $mail->AltBody = $sortie_pourLeClient;
        $mail->send();
        //echo 'Message has been sent';
    } catch (PHPMailer\PHPMailer\Exception $e) {
        error_log('Message could not be sent. Mailer Error: ' . $e);
    }
}

?>