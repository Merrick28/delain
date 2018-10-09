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
if (!isset($methode)) $methode = "" ;
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
        $quete_perso = new aquete_perso() ;
        $result = $quete_perso->demarre_quete($perso_cod, $aquete_cod, $tab_quete["triggers"][$trigger]);

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
    if ($quete_perso->chargeBy_perso_quete($perso_cod, $quete->aquete_cod))
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
    if ($quete_perso->chargeBy_perso_quete($perso_cod, $quete->aquete_cod))
    {
        $quete_perso->aqperso_actif = 'N';
        $quete_perso->aqperso_date_fin = date('Y-m-d H:i:s');
        $quete_perso->stocke();

        $contenu_page2 .= "La quête <b>{$quete->aquete_nom}</b> s'est termniné par un abandon, malgré tout vous retrouverez le journal de cette quête dans la section des quêtes terminées!";
        $contenu_page2 .= "<br><br>";
    }
break;

case 'terminer' :

    //On vérifie que le perso a bien démarrer la quete et l'arrête
    $quete = new aquete;
    $quete->charge($_REQUEST["quete"]);

    $quete_perso = new aquete_perso() ;
    if ($quete_perso->chargeBy_perso_quete($perso_cod, $quete->aquete_cod))
    {
        // Fin sur un succès (ou s'il manque l'étape de fin dans la définition de la quête)
        if ($quete_perso->aqperso_actif =="O" || $quete_perso->aqperso_actif == 'S')
        {
            $contenu_page2 .= "<br>Félicitation, vous avez réussie cette quête!";
            $quete_perso->aqperso_nb_termine ++ ;
        }
        $quete_perso->aqperso_actif = 'N';
        $quete_perso->aqperso_date_fin = date('Y-m-d H:i:s');
        $quete_perso->stocke();

        $contenu_page2 .= "<br>Voila, la quête <b>{$quete->aquete_nom}</b> est maintenant terminée, vous retrouverez le journal de cette quête dans la section des quêtes terminées!";
        $contenu_page2 .= "<br><br>";
    }
break;

case 'choix' :

    $quete_perso = new aquete_perso() ;
    if ( $quete_perso->chargeBy_perso_quete($perso_cod,1*$_REQUEST["quete"]) )
    {
        $quete_perso->set_choix_aventurier(1*$_REQUEST["choix"]);
    }
    $methode = "" ;     // => Pour réaliser la suite (run) dans la liste de mes quetes en cours !!!!

break;
}

if ($methode=="")
{
    //--------------------------- PAGE DE SUIVI DES QUETES EN COURS ET TERMINEES --------------------------------------
    $contenu_page2 .= "<div class=\"titre\" style=\"padding:5px;\"><center><b>Suivi de vos Quêtes</b></center></div><br><br>";
    $contenu_page2 .= '<table cellspacing="0" cellpadding="0" width="100%"><tr style="height:25px;">';
    if (!isset($_REQUEST["onglet"]))
    {
        // --------------------------------------- ONGLET DES QUETES EN COURS------------------------------------------------------------
        $contenu_page2 .= '<td class="onglet"><p style="text-align:center">Quête(s) en cours</p></td><td class="pas_onglet"><p style="text-align:center"><a href="/jeu_test/quete_auto.php?onglet=terminees">Quête(s) terminée(s)</a></p></td></tr>';
        $contenu_page2 .= '<tr><td colspan="2" class="reste_onglet">';


        $quete_perso = new aquete_perso() ;
        $quetes_perso = $quete_perso->get_perso_quete_en_cours($perso_cod);

        // Récupération des Quetes en cours
        if (!$quetes_perso)
        {
            // Affichage de la boite de selection
            $contenu_page2 .= "<br><br><br><br><center>Vous n'avez pas encore commencé de quête!!!!<center><br><br><br><br>" ;
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

            if (isset($_REQUEST["quete"])) $aquete_cod = 1*$_REQUEST["quete"];
            if ($aquete_cod==0) $aquete_cod = 1*($quetes_perso[0]->aqperso_aquete_cod);      // Si on a pas choisi une quete particulière, prendre la première

            // Affichage de la boite de selection
            $contenu_page2 .= '<form method="post">';
            if (isset($_REQUEST["onglet"])) $contenu_page2 .= '<input type="hidden" name="onglet" value="terminees">';
            $contenu_page2 .= "<br>Sélectionner la quête : ". create_selectbox_from_req("quete", "SELECT aquete_cod, aquete_nom FROM quetes.aquete where aquete_cod in ({$quete_id_list}) order by aquete_nom", $aquete_cod, array('style'=>'style="width:300px;" onchange="this.parentNode.submit();"'));
            $contenu_page2 .= '</form><hr>';

            $quete_perso->charge($recherche_quete[$aquete_cod]);
            $quete = $quete_perso->get_quete() ;

            //** C'est ici que l'on vérifie l'avancement de la quete, (nota il faut que l'on y passe obligatoirement au démarrage après la première étape) **//
            $quete_perso->run();

            //$contenu_page2 .= "<div class=\"titre\" style=\"padding:5px;\"><center><b>{$quete->aquete_nom}</b></center></div>" ;
            $contenu_page2 .= "Quête commencée le : ".date("d/m/Y H:i:s", strtotime($quete_perso->aqperso_date_debut)) ."<br>" ;
            $contenu_page2 .= "<u>Description de la quête</u> : ".$quete->aquete_description ."<br><br><div class=\"hr\">&nbsp;&nbsp;<b>Journal de la quête</b>&nbsp;&nbsp;</div><br>" ;

            $contenu_page2 .= $quete_perso->journal('O', 1);      // Texte avec l'historique de la quete jusqu'a l'étape en cours, montrer la dernière page en non-lu

            //** Le texte d'étape courante par exemple un choix (peut être vide si on attend un état spécifique)  **//
            $contenu_page2 .= $quete_perso->get_texte_etape_courante();

            // Si la quête est finie, proposer la fermeture au joueur!
            if ($quete_perso->est_finie())
            {
                $contenu_page2 .= '&nbsp;&nbsp;&nbsp;<form method="post"><input type="hidden" name="methode" value="terminer"><input type="hidden" name="quete" value="'.$aquete_cod.'"><input type="submit" class="test" value="  Terminer  "></form>';
            }
            $contenu_page2 .= "<br><br>";
        }
    }
    else
    {
        // --------------------------------------- ONGLET DES QUETES TERMINE------------------------------------------------------------
        $contenu_page2 .= '<td class="pas_onglet"><p style="text-align:center"><a href="/jeu_test/quete_auto.php">Quête(s) en cours</a></p></td><td class="onglet"><p style="text-align:center">Quête(s) terminée(s)</p></td></tr>';
        $contenu_page2 .= '<tr><td colspan="2" class="reste_onglet">';

        $quete_perso = new aquete_perso() ;
        $quetes_perso = $quete_perso->get_perso_quete_terminee($perso_cod);

        // Récupération des Quetes en cours
        if (!$quetes_perso)
        {
            // Affichage de la boite de selection
            $contenu_page2 .= "<br><br><br><br><center>Vous n'avez pas encore terminé de quête!!!!<center><br><br><br><br>" ;
        }
        else
        {
            $aq_select = array();
            foreach ($quetes_perso as $k => $qp)
            {
                $quete = new aquete() ;
                $quete->charge($qp->aqperso_aquete_cod);
                $nb_realisation = $qp->aqperso_actif == 'O' ?  $qp->aqperso_nb_realisation-1 : $qp->aqperso_nb_realisation ;
                for ($i=0; $i<$nb_realisation; $i++)
                {
                    $r = $i+1;
                    $aq_select[$qp->aqperso_cod.'*'.$r] = $quete->aquete_nom." ($r)";
                }
            }
            // Affichage de la boite de selection
            $contenu_page2 .= '<form method="post">';
            if (isset($_REQUEST["onglet"])) $contenu_page2 .= '<input type="hidden" name="onglet" value="terminees">';
            $contenu_page2 .= "<br>Sélectionner la quête : ". create_selectbox("quete_nb", $aq_select, $_REQUEST["quete_nb"], array('style'=>'style="width:300px;" onchange="this.parentNode.submit();"'));
            $contenu_page2 .= '</form><hr>';

            if (!isset($_REQUEST["quete_nb"])) $_REQUEST["quete_nb"] = array_keys($aq_select)[0];
            $q = explode("*", $_REQUEST["quete_nb"] );
            $quete_perso->charge($q[0]);
            $realisation = 1*$q[1];

            $perso_journal = new aquete_perso_journal();
            $journal_pages = $perso_journal->getBy_perso_realisation($quete_perso->aqperso_cod, $realisation);

            $contenu_page2 .= "Quête commencée le : ".date("d/m/Y H:i:s", strtotime($journal_pages[0]->aqpersoj_date)) ."<br>" ;
            $contenu_page2 .= "<u>Description de la quête</u> : ".$quete->aquete_description ." (réalisation #$realisation)<br><br><div class=\"hr\">&nbsp;&nbsp;<b>Journal de la quête</b>&nbsp;&nbsp;</div><br>" ;

            foreach ($journal_pages as $k => $jpages)
            {
                $contenu_page2 .= $jpages->aqpersoj_texte."<br><br>"; ;
            }

        }
    }
    $contenu_page2 .= "</td></tr></table>";     // Fin des onglets!!
    //echo "<pre>"; print_r($quetes); echo "</pre>";
}


// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');

$t->set_var('CONTENU_COLONNE_DROITE',$contenu_page2);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
