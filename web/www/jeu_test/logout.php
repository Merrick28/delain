<?php
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
require_once G_CHE . "includes/classes.php";

$myAuth = new myauth;
$myAuth->start();
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
if ($normal_auth)
{
    $myAuth->logout();
}

header('Location: ' . $type_flux . G_URL);





