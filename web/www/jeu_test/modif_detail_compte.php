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
	echo "<p class=\"titre\">Ajouter un commentaire sur ce compte</p>";
	echo "<form name=\"comment\" action=\"valide_modif_detail_compte.php\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"methode\" value=\"comment\">";
	echo "<input type=\"hidden\" name=\"compte\" value=\"$compte\">";
	echo "<p>Entrez votre commentaire ci dessous : (la date, heure et auteur du message seront rajoutés automatiquement)<br>";
	echo "<textarea name=\"comment\" cols=\"50\" rows=\"20\"></textarea><br>";
	echo "<center><input type=\"submit\" class=\"test\" value=\"Entrer !\"></center>";
	echo "</form>";


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
