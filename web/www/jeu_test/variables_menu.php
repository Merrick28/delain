<?php
/**
 * Modif Merrick
 * Cette page doit aussi prendre en compte les variables twig pour commencer à remplacer
 * le moteur de template de phplib
 *
 */
$__VERSION = "20200506";    // A changer aussi dans constante.php

$benchmark       = $profiler->start('Variables menu');
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

$param = new parametres();

$compte = new compte;
$compte = $verif_connexion->compte;

$perso = new perso;
$perso = $verif_connexion->perso;


// variables du perso
$is_refuge     = $perso->is_refuge();
$is_fam        = $perso->is_fam();
$is_intangible = $perso->isIntangible();
if ($is_intangible)
{
    $pa_ramasse = $param->getparm(42);
} else
{
    $pa_ramasse = $param->getparm(41);
}
$degats_perso = $perso->degats_perso();
$det_deg      = explode(";", $degats_perso);
$deg_min      = $det_deg[0];
$deg_max      = $det_deg[1];

$gerant = 'N';
$mg     = new magasin_gerant();
if ($mg->getByPersoCod($perso->perso_cod))
{
    $gerant = 'O';
}


$transaction        = $perso->transactions();
$nb_evt_non_lu      = sizeof($perso->getEvtNonLu());


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


// position
$var_menu_ppos = new perso_position();
$var_menu_ppos->getByPerso($perso->perso_cod);
$var_menu_pos = new positions();
$var_menu_pos->charge($var_menu_ppos->ppos_pos_cod);
$var_menu_etage = new etage();
$var_menu_etage->getByNumero($var_menu_pos->pos_etage);


// Animation Léno 2019: Concours de barde
$animation = "";
$pdo       = new bddpdo;
$req       =
    "SELECT cbar_saison FROM concours_barde where (now()>cbar_date_teaser or now()>=cbar_date_ouverture) and now()<=cbar_fermeture order by cbar_saison desc limit 1 ";
$stmt      = $pdo->query($req);
if ($rows = $stmt->fetch())
{
    $animation = '<hr /><a href="' . $type_flux . G_URL . '/jeu_test/concours_barde.php">Concours de Barde</a>';
}



// lieux
$tab_lieu   = array();
if ($perso->is_lieu())
{
    $tab_lieu = $perso->get_lieu();

}

//messagerie
$mdest  = new messages_dest();
$tab    = $mdest->getByPersoNonLu($perso->perso_cod);
$nb_msg = count($tab);
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
        } else
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
            } else
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
        } else
        {
            if (substr($_aff_nom, 0, 31) == "Image démoniaque, Familier de ")
            {
                $_aff_nom = "Kirga de " . substr($_aff_nom, 31);
            } else
            {
                if (substr($_aff_nom, 0, 27) == "Kirga-Uh-Kmot, Familier de ")
                {
                    $_aff_nom = "Kirga de " . substr($_aff_nom, 27);
                } else
                {
                    $_aff_nom = preg_replace("/ \(n° (\d+)\)$/", "", $_aff_nom);
                }
            }
        }

        // ajout d'info PA, PV et DLT
        $_blessure     = 100 * (1 * $result["perso_pv"]) / (1 * $result["perso_pv_max"]);
        $_aff_perso_pv =
            $_blessure > 50 ? ($_blessure == 100 ? $result["perso_pv"] : '<span style="color:lightgreen;font-size:9px;font-weight:bold;">' . $result["perso_pv"] . '</span>') : ($_blessure > 25 ? '<span style="color:#ffd700;font-size:9px;font-weight:bold;">' . $result["perso_pv"] . '</span>' : '<span style="color:#ff69b4;font-size:9px;font-weight:bold;">' . $result["perso_pv"] . '</span>');
        $_aff_perso_pa =
            $result["perso_pa"] == 0 ? $result["perso_pa"] : '<span style="color:lightgreen;font-size:9px;font-weight:bold;">' . $result["perso_pa"] . '</span>';
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
        } else
        {
            if ($result["perso_cod"] == $perso_cod)
            {
                $liste_boutons .= '<div class="' . $col_class . '">' . $_aff_msg . '<button id=' . $result["perso_cod"] . ' class="button-switch-act">' . $_aff_nom . '</button></div>';
            } else
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
        $barre_switch_rapide =
            '<div id="colonne0" data-switch-bar="standard" style="display:block"><div class="container-fluid"><div class="row">' . $liste_boutons . '</div></div></div>';
    } else
    {
        // Au dessus de 16 persos, il doit s'agir d'un admin monstres, on cache la barre par défaut (sera affiché si la souris passe en haut de l'écran
        $barre_switch_rapide =
            '<div id="colonne0" data-switch-bar="autohide" style="display:none"><div class="container-fluid"><div class="row">' . $liste_boutons . '</div></div></div>';
    }
}


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
$benchmark->stop();