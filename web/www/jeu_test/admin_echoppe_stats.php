<?php
include "blocks/_header_page_jeu.php";
ob_start();

$erreur = 0;
$methode           = get_request_var('methode', 'debut');
include "blocks/_test_admin_echoppe.php";
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            $type_lieu = '11,14';
            require "_admin_echoppe_stats_debut.php";
            break;
        case "stats":
            require "_admin_echoppe_stats_stats.php";
            break;
        case "stats2":
            require "_admin_echoppe_stats_stats2.php";
            break;
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";