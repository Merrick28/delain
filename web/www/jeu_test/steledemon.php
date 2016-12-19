<?php if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";

//
// on regarde si le joueur est bien sur le lieu qu'on attend
//
$erreur = 0;
if (!isset($methode))
{
	$methode = 'entree';
}
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un lieu !!!");
	$erreur = 1;
}
$perso_fam = false;
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 35)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n’êtes pas proche d'une stèle !!!</p>");
	}
	$req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
	$db->query($req);
	$db->next_record();
	if ($db->f("perso_type_perso") == 3)
	{
		$erreur = 1;
		$perso_fam = true;
		echo("<p>Un familier ne peut utiliser les stèles démoniques.</p>");
	}
}

//
// OK, tout est bon, on s’attaque à la suite
//
$htmlout = '';
if ($erreur == 0)
{
  $req = 'select mb.*, ps.perso_nom 
          from miniboss as mb, perso as ps 
          where mboss_perso_cod=perso_cod 
            and mboss_lieu_cod='.$tab_lieu['lieu_cod'];
  $db->query($req);
  $db->next_record();
  
  // Si la stèle et le boss sont actifs
  if ($db->f("mboss_stele_etat") && $db->f("mboss_boss_etat")) {
    $htmlout .= '<p>Il vous faut éliminer '.$db->f("perso_nom").' pour espérer détruire cette stèle.</p>';
  }
  
  echo '<p>'.$tab_lieu['description'].'</p>'.$htmlout;
}

?>
