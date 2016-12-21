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
$req_acc_tran = "select accepte_transaction($transaction) as resultat";
$db = new base_delain;
$db->query($req_acc_tran);
$db->next_record();
$resultat_temp = $db->f("resultat");
$tab_res = explode(";",$resultat_temp);
if ($tab_res[0] == -1)
{
	$contenu_page  = '<p>Une erreur est survenue : ' . $tab_res[1];
}
else
{
	$contenu_page  = '<p>La transaction a été validée. L\'objet de trouve maintenant dans votre inventaire.';
}
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");