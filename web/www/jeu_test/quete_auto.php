<?php
include_once "verif_connexion.php";
include_once '../includes/template.inc';
include_once '../includes/tools.php';
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
        $result = $quete->demarre_quete($perso_cod, $aquete_cod, $tab_quete["triggers"][$trigger]);

        if ($result!="")
        {
            // Erreur, impossible de démarrer la quete
            $contenu_page2 .= $result;
        }
        else
        {
            // la quete a bien été instanciée
            $methode = "" ;     // => Pour réaliser la suite dans la liste de mes quetes en cours !!!!
        }

    }
break;

case 'stop' :

    //On vérifie que le perso à bien démarrer la quete et on lui propose d'arrêter
    $quete = new aquete;
    $aquete_cod = $_REQUEST["quete"] ;
    $quete->charge($aquete_cod);

    $quete_perso = new aquete_perso() ;
    if ($quete_perso->get_perso_quete($perso_cod, $quete->aquete_cod))
    {
        $contenu_page2 .= "Vous êtes sur le point d'arrêter la quête <b>{$quete->aquete_nom}</b>, commencée le <b>".date("d/m/Y à H:i:s", strtotime($quete_perso->aqperso_date_debut))."</b> !<br>";
        if ($quete->aquete_nb_max_rejouable*1>0) $contenu_page2 .= "Cette quête ne peut être réalisée que <b>{$quete->aquete_nb_max_rejouable} fois</b> par aventurier, vous l'avez déjà réalisé <b>{$quete_perso->aqperso_nb_realisation} fois</b>?<br>";
        $contenu_page2 .= "Aussi, il n'est pas certain que vous puissiez la recommencer!<br>";
        $contenu_page2 .= "<b>Etes-vous sûr(e) de vouloir arrêter?</b>";
        $link = "/jeu_test/quete_auto.php?methode=stop2&quete={$aquete_cod}&choix=".$_REQUEST["choix"] ;
        $contenu_page2 .= '<br><br><a href="'.$link.'" style="margin:50px;">Oui, je veux vraiment arrêter là!</a>';
        $link = "/jeu_test/quete_auto.php?quete={$aquete_cod}" ;
        $contenu_page2 .= '<br><br><a href="'.$link.'" style="margin:50px;">NON!! Je continue la quête!</a>';

        $contenu_page2 .= "<br><br>";
    }
    break;

case 'stop2' :

    //On vérifie que le perso a bien démarrer la quete et l'arrête
    $quete = new aquete;
    $quete->charge($_REQUEST["quete"]);

    $quete_perso = new aquete_perso() ;
    if ($quete_perso->get_perso_quete($perso_cod, $quete->aquete_cod))
    {
        $quete_perso->aqperso_actif = 'N';
        $quete_perso->aqperso_date_fin = date('Y-m-d H:i:s');
        $quete_perso->stocke();

        $contenu_page2 .= "La quête <b>{$quete->aquete_nom}</b>c'est termniné' par un abandon!";
        $contenu_page2 .= "<br><br>";
    }
break;

case 'choix' :

    $quete_perso = new aquete_perso() ;
    $quete_perso->set_choix_aventurier($perso_cod, 1*$_REQUEST["quete"], 1*$_REQUEST["choix"]);
    $methode = "" ;     // => Pour réaliser la suite (run) dans la liste de mes quetes en cours !!!!

break;
}

if ($methode=="")
{
    $contenu_page2 .= "<div class=\"titre\" style=\"padding:5px;\"><center><b>Vos quêtes en cours</b></center></div><br>";

    $quete_perso = new aquete_perso() ;
    $quetes_perso = $quete_perso->get_perso_quete_en_cours($perso_cod);

    // Récupération des Quetes en cours
    if (!$quetes_perso)
    {
        // Affichage de la boite de selection
        $contenu_page2 .= "<br><br>Vous n'avez pas encore commencé de quête!!!!<br><br><br><br>" ;
    }
    else
    {
        $quete_id_list = "";        // S'il n'y a rien de commencé!
        $recherche_quete = array();     // recherche inversé
        foreach ($quetes_perso as $k => $q)
        {
            $recherche_quete[(1*$q->aqperso_aquete_cod)] = (1*$q->aqperso_cod) ;
            $quete_id_list.=(1*$q->aqperso_aquete_cod).",";
        }
        $quete_id_list=substr($quete_id_list, 0,-1);

        if($aquete_cod*1==0) $aquete_cod = 1*($quetes_perso[0]->aqperso_aquete_cod);      // Si on a pas choisi une quete particulière, prendre la première

        // Affichage de la boite de selection
        $contenu_page2 .= '<form method="post">';
        $contenu_page2 .= "Sélectionner une de vos quêtes : ". create_selectbox_from_req("aquete_cod", "SELECT aquete_cod, aquete_nom FROM quetes.aquete where aquete_cod in ({$quete_id_list}) order by aquete_nom", $aquete_cod, array('style'=>'onchange="this.parentNode.submit();"'));
        $contenu_page2 .= '</form>';
        $contenu_page2 .= "<br>";

        $quete = new aquete() ;
        $quete->charge($aquete_cod);
        $quete_perso->charge($recherche_quete[$aquete_cod]);

        //** C'est ici que l'on vérifie l'avancement de la quete, (nota il faut que l'on y passe obligatoirement au démarrage après la première étape) **//
        $quete_perso->run();

        $contenu_page2 .= "<div class=\"titre\" style=\"padding:5px;\"><center><b>{$quete->aquete_nom}</b></center></div>" ;
        $contenu_page2 .= "<br><u>Description de la quête</u> : ".$quete->aquete_description ."<br><br><hr><br>" ;

        $contenu_page2 .= $quete_perso->journal();      // Texte avec l'historique de la quete jusqu'a l'étape en cours.

        //** Le texte d'étape courante par exemple un choix (peut être vide si on attend un état spécifique)  **//
        $contenu_page2 .= $quete_perso->get_texte_etape_courante();

        $contenu_page2 .= "<br><br>";
    }



    //echo "<pre>"; print_r($quetes); echo "</pre>";
}


// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');

$t->set_var('CONTENU_COLONNE_DROITE',$contenu_page2);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
