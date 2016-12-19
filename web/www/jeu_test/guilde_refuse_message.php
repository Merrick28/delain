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
$req = "update guilde_perso set pguilde_message = 'N' where pguilde_perso_cod = $perso_cod ";
$db->query($req);
	?>
	<p>Les modifications sont enregistrées. Vous ne recevrez plus tous les messages de la guilde.

<p style="text-align:center;"><a href="guilde.php">Retour !</a>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
