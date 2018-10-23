<?php
// Un minimum de sécurité la page admin_traitement_quete_auto_edit.php pourrait être appelée en directe (sans vérification de compte)
if(!defined("APPEL")) die("Erreur d’appel de page !");

switch ($methode)
{
case "sauve_quete":
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
    $quete->aquete_max_delai = $_REQUEST["aquete_max_delai"] == "" ? NULL : $_REQUEST["aquete_max_delai"];

    $quete->stocke($new);
    $aquete_cod = $quete->aquete_cod ;  // rerendre l'id (pour le cas de la création)

    echo "<font color='blue'>LOG => sauve_quete</font><br><hr>";
    $_REQUEST['methode'] = 'edite_quete';        // => Après sauvegarde retour à l'édition de la quete
break;

case "sauve_etape":
    //récupérer les paramètres
    //echo "<pre>"; print_r($_REQUEST);echo "</pre>"; die();

    $quete = new aquete;                                // la quete de référence
    $quete->charge($_REQUEST["aquete_cod"]);

    $etape_modele = new aquete_etape_modele;       // etpape basée sur ce template
    $etape_modele->charge($_REQUEST["aqetapmodel_cod"]);

    // récupérer les paramètres de l'étape pour mise à jour
    $etape = new aquete_etape;
    $new = true ;
    if ( $_REQUEST["aqetape_cod"]*1!=0 ) {
        $new = false ;
        $etape->charge($_REQUEST["aqetape_cod"]);
    }
    $etape->aqetape_nom = $_REQUEST['aqetape_nom'];
    $etape->aqetape_aquete_cod = $_REQUEST["aquete_cod"];
    $etape->aqetape_aqetapmodel_cod = $_REQUEST["aqetapmodel_cod"];
    $etape->aqetape_texte = $_REQUEST['aqetape_texte'];

    // Cas particulier sur les étape choix, il faut obligatoirement un paramètre [1]
    if (($etape_modele->aqetapmodel_tag == "#CHOIX")&& (strpos($etape->aqetape_texte, "[1]")===false))
    {
            $etape->aqetape_texte.= "[1]";
    }
    else if (($etape_modele->aqetapmodel_tag == "#START")&& (strpos($etape->aqetape_texte, "[2]")===false))
    {
            $etape->aqetape_texte.= "[2]";
    }
    $etape->stocke($new);

    // AJouter un nom à l'étape avec le code si le nom n'a pas été fournie
    if ($etape->aqetape_nom=="")
    {
        $etape->aqetape_nom =  "#" . $etape->aqetape_cod . ": " . $etape_modele->aqetapmodel_nom ;
        $etape->stocke();
    }


    // Agencement entre les étapes (chemin par defaut)
    // Si c'est la première etape, il faut mettre à jour la quête sinon la dernière étape avant celle-ci
    $deniere_etape = $quete->get_derniere_etape();
    if ($etape_modele->aqetapmodel_tag == "#START")
    {
        // C'est la première etape, mettre à jour la quete
        $quete->aquete_etape_cod = $etape->aqetape_cod ;
        $quete->stocke();
    }
    else
    {
        $deniere_etape = $quete->get_derniere_etape();

        if (($deniere_etape->aqetape_cod != $etape->aqetape_cod ) && ($etape->aqetape_etape_cod == ''))
    {
            // On vient juste d'ajouter une etape, il faut mettre à jour la précédente avec le N° de celle-ci
            $deniere_etape->aqetape_etape_cod = $etape->aqetape_cod ;
            $deniere_etape->stocke();
        }
    }

    // Sauvegarde des elements créés pour l'étape
    $element_list = array();        // Liste des élement de l'etape, pour supprimer ceux qui ne son tplus utilisés
    // Boucle sur les elements de l'etape à sauvegarder
    foreach ($_REQUEST['aqelem_type'] as $param_id => $types)
    {
        // chaque paramètres définir plusieurs élements
        foreach ($types as $e => $type)
        {
            // Il y a certain element qui sont définit 2x en fonction du type, on ne garde qu'un seul type
            if ((!isset($_REQUEST["element_type"][$param_id])) || ($_REQUEST["element_type"][$param_id]==$type))
            {
                $element = new aquete_element;
                $new = true ;
                $aqelem_cod = 1*( $_REQUEST["aqelem_cod"][$param_id][$e] ) ;
                if ( $aqelem_cod != 0 ) {
                    $new = false ;
                    $element->charge($aqelem_cod);
                }

                $element->aqelem_aquete_cod = $quete->aquete_cod ;
                $element->aqelem_aqetape_cod = $etape->aqetape_cod ;
                $element->aqelem_param_id = $param_id  ;
                $element->aqelem_param_ordre = $e  ;
                $element->aqelem_type = $type ;
                $element->aqelem_misc_cod = 1*$_REQUEST["aqelem_misc_cod"][$param_id][$e];
                $element->aqelem_param_num_1 = isset($_REQUEST["aqelem_param_num_1"][$param_id][$e]) ? 1*$_REQUEST["aqelem_param_num_1"][$param_id][$e] : NULL ;
                $element->aqelem_param_num_2 = isset($_REQUEST["aqelem_param_num_2"][$param_id][$e]) ? 1*$_REQUEST['aqelem_param_num_2'][$param_id][$e] : NULL ;
                $element->aqelem_param_num_3 = isset($_REQUEST["aqelem_param_num_3"][$param_id][$e]) ? 1*$_REQUEST['aqelem_param_num_3'][$param_id][$e] : NULL ;
                $element->aqelem_param_txt_1 = $_REQUEST["aqelem_param_txt_1"][$param_id][$e];
                $element->aqelem_param_txt_2 = $_REQUEST['aqelem_param_txt_2'][$param_id][$e];
                $element->aqelem_param_txt_3 = $_REQUEST['aqelem_param_txt_3'][$param_id][$e];

                //echo "<pre>"; print_r($element);echo "</pre>";
                $element->stocke($new);
                $element_list[] = $element->aqelem_cod ;
            }
        }
    }

    $element = new aquete_element;
    $element->clean( $_REQUEST["aqetape_cod"], $element_list);        // supprimer tous les elements qui ne sont pas dans la liste.

    echo "<font color='blue'>LOG => sauve_etape</font><br><hr>";

    $aquete_cod = $quete->aquete_cod ;  // rerendre l'id (pour le cas de la création)
    $_REQUEST['methode'] = 'edite_quete';        // => Après sauvegarde d'une etape, retour à l'édition de la quete
break;


case "supprime_etape":

    $etape = new aquete_etape;
    $etape->supprime($_REQUEST["aqetape_cod"]);

    echo "<font color='blue'>LOG => supprime_etape</font><br><hr>";

    $_REQUEST['methode'] = 'edite_quete';        // => Après suppression retour à l'édition de la quete
break;

case "edite_quete":
case "edite_etape":
case "ajoute_etape":
    // Rien à faire , la page de modification sera présentée en page pricipale
    break;

case "deplace_etape":
    $_REQUEST['methode'] = 'edite_quete';        // => Après suppression retour à l'édition de la quete
    $etape1 = new aquete_etape;
    $etape2 = new aquete_etape;
    $etape3 = new aquete_etape;
    if ( $_REQUEST["aqetape_cod"]*1!=0 )
    {
        if ($_REQUEST["move"]=="down")
        {
            $etape2->charge($_REQUEST["aqetape_cod"]*1 );
            if ( $etape2->aqetape_etape_cod>0 )
            {
                $etape1->chargeBy_aqetape_etape_cod($etape2->aqetape_cod);
                $etape3->charge($etape2->aqetape_etape_cod);

                if ($etape3->aqetape_cod>0 && $etape1->aqetape_cod)
                {
                    $etape1->aqetape_etape_cod = $etape2->aqetape_etape_cod ;
                    $etape2->aqetape_etape_cod = $etape3->aqetape_etape_cod ;
                    $etape3->aqetape_etape_cod = $etape2->aqetape_cod ;
                    $etape1->stocke();
                    $etape2->stocke();
                    $etape3->stocke();
                }
            }
        }
        else if ($_REQUEST["move"]=="up")
        {
            $etape3->charge($_REQUEST["aqetape_cod"]*1);
            if ($etape2->chargeBy_aqetape_etape_cod($etape3->aqetape_cod))
            {
                if ($etape1->chargeBy_aqetape_etape_cod($etape2->aqetape_cod))
                {
                    $etape1->aqetape_etape_cod = $etape2->aqetape_etape_cod ;
                    $etape2->aqetape_etape_cod = $etape3->aqetape_etape_cod ;
                    $etape3->aqetape_etape_cod = $etape2->aqetape_cod ;
                    $etape1->stocke();
                    $etape2->stocke();
                    $etape3->stocke();
                }
            }
        }
    }
    break;
default:
    echo 'Méthode inconnue: [' , $methode , ']';
}
