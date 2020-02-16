<?php 
include G_CHE . 'ident.php';
require_once G_CHE . "includes/classes.php";

$myAuth = new myauth;
$myAuth->start();
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
if($normal_auth)
{
    $myAuth->logout();
}

header('Location: ' . $type_flux . G_URL);





