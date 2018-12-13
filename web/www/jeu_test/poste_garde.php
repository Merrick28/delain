<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

	$tab_temple = $db->get_lieu($perso_cod);
    echo("<p><strong>" . $tab_temple['nom'] . "</strong>");
	?>
	<p>Les gardes semblent bien entrainés, et vous comprenez qu'il est inutile d'essayer de vous battre contre eux.
	<?php 
	if ($db->is_milice($perso_cod) == 1)
{
	echo "<p><a href=\"milice_tel.php\">Se téléporter vers un autre lieu ? </a>";
}

?>
