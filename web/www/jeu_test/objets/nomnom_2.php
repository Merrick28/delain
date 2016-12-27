<?php 
include "../verif_connexion.php";
include '../../includes/template.inc';

$t = new template('..');
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);


// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$bd=new base_delain;
$req_cree_bouee = "select f_del_objet(14020693)";
$bd->query($req_cree_bouee);


// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");