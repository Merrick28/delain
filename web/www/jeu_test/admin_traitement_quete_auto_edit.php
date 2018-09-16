<?php
// Un minimum de sécurité la page admin_traitement_quete_auto_edit.php pourrait être appelée en directe (sans vérification de compte)
if(!defined("APPEL")) die("Erreur d’appel de page !");

switch ($methode)
{
case "update_quete":
    //récupérer les paramètres
    $quete = new aquete;
    $new = true ;
    if ( $_REQUEST["aquete_cod"]*1!=0 ) {
        $new = false ;
        $quete->charge($_REQUEST["aquete_cod"]);
    }

    $quete->aquete_nom = $_REQUEST["aquete_nom"];
    $quete->aquete_description = $_REQUEST["aquete_description"];
    $quete->aquete_actif = $_REQUEST["aquete_actif"];
    $quete->aquete_date_debut = $_REQUEST["aquete_date_debut"] == "" ? NULL : $_REQUEST["aquete_date_debut"];
    $quete->aquete_date_fin = $_REQUEST["aquete_date_fin"] == "" ? NULL : $_REQUEST["aquete_date_fin"];
    $quete->aquete_nb_max_instance = $_REQUEST["aquete_nb_max_instance"] == "" ? NULL : $_REQUEST["aquete_nb_max_instance"];
    $quete->aquete_nb_max_participant = $_REQUEST["aquete_nb_max_participant"] == "" ? NULL : $_REQUEST["aquete_nb_max_participant"];
    $quete->aquete_nb_max_rejouable = $_REQUEST["aquete_nb_max_rejouable"] == "" ? NULL : $_REQUEST["aquete_nb_max_rejouable"];
    $quete->aquete_nb_max_quete = $_REQUEST["aquete_nb_max_quete"] == "" ? NULL : $_REQUEST["aquete_nb_max_quete"];

    $quete->stocke($new);
    $aquete_cod = $quete->aquete_cod ;  // rerendre l'id (pour le cas de la création)

break;

case "update_etape":
    //récupérer les paramètres

    $quete = new aquete;                                // la quete de référence
    $quete->charge($_REQUEST["aquete_cod"]);

    $etape_template = new aquete_etape_template;       // etpape basée sur ce template
    $etape_template->charge($_REQUEST["aqetaptemp_cod"]);

    // récupérer les paramètres de l'étape pour mise à jour
    $etape = new aquete_etape;
    $new = true ;
    if ( $_REQUEST["aqetape_cod"]*1!=0 ) {
        $new = false ;
        $etape->charge($_REQUEST["aqetape_cod"]);
    }
    $etape->aqetape_nom = $_REQUEST['aqetape_nom'];
    $etape->aqetape_aquete_cod = $_REQUEST["aquete_cod"];
    $etape->aqetape_aqetaptemp_cod = $_REQUEST["aqetaptemp_cod"];
    $etape->aqetape_texte = $_REQUEST['aqetape_texte'];
    $etape->stocke($new);

    $aquete_cod = $quete->aquete_cod ;  // rerendre l'id (pour le cas de la création)

break;

default:
    echo 'Méthode inconnue: [' , $methode , ']';
}
