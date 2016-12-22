<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";

define("OBJET_PIOCHE_ID",332);
	
// V�rifie si le perso a la competence requise.
function perso_has_objet($perso_id,$object_id) {
  global $db;
  $req_matos = "select perobj_obj_cod from perso_objets,objets
    where perobj_obj_cod = obj_cod
    and perobj_perso_cod = $perso_id
    and obj_gobj_cod = $object_id ";
  $db->query($req_matos);
  return $db->next_record();
}
function perso_has_objet_equipe($perso_id,$object_id) {
  global $db;
  $req_matos = "select perobj_obj_cod from perso_objets,objets
    where perobj_obj_cod = obj_cod
    and perobj_equipe='O'
    and perobj_perso_cod = $perso_id
    and obj_gobj_cod = $object_id ";
  $db->query($req_matos);
  return $db->next_record();
}
