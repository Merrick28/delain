<?php
define('NO_DEBUG',true);
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 09/01/2019
 * Time: 09:55
 */


$gobj = new objet_generique();
$liste_obj = $gobj->getBy_gobj_tobj_cod($_POST['tobj_cod']);
echo json_encode($liste_obj);



