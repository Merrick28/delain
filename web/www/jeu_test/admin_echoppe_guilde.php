<?php
include "blocks/_header_page_jeu.php";
ob_start();
$methode           = get_request_var('methode', 'debut');
include "blocks/_test_admin_echoppe.php";
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            require "_admin_echoppe_guilde_debut.php";
            break;
        case "suite":
            $champ = "guilde_modif";
            require "_admin_echoppe_guilde_suite.php";
            break;
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
