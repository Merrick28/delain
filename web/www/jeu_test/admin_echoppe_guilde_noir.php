<?php
include "blocks/_header_page_jeu.php";

ob_start();
define('APPEL', 1);
$erreur  = 0;
$methode = get_request_var('methode', 'debut');
$perso   = new perso;
$perso   = $verif_connexion->perso;
if ($perso->perso_admin_echoppe_noir != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            require "_admin_echoppe_guilde_debut.php";
            break;
        case "suite":
            $champ = "guilde_modif_noir";
            require "_admin_echoppe_guilde_suite.php";
            break;

    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

