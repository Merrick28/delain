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
ob_start();

$iterations = 50;
echo '<table><tr><td>';
{
	echo '<h3>Début test ancienne méthode</h3>';
	$debut = time();
	for ($compteurBoucle = 0; $compteurBoucle < $iterations; $compteurBoucle++)
	{
		$db->query("select vue_perso6(perso_cod) from perso where perso_cod = 910");
	}
	$secondes = time() - $debut;
	echo "$iterations itérations réalisées en $secondes secondes<br />";

echo '</td><td>';
	echo '<h3>Début test nouvelle méthode</h3>';
	$debut = time();
	for ($compteurBoucle = 0; $compteurBoucle < $iterations; $compteurBoucle++)
	{
		$db->query("select vue_perso_6_optim(perso_cod) from perso where perso_cod = 910");
	}
	$secondes = time() - $debut;
	echo "$iterations itérations réalisées en $secondes secondes<br />";
}
echo '</td></tr></table>';

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
