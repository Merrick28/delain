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
	if ($tab_lieu['type_lieu'] != 3)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
	}
}

if ($erreur == 0)
{
	$nb_depose = 0;
	$req = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 86 ";
	$db->query($req);
	if ($db->nf() != 0)
	{
		$nb_depose = $nb_depose + 1;
	}
	$req = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 87 ";
	$db->query($req);
	if ($db->nf() != 0)
	{
		$nb_depose = $nb_depose + 1;
	}
	$req = "select qparm_cod from quete_params where qparm_quete_cod = 5 and qparm_gobj_cod = 88 ";
	$db->query($req);
	if ($db->nf() != 0)
	{
		$nb_depose = $nb_depose + 1;
	}

	if ($nb_depose == 3)
	{
		//
		$db->begin();
		$req = "delete from perso_objets where perobj_obj_cod in (select obj_cod from objets where obj_gobj_cod in (86,87,88)) ";
		$db->query($req);
		$req = "delete from objet_position where pobj_obj_cod in (select obj_cod from objets where obj_gobj_cod in (86,87,88)) ";
		$db->query($req);
		$req = "delete from perso_identifie_objet where pio_obj_cod in (select obj_cod from objets where obj_gobj_cod in (86,87,88)) ";
		$db->query($req);
		$req = "delete from objets where obj_gobj_cod in (86,87,88) ";
		$db->query($req);
		// on commence par ouvrir l'escalier
		$req = "update lieu set lieu_url = 'passage_escalier.php',lieu_dfin = (now() + '2 days'::interval) where lieu_cod in (184,186,187) ";
		$db->query($req);
		// on recrée les monstres qui vont bien
		$req = "select cree_monstre_hasard(58,1)"; 
		$db->query($req);
		$req = "select cree_monstre_hasard(59,3)"; 
		$db->query($req);
		$req = "select cree_monstre_hasard(60,2)"; 
		$db->query($req);
		// on enlève les médaillons
		$req = "delete from quete_params where qparm_quete_cod = 5 ";
		$db->query($req);
		// on donne quelques PX
		$req = "update perso set perso_px = perso_px + 20 where perso_cod = $perso_cod ";
		$db->query($req);
		echo "<p>Vous avez ouvert les escaliers vers le -5. Ceux ci resteront ouverts pendant 48 heures avant de se refermer.<br>";
		echo "Vous gagnez 20 PX pour cette action !<br><br>";
		$db->commit();
		
		
	}
	else
	{
		echo "<p>Erreur ! Les 3 médaillons ne sont pas sur l'escalier !";
	}
}	
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
