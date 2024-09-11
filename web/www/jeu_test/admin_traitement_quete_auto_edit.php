<?php
// Un minimum de sécurité la page admin_traitement_quete_auto_edit.php pourrait être appelée en directe (sans vérification de compte)
$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();


// Préparation du log
$log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) ";

//==================================================================================================================
// Traitement de données en focntion de la methode
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case "terminer_quete":
        //récupérer les paramètres
        $quete = new aquete;
        $new   = true;
        if ($_REQUEST["aquete_cod"] * 1 != 0)
        {
            $quete->charge($_REQUEST["aquete_cod"]);
            $quete->termine();

            // Logger les infos pour suivi admin
            $log .= "La quête auto #" . $quete->aquete_cod . " a été mise à l'état terminée.\n" ;
            writelog($log, 'quete_auto');
            echo "<div class='bordiv'><pre>$log</pre></div>";

        }
        $_REQUEST['methode'] = 'edite_quete';        // => Après Terminsason retour à l'édition de la quete
        break;

    case "delete_quete":
        //récupérer les paramètres
        $quete = new aquete;
        $new   = true;
        if ($_REQUEST["aquete_cod"] * 1 != 0)
        {
            $quete->charge($_REQUEST["aquete_cod"]);
            $quete->supprime();

            // Logger les infos pour suivi admin
            $log .= "supprime la quête auto #" . $quete->aquete_cod . "\n" . obj_diff($quete, new aquete);
            writelog($log, 'quete_auto');
            echo "<div class='bordiv'><pre>$log</pre></div>";

        }

        unset($_REQUEST['methode']);        // => Après supression une nouvelle quete doit être edité
        unset($_REQUEST['aquete_cod']);
        $aquete_cod = 0;
        break;

    case "dupliquer_quete":
        //duplication
        if ($_REQUEST["aquete_cod"] * 1 != 0)
        {
            $aquete_map = [] ;        // recalibrage des quete de la nouvelle quête
            $aquete_etape_map = [] ;        // recalibrage des étapes de la nouvelle quête

            $aquete = new aquete();
            $aquete->charge($_REQUEST["aquete_cod"]);
            $aquete->aquete_nom_alias .= " (copie)";
            $aquete->stocke(true);
            $aquete_map[$_REQUEST["aquete_cod"]] = $aquete->aquete_cod;

            // dupliquer les étapes !
            $req  = "SELECT aqetape_cod from quetes.aquete_etape WHERE aqetape_aquete_cod = :aqetape_aquete_cod order by aqetape_cod; ";
            $stmt2 = $pdo->prepare($req);
            $stmt2 = $pdo->execute(array(":aqetape_aquete_cod" => $_REQUEST["aquete_cod"]), $stmt2);
            while ($result2 = $stmt2->fetch())
            {
                $etape = new aquete_etape();
                $etape->charge($result2["aqetape_cod"]);
                $etape->aqetape_aquete_cod =  $aquete->aquete_cod ;
                $etape->stocke( true );
                $aquete_etape_map[$result2["aqetape_cod"]] = $etape->aqetape_cod;

                // dupliquer les éléments de l'étape !
                $req  = "SELECT aqelem_cod from quetes.aquete_element WHERE aqelem_aquete_cod=:aqelem_aquete_cod and  aqelem_aqetape_cod = :aqelem_aqetape_cod and aqelem_aqperso_cod is null ";
                $stmt3 = $pdo->prepare($req);
                $stmt3 = $pdo->execute(array(":aqelem_aquete_cod" =>  $_REQUEST["aquete_cod"], ":aqelem_aqetape_cod" => $result2["aqetape_cod"]), $stmt3);
                while ($result3 = $stmt3->fetch())
                {
                    $element = new aquete_element();
                    $element->charge($result3["aqelem_cod"]);
                    $element->aqelem_aquete_cod =  $aquete->aquete_cod ;
                    $element->aqelem_aqetape_cod =  $etape->aqetape_cod ;
                    $element->stocke( true );
                }
            }

            // recalibrer la première étape de la quete
            $aquete->aquete_etape_cod = $aquete_etape_map[ $aquete->aquete_etape_cod ];
            $aquete->stocke();

            // recalibrer aussi tout le workflow d'étapes
            $req  = "SELECT aqetape_cod from quetes.aquete_etape WHERE aqetape_aquete_cod = :aqetape_aquete_cod; ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":aqetape_aquete_cod" =>  $aquete->aquete_cod), $stmt);
            while ($result = $stmt->fetch())
            {
                $etape = new aquete_etape();
                $etape->charge($result["aqetape_cod"]);
                $etape->aqetape_etape_cod = $aquete_etape_map[ $etape->aqetape_etape_cod ] ;
                $etape->stocke();
            }


            // recalibrer les éléments du type "etape", "quete", choix, etc....
            $req  = "SELECT aqelem_cod from quetes.aquete_element WHERE aqelem_aquete_cod = :aqelem_aquete_cod and aqelem_type in ('quete','choix','choix_etape','etape','quete_etape'); ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":aqelem_aquete_cod" =>  $aquete->aquete_cod), $stmt);
            while ($result = $stmt->fetch())
            {
                $element = new aquete_element();
                $element->charge($result["aqelem_cod"]);

                //===== quete
                if  ( $element->aqelem_type == 'quete' && $element->aqelem_misc_cod>0 && isset($aquete_map[$element->aqelem_misc_cod]))
                {
                    $element->aqelem_misc_cod = $aquete_map[$element->aqelem_misc_cod] ;
                }
                //===== quete_etape
                else if  ( $element->aqelem_type == 'quete_etape' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                {
                    $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                }
                //===== etape
                else if  ( $element->aqelem_type == 'etape' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                {
                    $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                }
                //===== choix
                else if  ( $element->aqelem_type == 'choix' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                {
                    $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                }
                //===== choix_etape
                else if  ( $element->aqelem_type == 'choix_etape' && $element->aqelem_misc_cod>0 && isset($aquete_etape_map[$element->aqelem_misc_cod]))
                {
                    $element->aqelem_misc_cod = $aquete_etape_map[$element->aqelem_misc_cod] ;
                }
                $element->stocke();     // sauver les modifications !
            }


            // Logger les infos pour suivi admin
            $log .= "La quête auto #" . $aquete->aquete_cod . " a été dupliquée\n" ;
            writelog($log, 'quete_auto');
            echo "<div class='bordiv'><pre>$log</pre></div>";

        }

        unset($_REQUEST['methode']);        // => Après supression une nouvelle quete doit être edité
        unset($_REQUEST['aquete_cod']);
        $aquete_cod = 0;
        break;

    case "sauve_quete":
        //récupérer les paramètres
        $quete = new aquete;
        $new   = true;
        if ($_REQUEST["aquete_cod"] * 1 != 0)
        {
            $new = false;
            $quete->charge($_REQUEST["aquete_cod"]);
        }
        $clone_q = clone $quete;

        $quete->aquete_nom_alias          = $_REQUEST["aquete_nom_alias"];
        $quete->aquete_nom                = $_REQUEST["aquete_nom"];
        $quete->aquete_description        = $_REQUEST["aquete_description"];
        $quete->aquete_actif              = $_REQUEST["aquete_actif"];
        $quete->aquete_journal_archive    = $_REQUEST["aquete_journal_archive"];
        $quete->aquete_date_debut         = $_REQUEST["aquete_date_debut"] == "" ? NULL : $_REQUEST["aquete_date_debut"];
        $quete->aquete_date_fin           = $_REQUEST["aquete_date_fin"] == "" ? NULL : $_REQUEST["aquete_date_fin"];
        $quete->aquete_nb_max_instance    = $_REQUEST["aquete_nb_max_instance"] == "" ? NULL : $_REQUEST["aquete_nb_max_instance"];
        $quete->aquete_nb_max_participant = $_REQUEST["aquete_nb_max_participant"] == "" ? NULL : $_REQUEST["aquete_nb_max_participant"];
        $quete->aquete_limite_triplette   = $_REQUEST["aquete_limite_triplette"] == "" ? 'N' : $_REQUEST["aquete_limite_triplette"];
        $quete->aquete_nb_max_rejouable   = $_REQUEST["aquete_nb_max_rejouable"] == "" ? NULL : $_REQUEST["aquete_nb_max_rejouable"];
        $quete->aquete_nb_max_quete       = $_REQUEST["aquete_nb_max_quete"] == "" ? NULL : $_REQUEST["aquete_nb_max_quete"];
        $quete->aquete_max_delai          = $_REQUEST["aquete_max_delai"] == "" ? NULL : $_REQUEST["aquete_max_delai"];

        /// interraction ou QA standard
        if (isset($_REQUEST["aquete_pos_etage"]) && (int)$_REQUEST["aquete_pos_etage"]>0) {
            $quete->aquete_interaction = 'O' ;
            $quete->aquete_pos_etage =  (int)$_REQUEST["aquete_pos_etage"];
        } else {
            $quete->aquete_interaction = 'N' ;
            $quete->aquete_pos_etage =  NULL;
        }

        $quete->stocke($new);
        $aquete_cod = $quete->aquete_cod;  // rerendre l'id (pour le cas de la création)

        // Logger les infos pour suivi admin
        $log .= "ajoute/modifie la quete auto #" . $quete->aquete_cod . "\n" . obj_diff($clone_q, $quete);
        writelog($log, 'quete_auto');
        echo "<div class='bordiv'><pre>$log</pre></div>";

        $_REQUEST['methode'] = 'edite_quete';        // => Après sauvegarde retour à l'édition de la quete
        break;


    case "duplique_etape":

        $etape = new aquete_etape;
        if ( $etape->charge($_REQUEST["aqetape_cod"]) )
        {
            $etape->duplique();

            // Logger les infos pour suivi admin
            $log .= "duplique l'étape #" . $etape->aqetape_cod . " de la quete auto #" . $etape->aqetape_aquete_cod . "\n" . obj_diff(new aquete_etape, $etape);

            writelog($log, 'quete_auto');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        }

        $_REQUEST['methode'] = 'edite_quete';        // => Après suppression retour à l'édition de la quete
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
        $new   = true;
        if ($_REQUEST["aqetape_cod"] * 1 != 0)
        {
            // il se peut qu'on arrive pas à charger l'étape si un autre l'a supprimé, dans ce cas on en sauvegarde une nouvelle
            if ( $etape->charge($_REQUEST["aqetape_cod"]) )
            {
                $new = false;
            }
        }
        $clone_e = clone $etape;     /// pour le log

        $etape->aqetape_nom             = $_REQUEST['aqetape_nom'];
        $etape->aqetape_aquete_cod      = $_REQUEST["aquete_cod"];
        $etape->aqetape_aqetapmodel_cod = $_REQUEST["aqetapmodel_cod"];
        $etape->aqetape_texte           = $_REQUEST['aqetape_texte'];
        $etape->aqetape_saut_etape_cod  = (int) $_REQUEST['aqetape_saut_etape_cod'];

        // Traitement de cas particulier (et/ou controle spécifique si besoin)
        if (($etape_modele->aqetapmodel_tag == "#CHOIX") && (strpos($etape->aqetape_texte, "[1]") === false))
        {
            // Cas particulier sur les étape choix, il faut obligatoirement un paramètre [1]
            $etape->aqetape_texte .= "[1]";
        } else if (($etape_modele->aqetapmodel_tag == "#START") && (strpos($etape->aqetape_texte, "[2]") === false))
        {
            // Cas particulier sur les étape démarrage, il faut obligatoirement un paramètre [2]
            $etape->aqetape_texte .= "[2]";
        } else if (($etape_modele->aqetapmodel_tag == "#ECHANGE #OBJET") && (strpos($etape->aqetape_texte, "[1]") === false))
        {
            // Cas particulier sur les étapes d'échange d'objet, un paramètre texte est utilisé en tant qu'entier numérique, on fait la conversion!
            if (is_array($_REQUEST['aqelem_type']))
                foreach ($_REQUEST['aqelem_type'] as $param_id => $types)
                    foreach ($types as $e => $type)
                        if ((!isset($_REQUEST["element_type"][$param_id])) || ($_REQUEST["element_type"][$param_id] == $type))
                            if (isset($_REQUEST["aqelem_param_num_1"][$param_id][$e]))
                                $_REQUEST["aqelem_param_txt_1"][$param_id][$e] =
                                    (int)$_REQUEST["aqelem_param_txt_1"][$param_id][$e];

        } else if ($etape_modele->aqetapmodel_tag == "#RECEVOIR #PX")
        {
            // c'est uen étape de gain , interdir les valeurs negatives
            $_REQUEST['aqelem_param_num_1'][1][0] = abs((int)$_REQUEST['aqelem_param_num_1'][1][0] ?? 0);
            if ($_REQUEST['aqelem_param_num_1'][1][0] > 200) $_REQUEST['aqelem_param_num_1'][1][0] = 200;
            $_REQUEST['aqelem_param_num_1'][2][0] = abs((int)$_REQUEST['aqelem_param_num_1'][2][0] ?? 0);
            if ($_REQUEST['aqelem_param_num_1'][2][0] > 100000) $_REQUEST['aqelem_param_num_1'][2][0] = 100000;
        }
        $etape->stocke($new);

        // AJouter un nom à l'étape avec le code si le nom n'a pas été fournie
        if ($etape->aqetape_nom == "")
        {
            $etape->aqetape_nom = "#" . $etape->aqetape_cod . ": " . $etape_modele->aqetapmodel_nom;
            $etape->stocke();
        }


        // Agencement entre les étapes (chemin par defaut)
        // Si c'est la première etape, il faut mettre à jour la quête sinon la dernière étape avant celle-ci
        $deniere_etape = $quete->get_derniere_etape();
        if ($etape_modele->aqetapmodel_tag == "#START" || $etape_modele->aqetapmodel_tag == "#START #INTERACTION")
        {
            // C'est la première etape, mettre à jour la quete
            $quete->aquete_etape_cod = $etape->aqetape_cod;
            $quete->stocke();
        } else if ($_REQUEST['etape_position'] == '' || $_REQUEST['etape_position'] == 0)
        {
            // A la fin de toutes les étapes
            $deniere_etape = $quete->get_derniere_etape();

            if (($deniere_etape->aqetape_cod != $etape->aqetape_cod) && ($etape->aqetape_etape_cod == ''))
            {
                // On vient juste d'ajouter une etape, il faut mettre à jour la précédente avec le N° de celle-ci
                $deniere_etape->aqetape_etape_cod = $etape->aqetape_cod;
                $deniere_etape->stocke();
            }
        } else if ($etape->aqetape_etape_cod == '')
        {
            // Insertion après l'étape souhaité
            $deniere_etape = new aquete_etape;
            if ($deniere_etape->charge($_REQUEST["etape_position"]))
            {
                $etape->aqetape_etape_cod = $deniere_etape->aqetape_etape_cod;
                $etape->stocke();
                $deniere_etape->aqetape_etape_cod = $etape->aqetape_cod;
                $deniere_etape->stocke();
            }
        }

        // Sauvegarde des elements créés pour l'étape
        $log_elements = ""; // pour loger la différence sur les éléments
        $element_list = array();        // Liste des élement de l'etape, pour supprimer ceux qui ne son tplus utilisés
        // Boucle sur les elements de l'etape à sauvegarder
        if (is_array($_REQUEST['aqelem_type']))
        {
            foreach ($_REQUEST['aqelem_type'] as $param_id => $types)
            {
                // chaque paramètres définir plusieurs élements
                foreach ($types as $e => $type)
                {
                    // Il y a certain element qui sont définit 2x en fonction du type, on ne garde qu'un seul type
                    if ((!isset($_REQUEST["element_type"][$param_id])) || ($_REQUEST["element_type"][$param_id] == $type))
                    {
                        $element    = new aquete_element;
                        $new        = true;
                        $aqelem_cod = 1 * (int)($_REQUEST["aqelem_cod"][$param_id][$e]);
                        if ($aqelem_cod != 0)
                        {
                            $new = false;
                            $element->charge($aqelem_cod);
                        }
                        $clone_elem = clone $element;

                        $element->aqelem_aquete_cod  = $quete->aquete_cod;
                        $element->aqelem_aqetape_cod = $etape->aqetape_cod;
                        $element->aqelem_param_id    = $param_id;
                        $element->aqelem_param_ordre = $e;
                        $element->aqelem_type        = $type;
                        $element->aqelem_misc_cod    = 1 * (int) $_REQUEST["aqelem_misc_cod"][$param_id][$e];
                        $element->aqelem_param_num_1 = isset($_REQUEST["aqelem_param_num_1"][$param_id][$e]) ? 1 * (int)$_REQUEST["aqelem_param_num_1"][$param_id][$e] : NULL;
                        $element->aqelem_param_num_2 = isset($_REQUEST["aqelem_param_num_2"][$param_id][$e]) ? 1 * (int)$_REQUEST['aqelem_param_num_2'][$param_id][$e] : NULL;
                        $element->aqelem_param_num_3 = isset($_REQUEST["aqelem_param_num_3"][$param_id][$e]) ? 1 * (int)$_REQUEST['aqelem_param_num_3'][$param_id][$e] : NULL;
                        $element->aqelem_param_txt_1 = $_REQUEST["aqelem_param_txt_1"][$param_id][$e];
                        $element->aqelem_param_txt_2 = $_REQUEST['aqelem_param_txt_2'][$param_id][$e];
                        $element->aqelem_param_txt_3 = $_REQUEST['aqelem_param_txt_3'][$param_id][$e];

                        //echo "<pre>"; print_r($element);echo "</pre>";
                        $element->stocke($new);
                        $element_list[] = $element->aqelem_cod;
                        $log_elements   .= obj_diff($clone_elem, $element, "Ajout/Modification element #" . $element->aqelem_cod . "\n");
                    }
                }
            }
        }

        $element = new aquete_element;
        if ($result =
            $element->clean($_REQUEST["aqetape_cod"], $element_list))        // supprimer tous les elements qui ne sont pas dans la liste.
        {
            // Logguer les supressions
            foreach ($result as $k => $e)
            {
                $log_elements .= "Suppression element #" . $e->aqelem_cod . "\n" . obj_diff($element, $e);
            }
        }

        // Logger les infos pour suivi admin
        $log .= "ajoute/modifie l'étape #" . $etape->aqetape_cod . " de la quete auto #" . $quete->aquete_cod . "\n" . obj_diff($clone_e, $etape) . $log_elements;
        writelog($log, 'quete_auto');
        echo "<div class='bordiv'><pre>$log</pre></div>";

        $aquete_cod          = $quete->aquete_cod;  // reprendre l'id (pour le cas de la création)
        $_REQUEST['methode'] = 'edite_quete';        // => Après sauvegarde d'une etape, retour à l'édition de la quete
        break;

    case "skip_etape":
        // forcer le passage d'une étape pour les perso qui sont en cours de réalisation.
        $etape = new aquete_etape;
        if ( $etape->charge($_REQUEST["aqetape_cod"]) )
        {
            $perso_liste = $etape->skip_perso_en_cours();

            // Logger les infos pour suivi admin
            $log .= "force le passage à l'étape suivante pour persos en cours sur l'étape #" . $etape->aqetape_cod . " de la quete auto #" . $etape->aqetape_aquete_cod . "\nListe des persos concernés: " . $perso_liste;

            writelog($log, 'quete_auto');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        }

        $_REQUEST['methode'] = 'edite_quete';        // => Après suppression retour à l'édition de la quete
        break;

    case "supprime_etape":

        $etape = new aquete_etape;
        $etape->supprime($_REQUEST["aqetape_cod"]);

        // Logger les infos pour suivi admin
        $log .= "supprime l'étape #" . $etape->aqetape_cod . " de la quete auto #" . $etape->aqetape_aquete_cod . "\n" . obj_diff(new aquete_etape, $etape);
        writelog($log, 'quete_auto');
        echo "<div class='bordiv'><pre>$log</pre></div>";

        $_REQUEST['methode'] = 'edite_quete';        // => Après suppression retour à l'édition de la quete
        break;

    case "edite_quete":
    case "edite_etape":
    case "ajoute_etape":
        // Rien à faire , la page de modification sera présentée en page pricipale
        break;

    case "deplace_etape":
        $_REQUEST['methode'] = 'edite_quete';        // => Après suppression retour à l'édition de la quete
        $etape1              = new aquete_etape;
        $etape2              = new aquete_etape;
        $etape3              = new aquete_etape;
        if ($_REQUEST["aqetape_cod"] * 1 != 0)
        {
            if ($_REQUEST["move"] == "down")
            {
                $etape2->charge($_REQUEST["aqetape_cod"] * 1);
                if ($etape2->aqetape_etape_cod > 0)
                {
                    $etape1->chargeBy_aqetape_etape_cod($etape2->aqetape_cod);
                    $etape3->charge($etape2->aqetape_etape_cod);

                    if ($etape3->aqetape_cod > 0 && $etape1->aqetape_cod)
                    {
                        $etape1->aqetape_etape_cod = $etape2->aqetape_etape_cod;
                        $etape2->aqetape_etape_cod = $etape3->aqetape_etape_cod;
                        $etape3->aqetape_etape_cod = $etape2->aqetape_cod;
                        $etape1->stocke();
                        $etape2->stocke();
                        $etape3->stocke();
                    }
                }
            } else if ($_REQUEST["move"] == "up")
            {
                $etape3->charge($_REQUEST["aqetape_cod"] * 1);
                if ($etape2->chargeBy_aqetape_etape_cod($etape3->aqetape_cod))
                {
                    if ($etape1->chargeBy_aqetape_etape_cod($etape2->aqetape_cod))
                    {
                        $etape1->aqetape_etape_cod = $etape2->aqetape_etape_cod;
                        $etape2->aqetape_etape_cod = $etape3->aqetape_etape_cod;
                        $etape3->aqetape_etape_cod = $etape2->aqetape_cod;
                        $etape1->stocke();
                        $etape2->stocke();
                        $etape3->stocke();
                    }
                }
            }
        }
        break;

    case 'perso_step':

        $quete_perso = new aquete_perso();
        $quete_perso->charge(1 * $_REQUEST["aqperso_cod"]);                           // la quete du perso
        if ($quete_perso->cut_perso_quete(1 * $_REQUEST["aqpersoj_cod"]))          // retour au step choisi
        {
            $log .= "La quête auto #" . $quete_perso->aqperso_aquete_cod . " pour le perso " . $quete_perso->aqperso_perso_cod . " a été tronquée au step #" . $quete_perso->aqperso_quete_step . ".";
            writelog($log, 'quete_auto');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        } else
        {
            echo "<div class='bordiv'><pre>Impossible de retourner à ce step</pre></div>";
        }

        break;

    default:
        echo 'Méthode inconnue: [', $methode, ']';
}
