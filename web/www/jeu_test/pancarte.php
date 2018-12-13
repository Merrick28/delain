<?php 

if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

$tab_lieu = $db->get_lieu($perso_cod);
echo "<p>Vous voyez une pancarte qui indique : ";
echo "<p><strong><i>" . $tab_lieu['description'] . "</i></strong>";
