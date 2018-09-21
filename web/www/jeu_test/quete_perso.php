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
$contenu_page2 = '';
define("APPEL",1);
$erreur = 0;
//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
if ($perso->is_perso_quete())
{
	$erreur = 0;
}
else
{
	$erreur = 1;
	$contenu_page2 .= 'Vous n’avez pas accès à cette page !';
}
if (!isset($methode))
{
	$methode = 'debut';
}
if ($erreur == 0)
{
    // Page de démarrage des quetes autos
    $quete = new aquete;
    $tab_quete = $quete->get_debut_quete($perso_cod);
    foreach ($tab_quete["quetes"] as $k => $quete)
    {

        $etape = new aquete_etape();
        $etape->charge($quete->aquete_etape_cod);

        $contenu_page2 .= "<!-- début $key => $quete->aquete_etape_cod -->";
        $contenu_page2 .= "<b>{$quete->aquete_nom}</b><br><br>";
        $contenu_page2 .= $etape->get_initial_texte($tab_quete["triggers"][$k]["nom"]);
        $contenu_page2 .= "<br><br><hr><!-- fin $key => $val -->";

    }

    // Les quêtes standards
    $is_perso_quete = $db->is_perso_quete($perso_cod);
    if ($is_perso_quete)
    {
        $type_appel = 2;
        $tab_quete = $db->get_perso_quete($perso_cod);
        foreach($tab_quete as $key=>$val)
        {
            $contenu_page2 .= "<!-- début $key => $val -->";
            ob_start();
            require_once $val;
            $contenu_page2 .= ob_get_contents();
            ob_end_clean();
            $contenu_page2 .= "<!-- fin $key => $val -->";
        }
    }
}
// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');

$t->set_var('CONTENU_COLONNE_DROITE',$contenu_page2);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
