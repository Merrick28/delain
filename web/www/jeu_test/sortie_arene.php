<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";
$param = new parametres();
// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur une sortie d'arène !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 31)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur une sortie d'arène !!!");
	}
}

if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	echo("<p><b>$nom_lieu</b> - $desc_lieu ");
	echo("<p>Vous voyez la sortie de cette arène.");
	echo("<p><a href=\"action.php?methode=sortie_arene\">Prendre la sortie ! (" . $param->getparm(13) . " PA)</a></p>");
}
