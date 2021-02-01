<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
include "blocks/_test_admin_echoppe.php";
require "blocks/_valide_gerant.php";