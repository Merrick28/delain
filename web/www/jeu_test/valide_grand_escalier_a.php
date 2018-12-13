<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$param = new parametres();
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

// on regarde si le joueur est bien sur un escalier
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n’êtes pas sur un escalier !!!</p>");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 16)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n’êtes pas sur un escalier !!!</p>");
	}
}
$req = "select perso_type_perso, perso_pa from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
$pa_perso = $db->f("perso_pa");
if ($db->f("perso_type_perso") == 3)
{
    echo "<p>Un familier ne peut pas prendre un grand escalier tout seul !</p>";
	$erreur = 1;
}
if ($db->f("perso_type_perso") == 2)
{
    echo "<p>Évitons les massacres ! Un monstre de ce niveau ne devrait pas prendre un grand escalier, sauf intervention de Monstres & Cie...</p>";
	$erreur = 1;
}
if ($db->compte_objet($perso_cod,86) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.</p>";
	$erreur = 1;
}
if ($db->compte_objet($perso_cod,87) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.</p>";
	$erreur = 1;
}
if ($db->compte_objet($perso_cod,88) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.</p>";
	$erreur = 1;
}
if ($pa_perso < $param->getparm(43))
{
	echo("<p>Vous n’avez pas assez de PA !!!!</p>");
	$erreur = 1;
}
if ($db->is_locked($perso_cod))
{
	$req = "select fuite($perso_cod) as texte_fuite";
	$db->query($req);
	$db->next_record();
	$fuite = explode('#',$db->f("texte_fuite"));
	if($fuite[0] != 1)
	{
		$erreur = 0;
	}
	else
	{
		$erreur = 1;
	}
	echo $fuite[1];	
}

if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$pa = "update perso set perso_pa = max(perso_pa - ". $param->getparm(43) .",0) where perso_cod = $perso_cod ";
	$db->query($pa);
	
	$req_pos = "select ppos_pos_cod,pos_x,pos_y,pos_etage 
		from perso_position,positions 
		where ppos_perso_cod = $perso_cod 
			and ppos_pos_cod = pos_cod ";
	$db->query($req_pos);
	$db->next_record();
	
	$pos_actuelle = $db->f("ppos_pos_cod");
	$position_x =  $db->f("pos_x");
	$position_y =  $db->f("pos_y");
	$position_etage =  $db->f("pos_etage");
	
	$req = "select lieu_dest from lieu,lieu_position
		where lpos_pos_cod = $pos_actuelle
			and lpos_lieu_cod = lieu_cod ";
	$db->query($req);
	$db->next_record();
	$n_pos = $db->f("lieu_dest");
	
	$req = "select pos_etage,pos_x,pos_y from positions where pos_cod = $n_pos ";
	$db->query($req);
	$db->next_record();
	$n_etage = $db->f("pos_etage");
	$n_x = $db->f("pos_x");
	$n_y = $db->f("pos_y");
	
	
	$req = "select update_etage_visite($perso_cod,$n_etage)";
	$db->query($req);
	
	
	$deplace = "update perso_position set ppos_pos_cod = $n_pos where ppos_perso_cod = $perso_cod ";
	$db->query($deplace);
	
	
	// on cherche le lieu cod
	$req = "select lpos_lieu_cod from lieu_position where lpos_pos_cod = $n_pos ";
	$db->query($req);
	$db->next_record();
	$lieu = $db->f("lpos_lieu_cod");

	// On rajoute un évènement
	$texte = 'Déplacement de '. $position_x .','. $position_y .','. $position_etage .' vers '. $n_x .','. $n_y .','. $n_etage;
	$req = "select insere_evenement($perso_cod, $perso_cod, 33, '$texte', 'O', null)";
	$db->query($req);
	
	// on active pour le retour
	$req = "select pge_perso_cod from perso_grand_escalier 
		where pge_perso_cod = $perso_cod	
			and pge_lieu_cod = $lieu ";
	$db->query($req);
	if ($db->nf() == 0)
	{
		$req = "insert into perso_grand_escalier (pge_perso_cod,pge_lieu_cod) values ($perso_cod,$lieu) ";
		$db->query($req);	
	}
	
	$req = "select etage_libelle,etage_description from etage,positions where pos_cod = $n_pos and pos_etage = etage_numero ";
	$db->query($req);
	$db->next_record();
	echo "<p>Vous arrivez dans le lieu : <strong>" . $db->f("etage_libelle") . "</strong></p><br>";
	echo "<p><em>" . $db->f("etage_description") . "</em></p>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
