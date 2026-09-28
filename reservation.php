<?php
// NE PAS SUPPRIMER, ON LANCE LA SESSION
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
error_log("Entering Reservations.php ---------- From " . $details->city . ";".$ip);

		
// A décommenter pour activer le mode débug
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


$global_config = require('utils/const.php');
require 'utils/util.php';

if (!empty($_POST['bancontact_but']) ) {

	//Payment with stripe
	require_once 'modules/stripe-php/init.php';
	
	//setting private stripe api_key
	\Stripe\Stripe::setApiKey($global_config['stripe_sk']);
	// Create a new Bancontact source, now paymentintent
	//Return_url is the final page. (Thankyou.php) :
	//Stripe won't redirect to this url unless the webhook.php has finished executing. 
	$paymentIntent = \Stripe\PaymentIntent::create([
		'amount' => $_SESSION["price"] * 100,
		'currency' => 'eur',
		'payment_method_types' => ['bancontact'],
		'metadata' => [
			'depart' => $_SESSION["depart"],
			'dest' => $_SESSION["arrivee"],
			'date' => $_SESSION["date_aller"],
			'time' => $_SESSION["horaire"],
			'price' => $_SESSION["price"],
			'phone' => $_SESSION["telephone"],
			'email' => $_SESSION["email"],
			'passengers' => $_SESSION["passager"],
			'bags' => $_SESSION["bagages"],
			'car_type' => $_SESSION["vehicule"],
			'pickup_time_ret' => $_SESSION["horaire_retour"],
			'date_ret' => $_SESSION["date_retour"],
			'flight_num' => $_SESSION["reference"],
			'journey_type' => $_SESSION["trajet"],
			'fname' => $_SESSION["prenom"],
			'lname' => $_SESSION["nom"],
			'is_airport' => $_SESSION["origin_airport"],								
			'comments' => mb_strimwidth($_SESSION["message"], 0, 50, "..."),
			'babyseat' => $_SESSION['baby'],
			'distance' => $_SESSION['distance']]
		]);
		$paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntent->id);
		$bancpaymethod_stripe = \Stripe\PaymentMethod::create([
			'type' => 'bancontact',
			'billing_details' => [
				'name' => $_SESSION["nom"],
				'email' => $_SESSION["email"],
			],
		]);
        $paymentIntent->confirm([
            'payment_method' => $bancpaymethod_stripe->id,
            'return_url' => $global_config['stripe_return_url'],
        ]);

        header("Location: " . $paymentIntent->next_action->redirect_to_url->url);
        exit();
} elseif ($_POST['paypal']) {
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
		->setName(" TAXI SHUTTLE ")
		->setCurrency('EUR')
		->setQuantity(1)
		//->setSku('') // Similar to `item_number` in Classic API
		->setPrice($_SESSION["price"]);

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
		->setDescription("AIRPORTAXI Official RSVP ")
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
		// var_dump($e);
		header("Location: " . $global_config['paypal_base_url']);
		die();
	}
	header("Location: " . $payment->getApprovalLink());
	die();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd">
<html xml:lang="fr-FR" lang="fr-FR" xmlns="https://www.w3.org/1999/xhtml">

<head>
		<script id="cookieyes" type="text/javascript" src="https://cdn-cookieyes.com/client_data/b86bc3e4c62e7f64155d1cdf/script.js"></script>

	<title>Airportaxiofficial • Navettes aéroports • Location de voiture avec chauffeur privé à Bruxelles • Réservation
	</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="fr-FR" />
	<meta http-equiv="keywords" name="keywords"
		content="navettes aéroports, taxis alternatifs, locations de voiture avec chauffeur privé, taxi d'affaires, taxi-navette Bruxelles, véhicule de tourisme avec chauffeur Bruxelles, chauffeur prive Bruxelles, location véhicules avec chauffeur Bruxelles, chauffeur prive, chauffeur prive Nord, location voiture avec chauffeur Bruxelles, transport pour séminaires Bruxelles, tourisme d'affaires Bruxelles, Taxi, Bruxelles, transport avec chauffeur, transfert aéroport, transfert gare, Lesquin, Lille, Charleroi, Bruxelles, Anvers, Amsterdam, Schiphol, Roissy-Charles De Gaulle, Orly, déplacements d'affaires, déplacements particuliers, vtc, mise à disposition, évenementiels, taxi Charleroi, taxi Liège, taxi Namur, uber" />
	<meta http-equiv="description" name="description"
		content="Taxi Bruxelles - Navettes aéroports - Locations de voiture avec chauffeur privé" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport"
		content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="msapplication-tap-highlight" content="yes" />
	<meta name="Author" content="Airportaxiofficial" />
	<meta name="Geography" content="Belgium" />
	<meta name="country" content="Belgium" />
	<meta name="Language" content="French" />
	<link rel="alternate" href="https://airportaxiofficial.be" hreflang="fr-fr" />
	<meta name="Copyright" content="Airportaxiofficial" />
	<link rel="canonical" href="https://airportaxiofficial.be" />
	<meta name="google-site-verification" content="" />
	<meta name="robots" content="INDEX|FOLLOW" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta name="DP.PopRank" Content="2.00000" />
	<link rel="icon" href="images/favicon.ico" type="image/x-icon" />

	<!-- Google Fonts -->
	<link rel="stylesheet" type="text/css"
		href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400%7CQuicksand:400,700" />
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Anton">

	<!-- STYLES & JQUERY -->
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/icons.css" />
	<link rel="stylesheet" type="text/css" href="css/color.css" />
	<link rel="stylesheet" type="text/css" href="css/responsive.css" />

	<!-- Booking Stylesheets -->
	<link rel="stylesheet" href="reservation/css/form-wizard.css">
	<link rel="stylesheet" href="reservation/css/form-wizard_2.css">
	<link rel="stylesheet" href="reservation/css/form-wizard_3.css">
	<link rel="stylesheet" href="reservation/css/bootstrap.css">
	<link rel="stylesheet" href="reservation/css/font-awesome.min.css">
	<link rel="stylesheet" href="reservation/css/custom.css">
	<link rel="stylesheet" href="reservation/css/select2.min.css">
	<link rel="stylesheet" href="reservation/css/oldstyle.css">
	<!-- Calendar CSS -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<style>

	</style>

	<!-- <script src="js/jquery-1.9.0.min.js"></script> -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- <script src="https://js.stripe.com/v3/"></script> -->
	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
	
	<!-- the rest of the scripts at the bottom of the document -->

	<!-- JS mapShow -->
	<script src="reservation/js/mapShow.js"></script>
	<script src="reservation/js/select2.min.js"></script>
	<script src="reservation/js/custom.js"></script>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-819386446"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'AW-819386446');
	</script>
	<!-- Event snippet for whatsapp calls conversion page
In your html page, add the snippet and call gtag_report_conversion when someone clicks on the chosen link or button. -->
<script>
function gtag_report_conversion(url) {
  var callback = function () {
    if (typeof(url) != 'undefined') {
      window.location = url;
    }
  };
  gtag('event', 'conversion', {
      'send_to': 'AW-819386446/jjqcCO6M36QYEM6w24YD',
      'event_callback': callback
  });
  return false;
}
</script>


</head>

<body>


<!-- End Google Tag Manager (noscript) -->
<a onclick="gtag_report_conversion()" href="https://wa.me/+32492061896" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
</a>


	<!-- RD Navbar -->
	<div class="rd-navbar-wrap">
		<nav data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-static" data-md-device-layout="rd-navbar-fixed"
			data-lg-layout="rd-navbar-static" data-lg-device-layout="rd-navbar-static" data-sm-stick-up-offset="50px"
			data-lg-stick-up-offset="150px" class="rd-navbar">
			<div class="rd-navbar-inner">
				<!-- RD Navbar Panel -->
				<div class="rd-navbar-panel">
					<div class="rd-navbar-panel-canvas"></div>

					<!-- RD Navbar Toggle -->
					<button class="rd-navbar-toggle" data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
					<!-- END RD Navbar Toggle -->

					<!-- RD Navbar Collapse Toggle -->
					<button class="rd-navbar-collapse-toggle" data-rd-navbar-toggle=".rd-navbar-collapse">
						<span></span>
					</button>
					<ul class="rd-navbar-collapse">
						<li><a href="#googtrans(fr|fr)" class="lang-fr lang-select" data-lang="fr"><img
									src="images/langues/fr.png" title="Français" alt="FR" /></a></li>
						<li><a href="#googtrans(fr|en)" class="lang-en lang-select" data-lang="en"><img
									src="images/langues/en.png" title="Anglais" alt="EN" /></a></li>
						<li><a href="#googtrans(fr|nl)" class="lang-nl lang-select" data-lang="nl"><img
									src="images/langues/nl.png" title="Néerlandais" alt="NL" /></a></li>
						<li><a href="#googtrans(fr|de)" class="lang-de lang-select" data-lang="de"><img
									src="images/langues/de.png" title="Allemand" alt="DE" /></a></li>
						<li><a href="#googtrans(fr|es)" class="lang-es lang-select" data-lang="es"><img
									src="images/langues/es.png" title="Espagnol" alt="ES" /></a></li>
						<li><a href="#googtrans(fr|it)" class="lang-it lang-select" data-lang="it"><img
									src="images/langues/it.png" title="Italien" alt="IT" /></a></li>
						<li><a href="#googtrans(fr|pt)" class="lang-pt lang-select" data-lang="pt"><img
									src="images/langues/pt.png" title="Portuguais" alt="PT" /></a></a></li>
					</ul>
					<!-- END RD Navbar Collapse Toggle -->

					<!-- RD Navbar Brand -->
					<div class="rd-navbar-brand">
						<a href="/" class="brand-name">
							<img src="images/logo.png" class="brand logo" title="Airportaxiofficial"
								alt="Airportaxiofficial" />
							<span>Menu</span>
						</a>
					</div>
					<!-- END RD Navbar Brand -->
				</div>
				<!-- END RD Navbar Panel -->
			</div>
			<div class="rd-navbar-outer">
				<div class="rd-navbar-inner">
					<div class="rd-navbar-subpanel">
						<div class="rd-navbar-nav-wrap">
							<!-- RD Navbar Nav -->
							<ul class="rd-navbar-nav">
								<li><a href="/"><i class="icon-home homeicon"></i>Accueil</a></li>
								<li><a href="chauffeur-taxi.html">À propos</a></li>
								<li>
									<a href="navette-aeroport.html">Nos services</a>
									<!-- RD Navbar Dropdown -->
									<ul class="rd-navbar-dropdown">
										<li><a href="navette-aeroport.html">Navette aéorport</a></li>
										<li><a href="chauffeur-prive.html">Chauffeur privé</a></li>
									</ul>
									<!-- END RD Navbar Dropdown -->
								</li>
								<li><a href="vehicules.html">Véhicules</a></li>
								<li><a href="tarifs.html">Tarifs</a></li>
								<li class="active"><a href="reservation.php">Réservation</a></li>
								<li><a href="contact.html">Contact</a></li>
							</ul>
							<!-- END RD Navbar Nav -->
						</div>
						<!-- END RD Navbar Search Toggle -->
					</div>
				</div>
			</div>
		</nav>
	</div>
	<!-- END RD Navbar -->

	<!-- HEADER -->
	<div class="undermenuarea">
		<div class="boxedshadow"></div>
		<div class="grid">
			<div class="row">
				<div class="c8">
					<!-- <h1 class="titlehead">Réservation</h1> -->
				</div>
				<div class="c4">
					<!-- <h1 class="titlehead rightareaheader"><i class="icon-mobile-phone"></i> (+32) 492 061 896</h1> -->
				</div>
			</div>
		</div>
	</div>

	<!-- Page-->
	<div class="grid">
		<div class="row justabox">
			<!-- main content -->
			<section id="phase_1" class="form-box" id="top_form">
				<div class="c9 form-wizard">
					<span style="color:green;font-weight:bold;font-family:'Raleway', sans-serif;display:block;margin-top:5px">Airport Taxi - APD 25€ - Annulation Gratuite Avant 48H</span>
					<!-- Form progress -->
					<div class="form-wizard-steps form-wizard-tolal-steps-5">
						<div class="form-wizard-progress">
							<div class="form-wizard-progress-line" data-now-value="12.25" data-number-of-steps="5"
								style="width: 12.25%;"></div>
						</div>
						<!-- Step 1 -->
						<div class="form-wizard-step active">
							<div class="form-wizard-step-icon"><i class="icon-road" aria-hidden="true"></i></div>
							<p>Trajet</p>
						</div>
						<!-- Step 1 -->

						<!-- Step 2 -->
						<div class="form-wizard-step">
							<div class="form-wizard-step-icon"><i class="icon-car" aria-hidden="true"></i></div>
							<p>Prestation</p>
						</div>
						<!-- Step 2 -->

						<!-- Step 3 -->
						<div class="form-wizard-step">
							<div class="form-wizard-step-icon"><i class="icon-user" aria-hidden="true"></i></div>
							<p>Coordonnées</p>
						</div>
						<!-- Step 3 -->

						<!-- Step 4 -->
						<div class="form-wizard-step">
							<div class="form-wizard-step-icon"><i class="icon-credit-card" aria-hidden="true"></i></div>
							<p>Paiement</p>
						</div>
						<!-- Step 4 -->

						<!-- Step 5 -->
						<div class="form-wizard-step">
							<div class="form-wizard-step-icon"><i class="icon-check" aria-hidden="true"></i></div>
							<p>Confirmation</p>
						</div>
						<!-- Step 5 -->
					</div>
					<!-- Form progress -->

					<!-- Form Step 1 -->
					<fieldset>
						<!-- Progress Bar -->
						<div class="progress">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="20"
								aria-valuemin="0" aria-valuemax="100" style="width:20%"></div>
						</div>
						<!-- Progress Bar -->

						<h4 class="maintitle"><span class="stepTitle">Taxi Reservation</span><span
								class="step"><em>Étape 1
									- 5</em></span></h4>

						<form id="journey" role="form" name="form" method="POST" action="/reservation.php" onsubmit="return puNdest()">
							<div class="form-group">

								<div id="pu_pu">
									<label><i class="icon-map-marker" aria-hidden="true"></i> Adresse de
										départ</label>
									<input type="text" name="depart" id="autocomplete_address_departure"
										placeholder="Indiquer ici l'adresse de départ"
										value="<?php echo $_SESSION['depart'] == null ? '' : $_SESSION['depart'] ?>"
										class="form-control" required>
								</div>

								<div id="pu_air" style="display:none;">
									<div id="dest2air" style="display:block">
										<label for="inputAir" style="padding-bottom: 5px;">Prise en Charge <a
												class="change_dest" onClick="dest2dest()" name="dest2dest"> </a></label>
										<select name="destinationAcceuil" style="font-size: 16px"
											class="form-control js-example-basic-single" id="inputAir"
											placeholder="Rechercher Aéroport">
											<?php $selected_value = $_SESSION["depart"]; ?>
											<option value="">Rechercher Aéroport</option>
											<option value="Charleroi Airport, Belgium" <?php set_selected('Charleroi Airport, Belgium', $selected_value); ?>>Charleroi Airport
												(CRL)</option>
											<option value="Brussels Airport Zaventem, Belgium" <?php set_selected('Brussels Airport Zaventem, Belgium', $selected_value); ?>>
												Brussels
												Airport Zaventem (BRU)</option>
											<option value="Antwerp Airport, Antwerpen, Belgium" <?php set_selected('Antwerp Airport, Antwerpen, Belgium', $selected_value); ?>>Antwerpen
												Airport (ANR)</option>
											<option value="Amsterdam Airport Schiphol, the Netherlands" <?php set_selected('Amsterdam Airport Schiphol, the Netherlands', $selected_value); ?>>
												Amsterdam Schiphol (AMS)</option>
											<option value="CDG Airport, Paris, France" <?php set_selected('CDG Airport, Paris, France', $selected_value); ?>>Paris Charles de
												Gaulle (CDG)</option>
											<option value="Duesseldorf Airport" <?php set_selected('Duesseldorf Airport', $selected_value); ?>>
												Duesseldorf (DUS)
											</option>
											<option value="Cologne Bonn Airport (CGN)" <?php set_selected('Cologne Bonn Airport (CGN)', $selected_value); ?>>Cologne Bonn (CGN)
											</option>
											<option value="Paris-Orly Airport (ORY), France" <?php set_selected('Paris-Orly Airport (ORY), France', $selected_value); ?>>
												Paris-Orly
												(ORY)</option>
											<option value="Eindhoven Airport, Netherlands" <?php set_selected('Eindhoven Airport, Netherlands', $selected_value); ?>> Eindhoven
												(EIN)</option>
											<option value="Maastricht Aachen Airport, Netherlands" <?php set_selected('Maastricht Aachen Airport, Netherlands', $selected_value); ?>>
												Maastricht Aachen (MST)</option>
											<option value="Berlin Airport, Germany" <?php set_selected('Berlin Airport, Germany', $selected_value); ?>>
												Berlijn (BML)
											</option>
											<option value="Frankfurt Airport, Germany" <?php set_selected('Frankfurt Airport, Germany', $selected_value); ?>> Frankfurt (FRA)
											</option>
											<option value="Munich International Airport (MUC)" <?php set_selected('Munich International Airport (MUC)', $selected_value); ?>>
												Munich
												International (MUC)</option>
											<option value="Luxembourg Findel Airport" <?php set_selected('Luxembourg Findel Airport', $selected_value); ?>> Luxembourg Findel
												(LUX)</option>
											<option value="Liege Airport, Belgium" <?php set_selected('Liege Airport, Belgium', $selected_value); ?>>
												Liege Airport
											</option>
											<option value="Oostend Airport, Belgium" <?php set_selected('Oostend Airport, Belgium', $selected_value); ?>> Oostend (OST)
											</option>
											<option value="Nice-Côte Azur Airport" <?php set_selected('Nice-Côte Azur Airport', $selected_value); ?>> Nice-Côte Azur Airport
											</option>
											<option value="	Lyon Saint-Exupéry Airport" <?php set_selected('	Lyon Saint-Exupéry Airport', $selected_value); ?>>Lyon Saint-Exupéry Airport
											</option>
											<option value="Marseille Provence Airport" <?php set_selected('Marseille Provence Airport', $selected_value); ?>>Marseille Provence Airport
											</option>
											<option value="Toulouse-Blagnac Airport" <?php set_selected('Toulouse-Blagnac Airport', $selected_value); ?>> Toulouse-Blagnac Airport
											</option>
											<option value="Paris-Orly Airport" <?php set_selected('Paris-Orly Airport', $selected_value); ?>> Paris-Orly Airport
											</option>
											<option value="EuroAirport Basel-Mulhouse-Freiburg Airport" <?php set_selected('EuroAirport Basel-Mulhouse-Freiburg Airport', $selected_value); ?>> EuroAirport Basel-Mulhouse-Freiburg Airport
											</option>
											<option value="Bordeaux-Mérignac Airport" <?php set_selected('Bordeaux-Mérignac Airport', $selected_value); ?>>	Bordeaux-Mérignac Airport
											</option>
											<option value="Lille-Lesquin Airport" <?php set_selected('Lille-Lesquin Airport', $selected_value); ?>>	Lille-Lesquin Airport
											</option>
											<option value="Nantes Atlantique Airport" <?php set_selected('Nantes Atlantique Airport', $selected_value); ?>>	Nantes Atlantique Airport
											</option>
											<option value="Strasbourg Airport" <?php set_selected('Strasbourg Airport', $selected_value); ?>> Strasbourg Airport
											</option>
											<option value="Berlin-Tegel Airport" <?php set_selected('Berlin-Tegel Airport', $selected_value); ?>>Berlin-Tegel Airport TXL
											</option>
											<option value="Hamburg Airport" <?php set_selected('Hamburg Airport', $selected_value); ?>>	Hamburg Airport HAM
											</option>
											<option value="Stuttgart Airport" <?php set_selected('Stuttgart Airport', $selected_value); ?>>	Stuttgart Airport STR
											</option>
											<option value="Cologne Bonn Airport" <?php set_selected('Cologne Bonn Airport', $selected_value); ?>>Cologne Bonn Airport CGN
											</option>
											<option value="Hannover Airport" <?php set_selected('Hannover Airport', $selected_value); ?>>Hannover Airport HAJ
											</option>
											<option value="Nuremberg Airport" <?php set_selected('Nuremberg Airport', $selected_value); ?>>	Nuremberg Airport NUE
											</option>
											<option value="Bremen Airport" <?php set_selected('Bremen Airport', $selected_value); ?>>Bremen Airport BRE
											</option>
											<option value="Leipzig/Halle Airport" <?php set_selected('Leipzig/Halle Airport', $selected_value); ?>>	Leipzig/Halle Airport LEJ
											</option>
											<option value="Dresden Airport" <?php set_selected('Dresden Airport', $selected_value); ?>>	Dresden Airport DRS
											</option>
											<option value="Dusseldorf Airport" <?php set_selected('Dusseldorf Airport', $selected_value); ?>>Dusseldorf Airport DRS
											</option>
											<option value="Zürich Airport" <?php set_selected('Zürich Airport', $selected_value); ?>> Zürich Airport
											</option>
											<option value="Geneva Cointrin International Airport" <?php set_selected('Geneva Cointrin International Airport', $selected_value); ?>> Geneva Cointrin International Airport
											</option>
											
										</select>
									</div>
								</div>

							</div>
							<div class="form-group">
								<img onclick="inverseInputAddress()" class='arrow-img'
									src="reservation/images/opposite.png" alt="">
							</div>

							<div id="air" class="form-group">
								<div id="air_air" style="display:block;">
									<div id="dest2air2" style="display:block">
										<label for="inputAir3" style="padding-bottom: 5px;">Destination <a
												class="change_dest" onClick="dest2dest()" name="dest2dest"> </a></label>
										<select name="destinationAcceuil" style="font-size: 16px"
											class="form-control js-example-basic-single" id="inputAir3"
											placeholder="Rechercher Aéroport" required>
											<?php $selected_value = $_SESSION["destination"]; ?>
											<option value="">Rechercher Aéroport</option>
											<option value="Charleroi Airport, Belgium" <?php set_selected('Charleroi Airport, Belgium', $selected_value); ?>>Charleroi Airport
												(CRL)</option>
											<option value="Brussels Airport Zaventem, Belgium" <?php set_selected('Brussels Airport Zaventem, Belgium', $selected_value); ?>>
												Brussels
												Airport Zaventem (BRU)</option>
											<option value="Antwerp Airport, Antwerpen, Belgium" <?php set_selected('Antwerp Airport, Antwerpen, Belgium', $selected_value); ?>>Antwerpen
												Airport (ANR)</option>
											<option value="Amsterdam Airport Schiphol, the Netherlands" <?php set_selected('Amsterdam Airport Schiphol, the Netherlands', $selected_value); ?>>
												Amsterdam Schiphol (AMS)</option>
											<option value="CDG Airport, Paris, France" <?php set_selected('CDG Airport, Paris, France', $selected_value); ?>>Paris Charles de
												Gaulle (CDG)</option>
											<option value="Duesseldorf Airport" <?php set_selected('Duesseldorf Airport', $selected_value); ?>>
												Duesseldorf (DUS)
											</option>
											<option value="Cologne Bonn Airport (CGN)" <?php set_selected('Cologne Bonn Airport (CGN)', $selected_value); ?>>Cologne Bonn (CGN)
											</option>
											<option value="Paris-Orly Airport (ORY), France" <?php set_selected('Paris-Orly Airport (ORY), France', $selected_value); ?>>
												Paris-Orly
												(ORY)</option>
											<option value="Eindhoven Airport, Netherlands" <?php set_selected('Eindhoven Airport, Netherlands', $selected_value); ?>> Eindhoven
												(EIN)</option>
											<option value="Maastricht Aachen Airport, Netherlands" <?php set_selected('Maastricht Aachen Airport, Netherlands', $selected_value); ?>>
												Maastricht Aachen (MST)</option>
											<option value="Berlin Airport, Germany" <?php set_selected('Berlin Airport, Germany', $selected_value); ?>>
												Berlijn (BML)
											</option>
											<option value="Frankfurt Airport, Germany" <?php set_selected('Frankfurt Airport, Germany', $selected_value); ?>> Frankfurt (FRA)
											</option>
											<option value="Munich International Airport (MUC)" <?php set_selected('Munich International Airport (MUC)', $selected_value); ?>>
												Munich
												International (MUC)</option>
											<option value="Luxembourg Findel Airport" <?php set_selected('Luxembourg Findel Airport', $selected_value); ?>> Luxembourg Findel
												(LUX)</option>
											<option value="Liege Airport, Belgium" <?php set_selected('Liege Airport, Belgium', $selected_value); ?>>
												Liege Airport
											</option>
											<option value="Oostend Airport, Belgium" <?php set_selected('Oostend Airport, Belgium', $selected_value); ?>> Oostend (OST)
											</option>
											<option value="Nice-Côte Azur Airport" <?php set_selected('Nice-Côte Azur Airport', $selected_value); ?>> Nice-Côte Azur Airport
											</option>
											<option value="	Lyon Saint-Exupéry Airport" <?php set_selected('	Lyon Saint-Exupéry Airport', $selected_value); ?>>Lyon Saint-Exupéry Airport
											</option>
											<option value="Marseille Provence Airport" <?php set_selected('Marseille Provence Airport', $selected_value); ?>>Marseille Provence Airport
											</option>
											<option value="Toulouse-Blagnac Airport" <?php set_selected('Toulouse-Blagnac Airport', $selected_value); ?>> Toulouse-Blagnac Airport
											</option>
											<option value="Paris-Orly Airport" <?php set_selected('Paris-Orly Airport', $selected_value); ?>> Paris-Orly Airport
											</option>
											<option value="EuroAirport Basel-Mulhouse-Freiburg Airport" <?php set_selected('EuroAirport Basel-Mulhouse-Freiburg Airport', $selected_value); ?>> EuroAirport Basel-Mulhouse-Freiburg Airport
											</option>
											<option value="Bordeaux-Mérignac Airport" <?php set_selected('Bordeaux-Mérignac Airport', $selected_value); ?>>	Bordeaux-Mérignac Airport
											</option>
											<option value="Lille-Lesquin Airport" <?php set_selected('Lille-Lesquin Airport', $selected_value); ?>>	Lille-Lesquin Airport
											</option>
											<option value="Nantes Atlantique Airport" <?php set_selected('Nantes Atlantique Airport', $selected_value); ?>>	Nantes Atlantique Airport
											</option>
											<option value="Strasbourg Airport" <?php set_selected('Strasbourg Airport', $selected_value); ?>> Strasbourg Airport
											</option>
											<option value="Berlin-Tegel Airport" <?php set_selected('Berlin-Tegel Airport', $selected_value); ?>>Berlin-Tegel Airport TXL
											</option>
											<option value="Hamburg Airport" <?php set_selected('Hamburg Airport', $selected_value); ?>>	Hamburg Airport HAM
											</option>
											<option value="Stuttgart Airport" <?php set_selected('Stuttgart Airport', $selected_value); ?>>	Stuttgart Airport STR
											</option>
											<option value="Cologne Bonn Airport" <?php set_selected('Cologne Bonn Airport', $selected_value); ?>>Cologne Bonn Airport CGN
											</option>
											<option value="Hannover Airport" <?php set_selected('Hannover Airport', $selected_value); ?>>Hannover Airport HAJ
											</option>
											<option value="Nuremberg Airport" <?php set_selected('Nuremberg Airport', $selected_value); ?>>	Nuremberg Airport NUE
											</option>
											<option value="Bremen Airport" <?php set_selected('Bremen Airport', $selected_value); ?>>Bremen Airport BRE
											</option>
											<option value="Leipzig/Halle Airport" <?php set_selected('Leipzig/Halle Airport', $selected_value); ?>>	Leipzig/Halle Airport LEJ
											</option>
											<option value="Dresden Airport" <?php set_selected('Dresden Airport', $selected_value); ?>>	Dresden Airport DRS
											</option>
											<option value="Dusseldorf Airport" <?php set_selected('Dusseldorf Airport', $selected_value); ?>>Dusseldorf Airport DRS
											</option>
											<option value="Zürich Airport" <?php set_selected('Zürich Airport', $selected_value); ?>> Zürich Airport
											</option>
											<option value="Geneva Cointrin International Airport" <?php set_selected('Geneva Cointrin International Airport', $selected_value); ?>> Geneva Cointrin International Airport
											</option>
										
										</select>
									</div>

									<div id="dest2dest2" style="display:none">
										<div class="form-group">
											<label><i class="icon-map-marker" aria-hidden="true"></i> Destination</label>
											<input type="text" name="arrivee" id="autocomplete_address_arrival"
												placeholder="Indiquer ici l'adresse d'arrivée"
												value="<?php echo $_SESSION['arrivee'] == null ? '' : $_SESSION['arrivee'] ?>"
												class="form-control">
										</div>
									</div>

								</div>
							</div>
							<div class="container-fluid form-group">
								<div class="row form-inline">
									<div class="form-group c4">
										<label><i class="icon-calendar" aria-hidden="true"></i> Date aller</label>
										<input pattern="\d{1,2}/\d{1,2}/\d{4}" class="form-control required"
											name="date_aller" id="date_aller" type="text" value="<?php if (isset($_SESSION['date_aller']))
												echo stripslashes($_SESSION['date_aller']); ?>" placeholder="jj/mm/aaaa" autocomplete="off"
											style="width:100%">
									</div>
									<div class="form-group c4">
										<label><i class="icon-time" aria-hidden="true"></i> Heure</label>
										<select name="heure" class="form-control required notranslate"
											style="width:100%">
											<option value="">Sélectionner...</option>
											<option value="00" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "00") {
													echo ("selected");
												}
											} ?>>00h</option>
											<option value="01" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "01") {
													echo ("selected");
												}
											} ?>>01h</option>
											<option value="02" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "02") {
													echo ("selected");
												}
											} ?>>02h</option>
											<option value="03" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "03") {
													echo ("selected");
												}
											} ?>>03h</option>
											<option value="04" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "04") {
													echo ("selected");
												}
											} ?>>04h</option>
											<option value="05" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "05") {
													echo ("selected");
												}
											} ?>>05h</option>
											<option value="06" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "06") {
													echo ("selected");
												}
											} ?>>06h</option>
											<option value="07" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "07") {
													echo ("selected");
												}
											} ?>>07h</option>
											<option value="08" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "08") {
													echo ("selected");
												}
											} ?>>08h</option>
											<option value="09" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "09") {
													echo ("selected");
												}
											} ?>>09h</option>
											<option value="10" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "10") {
													echo ("selected");
												}
											} ?>>10h</option>
											<option value="11" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "11") {
													echo ("selected");
												}
											} ?>>11h</option>
											<option value="12" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "12") {
													echo ("selected");
												}
											} ?>>12h</option>
											<option value="13" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "13") {
													echo ("selected");
												}
											} ?>>13h</option>
											<option value="14" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "14") {
													echo ("selected");
												}
											} ?>>14h</option>
											<option value="15" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "15") {
													echo ("selected");
												}
											} ?>>15h</option>
											<option value="16" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "16") {
													echo ("selected");
												}
											} ?>>16h</option>
											<option value="17" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "17") {
													echo ("selected");
												}
											} ?>>17h</option>
											<option value="18" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "18") {
													echo ("selected");
												}
											} ?>>18h</option>
											<option value="19" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "19") {
													echo ("selected");
												}
											} ?>>19h</option>
											<option value="20" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "20") {
													echo ("selected");
												}
											} ?>>20h</option>
											<option value="21" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "21") {
													echo ("selected");
												}
											} ?>>21h</option>
											<option value="22" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "22") {
													echo ("selected");
												}
											} ?>>22h</option>
											<option value="23" <?php if (isset($_SESSION['heure'])) {
												if ($_SESSION['heure'] == "23") {
													echo ("selected");
												}
											} ?>>23h</option>
										</select>
									</div>
									<div class="form-group c4">
										<label><i class="icon-time" aria-hidden="true"></i> Minute</label>
										<select name="minute" class="form-control required notranslate"
											style="width:100%">
											<option value="">Sélectionner...</option>
											<option value="00" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "00") {
													echo ("selected");
												}
											} ?>>00min</option>
											<option value="05" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "05") {
													echo ("selected");
												}
											} ?>>05min</option>
											<option value="10" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "10") {
													echo ("selected");
												}
											} ?>>10min</option>
											<option value="15" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "15") {
													echo ("selected");
												}
											} ?>>15min</option>
											<option value="20" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "20") {
													echo ("selected");
												}
											} ?>>20min</option>
											<option value="25" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "25") {
													echo ("selected");
												}
											} ?>>25min</option>
											<option value="30" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "30") {
													echo ("selected");
												}
											} ?>>30min</option>
											<option value="35" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "35") {
													echo ("selected");
												}
											} ?>>35min</option>
											<option value="40" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "40") {
													echo ("selected");
												}
											} ?>>40min</option>
											<option value="45" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "45") {
													echo ("selected");
												}
											} ?>>45min</option>
											<option value="50" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "50") {
													echo ("selected");
												}
											} ?>>50min</option>
											<option value="55" <?php if (isset($_SESSION['minute'])) {
												if ($_SESSION['minute'] == "55") {
													echo ("selected");
												}
											} ?>>55min</option>
										</select>
									</div>
								</div>
							</div>
							<div style="clear:both;"></div>
							<div class="container-fluid form-group">
								<div class="row form-inline">
									<div class="form-group c4">
										<label><i class="icon-user" aria-hidden="true"></i> Passager(s) à bord</label>
										<select name="passager" id="passager" class="form-control" style="width:100%">
											<option value="1 personne">1 personne</option>
											<option value="2 personnes">2 personnes</option>
											<option value="3 personnes">3 personnes</option>
											<option value="4 personnes">4 personnes</option>
											<option value="5 personnes">5 personnes</option>
											<option value="6 personnes">6 personnes</option>
											<option value="7 personnes">7 personnes</option>
											<option value="8 personnes">8 personnes</option>
										</select>
									</div>
									<div class="form-group c4">
										<label><i class="icon-suitcase" aria-hidden="true"></i> Bagages</label>
										<select name="bagages" id="bagages" class="form-control" style="width:100%">
											<option value="1 bagage">1 bagage</option>
											<option value="2 bagages">2 bagages</option>
											<option value="3 bagages">3 bagages</option>
											<option value="4 bagages">4 bagages</option>
											<option value="5 bagages">5 bagages</option>
											<option value="6 bagages">6 bagages</option>
											<option value="7 bagages">7 bagages</option>
											<option value="8 bagages">8 bagages</option>
										</select>
									</div>
									<script type="text/javascript">
										function yesnoCheck() {
											if (document.getElementById('retour').checked) {
												document.getElementById('date-retour').style.display = 'block';
												document.getElementById('date-retour').style.opacity = '1';
											}
											else {

												document.getElementById('date-retour').style.display = 'none';
												document.getElementById('date-retour').style.opacity = '0';
											}
											document.getElementById('date-retour').style.transition = 'opacity 0.3s linear';
										}
									</script>
									<div class="form-group c4">

										<div class="grid-container" style="margin-top: 15px;">
											<div class="grid-item">
												<div style="clear:both;"></div>
												<label class="radio-inline">
													<input type="radio" onclick="javascript:yesnoCheck();" name="trajet"
														id="aller" value="Aller simple"
														checked="checked" />&nbsp;&nbsp;&nbsp;Aller simple
												</label>
											</div>
											<div class="grid-item">
												<div style="clear:both;"></div>
												<label class="radio-inline">
													<input type="radio" onclick="javascript:yesnoCheck();" name="trajet"
														id="retour" value="Aller retour" />&nbsp;&nbsp;&nbsp;Aller
													retour
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div style="clear:both;"></div>
							<div class="container-fluid form-group" style="display:none" id="date-retour"
								style="opacity:0">
								<div class="row form-inline">
									<div class="form-group c4">
										<label><i class="icon-calendar" aria-hidden="true"></i> Date retour</label>
										<input class="form-control" name="date_retour" id="date_retour" type="text"
											value="<?php if (isset($_SESSION['date_retour'])) {
												stripslashes($_SESSION['date_retour']);
											} ?>" placeholder="ex. dd/mm/yyyy" style="width:100%">
									</div>
									<div class="form-group c4">
										<label><i class="icon-time" aria-hidden="true"></i> Heure (retour)</label>
										<select name="heure_retour" class="form-control notranslate" style="width:100%">
											<option value="">Sélectionner...</option>
											<option value="00h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "00h") {
													echo ("selected");
												}
											} ?>>00h
											</option>
											<option value="01h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "01h") {
													echo ("selected");
												}
											} ?>>01h
											</option>
											<option value="02h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "02h") {
													echo ("selected");
												}
											} ?>>02h
											</option>
											<option value="03h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "03h") {
													echo ("selected");
												}
											} ?>>03h
											</option>
											<option value="04h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "04h") {
													echo ("selected");
												}
											} ?>>04h
											</option>
											<option value="05h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "05h") {
													echo ("selected");
												}
											} ?>>05h
											</option>
											<option value="06h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "06h") {
													echo ("selected");
												}
											} ?>>06h
											</option>
											<option value="07h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "07h") {
													echo ("selected");
												}
											} ?>>07h
											</option>
											<option value="08h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "08h") {
													echo ("selected");
												}
											} ?>>08h
											</option>
											<option value="09h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "09h") {
													echo ("selected");
												}
											} ?>>09h
											</option>
											<option value="10h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "10h") {
													echo ("selected");
												}
											} ?>>10h
											</option>
											<option value="11h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "11h") {
													echo ("selected");
												}
											} ?>>11h
											</option>
											<option value="12h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "12h") {
													echo ("selected");
												}
											} ?>>12h
											</option>
											<option value="13h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "13h") {
													echo ("selected");
												}
											} ?>>13h
											</option>
											<option value="14h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "14h") {
													echo ("selected");
												}
											} ?>>14h
											</option>
											<option value="15h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "15h") {
													echo ("selected");
												}
											} ?>>15h
											</option>
											<option value="16h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "16h") {
													echo ("selected");
												}
											} ?>>16h
											</option>
											<option value="17h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "17h") {
													echo ("selected");
												}
											} ?>>17h
											</option>
											<option value="18h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "18h") {
													echo ("selected");
												}
											} ?>>18h
											</option>
											<option value="19h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "19h") {
													echo ("selected");
												}
											} ?>>19h
											</option>
											<option value="20h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "20h") {
													echo ("selected");
												}
											} ?>>20h
											</option>
											<option value="21h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "21h") {
													echo ("selected");
												}
											} ?>>21h
											</option>
											<option value="22h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "22h") {
													echo ("selected");
												}
											} ?>>22h
											</option>
											<option value="23h" <?php if (isset($_SESSION['heure_retour'])) {
												if ($_SESSION['heure_retour'] == "23h") {
													echo ("selected");
												}
											} ?>>23h
											</option>
										</select>
									</div>
									<div class="form-group c4">
										<label><i class="icon-time" aria-hidden="true"></i> Minute (retour)</label>
										<select name="minute_retour" class="form-control notranslate"
											style="width:100%">
											<option value="">Sélectionner...</option>
											<option value="00min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "00min") {
													echo ("selected");
												}
											} ?>>00min
											</option>
											<option value="05min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "05min") {
													echo ("selected");
												}
											} ?>>05min
											</option>
											<option value="10min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "10min") {
													echo ("selected");
												}
											} ?>>10min
											</option>
											<option value="15min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "15min") {
													echo ("selected");
												}
											} ?>>15min
											</option>
											<option value="20min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "20min") {
													echo ("selected");
												}
											} ?>>20min
											</option>
											<option value="25min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "25min") {
													echo ("selected");
												}
											} ?>>25min
											</option>
											<option value="30min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "30min") {
													echo ("selected");
												}
											} ?>>30min
											</option>
											<option value="35min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "35min") {
													echo ("selected");
												}
											} ?>>35min
											</option>
											<option value="40min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "40min") {
													echo ("selected");
												}
											} ?>>40min
											</option>
											<option value="45min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "45min") {
													echo ("selected");
												}
											} ?>>45min
											</option>
											<option value="50min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "50min") {
													echo ("selected");
												}
											} ?>>50min
											</option>
											<option value="55min" <?php if (isset($_SESSION['minute_retour'])) {
												if ($_SESSION['minute_retour'] == "55min") {
													echo ("selected");
												}
											} ?>>55min
											</option>
										</select>
									</div>
								</div>
							</div>
							<div style="display:none;">
			<input type="text" id="fpickup" name="fpickup" value="asdsa">
			<input type="text" id="fdestin" name="fdestination">
		</div>
							<div style="clear:both;"></div>
							<div class="form-wizard-buttons" style="margin-bottom:10px;">
								<button type="submit" name="calculer" id="calculer" class="btn btn-submit"><i
										class="icon-road goTop" aria-hidden="true"></i> Calculer le trajet et le
									prix</button>
							</div>
							
						</form>

						<?php

						// on crée la fonction de calcul de distance et de prix
						
						function calculer_distance($adresse1, $adresse2)
						{
							$adresse1 = str_replace(" ", "+", $adresse1); //adresse de départ
							$adresse2 = str_replace(" ", "+", $adresse2); //adresse d'arrivée
							$url = 'https://maps.google.com/maps/api/directions/xml?language=fr&origin=' . $adresse1 . '&destination=' . $adresse2 . '&sensor=false&key=AIzaSyBfORYfXImx_02GW900_s-MJ6p87hTiq-4'; //on créé l'url
						
							// on lance une requête auprès de Google Maps avec l'url créée
						
							$ch = curl_init($url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
							$xml = curl_exec($ch);

							// on récupère les infos
						
							$charger_googlemap = simplexml_load_string($xml);
							$distance = $charger_googlemap->route->leg->distance->value;

							// si l'info est récupérée, on calcule la distance
						
							if ($charger_googlemap->status == "OK") {
								$distance = $distance / 1000;
								$distance = number_format($distance, 2, ',', ' ');
								return $distance;
							} else {

								// si l'info n'est pas récupérée, on lui attribue la valeur -1
						
								return "-1";
							}
						}

						function calculer_temps($adresse1, $adresse2, $heure_depart)
						{
							$adresse1 = urlencode($adresse1); //adresse de départ
							$adresse2 = urlencode($adresse2); //adresse d'arrivée
							$url = 'https://maps.google.com/maps/api/directions/xml?language=fr&origin=' . $adresse1 . '&destination=' . $adresse2 . '&departure_time=' . $heure_depart . '&mode=driving&sensor=false&key=AIzaSyBfORYfXImx_02GW900_s-MJ6p87hTiq-4'; //on crée l'url
						

							// on lance une requête auprès de Google Maps avec l'url créée
							$ch = curl_init($url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
							$xml = curl_exec($ch);

							// on récupère les infos
							$charger_googlemap = simplexml_load_string($xml);

							// si l'info est récupérée, on calcule la distance
							if ($charger_googlemap->status == "OK") {
								//Durée moyenne sans pris en compte du traffic
								//$duration = $charger_googlemap->route->leg->duration->value;
						
								//Durée en prenant en compte le traffic à l'heure de départ
								$duration = $charger_googlemap->route->leg->duration_in_traffic;

								return $duration;
							} else {
								// si l'info n'est pas récupérée, on lui attribue la valeur 0
								return "0";
							}
						}

						// si le bouton calculer est lancé, on récupère les informations du formulaire et on lance la fonction
						
						if (isset($_POST['calculer'])) {
							// Sécurisation des données de formulaire
							foreach ($_POST as $key => $value) {
								$sPOST[$key] = htmlspecialchars($value, ENT_COMPAT);
							}



							$depart = $sPOST['fpickup'];
							$arrivee = $sPOST['fdestination'];
							$date_aller = $sPOST['date_aller'];
							$heure = $sPOST['heure'];
							$minute = $sPOST['minute'];
							$date_retour = $sPOST['date_retour'];
							$heure_retour = $sPOST['heure_retour'];
							$minute_retour = $sPOST['minute_retour'];
							$passager = $sPOST['passager'];
							$bagages = $sPOST['bagages'];
							$trajet = $sPOST['trajet'];
							
							$date_aller_complet = $date_aller . " " . $heure . ":" . $minute . ":00";

							$timezone = new DateTimeZone('Europe/Paris');
							$heure_depart = DateTime::createFromFormat('d/m/Y H:i:s', $date_aller_complet, $timezone)->getTimestamp();
							
							$_SESSION["depart"] = $sPOST['depart'];
							$_SESSION["arrivee"] = $sPOST['arrivee'];
							$_SESSION["date_aller"] = $sPOST['date_aller'];
							$_SESSION["heure"] = $sPOST['heure'];
							$_SESSION["minute"] = $sPOST['minute'];
							$_SESSION["passager"] = $sPOST['passager'];
							$_SESSION["bagages"] = $sPOST['bagages'];
							$_SESSION['trajet'] = $trajet;


							if (isset($sPOST['prix'])) {
								$prix = $sPOST['prix'];
							}

							$prix = findPrice($depart, $arrivee, $trajet, $heure . ':' . $minute);
							$distance = $_SESSION["distance"];
							$duration = $_SESSION["duration"];
							// slack($prix, $depart, $arrivee, $trajet, $_SESSION["date_aller"] . ' ' . $heure . ' ' . $minute, null);

							// Règles prix (on impute la taxe de Stripe au client)
							// if ($distance > 0 && $distance < 17) {
							// 	$prix = 35;
							// } elseif ($distance > 16 && $distance < 21) {
							// 	$prix = 40;
							// } elseif ($distance > 20 && $distance < 31) {
							// 	$prix = 55;
							// } elseif ($distance > 30 && $distance < 51) {
							// 	$prix_km = 1.90;
							// 	$prix = $prix_km * $distance;
							// } elseif ($distance > 50) {
							// 	$prix_km = 1.7;
							// 	$prix = $prix_km * $distance;
							// }
						
							// $trajet = $_POST['trajet'];
						
							// if ($trajet == 'Aller simple') {
							// 	$prix = $prix;
							// 	$distance = $distance;
							// } elseif ($trajet == 'Aller retour') {
							// 	$prix = $prix * 1.9;
							// 	$distance = $distance * 2;
							// }
						
							$prix = ceil($prix * 100) / 100; // On arrondi au centième supérieur comme dans Stripe (ex: 28,642 arrondi à 28,65)
							$_SESSION["price"] = $prix;
							$prix = number_format($prix, 2, ',', ' ');

							echo '<p><i class="icon-time" aria-hidden="true" style="font-size:21px;color:#478fca"></i> <b>' . $_POST['date_aller'] . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' . $_POST['heure'] . 'h' . $_POST['minute'] . 'min</b></p>';
							echo '<p><i class="icon-map-marker" aria-hidden="true" style="font-size:23px;margin-right:5px;color:green"></i> <b>' . $depart . '</b></p>';
							echo '<p><i class="icon-map-marker" aria-hidden="true" style="font-size:23px;margin-right:5px;color:red"></i> <b>' . $arrivee . '</b></p>';

							// input values for the map
						
							echo '<input type="hidden" id="mapShow_address_departure" value="' . $depart . '">';
							echo '<input type="hidden" id="mapShow_address_arrival" value="' . $arrivee . '">';

							echo '<hr />';
							echo '<h4>Informations</h4>';
							echo '<div class="row">';
							echo '<p class="c6">Distance : <b>' . $distance . ' KM</b></p>';
							echo '<p class="c6">Durée estimée (<em>aller</em>) : <b>' . $duration . '</b></p>';
							echo '</div>';
							echo '<div class="row">';
							echo '<p class="c6">Passager(s) : <b>' . $passager . ' &ndash; ' . $bagages . '</b></p>';
							echo '<p class="c6">Trajet : <b>' . $_POST['trajet'] . ' &ndash; ' . $_POST['date_retour'] . ' &ndash; ' . $_POST['heure_retour'] . '' . $_POST['minute_retour'] . '</b></p>';
							echo '</div>';

							echo '<hr />';
							echo '<h4><b><span id="pricetag">Prix : ' . (($passager > 4 || $bagages > 4) ? $prix * 1.42 : $prix) . '-</span></b></h4>';
							// echo '<p>(&Agrave; partir de) : <b><span style="color:#fbc318;">' . $prix . ' EUROS</span></b></p>';
							echo '<hr />';

							// input values for the map
						
							echo '<div id="map"></div>';

							echo '<div class="form-wizard-buttons">';
							$date_aller_complet2 = str_replace('/', '-', $date_aller_complet);
							if (strtotime($date_aller_complet2) < ((time() + (3 * 3600)))) {
								echo '<div class="error" style="margin-bottom:5px; padding:10px;"><b>Veuillez nous contacter pour les reservations urgente</b></div>';
								echo '<a href="reservation.php" class="btn btn-previous"><i class="icon-refresh" aria-hidden="true"></i>  Nouvelle recherche</a>';

							} elseif ($depart != $arrivee && $distance != -1 && $distance != 0 && $distance < 500) {
								echo '<a href="reservation.php" class="btn btn-previous" style="margin-right:10px;"><i class="icon-refresh" aria-hidden="true"></i>  Nouvelle recherche</a>';
								echo '<button type="button" class="btn btn-next goTop"><i class="icon-car" aria-hidden="true"></i> Choix du véhicule</button>';
							} else {
								echo '<div class="error" style="margin-bottom:5px; padding:10px;"><b>Erreur ! Merci de réitérer votre recherche SVP</b></div>';
								echo '<a href="reservation.php" class="btn btn-previous"><i class="icon-refresh" aria-hidden="true"></i>  Nouvelle recherche</a>';
							}
							echo '</div>';

							echo '<style>form#journey{display:none}</style>';

							// Include Google Maps JS API (with callback initMap) -- only for build the itinerary
						
							echo '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&amp;key=AIzaSyBfORYfXImx_02GW900_s-MJ6p87hTiq-4&callback=initMap"></script>';
						} else {
							// Include Google Maps JS API
						
							echo '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&amp;key=AIzaSyBfORYfXImx_02GW900_s-MJ6p87hTiq-4"></script>';
						}

						// Formulaire de contact	
						// Vérifier que le formulaire a été envoyé...
						
						if (isset($_POST['envoi'])) {
							// Sécurisation des données de formulaire
							foreach ($_POST as $key => $value) {
								$sPOST[$key] = htmlspecialchars($value, ENT_COMPAT);
							}
							// Enregistrement des zones de champ...
							$_SESSION['nom'] = $sPOST['nom'];
							$_SESSION['prenom'] = $sPOST['prenom'];
							$_SESSION['email'] = str_replace(' ', '', $sPOST['email']);
							$_SESSION['telephone'] = $sPOST['telephone'];
							$_SESSION['depart'] = $sPOST['depart'];
							$_SESSION['arrivee'] = $sPOST['arrivee'];
							$_SESSION['trajet'] = $sPOST['trajet'];
							$_SESSION['distance'] = $sPOST['distance'];
							$_SESSION['duration'] = $sPOST['duration'];
							$_SESSION['date_aller'] = $sPOST['date_aller'];
							$_SESSION['date_retour'] = $sPOST['date_retour'];
							$_SESSION['horaire'] = $sPOST['horaire'];
							$_SESSION['horaire_retour'] = $sPOST['horaire_retour'];
							$_SESSION['passager'] = $sPOST['passager'];
							$_SESSION['bagages'] = $sPOST['bagages'];
							$_SESSION['price'] = $sPOST['prix'];
							$_SESSION['reference'] = $sPOST['reference'];
							$_SESSION['message'] = $sPOST['message'];
							$_SESSION["payeename"] = $sPOST['nom'];
							$_SESSION["payeemail"] = str_replace(' ', '', $sPOST['email']);
							$datecomplete=$_SESSION['horaire'] . ' ' . $_SESSION['date_aller'];
							slack($_SESSION['price'], $_SESSION['depart'], $_SESSION['arrivee'], $sPOST['date_retour'], $datecomplete, 'PAYMENT page --- Tel=' . $_SESSION['telephone']);


							// Enregistrement des boutons...
							$percent = 1;
							switch ($_POST['vehicule']) {
								case "Renault Megane BR":
									$_SESSION['vehicule'] = "Renault Megane BR";
									$percent = 1;
									break;

								case "Opel Vivaro":
									$_SESSION['vehicule'] = "Opel Vivaro";
									$percent = 1.42;
									break;

								case "Mercedes Class E":
									$_SESSION['vehicule'] = "Mercedes Class E";
									$percent = 1.25;
									break;

								case "Mercedes Vito":
									$_SESSION['vehicule'] = "Mercedes Vito";
									$percent = 1.70;
									break;
								case "Kia Ceed":
									$_SESSION['vehicule'] = "Kia Ceed";
									$percent = 1.25;
									break;									

								default:
									$_SESSION['vehicule'] = "";
							}
							$_SESSION['price'] = $_SESSION['price'] * $percent;


							// Enregistrement des cases...
						
							$_SESSION['baby'] = "";
							if (isset($_POST['baby'])) {
								$_SESSION['price'] = $_SESSION['price'] + 5;
								$_SESSION['baby'] = $_POST['baby'];
								
							} // Siège auto bébé
						
							echo '<style>#phase_1 {display:none !important} #phase_2 {display:block !important}</style>';

							// try {
							// Ouverture de la connexion à la BDD
						
							// $date = date('Y-m-d H:i:s');
							// $date_aller = $sPOST['date_aller'].' - '.$sPOST['horaire'];
							// $hours = floor($sPOST['duration'] / 3600);
							// $mins = round($sPOST['duration'] / 60) % 60;
							// $duration = $hours.':'.$mins.':00';
						
							// $sql = "INSERT INTO reservations (adresse_depart,adresse_arrivee,date_depart,nb_passagers,nb_bagages,distance,duree_estimee,prix,nom,prenom,email,telephone,infos,statut_paiement,date_resa) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
							// $pdo->prepare($sql)->execute([
							// utf8_decode($sPOST['depart']), 
							// utf8_decode($sPOST['arrivee']), 
							// $date_aller, 
							// $sPOST['passager'], 
							// $sPOST['bagages'], 
							// str_replace(',','.',$sPOST['distance']), 
							// $duration, 
							// str_replace(',','.',$sPOST['prix']), 
							// utf8_decode($sPOST['nom']),
							// utf8_decode($sPOST['prenom']),
							// $sPOST['email'],
							// $sPOST['telephone'],
							// ($sPOST['message']=='') ? NULL : utf8_decode($sPOST['message']),
							// NULL,
							// $date
							// ]);
						
							// $_SESSION['id_resa'] = $pdo->lastInsertId();
						
							// Fermeture de la connexion
							// $sql = null;
							// $bdd = null;
							// } catch (PDOException $e) {
							// print "Erreur de connexion a la BDD : " . $e->getMessage() . "<br/>";
							// die();
							// }
						} // Fin de if POST
						
						?>
					</fieldset>
					<!-- Form Step 1 -->

					<!-- Form Step 2 -->
					<fieldset>
						<!-- Progress Bar -->
						<div class="progress">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40"
								aria-valuemin="0" aria-valuemax="100" style="width:40%"></div>
						</div>
						<!-- Progress Bar -->

						<h4 class="maintitle"><span class="stepTitle">Résumé</span></h4>

						<?php
						echo '<div class="row">';
						echo '<p class="c6" style="margin:0"><i class="icon-time" aria-hidden="true" style="color:#478fca"></i> ' . $_POST['date_aller'] . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' . $_POST['heure'] . 'h' . $_POST['minute'] . 'min</p>';
						echo '<p class="c6" style="margin:0"><i class="icon-user" aria-hidden="true" style="color:gray"></i> ' . $passager . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;<i class="icon-suitcase" aria-hidden="true" style="color:gray"></i> ' . $bagages . '</p>';
						echo '</div>';
						echo '<div class="row">';
						echo '<p class="c6" style="margin:0"><i class="icon-map-marker" aria-hidden="true" style="color:green"></i> ' . $depart . '</p>';
						echo '<p class="c6" style="margin:0"><i class="icon-map-marker" aria-hidden="true" style="color:red"></i> ' . $arrivee . '</p>';
						echo '</div>';
						echo '<div class="row">';
						echo '<p class="c12" style="margin:0"><i class="icon-location-arrow" aria-hidden="true" style="color:black"></i> ' . $_POST['trajet'] . ' : ' . $distance . ' KM</p>';
						echo '</div>';
						?>

						<hr />

						<!-- form checking -->
						<script language="JavaScript">

							function checkDate() {
								var today = new Date();
								var today_date = today.getDate(); if (today_date < 10) today_date = "0" + today_date;
								var today_month = today.getMonth() + 1; if (today_month < 10) today_month = "0" + today_month;
								var today_year = today.getFullYear();
								var today_hour = today.getHours(); if (today_hour < 10) today_hour = "0" + today_hour;

								today = today_date + "/" + today_month + "/" + today_year;

								if (document.journey.date_aller.value == today) {
									console.log('rtre check date');
									// Le jour même on empèche de réserver 2h avant
									today_hour++; today_hour++;
									var op = document.journey.heure.getElementsByTagName("option");
									for (var i = 0; i < op.length; i++) {
										if (today_hour < 23) {
											(op[i].value < today_hour) ? op[i].hidden = true : op[i].hidden = false;
										} else {
											op[i].hidden = true;
										}
									}
								} else if (document.journey.date_aller.value > today) {
									// Si c'est une date ultérieure on affiche toutes les heures
									var op = document.journey.heure.getElementsByTagName("option");
									for (var i = 0; i < op.length; i++) {
										op[i].hidden = false;
									}
								} else {
									// On empèche de rentrer manuellement une date antérieure au jour actuel
									document.journey.date_aller.value = "";
									var op = document.journey.heure.getElementsByTagName("option");
									for (var i = 0; i < op.length; i++) {
										op[i].hidden = false;
									}
								}
							}

							function verifSelection() {

								if (document.mail_form.depart.value == "") {
									alert("Veuillez préciser la commune de départ SVP")
									return false
								}

								if (document.mail_form.arrivee.value == "") {
									alert("Veuillez préciser la commune d'arrivée SVP")
									return false
								}

								if (document.mail_form.distance.value == "") {
									alert("Erreur : distance non définie !")
									return false
								}

								if (document.mail_form.date_aller.value == "") {
									alert("Erreur : date aller non définie !")
									return false
								}

								if (document.mail_form.horaire.value == "") {
									alert("Erreur : horaire aller non définie !")
									return false
								}

								if (document.mail_form.vehicule.value == "") {
									alert("Veuillez choisir un véhicule SVP")
									return false
								}

								if (document.mail_form.nom.value == "") {
									alert("Veuillez préciser votre nom SVP")
									return false
								}

								if (document.mail_form.prenom.value == "") {
									alert("Veuillez préciser votre prénom SVP")
									return false
								}

								if (document.mail_form.email.value == "") {
									alert("Veuillez préciser votre adresse email SVP")
									return false
								}

								atPos = document.mail_form.email.value.indexOf("@", 1)			// there must be one "@" symbol
								if (atPos == -1) {
									alert('Votre adresse e-mail ne contient pas le signe "@". Veuillez vérifier.')
									document.mail_form.email.focus()
									return false
								}

								if (document.mail_form.email.value.indexOf("@", atPos + 1) != -1) {	// and only one "@" symbol
									alert('Il ne doit y avoir qu\'un signe "@". Veuillez vérifier.')
									document.mail_form.email.focus()
									return false
								}

								periodPos = document.mail_form.email.value.indexOf(".", atPos)

								if (periodPos == -1) {					// and at least one "." after the "@"
									alert('Vous avez oublié le point "." après le signe "@". Veuillez vérifier.')
									document.mail_form.email.focus()
									return false
								}

								if (periodPos + 3 > document.mail_form.email.value.length) {		// must be at least 2 characters after the 
									alert('Il doit y avoir au moins deux caractères après le signe ".". Veuillez vérifier.')
									document.mail_form.email.focus()
									return false
								}

								if (document.mail_form.telephone.value == "") {
									alert("Veuillez préciser votre numéro de téléphone SVP")
									return false
								}

								if (document.mail_form.cgv.checked == false) {
									alert("Vous devez accepter nos conditions générales de service.")
									return false
								}

							} // Fin de la fonction

						</script>
						<!-- ENDS form checking -->

						<form name="mail_form" method="post" action="<?= $_SERVER['PHP_SELF'] ?>"
							onSubmit="return verifSelection()">
							<h4 class="maintitle"><span class="stepTitle">Choix du véhicule</span><span
									class="step"><em>Étape 2 - 5</em></span></h4>

							<!-- Renault Megane BR -->
							<div style="display:<?php if($_SESSION["bagages"] > 3 || $_SESSION["passager"] > 4) echo "none"; ?>" class="form-group">
								<label style="margin-bottom:-15px;">
									<h4 style="color:#fbc318;"><b>Economic Class</b> &ndash; <em>Renault Megane BR</em></h4>
								</label>  
								<p style="font-size:13px;margin-top:0;margin-bottom:0;">
									<i class="icon-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max.
									4
									&nbsp;&nbsp;&nbsp;
									<i class="icon-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i>
									max. 3
								</p>
								<hr style="margin-top:5px;" />
								<div class="c8">
									<img class="img-responsive" src="images/vehicules/skoda.jpg"
										style="max-width:300px !important;" title="Renault Megane BR" alt="Renault Megane BR" />
								</div>
								<div class="c4">
									<?php echo '<p style="color:#fbc318;font-size:15px;border:1px solid #fbc318;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . $prix . ' EUROS</b></p>'; ?>
									<p><i class="icon-credit-card" aria-hidden="true"></i> Paiement à bord</p>
									<p><i class="icon-wifi" aria-hidden="true"></i> Wifi <i class="icon-tint"
											aria-hidden="true"></i> Eau (petite bout.)</p>
									<p><i class="icon-time" aria-hidden="true"></i> 60 min. d'attente gratuite</p>
									<label class="control control--radio">
										Sélectionner <i class="icon-chevron-right" aria-hidden="true"></i>
										<input type="radio" class="btn btn-next goTop" name="vehicule"
											value="Renault Megane BR" <?php if (isset($_SESSION['vehicule'])) {
												if ($_SESSION['vehicule'] == "Renault Megane BR") {
													echo ("checked");
												}
											} ?>>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
							<div style="clear:both;height:30px;"></div>

							<!-- OPEL VIVARO -->
							<div class="form-group">
								<label style="margin-bottom:-15px;">
									<h4 style="color:#fbc318;"><b>Economic Van</b> &ndash; <em>Opel Vivaro</em></h4>
								</label>
								<p style="font-size:13px;margin-top:0;margin-bottom:0;">
									<i class="icon-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max.
									8
									&nbsp;&nbsp;&nbsp;
									<i class="icon-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i>
									max. 8
								</p>
								<hr style="margin-top:5px;" />
								<div class="c8">
									<img class="img-responsive" src="images/vehicules/vivaro.jpg"
										style="max-width:300px !important;" title="Opel Vivaro" alt="Opel Vivaro" />
								</div>
								<div class="c4">
									<?php echo '<p style="color:#fbc318;font-size:15px;border:1px solid #fbc318;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . ($prix * 1.42) . ' EUROS</b></p>'; ?>
									<p><i class="icon-credit-card" aria-hidden="true"></i> Paiement à bord</p>
									<p><i class="icon-wifi" aria-hidden="true"></i> Wifi <i class="icon-tint"
											aria-hidden="true"></i> Eau (petite bout.)</p>
									<p><i class="icon-time" aria-hidden="true"></i> 60 min. d'attente gratuite</p>
									<label class="control control--radio">
										Sélectionner <i class="icon-chevron-right" aria-hidden="true"></i>
										<input type="radio" class="btn btn-next goTop" name="vehicule"
											value="Opel Vivaro" <?php if (isset($_SESSION['vehicule'])) {
												if ($_SESSION['vehicule'] == "Opel Vivaro") {
													echo ("checked");
												}
											} ?>>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
							<div style="clear:both;height:30px;"></div>

							<!-- MERCEDES CLASS E AMG -->
							<div style="display:<?php if($_SESSION["bagages"] > 3 || $_SESSION["passager"] > 4) echo "none"; ?>" class="form-group">
								<label style="margin-bottom:-15px;">
									<h4 style="color:#fbc318;"><b>Business Class</b> &ndash; <em>Mercedes Class E
											AMG</em></h4>
								</label>
								<p style="font-size:13px;margin-top:0;margin-bottom:0;">
									<i class="icon-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max.
									3
									&nbsp;&nbsp;&nbsp;
									<i class="icon-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i>
									max. 3
								</p>
								<hr style="margin-top:5px;" />
								<div class="c8">
									<img class="img-responsive" src="images/vehicules/class_e.png"
										style="max-width:300px !important;" title="Mercedes Class E AMG"
										alt="Mercedes Class E AMG" />
								</div>
								<div class="c4">
									<?php echo '<p style="color:#fbc318;font-size:15px;border:1px solid #fbc318;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . ($prix * 1.25) . ' EUROS</b></p>'; ?>
									<p><i class="icon-credit-card" aria-hidden="true"></i> Paiement à bord</p>
									<p><i class="icon-wifi" aria-hidden="true"></i> Wifi <i class="icon-tint"
											aria-hidden="true"></i> Eau (petite bout.)</p>
									<p><i class="icon-time" aria-hidden="true"></i> 60 min. d'attente gratuite</p>
									<label class="control control--radio">
										Sélectionner <i class="icon-chevron-right" aria-hidden="true"></i>
										<input type="radio" class="btn btn-next goTop" name="vehicule"
											value="Mercedes Class E" <?php if (isset($_SESSION['vehicule'])) {
												if ($_SESSION['vehicule'] == "Mercedes Class E") {
													echo ("checked");
												}
											} ?>>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
							<div style="clear:both;height:30px;"></div>

							<!-- Kia Ceed -->
							<div style="display:<?php if($_SESSION["bagages"] > 4 || $_SESSION["passager"] > 4) echo "none"; ?>" class="form-group">
								<label style="margin-bottom:-15px;">
									<h4 style="color:#fbc318;"><b>Electric Class</b> &ndash; <em>Kia Ceed / Renault  Megane Electric</em></h4>
								</label>
								<p style="font-size:13px;margin-top:0;margin-bottom:0;">
									<i class="icon-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max.
									4
									&nbsp;&nbsp;&nbsp;
									<i class="icon-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i>
									max. 4
								</p>
								<hr style="margin-top:5px;" />
								<div class="c8">
									<img class="img-responsive" src="images/vehicules/kiaceed.png"
										style="max-width:300px !important;" title="Kia Ceed"
										alt="Kia Ceed" />
								</div>
								<div class="c4">
									<?php echo '<p style="color:#fbc318;font-size:15px;border:1px solid #fbc318;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . ($prix * 1.25) . ' EUROS</b></p>'; ?>
									<p><i class="icon-credit-card" aria-hidden="true"></i> Paiement à bord</p>
									<p><i class="icon-wifi" aria-hidden="true"></i> Wifi <i class="icon-tint"
											aria-hidden="true"></i> Eau (petite bout.)</p>
									<p><i class="icon-time" aria-hidden="true"></i> 60 min. d'attente gratuite</p>
									<label class="control control--radio">
										Sélectionner <i class="icon-chevron-right" aria-hidden="true"></i>
										<input type="radio" class="btn btn-next goTop" name="vehicule"
											value="Kia Ceed" <?php if (isset($_SESSION['vehicule'])) {
												if ($_SESSION['vehicule'] == "Kia Ceed") {
													echo ("checked");
												}
											} ?>>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
							<div style="clear:both;height:30px;"></div>
							<!-- MERCEDES Vito -->
							<div class="form-group">
								<label style="margin-bottom:-15px;">
									<h4 style="color:#fbc318;"><b>Business Van</b> &ndash; <em>Mercedes Vito</em>
									</h4>
								</label>
								<p style="font-size:13px;margin-top:0;margin-bottom:0;">
									<i class="icon-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max.
									8
									&nbsp;&nbsp;&nbsp;
									<i class="icon-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i>
									max. 8
								</p>
								<hr style="margin-top:5px;" />
								<div class="c8">
									<img class="img-responsive" src="images/vehicules/class_v.png"
										style="max-width:300px !important;" title="Mercedes Vito"
										alt="Mercedes Vito" />
								</div>
								<div class="c4">
									<?php echo '<p style="color:#fbc318;font-size:15px;border:1px solid #fbc318;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . ($prix * 1.70) . ' EUROS</b></p>'; ?>
									<p><i class="icon-credit-card" aria-hidden="true"></i> Paiement à bord</p>
									<p><i class="icon-wifi" aria-hidden="true"></i> Wifi <i class="icon-tint"
											aria-hidden="true"></i> Eau (petite bout.)</p>
									<p><i class="icon-time" aria-hidden="true"></i> 60 min. d'attente gratuite</p>
									<label class="control control--radio">
										Sélectionner <i class="icon-chevron-right" aria-hidden="true"></i>
										<input type="radio" class="btn btn-next goTop" name="vehicule"
											value="Mercedes Vito" <?php if (isset($_SESSION['vehicule'])) {
												if ($_SESSION['vehicule'] == "Mercedes Vito") {
													echo ("checked");
												}
											} ?>>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
							<div style="clear:both;height:15px;"></div>
							
							
							<div style="clear:both;height:30px;"></div>	
							<div class="form-wizard-buttons">
								Tous nos prix comprennent la TVA.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-previous goTop">Précédent</button>
							</div>
					</fieldset>
					<!-- Form Step 2 -->

					<!-- Form Step 3 -->
					<fieldset>
						<!-- Progress Bar -->
						<div class="progress">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60"
								aria-valuemin="0" aria-valuemax="100" style="width:60%"></div>
						</div>
						<!-- Progress Bar -->

						<h4 class="maintitle"><span class="stepTitle">Résumé</span></h4>

						<?php
						echo '<div class="row">';
						echo '<p class="c6" style="margin:0"><i class="icon-time" aria-hidden="true" style="color:#478fca"></i> ' . $_POST['date_aller'] . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' . $_POST['heure'] . 'h' . $_POST['minute'] . 'min</p>';
						echo '<p class="c6" style="margin:0"><i class="icon-user" aria-hidden="true" style="color:gray"></i> ' . $passager . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;<i class="icon-suitcase" aria-hidden="true" style="color:gray"></i> ' . $bagages . '</p>';
						echo '</div>';
						echo '<div class="row">';
						echo '<p class="c6" style="margin:0"><i class="icon-map-marker" aria-hidden="true" style="color:green"></i> ' . $depart . '</p>';
						echo '<p class="c6" style="margin:0"><i class="icon-map-marker" aria-hidden="true" style="color:red"></i> ' . $arrivee . '</p>';
						echo '</div>';
						echo '<div class="row">';
						echo '<p class="c12" style="margin:0"><i class="icon-location-arrow" aria-hidden="true" style="color:black"></i> ' . $_POST['trajet'] . ' : ' . $distance . ' KM</p>';
						echo '</div>';
						?>

						<hr />

						<h4 class="maintitle"><span class="stepTitle">Informations</span><span class="step"><em>Étape 3
									- 5</em></span></h4>

						<div class="container-fluid form-group">
							<div class="row form-inline">
								<div class="form-group c6">
									<label>Nom</label>
									<input type="text" class="form-control" style="width:100%" name="nom"
										value="<?php echo $_SESSION['nom'] == null ? '' : $_SESSION['nom'] ?>"
										placeholder="ex. Dupont" />
								</div>
								<div class="form-group c6">
									<label>Prénom</label>
									<input type="text" class="form-control" style="width:100%" name="prenom"
										value="<?php echo $_SESSION['prenom'] == null ? '' : $_SESSION['prenom'] ?>"
										placeholder="ex. Jacques" />
								</div>
							</div>
						</div>
						<div style="clear:both;"></div>
						<div class="container-fluid form-group">
							<div class="row form-inline">
								<div class="form-group c6">
									<label>Email</label>
									<input type="text" class="form-control" style="width:100%" name="email"
										value="<?php echo $_SESSION['email'] == null ? '' : $_SESSION['email'] ?>"
										placeholder="ex. email@exemple.com" />
								</div>
								<div class="form-group c6">
									<label>Téléphone</label>
									<input type="text" class="form-control" style="width:100%" name="telephone"
										value="<?php echo $_SESSION['telephone'] == null ? '' : $_SESSION['telephone'] ?>"
										placeholder="ex. +32.123456789" />
								</div>
							</div>
						</div>
						<div style="clear:both;"></div>
						<div class="container-fluid form-group">
							<div class="row form-inline">
								<div class="form-group c6">
									<label>N. Vol, train...</label>
									<input type="text" class="form-control" style="width:100%" name="reference"
										value="<?php echo $_SESSION['reference'] == null ? '' : $_SESSION['reference'] ?>"
										placeholder="ex. Vol AB1234 Zaventem" />
								</div>
								<div class="form-group c6">
									<label><i class="icon-child" aria-hidden="true"></i> Siège enfant</label>
									<div style="clear:both;"></div>
									<label>
										<input type="checkbox" name="baby" value="Siège bébé demandé" <?php if (!empty($_SESSION['baby'])) {
											if ($_SESSION['baby'][0] == "Siège bébé demandé") {
												echo ("checked");
											}
										} ?>>
										Siège auto bébé demandé
									</label>
								</div>
							</div>
						</div>
						<div style="clear:both;"></div>
						<div class="container-fluid form-group">
							<label>Infos supplémentaires</label>
							<textarea class="form-control" style="border-radius:4px;" name="message" cols="45" rows="3"
								placeholder="Votre message ici..."><?php echo $_SESSION['message'] == null ? '' : $_SESSION['message'] ?></textarea>
						</div>
						<div class="form-group">
							<label>Conditions générales</label>
							<div style="clear:both;"></div>
							<label>
								<input type="checkbox" name="cgv" value="acceptées" checked />
								Je reconnais avoir pris connaissance des <a href="conditions.html" target="_blank"><strong>conditions
										générales de vente</strong></a> et je les accepte.
							</label>
						</div>
						<div style="clear:both;"></div>

						<!-- Form details -->
						<input type="hidden" name="depart" id="depart" value="<?= stripslashes($depart); ?>" />
						<input type="hidden" name="arrivee" id="arrivee" value="<?= stripslashes($arrivee); ?>" />
						<input type="hidden" name="trajet" id="trajet" value="<?= stripslashes($trajet); ?>" />
						<input type="hidden" name="distance" id="distance" value="<?= stripslashes($distance); ?>" />
						<input type="hidden" name="duration" id="duration"
							value="<?= stripslashes($duration_value); ?>" />
						<input type="hidden" name="date_aller" id="date_aller"
							value="<?= stripslashes($date_aller); ?>" />
						<input type="hidden" name="date_retour" id="date_retour"
							value="<?= stripslashes($date_retour); ?>" />
						<input type="hidden" name="horaire" id="horaire"
							value="<?php echo $heure . 'h' . $minute; ?>" />
						<input type="hidden" name="horaire_retour" id="horaire_retour"
							value="<?php echo $heure_retour . $minute_retour; ?>" />
						<input type="hidden" name="passager" id="passager" value="<?= stripslashes($passager); ?>" />
						<input type="hidden" name="bagages" id="bagages" value="<?= stripslashes($bagages); ?>" />
						<input type="hidden" name="prix" id="prix" value="<?= stripslashes($prix); ?>" />
						<!-- Form details -->

						<div class="form-wizard-buttons">
							<button type="button" class="btn btn-previous goTop">Précédent</button>
							<button type="submit" name="envoi" class="btn btn-submit goTop"><i class="icon-check"
									aria-hidden="true"></i> Paiement</button>
						</div>
						</form>
					</fieldset>
					<!-- Form Step 3 -->
				</div>
				<div class="c3 simplebox">
					<h2 class="title stresstitle">Réservation mini-bus</h2>
					<hr class="hrtitle" />
					<b>Pour tous vos déplacements de groupe</b>
					<ul>
						<li style="color:green;"><i class="icon-user" aria-hidden="true"></i> <b>Bus 19 places, jusqu'à
								50 pers.</b></li>
						<li style="color:green;"><i class="icon-calendar" aria-hidden="true"></i> <b>Réservation 2/3
								semaines à l'avance</b></li>
						<li style="color:red;">
							<marquee><i class="icon-phone" aria-hidden="true"></i> <b>Devis par téléphone : (+32) 492
									061 896</b></marquee>
						</li>
					</ul>
					<br />
					<h2 class="title stresstitle">Transport</h2>
					<hr class="hrtitle" />
					<h6>Véhicule :
						<span style="float:right;font-size:13px;color:grey;">
							<a href="vehicules.html">Voir nos modèles</a>
						</span>
					</h6>
					<h6>Capacité :
						<span style="float:right;font-size:13px;color:grey;">1 - 6 personne(s)</span>
					</h6>
					<h6>Extras :
						<span style="float:right;font-size:13px;color:grey;">Climatisation</span>
					</h6>
					<h6>
						<span style="color:transparent">Extras : </span>
						<span style="float:right;font-size:13px;color:grey;">Bouteilles d'eau</span>
					</h6>
					<h6>
						<span style="color:transparent">Extras : </span>
						<span style="float:right;font-size:13px;color:grey;">Wifi - Presse</span>
					</h6>
					<br />
					<h2 class="title stresstitle">Prestations</h2>
					<hr class="hrtitle" />
					<ul class="space-bot">
						<li><a href="navette-aeroport.html"><b> &mdash; Navette aéorport</b></a></li>
						<li><a href="chauffeur-prive.html"><b> &mdash; Chauffeur privé</b></a></li>
						<li><a href="reservation.php" style="color:#cb1010;"><b> &mdash; Réservation en ligne</b></a>
						</li>
					</ul>
					<br />
					<h2 class="title stresstitle">Nos atouts</h2>
					<hr class="hrtitle" />
					<ul class="icons space-bot">
						<li><i class="icon-check"></i> Chauffeur professionnel</li>
						<li><i class="icon-check"></i> Ponctualité et discretion</li>
						<li><i class="icon-check"></i> Attente de 60 min aux aéroports GRATUIT </li>
						<li><i class="icon-check"></i> Service disponible 24/7</li>
						<li><i class="icon-check"></i> <a href="reservation.php" style="color:#cb1010;"><b>Réservation
									en ligne</b></a></li>
					</ul>
					<br />
					<h2 class="title stresstitle">Paiement</h2>
					<hr class="hrtitle" />
					<center>
						<img src="images/paiement.png" title="Paiement" alt="Paiement" />
					</center>
					<p style="font-size:13px;">
						<i class="icon-info-sign"></i> Paiement CB à bord accepté
					</p>
				</div>
			</section>

			<!-- main content -->
			<section id="phase_2" class="form-box" style="display:none">
				<div class="c9 form-wizard_2">
					<!-- <span>Remplissez tous les champs de formulaire pour aller à l'étape suivante</span> -->
					<!-- Form progress -->
					<div class="form-wizard-steps form-wizard-tolal-steps-5">
						<div class="form-wizard-progress">
							<div class="form-wizard-progress-line" data-now-value="67.5" data-number-of-steps="5"
								style="width: 67.5%;"></div>
						</div>
						<!-- Step 1 -->
						<div class="form-wizard-step activated">
							<div class="form-wizard-step-icon"><i class="icon-road" aria-hidden="true"></i></div>
							<p>Trajet</p>
						</div>
						<!-- Step 1 -->

						<!-- Step 2 -->
						<div class="form-wizard-step activated">
							<div class="form-wizard-step-icon"><i class="icon-car" aria-hidden="true"></i></div>
							<p>Prestation</p>
						</div>
						<!-- Step 2 -->

						<!-- Step 3 -->
						<div class="form-wizard-step activated">
							<div class="form-wizard-step-icon"><i class="icon-user" aria-hidden="true"></i></div>
							<p>Coordonnées</p>
						</div>
						<!-- Step 3 -->

						<!-- Step 4 -->
						<div class="form-wizard-step active">
							<div class="form-wizard-step-icon"><i class="icon-credit-card" aria-hidden="true"></i></div>
							<p>Paiement</p>
						</div>
						<!-- Step 4 -->

						<!-- Step 5 -->
						<div class="form-wizard-step">
							<div class="form-wizard-step-icon"><i class="icon-check" aria-hidden="true"></i></div>
							<p>Confirmation</p>
						</div>
						<!-- Step 5 -->
					</div>
					<!-- Form progress -->

					<!-- Form Step 4 -->
					<fieldset>
						<!-- Progress Bar -->
						<div class="progress">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="80"
								aria-valuemin="0" aria-valuemax="100" style="width:80%"></div>
						</div>

						<!-- Progress Bar -->
						<h4 class="maintitle"><span class="stepTitle">Paiement</span><span class="step"><em>Étape 4 -
									5</em></span></h4>

						<?php

						?>

						<div style="clear:both;"></div>
						<hr />
						<div class="container-fluid form-group">
							<div class="row form-inline">

								<p>
									<center><b></b></center>
								</p>
								<br>

								<div class="form-group c12">
									<!-- <label><img src="images/stripe_payment_logo.png" align="absmiddle"
											alt="Stripe Payment Logo" style="width:400px;margin-bottom:50px;" /></label> -->
									<br />
									<?php
									echo '<table class="c12">';
									echo '<tr>';
									echo '<td style="width:50%">Adresse de départ</td>';
									echo '<td style="text-align:right;"><span style="color:#cb1010;">' . $_SESSION['depart'] . '</span></td>';
									echo '</tr>';
									echo '<tr>';
									echo '<td style="width:50%">Adresse d\'arrivée</td>';
									echo '<td style="text-align:right;"><span style="color:#cb1010;">' . $_SESSION['arrivee'] . '</span></td>';
									echo '</tr>';
									echo '<tr>';
									echo '<td style="width:50%">Distance</td>';
									echo '<td style="text-align:right;"><span style="color:#cb1010;">' . $_SESSION['distance'] . ' KM</span></td>';
									echo '</tr>';
									echo '<tr style="border-bottom: 1px solid #999;">';
									echo '<td style="width:50%">Véhicule</td>';
									echo '<td style="text-align:right;"><span style="color:#cb1010;">' . $_SESSION['vehicule'] . '</span></td>';
									echo '</tr>';
									echo '<tr>';
									echo '<td style="width:50%">Prix</td>';
									echo '<td style="text-align:right;"><span style="color:#cb1010;"><b>' . $_SESSION['price'] . ' €</b></span></td>';
									echo '</tr>';
									echo '</table>';
									?>
									<br />
									<hr />
									<b>
										<center>Choisissez ici votre mode de paiement</center>
									</b>
									<br />

									<?php

									require_once 'modules/stripe-php/init.php';
									$global_config = require('utils/const.php');
									if ($_POST ) {
										\Stripe\Stripe::setApiKey($global_config['stripe_sk']); // Clé secrète test
										$session = \Stripe\Checkout\Session::create([
											'payment_method_types' => ['card','klarna'],
											'customer_email' => $_SESSION["payeemail"],
											'line_items' => [
												[
													'price_data' => [
														'currency' => 'eur',
														'product_data' => [
															'name' => 'Reservation Airport Taxi Shuttle',
															'images'=> ['https://airportaxiofficial.be/images/logo.png'],															
														],
														'unit_amount' => $_SESSION['price'] * 100,
													],
													'quantity' => 1,
													
												]
											],
											'mode' => 'payment',
											'success_url' => "https://airportaxiofficial.be/success.php?method=visa",
											'cancel_url' => "https://airportaxiofficial.be",
											'metadata' => [
												'depart' => $_SESSION["depart"],
												'dest' => $_SESSION["arrivee"],
												'date' => $_SESSION["date_aller"],
												'time' => $_SESSION["horaire"],
												'price' => $_SESSION["price"],
												'phone' => $_SESSION["telephone"],
												'email' => $_SESSION["email"],
												'passengers' => $_SESSION["passager"],
												'bags' => $_SESSION["bagages"],
												'car_type' => $_SESSION["vehicule"],
												'pickup_time_ret' => $_SESSION["horaire_retour"],
												'date_ret' => $_SESSION["date_retour"],
												'flight_num' => $_SESSION["reference"],
												'journey_type' => $_SESSION["trajet"],
												'fname' => $_SESSION["prenom"],
												'lname' => $_SESSION["nom"],
												'is_airport' => $_SESSION["origin_airport"],								
												'comments' => mb_strimwidth($_SESSION["message"], 0, 50, "..."),
												'babyseat' => $_SESSION['baby'],
												'distance' => $_SESSION['distance'],
											],
										]);
										//   $tmp_cmp = strpos($session,'{');
										//   $session = substr($session,$tmp_cmp);
										$_SESSION["stripe_session"] = $session->id;
										$error = '';
										$success = '';

										try {
											if (!isset($_POST['stripeToken']))
												throw new Exception("The Stripe Token was not generated correctly");

											$customerStripe = \Stripe\Customer::create(
												array(
													"email" => $_POST['email'],
													"source" => $_POST['stripeToken'],
												)
											);

											\Stripe\Charge::create(
												array(
													"amount" => $_SESSION["price"],
													"currency" => "eur",
													"customer" => $customerStripe->id,
													"description" => "Réservation sur Airportaxiofficial - Paiement Stripe"
												)
											);

										} catch (Exception $e) {
											$error = $e->getMessage();
										}
									}
									?>

									<form action="" method="POST" id="formPayment">
										<div title="Visa" class="payMethod visa" onClick="creditcard()">
											<input type="radio" name="pay" id="visaRadio">

										</div>
										<div title="MasterCard" class="payMethod master" onClick="creditcard()">
											<input type="radio" name="pay" id="masterCardRadio">
										</div>
										<div title="AMEX" class="payMethod amex" onClick="creditcard()">
											<input type="radio" name="pay" id="amexRadio">
										</div>
										<div title="Bancontact" class="payMethod banque" onClick="bancontact()">
											<input type="radio" name="pay" id="banqueRadio">
										</div>
										<div title="Paypal" class="payMethod paypal" onClick="paypal()">
											<input type="radio" name="pay" id="paypalRadio">
										</div>
										<div title="GooglePay" class="payMethod gPay" onClick="creditcard()">
											<input type="radio" name="pay" id="gPayRadio">
										</div>
										<div title="ApplePay" class="payMethod aPay" onClick="creditcard()">
											<input type="radio" name="pay" id="aPayRadio">
										</div>
										<div title="Cash" class="payMethod cash" onClick="cash()">
											<input type="radio" name="pay" id="cashRadio">
										</div>


									</form>
								</div>

							</div>
							<hr />
							<div class="row form-inline">
								<div class="form-group c12">
									<label><i class="icon-handshake-o" aria-hidden="true"></i> Paiement au
										chauffeur</label>
									<p><i class="icon-check-square-o" aria-hidden="true" style="color:green"></i> Vous
										pouvez effectuer un paiement à bord si vous souhaitez payer directement le
										chauffeur le jour de la prestation.</p>
									<p><i class="icon-check-square-o" aria-hidden="true" style="color:green"></i> Un
										contact téléphone sera fait pour confirmer la réservation et le point de
										rencontre.</p>
									<br />
									<a href="https://airportaxiofficial.be/" target="_blank" class="btn btn-previous"
										style="float:right;"><i class="icon-home" aria-hidden="true"></i> Retour à
										l'accueil</a>
								</div>
							</div>
						</div>
					</fieldset>
					<!-- Form Step 5 -->

				</div>
				<div class="c3 simplebox">
					<h2 class="title stresstitle">Transport</h2>
					<hr class="hrtitle" />
					<h6>Véhicule :
						<span style="float:right;font-size:13px;color:grey;">
							<a href="vehicules.html">Voir nos modèles</a>
						</span>
					</h6>
					<h6>Capacité :
						<span style="float:right;font-size:13px;color:grey;">1 - 6 personne(s)</span>
					</h6>
					<h6>Extras :
						<span style="float:right;font-size:13px;color:grey;">Climatisation</span>
					</h6>
					<h6>
						<span style="color:transparent">Extras : </span>
						<span style="float:right;font-size:13px;color:grey;">Bouteilles d'eau</span>
					</h6>
					<h6>
						<span style="color:transparent">Extras : </span>
						<span style="float:right;font-size:13px;color:grey;">Wifi - Presse</span>
					</h6>
					<br />
					<h2 class="title stresstitle">Prestations</h2>
					<hr class="hrtitle" />
					<ul class="space-bot">
						<li><a href="navette-aeroport.html"><b> &mdash; Navette aéorport</b></a></li>
						<li><a href="chauffeur-prive.html"><b> &mdash; Chauffeur privé</b></a></li>
						<li><a href="reservation.php" style="color:#cb1010;"><b> &mdash; Réservation en ligne</b></a>
						</li>
					</ul>
					<br />
					<h2 class="title stresstitle">Nos atouts</h2>
					<hr class="hrtitle" />
					<ul class="icons space-bot">
						<li><i class="icon-check"></i> Chauffeur professionnel</li>
						<li><i class="icon-check"></i> Ponctualité et discretion</li>
						<li><i class="icon-check"></i> Service disponible 24/7</li>
						<li><i class="icon-check"></i> <a href="reservation.php" style="color:#cb1010;"><b>Réservation
									en ligne</b></a></li>
					</ul>
					<br />
					<h2 class="title stresstitle">Paiement</h2>
					<hr class="hrtitle" />
					<center>
						<img src="images/paiement.png" title="Paiement" alt="Paiement" />
					</center>
					<p style="font-size:13px;">
						<i class="icon-info-sign"></i> Paiement à bord accepté
					</p>
				</div>
			</section>



			<section id="phase_3" class="form-box" style="display:none">
				<div class="c12 form-wizard_3">
					<span>Remplissez tous les champs de formulaire pour aller à l'étape suivante</span>
					<!-- Form progress -->
					<div class="form-wizard-steps form-wizard-tolal-steps-5">
						<div class="form-wizard-progress">
							<div class="form-wizard-progress-line" data-now-value="100" data-number-of-steps="5"
								style="width: 100%;"></div>
						</div>

						<!-- Step 1 -->
						<div class="form-wizard-step activated">
							<div class="form-wizard-step-icon"><i class="icon-road" aria-hidden="true"></i></div>
							<p>Trajet</p>
						</div>
						<!-- Step 1 -->

						<!-- Step 2 -->
						<div class="form-wizard-step activated">
							<div class="form-wizard-step-icon"><i class="icon-car" aria-hidden="true"></i></div>
							<p>Prestation</p>
						</div>
						<!-- Step 2 -->

						<!-- Step 3 -->
						<div class="form-wizard-step activated">
							<div class="form-wizard-step-icon"><i class="icon-user" aria-hidden="true"></i></div>
							<p>Coordonnées</p>
						</div>
						<!-- Step 3 -->

						<!-- Step 4 -->
						<div class="form-wizard-step activated">
							<div class="form-wizard-step-icon"><i class="icon-credit-card" aria-hidden="true"></i></div>
							<p>Paiement</p>
						</div>
						<!-- Step 4 -->

						<!-- Step 5 -->
						<div class="form-wizard-step active">
							<div class="form-wizard-step-icon"><i class="icon-check" aria-hidden="true"></i></div>
							<p>Confirmation</p>
						</div>
						<!-- Step 5 -->
					</div>
					<!-- Form progress -->

					<!-- Form Step 5 -->
					<fieldset style="display:block">
						<!-- Progress Bar -->
						<div class="progress">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
								aria-valuemin="0" aria-valuemax="100" style="width:100%"></div>
						</div>
						<!-- Progress Bar -->
						<h4 class="maintitle"><span class="stepTitle">Confirmation</span><span class="step"><em>Étape 5
									- 5</em></span></h4>
						<div style="clear:both;"></div>
						<hr />

						<div>
							<?= $success ?>
						</div>

					</fieldset>
					<!-- Form Step 4 -->
				</div>
			</section>

		</div><!-- end row -->
	</div><!-- end page -->

	<!-- FOOTER -->
	<div id="wrapfooter">
		<div class="grid">
			<div class="row" id="footer">
				<!-- to top button  -->
				<p class="back-top floatright">
					<a href="#top"><span></span></a>
				</p>
				<!-- 1st column -->
				<div class="c3">
				<a href="https://www.trustpilot.com/review/airportaxiofficial.be">
					<img src="images/logo-footer.png" style="padding-top:10px;max-width:80%;" title="Airportaxiofficial"
						alt="Airportaxiofficial" />
						<img src="reservation/images/trust5.png" style="padding-top:10px;max-width:80%;"
							title="Airportaxiofficial" alt="Airportaxiofficial_trustpilot" /></a>
							
				<img src="reservation/images/googlereviews.png" style="padding-top:10px;max-width:80%;"
						title="Airportaxiofficial" alt="Airportaxiofficial_google" />
				
				</div>
				<!-- 2nd column -->
				<div class="c3">
					<h2 class="title"><i class="icon-info-sign"></i> À propos</h2>
					<hr class="footerstress" />
					<span>
						Service de transport privé de qualité.<br>
						Disponibilité 24h/24 - 7j/7.
					</span>
				</div>
				<!-- 3rd column -->
				<div class="c3">
					<h2 class="title"><i class="icon-envelope-alt"></i> Contact</h2>
					<hr class="footerstress" />
					<dl>
						<dd><span>Téléphone:</span> (+32) 4 92 06 18 96</dd>
						<dd>E-mail: <a href="mailto:info@airportaxiofficial.be">info@airportaxiofficial.be</a></dd>
					</dl>					
				</div>
				<!-- 4th column -->
				<div class="c3">
					<h2 class="title"><i class="icon-link"></i> Pages</h2>
					<hr class="footerstress" />
					<ul>
						<li><a href="chauffeur-taxi.html">À propos</a></li>
						<li><a href="navette-aeroport.html">Navette aéorport</a></li>
						<li><a href="chauffeur-prive.html">Chauffeur privé</a></li>
						<li><a href="vehicules.html">Véhicules</a></li>
						<li><a href="tarifs.html">Tarifs</a></li>
						<li><a href="reservation.php">Réservation</a></li>
						<li><a href="conditions.html">Conditions Générales</a></li>
						<li class="last"><a href="contact.html">Contact</a></li>
					</ul>
					<a href="https://techway.be">
					<img src="reservation/images/developpedbytechway.png" style="padding-top:10px;max-width:80%;"
						title="Techway.be" alt="techway.be" />
					</a>
				</div>
				<!-- end 4th column -->
			</div>
		</div>
	</div>

	<!-- copyright area -->
	<div class="copyright">
		<div class="grid">
			<div class="row">
				<div class="c12">
					<span class="left">Airportaxiofficial &copy; 2025 &ndash; Tous droits réservés.</span>
					<a class="right" href="mentions.html">Mentions légales</a>
				</div>
			</div>
		</div>
	</div>
	<!-- END CONTENT AREA -->

	<!-- JAVASCRIPTS -->
	<!-- all -->
	<script src="js/modernizr-latest.js"></script>

	<!-- menu & scroll to top -->
	<script src="js/common.js"></script>

	<!-- rd navbar -->
	<script src="js/jquery.rd-navbar.js"></script>

	<!-- google translate -->
	<script type="text/javascript"
		src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

	<script type="text/javascript">
		// function initialisation(){
			// var centreCarte = new google.maps.LatLng(50.854087, 4.360282);
			// var optionsCarte = {
				// zoom: 5,
				// center: centreCarte,
				// mapTypeId: google.maps.MapTypeId.ROADMAP
			// }
			// var maCarte = new google.maps.Map(document.getElementById("map_footer"), optionsCarte);
			// var optionsCercle = {
				// map: maCarte,
				// center: maCarte.getCenter(),
				// radius: 330000,
				// strokeColor: '#c39403',
				// strokeOpacity: 1,
				// strokeWeight: 1,
				// fillColor: '#fbc318',
				// fillOpacity: 0.35
			// }
			// var monCercle = new google.maps.Circle(optionsCercle);
		// }
		// google.maps.event.addDomListener(window, 'load', initialisation);
	</script>

	<!-- JS -->
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		$(function () {
			$("#date_aller").datepicker();
			$("#date_retour").datepicker();
			$('.ui-datepicker').addClass('notranslate');
		});
		jQuery(function ($) {
			$.datepicker.regional['fr'] = {
				closeText: 'Fermer',
				prevText: '&#x3c;Préc',
				nextText: 'Suiv&#x3e;',
				currentText: 'Aujourd\'hui',
				monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
				monthNamesShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
				dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
				dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
				dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
				weekHeader: 'Sm',
				dateFormat: 'dd/mm/yy',
				firstDay: 1,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: '',
				minDate: 0,
				maxDate: '+12M +0D',
				numberOfMonths: 1,
				showButtonPanel: true
			};
			$.datepicker.setDefaults($.datepicker.regional['fr']);
		});
	</script>

	<!-- Plugin Custom JS -->
	<script src="reservation/js/form-wizard.js"></script>
	<script src="reservation/js/form-wizard_2.js"></script>

	<!-- Custom JS code to bind to Autocomplete API -->
	<script type="text/javascript">
		function initializeAutocompleteEstablishment(id) {
			var element = document.getElementById(id);
			if (element) {
				var options = {
					types: ['establishment'],
					componentRestrictions: { country: ["be", "fr", "nl", "al", "lux"] }
				};
				var autocomplete = new google.maps.places.Autocomplete(element, options);
				google.maps.event.addListener(autocomplete, 'place_changed', onPlaceChanged);
			}
		}
		function initializeAutocomplete(id) {
			var element = document.getElementById(id);
			if (element) {
				var options = {
					types: ["address"],
					componentRestrictions: { country: ["be", "fr", "nl", "al", "lux"] }
				};
				var autocomplete = new google.maps.places.Autocomplete(element, options);
				google.maps.event.addListener(autocomplete, 'place_changed', onPlaceChanged);
			}
		}
		function onPlaceChanged() {
			var place = this.getPlace();
			// console.log(getPlace);

			//console.log(place);  // Uncomment this line to view the full object returned by Google API.
			for (var i in place.address_components) {
				var component = place.address_components[i];
				for (var j in component.types) { // Some types are ["country", "political"]
					var type_element = document.getElementById(component.types[j]);
					if (type_element) {
						type_element.value = component.long_name;
					}
				}
			}
		}
		google.maps.event.addDomListener(window, 'load', function () {
			initializeAutocomplete('autocomplete_address_departure');
			initializeAutocomplete('autocomplete_address_arrival');
		});
	</script>
	<!-- STRIPE  CHECKOUT FORM-->
	<script>
    var handler = StripeCheckout.configure({
		key: '<?php echo $global_config['stripe_pk']; ?>',
        locale: 'auto',
        image: 'https://airportaxiofficial.be/favicon.ico',
        token: function(token) {
            var form = document.createElement("form");
            var tokenID = document.createElement("input");
            var email = document.createElement("input");
            var ip_address = document.createElement("input");
            var submitButton = document.createElement("button");

            submitButton.type = "submit";
            submitButton.value = "Submit";
            submitButton.name = "stripe_but";

            form.method = "POST";
            
            tokenID.type = "hidden";
            tokenID.name = "stripe_token";
            tokenID.value = token.id;

            email.type = "hidden";
            email.name = "stripe_email";
            email.value = token.email;

            ip_address.type = "hidden";
            ip_address.name = "stripe_client_ip";
            ip_address.value = token.client_ip;

            form.appendChild(tokenID);
            form.appendChild(email);
            form.appendChild(ip_address);
            form.appendChild(submitButton);
            document.body.appendChild(form);

            form.submit(); // Submit the form
        }
    });

		function creditcard() {
			var stripe = Stripe('<?php  echo $global_config['stripe_pk'] ?>');
			var session_id = "<?php echo ($_SESSION["stripe_session"]); ?>";
			stripe.redirectToCheckout({
				sessionId: session_id
			}).then(function (result) {
				// If `redirectToCheckout` fails due to a browser or network
				// error, display the localized error message to your customer
				// using `result.error.message`.
			});
		}

		//Stripe Bancontact
		function bancontact() {
			var form = document.createElement("form");
			var but = document.createElement("button");
			but.name = "bancontact_but";
			but.submit = "post";
			but.type = "submit";
			but.value = "bancontact_but_value";
			form.name = "bancontact_form_button";
			form.method = "POST";
			form.action = "/reservation.php";
			form.appendChild(but);
			document.body.appendChild(form);
			but.click();
		}

		//Paypal
		function paypal() {
			var form = document.createElement("form");
			var but = document.createElement("button");
			but.name = "paypal_but";
			but.submit = "post";
			but.type = "submit";
			but.value = "paypal_but_value";
			form.name = "paypal_form_button";
			form.method = "POST";
			form.action = "/success.php?method=paypal";
			form.appendChild(but);
			document.body.appendChild(form);
			but.click();
		}

		function cash() {
			//Cash
			var form = document.createElement("form");
			var but = document.createElement("button");
			but.name = "cash_but";
			but.submit = "post";
			but.type = "submit";
			but.value = "cash_but_value";
			form.name = "cash_form_button";
			form.method = "POST";
			form.action = "/success.php?method=cash";
			form.appendChild(but);
			document.body.appendChild(form);
			but.click();
		}
	</script>

</body>

</html>