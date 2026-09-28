<?php
session_start();
$global_config = require('utils/const.php');
$paymethod = '';
require 'utils/util.php';

if ($_GET['method'] == "visa") {
    // error_log('enter success visa');
    $paymethod = 'card';

    #do nothing if visa/mastercard
} elseif ($_GET['method'] == "cash") {
    error_log('enter success cash');
    $paymethod = 'cash';

    #do nothing if cash
} elseif ($_GET['method'] == "banc") {
    $paymethod = 'bancontact';
    // error_log('enter success banc');
    
} elseif ($_GET['method'] == "paypal") {
    require 'modules/PayPal-PHP-SDK/autoload.php';            
    $paymethod = 'paypal';
    $price = $_SESSION["price"]; 
    if (!isset($_GET['paymentId'])) {
        error_log('redirecting to paypal page');

        //Payment with paypal
        //Setting up params for payment
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $global_config['paypal_client_ID'],
                $global_config['paypal_secret']

            )
        );
        $apiContext->setConfig(
            array(
                'log.LogEnabled' => true,
                'log.FileName' => 'PayPal.log',
                'log.LogLevel' => 'DEBUG',
                'mode' => 'live' //sandbox for testing
            )
        );
           
        $item = (new \PayPal\Api\Item())
            ->setName($_SESSION["depart"] . " - " . $_SESSION["arrivee"])
            ->setCurrency('EUR')
            ->setQuantity(1)
            //->setSku('') // Similar to `item_number` in Classic API
            ->setPrice($price);

        $itemlist = (new \PayPal\Api\ItemList())
            ->addItem($item);

        $details = (new \PayPal\Api\Details())
            ->setShipping(0)
            ->setTax(0)
            ->setSubtotal($_SESSION["price"]);

        $amount = (new \PayPal\Api\Amount())
            ->setCurrency("EUR")
            ->setTotal($price)
            ->setDetails($details);

        $reftmp = time();
        insert_reservation($reftmp, $_SESSION["email"], $_SESSION["telephone"], $_SESSION["depart"], $_SESSION["arrivee"], $_SESSION['date_aller'], $_SESSION['horaire'], $_SESSION['passager'], $_SESSION['bagages'], $_SESSION['vehicule'], $_SESSION['date_retour'] . ' ' . $_SESSION['horaire_retour'], $_SESSION['baby'] . ' check  ' . $_SESSION["reference"], 'PaypalTMP', $_SESSION['price']);                
        
        $transaction = (new \PayPal\Api\Transaction())
            ->setAmount($amount)
            ->setItemList($itemlist)
            ->setDescription("taxi4airportbelgium Reservation " . $date)
            ->setCustom($reftmp);


        $payment = new \PayPal\Api\Payment();
        $payment->setTransactions([$transaction]);
        $payment->setIntent('sale');
        $redirectUrls = (new \PayPal\Api\RedirectUrls())
            ->setReturnUrl($global_config['paypal_success_url'])
            ->setCancelUrl($global_config['paypal_cancel_url']);

        $payment->setRedirectUrls($redirectUrls);
        $payment->setPayer((new \PayPal\Api\Payer())->setPaymentMethod('paypal'));

        //creating PayPal payment
        try {
            $payment->create($apiContext);
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            error_log($e);
            header("Location: " . $global_config['paypal_base_url']);
            die();
        }
        header("Location: " . $payment->getApprovalLink());
        die();
    } else {
        error_log('enter success paypal');
        try {
            //code...
            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    $global_config['paypal_client_ID'],
                    $global_config['paypal_secret']

                )
            );
            $apiContext->setConfig(
                array(
                    'log.LogEnabled' => true,
                    'log.FileName' => 'PayPal.log',
                    'log.LogLevel' => 'DEBUG',
                    'mode' => 'live' //sandbox for testing
                )
            );
            $payment = \PayPal\Api\Payment::get($_GET['paymentId'], $apiContext);
            $amount_paypal = (float) $payment->getTransactions()[0]->getAmount()->getTotal();
            $retrievedCustomData = $payment->getTransactions()[0]->getCustom();
           
            if (!isset($_SESSION["email"])) {
                error_log('lost session values in paypal, getting them back from db');
                $values = getTmpReservation($retrievedCustomData);
                // insert_reservation($ref, $_SESSION["email"], $_SESSION["telephone"], $_SESSION["depart"], $_SESSION["arrivee"], $_SESSION['date_aller'], $_SESSION['horaire'], $_SESSION['passager'], $_SESSION['bagages'], $_SESSION['vehicule'], $_SESSION['date_retour'] . ' ' . $_SESSION['horaire_retour'], $_SESSION['baby'] . ' check  ' . $_SESSION["reference"], $paymethod, $_SESSION['price']);
                $_SESSION["email"] = $values->email;
                $_SESSION["telephone"] = $values->phone;
                $_SESSION["depart"] = $values->origin;
                $_SESSION["arrivee"] = $values->dest;
                $_SESSION['date_aller'] = $values->depart_date;
                $_SESSION['horaire'] = $values->depart_time;
                $_SESSION['passager'] = $values->passengers;
                $_SESSION['bagages'] = $values->valises;
                $_SESSION['vehicule'] = $values->type;
                // Assuming 'date_retour' and 'horaire_retour' are separate values in $values
                $_SESSION['date_retour'] = $values->date_retour;

                // Assuming 'baby' and 'reference' are separate values in $values
                $_SESSION['baby'] = $values->vol;
                $_SESSION['reference'] = $values->vol;

                $_SESSION['price'] = $values->total;

            }

            
            $execution = (new \PayPal\Api\PaymentExecution())
                ->setPayerId($_GET['PayerID'])
                ->setTransactions($payment->getTransactions());
            try {
                $payment->execute($execution, $apiContext);
            } catch (\PayPal\Exception\PayPalConnectionException $e) {
                error_log($e);
                header("Location: " . $global_config['paypal_base_url']);
                die();
            }
            
        } catch (\Throwable $e) {
            // var_dump($th);
            error_log($e);
            header("Location: " . $global_config['paypal_base_url']);
            die();
        }
    }
}

try {
    require_once 'utils/util.php';
    //SEND MAIL
    if ( $paymethod == 'bancontact' || $paymethod == 'card') {
        error_log('entering banc visa conf in success page, pay method is : '.$paymethod);
        error_log("email : " . $_SESSION["email"] . " depart: ". $_SESSION["depart"]. " , arrive: ".$_SESSION["arrivee"]);
    }
    else{    
        if (isset($_SESSION["email"])) {
            $reserv = getReservation($_SESSION["email"], $_SESSION["telephone"], $_SESSION["depart"], $_SESSION["arrivee"], $_SESSION['date_aller'], $_SESSION['horaire'], $_SESSION['passager'], $_SESSION['bagages'], $_SESSION['vehicule'], $paymethod, $_SESSION['price']);
            if (isset($reserv["email"])) {
                error_log('confirmation already sent');
            
            } 
            else {            
                $ref = time();
                sendMail($ref, $paymethod);
                insert_reservation($ref, $_SESSION["email"], $_SESSION["telephone"], $_SESSION["depart"], $_SESSION["arrivee"], $_SESSION['date_aller'], $_SESSION['horaire'], $_SESSION['passager'], $_SESSION['bagages'], $_SESSION['vehicule'], $_SESSION['date_retour'] . ' ' . $_SESSION['horaire_retour'], $_SESSION['baby'] . ' check  ' . $_SESSION["reference"], $paymethod, $_SESSION['price']);
                registerCalendar($_SESSION["depart"], $_SESSION["arrivee"], $_SESSION['date_aller'], $_SESSION['horaire'], $_SESSION["telephone"], $_SESSION["email"], $paymethod, $_SESSION['date_retour'], $_SESSION['horaire_retour'], $_SESSION['vehicule']);            
            }
        } else {
            error_log('email is not set ! ERROR send mail');
        }
    }

    require_once 'successview.php';
} catch (\Throwable $e) {
    error_log($e);
}