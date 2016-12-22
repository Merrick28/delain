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
$db = new base_delain;
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
	$req = "update lieu ";
	$req = $req . "set lieu_url = 'escalier_d.php', lieu_description = 'Vous voyez un escalier vous permettant de descendre au niveau -4' ";
	$req = $req . "where lieu_cod in (48,49,50) ";
	$db->query($req);
		
	$req2 = "delete from perso_objets where perobj_obj_cod = 636 ";
	$db->query($req2);
	echo("<p>Vous entendez un bruit qui annonce que les accès au niveau -4 sont ouverts");
	echo("<p><a href=\"escalier_d.php\">Retour !</A>");
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
