<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";

// V�rifie si le perso a les composants suffisants pour une formule.
function perso_has_formule_composants($perso_id, $formule_id) {
  global $db;
  $req_formule = "select 1 from formule_composant frco
      where frmco_frm_cod = $formule_id
      and frmco_num > (select count(obj_cod) from objets,perso_objets where frco.frmco_gobj_cod = obj_gobj_cod
      and perobj_obj_cod = obj_cod
      and perobj_perso_cod = $perso_id)";
   $db->query($req_formule);
   return !$db->next_record();
}

// Liste les ids de formules possibles avec les composants dans l'inventaire du perso.
function perso_formules_disponibles($perso_id) {
   global $db;
   $res = array();
   $req_formule = "select frm_cod,frm_nom from formule frm1 where not exists(select 1 from formule_composant frco
      where frmco_frm_cod = frm1.frm_cod
      and frmco_num > (select count(obj_cod) from objets,perso_objets where frco.frmco_gobj_cod = obj_gobj_cod
      and perobj_obj_cod = obj_cod
      and perobj_perso_cod = $perso_id))";
   $db->query($req_formule);
   while($db->next_record()){
     echo "DEBUG:formule=".$db->f("frm_cod");
     $res[] =  array($db->f("frm_cod"),$db->f("frm_nom"));
   }
   return $res;
}

// S�lection de tous les objets de l'inventaire pouvant �tre des composants
function perso_composants_disponibles($perso_id) {
  global $db;

  $req_matos = "select distinct obj_gobj_cod,obj_nom from perso_objets,objets,formule_composant
    where perobj_obj_cod = obj_cod
    and perobj_perso_cod = $perso_id
    and obj_gobj_cod = frmco_gobj_cod ";
  $db->query($req_matos);
  $res = array();
  while($db->next_record()){
     echo "DEBUG:comp=".$db->f("obj_gobj_cod");
     $res[] =  array($db->f("obj_gobj_cod"),$db->f("obj_nom"));
  }
  return $res;
}

// R�alisation de formule directe (sans tenir compte du cout en temps )
function perso_realiser_formule($perso_id,$formule_id){

   // Pr�requis
   perso_prerequis_formule($perso_id,$formule_id);

   // Ajout produits
}
// debute une formule, ajoute l'objet inachev� dans l'inventaire
function perso_debuter_formule($perso_id,$formule_id){

   // Pr�requis
   perso_prerequis_formule($perso_id,$formule_id);
   
   // Ajout Objet inachev�
   
}
// Am�liore l'�tat d'un objet inachev�
function perso_travailler_objet($perso_id,$objet_id){

}
// Finalise un objet inachev�
function perso_terminer_formule($perso_id,$objet_id){

}
// D�but commun pour commencer une formule
function perso_prerequis_formule($perso_id,$formule_id){
   // Check comp�tence

   // Check outil

   // Check composants

   // Check cout

   // Test competences

   // Retrait composants

}
