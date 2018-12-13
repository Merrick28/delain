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
	if ($tab_lieu['lieu_cod'] != 2139)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
	}
}
if ($erreur == 0)
{
	$req = "select lpos_lieu_cod from lieu_position where lpos_pos_cod =  ";
$req = $req . "(select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod) ";
$db->query($req);
$db->next_record();
$lieu = $db->f("lpos_lieu_cod");
	$req = "select pge_perso_cod from perso_grand_escalier where pge_perso_cod = $perso_cod ";
	$req = $req . "and pge_lieu_cod = $lieu ";
	$db->query($req);
	if ($db->nf() == 0)
	{
		echo "<p>Erreur ! Vous ne pouvez pas utiliser ce passage !";
	}
	else
	{
	

	$tab_lieu = $db->get_lieu($perso_cod);
	
	$req_pa = "select perso_pa from perso where perso_cod = $perso_cod ";
	$db->query($req_pa);
	$db->next_record();
	$pa_perso = $db->f("perso_pa");
	if ($pa_perso < 4)
	{
		echo("<p>Vous n'avez pas assez de PA !!!!");
	}
	else
	{
		$pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod ";
		$db->query($pa);
		
		$req_pos = "select ppos_pos_cod,pos_x,pos_y,pos_etage from perso_position,positions where ppos_perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
		$db->query($req_pos);
		$db->next_record();
		
		$pos_actuelle = $db->f("ppos_pos_cod");
		
		$req = "select lieu_dest from lieu,lieu_position ";
		$req = $req . "where lpos_pos_cod = $pos_actuelle ";
		$req = $req . "and lpos_lieu_cod = lieu_cod ";
		$db->query($req);
		$db->next_record();
		$n_pos = $db->f("lieu_dest");
		
		$req = "select pos_etage from positions where pos_cod = $n_pos ";
		$db->query($req);
		$db->next_record();
		$n_etage = $db->f("pos_etage");
		
		
		$req = "select update_etage_visite($perso_cod,$n_etage)";
		$db->query($req);
		
		
		$deplace = "update perso_position set ppos_pos_cod = $n_pos where ppos_perso_cod = $perso_cod ";
		$db->query($deplace);
		
		
		// on efface pour le retour
		$req = "delete from perso_grand_escalier where pge_perso_cod = $perso_cod ";
		$req = $req . "and pge_lieu_cod = 2139 ";
		$db->query($req);
		
		$req = "select etage_libelle,etage_description from etage,positions where pos_cod = $n_pos and pos_etage = etage_numero ";
		$db->query($req);
		$db->next_record();
		echo "<p>Vous arrivez dans le lieu : <strong>" . $db->f("etage_libelle") . "</strong><br>";
		echo "<p><em>" . $db->f("etage_description") . "</em>";
		
		// on remet l'ancien temple si besoin
		$req = "select ptemple_anc_pos_cod from perso_temple where ptemple_perso_cod = $perso_cod ";
		$db->query($req);
		$db->next_record();
		if ($db->f("ptemple_anc_pos_cod") == 0)
		{
			$req = "delete from perso_temple where ptemple_perso_cod = $perso_cod ";
		}
		else
		{
			$req = "update perso_temple set ptemple_pos_cod = ptemple_anc_pos_cod,ptemple_nombre = ptemple_anc_nombre ";
			$req = $req . "where ptemple_perso_cod = $perso_cod ";
		}
		$db->query($req);
		
	}
	}
	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');

