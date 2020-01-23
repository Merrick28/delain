<?php
/**
 * Modif Merrick
 * Cette page doit aussi prendre en compte les variables twig pour commencer à remplacer
 * le moteur de template de phplib
 *
 */
$__VERSION = "20201223";    // A changer aussi dans constante.php

$benchmark = $profiler->start('Variables menu');
include_once "verif_connexion.php";
$db2 = new base_delain;

$param = new parametres();

$compte = new compte;
$compte->charge($compt_cod);

$perso = new perso;
$perso->charge($perso_cod);

$get_compte = '';
//if ($db->is_admin_monstre($compt_cod) || $db->is_admin($compt_cod))
$get_compte = "&compt_cod=$compt_cod";

// Chemin d'accès relatif
$chemin = $t->root;
$t->set_var('URL_RELATIVE', $chemin . '/');


// variables du perso
$is_enchanteur = $perso->is_enchanteur();
$is_enlumineur = $perso->is_enlumineur();
$is_refuge     = $perso->is_refuge();
$is_milice     = $perso->is_milice();
$is_fam        = $perso->is_fam();
$is_intangible = $perso->isIntangible();
if ($is_intangible)
{
    $pa_ramasse = $param->getparm(42);
}
else
{
    $pa_ramasse = $param->getparm(41);
}
$degats_perso    = $perso->degats_perso();
$det_deg         = explode(";", $degats_perso);
$deg_min         = $det_deg[0];
$deg_max         = $det_deg[1];
$prochain_niveau = $perso->px_limite();

$gerant = 'N';
$mg     = new magasin_gerant();
if ($mg->getByPersoCod($perso->perso_cod))
{
    $gerant = 'O';
}

$admin_dieu         = $perso->is_admin_dieu();
$fidele_gerant      = $perso->is_fidele_gerant();
$pa                 = $perso->perso_pa;
$nom_perso          = $perso->perso_nom;
$admin_echoppe      = $perso->perso_admin_echoppe;
$admin_echoppe_noir = $perso->perso_admin_echoppe_noir;
$is_vampire         = $perso->perso_niveau_vampire;
$potions            = $perso->is_potions();
$religion           = $perso->is_religion();
$transaction        = $perso->transactions();
$px_actuel          = floor($perso->perso_px);
$barre_energie      = $perso->barre_energie();
$is_fam_divin       = $perso->is_fam_divin();
$pa_dep             = $perso->get_pa_dep();
$nb_evt_non_lu      = sizeof($perso->getEvtNonLu());
if ($is_fam_divin == 1)
{
    $barre_divine   = $perso->barre_divin();
    $energie_divine = $perso->energie_divine();
}


//HP
$barre_hp = $perso->barre_hp();

// Gestion des droits
// par défaut, on met tout à NON
$droit['modif_perso']    = 'N';
$droit['modif_gmon']     = 'N';
$droit['controle']       = 'N';
$droit['acces_log']      = 'N';
$droit['droits']         = 'N';
$droit['carte']          = 'N';
$droit['controle_admin'] = 'N';
$droit['objet']          = 'N';
$droit['enchantements']  = 'N';
$droit['potions']        = 'N';
$droit['news']           = 'N';
$droit['animations']     = 'N';
$droit['factions']       = 'N';
$droit['creer_monstre']  = 'N';
$cd                      = new compt_droit();
if ($cd->charge($compt_cod))
{
    $droit['modif_perso']    = $cd->dcompt_modif_perso;
    $droit['modif_gmon']     = $cd->dcompt_modif_gmon;
    $droit['controle']       = $cd->dcompt_controle;
    $droit['acces_log']      = $cd->dcompt_acces_log;
    $droit['droits']         = $cd->dcompt_gere_droits;
    $droit['carte']          = $cd->dcompt_modif_carte;
    $droit['controle_admin'] = $cd->dcompt_controle_admin;
    $droit['objet']          = $cd->dcompt_objet;
    $droit['enchantements']  = $cd->dcompt_enchantements;
    $droit['potions']        = $cd->dcompt_potions;
    $droit['news']           = $cd->dcompt_news;
    $droit['animations']     = $cd->dcompt_animations;
    $droit['factions']       = $cd->dcompt_factions;
    $droit['creer_monstre']  = $cd->dcompt_creer_monstre;
}

// variables du compte
$is_admin         = $compte->is_admin();
$is_admin_monstre = $compte->is_admin_monstre();

/***********************************************/
/* Normalement, ici, on a toutes les variables */
/* Reste à passer à la mise en forme           */
/***********************************************/


// nom du perso
$t->set_var('PERSO_NOM', $nom_perso);

//intangible
if ($is_intangible)
{
    $intangible = "<em>Perso impalpable !</em><br><br>";
}
else
{
    $intangible = '';
}
$t->set_var('INTANGIBLE', $intangible);

// pa
$t->set_var('PERSO_PA', $pa);

// hp
$t->set_var('PERSO_BARRE_VIE', $barre_hp);
$t->set_var('PERSO_PV', $perso->perso_pv);
$t->set_var('PERSO_PV_MAX', $perso->perso_pv_max);

// Barre d'énergie enchanteur
if ($is_enchanteur)
{
    $enchanteur = "<img src=\"" . G_IMAGES . "energi10.png\" alt=\"\"> <div title=\"" . $perso->perso_energie . "/100 énergie\" alt=\"" . $perso->perso_energie . "/100 énergie\" class=\"container-nrj\"><div class=\"barre-nrj\" style=\"width:" . $barre_energie . "%\"></div></div>";
    $forge      = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/enchantement_general.php">Forgeamage</a><br>';
}
else
{
    $enchanteur = '';
    $forge      = '';
}

$t->set_var('ENCHANTEUR', $enchanteur);
$t->set_var('FORGE', $forge);

// Barre d'énergie pour familiers divins
if ($is_fam_divin == 1)
{
    $fam_divin = "<img src=\"" . G_IMAGES . "magie.gif\" alt=\"\"> <div title=\"Énergie divine : " . $energie_divine . "\" alt=\"Énergie divine : " . $energie_divine . "\" class=\"container-div\"><div class=\"barre-div\" style=\"width:" . $barre_divine . "%\"></div></div>";
}
else
{
    $fam_divin = '';
}
$t->set_var('FAM_DIVIN', $fam_divin);

// PX
$t->set_var('PERSO_BARRE_XP', $perso->barre_xp());
$t->set_var('PERSO_PX', $px_actuel);
$t->set_var('PERSO_PROCHAIN_NIVEAU', $prochain_niveau);

// affichage dégats et armure
$t->set_var('PERSO_DEGATS', $det_deg[0] . '-' . $det_deg[1]);
$t->set_var('PERSO_ARMURE', $perso->armure());
if ($is_fam)
{
    $t->set_var('PERSO_MOVE', "");
}
else
{
    $is_locked = $perso->is_locked();   // réutilisé plus loin!
    $bagde_lock ="&nbsp;&nbsp;";
    if ($is_locked)
    {
        $bagde_lock = '&nbsp;<span class="badge-profil">'.$perso->nb_locks().'</span>';
        $moveimage = 'run';
    } else if ($pa_dep==4) {
        $moveimage = 'footsteps';
    } else if ($pa_dep<4) {
            $moveimage = 'footgreen';
    } else {
        $moveimage = 'footred';
    }
    $t->set_var('PERSO_MOVE', $bagde_lock.'<img src="' . G_IMAGES . $moveimage.'.png" title="Déplacement" alt="dep">: <strong>'.$pa_dep.'</strong>');
}


// position
$var_menu_ppos = new perso_position();
$var_menu_ppos->getByPerso($perso->perso_cod);
$var_menu_pos = new positions();
$var_menu_pos->charge($var_menu_ppos->ppos_pos_cod);
$var_menu_etage = new etage();
$var_menu_etage->getByNumero($var_menu_pos->pos_etage);

$t->set_var('PERSO_POS_X', $var_menu_pos->pos_x);
$t->set_var('PERSO_POS_Y', $var_menu_pos->pos_y);
$t->set_var('PERSO_ETAGE', $var_menu_etage->etage_libelle);

// Animation Léno 2019: Concours de barde
$animation = "" ;
$pdo   = new bddpdo;
$req = "SELECT cbar_saison FROM concours_barde where (now()>cbar_date_teaser or now()>=cbar_date_ouverture) and now()<=cbar_fermeture order by cbar_saison desc limit 1 ";
$stmt  = $pdo->query($req);
if ($rows  = $stmt->fetch())
{
    $animation = '<hr /><a href="' . $chemin . '/concours_barde.php">Concours de Barde</a>';
}
$t->set_var('PERSO_ANIMATION', $animation);

// passage niveau
if ($px_actuel >= $prochain_niveau)
{
    $passage_niveau = '<hr /><a href="' . $chemin . '/niveau.php"><strong>Passer au niveau supérieur ! </strong>(6 PA)</a>';
}
else
{
    $passage_niveau = '';
}
$t->set_var('PASSAGE_NIVEAU', $passage_niveau);

// Quête avec perso
if ($perso->is_perso_quete())
{
    $perso_quete = "<hr /><a href=\"$chemin/quete_perso.php\"><strong>Quête</strong></a>";
}
else
{
    $perso_quete = '';
}
$t->set_var('PERSO_QUETE', $perso_quete);

// Menu pour gérer les quetes perso en cours ou terminée
$nb_quete_auto = $perso->perso_nb_auto_quete();
if ($nb_quete_auto["nb_total"] * 1 > 0)
{
    if ((int)$nb_quete_auto["journal_nb_news"]>0)
    {
        $perso_auto_quete = "<img src=\"" . G_IMAGES . "calice.png\"> <a href=\"$chemin/quete_auto.php?onglet=apercu\"><strong>Mes quêtes " . ((int)$nb_quete_auto["nb_encours"] > 0 ? "(" . $nb_quete_auto["nb_encours"] . ")" : "") . "</strong></a><br>";
    }
    else
    {
        $perso_auto_quete = "<img src=\"" . G_IMAGES . "calice.png\"> <a href=\"$chemin/quete_auto.php?onglet=apercu\">Mes quêtes " . ((int)$nb_quete_auto["nb_encours"] > 0 ? "(" . $nb_quete_auto["nb_encours"] . ")" : "") . "</a><br>";
    }
}
else
{
    $perso_auto_quete = '';
}
$t->set_var('PERSO_AUTO_QUETE', $perso_auto_quete);

// lieux
$tab_lieu   = array();
$perso_lieu = "";
if ($perso->is_lieu())
{
    $tab_lieu = $perso->get_lieu();
    $temp     = $tab_lieu['lieu'];
    if (!empty($tab_lieu['lieu']->lieu_url))
    {
        $nom_lieu   = $tab_lieu['lieu']->lieu_nom;
        $libelle    = $tab_lieu['lieu_type']->tlieu_libelle;
        $perso_lieu = "<hr /><a href=\"$chemin/lieu.php\"><strong>" . $nom_lieu . "</strong> (" . $libelle . ")</a>";
    }
}
$t->set_var('PERSO_LIEU', $perso_lieu);

//messagerie
$mdest  = new messages_dest();
$tab    = $mdest->getByPersoNonLu($perso->perso_cod);
$nb_msg = count($tab);
if ($nb_msg != 0)
{
    $perso_messagerie = "<strong>Messagerie (" . $nb_msg . ")</strong>";
}
else
{
    $perso_messagerie = "Messagerie";
}
$t->set_var('PERSO_MESSAGERIE', $perso_messagerie);

// deplacement
$texte_dep = '';
if (!$is_fam)
{
    if ((!$is_locked) && ($droit['controle'] != 'O'))
    {
        $texte_dep .= "<img src=\"" . G_IMAGES . "deplacement.gif\" alt=\"\"> ";
        $texte_dep .= "<a href=\"$chemin/deplacement.php\">";
        $texte_dep .= "Déplacement (" . $pa_dep . " PA)";
        $texte_dep .= "</a>";
        $texte_dep .= "<br>";
    }
    if (($is_locked) && ($droit['controle'] != 'O'))
    {
        $texte_dep .= "<img src=\"" . G_IMAGES . "fuite.gif\" alt=\"\"> ";
        if ($perso->perso_pa >= $pa_dep)
        {
            $texte_dep .= "<a href=\"$chemin/deplacement.php\">";
        }
        $texte_dep .= "Fuite (" . $pa_dep . " PA)";
        if ($perso->perso_pa >= $pa_dep)
        {
            $texte_dep .= "</a>";
        }
        $texte_dep .= "<br>";
    }
}
$t->set_var('TEXTE_DEP', $texte_dep);

// ramasser
$ramasser = '';
if (($perso->nb_obj_case() != 0) || ($perso->nb_or_case() != 0))
{
    $ramasser = '<img src="' . G_IMAGES . 'ramasser.gif" alt=""> ';
    if ($perso->perso_pa >= $pa_ramasse)
    {
        $ramasser .= "<a href=\"$chemin/ramasser.php\">";
    }
    $ramasser .= "Ramasser (" . $pa_ramasse . "PA)";
    if ($perso->perso_pa >= $pa_ramasse)
    {
        $ramasser .= "</a>";
    }
}
$t->set_var('RAMASSER', $ramasser);

// Transactions
if ($transaction > 0)
{
    $perso_transactions = "<strong>Transactions (" . $transaction . ")</strong>";
}
else
{
    $perso_transactions = "Transactions";
}
$t->set_var('TRANSACTIONS', $perso_transactions);

// wiki
if ($is_admin_monstre)
{
    $wiki           = '<a href="http://wikimonstre.jdr-delain.net/index.php/Accueil">Wiki Monstre</a>';
    $option_monstre = '<img src="' . G_IMAGES . 'iconeswitch.gif" alt=""> <a href="' . $chemin . '/option_monstre.php">Option du monstre</a>';
}
else
{
    $wiki           = '<a href="http://wiki.jdr-delain.net/" target="_blank">Wiki</a>';
    $option_monstre = '';
}

// 2019-02-12 - Marlyza - Ajout menu attribution de titre
if ($droit['modif_perso'] == 'O')
{
    $option_monstre .= ($option_monstre == "" ? "" : "<br>") . '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_titre.php">Gestion de récompense</a>';
}

// 2018-10-30 - Marlyza - Ajout menu téléportation
if ($is_admin_monstre || ($droit['modif_perso'] == 'O'))
{
    $option_monstre .= ($option_monstre == "" ? "" : "<br>") . '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_teleportation.php">Téléportation</a><br>';
}


$t->set_var('WIKI', $wiki);

//controle	
if ($droit['controle'] == 'O')
{
    $controle = '
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/visu_amelioration.php">Améliorations</a><br>
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/detail_compte.php">Détail du compte</a><br>
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/rech_compte.php">Recherches sur comptes</a><br>
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/multi_trace.php">Visu des multi</a><br>
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/sitting.php">Sittings > 5 j.</a><br>
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/controle_interaction_4e.php">Intéractions 4e persos</a><br>';
}
else
{
    $controle = '';
}

// Modif perso
if ($droit['modif_perso'] == 'O')
{
    $modif_perso = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_perso_edit.php">Modif. perso</a><br>';
}
else
{
    $modif_perso = '';
}

// modif monstre
if ($droit['modif_gmon'] == 'O')
{
    $modif_monstre = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_type_monstre_edit.php">Modif. types monstre</a><br>
		<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_repartition_monstres.php">Modif. répart. monstres</a><br>';
}
else
{
    $modif_monstre = '';
}


// modif carte
if ($droit['carte'] == 'O')
{
    $droit_carte = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_etage.php">Modif. étages</a><br>';
}
else
{
    $droit_carte = '';
}

// controle admin
if ($droit['controle_admin'] == 'O')
{
    $controle_admin = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/controle_admins.php">Controle admins</a><br>';
}
else
{
    $controle_admin = '';
}

// gestion_droits
if ($droit['droits'] == 'O')
{
    $gestion_droits = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_gestion_droits.php">Gestion des droits</a><br>';
    $gestion_droits .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_params.php">Gestion des paramètres</a><br>';
}
else
{
    $gestion_droits = '';
}

// modif objets
if ($droit['objet'] == 'O')
{
    $modif_objets = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_objet_generique_edit.php">Gestion objets generiques</a><br>';
}
else
{
    $modif_objets = '';
}

// enchantements
if ($droit['enchantements'] == 'O')
{
    $droit_enchantement = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_enchantements.php">Enchantements</a><br>';
    $droit_enchantement .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_enluminure.php">Enluminure</a><br>';
    $droit_enchantement .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_magie.php">Modif. sorts</a><br>';
    $droit_enchantement .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_bonusmalus.php">Modif. bonus/malus</a><br>';
}
else
{
    $droit_enchantement = '';
}

// potions
if ($droit['potions'] == 'O')
{
    $droit_potion = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_potions.php">Création de potions</a><br>';
}
else
{
    $droit_potion = '';
}

// acces logs
if ($droit['acces_log'] == 'O')
{
    $droit_logs = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_visu_logs.php">Voir les logs</a><br>';
}
else
{
    $droit_logs = '';
}

// modif perso
if ($droit['modif_perso'] == 'O')
{
    $quete_auto = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_quete_auto_edit.php">Quetes auto</a><br>';
}
else
{
    $quete_auto = '';
}

// news
if ($droit['news'] == 'O')
{
    $news = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_news.php">Lancer une news</a><br>';
}
else
{
    $news = '';
}

// animations
if ($droit['animations'] == 'O')
{
    $animations = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_animations.php">Animations</a><br>';
}
else
{
    $animations = '';
}

// factions
if ($droit['factions'] == 'O')
{
    $factions = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_factions.php">Factions</a><br>';
}
else
{
    $factions = '';
}

// admin_echoppe
if ($admin_echoppe == 'O')
{
    $echoppe = '<img src="' . G_IMAGES . 'inventaire.gif" alt=""> <a href="' . $chemin . '/admin_echoppe.php">Admin. échoppes</a><br>';
}
else
{
    $echoppe = '';
}

// gerant
if ($gerant == 'O')
{
    $gerant = '<img src="' . G_IMAGES . 'inventaire.gif" alt=""> <a href="' . $chemin . '/gere_echoppe.php">Gestion échoppes</a><br>';
}
else
{
    $gerant = '';
}

// Gestion des favoris
$arr_favoris = $perso->get_favoris();
if (count($arr_favoris) == 0)
{
    $favoris = '<div id="barre-favoris" style="display:none;"><hr /></div>';
}
else
{
    $favoris = '<div id="barre-favoris"><hr />';
    foreach ($arr_favoris as $key => $fav)
    {
        $favoris .= '<div id="fav-link-' . $fav["pfav_cod"] . '"><img onclick="javascript:delSortFavoris(' . substr($fav["pfav_type"],-1) .',' . $fav["pfav_misc_cod"] .');" src="' . G_IMAGES . 'favoris.png" alt=""> <a href="' . $fav["link"] . '">' . htmlspecialchars($fav["nom"]) . '</a></div>';
    }
    $favoris .= '</div>';
}
$t->set_var('FAVORIS', $favoris);

// voie magique
$psort5 = $perso->sort_lvl5();
$psortm = $perso->sort_memo();
if ($psort5 > 0 && is_array($psortm) && count($psortm) > 5)
{
    $voie_magique = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/choix_voie_magique.php">Voie magique</a><br>';
}
else
{
    $voie_magique = '';
}
$t->set_var('VOIE_MAGIQUE', $voie_magique);

//Enluminure
if ($is_enlumineur)
{
    $enlumineur = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/enluminure_general.php">Enluminure</a><br>';
}
else
{
    $enlumineur = '';
}
$t->set_var('ENLUMINEUR', $enlumineur);

// potions
if ($potions == 1)
{
    $potion = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/comp_potions.php">Alchimie</a><br>';
}
else
{
    $potion = '';
}
$t->set_var('POTION', $potion);

//religion
if ($religion || $fidele_gerant || $admin_dieu)
{
    $religion = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/religion.php">Religion</a><br>';
}
else
{
    $religion = '';
}
$t->set_var('RELIGION', $religion);

// Compétences spéciales
$commandement = '';
$enseignement = '';
$creuser      = '';
$vol          = '';
$pcomp        = new perso_competences();
if ($pcomp->getByPersoComp($perso->perso_cod, 80))
{
    $commandement = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/comp_commandement.php">Commandement</a><br>';
}
if ($pcomp->getByPersoComp($perso->perso_cod, 81))
{
    $enseignement = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/comp_enseignement.php">Enseignement</a><br>';
}
if ($pcomp->getByPersoComp($perso->perso_cod, 83))
{
    $creuser = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/objets/pioche.php">Creuser</a><br>';
}
if ($pcomp->getByPersoComp($perso->perso_cod, 86))
{
    $vol = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/comp_vol.php">Vol</a><br>';
}


$pc  = new perso_commandement();
$tab = $pc->getBy_perso_subalterne_cod($perso->perso_cod);
if ($tab !== false)
{
    $commandement = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/comp_commandement.php">Commandement</a><br>';
}

$t->set_var('ENSEIGNEMENT', $enseignement);
$t->set_var('CREUSER', $creuser);
$t->set_var('VOL', $vol);

// Section administration
if ($controle . $controle_admin . $droit_logs . $gestion_droits != '' &&
    $modif_perso . $modif_monstre . $modif_objets .
    $droit_carte . $droit_enchantement . $droit_potion .
    $quete_auto . $factions . $news . $animations .
    $echoppe . $gerant .
    $option_monstre . $commandement != ''
)
{
    $gestion_droits .= '<hr />';
}
if ($modif_perso . $modif_monstre . $modif_objets != '' &&
    $droit_carte . $droit_enchantement . $droit_potion .
    $quete_auto . $factions . $news . $animations .
    $echoppe . $gerant .
    $option_monstre . $commandement != ''
)
{
    $modif_objets .= '<hr />';
}
if ($droit_carte . $droit_enchantement . $droit_potion != '' &&
    $quete_auto . $factions . $news . $animations .
    $echoppe . $gerant .
    $option_monstre . $commandement != ''
)
{
    $droit_potion .= '<hr />';
}
if ($quete_auto . $factions . $news . $animations != '' &&
    $echoppe . $gerant .
    $option_monstre . $commandement != ''
)
{
    $animations .= '<hr />';
}
if ($echoppe . $gerant != '' &&
    $option_monstre . $commandement != ''
)
{
    $gerant .= '<hr />';
}
if ($controle . $controle_admin . $droit_logs . $gestion_droits .
    $modif_perso . $modif_monstre . $modif_objets .
    $droit_carte . $droit_enchantement . $droit_potion .
    $quete_auto . $factions . $news . $animations .
    $echoppe . $gerant .
    $option_monstre . $commandement != ''
)
{
    $option_monstre .= '<hr />';
}


$t->set_var('COMMANDEMENT', $commandement);
$t->set_var('GERANT', $gerant);
$t->set_var('ECHOPPE', $echoppe);
$t->set_var('ANIMATIONS', $animations);
$t->set_var('NEWS', $news);
$t->set_var('QUETE_AUTO', $quete_auto);
$t->set_var('FACTIONS', $factions);
$t->set_var('DROIT_LOGS', $droit_logs);
$t->set_var('DROIT_POTION', $droit_potion);
$t->set_var('DROIT_ENCHANTEMENT', $droit_enchantement);
$t->set_var('MODIF_OBJETS', $modif_objets);
$t->set_var('GESTION_DROITS', $gestion_droits);
$t->set_var('DROIT_CARTE', $droit_carte);
$t->set_var('MODIF_MONSTRE', $modif_monstre);
$t->set_var('OPTION_MONSTRE', $option_monstre);
$t->set_var('CONTROLE', $controle);
$t->set_var('MODIF_PERSO', $modif_perso);
$t->set_var('CONTROLE_ADMIN', $controle_admin);

$milice = '';
if ($is_milice == 1)
{
    $milice = '<img src="' . G_IMAGES . 'attaquer.gif" alt=""> <a href="' . $chemin . '/milice.php">Milice</a><br>';
}
$t->set_var('MILICE', $milice);

$vampire = "" ;
if ($is_vampire != 0)
{
    $vampire = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/vampirisme.php">Vampirisme</a><br>';
}
$t->set_var('VAMPIRE', $vampire);
//
// gestion des vote
// 
$cv           = new compte_vote();
$totalXpGagne = 0;
$tab          = $cv->getBy_compte_vote_compte_cod($compte->compt_cod);
if ($tab !== false)
{
    $totalXpGagne = $tab[0]->compte_vote_total_px_gagner;
}


$cvip    = new compte_vote_ip();
$tab     = $cvip->getByCompteTrue($compte->compt_cod);
$nbrVote = count($tab);


$tab         = $cvip->getByCompteTrueMois($compte->compt_cod);
$nbrVoteMois = count($tab);

$tab          = $cvip->getVoteAValider($compte->compt_cod);
$VoteAValider = count($tab);

$tab          = $cvip->getVoteRefus($compte->compt_cod);
$votesRefusee = count($tab);

//
// gestion de la barre de switch rapide (seulement sur des pages spécifiques)
//
$barre_switch_rapide = '';

if (!in_array($_SERVER["PHP_SELF"], array("/jeu_test/switch.php", "/switch_rapide.php")))
{
    $pdo   = new bddpdo;
    $req   = "SELECT perso_cod, 
                      perso_nom, 
                      perso_pv, 
                      perso_pv_max, 
                      perso_pa, 
                      dlt_passee(perso_cod) dlt_passee, 
                      perso_type_perso, 
                      CASE WHEN perso_dlt<NOW() THEN '' ELSE replace(substr((perso_dlt-now())::text,1,5),':','h') END dlt, 
                      type, 
                      ordre,
                      (select count(*) from messages_dest where dmsg_perso_cod = perso_cod and dmsg_lu = 'N' and dmsg_archive = 'N') as nb_msg_non_lu
               FROM perso 
               JOIN (
                    select perso_cod as p_perso_cod, 1 as type, perso_cod ordre
                    from compte  
                    join perso_compte on compt_cod=? and pcompt_compt_cod=compt_cod 
                    join perso on perso_cod=pcompt_perso_cod
                    where perso_actif='O'
                    
                    union
                    
                    select perso_cod as p_perso_cod, 1 as type, pfam_perso_cod ordre 
                    from compte  
                    join perso_compte on compt_cod=? and pcompt_compt_cod=compt_cod 
                    join perso_familier on pfam_perso_cod=pcompt_perso_cod 
                    join perso on perso_cod=pfam_familier_cod where perso_actif='O' 
                    
                    union 
                    
                    select perso_cod as p_perso_cod, 2 as type, perso_cod ordre 
                    from compte_sitting
                    join perso_compte on csit_compte_sitteur=? and pcompt_compt_cod=csit_compte_sitte and csit_ddeb <= now() and csit_dfin >= now()
                    join perso on perso_cod=pcompt_perso_cod 
                    where perso_actif='O'
                    
                    union
                    
                    select perso_cod as p_perso_cod, 2 as type, pfam_perso_cod ordre 
                    from compte_sitting  
                    join perso_compte on csit_compte_sitteur=? and pcompt_compt_cod=csit_compte_sitte and csit_ddeb <= now() and csit_dfin >= now()
                    join perso_familier on pfam_perso_cod=pcompt_perso_cod 
                    join perso on perso_cod=pfam_familier_cod where perso_actif='O'
                ) as p on p_perso_cod = perso_cod
                ORDER BY type, ordre, perso_type_perso ";
    $stmt  = $pdo->prepare($req);
    $stmt  = $pdo->execute(array($compt_cod, $compt_cod, $compt_cod, $compt_cod), $stmt);
    $count = 1 * $stmt->rowCount();
    $rows  = $stmt->fetchAll();

    // pour optimiser l'affichage on compte le nombre de perso et de fam
    $nb_perso    = 0;
    $nb_familier = 0;
    $nb_button   = sizeof($rows);
    foreach ($rows as $result)
    {
        if ((1 * $result["perso_type_perso"]) == 3)
        {
            $nb_familier++;
        }
        else
        {
            $nb_perso++;
        }
    }

    $liste_boutons = "";
    $col           = 0;
    if ($nb_familier > 0)
    {
        $col_class = 'col-xs-12  col-sm-6';
    }                         // perso + fam on préparera regroupemet par 2
    else
    {
        if ($nb_perso > 3)
        {
            $col_class = 'col-xs-12 col-sm-6 col-md-3 col-lg-3';
        }        //pas de fam juste 4 persos
        else
        {
            $col_class = 'col-xs-12 col-sm-6 col-md-4 col-lg-4';
        }
    }        // pas de fam juste 3 persos ou moins
    foreach ($rows as $result)
    {
        if (($col % 2 == 1) && ((1 * $result["perso_type_perso"]) != 3) && ($nb_familier > 0))
        {
            // le dernier perso n'avait pas de fam, on padd
            $liste_boutons .= '<div class="col-xs-12  col-sm-6"><button class="button-switch">&nbsp;</button></div></div>';
            $col++;
        }
        if (($col % 2 == 0) && ($nb_familier > 0))
        {
            // regroupement des cases par paire (le perso et son fam) seulement s'il y a des familiers
            if ($nb_perso > 3)
            {
                $liste_boutons .= '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">';
            }
            else
            {
                $liste_boutons .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">';
            }
        }
        // Raccourcir les nom en retirant les tags superflux
        $_aff_nom = $result["perso_nom"];
        if (substr($_aff_nom, 0, 12) == "Familier de ")
        {
            $_aff_nom = "Fam. de " . substr($_aff_nom, 12);
            $pesprit  = strpos($_aff_nom, "(esprit de ");
            if ($pesprit > 0)
            {
                $_aff_nom = substr($_aff_nom, 0, $pesprit) . "(" . substr($_aff_nom, $pesprit + 11);
            }
        }
        else
        {
            if (substr($_aff_nom, 0, 31) == "Image démoniaque, Familier de ")
            {
                $_aff_nom = "Kirga de " . substr($_aff_nom, 31);
            }
            else
            {
                if (substr($_aff_nom, 0, 27) == "Kirga-Uh-Kmot, Familier de ")
                {
                    $_aff_nom = "Kirga de " . substr($_aff_nom, 27);
                }
                else
                {
                    $_aff_nom = preg_replace("/ \(n° (\d+)\)$/", "", $_aff_nom);
                }
            }
        }

        // ajout d'info PA, PV et DLT
        $_blessure     = 100 * (1 * $result["perso_pv"]) / (1 * $result["perso_pv_max"]);
        $_aff_perso_pv = $_blessure > 50 ? ($_blessure == 100 ? $result["perso_pv"] : '<span style="color:lightgreen;font-size:9px;font-weight:bold;">' . $result["perso_pv"] . '</span>') : ($_blessure > 25 ? '<span style="color:#ffd700;font-size:9px;font-weight:bold;">' . $result["perso_pv"] . '</span>' : '<span style="color:#ff69b4;font-size:9px;font-weight:bold;">' . $result["perso_pv"] . '</span>');
        $_aff_perso_pa = $result["perso_pa"] == 0 ? $result["perso_pa"] : '<span style="color:lightgreen;font-size:9px;font-weight:bold;">' . $result["perso_pa"] . '</span>';
        $_aff_dlt      = $result["dlt"] == "" ? "" : " &rArr; " . $result["dlt"];
        $_aff_nom      .= "<br><span style=\"color:white; font-size:9px;font-weight:normal;\">" . $_aff_perso_pv . "/" . $result["perso_pv_max"] . " - " . $_aff_perso_pa . "PA" . $_aff_dlt . "</span>";

        $_aff_msg = "";
        if (1 * $result["nb_msg_non_lu"] > 0)
        {
            $_aff_msg = '<span class="badge-btn">' . $result["nb_msg_non_lu"] . '</span>';
        }

        if ($result["dlt_passee"] != 0)
        {
            $liste_boutons .= '<div class="' . $col_class . '">' . $_aff_msg . '<button id=' . $result["perso_cod"] . ' class="button-switch-dlt">' . $_aff_nom . '</button></div>';
        }
        else
        {
            if ($result["perso_cod"] == $perso_cod)
            {
                $liste_boutons .= '<div class="' . $col_class . '">' . $_aff_msg . '<button id=' . $result["perso_cod"] . ' class="button-switch-act">' . $_aff_nom . '</button></div>';
            }
            else
            {
                $liste_boutons .= '<div class="' . $col_class . '">' . $_aff_msg . '<button id=' . $result["perso_cod"] . ' class="button-switch">' . $_aff_nom . '</button></div>';
            }
        }

        if (($col % 2 == 1) && ($nb_familier > 0))
        {
            // regroupement des cases par paire (le perso et son fam)
            $liste_boutons .= '</div>';
        }

        $col++; // colonne suivante
    }

    if (($col % 2 == 1) && ($nb_familier > 0))
    {
        // le dernier perso n'avait pas de fam, on padd
        $liste_boutons .= '<div class="col-xs-12  col-sm-6"><button class="button-switch">&nbsp;</button></div></div>';
        $col++;
    }

    if (($liste_boutons != '') && ($nb_button <= 16))
    {
        $barre_switch_rapide = '<div id="colonne0" data-switch-bar="standard" style="display:block"><div class="container-fluid"><div class="row">' . $liste_boutons . '</div></div></div>';
    }
    else
    {
        // Au dessus de 16 persos, il doit s'agir d'un admin monstres, on cache la barre par défaut (sera affiché si la souris passe en haut de l'écran
        $barre_switch_rapide = '<div id="colonne0" data-switch-bar="autohide" style="display:none"><div class="container-fluid"><div class="row">' . $liste_boutons . '</div></div></div>';
    }
}

$t->set_var('BARRE_SWITCH_RAPIDE', $barre_switch_rapide);

$barre_menu_icone = '
<div id="colonne0-icons"><center>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/frame_vue.php"><img title="Vue" src="' . G_IMAGES . 'eye.png"></a></div>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/evenements.php"><img title="Evénements" src="' . G_IMAGES . 'events.png"></a>' . ($nb_evt_non_lu <= 0 ? '' : '<span class="badge">' . $nb_evt_non_lu . '</span>') . '</div>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/inventaire.php"><img title="Inventaire" src="' . G_IMAGES . 'chest.png"></a></div>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/transactions2.php"><img title="Transaction" src="' . G_IMAGES . 'transac.png"></a>' . ($transaction <= 0 ? '' : '<span class="badge">' . $transaction . '</span>') . '</div>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/combat.php"><img title="Combat" src="' . G_IMAGES . 'war.png"></a></div>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/magie.php"><img title="Magie" src="' . G_IMAGES . 'book.png"></a></div>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/messagerie2.php"><img title="Messagerie" src="' . G_IMAGES . 'mail.png"></a>' . ($nb_msg <= 0 ? '' : '<span class="badge">' . $nb_msg . '</span>') . '</div>
	<div style="float: left;  width:12.5%;"><a href="/jeu_test/switch.php"><img title="Gestion de compte" src="' . G_IMAGES . 'castle.png"></a></div>
</center></div>';
//$barre_menu_icone='';
$t->set_var('BARRE_MENU_ICONE', $barre_menu_icone);

// Injection de la version
$t->set_var('__VERSION', $__VERSION);

// variables twig
$var_twig_defaut = array(
    '__VERSION'           => $__VERSION,
    'G_IMAGES'            => G_IMAGES,
    'G_URL'               => G_URL,
    'PERSO'               => $perso,
    'BARRE_MENU_ICONE'    => $barre_menu_icone,
    'TYPE_FLUX'           => $type_flux,
    'DEG_MIN'             => $deg_min,
    'DEG_MAX'             => $deg_max,
    'POSITION'            => $var_menu_pos,
    'ETAGE'               => $var_menu_etage,
    'LIEU'                => $tab_lieu,
    'PA_RAMASSE'          => $pa_ramasse,
    'DROIT'               => $droit,
    'NB_MSG'              => $nb_msg,
    'GERANT'              => $gerant,
    'COMPTE'              => $compte,
    'BARRE_SWITCH_RAPIDE' => $barre_switch_rapide,
    'IS_INTANGIBLE'       => $is_intangible,
    'IS_REFUGE'           => $is_refuge,
    'PERSO_ANIMATION'     => $animation
);
$benchmark->stop($benchmark);
?>
