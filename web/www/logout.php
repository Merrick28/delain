<?php
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
include 'classes.php';

$myAuth = new myauth;
$myAuth->start();
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));

$myAuth->logout();


// on suprri

header('Location: ' . $type_flux . G_URL);





