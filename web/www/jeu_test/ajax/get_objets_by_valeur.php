<?php
define('NO_DEBUG',true);
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 09/01/2019
 * Time: 09:55
 */


$gobj = new objet_generique();
$prix = explode(";",$_POST['valeur']);




$liste_obj = $gobj->getByValeur($prix[0],$prix[1]);
echo json_encode($liste_obj);



