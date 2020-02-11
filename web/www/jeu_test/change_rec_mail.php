<?php
include "blocks/_header_page_jeu.php";
$contenu_page = '<p class="titre">Réception des événements par courriel</p>';
if ($type == 'e')
{
    if ($met == 'n')
    {
        $req = "update compte set compt_envoi_mail = 0 where compt_cod = $compt_cod ";
        $stmt = $pdo->query($req);
        $contenu_page .= 'Vous ne recevez plus les comptes rendus d’événements par courriel<br>';
    } else
    {
        $req = "update compte set compt_envoi_mail = 1 where compt_cod = $compt_cod ";
        $stmt = $pdo->query($req);
        $contenu_page .= 'Vous recevrez les comptes rendus d’événements par courriel<br>';
    }
}
if ($type == 'm')
{
    if ($met == 'n')
    {
        $req = "update compte set compt_envoi_mail_message = 0 where compt_cod = $compt_cod ";
        $stmt = $pdo->query($req);
        $contenu_page .= 'Vous ne recevez plus les avis de messages par courriel<br>';
    } else
    {
        $req = "update compte set compt_envoi_mail_message = 1 where compt_cod = $compt_cod ";
        $stmt = $pdo->query($req);
        $contenu_page .= 'Vous recevrez les avis de messages par courriel<br>';
    }
}

if (isset($_POST["methode"]) && $_POST["methode"] == 'frequence')
{
    $frequence = pg_escape_string($_POST['frequence']);
    $req = "update compte set compt_envoi_mail_frequence = $frequence where compt_cod = $compt_cod ";
    $stmt = $pdo->query($req);
    $contenu_page .= 'Fréquence de réception des courriels mise à jour';
}
include "blocks/_footer_page_jeu.php";