<?php
include "blocks/_header_page_jeu.php";
ob_start();
define('APPEL', 1);
include "blocks/_test_admin_echoppe.php";
$methode          = get_request_var('methode', 'debut');
$_admin_echoppe_type = "";

include "blocks/_admin_echoppe.php";
include "blocks/_footer_page_jeu.php";