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
	if (!isset($compte))
	{
		$req = "select compt_cod,compt_nom,compt_mail,to_char(compt_dcreat,'DD/MM/YYY hh24:mi:ss') as creation,to_char(compt_der_connex,'DD/MM/YYYY hh24:mi:ss') as connex,compt_ip,compt_commentaire from compte ";
		$req = $req . "where compt_cod in (select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod) ";
		$db->query($req);
		$db->next_record();
		$compte = $db->f("compt_cod");
	}
	

	$req = "update perso set perso_type_perso = 2 where perso_cod in ";
	$req = $req . "(select pcompt_perso_cod from perso_compte where pcompt_compt_cod = $compte) ";
	$db->query($req);
	
	$req = "delete from perso_compte where pcompt_compt_cod = $compte ";
	$db->query($req);
	
	$req = "update compte set compt_password = 'jkdhfqldshfqldkh', compt_validation = 1231, compt_actif = 'N' where compt_cod = $compte ";
	$db->query($req);
	echo "<p>Le compte a été désactivé.";
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
?>
