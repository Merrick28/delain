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
if ($quantite <= 0)
{
	echo("La somme que vous voulez mettre au sol n'est pas valide !)");
}
else
{
	$req_depose = "select depose_or($perso_cod,$quantite) as depose";
	$db->query($req_depose);
	$db->next_record();
		if ($db->f("depose") == 0)
		{
			echo("<p>Vous avez déposé avec succès $quantite brouzoufs au sol.");
		}
		else
		{
			printf("<p>Une erreur est survenue : %s",$db->f("depose"));
		}
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
