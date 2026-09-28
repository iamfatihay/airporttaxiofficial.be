<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xml:lang="fr-FR" lang="fr-FR" xmlns="http://www.w3.org/1999/xhtml">

    <head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Content-Language" content="fr-FR">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Driven Limousine Service - Réservation</title>

        <!-- Google Font -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
		<!-- BootStrap Stylesheet -->
        <link rel="stylesheet" href="css/bootstrap.css">
		<!-- Font-Awesome Stylesheet -->
        <link rel="stylesheet" href="css/font-awesome.min.css">
		
		<!-- Plugin Custom Stylesheet -->
		<link rel="stylesheet" href="css/form-wizard.css">
		<link rel="stylesheet" href="css/form-wizard_2.css">
		<!-- Plugin Custom Stylesheet -->
		
		<!-- Calendar CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<style>
			#ui-datepicker-div {font-size:90%;}
		</style>
		<!-- ENDS Calendar CSS -->
		
    </head>

    <body>

        <!-- main content -->
        <section id="phase_1" class="form-box">
            <div class="container">
                
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 form-wizard">
					
						<h3>Votre réservation en ligne</h3>
                    	<p>Remplissez tous les champs de formulaire pour aller à l'étape suivante</p>
							
						<!-- Form progress -->
                    	<div class="form-wizard-steps form-wizard-tolal-steps-5">
                    		<div class="form-wizard-progress">
                    		    <div class="form-wizard-progress-line" data-now-value="12.25" data-number-of-steps="5" style="width: 12.25%;"></div>
                    		</div>
							<!-- Step 1 -->
                    		<div class="form-wizard-step active">
                    			<div class="form-wizard-step-icon"><i class="fa fa-road" aria-hidden="true"></i></div>
                    			<p>Trajet</p>
                    		</div>
							<!-- Step 1 -->
							
							<!-- Step 2 -->
                    		<div class="form-wizard-step">
                    			<div class="form-wizard-step-icon"><i class="fa fa-car" aria-hidden="true"></i></div>
                    			<p>Prestation</p>
                    		</div>
							<!-- Step 2 -->
							
							<!-- Step 3 -->
							<div class="form-wizard-step">
                    			<div class="form-wizard-step-icon"><i class="fa fa-user" aria-hidden="true"></i></div>
                    			<p>Coordonnées</p>
                    		</div>
							<!-- Step 3 -->
							
							<!-- Step 4 -->
							<div class="form-wizard-step">
                    			<div class="form-wizard-step-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
                    			<p>Confirmation</p>
                    		</div>
							<!-- Step 4 -->
							
							<!-- Step 5 -->
							<div class="form-wizard-step">
                    			<div class="form-wizard-step-icon"><i class="fa fa-credit-card" aria-hidden="true"></i></div>
                    			<p>Paiement</p>
                    		</div>
							<!-- Step 5 -->
                    	</div>
						<!-- Form progress -->
                    	
						<!-- Form Step 1 -->
                    	<fieldset>
							<!-- Progress Bar -->
							<div class="progress">
								<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width:20%"></div>
							</div>
							<!-- Progress Bar -->
							
                    	    <h4>Votre trajet<span><em><small>Étape 1 - 5</small></em></span></h4>
							
							<form id="journey" role="form" name="form" method="post" action="">
								<div class="form-group">
									<label><i class="fa fa-map-marker" aria-hidden="true"></i> Adresse de départ</label>
									<input type="text" name="depart" id="autocomplete_address_departure" placeholder="Indiquer ici l'adresse de départ" class="form-control required">
								</div>
								<div class="form-group">
									<label><i class="fa fa-map-marker" aria-hidden="true"></i> Adresse d'arrivée</label>
									<input type="text" name="arrivee" id="autocomplete_address_arrival" placeholder="Indiquer ici l'adresse d'arrivée" class="form-control required">
								</div>
								<div class="container-fluid form-group">
									<div class="row form-inline">
										<div class="form-group col-md-4 col-xs-12">
											<label><i class="fa fa-calendar" aria-hidden="true"></i> Date aller</label>
											<input class="form-control required" name="date_aller" id="date_aller" type="text" value="<?=stripslashes($_SESSION['date_aller']);?>" placeholder="ex. 01/01/2017" style="width:100%">
										</div>
										<div class="form-group col-md-4 col-xs-12">
											<label><i class="fa fa-clock-o" aria-hidden="true"></i> Heure</label>
											<select name="heure" class="form-control required" style="width:100%">
												<option value="">Sélectionner...</option>
												<option value="00h"<?php if ($_SESSION['heure'] == "00h") { echo("selected"); } ?>>00h</option>
												<option value="01h"<?php if ($_SESSION['heure'] == "01h") { echo("selected"); } ?>>01h</option>
												<option value="02h"<?php if ($_SESSION['heure'] == "02h") { echo("selected"); } ?>>02h</option>
												<option value="03h"<?php if ($_SESSION['heure'] == "03h") { echo("selected"); } ?>>03h</option>
												<option value="04h"<?php if ($_SESSION['heure'] == "04h") { echo("selected"); } ?>>04h</option>
												<option value="05h"<?php if ($_SESSION['heure'] == "05h") { echo("selected"); } ?>>05h</option>
												<option value="06h"<?php if ($_SESSION['heure'] == "06h") { echo("selected"); } ?>>06h</option>
												<option value="07h"<?php if ($_SESSION['heure'] == "07h") { echo("selected"); } ?>>07h</option>
												<option value="08h"<?php if ($_SESSION['heure'] == "08h") { echo("selected"); } ?>>08h</option>
												<option value="09h"<?php if ($_SESSION['heure'] == "09h") { echo("selected"); } ?>>09h</option>
												<option value="10h"<?php if ($_SESSION['heure'] == "10h") { echo("selected"); } ?>>10h</option>
												<option value="11h"<?php if ($_SESSION['heure'] == "11h") { echo("selected"); } ?>>11h</option>
												<option value="12h"<?php if ($_SESSION['heure'] == "12h") { echo("selected"); } ?>>12h</option>
												<option value="13h"<?php if ($_SESSION['heure'] == "13h") { echo("selected"); } ?>>13h</option>
												<option value="14h"<?php if ($_SESSION['heure'] == "14h") { echo("selected"); } ?>>14h</option>
												<option value="15h"<?php if ($_SESSION['heure'] == "15h") { echo("selected"); } ?>>15h</option>
												<option value="16h"<?php if ($_SESSION['heure'] == "16h") { echo("selected"); } ?>>16h</option>
												<option value="17h"<?php if ($_SESSION['heure'] == "17h") { echo("selected"); } ?>>17h</option>
												<option value="18h"<?php if ($_SESSION['heure'] == "18h") { echo("selected"); } ?>>18h</option>
												<option value="19h"<?php if ($_SESSION['heure'] == "19h") { echo("selected"); } ?>>19h</option>
												<option value="20h"<?php if ($_SESSION['heure'] == "20h") { echo("selected"); } ?>>20h</option>
												<option value="21h"<?php if ($_SESSION['heure'] == "21h") { echo("selected"); } ?>>21h</option>
												<option value="22h"<?php if ($_SESSION['heure'] == "22h") { echo("selected"); } ?>>22h</option>
												<option value="23h"<?php if ($_SESSION['heure'] == "23h") { echo("selected"); } ?>>23h</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-xs-12">
											<label><i class="fa fa-clock-o" aria-hidden="true"></i> Minute</label>
											<select name="minute" class="form-control required" style="width:100%">
												<option value="">Sélectionner...</option>
												<option value="00min"<?php if ($_SESSION['minute'] == "00min") { echo("selected"); } ?>>00min</option>
												<option value="05min"<?php if ($_SESSION['minute'] == "05min") { echo("selected"); } ?>>05min</option>
												<option value="10min"<?php if ($_SESSION['minute'] == "10min") { echo("selected"); } ?>>10min</option>
												<option value="15min"<?php if ($_SESSION['minute'] == "15min") { echo("selected"); } ?>>15min</option>
												<option value="20min"<?php if ($_SESSION['minute'] == "20min") { echo("selected"); } ?>>20min</option>
												<option value="25min"<?php if ($_SESSION['minute'] == "25min") { echo("selected"); } ?>>25min</option>
												<option value="30min"<?php if ($_SESSION['minute'] == "30min") { echo("selected"); } ?>>30min</option>
												<option value="35min"<?php if ($_SESSION['minute'] == "35min") { echo("selected"); } ?>>35min</option>
												<option value="40min"<?php if ($_SESSION['minute'] == "40min") { echo("selected"); } ?>>40min</option>
												<option value="45min"<?php if ($_SESSION['minute'] == "45min") { echo("selected"); } ?>>45min</option>
												<option value="50min"<?php if ($_SESSION['minute'] == "50min") { echo("selected"); } ?>>50min</option>
												<option value="55min"<?php if ($_SESSION['minute'] == "55min") { echo("selected"); } ?>>55min</option>
											</select>
										</div>
									</div>
								</div>
								<div style="clear:both;"></div>
								<div class="container-fluid form-group">
									<div class="row form-inline">
										<div class="form-group col-md-4 col-xs-12">
											<label><i class="fa fa-user" aria-hidden="true"></i> Passager(s) à bord</label>
											<select name="passager" id="passager" class="form-control" style="width:100%">
												<option value="1 personne">1 personne</option>
												<option value="2 personnes">2 personnes</option>
												<option value="3 personnes">3 personnes</option>
												<option value="4 personnes">4 personnes</option>
											</select>
										</div>
										<script type="text/javascript">
										function yesnoCheck() {
											if (document.getElementById('retour').checked) {
												document.getElementById('date-retour').style.display = 'block';
											}
											else document.getElementById('date-retour').style.display = 'none';
										}
										</script>
										<div class="form-group col-md-4 col-xs-12">
											<label>Je souhaite un</label>
											<div style="clear:both;"></div>
											<label class="radio-inline">
												<input type="radio" onclick="javascript:yesnoCheck();" name="trajet" id="aller" value="Aller simple" checked="checked" />&nbsp;&nbsp;&nbsp;Aller simple
											</label>
											<div style="clear:both;"></div>
											<label class="radio-inline">
												<input type="radio" onclick="javascript:yesnoCheck();" name="trajet" id="retour" value="Aller retour" />&nbsp;&nbsp;&nbsp;Aller retour 
											</label>
										</div>
										<div class="form-group col-md-4 col-xs-12" id="date-retour" style="display:none">
											<label><i class="fa fa-calendar" aria-hidden="true"></i> Date retour</label>
											<input class="form-control" name="date_retour" id="date_retour" type="text" value="<?=stripslashes($_SESSION['date_retour']);?>" placeholder="ex. 01/01/2017" style="width:100%">
										</div>
									</div>
								</div>
								<div style="clear:both;"></div>
								<div class="form-wizard-buttons">
									<a href="http://www.drivenlimousineservice.be/" class="btn btn-previous" style="margin-right:10px;"><i class="fa fa-home" aria-hidden="true"></i> Retour à l'accueil</a>
									<button type="submit" name="calculer" id="calculer" class="btn btn-submit"><i class="fa fa-road" aria-hidden="true"></i> Calculer le trajet et le prix</button>
								</div>
							</form>

							<?php
							
							// on crée la fonction de calcul de distance et de prix


							// si le bouton calculer est lancé, on récupère les informations du formulaire et on lance la fonction

							if (isset($_POST['calculer']))
								{
								$depart = $_POST['depart'];
								$arrivee = $_POST['arrivee'];
								$passager = $_POST['passager'];
								$distance = $_SESSION["distance"];
								$prix = $_POST['prix'];
								
								if ($distance > 0 && $distance < 11)
									{
									$prix = 20;
									}
								elseif ($distance > 10 && $distance < 31)
									{
									$prix = 35;
									}
								elseif ($distance > 30)
									{
									$prix_km = 1.16;
									$prix = $prix_km * $_SESSION["distance"];
									}
								$trajet = $_POST['trajet'];
								if ($trajet == 'Aller simple')
									{
									$prix = $prix * 1;
									}
								elseif ($trajet == 'Aller retour')
									{
									$prix = $prix * 1.8;
									}
								
								echo '<p><i class="fa fa-clock-o" aria-hidden="true" style="font-size:21px;color:#478fca"></i> <b>' . $_POST['date_aller'] . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' . $_POST['heure'] . '' . $_POST['minute'] . '</b></p>';
								echo '<p><i class="fa fa-map-marker" aria-hidden="true" style="font-size:23px;margin-right:5px;color:green"></i> <b>' . $depart . '</b></p>';
								echo '<p><i class="fa fa-map-marker" aria-hidden="true" style="font-size:23px;margin-right:5px;color:red"></i> <b>' . $arrivee . '</b></p>';
								echo '<hr />';
								echo '<h4>Informations</h4>';
								echo '<div class="row">';
								echo '<p class="col-xs-12 col-md-6">Distance : <b>' . $_SESSION["distance"] . ' KM</b></p>';
								echo '<p class="col-xs-12 col-md-6">Durée probable : <b>-- min</b></p>';
								echo '</div>';
								echo '<div class="row">';
								echo '<p class="col-xs-12 col-md-6">Passager : <b>' . $passager . '</b></p>';
								echo '<p class="col-xs-12 col-md-6">Trajet : <b>' .$_POST['trajet'] . ' &mdash; ' . $_POST['date_retour'] . '</b></p>';
								echo '</div>';
								echo '<hr />';
								echo '<h4>Prix</h4>';
								echo '<p><small>(&Agrave; partir de)</small> : <b><span style="color:#de1f25;">' . $prix . ' EUROS</span></b></p>';
								echo '<hr />';
								echo '<div class="form-wizard-buttons">';
								echo '<a href="reservation.php" class="btn btn-previous" style="margin-right:10px;"><i class="fa fa-refresh" aria-hidden="true"></i>  Nouvelle recherche</a>';
								echo '<button type="button" class="btn btn-next"><i class="fa fa-car" aria-hidden="true"></i> Choix du véhicule</button>';
								echo '</div>';
								echo '<style>form#journey{display:none}</style>';
								}
								
							// Formulaire de contact	
							// Vérifier que le formulaire a été envoyé...

							if (isset($_POST['envoi']))
								{

								// On commence une session pour enregistrer les variables du formulaire...

								session_start();

								// Enregistrement des zones de champ...
								
								$_SESSION['nom'] = $_POST['nom'];
								$_SESSION['prenom'] = $_POST['prenom'];
								$_SESSION['email'] = $_POST['email'];
								$_SESSION['telephone'] = $_POST['telephone'];
								$_SESSION['depart'] = $_POST['depart'];
								$_SESSION['arrivee'] = $_POST['arrivee'];
								$_SESSION['distance'] =  $_POST['distance'];
								$_SESSION['date_aller'] = $_POST['date_aller'];
								$_SESSION['date_retour'] = $_POST['date_retour'];
								$_SESSION['horaire'] = $_POST['horaire'];
								$_SESSION['passager'] = $_POST['passager'];
								$_SESSION['trajet'] = $_POST['trajet'];
								$_SESSION['prix'] = $_POST['prix'];
								
								// Enregistrement des boutons...
								$percent = 1;
								switch($_POST['vehicule']) {
									case "Citroën C4" : $_SESSION['vehicule'] = "Citroën C4"; $percent = 1;
								break;
									case "Mercedes Class E" : $_SESSION['vehicule'] = "Mercedes Class E"; $percent = 1.2;
								break;
									case "Mercedes Vito" : $_SESSION['vehicule'] = "Mercedes Vito"; $percent = 1.25;
								break;
								default : $_SESSION['vehicule'] = "";
								}

								// Enregistrement des zones de texte...

								$_SESSION['message'] = $_POST['message'];

								// Enregistrement des cases...

								$_SESSION['cgv'][0] = "";
								if (isset($_POST['cgv'][0]))
									{
									$_SESSION['cgv'][0] = $_POST['cgv'][0];
									}

								// Définir l'indicateur d'erreur sur zéro...

								$flag_erreur = 0;

								// N'envoyer le formulaire que s'il n'y a pas d'erreurs...

								if ($flag_erreur == 0)
									{

									// Addresse de réception du formulaire

									$email_dest = "info@airportaxiofficial.be";
									$sujet = "Réservation - Driven Limousine Service";
									$entetes = "MIME-Version: 1.0 \n";
									$entetes.= "From: Driven Limousine Service<info@airportaxiofficial.be>\n";
									$entetes.= "Return-Path: Driven Limousine Service<info@airportaxiofficial.be>\n";
									$entetes.= "Reply-To: Driven Limousine Service<info@airportaxiofficial.be>\n";
									$entetes.= "Content-Type: text/html; charset=iso-8859-1 \n";
									$partie_entete = "<html>\n<head>\n<title>Formulaire de réservation - Driven Limousine Service</title>\n<meta http-equiv=Content-Type content=text/html; charset=iso-8859-1>\n<style>body{font:400 15px Verdana,sans-serif;background:#f4f4f4}table{border:1px solid #777;width:80%;margin:15px auto 30px}table td{border-bottom:1px dotted #333;color:#036;padding:10px}h3{color:#de1f25}</style>\n</head>\n<body>\n";

									// Partie HTML de l'e-mail...
									
									$partie_html.= "<h3><i class='fa fa-info-circle'></i> Informations sur le trajet</h3>";
									$partie_html.= "<table>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Adresse de départ</td><td><b>" . $_SESSION['depart'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Adresse de destination</td><td><b>" . $_SESSION['arrivee'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Distance</td><td><b>" . $_SESSION['distance'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Date aller</td><td><b>" . $_SESSION['date_aller'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;border-bottom:none;'>Horaire</td><td style='border-bottom:none;'><b>" . $_SESSION['horaire'] . "</b></td></tr>";
									$partie_html.= "</table>";
									
									$partie_html.= "<hr />";
									
									$partie_html.= "<h3><i class='fa fa-user'></i> Identité du client</h3>";
									$partie_html.= "<table>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Nom</td><td><b>" . $_SESSION['nom'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Prénom</td><td><b>" . $_SESSION['prenom'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Email</td><td><b>" . $_SESSION['email'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;border-bottom:none;'>Téléphone</td><td style='border-bottom:none;'><b>" . $_SESSION['telephone'] . "</b></td></tr>";
									$partie_html.= "</table>";
									
									$partie_html.= "<hr />";
									
									$partie_html.= "<h3><i class='fa fa-car'></i> Prestation demandée</h3>";
									$partie_html.= "<table>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Choix du véhicule</td><td><b>" . $_SESSION['vehicule'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Nbre de passagers</td><td><b>" . $_SESSION['passager'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Type de trajet</td><td><b>" . $_SESSION['trajet'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Date retour</td><td><b>" . $_SESSION['date_retour'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;border-bottom:none;'>Prix</td><td style='border-bottom:none;'><b>" . $_SESSION['prix'] * $percent . " EUROS</b></td></tr>";
									$partie_html.= "</table>";
									
									$partie_html.= "<hr />";
									
									$partie_html.= "<h3><i class='fa fa-question-circle'></i> Informations complémentaires</h3>";
									$partie_html.= "<table>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;'>Message</td><td><b>" . $_SESSION['message'] . "</b></td></tr>";
									$partie_html.= "<tr><td style='width:35%;background:#cce7e7;border-bottom:none;'>Conditions générales</td><td style='border-bottom:none;'>CGV acceptées (case cochée)</td></tr>";
									$partie_html.= "</table>";

									// Fin du message HTML

									$fin = "</body></html>\n\n";
									$sortie = $partie_entete . $partie_html . $fin;
									
									// Send the e-mail

									if (@!mail($email_dest, $sujet, $sortie, $entetes)) {
										echo ("Envoi du formulaire impossible");
										exit();
										}
										else
										{

										// Basculement sur la phase 2
										
										echo '<style>#phase_1 {display:none !important} #phase_2 {display:block !important}</style>';
										} // Fin else
									} // Fin du if ($flag_erreur == 0) {
								} // Fin de if POST
								
							?>
						</fieldset>
						<!-- Form Step 1 -->

						<!-- Form Step 2 -->
						<fieldset>
							<!-- Progress Bar -->
							<div class="progress">
								<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%"></div>
							</div>
							<!-- Progress Bar -->
							
							<h4>Résumé</h4>
							
							<?php
								echo '<div class="row">';
								echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-clock-o" aria-hidden="true" style="color:#478fca"></i> ' . $_POST['date_aller'] . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' . $_POST['heure'] . $_POST['minute'] . '</small></p>';
								echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-user" aria-hidden="true" style="color:gray"></i> ' . $passager . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' .$_POST['trajet'] . ' (<i class="fa fa-location-arrow" aria-hidden="true" style="color:gray"></i> ' . $_SESSION["distance"] . ' KM)</small></p>';
								echo '</div>';
								echo '<div class="row">';
								echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-map-marker" aria-hidden="true" style="color:green"></i> ' . $depart . '</small></p>';
								echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-map-marker" aria-hidden="true" style="color:red"></i> ' . $arrivee . '</small></p>';
								echo '</div>';
							?>
							
							<hr />
							
							<!-- form checking -->
							<script language="JavaScript">
							
								function verifSelection() {

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

									atPos = document.mail_form.email.value.indexOf("@",1)			// there must be one "@" symbol
									if (atPos == -1) {
										alert('Votre adresse e-mail ne contient pas le signe "@". Veuillez vérifier.')
										document.mail_form.email.focus()
										return false
									}

									if (document.mail_form.email.value.indexOf("@",atPos+1) != -1) {	// and only one "@" symbol
										alert('Il ne doit y avoir qu\'un signe "@". Veuillez vérifier.')
										document.mail_form.email.focus()
										return false
									}

									periodPos = document.mail_form.email.value.indexOf(".",atPos)

									if (periodPos == -1) {					// and at least one "." after the "@"
										alert('Vous avez oublié le point "." après le signe "@". Veuillez vérifier.')
										document.mail_form.email.focus()
										return false
									}

									if (periodPos+3 > document.mail_form.email.value.length)	{		// must be at least 2 characters after the 
										alert('Il doit y avoir au moins deux caractères après le signe ".". Veuillez vérifier.')
										document.mail_form.email.focus()
										return false
									}
									
									if (document.mail_form.telephone.value == "") {
										alert("Veuillez préciser votre numéro de téléphone SVP")
										return false
									}
									
									if (document.mail_form.cgv.checked == false ) {
										alert("Vous devez accepter nos conditions générales de service.")
										return false
									}

								} // Fin de la fonction

							</script>
							<!-- ENDS form checking -->
							
							<form name="mail_form" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return verifSelection()">
								<h4>Choix du véhicule<span><em><small>Étape 2 - 5</small></em></span></h4>
								<div class="form-group">
									<label style="margin-bottom:-15px;"><h4 style="color:#de1f25;"><b>Primary Class</b> &ndash; <small><em>Citroën C4</em></small></h4></label>
									<p style="font-size:13px;margin-top:0;margin-bottom:0;">
										<i class="fa fa-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max. 4
										&nbsp;&nbsp;&nbsp;
										<i class="fa fa-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i> max. 2
									</p>
									<hr style="margin-top:5px;" />
									<div class="col-md-8 col-xs-12">
										<img class="img-responsive" src="http://www.drivenlimousineservice.be/img/vehicules/c4.png" title="Citroën C4" alt="Citroën C4" />
									</div>
									<div class="col-md-4 col-xs-12">
										<?php echo '<p style="color:#de1f25;font-size:15px;border:1px solid #de1f25;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . $prix . ' EUROS</b></p>'; ?>
										<p><i class="fa fa-credit-card" aria-hidden="true"></i> Paiement à bord</p>
										<p><i class="fa fa-cogs" aria-hidden="true"></i> Service d'acceil, Wifi</p>
										<p><i class="fa fa-clock-o" aria-hidden="true"></i> 30min d'attente gratuite</p>
										<label class="control control--radio">
											Sélectionner <i class="fa fa-chevron-right" aria-hidden="true"></i>
											<input type="radio" class="btn btn-next" name="vehicule" value="Citroën C4" <?php if ($_SESSION['vehicule'] == "Citroën C4") { echo("checked"); } ?>>
											<div class="control__indicator"></div>
										</label>
									</div>
								</div>
								<div style="clear:both;height:30px;"></div>
								<div class="form-group">
									<label style="margin-bottom:-15px;"><h4 style="color:#de1f25;"><b>Business Class</b> &ndash; <small><em>Mercedes Class E</em></small></h4></label>
									<p style="font-size:13px;margin-top:0;margin-bottom:0;">
										<i class="fa fa-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max. 4
										&nbsp;&nbsp;&nbsp;
										<i class="fa fa-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i> max. 2
									</p>
									<hr style="margin-top:5px;" />
									<div class="col-md-8 col-xs-12">
										<img class="img-responsive" src="http://www.drivenlimousineservice.be/img/vehicules/class_e.png" title="Mercedes Class E" alt="Mercedes Class E" />
									</div>
									<div class="col-md-4 col-xs-12">
										<?php echo '<p style="color:#de1f25;font-size:15px;border:1px solid #de1f25;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . ($prix * 1.2) . ' EUROS</b></p>'; ?>
										<p><i class="fa fa-credit-card" aria-hidden="true"></i> Paiement à bord</p>
										<p><i class="fa fa-wifi" aria-hidden="true"></i> Wifi <i class="fa fa-tint" aria-hidden="true"></i> Eau (petite bout.)</p>
										<p><i class="fa fa-clock-o" aria-hidden="true"></i> 1h d'attente gratuite</p>
										<label class="control control--radio">
											Sélectionner <i class="fa fa-chevron-right" aria-hidden="true"></i>
											<input type="radio" class="btn btn-next" name="vehicule" value="Mercedes Class E" <?php if ($_SESSION['vehicule'] == "Mercedes Class E") { echo("checked"); } ?>>
											<div class="control__indicator"></div>
										</label>
									</div>
								</div>
								<div style="clear:both;height:30px;"></div>
								<div class="form-group">
									<label style="margin-bottom:-15px;"><h4 style="color:#de1f25;"><b>Business Van</b> &ndash; <small><em>Mercedes Vito</em></small></h4></label>
									<p style="font-size:13px;margin-top:0;margin-bottom:0;">
										<i class="fa fa-user" aria-hidden="true" style="font-size:18px;color:gray"></i> max. 6
										&nbsp;&nbsp;&nbsp;
										<i class="fa fa-suitcase" aria-hidden="true" style="font-size:18px;color:gray"></i> max. 6
									</p>
									<hr style="margin-top:5px;" />
									<div class="col-md-8 col-xs-12">
										<img class="img-responsive" src="http://www.drivenlimousineservice.be/img/vehicules/class_v.png" title="Mercedes Vito" alt="Mercedes Vito" />
									</div>
									<div class="col-md-4 col-xs-12">
										<?php echo '<p style="color:#de1f25;font-size:15px;border:1px solid #de1f25;border-radius:4px;padding:3px;text-align:center;"><b>PRIX : ' . ($prix * 1.25) . ' EUROS</b></p>'; ?>
										<p><i class="fa fa-credit-card" aria-hidden="true"></i> Paiement à bord</p>
										<p><i class="fa fa-wifi" aria-hidden="true"></i> Wifi <i class="fa fa-tint" aria-hidden="true"></i> Eau (petite bout.)</p>
										<p><i class="fa fa-clock-o" aria-hidden="true"></i> 1h d'attente gratuite</p>
										<label class="control control--radio">
											Sélectionner <i class="fa fa-chevron-right" aria-hidden="true"></i>
											<input type="radio" class="btn btn-next" name="vehicule" value="Mercedes Vito" <?php if ($_SESSION['vehicule'] == "Mercedes Vito") { echo("checked"); } ?>>
											<div class="control__indicator"></div>
										</label>
									</div>
								</div>
								<div style="clear:both;height:15px;"></div>
								<div class="form-wizard-buttons">
									<small>Tous nos prix comprennent la TVA.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
									<button type="button" class="btn btn-previous">Précédent</button>
								</div>
						</fieldset>
						<!-- Form Step 2 -->

						<!-- Form Step 3 -->
						<fieldset>
								<!-- Progress Bar -->
								<div class="progress">
									<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:60%"></div>
								</div>
								<!-- Progress Bar -->
								
								<h4>Résumé</h4>
								
								<?php
									echo '<div class="row">';
									echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-clock-o" aria-hidden="true" style="color:#478fca"></i> ' . $_POST['date_aller'] . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' . $_POST['heure'] . $_POST['minute'] . '</small></p>';
									echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-user" aria-hidden="true" style="color:gray"></i> ' . $passager . '&nbsp;&nbsp;&ndash;&nbsp;&nbsp;' .$_POST['trajet'] . ' (<i class="fa fa-location-arrow" aria-hidden="true" style="color:gray"></i> ' . $_SESSION["distance"] . ' KM)</small></p>';
									echo '</div>';
									echo '<div class="row">';
									echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-map-marker" aria-hidden="true" style="color:green"></i> ' . $depart . '</small></p>';
									echo '<p class="col-xs-12 col-md-6" style="margin:0"><small><i class="fa fa-map-marker" aria-hidden="true" style="color:red"></i> ' . $arrivee . '</small></p>';
									echo '</div>';
								?>
								
								<hr />
								
								<h4>Informations<span><em><small>Étape 3 - 5</small></em></span></h4>
							
							
								<div class="container-fluid form-group">
									<div class="row form-inline">
										<div class="form-group col-md-6 col-xs-12">
											<label>Nom</label>
											<input type="text" class="form-control" style="width:100%" name="nom" value="<?=stripslashes($_SESSION['nom']);?>" placeholder="ex. Dupont" />
										</div>
										<div class="form-group col-md-6 col-xs-12">
											<label>Prénom</label>
											<input type="text" class="form-control" style="width:100%" name="prenom" value="<?=stripslashes($_SESSION['prenom']);?>" placeholder="ex. Jacques" />
										</div>
									</div>
								</div>
								<div style="clear:both;"></div>
								<div class="container-fluid form-group">
									<div class="row form-inline">
										<div class="form-group col-md-6 col-xs-12">
											<label>Email</label>
											<input type="text" class="form-control" style="width:100%" name="email" value="<?=stripslashes($_SESSION['email']);?>" placeholder="ex. email@exemple.com" />
										</div>
										<div class="form-group col-md-6 col-xs-12">
											<label>Téléphone</label>
											<input type="text" class="form-control" style="width:100%" name="telephone" value="<?=stripslashes($_SESSION['telephone']);?>" placeholder="ex. +32.123456789" />
										</div>
									</div>
								</div>
								<div style="clear:both;"></div>
								<div class="form-group">
									<label>Infos supplémentaires (N. vol, train...)</label>
									<textarea class="form-control" name="message" cols="45" rows="3" placeholder="Votre message ici..."><?=stripslashes($_SESSION['message']);?></textarea>
								</div>
								<div class="form-group">
									<label>Conditions générales</label>
									<div style="clear:both;"></div>
									<input type="checkbox" name="cgv" value="acceptées" <?php if ($_SESSION['cgv'][0] == "acceptées") { echo("checked"); } ?>>
									Je reconnais avoir pris connaissance des <a href="http://www.drivenlimousineservice.be/cgv.html" target="_blank"><strong>conditions générales de vente</strong></a> et je les accepte.
								</div>
								<div style="clear:both;"></div>
								
								<!-- Form details -->
								<input type="hidden" name="depart" id="depart" value="<?=stripslashes($_SESSION['depart']); echo $depart;?>" />
								<input type="hidden" name="arrivee" id="arrivee" value="<?=stripslashes($_SESSION['arrivee']); echo $arrivee;?>" />
								<input type="hidden" name="distance" id="distance" value="<?=stripslashes($_SESSION['distance']); echo $distance;?> KM" />
								<input type="hidden" name="date_aller" id="date_aller" value="<?=stripslashes($_SESSION['date_aller']); echo $_POST['date_aller'];?>" />
								<input type="hidden" name="horaire" id="horaire" value="<?=stripslashes($_SESSION['horaire']); echo $_POST['heure'] . $_POST['minute'];?>" />
								<input type="hidden" name="passager" id="passager" value="<?=stripslashes($_SESSION['passager']); echo $passager;?>" />
								<input type="hidden" name="trajet" id="trajet" value="<?=stripslashes($_SESSION['trajet']); echo $trajet;?>" />
								<input type="hidden" name="date_retour" id="date_retour" value="<?=stripslashes($_SESSION['date_retour']); echo $_POST['date_retour'];?>" />
								<input type="hidden" name="prix" id="prix" value="<?=stripslashes($_SESSION['prix']); echo $prix;?> EUROS" />
								<!-- Form details -->
								
								<div class="form-wizard-buttons">
									<button type="button" class="btn btn-previous">Précédent</button>
									<button type="submit" name="envoi" class="btn btn-submit"><i class="fa fa-check" aria-hidden="true"></i> Je valide</button>
								</div>
							</form>
						</fieldset>
						<!-- Form Step 3 -->
					</div>
				</div>
			</div>
		</section>
		
        <!-- main content -->
        <section id="phase_2" class="form-box" style="display:none">
            <div class="container">
                
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 form-wizard_2">

						<h3>Votre réservation en ligne</h3>
                    	<p>Remplissez tous les champs de formulaire pour aller à l'étape suivante</p>
							
						<!-- Form progress -->
                    	<div class="form-wizard-steps form-wizard-tolal-steps-5">
                    		<div class="form-wizard-progress">
                    		    <div class="form-wizard-progress-line" data-now-value="12.25" data-number-of-steps="5a" style="width: 12.25%;"></div>
                    		</div>
							<!-- Step 1 -->
                    		<div class="form-wizard-step activated">
                    			<div class="form-wizard-step-icon"><i class="fa fa-road" aria-hidden="true"></i></div>
                    			<p>Trajet</p>
                    		</div>
							<!-- Step 1 -->
							
							<!-- Step 2 -->
                    		<div class="form-wizard-step activated">
                    			<div class="form-wizard-step-icon"><i class="fa fa-car" aria-hidden="true"></i></div>
                    			<p>Prestation</p>
                    		</div>
							<!-- Step 2 -->
							
							<!-- Step 3 -->
							<div class="form-wizard-step activated">
                    			<div class="form-wizard-step-icon"><i class="fa fa-user" aria-hidden="true"></i></div>
                    			<p>Coordonnées</p>
                    		</div>
							<!-- Step 3 -->
							
							<!-- Step 4 -->
							<div class="form-wizard-step activated">
                    			<div class="form-wizard-step-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
                    			<p>Confirmation</p>
                    		</div>
							<!-- Step 4 -->
							
							<!-- Step 5 -->
							<div class="form-wizard-step active">
                    			<div class="form-wizard-step-icon"><i class="fa fa-credit-card" aria-hidden="true"></i></div>
                    			<p>Paiement</p>
                    		</div>
							<!-- Step 5 -->
                    	</div>
						<!-- Form progress -->					
							
						<!-- Form Step 4 -->
						<fieldset>
							<!-- Progress Bar -->
							<div class="progress">
								<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:80%"></div>
							</div>
							<!-- Progress Bar -->
							<h4>Confirmation<span><em><small>Étape 4 - 5</small></em></span></h4>
							<div style="clear:both;"></div>
							<hr />
							<div class="success">
								<h3>Votre réservation a été effectuée avec succès !</h3>
								<div class="success-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
							</div>
							<hr />
							<center><p>Vous pouvez dès maintenant procéder au paiement afin de finaliser définitivement votre réservation.</p></center>
							<div class="form-wizard-buttons">
								<button type="button" class="btn btn-next"><i class="fa fa-credit-card" aria-hidden="true"></i> Paiement</button>
							</div>
						</fieldset>
						<!-- Form Step 4 -->
						
						<!-- Form Step 5 -->
						<fieldset>
							<!-- Progress Bar -->
							<div class="progress">
								<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"></div>
							</div>
							<!-- Progress Bar -->
							<h4>Paiement<span><em><small>Étape 5 - 5</small></em></span></h4>
							<div style="clear:both;"></div>
							<hr />
							<div class="container-fluid form-group">
								<div class="row form-inline">
									<div class="form-group col-md-6 col-xs-12">
										<label><i class="fa fa-cc-paypal" aria-hidden="true"></i> Paiement via Paypal</label>
										<form method="post" action="https://www.paypal.com/cgi-bin/webscr" name="_xclick">
											<input type="hidden" value="_xclick" name="cmd" />
											<input type="hidden" value="amrani.hassan57@gmail.com" name="business" />
											<input type="hidden" value="EUR" name="currency_code" />
											<input type="hidden" value="Paiement du service" name="item_name" />
											<input type="hidden" value="../reservation.html" name="return" />
											<input type="hidden" id="amount" class="form-control" style="width:100%" name="amount" value="<?php echo $_SESSION['prix']* $percent; ?>" />
											<input class="btn btn-submit" style="width:100%;" type="submit" value="Payer" />
											<div style="clear:both;"></div>
											<br />
											<img class="img-responsive" alt="Paiement Paypal" title="Paiement online Paypal" src="http://www.drivenlimousineservice.be/img/paypal.png" />
										</form>
									</div>
									<div class="form-group col-md-6 col-xs-12">
										<label><i class="fa fa-handshake-o" aria-hidden="true"></i> Paiement au chauffeur</label>

										<p><i class="fa fa-check-square-o" aria-hidden="true" style="color:green"></i> Paiement à bord, directement au chauffeur le jour de la prestation</p>
										<p><i class="fa fa-check-square-o" aria-hidden="true" style="color:green"></i> Un contact téléphone sera fait pour confirmer la réservation et le point de rencontre</p>
										<br />
										<a href="http://www.drivenlimousineservice.be/" class="btn btn-previous" style="margin-right:10px;"><i class="fa fa-home" aria-hidden="true"></i> Retour à l'accueil</a>
									</div>
								</div>
							</div>
						</fieldset>
						<!-- Form Step 5 -->
                    </div>
                </div>
            </div>
        </section>
		<!-- main content -->

        <!-- Jquery JS -->
        <script src="js/jquery-1.11.1.min.js"></script>
		<!-- bootStrap JS -->
		<script src="js/bootstrap.min.js"></script>
		
		<!-- JS -->
		<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script>
			$(function() {
				$( "#date_aller" ).datepicker();
				$( "#date_retour" ).datepicker();
			});

			jQuery(function($){
				$.datepicker.regional['fr'] = {
					closeText: 'Fermer',
					prevText: '&#x3c;Préc',
					nextText: 'Suiv&#x3e;',
					currentText: 'Aujourd\'hui',
					monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
					'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
					monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
					'Jul','Aou','Sep','Oct','Nov','Dec'],
					dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
					dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
					dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
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
        <script src="js/form-wizard.js"></script>
		<script src="js/form-wizard_2.js"></script>
        <!-- Plugin Custom JS -->

		<!-- Include Google Maps JS API -->
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>

		<!-- Custom JS code to bind to Autocomplete API -->
		<script type="text/javascript">
			function initializeAutocomplete(id) {
				var element = document.getElementById(id);
				if (element) {
					var autocomplete = new google.maps.places.Autocomplete(element, {
						types: ['geocode']
					});
					google.maps.event.addListener(autocomplete, 'place_changed', onPlaceChanged);
				}
			}

			function onPlaceChanged() {
				var place = this.getPlace();

				// console.log(place);  // Uncomment this line to view the full object returned by Google API.

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

			google.maps.event.addDomListener(window, 'load', function() {
				initializeAutocomplete('autocomplete_address_departure');
				initializeAutocomplete('autocomplete_address_arrival');
			});
		</script>

    </body>

</html>
