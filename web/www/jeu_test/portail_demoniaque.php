<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

$desc = $db->get_lieu($perso_cod);
echo "<p><b>". $tab_lieu['nom'] ."</b> - ". $tab_lieu['description'];

?>
