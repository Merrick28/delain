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
ob_start();

$req_concentration = "select concentration_nb_tours from concentrations where concentration_perso_cod = $perso_cod";
$db->query($req_concentration);
$nb_concentration = $db->nf();

if ($nb_concentration == 0)
{
	echo("<p>Vous n'avez effectué aucune concentration.</p>");
}
else
{
	$db->next_record();
	printf("<p>Vous êtes concentré(e) pendant %s tours.",$db->f("concentration_nb_tours"));
}
echo("<p><div align=\"center\"><a href=\"valide_concentration.php\">Se concentrer ! (4 PA)</a></div></p>");
if ($nb_concentration != 0)
{
	echo("<p><em>Attention !! Les concentrations ne se cumulent pas. Si vous vous concentrez de nouveau, la concentration précédente sera annulée !</em></p>");
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
