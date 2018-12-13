<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',IMG_PATH);
// on va maintenant charger toutes les variables li�es au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();

$req = "select dcompt_etage from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	die("Erreur sur les �tages possibles !");
}
else
{
	$db->next_record();
	$droit['etage'] = $db->f("dcompt_etage");
}
if ($droit['etage'] == 'A')
{
	$restrict = '';
	$restrict2 = '';
}
else
{
	$restrict = 'where etage_numero in (' . $droit['etage'] . ') ';
	$restrict2 = 'and pos_etage in (' . $droit['etage'] . ') ';
}

$req = "select etage_libelle,etage_numero,etage_reference from etage " . $restrict . "order by etage_reference desc, etage_numero asc";
$db->query($req);
echo("<p>");
while($db->next_record())
{
	$bold = ($db->f("etage_numero") == $db->f("etage_reference"));
	echo ($bold?'<p /><strong>':'')."<a href=\"tab_vue_total.php?num_etage=" . $db->f("etage_numero") . '">' . $db->f("etage_libelle") . "</a>".($bold?'</strong>':'')."<br />";
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');
