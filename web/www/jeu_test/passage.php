<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un passage !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 10 && $tab_lieu['type_lieu'] != 34) // Passage et grande porte
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un passage !!!");
	}
}

if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$tab_lieu = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	echo("<p><b>$nom_lieu</b> - $desc_lieu ");
	echo("<p>Vous voyez un passage vers un autre lieu.");
	echo("<p><a href=\"action.php?methode=passage\">Prendre ce passage ! (" . $db->getparm_n(13) . " PA)</a></p>");
}


?>

