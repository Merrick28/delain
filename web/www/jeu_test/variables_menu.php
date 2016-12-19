<?php
include_once "verif_connexion.php";
$db2 = new base_delain;



$get_compte = '';
//if ($db->is_admin_monstre($compt_cod) || $db->is_admin($compt_cod))
$get_compte = "&compt_cod=$compt_cod";

// Chemin d'accès relatif
$chemin = $t->root;
$t->set_var('URL_RELATIVE', $chemin . '/');

// on va récupérer le tableau json d'une api externe
echo "<!-- " . URL_API . 'game/menu.php?type_auth=programme' . $get_compte . '&ext_perso_cod=' . $perso_cod . '&typesort=json&cle_connect=' . apc_fetch('cle_connec') . ' -->';
$tabtemp = file_get_contents(URL_API . '/game/menu.php?type_auth=programme' . $get_compte . '&ext_perso_cod=' . $perso_cod . '&typesort=json&cle_connect=' . apc_fetch('cle_connec'));


// tout est maintenant dans $result_perso
$result_perso = json_decode($tabtemp, true);


$is_enchanteur = false;
if ($result_perso['enchanteur'] == 1)
{
    $is_enchanteur = true;
}

$is_enlumineur = false;
if ($result_perso['enlumineur'] == 1)
{
    $is_enlumineur = true;
}

$is_refuge = false;
if ($result_perso['refuge'] == 1)
{
    $is_refuge = true;
}

$is_milice = false;
if ($result_perso['milice'] == 1)
{
    $is_milice = true;
}

$is_fam = false;
if ($result_perso['is_fam'] == 1)
{
    $is_fam = true;
}

$is_intangible = false;
if ($result_perso['intangible'] == 1)
{
    $is_intangible = true;
}


$gerant             = $result_perso['gerant'];
$admin_dieu         = $result_perso['admin_dieu'];
$fidele_gerant      = $result_perso['fidele_gerant'];
$pa                 = $result_perso['pa'];
$nom_perso          = $result_perso['nom'];
$admin_echoppe      = $result_perso['admin_echoppe'];
$admin_echoppe_noir = $result_perso['admin_echoppe_noir'];
$is_vampire         = $result_perso['is_vampire'];
$potions            = $result_perso['potions'];
$religion           = $result_perso['religion'];
$transaction        = $result_perso['transaction'];

// Gestion des droits
$req = "select * from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
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
}
else
{
    $db->next_record();
    $droit['modif_perso']    = $db->f("dcompt_modif_perso");
    $droit['modif_gmon']     = $db->f("dcompt_modif_gmon");
    $droit['controle']       = $db->f("dcompt_controle");
    $droit['acces_log']      = $db->f("dcompt_acces_log");
    $droit['droits']         = $db->f("dcompt_gere_droits");
    $droit['carte']          = $db->f("dcompt_modif_carte");
    $droit['controle_admin'] = $db->f("dcompt_controle_admin");
    $droit['objet']          = $db->f("dcompt_objet");
    $droit['enchantements']  = $db->f("dcompt_enchantements");
    $droit['potions']        = $db->f("dcompt_potions");
    $droit['news']           = $db->f("dcompt_news");
    $droit['animations']     = $db->f("dcompt_animations");
    $droit['factions']       = $db->f("dcompt_factions");
}
$is_admin         = $db->is_admin($compt_cod);
$is_admin_monstre = $db->is_admin_monstre($compt_cod);

// nom du perso
$t->set_var('PERSO_NOM', $nom_perso);

//intangible
if ($is_intangible)
    $intangible = "<i>Perso impalpable !</i><br><br>";
else
    $intangible = '';
$t->set_var('INTANGIBLE', $intangible);

// niveau
$req_niveau = "select perso_pv,perso_pv_max,limite_niveau_actuel($perso_cod) as limite,perso_energie from perso where perso_cod = $perso_cod";
$db->query($req_niveau);
$db->next_record();
$px_actuel  = $result_perso['perso_px'];
$px_limite  = $result_perso['prochain_niveau'];


// pa
$t->set_var('PERSO_PA', $result_perso['pa']);

// hp
$t->set_var('PERSO_BARRE_VIE', $result_perso['barre_hp']);
$t->set_var('PERSO_PV', $result_perso['pv']);
$t->set_var('PERSO_PV_MAX', $result_perso['pv_max']);

// Barre d'énergie enchanteur
if ($is_enchanteur)
{
    $enchanteur = "<img src=\"" . G_IMAGES . "energi10.png\" alt=\"\"> <img src=\"" . G_IMAGES . "nrj" . $result_perso['barre_energie'] . ".png\" title=\"" . $result_perso['perso_energie'] . "/100 énergie\" alt=\"" . $result_perso['perso_energie'] . "/100 énergie\">";
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
if ($result_perso['is_fam_divin'] == 1)
{
    $fam_divin = "<img src=\"" . G_IMAGES . "magie.gif\" alt=\"\"> <img src=\"" . G_IMAGES . "nrj" . $result_perso['barre_divine'] . ".png\" title=\"Énergie divine : " . $result_perso['energie_divine'] . "\" alt=\"Énergie divine : " . $result_perso['energie_divine'] . "\">";
}
else
    $fam_divin = '';
$t->set_var('FAM_DIVIN', $fam_divin);

// PX
$t->set_var('PERSO_BARRE_XP', $result_perso['barre_xp']);
$t->set_var('PERSO_PX', $result_perso['perso_px']);
$t->set_var('PERSO_PROCHAIN_NIVEAU', $result_perso['prochain_niveau']);

// affichage dégats et armure
$t->set_var('PERSO_DEGATS', $result_perso['degats']);
$t->set_var('PERSO_ARMURE', $result_perso['armure']);

// position
$t->set_var('PERSO_POS_X', $result_perso['posx']);
$t->set_var('PERSO_POS_Y', $result_perso['posy']);
$t->set_var('PERSO_ETAGE', $result_perso['etage']);

// passage niveau

if ($result_perso['perso_px'] >= $result_perso['prochain_niveau'])
    $passage_niveau = '<a href="' . $chemin . '/niveau.php"><b>Passer au niveau supérieur ! </b>(6 PA)</a><br><hr />';
else
    $passage_niveau = '';
$t->set_var('PASSAGE_NIVEAU', $passage_niveau);

// Quête avec perso
if ($result_perso['quete'] == 1)
    $perso_quete = "<a href=\"$chemin/quete_perso.php\"><b>Quête</b></a><hr />";
else
    $perso_quete = '';
$t->set_var('PERSO_QUETE', $perso_quete);

// lieux
$perso_lieu = "";
if ($result_perso['lieu'] == 1)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['url'] != null && !empty($tab_lieu['url']))
    {
        $nom_lieu   = $result_perso['nom_lieu'];
        $libelle    = $result_perso['desc_lieu'];
        $perso_lieu = "<a href=\"$chemin/lieu.php\"><b>" . $result_perso['nom_lieu'] . "</b> (" . $result_perso['desc_lieu'] . ")</a><hr />";
    }
}
$t->set_var('PERSO_LIEU', $perso_lieu);

//messagerie
$nb_msg           = $result_perso['nb_mess'];
if ($nb_msg != 0)
    $perso_messagerie = "<b>Messagerie (" . $nb_msg . ")</b>";
else
    $perso_messagerie = "Messagerie";
$t->set_var('PERSO_MESSAGERIE', $perso_messagerie);

// deplacement
$texte_dep = '';
if (!$is_fam)
{
    $is_locked = $db->is_locked($perso_cod);
    if ((!$is_locked) && ($droit['controle'] != 'O'))
    {
        $texte_dep .= "<img src=\"" . G_IMAGES . "deplacement.gif\" alt=\"\"> ";
        //$pa_n = $db->get_pa_dep($perso_cod);
        $texte_dep .= "<a href=\"$chemin/deplacement.php\">";
        $texte_dep .= "Déplacement (" . $result_perso['pa_dep'] . " PA)";
        $texte_dep .= "</a>";
        $texte_dep .= "<br>";
    }
    if (($is_locked) && ($droit['controle'] != 'O'))
    {
        $texte_dep .= "<img src=\"" . G_IMAGES . "fuite.gif\" alt=\"\"> ";
        if ($result_perso['pa'] >= $result_perso['pa_dep'])
        {
            $texte_dep .= "<a href=\"$chemin/deplacement.php\">";
        }
        $texte_dep .= "Fuite (" . $result_perso['pa_dep'] . " PA)";
        if ($result_perso['pa'] >= $result_perso['pa_dep'])
        {
            $texte_dep .= "</a>";
        }
        $texte_dep .= "<br>";
    }
}
$t->set_var('TEXTE_DEP', $texte_dep);

// ramasser
$ramasser = '';
if (($db->nb_obj_sur_case($perso_cod) != 0) || ($db->nb_or_sur_case($perso_cod)))
{
    $pa_ramasse = $result_perso['pa_ramasse'];
    $ramasser   = '<img src="' . G_IMAGES . 'ramasser.gif" alt=""> ';
    if ($result_perso['pa'] >= $pa_ramasse)
        $ramasser .= "<a href=\"$chemin/ramasser.php\">";
    $ramasser .= "Ramasser (" . $pa_ramasse . "PA)";
    if ($result_perso['pa'] >= $pa_ramasse)
    {
        $ramasser .= "</a>";
    }
}
$t->set_var('RAMASSER', $ramasser);

// Transactions
if ($transaction > 0)
    $perso_transactions = "<b>Transactions (" . $transaction . ")</b>";
else
    $perso_transactions = "Transactions";
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
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/controle_interaction_4e.php">Intéractions 4e persos</a><br>
	<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/controle_triche.php">Visu de la triche (bêta)</a><br>';
}
else
    $controle = '';

// Modif perso
if ($droit['modif_perso'] == 'O')
    $modif_perso = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_perso_edit.php">Modif. perso</a><br>';
else
    $modif_perso = '';

// modif monstre
if ($droit['modif_gmon'] == 'O')
    $modif_monstre = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_type_monstre_edit.php">Modif. types monstre</a><br>
		<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_repartition_monstres.php">Modif. répart. monstres</a><br>';
else
    $modif_monstre = '';

// modif carte
if ($droit['carte'] == 'O')
    $droit_carte = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_etage.php">Modif. étages</a><br>';
else
    $droit_carte = '';

// controle admin
if ($droit['controle_admin'] == 'O')
    $controle_admin = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/controle_admins.php">Controle admins</a><br>';
else
    $controle_admin = '';

// gestion_droits
if ($droit['droits'] == 'O')
{
    $gestion_droits = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_gestion_droits.php">Gestion des droits</a><br>';
    $gestion_droits .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_params.php">Gestion des paramètres</a><br>';
}
else
    $gestion_droits = '';

// modif objets
if ($droit['objet'] == 'O')
    $modif_objets = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_objet_generique_edit.php">Gestion objets generiques</a><br>';
else
    $modif_objets = '';

// enchantements
if ($droit['enchantements'] == 'O')
{
    $droit_enchantement = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_enchantements.php">Enchantements</a><br>';
    $droit_enchantement .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_enluminure.php">Enluminure</a><br>';
    $droit_enchantement .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_magie.php">Modif. sorts</a><br>';
    $droit_enchantement .= '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_bonusmalus.php">Modif. bonus/malus</a><br>';
}
else
    $droit_enchantement = '';

// potions
if ($droit['potions'] == 'O')
    $droit_potion = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_potions.php">Création de potions</a><br>';
else
    $droit_potion = '';

// acces logs
if ($droit['acces_log'] == 'O')
    $droit_logs = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_visu_logs.php">Voir les logs</a><br>';
else
    $droit_logs = '';

// modif perso
if ($droit['modif_perso'] == 'O')
    $quete_auto = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_quete_auto_edit.php">Quetes auto</a><br>';
else
    $quete_auto = '';

// news
if ($droit['news'] == 'O')
    $news = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_news.php">Lancer une news</a><br>';
else
    $news = '';

// animations
if ($droit['animations'] == 'O')
    $animations = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_animations.php">Animations</a><br>';
else
    $animations = '';

// factions
if ($droit['factions'] == 'O')
    $factions = '<img src="' . G_IMAGES . 'evenements.gif" alt=""> <a href="' . $chemin . '/admin_factions.php">Factions</a><br>';
else
    $factions = '';

// admin_echoppe
if ($admin_echoppe == 'O')
    $echoppe = '<img src="' . G_IMAGES . 'inventaire.gif" alt=""> <a href="' . $chemin . '/admin_echoppe.php">Admin. échoppes</a><br>';
else
    $echoppe = '';

// gerant
if ($gerant == 'O')
    $gerant = '<img src="' . G_IMAGES . 'inventaire.gif" alt=""> <a href="' . $chemin . '/gere_echoppe.php">Gestion échoppes</a><br>';
else
    $gerant = '';

// voie magique
$req          = 'select count (1) as nv5 from perso, perso_nb_sorts_total, sorts where perso_cod = pnbst_perso_cod and pnbst_sort_cod = sort_cod and sort_niveau >= 5 and pnbst_nombre > 0 and perso_voie_magique = 0 and perso_cod = ' . $perso_cod;
$db->query($req);
$db->next_record();
$nv5          = $db->f('nv5');
$req          = 'select count(1) as mem from perso_sorts, perso where psort_perso_cod = perso_cod and perso_type_perso = 1 and perso_cod = ' . $perso_cod;
$db->query($req);
$db->next_record();
$mem          = $db->f('mem');
if ($nv5 > 0 && $mem > 5)
    $voie_magique = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/choix_voie_magique.php">Voie magique</a><br>';
else
    $voie_magique = '';
$t->set_var('VOIE_MAGIQUE', $voie_magique);

//Enluminure
if ($is_enlumineur)
    $enlumineur = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/enluminure_general.php">Enluminure</a><br>';
else
    $enlumineur = '';
$t->set_var('ENLUMINEUR', $enlumineur);

// potions
if ($potions == 1)
    $potion = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/comp_potions.php">Alchimie</a><br>';
else
    $potion = '';
$t->set_var('POTION', $potion);

//religion
if ($religion == 1 || $fidele_gerant == 'O' || $admin_dieu == 'O')
    $religion = '<img src="' . G_IMAGES . 'magie.gif" alt=""> <a href="' . $chemin . '/religion.php">Religion</a><br>';
else
    $religion = '';
$t->set_var('RELIGION', $religion);

// Compétences spéciales
$commandement = '';
$enseignement = '';
$creuser      = '';
$vol          = '';
$req          = "select pcomp_pcomp_cod from perso_competences where pcomp_pcomp_cod IN(80,81,82,83,84,85,86) and pcomp_perso_cod = $perso_cod";
$db->query($req);
while ($db->next_record())
{
    $comp = $db->f("pcomp_pcomp_cod");
    switch ($comp)
    {
        case 80 :
            $commandement = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/comp_commandement.php">Commandement</a><br>';
            break;
        case 81 :
            $enseignement = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/comp_enseignement.php">Enseignement</a><br>';
            break;
        case 83 :
            $creuser      = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/objets/pioche.php">Creuser</a><br>';
            break;
        case 84 :
        case 85 :
        case 86 :
            $vol          = '<img src="' . G_IMAGES . 'concentration.gif" alt=""> <a href="' . $chemin . '/comp_vol.php">Vol</a><br>';
            break;
    }
}
$req = "select perso_superieur_cod from perso_commandement where $perso_cod = perso_subalterne_cod";
$db->query($req);
if ($db->next_record())
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
   $option_monstre . $commandement != '')
{
    $gestion_droits .= '<hr />';
}
if ($modif_perso . $modif_monstre . $modif_objets != '' &&
   $droit_carte . $droit_enchantement . $droit_potion .
   $quete_auto . $factions . $news . $animations .
   $echoppe . $gerant .
   $option_monstre . $commandement != '')
{
    $modif_objets .= '<hr />';
}
if ($droit_carte . $droit_enchantement . $droit_potion != '' &&
   $quete_auto . $factions . $news . $animations .
   $echoppe . $gerant .
   $option_monstre . $commandement != '')
{
    $droit_potion .= '<hr />';
}
if ($quete_auto . $factions . $news . $animations != '' &&
   $echoppe . $gerant .
   $option_monstre . $commandement != '')
{
    $animations .= '<hr />';
}
if ($echoppe . $gerant != '' &&
   $option_monstre . $commandement != '')
{
    $gerant .= '<hr />';
}
if ($controle . $controle_admin . $droit_logs . $gestion_droits .
   $modif_perso . $modif_monstre . $modif_objets .
   $droit_carte . $droit_enchantement . $droit_potion .
   $quete_auto . $factions . $news . $animations .
   $echoppe . $gerant .
   $option_monstre . $commandement != '')
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

if ($is_milice == 1)
    $milice = '<img src="' . G_IMAGES . 'attaquer.gif" alt=""><a href="' . $chemin . '/milice.php">Milice</a><br>';
else
    $milice = '';

if ($is_vampire != 0)
{
    ?>
    <img src="<?php echo G_IMAGES; ?>magie.gif" alt=""> <a href="<?php echo $chemin; ?>/vampirisme.php">Vampirisme</a><br>
    <?php
}

// gestion des vote
// 

$totalXpGagne = 0;
try {
    $req_Vote= "SELECT compte_vote_cod, compte_vote_total_px_gagner, compte_vote_nbr, 
                    compte_vote_compte_cod
               FROM public.compte_vote  where compte_vote_compte_cod=".$compt_cod;
    $db->query($req_Vote);

    if($db->next_record())
    {
        $totalXpGagne = $db->f('compte_vote_total_px_gagner');
    }
} catch (Exception $e) {
    $totalXpGagne = 0;
}



$req_NbrVote= "SELECT count(*)as compte_vote_nbr
               FROM public.compte_vote_ip  where compte_vote_compte_cod=".$compt_cod."and compte_vote_pour_delain = true";
$db->query($req_NbrVote);
$db->next_record();
$nbrVote = $db->f('compte_vote_nbr');

$req_NbrVote_mois= "SELECT count(*)as compte_vote_nbr_mois
               FROM public.compte_vote_ip  where compte_vote_compte_cod=".$compt_cod."and compte_vote_pour_delain = true  and to_char(compte_vote_date, 'yyyy-mm') = to_char(current_date, 'yyyy-mm')";
$db->query($req_NbrVote_mois);
$db->next_record();
$nbrVoteMois = $db->f('compte_vote_nbr_mois');


$req_vote_a_valid = "SELECT count(*) as voteavalider
  FROM public.compte_vote_ip where compte_vote_verifier=false and to_char(compte_vote_date, 'yyyy-mm') = to_char(current_date, 'yyyy-mm') and compte_vote_compte_cod=".$compt_cod;
$db->query($req_vote_a_valid);
$db->next_record();
$VoteAValider= $db->f('voteavalider');




$req_vote_refu = "SELECT count(*) as refus
  FROM public.compte_vote_ip where compte_vote_pour_delain=false
            and compte_vote_verifier  = true
            and to_char(compte_vote_date, 'yyyy-mm') = to_char(current_date, 'yyyy-mm')  
            and compte_vote_compte_cod=".$compt_cod;
$db->query($req_vote_refu);
$db->next_record();
$votesRefusee = $db->f('refus');
?>
