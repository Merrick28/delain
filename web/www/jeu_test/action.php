<?php
include_once "verif_connexion.php";
include_once '../includes/template.inc';

if (!isset($methode))
{
    $methode = '';
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
//
//Contenu de la div de droite
//
$contenu_page = '';
if (!$db->is_admin($compt_cod) || ($db->is_admin_monstre($compt_cod) && ($db->is_monstre($perso_cod) || $db->is_pnj($perso_cod))))
{

    switch ($methode)
    {
        case 'attaque2':
            /* on porte une attaque */
            $arme_dist = $db->arme_distance($perso_cod);
            if (isset($_POST['cible']))
                $cible     = $_POST['cible'];
            if (isset($_GET['cible']))
                $cible     = $_GET['cible'];

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

            $pa_n = $db->get_pa_attaque($perso_cod);
            $pa_f = $db->get_pa_foudre($perso_cod);
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
            $req        = 'select attaque(' . $perso_cod . ',' . $cible . ',' . $type_at . ') as resultat';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            $contenu_page .= '
			<form name="attaquer1" method="post" action="action.php">
			<input type="hidden" name="ctl" value="0">
			<input type="hidden" name="methode" value="attaque2">
			<input type="hidden" name="type_at" value="' . $type_at . '">
			<input type="hidden" name="type" value="1">';
            $attaquable = 1;
            //on regarde pour le nombre de PA

            $req = 'select perso_pa from perso where perso_cod = ' . $perso_cod;
            $db->query($req);
            $db->next_record();
            $pa  = $db->f('perso_pa');

            if ($pa < $pa_at[$type_at])
            {
                $attaquable = 0;
            }
            // on regarde pour la distance de la cible
            $req = 'select perso_tangible from perso where perso_cod = ' . $cible;
            $db->query($req);
            $db->next_record();
            if ($db->f('perso_tangible') == 'N')
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
            include ('inc_competence_combat.php');
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
            $req_ramasser = 'select ramasse_' . $objet[$type_objet] . '(' . $perso_cod . ',' . $num_objet . ') as resultat';
            $db->query($req_ramasser);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;

        case 'deplacement':
            /* On se déplace */
            $req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
            $db->query($req);
            $db->next_record();
            if ($db->f('perso_type_perso') == 3)
            {
                $contenu_page .= '<p>Erreur ! Un familier ne peut pas se déplacer seul !</p>';
                break;
            }
            if (isset($_POST['position']))
                $position = $_POST['position'];
            if (isset($_GET['position']))
                $position = $_GET['position'];
            if (!isset($position) || $position === '')
            {
                $contenu_page .= '<p>Erreur ! Position non définie !</p>';
                break;
            }
            $req_deplace = 'select deplace_code(?,?) as deplace';
           
            $stmt = $pdo->prepare($req_deplace);
            $stmt = $pdo->execute(array(intval($perso_cod),intval($position)),$stmt);
            $retour = $stmt->fetch();
            //$db->query($req_deplace);
            
            
            //$db->next_record();
            $result      = explode('#', $retour['deplace']);
            $page_retour = 'frame_vue.php';

            $retour      = '';
            if ($menu_deplacement !== '')
            {
                $page_retour = 'deplacement.php';
                $retour      = '<hr /><p><a href="' . $page_retour . '">Retour !</a></p>';
            }
            $contenu_page .= $result[1];

            if (strpos($result[1], 'Erreur') !== 0)
            {
                $is_phrase = rand(1, 100);
                if ($is_phrase < 34)
                {
                    $req = 'select choix_rumeur() as rumeur ';
                    $db->query($req);
                    $db->next_record();
                    $contenu_page .= '<hr /><p><i>Rumeur :</i> ' . $db->f('rumeur') . '</p>';
                }
                else if ($is_phrase < 67)
                {
                    include 'phrase.php';
                    $idx_phrase = rand(1, sizeof($phrase));
                    $contenu_page .= '<hr /><p><i>' . $phrase[$idx_phrase] . '</i></p>';
                }
                else
                {
                    $req = "select indice_lieu($position) as indice";
                    $db->query($req);
                    $db->next_record();
                    $contenu_page .= '<hr /><p>Sur le sol est gravé un indice qui pourrait être fort utile : <br /><i>' . $db->f('indice') . '</i></p>';
                }
            }
            $contenu_page .= $retour;
            if ($menu_deplacement === '')
            {
                include('frame_vue.php');
                //header('Location:' . $type_flux.G_URL . 'jeu_test/' . $page_retour);
            }
            break;
        case "passage":
            /* On se déplace */
            $req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
            $db->query($req);
            $db->next_record();
            if ($db->f('perso_type_perso') == 3)
            {
                $contenu_page .= '<p>Erreur ! Un familier ne peut pas se déplacer seul !';
                break;
            }
            $req_deplace = 'select passage(' . $perso_cod . ') as deplace';
            $db->query($req_deplace);
            $db->next_record();
            $result      = explode('#', $db->f('deplace'));
            $contenu_page .= $result[0];
            $contenu_page .= '<br />';
            if ($result[1] == 0)
            {
                $is_phrase = rand(1, 100);
                if ($is_phrase < 34)
                {
                    $req = 'select choix_rumeur() as rumeur ';
                    $db->query($req);
                    $db->next_record();
                    $contenu_page .= '<hr /><p><i>Rumeur :</i> ' . $db->f('rumeur') . '</p>';
                }
                else if ($is_phrase < 67)
                {
                    include 'phrase.php';
                    $idx_phrase = rand(1, sizeof($phrase));
                    $contenu_page .= '<hr /><p><i>' . $phrase[$idx_phrase] . '</i></p>';
                }
                else
                {
                    $req = "select indice_lieu(ppos_pos_cod) as indice from perso_position where ppos_perso_cod=$perso_cod";
                    $db->query($req);
                    $db->next_record();
                    $contenu_page .= '<hr /><p>Sur le sol est gravé un indice qui pourrait être fort utile : <br /><i>' . $db->f('indice') . '</i></p>';
                }
            }
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;
        case "sortie_arene":

            $req    = 'select sortir_arene(' . $perso_cod . ') as res';
            $db->query($req);
            $db->next_record();
            $result = explode(';', $db->f('res'));
            $contenu_page .= $result[1];
            $contenu_page .= '<br /><br />';
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;
        case "sortir_donjon":

            $req    = 'select sortir_donjon(' . $perso_cod . ') as res';
            $db->query($req);
            $db->next_record();
            $result = explode(';', $db->f('res'));
            $contenu_page .= $result[1];
            $contenu_page .= '<br /><br />';
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;
        case "enreg_pos_donjon":

            $req    = 'select enregistre_avancee_donjon(' . $perso_cod . ') as res';
            $db->query($req);
            $db->next_record();
            $result = explode(';', $db->f('res'));
            $contenu_page .= $result[1];
            $contenu_page .= '<br /><br />';
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            break;

        case 'passage_prison':
            /* On se déplace */
            $req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
            $db->query($req);
            $db->next_record();
            if ($db->f('perso_type_perso') == 3)
            {
                $contenu_page .= '<p>Erreur ! Un familier ne peut pas se déplacer seul !';
                break;
            }
            $req_deplace = 'select passage(' . $perso_cod . ') as deplace';
            $db->query($req_deplace);
            $db->next_record();
            $result      = explode('#', $db->f('deplace'));
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
                        $idx_phrase = rand(1, 109);
                        $contenu_page .= '<p><i>' . $phrase[$idx_phrase] . '</i><br /><br />';
                    }
                    else
                    {
                        $req = 'select choix_rumeur() as rumeur ';
                        $db->query($req);
                        $db->next_record();
                        $contenu_page .= '<p><i>Rumeur :</i> ' . $db->f('rumeur') . '<br />';
                    }
                }
            }
            $contenu_page .= '<a href="frame_vue.php">Retour !</a></p>';
            // on remet l'ancien temple si besoin
            $req = 'select ptemple_anc_pos_cod from perso_temple where ptemple_perso_cod = ' . $perso_cod;
            $db->query($req);
            $db->next_record();
            if ($db->f('ptemple_anc_pos_cod') == 0)
            {
                $req = 'delete from perso_temple where ptemple_perso_cod = ' . $perso_cod;
            }
            else
            {
                $req = 'update perso_temple set ptemple_pos_cod = ptemple_anc_pos_cod,ptemple_nombre = ptemple_anc_nombre ';
                $req = $req . 'where ptemple_perso_cod = ' . $perso_cod;
            }
            $db->query($req);
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

            $tab_cible     = $db->get_pos($cible);

//Ajout Teruo 22/01/2016: test recherche tableau sort interdit
            $tab_pos     = $db->get_pos($perso_cod);
            $req            = 'select sinterd_pos_cod, sinterd_sort_cod from pos_sort_interdit where ' . $sort_cod . ' in (sinterd_sort_cod, 0) and sinterd_pos_cod = ' . $tab_pos['pos_cod'] ;
	  
            $db->query($req);
            if ($db->nf() != 0)
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort depuis cette case !';
                break;
            }



            $req            = 'select sinterd_pos_cod, sinterd_sort_cod from pos_sort_interdit where ' . $sort_cod . ' in (sinterd_sort_cod, 0) and sinterd_pos_cod = ' . $tab_cible['pos_cod'] ;
            $db->query($req);
            if ($db->nf() != 0)
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort sur cette case !';
                break;
            }

//Fin ajout

            if ($db->is_intangible($perso_cod))
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }
            $req            = 'select sort_fonction,sort_soi_meme,sort_aggressif from sorts where sort_cod = ' . $sort_cod;
            $db->query($req);
            $db->next_record();
            $sort_soi_meme  = $db->f('sort_soi_meme');
            $fonction       = $db->f('sort_fonction');
            $sort_aggressif = $db->f('sort_aggressif');
            $tab_lieu       = $db->get_lieu($cible);
            $lieu_protege   = $tab_lieu['lieu_refuge'];
            if ($lieu_protege == 'O' and $sort_aggressif == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort agressif sur une cible résidant dans un lieu protégé !';
                break;
            }
            if ($perso_cod == $cible and $sort_aggressif == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer un sort aggressif sur vous même !';
                break;
            }
            $prefixe = 'nv_';
            if ($type_lance == 3)
            {
                $prefixe = 'dv_';
            }
            $req = 'select ' . $prefixe . $fonction . '(' . $perso_cod . ',' . $cible . ',' . $type_lance . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;

        case 'magie_case':
            $tab_lieu     = $db->get_lieu($perso_cod);
            $lieu_protege = $tab_lieu['lieu_refuge'];
            if ($lieu_protege == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort en étant sur un lieu protégé !';
                break;
            }
            $sort_cod   = $_POST['sort_cod'];
            $position   = $_POST['position'];
            $type_lance = $_POST['type_lance'];
            if ($db->is_intangible($perso_cod))
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }
            if (!isset($sort_cod))
            {
                $contenu_page .= '<p>Erreur ! Sort non défini !';
                break;
            }
            if (!isset($position))
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
            $lieu_protege   = 'N';
            $req            = 'select lieu_refuge from lieu,lieu_position,positions
				where pos_cod = ' . $position . '
					and lpos_pos_cod = pos_cod
					and lpos_lieu_cod = lieu_cod';
            $db->query($req);
            if ($db->next_record())
                $lieu_protege   = $db->f('lieu_refuge');
            $req            = 'select sort_fonction,sort_aggressif from sorts where sort_cod = ' . $sort_cod;
            $db->query($req);
            $db->next_record();
            $fonction       = $db->f('sort_fonction');
            $sort_aggressif = $db->f('sort_aggressif');
            if ($lieu_protege == 'O' and $sort_aggressif == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort agressif sur une cible résidant dans un lieu protégé !';
                break;
            }

//Ajout Teruo 22/01/2016: test recherche tableau sort interdit
            $tab_pos     = $db->get_pos($perso_cod);
            $req            = 'select sinterd_pos_cod, sinterd_sort_cod from pos_sort_interdit where ' . $sort_cod . ' in (sinterd_sort_cod, 0) and sinterd_pos_cod = ' . $tab_pos['pos_cod'] ;
	  
            $db->query($req);
            if ($db->nf() != 0)
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort depuis cette case !';
                break;
            }



            $req            = 'select sinterd_pos_cod, sinterd_sort_cod from pos_sort_interdit where ' . $sort_cod . ' in (sinterd_sort_cod, 0) and sinterd_pos_cod = ' . $position ;
            $db->query($req);
            if ($db->nf() != 0)
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer ce sort sur cette case !';
                break;
            }

//Fin ajout


            $req = 'select ' . $prefixe . $fonction . '(' . $perso_cod . ',' . $position . ',' . $type_lance . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'voie_magique':
            $req = 'select perso_voie_magique, mvoie_libelle from perso, voie_magique
                where perso_voie_magique = mvoie_cod and perso_cod = ' . $perso_cod;
            $db->query($req);
            if ($db->nf())
            {
                $contenu_page .= 'ERREUR: Vous aviez déjà choisi une voie magique: ' . $db->f('mvoie_libelle');
            }
            else
            {
                if (!isset($_POST['voie']))
                    $voie = -1;  // Erreur
                else
                    $voie = $_POST['voie'];
                $req  = 'select mvoie_libelle from voie_magique where mvoie_cod = ' . $voie;
                $db->query($req);
                if ($db->nf())
                {
                    $db->next_record();
                    $contenu_page .= 'Vous avez choisi la voie magique: ' . $db->f('mvoie_libelle');
                    $db->query('update perso set perso_voie_magique = ' . $voie . ' where perso_cod = ' . $perso_cod);
                }
                else
                {
                    $contenu_page .= 'ERREUR: Voie magique choisie non spécifiée ou inconnue';
                }
            }
            break;

        case 'desengagement':
            switch ($valide)
            {
                case 'N':
                    $contenu_page .= '<p>Le désengagement permet de détruire tous les blocages de combat (offensifs ou défensifs) avec une cible déterminée.<br />';
                    $contenu_page .= '<p>Voulez-vous continuer ?<br />';
                    $contenu_page .= '<a href="action.php?methode=desengagement&valide=O&cible=$cible">Oui, je veux me désengager !</a><br />';
                    $contenu_page .= '<a href="etat.php">Non, je ne souhaite pas me désengager !</a><br />';
                    break;
                case 'O':
                    $req = 'select desengagement(' . $perso_cod . ',' . $cible . ') as resultat ';
                    $db->query($req);
                    $db->next_record();
                    $contenu_page .= $db->f('resultat');
                    break;
            }
            break;
        case 'revolution':
            $cible         = $_POST['cible'];
            $req           = 'select cree_revolution(' . $perso_cod . ',' . $cible . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'vote_guilde':
            $vote          = $_POST['vote'];
            $revguilde_cod = $_POST['revguilde_cod'];
            $req           = 'select vote_revolution(' . $perso_cod . ',' . $revguilde_cod . ',\'' . $vote . '\') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'passe_niveau':
            $amelioration  = $_POST['amelioration'];
            if (!isset($amelioration) || $amelioration === '')
            {
                break;
            }
            $req   = 'select f_passe_niveau(' . $perso_cod . ',' . $amelioration . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            $contenu_page .= '<center><a href="index.php">Retour</a></center>';
            break;
        case 'depose_objet':
            $req   = 'select depose_objet(' . $perso_cod . ',' . $objet . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'vente_bat':
            $objet = $_POST['objet'];
            $req   = 'select vente_bat(' . $perso_cod . ',' . $objet . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'nv_magasin_achat':
            $db2   = new base_delain;
            $lieu  = $_POST['lieu'];
            foreach ($gobj as $key => $val)
            {
                if ($val != 0)
                {
                    $type = explode('-', $key);
                    // on cherche l'objet kivabien dans le magasin
                    $req  = 'select obj_cod,obj_nom ';
                    $req  = $req . 'from objets,stock_magasin,objet_generique ';
                    $req  = $req . 'where mstock_lieu_cod = ' . $lieu;
                    $req  = $req . 'and mstock_obj_cod = obj_cod ';
                    $req  = $req . 'and obj_gobj_cod = ' . $type[0];
                    $req  = $req . 'and coalesce(obj_obon_cod,0) = ' . $type[1];
                    $req  = $req . 'and obj_gobj_cod = gobj_cod ';
                    $req  = $req . 'limit ' . $val;
                    $db->query($req);
                    if ($db->nf() == 0)
                    {
                        $contenu_page .= '<p>Erreur, pas d’objet trouvé dans le magasin pour ' . $key;
                    }
                    else
                    {
                        while ($db->next_record())
                        {
                            $contenu_page .= '<p>pour l’objet : <b>' . $db->f('obj_nom') . '</b>';
                            $req = 'select magasin_achat(' . $perso_cod . ',' . $lieu . ',' . $db->f('obj_cod') . ') as resultat ';
                            $db2->query($req);
                            $db2->next_record();
                            $contenu_page .= $db2->f('resultat');
                        }
                    }
                }
            }
            /* Ancienne version certainement. Je laisse pour l'instant, mais c'est plus un code pollueur qu'autre chose
              foreach($gobj as $key=>$val)
              {
              if ($val != 0)
              {
              $req = 'select obj_nom from objets where obj_cod = ' . $key;
              $db->query($req);
              $db->next_record();
              $contenu_page .= '<p>pour l\'objet : <b>' . $db->f('obj_nom') . '</b>';
              $req = 'select magasin_achat(' . $perso_cod . ',' . $lieu . ',' . $key . ') as resultat ';
              $db2->query($req);
              $db2->next_record();
              $contenu_page .= $db2->f('resultat');
              }
              } */
            break;
        case 'nv_magasin_vente':
            $lieu = $_POST['lieu'];
            foreach ($obj as $key => $val)
            {
                $req = 'select magasin_vente(' . $perso_cod . ',' . $lieu . ',' . $key . ') as resultat ';
                $db->query($req);
                $db->next_record();
                $contenu_page .= $db->f('resultat');
            }
            break;
        case 'magasin_identifie':
            $objet = $_POST['objet'];
            $lieu  = $_POST['lieu'];
            $req   = 'select magasin_identifie(' . $perso_cod . ',' . $lieu . ',' . $objet . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'nv_magasin_identifie':
            $lieu  = $_POST['lieu'];
            foreach ($obj as $key => $val)
            {
                $req = 'select magasin_identifie(' . $perso_cod . ',' . $lieu . ',' . $key . ') as resultat ';
                $db->query($req);
                $db->next_record();
                $contenu_page .= $db->f('resultat');
            }
            break;
        case 'nv_magasin_repare':
            $lieu = $_POST['lieu'];
            foreach ($obj as $key => $val)
            {
                $req = 'select magasin_repare(' . $perso_cod . ',' . $lieu . ',' . $key . ') as resultat ';
                $db->query($req);
                $db->next_record();
                $contenu_page .= $db->f('resultat');
            }
            break;
        case 'magasin_repare':
            $objet         = $_POST['objet'];
            $lieu          = $_POST['lieu'];
            $req           = 'select magasin_repare(' . $perso_cod . ',' . $lieu . ',' . $objet . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'repare':
            $type_rep[1]   = 'arme';
            $type_rep[2]   = 'armure';
            $type_rep[4]   = 'casque';
            $autorise      = 0;
            $query_val     = "select gobj_tobj_cod
						from objets,objet_generique
						where obj_gobj_cod = gobj_cod
						and obj_cod = " . $objet;
            $db->query($query_val);
            $db->next_record();
            $type_controle = $db->f('gobj_tobj_cod');
            if ($type_controle != $type)
            {
                $query_val = "insert into trace2 (trace2_texte) values ($perso_cod)";
                $db->query($query_val);
            }
            $query_val = "select perobj_cod
						from perso_objets
						where perobj_perso_cod = " . $perso_cod . "
						and perobj_obj_cod = " . $objet . "
						and perobj_identifie = 'O'
						and ( (perobj_equipe = 'N' and " . $type . " in (2,4) )
						      or ( " . $type . " = 1)
							)";
            $db->query($query_val);
            if ($db->nf() != 0)
                $autorise  = 1;


            //if (($type != 1) && ($type != 2) && ($type != 4))
            if ($autorise != 1)
            {
                $contenu_page .= '<p>Inutile d’essayer de réparer ce genre d’objets....';
            }
            else
            {
                $req = 'select f_repare_' . $type_rep[$type] . '(' . $perso_cod . ',' . $objet . ') as resultat';
                $db->query($req);
                $db->next_record();
                $contenu_page .= $db->f('resultat');
            }
            $contenu_page .= '<center><a href="inventaire.php">Retour à l’inventaire</a></center>';
            break;
        case 'receptacle':
            $tab_lieu     = $db->get_lieu($perso_cod);
            $lieu_protege = $tab_lieu['lieu_refuge'];
            if ($lieu_protege == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort en étant sur un lieu protégé !';
                break;
            }
            if ($db->is_intangible($perso_cod))
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }
            $req          = 'select cree_receptacle(' . $perso_cod . ',' . $sort . ',' . $type_lance . ') as resultat';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'enluminure':
            $tab_lieu     = $db->get_lieu($perso_cod);
            $lieu_protege = $tab_lieu['lieu_refuge'];
            if ($lieu_protege == 'O')
            {
                $contenu_page .= '<p>Vous ne pouvez pas lancer de sort en étant sur un lieu protégé !';
                break;
            }
            if ($db->is_intangible($perso_cod))
            {
                $contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
                break;
            }
            $req  = 'select cree_parchemin(' . $perso_cod . ',' . $sort . ',' . $type_lance . ') as resultat';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'milice_tel':
            $req  = 'select milice_tel(' . $perso_cod . ',' . $destination . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'prie':
            if (isset($_POST['dieu']))
                $dieu = $_POST['dieu'];
            if (isset($_GET['dieu']))
                $dieu = $_GET['dieu'];
            if (!isset($dieu) || $dieu === '')
            {
                $contenu_page .= '<p>Erreur ! Dieu non définie !';
                break;
            }
            $req  = 'select prie_dieu(' . $perso_cod . ',' . $dieu . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'prie_ext':
            if (isset($_POST['dieu']))
                $dieu = $_POST['dieu'];
            if (isset($_GET['dieu']))
                $dieu = $_GET['dieu'];
            if (!isset($dieu) || $dieu === '')
            {
                $contenu_page .= '<p>Erreur ! Dieu non définie !';
                break;
            }
            $req          = 'select prie_dieu_ext(' . $perso_cod . ',' . $dieu . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'ceremonie':
            $req          = 'select ceremonie_dieu(' . $perso_cod . ',' . $dieu . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'dgrade':
            $req          = 'select change_grade(' . $perso_cod . ',' . $dieu . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'don_br':
            $req          = 'select don_br(' . $perso_cod . ',' . $dest . ',' . $qte . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'vente_auberge':
            $req          = 'select vend_objet(' . $perso_cod . ',' . $objet . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'achat_objet':
            $req          = 'select achete_objet(' . $perso_cod . ',' . $objet . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'redist':
            $req          = 'select start_redispatch(' . $perso_cod . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'mode_combat':
            $req          = 'select change_mcom_cod(' . $perso_cod . ',' . $mode . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'niveau_redist':
            $amelioration = $_POST['amelioration'];
            $req          = 'select detail_redispatch(' . $perso_cod . ',' . $amelioration . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            $contenu_page .= '<center><a href="niveau_redist.php">Retour</a></center>';
            break;
        case 'embr':
            $req          = 'select embr(' . $perso_cod . ',' . $cible . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'ouvre_cadeau':
            $req          = 'select ouvre_cadeau(' . $perso_cod . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'don_cadeau_rouge':
            $req          = 'select donne_rouge(' . $perso_cod . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'don_cadeau_rougeX10':
            $req          = 'select donne_rouge(' . $perso_cod . ') as resultat ';
            for ($i = 0; $i < 10; $i++)
            {
                $db->query($req);
                $db->next_record();
                $contenu_page .= $db->f('resultat');
            }
            break;
        case 'don_cadeau_noir':
            $req          = 'select donne_noir(' . $perso_cod . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'donne_bonbon':
            $req          = 'select donne_bonbon(' . $perso_cod . ',' . $cible . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'teld':
            $req          = 'select teleportation_divine(' . $perso_cod . ',' . $pos . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'offre_boire':
            $req          = 'select offre_boire(' . $perso_cod . ',' . $cible . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'enc':
            $req          = 'select f_enchantement(' . $perso_cod . ',' . $obj . ',' . $enc . ',' . $type_appel . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            break;
        case 'cree_groupe':
            $req          = 'select cree_groupe(' . $perso_cod . ',\'' . pg_escape_string($nom_groupe) . '\') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            $contenu_page .= '<p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
            break;
        case 'regle_groupe':
            $req          = 'select regle_groupe(' . $perso_cod . ',' . $pa . ',' . $pv . ',' . $dlt . ',' . $bonus . ',' . $messages . ',' . $messagemort . ',' . $champions . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            $contenu_page .= '<p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
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
                    $contenu_page .= '<br><br><p><b>********* Vous devez renseigner au moins un membre de coterie ! *********</b><br><br>';
                    $erreur = 1;
                }
                if ($erreur == 0)
                {
                    // on cherche le destinataire
                    if ($tab_dest[$cpt] != "")
                    {
                        $nom_dest     = ltrim(rtrim($tab_dest[$cpt]));
                        $nom_dest     = pg_escape_string($nom_dest);
                        $req_dest     = "select f_cherche_perso('$nom_dest') as num_perso";
                        $db->query($req_dest);
                        $db->next_record();
                        $tab_res_dest = $db->f("num_perso");
                        $req          = 'select invite_groupe(' . $perso_cod . ',' . $groupe . ',' . $tab_res_dest . ') as resultat ';
                        $db->query($req);
                        $db->next_record();
                        $contenu_page .= '<br><br>' . $db->f('resultat');
                    }
                }
            }
            $contenu_page .= '<p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
            break;
        case 'accinv':
            $req         = 'select accepte_invitation(' . $perso_cod . ',' . $g . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            $contenu_page .= '<p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
            break;
        case 'refinv':
            $req         = 'select refuse_invitation(' . $perso_cod . ',' . $g . ') as resultat ';
            $db->query($req);
            $db->next_record();
            $contenu_page .= $db->f('resultat');
            $contenu_page .= '<p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
            break;
        case 'abtemp':
            $contenu_page .= 'En continuant, vous abandonnerez le dispensaire qui vous ramenait en cas de mort.<br />
			<a href="' . $PHP_SELF . '?methode=abtemp2">Cliquez ici pour continuer</a>';
            break;
        case 'abtemp2':
            $req_temple1 = 'delete from perso_temple where ptemple_perso_cod = ' . $perso_cod;
            $db->query($req_temple1);
            $contenu_page .= 'Vous n’avez plus de dispensaire spécifique pour vous ramener en cas de mort.';
            break;
        case 'retour_plan': // Retour du perso dans son plan d'origine
            // On vérifie qu'il est sur un bâtiment administratif
            $req         = 'select ppp_pos_cod from lieu, lieu_position, perso_position, perso_plan_parallele, perso where lieu_tlieu_cod = 9 and perso_pa >= 12 and lieu_cod = lpos_lieu_cod and lpos_pos_cod = ppos_pos_cod and ppp_perso_cod = ppos_perso_cod and perso_cod = ppos_perso_cod and ppos_perso_cod = ' . $perso_cod;
            $db->query($req);
            if ($db->next_record())
            {
                $req = 'update perso_position set ppos_pos_cod = ' . $db->f('ppp_pos_cod') . ' where ppos_perso_cod = ' . $perso_cod;
                $db->query($req);
                $db->query('delete from perso_plan_parallele where ppp_perso_cod = ' . $perso_cod);
                $db->query('update perso set perso_pa = 0 where perso_cod = ' . $perso_cod);
                $contenu_page .= 'Bon retour dans les souterrains !';
                break;
            }
            $contenu_page .= 'Si vous avez assez de PA, vous n’êtes probablement pas dans le bâtiment administratif d’un plan parallèle. Dommage pour vous.';
            break;

        /* Quête de la construction de la cathédrale de Balgur au -3 */
        case 'batir':
            /* Vérif position */
            $p_position = $db->get_pos($perso_cod);
            if ($p_position['pos_cod'] == 7327)
            {
                /* Vérif des PA */
                $req     = 'select perso_pa from perso where perso_cod = ' . $perso_cod;
                $db->query($req);
                $db->next_record();
                $nb_p_pa = $db->f('perso_pa');
                if ($nb_p_pa >= 6)
                {
                    /* Vérif pioche */
                    $req_matos = "select count(perobj_cod) as nbobj from perso_objets, objets 
						where perobj_obj_cod = obj_cod 
						and obj_gobj_cod=332 
						and perobj_perso_cod=" . $perso_cod . " 
						and perobj_equipe='O' ";
                    $db->query($req_matos);
                    $db->next_record();
                    $matos     = $db->f('nbobj');
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
                    }
                    else
                    {
                        $contenu_page .= 'Vous n’avez pas de pioche équipée.';
                    }
                }
                else
                {
                    $contenu_page .= 'Vous n’avez pas assez de PA pour cette action.';
                }
            }
            else
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
}
else
{
    $contenu_page .= '<p>Vous ne pouvez pas valider des actions en étant administrateur !';
}

if (!$inc_vue)
{
    // on va maintenant charger toutes les variables liées au menu
    include('variables_menu.php');

    $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
    $t->parse('Sortie', 'FileRef');
    $t->p('Sortie');
}