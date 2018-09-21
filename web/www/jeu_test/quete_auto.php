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

switch ($methode) {

case 'start' :

    //On vérifie que le perso est bien en position pour démarrer cette quete.
    $quete = new aquete;
    $tab_quete = $quete->get_debut_quete($perso_cod);
    $aquete_cod = $_REQUEST["quete"] ;

    $trigger = -1 ;   //index de la quete démarrée dans la liste possible
    foreach ($tab_quete["quetes"] as $k => $quete)
    {
        if ($quete->aquete_cod == $aquete_cod)
        {
            $trigger = $k ;
            break;  // Inutile de chercher plus loin on a notre champion!
        }
    }

    if ($trigger == -1)
    {
        $contenu_page2 .= "Malheureusement vous n'est pas ou plus en mesure de démarrer cette quete!";
    }
    else
    {
        // charger le choix fait par l'aventurier pour la nouvelle instance (détermine la première etape)
        $tab_quete["triggers"][$trigger]["aqelem_cod"] = $_REQUEST["choix"];

        // Instanciation de la quete automatique.
        $quete = new aquete_perso() ;
        $result = $quete->nouvelle_instance($perso_cod, $aquete_cod, $tab_quete["triggers"][$trigger]);

        $contenu_page2 .= $result;
    }
break;

default:
    $contenu_page2 .= "Liste des quêtes en cours!<br>";
break;
}

// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');

$t->set_var('CONTENU_COLONNE_DROITE',$contenu_page2);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
