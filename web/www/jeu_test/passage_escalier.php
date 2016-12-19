<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";

// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 3)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
	}
}
if ($db->compte_objet($perso_cod,86) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
	$erreur = 1;
}
if ($db->compte_objet($perso_cod,87) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
	$erreur = 1;
}
if ($db->compte_objet($perso_cod,88) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
	$erreur = 1;
}

if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$tab_lieu = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	echo("<p><b>$nom_lieu</b><br>$desc_lieu ");
	echo("<p><a href=\"action.php?methode=passage\">Prendre cet escalier ! (" . $db->getparm_n(13) . " PA)</a></p>");
}

?>
