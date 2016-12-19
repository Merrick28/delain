<?php 
include_once "verif_connexion.php";
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$req_ref_tran = "delete from transaction where tran_cod = $transaction";
$db->query($req_ref_tran);

?>
La transaction a été annulée !<br><br><a href="transactions2.php">Retour aux transactions</a>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
