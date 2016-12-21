<?php // on définit une variable pour savoir que c'est cette page qui appelle les "sous pages"
DEFINE('APPEL_VUE',1);
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/vue_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

if (isset($contenu_page) && $contenu_page != '')
{
	$contenu_page = '<tr><td colspan="2"><div id="vue_resultat" class="bordiv">' . nl2br($contenu_page) . '</div></td></tr>';
	$t->set_var('VUE_RESULTAT', $contenu_page);
}
else
{
	$t->set_var('VUE_RESULTAT', '');
}

if(!isset($t_frdr) || $t_frdr === '')
    $t_frdr = 0;

include('vue_gauche.php');
//$vue_gauche = '';
$t->set_var('VUE_GAUCHE',$vue_gauche);

include('fr_dr.php');
//$vue_droite = '';
$t->set_var('VUE_DROITE',$vue_droite);

include('tableau_vue3.php');
//$vue_bas = '';
$t->set_var('VUE_BAS',$vue_bas);
// affichage de la page
$t->parse('Sortie','FileRef');
$t->p('Sortie');
