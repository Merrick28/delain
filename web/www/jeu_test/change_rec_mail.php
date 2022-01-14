<?php
include "blocks/_header_page_jeu.php";
$compte       = $verif_connexion->compte;
$contenu_page = '<p class="titre">Réception des événements par courriel</p>';
if ($_REQUEST['type'] == 'e')
{
    if ($_REQUEST['met'] == 'n')
    {
        $compte->compt_envoi_mail = 0;
        $compte->stocke();
        $contenu_page .= 'Vous ne recevez plus les comptes rendus d’événements par courriel<br>';
    } else
    {
        $compte->compt_envoi_mail = 1;
        $compte->stocke();
        $contenu_page .= 'Vous recevrez les comptes rendus d’événements par courriel<br>';
    }
}
if ($_REQUEST['type'] == 'm')
{
    if ($_REQUEST['met'] == 'n')
    {
        $compte->compt_envoi_mail_message = 0;
        $compte->stocke();
        $contenu_page .= 'Vous ne recevez plus les avis de messages par courriel<br>';
    } else
    {
        $compte->compt_envoi_mail_message = 1;
        $compte->stocke();
        $contenu_page .= 'Vous recevrez les avis de messages par courriel<br>';
    }
}

if (isset($_POST["methode"]) && $_POST["methode"] == 'frequence')
{
    $compte->compt_envoi_mail_frequence = $_POST['frequence'];
    $compte->stocke();
    $contenu_page .= 'Fréquence de réception des courriels mise à jour';
}
include "blocks/_footer_page_jeu.php";