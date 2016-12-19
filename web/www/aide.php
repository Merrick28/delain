<?php 
include 'includes/template.inc';
$t = new template;
$t->set_file('FileRef','template/delain/index.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
//
// identification
//
ob_start();
include G_CHE . "ident.php";
$ident = montre_formulaire_connexion($verif_auth, ob_get_contents());
ob_end_clean();
$t->set_var("IDENT",$ident);

//
//Contenu de la div de droite
//
$contenu_page = '';
include "doc/aide_v2.php";
//$contenu_page = "test";
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
