<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
$erreur = 0;
if ($perso->perso_admin_echoppe_noir != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
require "blocks/_valide_gerant.php";