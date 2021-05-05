<?php
include "blocks/_header_page_jeu.php";
ob_start();
define("APPEL", 1);


$type_lieu = 1;
$nom_lieu  = 'une banque';
include "blocks/_test_lieu.php";

$perso     = $verif_connexion->perso;
$perso_cod = $verif_connexion->perso_cod;

if ($erreur == 0)
{
    echo '<div class="bordiv">
    <div  class="soustitre2" style="margin-left:8px; margin-right:8px; padding:8px; border-radius:10px 10px 0 0; border:solid black 2px;">
    <img height="50px;" src="/images/coffre.png" style="vertical-align:middle;">
    &nbsp; &nbsp;<em>Les coffres individuels de </em> <strong><FONT color="#8b0000">stockage</FONT></strong> 
    </div>';
}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";