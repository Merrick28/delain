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
	if ($db->compte_objet($perso_cod,$objet) != 0)
	{
		// on enlève de l'inventaire
		$req = "delete from perso_objets where perobj_perso_cod = $perso_cod and perobj_obj_cod in (select obj_cod from objets where obj_gobj_cod =  $objet) ";
		$db->query($req);
		// on détruit l'objet
		$req = "delete from perso_identifie_objet where pio_obj_cod in (select obj_cod from objets where obj_gobj_cod =  $objet) ";
		$db->query($req);	
		$req = "delete from objets where obj_cod in (select obj_cod from objets where obj_gobj_cod =  $objet) ";
		$db->query($req);	
		// on la rajoute à la quête
		$req = "insert into quete_params (qparm_quete_cod,qparm_gobj_cod) values (5,$objet) ";
		$db->query($req);
		echo "<p>Vous avez déposé le médaillon à sa place. Il est impossible de l'en retirer maintenant.";
		
		
		
	}
	else
	{
		echo "<p>Erreur ! L'objet n'est pas dans voter inventaire !";
	}

}	
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
