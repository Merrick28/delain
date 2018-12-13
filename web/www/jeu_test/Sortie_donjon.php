<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";
// on regarde si le joueur est bien sur une sortie de donjon
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur une sortie de donjon !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 37)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur une sortie de donjon !!!");
	}
}

if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$tab_lieu = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	echo("<p><strong>$nom_lieu</strong> - $desc_lieu ");
	echo("<p>Vous voyez la sortie de ce donjon.");
	echo("<p><a href=\"action.php?methode=sortir_donjon\">Prendre la sortie ! (4PA)</a></p>");
}
