﻿<?php
include "blocks/_header_page_jeu.php";

$pdo = new bddpdo();

$perso = new perso;
if (!$perso->charge($perso_cod))
{
    die('Erreur sur chargement de perso !');
}

$compte = new compte;
$compte->charge($compt_cod);

if (!isset($methode))
{
    $methode = '';
}

function affiche_apres_deplacement($position)
{
    $is_phrase = rand(1, 100);
    if ($is_phrase < 34)
    {
        $rumeur = new rumeurs();
        $retour = '<hr /><p><em>Rumeur :</em> ' . $rumeur->get_rumeur() . '</p>';
    } else if ($is_phrase < 67)
    {
        include 'phrase.php';
        $idx_phrase = rand(1, sizeof($phrase));
        $retour     = '<hr /><p><em>' . $phrase[$idx_phrase] . '</em></p>';
    } else
    {
        $pos = new positions();
        $pos->charge($position);
        $retour = '<hr /><p>Sur le sol est gravé un indice qui pourrait être fort utile : <br /><em>' . $pos->get_indice() . '</em></p>';
    }
    return $retour;
}


$menu_deplacement = isset($_POST['menu_deplacement']) ? $_POST['menu_deplacement'] : '';
$inc_vue          = ($methode == 'deplacement') && (!$menu_deplacement);
if (!$inc_vue)
{
    $t = new template;
    $t->set_file('FileRef', '../template/delain/general_jeu.tpl');
    // chemins
    $t->set_var('URL', $type_flux . G_URL);
    $t->set_var('URL_IMAGES', G_IMAGES);
}

if (!$compte->is_admin() || ($compte->is_admin_monstre() && $perso->perso_type_perso == 2 || $perso->perso_pnj == 1))
{
    switch ($methode)
    {
        case 'attaque2':
            /* on porte une attaque */
            $perso->has_arme_distance();
            if (isset($_POST['cible']))
            {
                $cible = $_POST['cible'];
            }

            if (isset($_GET['cible']))
            {
                $cible = $_GET['cible'];
            }


            if (!isset($cible))
            {
                $contenu_page .= '<p>Erreur : cible non définie !';
                break;
            }

            $tex_at[0]  = 'Attaquer ';
            $tex_at[1]  = 'Utiliser attaque foudroyante ';
            $tex_at[2]  = 'Utiliser attaque foudroyante (niv. 2) ';
            $tex_at[3]  = 'Utiliser attaque foudroyante (niv. 3) ';
            $tex_at[4]  = 'Utiliser feinte ';
            $tex_at[5]  = 'Utiliser feinte (niv. 2) ';
            $tex_at[6]  = 'Utiliser feinte (niv. 3) ';
            $tex_at[7]  = 'Utiliser coup de grâce ';
            $tex_at[8]  = 'Utiliser coup de grâce (niv. 2) ';
            $tex_at[9]  = 'Utiliser coup de grâce (niv. 3) ';
            $tex_at[10] = 'Utiliser bout portant ';
            $tex_at[11] = 'Utiliser bout portant (niv. 2) ';
            $tex_at[12] = 'Utiliser bout portant (niv. 3) ';
            $tex_at[13] = 'Utiliser tir précis ';
            $tex_at[14] = 'Utiliser tir précis (niv. 2) ';
            $tex_at[15] = 'Utiliser tir précis (niv. 3) ';
            $tex_at[16] = 'Utiliser balayage ';
            $tex_at[17] = 'Utiliser garde-manger ';
            $tex_at[18] = 'Utiliser les neufs têtes de l’hydre ';
            $tex_at[19] = 'Utiliser Jeu de trolls ';
            $tex_at[20] = 'Utiliser charge divine ';

            $pa_n = $perso->get_pa_attaque();
            $pa_f = $perso->get_pa_foudre();
            $pa_s = 12; // volontairement haut pour ne pas afficher le message d’utilisation

            $pa_at[0]  = $pa_n;
            $pa_at[1]  = $pa_f;
            $pa_at[2]  = $pa_f;
            $pa_at[3]  = $pa_f;
            $pa_at[4]  = $pa_n + 3;
            $pa_at[5]  = $pa_n + 1;
            $pa_at[6]  = $pa_n;
            $pa_at[7]  = $pa_n + 3;
            $pa_at[8]  = $pa_n + 1;
            $pa_at[9]  = $pa_n;
            $pa_at[10] = $pa_n;
            $pa_at[11] = $pa_n;
            $pa_at[12] = $pa_n;
            $pa_at[13] = $pa_n + 3;
            $pa_at[14] = $pa_n + 1;
            $pa_at[15] = $pa_n;
            $pa_at[16] = $pa_s;
            $pa_at[17] = $pa_s;
            $pa_at[18] = $pa_s;
            $pa_at[19] = $pa_s;
            $pa_at[20] = $pa_s;
            if (!isset($type_at))
            {
                $type_at = 0;
            }

            $req    = 'select attaque(:perso,:cible,:type_attaque) as resultat';
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":perso"        => $perso_cod,
                                          ":cible"        => $cible,
                                          ":type_attaque" => $type_at), $stmt);
            $result = $stmt->fetch();

            $contenu_page .= $result['resultat'];
            $contenu_page .= '
			<form name="attaquer1" method="post" action="action.php">
			<input type="hidden" name="ctl" value="0">
			<input type="hidden" name="methode" value="attaque2">
			<input type="hidden" name="type_at" value="' . $type_at . '">
			<input type="hidden" name="type" value="1">';
            $attaquable   = 1;

            // on recharhe le perso
            $perso->charge($perso_cod);

            //on regarde pour le nombre de PA
            if ($perso->perso_pa < $pa_at[$type_at])
            {
                $attaquable = 0;
            }

            // on charge le "perso" de la cible
            $perso_cible = new perso;
            $perso_cible->charge($cible);
            // on regarde pour la distance de la cible
            if ($perso_cible->perso_tangible == 'N')
            {
                $attaquable = 0;
            }
            if ($attaquable == 1)
            {
                $contenu_page .= '<input type="hidden" name="cible" value="' . $cible . '">';
                $contenu_page .= '<p style="text-align:center;"><a href="' . $PHP_SELF . '?methode=' . $methode . '&type_at=' . $type_at . '&cible=' . $cible . '">';
                $contenu_page .= $tex_at[$type_at] . 'de nouveau (' . $pa_at[$type_at] . ' PA)</a>';
                $contenu_page .= '</form>';
            }
            $contenu_page2 = '<hr>Autres types d’attaque : <br />';
            // autres attaques
            $contenu_page2 .= '<form name="attaquer_autres" method="post" action="action.php">
				<input type="hidden" name="cible" value="' . $cible . '">
				<input type="hidden" name="ctl" value="0">
				<input type="hidden" name="methode" value="attaque2">
				<input type="hidden" name="type" value="1">
				<select name="type_at">';

            // Méthode de combat
            $inc_attaque_courante = $type_at;
            $inc_verif_pa         = $pa;

            include('inc_competence_combat.php');

            $contenu_page2 .= $resultat_inc_competence_combat;

            $contenu_page2 .= '</select> <input type="submit" value="Valider" class="test"></form>';
            if ($resultat_inc_competence_combat == '')
            {
                $contenu_page2 = '';
            }
            $contenu_page .= $contenu_page2;
            // fin autres attaques

            break;
        case 'ramasse_objet':
            /* on ramasse un objet */
            $type_objet = isset($_POST['type_objet']) ? $_POST['type_objet'] : $_GET['type_objet'];
            $num_objet  = isset($_POST['num_objet']) ? $_POST['num_objet'] : $_GET['num_objet'];
            $objet[1]   = 'objet';
            $objet[2]   = 'or';
            if (!isset($type_objet))
            {
                $contenu_page .= '<p>Erreur : type objet non défini !';
                break;
            }
            if (!isset($num_objet))
            {
                $contenu_page .= '<p>Erreur : numéro objet non défini !';
                break;
            }
            // passage en pdo sd
            $req    = 'select ramasse_' . $objet[$type_objet] . '(:perso,:num_objet) as resultat';
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":perso"     => $perso_cod,
                                          ":num_objet" => $num_objet
            ), $stmt);
            $result = $stmt->fetch();

            $contenu_page .= $result['resultat'];

            break;


        case 'deplacement':
            /* On se déplace */

            if ($perso->perso_type_perso == 3)
            {
                $contenu_page .= '<p>Erreur ! Un familier ne peut pas se déplacer seul !</p>';
                $resultat_dep = $contenu_page;
                if ($menu_deplacement === '') include('frame_vue.php');
                break;
            }
            if (isset($_POST['position']))
            {
                $position = $_POST['position'];
            }

            if (isset($_GET['position']))
            {
                $position = $_GET['position'];
            }

            if (!isset($position) || $position === '')
            {
                $contenu_page .= '<p>Erreur ! Position non définie !</p>';
                $resultat_dep = $contenu_page;
                if ($menu_deplacement === '') include('frame_vue.php');
                break;
            }


            $req_deplace = 'select deplace_code(:perso_cod,:position) as deplace';

            $stmt   = $pdo->prepare($req_deplace);
            $stmt   = $pdo->execute(array(
                ':perso_cod' => intval($perso_cod),
                ':position'  => intval($position)), $stmt);
            $retour = $stmt->fetch();

            $result      = explode('#', $retour['deplace']);
            $page_retour = 'frame_vue.php';

            $retour = '';
            if ($menu_deplacement !== '')
            {
                $page_retour = 'deplacement.php';
                $retour      = '<hr /><p><a href="' . $page_retour . '">Retour !</a></p>';
            }
            $contenu_page .= $result[1];

            if (strpos($result[1], 'Erreur') !== 0)
            {
                $contenu_page .= affiche_apres_deplacement($position);
            }
            $contenu_page .= $retour;

            if ($menu_deplacement === '')
            {
                $resultat_dep = $contenu_page;
                include('frame_vue.php');
                die('');
                //header('Location:' . $type_flux.G_URL . 'jeu_test/' . $page_retour);
            }
            break;
        case "passage":
            /* On se déplace */

            if ($perso->perso_type_perso == 3)
            {
                $contenu_page .= '<p>Erreur ! Un familier ne peut pas se déplacer seul !';
                break;
            }
            $req_deplace  = 'select passage(:perso_cod) as deplace';
            $stmt         = $pdo->prepare($req_deplace);
            $stmt         = $pdo->execute(array(
                ':perso_cod' => intval($perso_cod)
            ), $stmt);
            $retour       = $stmt->fetch();
            $result       = explode('#', $result['deplace']);
            $contenu_page .= $result[0];
            $contenu_page .= '<br />';
            if ($result[1] == 0)
            {
                $contenu_page .= affiche_apres_deplacement($position);
            }
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;
        case "sortie_arene":

            $req_deplace  = 'select sortir_arene(:perso_cod) as res';
            $stmt         = $pdo->prepare($req_deplace);
            $stmt         = $pdo->execute(array(
                ':perso_cod' => intval($perso_cod)
            ), $stmt);
            $retour       = $stmt->fetch();
            $result       = explode(';', $retour['res']);
            $contenu_page .= $result[1];
            $contenu_page .= '<br /><br />';
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;
        case "sortir_donjon":
            $req_deplace  = 'select sortir_donjon(:perso_cod) as res';
            $stmt         = $pdo->prepare($req_deplace);
            $stmt         = $pdo->execute(array(
                ':perso_cod' => intval($perso_cod)
            ), $stmt);
            $retour       = $stmt->fetch();
            $result       = explode(';', $retour['res']);
            $contenu_page .= $result[1];
            $contenu_page .= '<br /><br />';
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;
        case "enreg_pos_donjon":
            $req_deplace  = 'select enregistre_avancee_donjon(:perso_cod) as res';
            $stmt         = $pdo->prepare($req_deplace);
            $stmt         = $pdo->execute(array(
                ':perso_cod' => intval($perso_cod)
            ), $stmt);
            $retour       = $stmt->fetch();
            $result       = explode(';', $retour['res']);
            $contenu_page .= $result[1];
            $contenu_page .= '<br /><br />';
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;

        case 'passage_prison':
            /* On se déplace */
            if ($perso->perso_type_perso == 3)
            {
                $contenu_page .= '<p>Erreur ! Un familier ne peut pas se déplacer seul !';
                break;
            }
            $req_deplace  = 'select passage(:perso_cod) as deplace';
            $stmt         = $pdo->prepare($req_deplace);
            $stmt         = $pdo->execute(array(
                ':perso_cod' => intval($perso_cod)
            ), $stmt);
            $retour       = $stmt->fetch();
            $result       = explode('#', $result['deplace']);
            $contenu_page .= $result[0];
            $contenu_page .= '<br />';
            if ($result[1] == 0)
            {
                $is_phrase = rand(1, 100);
                if ($is_phrase > 80)
                {
                    $is_phrase = rand(1, 100);
                    if ($is_phrase > 50)
                    {
                        include 'phrase.php';
                        $idx_phrase   = rand(1, 109);
                        $contenu_page .= '<p><em>' . $phrase[$idx_phrase] . '</em><br /><br />';
                    } else
                    {
                        $rumeur       = new rumeurs();
                        $contenu_page .= '<p><em>Rumeur :</em> ' . $rumeur->get_rumeur() . '<br />';
                    }
                }
            }
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            // on remet l'ancien temple si besoin

            $ptemple = new perso_temple();
            $temple  = $ptemple->getBy_ptemple_perso_cod($perso_cod)[0];

            if ($temple->ptemple_anc_pos_cod == 0)
            {
                $temple->efface();
                unset($temple);
            } else
            {
                $temple->ptemple_pos_cod = $temple->ptemple_anc_pos_cod;
                $temple->ptemple_nombre  = $temple->ptemple_anc_nombre;
                $temple->stocke();
            }

            break;
        case 'magie':
            /* On lance un sort */
            $sort_cod   = $_POST['sort_cod'];
            $cible      = $_POST['cible'];
            $type_lance = $_POST['type_lance'];
            if (!isset($sort_cod))
            {
                $contenu_page .= '<p>Erreur ! Sort non défini !';
                break;
            }
            if (!isset($cible))
            {
                $contenu_page .= '<p>Erreur ! Cible non définie !';
                break;
            }
            if (!isset($type_lance))
            {
                $contenu_page .= '<p>Erreur ! Type de lancer non défini !';
                break;
            }


            $perso_cible = new perso;
            $perso_cible->charge($cible);
            $logger->warning('Cible ' . print_r($perso_cible, true));
            $pos_cible = $perso_cible->get_position_object();

            $pos_sort_interdit = new pos_sort_interdit();


            if ($pos_sort_interdit->is_sort_interdit($sort_cod, $perso->get_position_object()->pos_cod))
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort depuis cette case !';
                break;
            }
            if ($pos_sort_interdit->is_sort_interdit($sort_cod, $pos_cible->pos_cod))
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort depuis cette case !';
                break;
            }


            if ($perso->perso_tangible != 'O')
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }

            $sort = new sorts;
            $sort->charge($sort_cod);

            if ($perso_cible->is_refuge() and $sort->sort_aggressif == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort agressif sur une cible résidant dans un lieu protégé !';
                break;
            }
            if ($perso_cod == $perso_cible->perso_cod and $sort->sort_aggressif == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer un sort aggressif sur vous même !';
                break;
            }
            $prefixe = 'nv_';
            if ($type_lance == 3)
            {
                $prefixe = 'dv_';
            }
            if ($type_lance == 5)
            {   // sort lancé avec un objet, on indique l'objet utilisé (on vérifiera que le sort lancé est bien celui de l'objet)
                $req    = 'select prepare_objets_sorts(:perso_cod,:objsort_cod,:sort_cod) as resultat; ';
                $stmt   = $pdo->prepare($req);
                $pdo->execute(
                    array(':perso_cod'      => $perso_cod,
                          ':objsort_cod'    => $_REQUEST["objsort_cod"],
                          ':sort_cod'       => $sort_cod), $stmt
                );
            }

            $req    = 'select ' . $prefixe . $sort->sort_fonction . '(:perso_cod,:cible,:type_lance) as resultat ';
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(
                array(':perso_cod'  => $perso_cod,
                      ':cible'      => $perso_cible->perso_cod,
                      ':type_lance' => $type_lance), $stmt
            );
            $result = $stmt->fetch();
            $contenu_page .= $result['resultat'];

            // on recharge le perso
            $perso->charge($perso_cod);

            // on regarde combien de sorts ont été lancé
            $pnbs = new perso_nb_sorts();
            $pnbs->getByPersoSort($perso_cod, $sort->sort_cod);

            // bouton de relance
            $sort_pa = $perso->get_cout_pa_magie($sort->sort_cod, $type_lance);
            if ($perso->perso_pa >= $sort_pa && ($pnbs->pnbs_nombre < 2 || is_null($pnbs->pnbs_nombre)))
            {
                $adds        = ($type_lance != 0) ? "" : "&fam_1=" . (1 * substr($sort->sort_combinaison, 0, 1)) . "&fam_2=" . (1 * substr($sort->sort_combinaison, 1, 1)) . "&fam_3=" . (1 * substr($sort->sort_combinaison, 2, 1)) . "&fam_4=" . (1 * substr($sort->sort_combinaison, 3, 1)) . "&fam_5=" . (1 * substr($sort->sort_combinaison, 4, 1)) . "&fam_6=" . (1 * substr($sort->sort_combinaison, 5, 1));
                $adds       .= ($type_lance != 5) ? "" : "&objsort_cod=".$_REQUEST["objsort_cod"];
                $contenu_page .= '<br><br><a href="choix_sort.php?&sort=' . $sort_cod . '&type_lance=' . $type_lance . $adds . '" class="centrer">Relancer (' . $sort_pa . ' PA)</a></center>';
            }

            if ($type_lance == 5)
            {   // On fait le menage maintenant que le sort a été lancé
             $req    = 'delete from objets_sorts_magie where objsortm_perso_cod = :perso_cod; ';
             $stmt   = $pdo->prepare($req);
             $pdo->execute(array(':perso_cod' => $perso_cod), $stmt );
            }
            break;

        case 'magie_case':

            if ($perso->is_refuge())
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort en étant sur un lieu protégé !';
                break;
            }


            if ($perso->perso_tangible != 'O')
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }


            $sort_cod = $_POST['sort_cod'];
            $logger->debug('Sort ' . $sort_cod);
            $position   = $_POST['position'];
            $type_lance = $_POST['type_lance'];

            $sort = new sorts;
            if (!$sort->charge($sort_cod))
            {
                $contenu_page .= '<p>Erreur ! Sort non défini !';
                break;
            }
            $pos = new positions();
            if (!$pos->charge($position))
            {
                $contenu_page .= '<p>Erreur ! Cible non définie !';
                break;
            }
            if (!isset($type_lance))
            {
                $contenu_page .= '<p>Erreur ! Type de lancer non défini !';
                break;
            }

            $prefixe = 'nv_';
            if ($type_lance == 3)
            {
                $prefixe = 'dv_';
            }

            // on regarde si on est sur un lieu protégé
            $lieu_protege = 'N';
            $lpos         = new lieu_position();
            $lpos->getByPos($pos->pos_cod);
            $lieu = new lieu();
            if ($lieu->charge($lpos->lpos_lieu_cod))
            {
                $lieu_protege = $lieu->lieu_refuge;
            }

            if ($lieu_protege == 'O' and $sort->sort_aggressif == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort agressif sur une cible résidant dans un lieu protégé !';
                break;
            }

            $pos_perso         = $perso->get_position_object();
            $pos_sort_interdit = new pos_sort_interdit();
            if ($pos_sort_interdit->is_sort_interdit($sort_cod, $pos_perso->pos_cod))
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort depuis cette case !';
                break;
            }
            if ($pos_sort_interdit->is_sort_interdit($sort_cod, $pos->pos_cod))
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort sur cette case !';
                break;
            }

            if ($type_lance == 5)
            {   // sort lancé avec un objet, on indique l'objet utilisé (on vérifiera que le sort lancé est bien celui de l'objet)
                $req    = 'select prepare_objets_sorts(:perso_cod,:objsort_cod,:sort_cod) as resultat; ';
                $stmt   = $pdo->prepare($req);
                $pdo->execute(
                    array(':perso_cod'      => $perso_cod,
                        ':objsort_cod'    => $_REQUEST["objsort_cod"],
                        ':sort_cod'       => $sort_cod), $stmt
                );
            }

            $req          = 'select ' . $prefixe . $sort->sort_fonction . '(:perso_cod,:cible,:type_lance) as resultat ';
            $stmt         = $pdo->prepare($req);
            $stmt         = $pdo->execute(
                array(':perso_cod'  => $perso_cod,
                      ':cible'      => $pos->pos_cod,
                      ':type_lance' => $type_lance), $stmt
            );
            $result       = $stmt->fetch();
            $contenu_page .= $result['resultat'];

            // on recharge le perso
            $perso->charge($perso_cod);

            // on regarde combien de sorts ont été lancé
            $pnbs = new perso_nb_sorts();
            $pnbs->getByPersoSort($perso_cod, $sort->sort_cod);

            // bouton de relance
            $sort_pa = $perso->get_cout_pa_magie($sort->sort_cod, $type_lance);
            if ($perso->perso_pa >= $sort_pa && ($pnbs->pnbs_nombre < 2 || is_null($pnbs->pnbs_nombre)))
            {
                $adds        = ($type_lance != 0) ? "" : "&fam_1=" . (1 * substr($sort->sort_combinaison, 0, 1)) . "&fam_2=" . (1 * substr($sort->sort_combinaison, 1, 1)) . "&fam_3=" . (1 * substr($sort->sort_combinaison, 2, 1)) . "&fam_4=" . (1 * substr($sort->sort_combinaison, 3, 1)) . "&fam_5=" . (1 * substr($sort->sort_combinaison, 4, 1)) . "&fam_6=" . (1 * substr($sort->sort_combinaison, 5, 1));
                $adds       .= ($type_lance != 5) ? "" : "&objsort_cod=".$_REQUEST["objsort_cod"];
                $contenu_page .= '<br><br><a href="choix_sort.php?&sort=' . $sort_cod . '&type_lance=' . $type_lance . $adds . '" class="centrer">Relancer (' . $sort_pa . ' PA)</a></center>';
            }

            if ($type_lance == 5)
            {   // On fait le menage maintenant que le sort a été lancé
                $req    = 'delete from objets_sorts_magie where objsortm_perso_cod = :perso_cod; ';
                $stmt   = $pdo->prepare($req);
                $pdo->execute(array(':perso_cod' => $perso_cod), $stmt );
            }

            break;
        case 'voie_magique':
            $vm = new voie_magique();
            if (!empty($perso->perso_voie_magique))
            {

                $vm->charge($perso->perso_voie_magique);
                $contenu_page .= 'ERREUR: Vous aviez déjà choisi une voie magique: ' . $vm->mvoie_libelle;
            } else
            {
                if (!isset($_POST['voie']))
                {
                    $voie = -1;  // Erreur
                } else
                {
                    $voie = $_POST['voie'];
                }
                if (!$vm->charge($voie))
                {
                    $contenu_page .= 'ERREUR: Voie magique choisie non spécifiée ou inconnue';
                } else
                {
                    $perso->perso_voie_magique = $voie;
                    $perso->stocke();
                    $contenu_page .= 'Vous avez choisi la voie magique: ' . $vm->mvoie_libelle;
                }

            }

            break;

        case 'desengagement':
            switch ($_REQUEST['valide'])
            {
                case 'N':
                    $contenu_page .= '<p>Le désengagement permet de détruire tous les blocages de combat (offensifs ou défensifs) avec une cible déterminée.<br />';
                    $contenu_page .= '<p>Voulez-vous continuer ?<br />';
                    $contenu_page .= '<a href="action.php?methode=desengagement&valide=O&cible=$cible">Oui, je veux me désengager !</a><br />';
                    $contenu_page .= '<a href="etat.php">Non, je ne souhaite pas me désengager !</a><br />';
                    break;
                case 'O':
                    $contenu_page .= $perso->desengagement($_REQUEST['cible']);
                    break;
            }
            break;
        case 'revolution':
            $contenu_page .= $perso->cree_revolution($_POST['cible']);
            break;
        case 'vote_guilde':
            $contenu_page .= $perso->vote_revolution($_POST['revguilde_cod'], $_POST['vote']);
            break;
        case 'utilise_potion':
            $gobj_cod = 0 ;

            // Charger l'objet pour récupérer son cod générique
            $o = new perso_objets();
            if ($o->getByPersoObjet($perso_cod, $_POST["obj_cod"]))
            {
                $potion = new objets();
                $potion->charge($_REQUEST["obj_cod"]);
                $gobj_cod = $potion->obj_gobj_cod ;
            }

            $req = 'select fpot_fonction from potions.fonction_potion where fpot_gobj_cod = :fpot_gobj_cod ' ;
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute( array(':fpot_gobj_cod' =>$gobj_cod), $stmt);
            if (!$result = $stmt->fetch())
            {
                $contenu_page .= 'Erreur sur la fonction appelée.';
            }
            else
            {
                $fonction = $result['fpot_fonction'] ;
                $req = 'select potions.' . $fonction . '(:perso_cod, :cible) as resultat';
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute( array(':perso_cod' => $perso_cod, ':cible' => $_POST['cible']), $stmt);
                if ($result = $stmt->fetch())
                {
                    $contenu_page .= $result['resultat'].'<br><br>';
                }
            }
            break;
        case 'rituel_modif_caracs':
            if (((int)$_POST['diminution']<=0) || ((int)$_POST['amelioration']<=0))
            {
                $contenu_page .= "Pour faire le rituel, vous devez choisir les 2 caractéristiques!<br><br>";
            }
            else
            {
                $contenu_page .= $perso->rituel_modif_caracs((int)$_POST['diminution'],(int)$_POST['amelioration']);
            }
            $contenu_page .= '<br><a href="frame_vue.php" class="centrer">Retour</a>';
            break;
        case 'rituel_modif_voiemagique':
            if ((int)$_POST['mvoie_cod']<=0)
            {
                $contenu_page .= "Pour faire le rituel de modification de voie, vous devez choisir une nouvelle voie!<br><br>";
            }
            else
            {
                $contenu_page .= $perso->rituel_modif_voiemagique((int)$_POST['mvoie_cod']);
            }
            $contenu_page .= '<br><a href="frame_vue.php" class="centrer">Retour</a>';
            break;
        case 'passe_niveau':
            $contenu_page .= $perso->passe_niveau($_POST['amelioration']);
            $contenu_page .= '<a href="index.php" class="centrer">Retour</a>';
            break;
        case 'depose_objet':
            $contenu_page .= $perso->depose_objet($objet);
            break;
        case 'vente_bat':
            $contenu_page .= $perso->vente_bat($objet);
            break;
        case 'nv_magasin_achat':
            $lieu = $_POST['lieu'];
            $sm   = new stock_magasin();
            //$sm->getBy_mstock_lieu_cod($lieu)[0];
            foreach ($gobj as $key => $val)
            {
                if ($val != 0)
                {
                    $type = explode('-', $key);

                    if ($type[2]=="generique")
                    {
                        // Cas des objets achetés dans les stocks de génériques
                        $gobj         = $type[0];
                        $qte          = $val;
                        $contenu_page .= $perso->magasin_achat_generique($lieu, $gobj, $qte);
                    }
                    else
                    {
                        // Cas des objets achetés dans les stocks standards
                        $gobj         = $type[0];
                        $qte          = $val;
                        $bonus        = $type[1];
                        $liste_objets = $sm->get_objets($lieu, $gobj, $bonus, $qte);

                        if (count($liste_objets) == 0)
                        {
                            $contenu_page .= '<p>Erreur, pas d’objet trouvé dans le magasin pour ' . $key;
                        } else
                        {
                            foreach ($liste_objets as $objet)
                            {
                                $contenu_page .= '<p>pour l’objet : <strong>' . $objet->obj_nom . '</strong>';
                                $contenu_page .= $perso->magasin_achat($lieu, $objet->obj_cod);
                            }
                        }
                    }

                }
            }

            break;
        case 'nv_magasin_vente':
            $lieu = $_POST['lieu'];
            foreach ($obj as $key => $val)
            {
                if ($_POST['stock'][$key]!="")
                {
                    //pour les runes (voir echoppe_magie) on converti les objet bvers un stock de générique
                    $contenu_page .= $perso->magasin_vente_generique($lieu, $key);
                }
                else
                {
                    // Vente normale!
                    $contenu_page .= $perso->magasin_vente($lieu, $key);
                }
            }
            break;
        case 'magasin_identifie':
            $contenu_page .= $perso->magasin_identifie($_POST['lieu'], $_POST['objet']);
            break;
        case 'nv_magasin_identifie':
            $lieu = $_POST['lieu'];
            foreach ($obj as $key => $val)
            {
                $contenu_page .= $perso->magasin_identifie($_POST['lieu'], $key);
            }
            break;
        case 'nv_magasin_repare':
            foreach ($obj as $key => $val)
            {
                $contenu_page .= $perso->magasin_repare($_POST['lieu'], $key);
            }
            break;
        case 'magasin_repare':
            $contenu_page .= $contenu_page .= $perso->magasin_repare($_POST['lieu'], $_POST['objet']);
            break;
        case 'repare':
            $type_rep[1] = 'arme';
            $type_rep[2] = 'armure';
            $type_rep[4] = 'casque';
            $autorise    = 0;

            $objet = new objets();
            $objet->charge($_REQUEST['objet']);

            $objet_generique = new objet_generique();
            $objet_generique->charge($objet->obj_gobj_cod);

            if ($objet_generique->gobj_tobj_cod != $type)
            {
                $trace               = new trace2();
                $trace->trace2_texte = $perso_cod;
                $trace->stocke(true);
            }

            $erreur = false;
            $perobj = new perso_objets();
            if (!$perobj->getByPersoObjet($perso_cod, $objet->obj_cod))
            {
                $contenu_page .= 'Erreur sur le chargement du perso/objet';
                $erreur       = true;
            } else
            {
                if ($perobj->perobj_identifie != 'O')
                {
                    $contenu_page .= 'Vous ne pouvez pas réparer un objet non identifié';
                }
                if (($perobj->perobj_equipe == 'N') && ($type == 2 || $type == 4))
                {
                    $autorise = 1;
                }
                if ($type == 1)
                {
                    $autorise = 1;
                }

            }

            if ($autorise == 1)
            {
                $contenu_page .= $perso->repare_objet($type_rep[$type], $objet->obj_cod);
            }
            $contenu_page .= '<a class="centrer" href="inventaire.php">Retour à l’inventaire</a>';
            break;
        case 'receptacle':
            if ($perso->is_refuge() == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort en étant sur un lieu protégé !';
                break;
            }
            if ($perso->perso_tangible != 'O')
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }
            $contenu_page .= $perso->cree_receptacle($_REQUEST['sort'], $_REQUEST['type_lance']);
            break;
        case 'enluminure':
            if ($perso->is_refuge() == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort en étant sur un lieu protégé !';
                break;
            }
            if ($perso->perso_tangible != 'O')
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }
            $contenu_page .= $perso->cree_parchemin($_REQUEST['sort'], $_REQUEST['type_lance']);
            break;
        case 'milice_tel':
            $contenu_page .= $perso->milice_tel($_REQUEST['destination']);
            break;
        case 'prie':
            if (!isset($_REQUEST['dieu']))
            {
                $contenu_page .= '<p>Erreur ! Dieu non définie !';
                break;
            }
            $contenu_page .= $perso->prie_dieu($_REQUEST['dieu']);
            break;
        case 'prie_ext':
            if (!isset($_REQUEST['dieu']))
            {
                $contenu_page .= '<p>Erreur ! Dieu non définie !';
                break;
            }
            $contenu_page .= $perso->prie_dieu_ext($_REQUEST['dieu']);
            break;
        case 'ceremonie':
            $contenu_page .= $perso->ceremonie_dieu($_REQUEST['dieu']);
            break;
        case 'dgrade':
            $contenu_page .= $perso->change_grade($_REQUEST['dieu']);
            break;
        case 'don_br':
            $contenu_page .= $perso->don_br($_REQUEST['dest'], $_REQUEST['qte']);
            break;
        case 'vente_auberge':
            $contenu_page .= $perso->vente_auberge($_REQUEST['objet']);
            break;
        case 'achat_objet':
            $contenu_page .= $perso->achete_objet($_REQUEST['objet']);
            break;
        case 'redist':
            $contenu_page .= $perso->start_redispatch();
            break;
        case 'mode_combat':
            $contenu_page .= $perso->change_mode_combat($_REQUEST['mode']);
            break;
        case 'niveau_redist':
            $contenu_page .= $perso->detail_redispatch($_POST['amelioration']);
            $contenu_page .= '<a href="niveau_redist.php" class="centrer">Retour</a>';
            break;
        case 'embr':
            $contenu_page .= $perso->embr($_POST['cible']);
            break;
        case 'ouvre_cadeau':
            $contenu_page .= $perso->ouvre_cadeau();
            break;
        case 'don_cadeau_rouge':
            $contenu_page .= $perso->donne_rouge();
            break;
        case 'don_cadeau_rougeX10':
            for ($i = 0; $i < 10; $i++)
            {
                $contenu_page .= $perso->donne_rouge();
            }
            break;
        case 'don_cadeau_noir':
            $contenu_page .= $perso->donne_noir();
            break;
        case 'donne_bonbon':
            $contenu_page .= $perso->donne_bonbon($_REQUEST['cible']);
            break;
        case 'teld':
            $contenu_page .= $perso->teleportation_divine($_REQUEST['pos']);
            break;
        case 'offre_boire':
            $contenu_page .= $perso->offre_boire($_REQUEST['cible']);
            break;
        case 'enc':
            $contenu_page .= $perso->f_enchantement($_REQUEST['obj'], $_REQUEST['enc'], $_REQUEST['type_appel']);
            break;
        case 'cree_groupe':
            $contenu_page .= $perso->cree_groupe($_REQUEST['nom_groupe']);
            $contenu_page .= '<br /><a class="centrer" href="groupe.php">Retour à la gestion de la coterie</a>';
            break;
        case 'regle_groupe':
            $contenu_page .= $perso->regle_groupe($_REQUEST['pa'],
                $_REQUEST['pv'],
                $_REQUEST['dlt'],
                $_REQUEST['bonus'],
                $_REQUEST['messages'],
                $_REQUEST['messagemort'],
                $_REQUEST['champions']);
            $contenu_page .= '<br /><a class="centrer" href="groupe.php">Retour à la gestion de la coterie</a>';
            break;
        case 'invite_groupe':
            $erreur       = 0;
            $tab_dest     = explode(";", $dest);
            $nb_dest      = count($tab_dest);
            $nb_vrai_dest = 0;
            for ($cpt = 0; $cpt < $nb_dest; $cpt++)
            {
                if ($tab_dest[$cpt] != "")
                {
                    $nb_vrai_dest = $nb_vrai_dest + 1;
                }
                if ($nb_vrai_dest == 0)
                {
                    $contenu_page .= '<br><br><p><strong>********* Vous devez renseigner au moins un membre de coterie ! *********</strong><br><br>';
                    $erreur       = 1;
                }
                if ($erreur == 0)
                {
                    // on cherche le destinataire
                    if ($tab_dest[$cpt] != "")
                    {
                        if ($invite = $perso->f_cherche_perso($tab_dest[$cpt]))
                        {
                            $contenu_page .= '<br><br>' . $perso->invite_groupe($groupe, $invite->perso_cod);
                        }
                    }
                }
            }
            $contenu_page .= '<p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
            break;
        case 'accinv':
            $contenu_page .= $perso->accepte_invitation($_REQUEST['g']);
            $contenu_page .= '<br /><a class="centrer" href="groupe.php">Retour à la gestion de la coterie</a>';
            break;
        case 'refinv':
            $contenu_page .= $perso->refuse_invitation($_REQUEST['g']);
            $contenu_page .= '<br /><a class="centrer" href="groupe.php">Retour à la gestion de la coterie</a>';
            break;
        case 'abtemp':
            $contenu_page .= 'En continuant, vous abandonnerez le dispensaire qui vous ramenait en cas de mort.<br />
			<a href="' . $PHP_SELF . '?methode=abtemp2">Cliquez ici pour continuer</a>';
            break;
        case 'abtemp2':
            $ptemple = new perso_temple();
            $ptemple = $ptemple->getBy_ptemple_perso_cod($perso_cod)[0];
            $ptemple->efface();
            $contenu_page .= 'Vous n’avez plus de dispensaire spécifique pour vous ramener en cas de mort.';
            break;
        case 'retour_plan': // Retour du perso dans son plan d'origine
            // On vérifie qu'il est sur un bâtiment administratif
            if ($perso->perso_pa != 12)
            {
                $contenu_page .= "Vous n'avez pas assez de PA pour cette action.";
            } else
            {
                if ($lieu = $perso->get_lieu())
                {
                    if ($lieu['lieu_type']->tlieu_cod != 9)
                    {
                        $contenu_page .= "Vous n'êtes pas sur un bâtiment administratif";
                    } else
                    {
                        $ppp = new perso_plan_parallele();
                        $ppp->$charge($perso_cod);

                        $perso_pos = new perso_position();
                        $perso_pos->getByPerso($perso_cod);
                        $perso_pos->ppos_pos_cod = $ppp->ppp_pos_cod;
                        $perso_cod->stocke();

                        $ppp->delete();

                        $perso->perso_pa = 0;
                        $perso->stocke();

                        unset($perso);
                        $perso->charge($perso_cod);

                        $contenu_page .= 'Bon retour dans les souterrains !';
                    }

                }
            }
          break;

        /* Quête de la construction de la cathédrale de Balgur au -3 */
        case 'batir':
            /* Vérif position */
            $position = $perso->get_position_object();
            if ($position->pos_cod == 7327)
            {
                /* Vérif des PA */
                if ($perso->perso_pa >= 6)
                {
                    /* Vérif pioche */
                    $req_matos = "select count(perobj_cod) as nbobj from perso_objets, objets 
						where perobj_obj_cod = obj_cod 
						and obj_gobj_cod=332 
						and perobj_perso_cod=" . $perso_cod . " 
						and perobj_equipe='O' ";
                    $db->query($req_matos);
                    $db->next_record();
                    $matos = $db->f('nbobj');
                    if ($matos)
                    {
                        /* Action */
                        $req = 'update dieu set dieu_pouvoir = (dieu_pouvoir + 10) where dieu_cod = 2';
                        $db->query($req);
                        $req = 'update perso set perso_pa = (perso_pa - 6), perso_px = perso_px + 0.25 where perso_cod = ' . $perso_cod;
                        $db->query($req);
                        $req = 'insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant)'
                            . 'values(nextval(\'seq_levt_cod\'),89,now(),1,' . $perso_cod . ',\'[perso_cod1] a travaillé.\',\'O\',\'O\',' . $perso_cod . ')';
                        $db->query($req);
                        $contenu_page .= 'La construction du bâtiment a progressé.<br>Votre dieu a gagné en puissance.<br>Vous gagnez 0.25px.';
                    } else
                    {
                        $contenu_page .= 'Vous n’avez pas de pioche équipée.';
                    }
                } else
                {
                    $contenu_page .= 'Vous n’avez pas assez de PA pour cette action.';
                }
            } else
            {
                $contenu_page .= 'Vous n’êtes pas sur le chantier de la cathédrale.';
            }
            break;
        /* Fin modif pour la quête de Balgur */

        default :
            /* si aucune methode n'est passée..... */
            $contenu_page .= '<p>Erreur : action non définie !';
            break;
    }
} else
{
    $contenu_page .= '<p>Vous ne pouvez pas valider des actions en étant administrateur !';
}

if (!$inc_vue)
{
    include "blocks/_footer_page_jeu.php";
}