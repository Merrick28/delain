<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);


//
//Contenu de la div de droite
//
$req = "select dcompt_acces_log from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);

$acces_autorise = $db->next_record() && $db->f("dcompt_acces_log") == 'O';

if (!$acces_autorise)
{
	echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
}
else
{
	$liste_logs = array(
		"perso" => array("perso_edit.log", "Modification sur les personnages."),
		"monstre" => array("monstre_edit.log", "Modification sur les monstres."),
		"droits" => array("droit_edit.log", "Modification des droits d’accès."),
		"objet" => array("objet_edit.log", "Modification sur les objets."),
		"refuge" => array("change_refuge.log", "Modification sur le statut refuge des magasins."),
		"temple" => array("temple.log", "Modification sur le statut refuge des temples."),
		"factions" => array("factions.log", "Modification sur les factions."),
		"animations" => array("animations.log", "Lancement des animations."),
		"lieux" => array("lieux_etages.log", "Modification sur les étages et lieux."),
		"poste" => array("relais_poste.log", "Transactions via les relais de la poste."),
	);
	$visu = (isset($visu)) ? $visu : "début";
	$mode = (isset($mode)) ? $mode : "web";

	if (isset($liste_logs[$visu]) && $mode == "web")
	{
		echo "<p><b>Visualisation du fichier de log " . $liste_logs[$visu][1] . "</b> - <a href='?visu=liste'>Retour au début</a></p>";
		echo "<div class='bordiv' style='max-height: 800px; overflow: auto;'><pre>";
		include ('../logs/' . $liste_logs[$visu][0]);
		echo "</pre></div>";
		echo '<p style="text-align:center"><a href="?visu=liste">Retour au début</a></p>';
	}
	if (isset($liste_logs[$visu]) && $mode == "texte")
	{
		header ('Content-Type: text/plain; charset=utf-8');
		include ('../logs/' . $liste_logs[$visu][0]);
		die();
	}
	if (!isset($liste_logs[$visu]))
	{
		echo "<p><b>Liste des fichiers de log</b></p>";
		foreach ($liste_logs as $id => $valeurs)
		{
			$nom = $valeurs[1];
			echo " - $nom <a href='?mode=web&visu=$id'>mode web</a> / <a href='?mode=texte&visu=$id'>mode texte</a><br/>";
		}
	}
}

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
//include"../logs/monstre_edit.log";
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
