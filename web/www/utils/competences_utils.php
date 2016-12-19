<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";

define("COMPETENCE_ATTAQUE_POINGS_ID",30);
define("COMPETENCE_FORGE_MAGE_ID",88);
define("COMPETENCE_FORGE_ID",98);
define("COMPETENCE_COUTURE_ID",99);
	
// Vérifie si le perso a la competence requise.
function perso_has_competence($perso_id, $competenceid) {
  global $db;
  $req_comp = "select pcomp_modificateur from perso_competences ";
  $req_comp = $req_comp . "where pcomp_perso_cod = $perso_id ";
  $req_comp = $req_comp . "and pcomp_modificateur != 0 ";
  $req_comp = $req_comp . "and pcomp_pcomp_cod = $competenceid";
  $db->query($req_comp);
  return $db->next_record();
}

// Vérifie si le perso a une compétence requise parmi une liste
function perso_has_one_competence_in_list($perso_id, $competence_list) {
  global $db;
  $comp_range = "";
  for ($i = 0; $i < count($competence_list); $i++) {
    $comp_range .= $competence_list[$i];
    if($i < (count($competence_list)-1)){
      $comp_range .= ',';
    }
  }
  $req_comp = "select pcomp_modificateur from perso_competences ";
  $req_comp = $req_comp . "where pcomp_perso_cod = $perso_id ";
  $req_comp = $req_comp . "and pcomp_modificateur != 0 ";
  $req_comp = $req_comp . "and pcomp_pcomp_cod IN ($comp_range)";
  $db->query($req_comp);
  return $db->next_record();
}

// un objet pour contenir les infos du retour d'une utilisationde compétence.
class RetourCompetence {
  // Valeur de la compétence
  var $comp_val = 0;
  // Entier : le jet dans la compétence.
  var $jet = 100;
  // Booleen : true si succès.
  var $succes = false;
  // Boolean : true si special.
  var $special = false;
  // Booleen : true si critique.
  var $critique = false;
  // Booleen : true si echec critique.
  var $echec_critique = false;
  // Booleen : true si amélioration.
  var $amelioration = false;
  // Booleen : true si erreur pendant le lancement.
  var $erreur = false;
  // Texte : le log de l'utilisationde la compétence.
  var $log = '';
}

// Utilisation d'une competence avec un perso - sans amélioration.
function perso_utiliser_competence($perso_id, $competenceid, $bonus) {
  global $db;
  $retour = new RetourCompetence;
  $req_comp = "select pcomp_modificateur,comp_libelle from perso_competences,competences
    where pcomp_perso_cod = $perso_id
    and pcomp_modificateur != 0
    and pcomp_pcomp_cod = $competenceid
    and pcomp_pcomp_cod = comp_cod";
  //echo $req_comp;
  $db->query($req_comp);
  if(!$db->next_record()){
    $retour->log .= "Vous ne disposez pas de la compétence requise ! <br /><br />";
    $retour->erreur = false;
    return $retour;
  } else {
    $comp_valeur = $db->f("pcomp_modificateur");
    $retour->comp_val = $comp_valeur;
    $comp_libelle = $db->f("comp_libelle");
    $comp_valeur += $bonus;
    $comp_valeur_spe = $comp_valeur / 4;
    $des = rand(1,100);
    $retour->log .= "Vous utilisez la compétence <b> $comp_libelle </b> <br /><br />";
    $retour->log .= "Votre chance de réussite en tenant compte des modificateurs est de <b> $comp_valeur </b> <br /><br />";
    $retour->log .= "Votre lancer de dés est de <b> $des </b> <br /><br />";
    $retour->jet = $des;
    if($des > 96){
      $retour->log .= "Il s'agit donc d'un échec automatique.<br /><br />";
      $retour->echec_critique = true;
      return $retour;
    } else if($des > 5 && $des > $comp_valeur_spe && $des > $comp_valeur){
      $retour->log .= "Il s'agit donc d'un échec.<br /><br />";
      return $retour;
    } else if($des > 5 && $des > $comp_valeur_spe && $des <= $comp_valeur){
      $retour->log .= "Il s'agit donc d'une réussite.<br /><br />";
      $retour->succes = true;
      return $retour;
    } else if($des > 5 && $des <= $comp_valeur_spe){
      $retour->log .= "Il s'agit donc d'une réussite spéciale.<br /><br />";
      $retour->succes = true;
      $retour->special = true;
      return $retour;
    } else if($des <= 5){
      $retour->log .= "Il s'agit donc d'une réussite critique.<br /><br />";
      $retour->succes = true;
      $retour->critique = true;
      return $retour;
    }
  }
}
// Utilisation d'une competence avec un perso - sans amélioration.
function perso_utiliser_competence_avec_amelioration($perso_id, $competenceid, $bonus) {
  $retour = perso_utiliser_competence($perso_id, $competenceid, $bonus);
  if($retour->succes || (!$retour->echec_critique && $retour->comp_val <= 40)){
      $retour = perso_amelioration_competence($perso_id, $competenceid, $retour);
  }
  return $retour;
}
// amelioration d'une competence
function perso_amelioration_competence($perso_id, $competenceid, $retour) {
  global $db;
  $comp_init =  $retour->comp_val;
  $req_comp = "select ameliore_competence_px($perso_id,$competenceid,$comp_init) as amel_retour";
  $db->query($req_comp);
  if($db->next_record()){
    $amel_retour = $db->f("amel_retour");
    $amel_res = split(';',$amel_retour);
    $jet = $amel_res[0];
    $succes = $amel_res[1];
    $new_val = $amel_res[2];
    $retour->log .= "Votre jet d'amélioration est de <b> $jet </b> <br /><br />";
    if($succes == 1){
      $retour->log .= "Vous avez donc amélioré cette compétence <br /><br />";
      $retour->log .= "Sa nouvelle valeur est de <b> $new_val </b> <br /><br />";
    } else {
      $retour->log .= "Vous n'avez pas amélioré cette compétence <br /><br />";
    }
    echo $amel_retour;
  }
  return $retour;
}


?>
