<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

// on regarde si le joueur est bien sur un passage ondulant
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un passage ondulant !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 29 and $tab_lieu['type_lieu'] != 30)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un passage ondulant !!!");
	}
}

if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	$cout_pa = $tab_lieu['lieu_prelev'];
	$type_lieu = $tab_lieu['type_lieu'];
	echo("<p><b>$nom_lieu</b> - $desc_lieu ");
	echo("<p>Ce passage a quelque chose d’étrange, il ne semble pas constitué de la même manière que les passages magiques que vous connaissez.");
	if ($type_lieu == 29)
	{
		echo("<p><b>Il semblerait bien que vous ne puissiez pas le prendre en étant tangible.</b> Certainement une propriété de la matière qui pourrait vous empêcher de le prendre.");
	}
	echo("<p><a href=\"action.php?methode=passage\">Prendre ce passage ! (" . $cout_pa . " PA)</a></p>");
}
?>
