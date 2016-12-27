<?php 
include "../verif_connexion.php";
include '../../includes/template.inc';

$t = new template('..');
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$bd = new base_delain;

$req_matos = "select perobj_obj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 665 order by perobj_obj_cod";
$bd->query($req_matos);
if(!($bd->next_record()))
{
  // PAS D'OBJET.
    $contenu_page .= "<p>Vous ne portez pas le crâne d'orizante!'</p>";
}
else
{
$erreur = 0;
    $req = 'select obj_nom, obj_gobj_cod,  obj_cod, obj_poids, obj_description, trouve_objet(obj_cod) as trouve from objets where obj_gobj_cod between 665 and 687  and obj_cod not between 7994364 and 7994807 order by obj_cod';
    $bd->query($req);
    $contenu_page .= '<table border = 1><tr><th>Nom</th><th>Type</th><th>Numéro</th><th>Poids</th><th>Position</th><th>Description</th></tr>';
    while ( $bd->next_record())
    {
        $contenu_page .= '<tr><td>' . $bd->f('obj_nom') . '</td><td>' . $bd->f('obj_gobj_cod') . '</td><td>' . $bd->f('obj_cod') . '</td><td>' . $bd->f('obj_poids') . '</td><td>' . $bd->f('trouve') . '</td><td>' . $bd->f('obj_description') . '</td></tr>';
    }
    $contenu_page .= '</table>';
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");