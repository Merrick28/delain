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
if ($db->is_admin($compt_cod))
{
	$req = "select compt_nom from compte where compt_cod = $compte ";
	$db->query($req);
	$db->next_record();
	echo "<p>Voulez vous vraiment invalider le compte <b>" . $db->f("compt_nom") . "</b>? (Cette action est définitive, elle a comme effet de transformer tous les persos en monstres ";
	echo "et d'empêcher le login du fautif).";
	echo "<p><a href=\"valide_invalide_compte.php?compte=$compte\">OUI ! </a>";
	echo "<p><a href=\"detail_compte.php\">NON !</a>";
}
else
{
	echo "<p>Erreur ! Vous n'êtes pas administrateur !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');