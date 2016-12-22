<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$erreur = 0;
if ($db->is_milice($perso_cod) == 0)
{
	echo "<p>Erreur ! Vous n'averz pas accès à cette page !";
	$erreur = 1;
}
$lieu['entree'] = 15;
$lieu['bat_adm'] = 9;
$lieu['poste_garde'] = 5;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un lieu permettant cette action !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$suite = 1;
	$tab_lieu = $db->get_lieu($perso_cod);
	if (!in_array($tab_lieu['type_lieu'], $lieu))
	{
   	echo "<p>Erreur ! Le lieu sur lequel vous vous trouvez ne permet pas cette action !";
   	$suite = 0;
	}
	$etage_min = $db->getparm_n(67);
	$req = "select pos_etage from positions,perso_position ";
	$req = $req . "where ppos_perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
	$db->query($req);
	$db->next_record();
	if (($db->f("pos_etage") > 0) || ($db->f("pos_etage") < -3))
	{
		echo "<p>Erreur ! Le lieu sur lequel vous vous trouvez ne permet pas cette action !";
   	$suite = 0;
	}
	if ($suite == 1)
	{
		$req = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
		$db->query($req);
		$db->next_record();
		$pos_actu = $db->f("ppos_pos_cod");
		echo "<p>Liste des destinations possibles (cliquez sur un lieu pour vous y rendre - ", $db->getparm_n(68) , " PA):";
		echo "<table>";
		$req = "select pos_cod,lieu_nom,pos_x,pos_y,etage_libelle,pos_etage ";
		$req = $req . "from lieu,lieu_position,positions,etage ";
		$req = $req . "where lieu_tlieu_cod in (15,9,5) ";
		$req = $req . "and lpos_lieu_cod = lieu_cod ";
		$req = $req . "and lpos_pos_cod = pos_cod ";
		$req = $req . "and pos_cod != $pos_actu ";
		$req = $req . "and pos_etage <= 0 ";
		$req = $req . "and pos_etage >= $etage_min ";
		$req = $req . "and etage_numero = pos_etage ";
		$req = $req . "order by pos_etage desc, lieu_nom ";
		$db->query($req);
		while ($db->next_record())
		{
			echo "<tr>";
			echo "<td class=\"soustitre2\"><b><a href=\"action.php?methode=milice_tel&destination=" , $db->f("pos_cod") , "\">" , $db->f("lieu_nom") , "</a><b></td>";
			echo "<td>" , $db->f("pos_x") , "</td>";
			echo "<td class=\"soustitre2\">" , $db->f("pos_y") , "</td>";
			echo "<td>" , $db->f("etage_libelle") , "</td>";
			echo "</tr>";
		}
		echo "</table>";



		
	}	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
