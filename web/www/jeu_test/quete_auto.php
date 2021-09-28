<?php
include "blocks/_header_page_jeu.php";
include_once '../includes/tools.php';

$methode          = get_request_var('methode', '');
define("APPEL", 1);

// Détection des droits admin, pour ajouter de l'info suplémentaire dans l'écran
$isAdminAnimation = false;
$pdo = new bddpdo;

$droit_modif = 'dcompt_animations';
include "blocks/_test_droit_modif_generique.php";
if ($erreur == 0)
{
    $isAdminAnimation = true;
}


switch ($methode)
{

    case 'start' :

        //On vérifie que le perso est bien en position pour démarrer cette quete.
        $quete = new aquete;
        $tab_quete = $quete->get_debut_quete($perso_cod);
        $aquete_cod = $_REQUEST["quete"];


        //echo "<pre>"; print_r($_REQUEST);echo "</pre>";
        //echo "<pre>"; print_r($tab_quete);echo "</pre>";
        //die();


        $trigger = -1;   //index de la quete démarrée dans la liste possible
        foreach ($tab_quete["quetes"] as $k => $quete)
        {
            if ($quete->aquete_cod == $aquete_cod  && $tab_quete["triggers"][$k]["aqelem_cod"] == $_REQUEST["trigger"])
            {
                $trigger = $k;
                break;  // Inutile de chercher plus loin on a notre champion!
            }
        }

        if ($trigger == -1)
        {
            $contenu_page .= "Malheureusement vous n'est pas ou plus en mesure de démarrer cette quete!";
        } else
        {

            // charger le choix fait par l'aventurier pour la nouvelle instance (détermine la première etape)
            $tab_quete["triggers"][$trigger]["aqelem_cod"] = $_REQUEST["choix"];

            // Instanciation de la quete automatique.
            $quete_perso = new aquete_perso();
            $result = $quete_perso->demarre_quete($perso_cod, $aquete_cod, $tab_quete["triggers"][$trigger]);

            if ($result != "")
            {
                // Erreur, impossible de démarrer la quete
                $contenu_page .= $result;
            } else
            {
                // la quete a bien été instanciée
                $methode = "";     // => Pour réaliser la suite dans la liste de mes quetes en cours !!!!
            }

        }
        break;

    case 'interaction' :

        //On vérifie que le perso est bien en position pour démarrer cette quete.
        $quete = new aquete;
        $tab_quete = $quete->get_debut_quete($perso_cod);
        $aquete_cod = $_REQUEST["quete"];

        // dans le cas d'une interaction, il n'y a pas de choix, on ouvre directement sur la première etape,
        $trigger = -1;   //index de la quete démarrée dans la liste possible
        foreach ($tab_quete["quetes"] as $k => $quete)
        {
            if ($quete->aquete_cod == $aquete_cod && $quete->aquete_interaction == 'O')
            {
                $trigger = $k;
                break;  // Inutile de chercher plus loin on a notre champion!
            }
        }

        if ($trigger == -1)
        {
            $contenu_page .= "Malheureusement vous n'est pas ou plus en mesure d'interagir avec ça!";
        } else
        {
            // Instanciation de la quete automatique.
            $quete_perso = new aquete_perso();
            $result = $quete_perso->demarre_interaction($perso_cod, $aquete_cod,  $tab_quete["triggers"][$trigger]);

            if ($result != "")
            {
                // Erreur, impossible de démarrer la quete
                $contenu_page .= $result;
            } else
            {
                // la quete a bien été instanciée
                $methode = "interagir";     // => Pour réaliser la suite dans la liste de mes quetes en cours !!!!
            }

        }
        break;

    case 'stop' :

        //On vérifie que le perso à bien démarrer la quete et on lui propose d'arrêter
        $quete = new aquete;
        $aquete_cod = $_REQUEST["quete"];
        $quete->charge($aquete_cod);

        $quete_perso = new aquete_perso();
        if ($quete_perso->chargeBy_perso_quete($perso_cod, $quete->aquete_cod))
        {
            $contenu_page .= "Vous êtes sur le point d'arrêter la quête <strong>{$quete->aquete_nom}</strong>, commencée le <strong>" . date("d/m/Y à H:i:s", strtotime($quete_perso->aqperso_date_debut)) . "</strong> !<br>";
            if ($quete->aquete_nb_max_rejouable * 1 > 0) $contenu_page .= "Cette quête ne peut être réalisée que <strong>{$quete->aquete_nb_max_rejouable} fois</strong> par aventurier, vous l'avez déjà réalisé <strong>{$quete_perso->aqperso_nb_realisation} fois</strong>?<br>";
            $contenu_page .= "Aussi, il n'est pas certain que vous puissiez la recommencer!<br>";
            $contenu_page .= "<strong>Etes-vous sûr(e) de vouloir arrêter?</strong>";
            $link = "/jeu_test/quete_auto.php?methode=stop2&quete={$aquete_cod}&choix=" . $_REQUEST["choix"];
            $contenu_page .= '<br><br><a href="' . $link . '" style="margin:50px;">Oui, je veux vraiment arrêter là!</a>';
            $link = "/jeu_test/quete_auto.php?quete={$aquete_cod}";
            $contenu_page .= '<br><br><a href="' . $link . '" style="margin:50px;">NON!! Je continue la quête!</a>';

            $contenu_page .= "<br><br>";
        }
        break;

    case 'stop2' :

        //On vérifie que le perso a bien démarrer la quete et l'arrête
        $quete = new aquete;
        $quete->charge($_REQUEST["quete"]);

        $quete_perso = new aquete_perso();
        if ($quete_perso->chargeBy_perso_quete($perso_cod, $quete->aquete_cod))
        {
            $quete_perso->aqperso_quete_step++; // Step suivant pour le journal

            $perso_journal = new aquete_perso_journal();
            if ($quete->aquete_journal_archive != 'O')
            {
                // La quête est terminée, et l'on n'en conserve pas de journal, supprimer toutes les entrées déjà faites.
                $perso_journal->deleteBy_aqperso_cod($quete_perso->aqperso_cod, $quete_perso->aqperso_nb_realisation);
            } else
            {
                // On conserve l'archive du journal de quete
                $perso_journal->aqpersoj_aqperso_cod = $quete_perso->aqperso_cod;
                $perso_journal->aqpersoj_realisation = $quete_perso->aqperso_nb_realisation;
                $perso_journal->aqpersoj_quete_step = $quete_perso->aqperso_quete_step;
                $perso_journal->aqpersoj_texte = "Vous avez choisi d'abandonner cette quête!<br> ";
                $perso_journal->aqpersoj_lu = "O";
                $perso_journal->stocke(true);
            }


            $quete_perso->aqperso_actif = 'N';
            $quete_perso->aqperso_date_fin = date('Y-m-d H:i:s');
            $quete_perso->stocke();

            if ($quete->aquete_journal_archive != 'O')
            {
                $contenu_page .= "La quête <strong>{$quete->aquete_nom}</strong> s'est termniné par un abandon!";
            } else
            {
                $contenu_page .= "La quête <strong>{$quete->aquete_nom}</strong> s'est termniné par un abandon, malgré tout vous retrouverez le journal de cette quête dans la section des quêtes terminées!";
            }
            $contenu_page .= "<br><br>";
        }
        break;

    case 'terminer' :

        //On vérifie que le perso a bien démarrer la quete et l'arrête
        $quete = new aquete;
        $quete->charge($_REQUEST["quete"]);

        $quete_perso = new aquete_perso();
        if ($quete_perso->chargeBy_perso_quete($perso_cod, $quete->aquete_cod))
        {
            // Fin sur un succès (ou s'il manque l'étape de fin dans la définition de la quête)
            if ($quete_perso->aqperso_actif == "O" || $quete_perso->aqperso_actif == 'S')
            {
                $contenu_page .= "<br>Félicitation, vous avez réussi cette quête!";
                $quete_perso->aqperso_nb_termine++;
            } else if ($quete_perso->aqperso_actif == 'E')
            {
                $contenu_page .= "<br>Malheureusement, vous n'avez pas réussi cette quête!";
            }

            // Si cela n'avait pas été fait !
            if ($quete_perso->aqperso_actif == "O")
            {
                if ($quete->aquete_journal_archive != 'O')
                {
                    // La quête est terminée, et l'on n'en conserve pas de journal, supprimer toutes les entrées déjà faites.
                    $perso_journal = new aquete_perso_journal();
                    $perso_journal->deleteBy_aqperso_cod($quete_perso->aqperso_cod, $quete_perso->aqperso_nb_realisation);
                }

                $quete_perso->aqperso_actif = 'N';
                $quete_perso->aqperso_date_fin = date('Y-m-d H:i:s');
                $quete_perso->stocke();
            }

            if ($quete->aquete_journal_archive != 'O')
            {
                $contenu_page .= "<br>Voila, la quête <strong>{$quete->aquete_nom}</strong> est maintenant terminée!";
            } else
            {
                $contenu_page .= "<br>Voila, la quête <strong>{$quete->aquete_nom}</strong> est maintenant terminée, vous retrouverez le journal de cette quête dans la section des quêtes terminées!";
            }
            $contenu_page .= "<br><br>";
        }
        break;

    case 'choix' :
        $aquete_cod = 1 * $_REQUEST["quete"] ;
        $quete_perso = new aquete_perso();
        if ($quete_perso->chargeBy_perso_quete($perso_cod, $aquete_cod))
        {
            $quete_perso->set_choix_aventurier(1 * $_REQUEST["choix"]);
        }

        $quete = new aquete();
        $quete->charge($aquete_cod);
        if ($quete->aquete_interaction = 'O') {
            $methode = "interagir";
        } else {
            $methode = "";     // => Pour réaliser la suite (run) dans la liste de mes quetes en cours !!!!
        }
        break;

    case 'dialogue' :
        // La saisie du joueur est dans le $_REQUEST, rien de particulier ici, tout sera géré dans le run!

        $aquete_cod = 1 * $_REQUEST["quete"] ;
        $quete = new aquete();
        $quete->charge($aquete_cod);
        if ($quete->aquete_interaction == 'O') {
            $methode = "interagir";
        } else {
            $methode = "";     // => Pour réaliser la suite (run) dans la liste de mes quetes en cours !!!!
        }
        break;
}
if ($methode == "interagir") {

    $quete_perso = new aquete_perso();
    $quete_perso->chargeBy_perso_quete($perso_cod, $aquete_cod) ;
    $quete = $quete_perso->get_quete();

    $contenu_page .= "<div class=\"titre\" style=\"padding:5px;\"><center><strong>Interaction avec l'environnement</strong></center></div><br><br>";
    $contenu_page .= $quete->aquete_description."<br><br>" ;

    //** C'est ici que l'on vérifie l'avancement de la quete, (nota il faut que l'on y passe obligatoirement au démarrage après la première étape) **//
    $quete_perso->run();

    $notes =  $quete_perso->journal('O', 0, false); ;
    // Si la quête est finie, proposer la fermeture au joueur!
    if (! $quete_perso->est_finie() )
    {
        $notes .= $quete_perso->get_texte_etape_courante();
    }
    $contenu_page .= "<span id='perso-journal'>$notes</span>";      // Texte avec l'historique de la quete jusqu'a l'étape en cours

    if ($notes != "")
    {
        $contenu_page .= '<input style="float:right; margin-right:50px; margin-top:3px;" onclick="addQANotes(\'perso-journal\');" type="submit" class="test" value="  Ajouter dans mes Notes ">';
    }

    $contenu_page .= "<br><br>";
} else if ($methode == "")
{
    //--------------------------- PAGE DE SUIVI DES QUETES EN COURS ET TERMINEES --------------------------------------
    $contenu_page .= "<div class=\"titre\" style=\"padding:5px;\"><center><strong>Suivi de vos Quêtes</strong></center></div><br><br>";
    $contenu_page .= '<table cellspacing="0" cellpadding="0" width="100%"><tr style="height:25px;">';

    if ( !isset($_REQUEST["onglet"]) || (isset($_REQUEST["onglet"]) && ($_REQUEST["onglet"]=="encours"))   )
    {
        // --------------------------------------- ONGLET DES QUETES EN COURS------------------------------------------------------------
        $contenu_page .= '<td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=apercu">Aperçu</a></p></td>
                            <td class="onglet" style="width: 25%"><p style="text-align:center">Quête(s) en cours</p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=terminees">Quête(s) terminée(s)</a></p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=notes">Mes notes</a></p></td>
                            </tr>';
        $contenu_page .= '<tr><td colspan="4" class="reste_onglet">';


        $quete_perso = new aquete_perso();
        $quetes_perso = $quete_perso->get_perso_quete_en_cours($perso_cod);

        // Récupération des Quetes en cours
        if (!$quetes_perso)
        {
            // Affichage de la boite de selection
            $contenu_page .= "<br><br><br><br><center>Vous n'avez pas encore commencé de quête!!!!<center><br><br><br><br>";
        } else
        {
            $quete_id_list = "";        // S'il n'y a rien de commencé!
            $recherche_quete = array();     // recherche inversé
            foreach ($quetes_perso as $k => $q)
            {
                $recherche_quete[(1 * $q->aqperso_aquete_cod)] = (1 * $q->aqperso_cod);
                $quete_id_list .= (1 * $q->aqperso_aquete_cod) . ",";
            }
            $quete_id_list = substr($quete_id_list, 0, -1);

            if (isset($_REQUEST["quete"])) $aquete_cod = 1 * $_REQUEST["quete"];
            if ($aquete_cod == 0) $aquete_cod = 1 * ($quetes_perso[0]->aqperso_aquete_cod);      // Si on a pas choisi une quete particulière, prendre la première

            // Affichage de la boite de selection
            $contenu_page .= '<form method="post">';

            // Récupérer la liste de quete en cours
            //echo "<pre>"; print_r($quetes_perso); echo "</pre>"; die();
            //selectbox
            $selectbox = '<select name="quete" style="width:300px;" onchange="this.parentNode.submit();">';
            foreach ($quetes_perso as $k => $q)
            {
                $aq = new aquete();
                $aq->charge($q->aqperso_aquete_cod);
                $news = $q->get_journal_nb_news();
                $style = ((int)$news["journal_news"]>0) ?  'style="font-weight: bold;"' : '';
                $selectbox.='<option '.$style.' '.($q->aqperso_aquete_cod==$aquete_cod ? "selected" : "").' value="'.$q->aqperso_aquete_cod.'">'.$aq->aquete_nom.'</option>';
            }
            $selectbox.= '</select>';

            //$contenu_page .= "<br>Sélectionner la quête : " . create_selectbox_from_req("quete", "SELECT aquete_cod, aquete_nom FROM quetes.aquete where aquete_cod in ({$quete_id_list}) order by aquete_nom", $aquete_cod, array('style' => 'style="width:300px;" onchange="this.parentNode.submit();"'));
            $contenu_page .= "<br>Sélectionner la quête : " . $selectbox;
            $contenu_page .= '</form><hr>';

            $quete_perso->charge($recherche_quete[$aquete_cod]);
            $quete = $quete_perso->get_quete();

            //** C'est ici que l'on vérifie l'avancement de la quete, (nota il faut que l'on y passe obligatoirement au démarrage après la première étape) **//
            $quete_perso->run();

            //$contenu_page .= "<div class=\"titre\" style=\"padding:5px;\"><center><strong>{$quete->aquete_nom}</strong></center></div>" ;
            $link = "/jeu_test/quete_auto.php?methode=stop&quete={$aquete_cod}";
            $contenu_page .= "Quête commencée le : " . date("d/m/Y H:i:s", strtotime($quete_perso->aqperso_date_debut)) . "&nbsp;&nbsp;(<em style=\"font-size:9px;\"><a href={$link}>Arrêter cette quête</a></em>)<br>";
            $contenu_page .= "<u>Description de la quête</u> : " . $quete->aquete_description . "<br>";


            /*  $contenu_page .= "<div class=\"hr\">&nbsp;&nbsp;<strong>Options admin</strong>&nbsp;&nbsp;</div><br>" ;
              $req = " SELECT aqpersoj_cod, 'Step #'||aqpersoj_quete_step::text || COALESCE (' Etape '||aqetape_cod::text||' '||aqetape_nom, '') as nom
                                FROM quetes.aquete_perso_journal
                                JOIN quetes.aquete_perso on aqperso_cod=aqpersoj_aqperso_cod and aqperso_nb_realisation=aqpersoj_realisation
                                LEFT JOIN quetes.aquete_etape on aqetape_cod=aqpersoj_etape_cod
                                WHERE aqperso_perso_cod={$perso_cod} AND aqperso_aquete_cod={$aquete_cod} ORDER BY aqpersoj_quete_step";*/


            if ($isAdminAnimation)
            {
                $contenu_page .= '<br><strong style="color:#800000"><u>Options d\'admin</u></strong>&nbsp;&nbsp;:<br>';
                $contenu_page .= '<form action="admin_quete_auto_edit.php" method="post">';
                $contenu_page .= '<input type="hidden" name="methode" value="perso_step">';
                $contenu_page .= '<input type="hidden" name="aqperso_cod" value="' . $quete_perso->aqperso_cod . '">';
                $contenu_page .= '<input type="hidden" name="aquete_cod" value="' . $aquete_cod . '">';
                $req = "SELECT aqpersoj_cod, 'Step #'||aqpersoj_quete_step::text||COALESCE(' Etape #'||aqetape_cod::text||' - '||aqetape_nom, '') as nom 
                                FROM quetes.aquete_perso_journal 
                                JOIN quetes.aquete_perso on aqperso_cod=aqpersoj_aqperso_cod and aqperso_nb_realisation=aqpersoj_realisation 
                                LEFT JOIN quetes.aquete_etape on aqetape_cod=aqpersoj_etape_cod
                                where aqperso_perso_cod={$perso_cod} AND aqperso_aquete_cod={$aquete_cod} and aqpersoj_quete_step!={$quete_perso->aqperso_quete_step} ORDER BY aqpersoj_quete_step";
                $contenu_page .= "Retourner à la fin de : " . create_selectbox_from_req("aqpersoj_cod", $req, 0, array('style' => 'style="width:500px;"')) . "&nbsp;<input class='test' type=\"submit\" value=\"Retourner à cet état\" />";
                $contenu_page .= '<br><u>ATTENTION</u>: Après le retour au step choisi, tous les éléments de la quête pour ce perso seront supprimés comme s\'il n\'avaient jamais eu lieu.<br>';
                $contenu_page .= '</form>';
            }

            $contenu_page .= "<div class=\"hr\">&nbsp;&nbsp;<strong>Journal de la quête</strong>&nbsp;&nbsp;</div><br>";
            $notes = $quete_perso->journal('O', 1, $isAdminAnimation);      // Texte avec l'historique de la quete jusqu'a l'étape en cours, montrer la dernière page en non-lu


            // Si la quête est finie, proposer la fermeture au joueur!
            if ($quete_perso->est_finie())
            {
                $contenu_page .= "<span id='perso-journal'>$notes</span>";      // Texte avec l'historique de la quete jusqu'a l'étape en cours
                //if ($notes != "")
                //{
                //    $contenu_page .= '<input style="float:right; margin-right:50px; margin-top:3px;" onclick="addQANotes(\'perso-journal\');" type="submit" class="test" value="  Ajouter dans mes Notes ">';
                //}

                $contenu_page .= '&nbsp;&nbsp;&nbsp;<form method="post"><input type="hidden" name="methode" value="terminer"><input type="hidden" name="quete" value="' . $aquete_cod . '"><input type="submit" class="test" value="  Terminer  "></form>';

                // Maintenant on force la fermeture ici, trop de joueur ne clique pas sur "Terminer"=================
                if ($quete->aquete_journal_archive != 'O')
                {
                    // La quête est terminée, et l'on n'en conserve pas de journal, supprimer toutes les entrées déjà faites.
                    $perso_journal = new aquete_perso_journal();
                    $perso_journal->deleteBy_aqperso_cod($quete_perso->aqperso_cod, $quete_perso->aqperso_nb_realisation);
                }

                $quete_perso->aqperso_actif = 'N';
                $quete_perso->aqperso_date_fin = date('Y-m-d H:i:s');
                $quete_perso->stocke();

            }
            else
            {
                //** Le texte d'étape courante par exemple un choix (peut être vide si on attend un état spécifique)  **//
                $notes .= $quete_perso->get_texte_etape_courante();
                $contenu_page .= "<span id='perso-journal'>$notes</span>";      // Texte avec l'historique de la quete jusqu'a l'étape en cours
                //if ($notes != "")
                //{
                //    $contenu_page .= '<input style="float:right; margin-right:50px; margin-top:3px;" onclick="addQANotes(\'perso-journal\');" type="submit" class="test" value="  Ajouter dans mes Notes ">';
                //}
            }



            $contenu_page .= "<br><br>";
        }
    } else if (isset($_REQUEST["onglet"]) && ($_REQUEST["onglet"]=="apercu"))
    {
        // --------------------------------------- ONGLET DES QUETES EN COURS APPERCU ------------------------------------------------------------
        $contenu_page .= '<td class="onglet" style="width: 25%"><p style="text-align:center">Aperçu</p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=encours">Quête(s) en cours</a></p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=terminees">Quête(s) terminée(s)</a></p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=notes">Mes notes</a></p></td>
                            </tr>';
        $contenu_page .= '<tr><td colspan="4" class="reste_onglet">';


        $quete_perso = new aquete_perso();
        $quetes_perso = $quete_perso->get_perso_quete_en_cours($perso_cod);

        // Récupération des Quetes en cours
        if (!$quetes_perso)
        {
            // Affichage de la boite de selection
            $contenu_page .= "<br><br><br><br><center>Vous n'avez pas encore commencé de quête!!!!<center><br><br><br><br>";
        } else
        {
            foreach ($quetes_perso as $k => $q)
            {
                // Préparation du journal pour indiquer le résultat de l'échange
                $journal = new aquete_perso_journal();
                $journal->chargeDernierePage($q->aqperso_cod, $q->aqperso_nb_realisation) ;

                $aq = new aquete();
                $aq->charge($q->aqperso_aquete_cod);

                $news = $q->get_journal_nb_news();
                $style = ((int)$news["journal_news"]>0) ?  'style="background-color: #BA9C6C;"' : '';

                $contenu_page .= '<div class="hr">&nbsp;&nbsp;<strong>Commencée le ' .date("d/m/Y H:i:s", strtotime($q->aqperso_date_debut))  . '</strong>&nbsp;&nbsp;</div>';
                $contenu_page .= "Quête : <a href=\"quete_auto.php?onglet=encours&quete=".$q->aqperso_aquete_cod."\" style=\"font-weight:bold;\">" . $aq->aquete_nom . "</a><br>";
                $contenu_page .= "<div {$style}>{$journal->aqpersoj_texte}</div>";

            }
        }
        $contenu_page .= "<br><br>";
    } else if (isset($_REQUEST["onglet"]) && ($_REQUEST["onglet"]=="notes"))
    {
        // --------------------------------------- ONGLET DES QUETES EN COURS APPERCU ------------------------------------------------------------
        // Editeur WYSIWYG pour le texte d'étape! (SCEdtor) / Avait été retiré? pourquoi? par qui ?
        $contenu_page .=  '<link href="/styles/sceditor.min.css" rel="stylesheet">';
        $contenu_page .=  '<script src="/scripts/sceditor.min.js" type="text/javascript"></script>';
        $contenu_page .=  '<script src="/scripts/sceditor-xhtml.min.js" type="text/javascript"></script>';
        $contenu_page .=  '<script>//# sourceURL=quete_auto_edit_note.js
  
        $( document ).ready(function() {
        
               $("#id-textarea-notes").height( $("#id-textarea-notes")[0].scrollHeight);
               sceditor.command.set("save", {
                    exec: function() {
                        // this is set to the editor instance
                      console.log(this.val());
                      runAsync({request: "save-qa-notes", data:{notes:this.val()}}, popSaveQANotesStatus, {})
        
                    },
                    tooltip: "Sauvegarder les modifications!"
                });              
        
                var textarea = document.getElementById("id-textarea-notes");
                sceditor.create(textarea, {
                    format: "xhtml",
                    plugins: "plaintext",
                    style: "/styles/sceditor.min.css",
                    toolbar: "save|pastetext,bold,italic,underline,strike,subscript,superscript|size,color,removeformat|emoticon,image|maximize|source", 
                });
            });
        </script>';


        $contenu_page .= '<td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=apercu">Aperçu</a></p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=encours">Quête(s) en cours</a></p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=terminees">Quête(s) terminée(s)</a></p></td>
                            <td class="onglet" style="width: 25%"><p style="text-align:center">Mes notes</p></td>
                            </tr>';
        $contenu_page .= '<tr><td colspan="4" class="reste_onglet">';

        $pnotes = new aquete_perso_notes();
        $result = $pnotes->getBy_aqperson_perso_cod($perso_cod) ;
        $notes = $result ? $result[0]->aqperson_notes : "" ;


            // Affichage de la boite de selection
        //$contenu_page .= "<br><center><strong>Mes notes personnelles</strong> <em style='font-size:10px;'>(les notes personnelles sont limitées à 4096 caractères)</em></center>";
        $contenu_page .= "<br><center><strong>Mes notes personnelles</strong> <em style='font-size:10px;'></center>";
        $contenu_page .= '<hr>';
        $contenu_page .= '<textarea id="id-textarea-notes" style="min-height: 600px; width: 100%;">'.$notes.'</textarea>';

        //$contenu_page .= "Le texte contient: <span id='id-textarea-taille'>".(strlen($notes))."</span>/ 4096";
        $contenu_page .= "<br><br>";
    } else
    {
        // --------------------------------------- ONGLET DES QUETES TERMINE------------------------------------------------------------
        $contenu_page .= '<td class="pas_onglet" style="width: é(%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=apercu">Aperçu</a></p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=encours">Quête(s) en cours</a></p></td>
                            <td class="onglet" style="width: 25%"><p style="text-align:center">Quête(s) terminée(s)</p></td>
                            <td class="pas_onglet" style="width: 25%"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=notes">Mes notes</a></p></td>
                            </tr>';
        $contenu_page .= '<tr><td colspan="4" class="reste_onglet">';

        $quete_perso = new aquete_perso();
        $quetes_perso = $quete_perso->get_perso_quete_terminee($perso_cod);

        // Récupération des Quetes en cours
        if (!$quetes_perso)
        {
            // Affichage de la boite de selection
            $contenu_page .= "<br><br><br><br><center>Vous n'avez pas encore terminé de quête!!!!<center><br><br><br><br>";
        } else
        {
            $perso_journal = new aquete_perso_journal();
            $aq_select = array();
            foreach ($quetes_perso as $k => $qp)
            {
                $quete = new aquete();
                $quete->charge($qp->aqperso_aquete_cod);
                $nb_realisation = $qp->aqperso_actif == 'O' ? $qp->aqperso_nb_realisation - 1 : $qp->aqperso_nb_realisation;
                $Liste_perso_realisation = $perso_journal->getListe_perso_realisation($qp->aqperso_cod);
                for ($i = 0; $i < $nb_realisation; $i++)
                {
                    $r = $i + 1;
                    if (in_array($r, $Liste_perso_realisation))
                    {
                        // seulement si la quete à été journalisée
                        $aq_select[$qp->aqperso_cod . '*' . $r] = $quete->aquete_nom . " ($r)";
                    }
                }
            }

            //seulement s'il y a eu des quêtes journalisée (le perso n'a peut-être terminé que des quêtes qui ne vont pas dans le journal)
            if (sizeof($aq_select)==0)
            {
                // Affichage de la boite de selection
                $contenu_page .= "<br><br><br><br><center>Vous n'avez pas encore terminé de quête (journalisée)!!!!<center><br><br><br><br>";
            }
            else
            {
                // Affichage de la boite de selection
                $contenu_page .= '<form method="post">';
                $contenu_page .= '<input type="hidden" name="onglet" value="terminees">';
                $contenu_page .= "<br>Sélectionner la quête : " . create_selectbox("quete_nb", $aq_select, $_REQUEST["quete_nb"], array('style' => 'style="width:300px;" onchange="this.parentNode.submit();"'));
                $contenu_page .= '</form><hr>';

                if (!isset($_REQUEST["quete_nb"])) $_REQUEST["quete_nb"] = array_keys($aq_select)[0];
                $q = explode("*", $_REQUEST["quete_nb"]);
                $quete_perso->charge($q[0]);
                $realisation = 1 * $q[1];

                $journal_pages = $perso_journal->getBy_perso_realisation($quete_perso->aqperso_cod, $realisation);

                $contenu_page .= "Quête commencée le : " . date("d/m/Y H:i:s", strtotime($journal_pages[0]->aqpersoj_date)) . " et terminée le : " . date("d/m/Y H:i:s", strtotime($journal_pages[count($journal_pages) - 1]->aqpersoj_date)) . "<br>";
                $contenu_page .= "<u>Description de la quête</u> : " . $quete->aquete_description . " (réalisation #$realisation)<br><br><div class=\"hr\">&nbsp;&nbsp;<strong>Journal de la quête</strong>&nbsp;&nbsp;</div><br>";

                foreach ($journal_pages as $k => $jpages)
                {
                    $contenu_page .= $jpages->aqpersoj_texte . "<br><br>";
                }
            }
        }
    }
    $contenu_page .= "</td></tr></table>";     // Fin des onglets!!
    //echo "<pre>"; print_r($quetes); echo "</pre>";
}

include "blocks/_footer_page_jeu.php";
