<?php
include "blocks/_header_page_jeu.php";

$compte = new compte;

$adresse      = $_POST['mail1'];
$from         = $param->getparm(16);
$contenu_page = '<p class="titre">Changement d’adresse électronique</p>
<p style="text-align:center;"><strong>Changement effectué !</strong></p>';
// changment d'adresse
$compte->compte_mail = $_POST['mail1'];

// génération de password
$n_pass = rand(100000, 999999);
$compte->charge($compte_cod);
$compte->compt_passwd_hash = crypt($n_pass);
$compte->stocke();

// génération du mail
$texte_mail = "Bonjour,\n";
$texte_mail = $texte_mail . "Vous venez de modifier votre adresse mail pour les souterrains de Delain.\r\n";
$texte_mail = $texte_mail . "Votre nouveau mot de passe est : $n_pass \r\n";
$entete     = "From: " . $from . "\r\n";
$entete     = $entete . "Reply-To: " . $from . "\r\n";
$entete     = $entete . "Error-To: " . $from . "\r\n";
$sujet      = "Changement d’adresse électronique\r\n";
if (mail($adresse, $sujet, $texte_mail, $entete))
{
    $contenu_page .= '<br><p>Un mail vous a été adressé à l’adresse ' . $adresse . ' pour votre nouveau mot de passe
	<p>Vous ne pourrez jouer qu’une fois ce mail reçu';
} else
{
    $contenu_page .= '<br><p>Une erreur est survenue lors de l’envoi du mail ! Veuillez contacter un administrateur du jeu : controleurs@jdr-delain.net</p>';
}
include "blocks/_footer_page_jeu.php";


$myAuth = new myauth;
$myAuth->start();
$myAuth->logout();

