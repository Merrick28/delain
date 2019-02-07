<?php
if (!isset($is_log))
{
    $is_log = 'N';
}


// affichage d'un bloc perso
function affiche_perso($perso_cod)
{
    global $type_flux;
    global $is_log;
    global $twig;

    $perso = new perso;
    $perso->charge($perso_cod);

    $perso_position = new perso_position();
    $perso_position->getByPerso($perso->perso_cod);

    $position = new positions();
    $position->charge($perso_position->ppos_pos_cod);

    $etage = new etage();
    $etage->getByNumero($position->pos_etage);


    $desc = nl2br(htmlspecialchars(str_replace('\'', '’', $perso->perso_description)));

    if ($perso->perso_avatar == '')
    {
        $avatar = G_IMAGES . $perso->perso_race_cod . "_" . $perso->perso_sex . ".png";
    } else
    {
        $avatar = $type_flux . G_URL . "avatars/" . $perso->perso_avatar;
    }
    //
    // Partie permier avril
    //
    //$avatar = G_URL . "avatars/" . $aff_avat;
    $annee_en_cours = date('Y');
    $debut_1avril   = mktime(0, 0, 1, 4, 1, $annee_en_cours);
    $fin_1avril     = mktime(0, 0, 1, 4, 2, $annee_en_cours);
    $is1avril       = false; //en 2018 on change la blague!// time() > $debut_1avril && time() < $fin_1avril;
    //
    // fin 1er avril
    //

    $limite_niveau_actuel = $perso->px_limite_actuel();
    $limite_niveau        = $perso->px_limite();
    $barre_xp             = $perso->barre_xp();
    $barre_hp             = $perso->barre_hp();
    $barre_energie        = $perso->barre_energie();
    $dlt_passee           = $perso->dlt_passee();

    // récupération énergie divine pour les familiers divins
    $barre_divine   = -1;
    $energie_divine = -1;
    if ($perso->perso_gmon_cod == 441)
    {
        $dieu_perso = new dieu_perso();
        $dieu_perso->getByPersoCod($perso->perso_cod);
        $barre_divine = $perso->barre_divin();
    }


    $myguilde = "Pas de guilde";

    $guilde_perso = new guilde_perso();
    if ($guilde_perso->get_by_perso($perso->perso_cod))
    {
        if ($guilde_perso->pguilde_valide == 'O')
        {
            $guilde = new guilde;
            if ($guilde->charge($guilde_perso->pguilde_guilde_cod))
            {
                $myguilde = 'Guilde : ' . $guilde->guilde_nom;
            }
        }
    }


    $tours_impalpable = ($perso->perso_nb_tour_intangible > 1) ? ' tours' : ' tour';
    $impalpable       = ($perso->perso_tangible == 'N') ? '<br /><em>Impalpable (' . $perso->perso_nb_tour_intangible . $tours_impalpable . ')</em>' : '';

    $template     = $twig->load('_tab_switch_perso.twig');
    $options_twig = array(
        'PERSO'         => $perso,
        'POSITION'      => $position,
        'ETAGE'         => $etage,
        'MYGUILDE'      => $myguilde,
        'AVATAR'        => $avatar,
        'TYPE_FLUX'     => $type_flux,
        'G_URL'         => G_URL,
        'IS_LOG'        => $is_log,
        'IMPALPABLE'    => $impalpable,
        'G_IMAGES'      => G_IMAGES,
        'BARRE_HP'      => $barre_hp,
        'BARRE_ENERGIE' => $barre_energie,
        'DIEU_PERSO'    => $dieu_perso,
        'BARRE_DIVINE'  => $barre_divine,
        'BARRE_XP'      => $barre_xp


    );
    echo $template->render($options_twig);


}

function affiche_case_perso_vide()
{
    global $type_flux, $compt_cod, $twig;
    $template     = $twig->load('_tab_switch_perso_vide.twig');
    $options_twig = array(
        'TYPE_FLUX' => $type_flux,
        'G_URL'     => G_URL,
        'G_IMAGES'  => G_IMAGES,
        'COMPT_COD' => $compt_cod
    );
    echo $template->render($options_twig);
}

function affiche_case_monstre_vide()
{
    global $twig;
    $template     = $twig->load('_tab_switch_monstre_vide.twig');
    $options_twig = array(
        'G_IMAGES' => G_IMAGES
    );
    echo $template->render($options_twig);
}

/***************************************************************/
/* Fin des fonctions                                           */
/***************************************************************/
//
/***************************************************************/
/* Début de la page										    	*/
/***************************************************************/
$compte = new compte;
$compte->charge($compt_cod);


$nb_perso_max   = $compte->compt_ligne_perso * 3;
$nb_perso_ligne = 3;
$ok_4           = $compte->autorise_4e_global();


if ($ok_4)
{
    $nb_perso_max   = $compte->compt_ligne_perso * 4;
    $nb_perso_ligne = 4;
}
$taille = 100 / $nb_perso_ligne;
$type_4 = $compte->compt_type_quatrieme;

/*********************/
/* Persos classiques */
/*********************/
$tab_perso     = $compte->getPersosActifs(false, true);
$perso_normaux = array();
$quatriemes    = array();

$cpt_normaux    = 0;
$cpt_quatriemes = 0;

foreach ($tab_perso as $detail_perso)
{
    if ($detail_perso->perso_type_perso == 2 || $detail_perso->perso_pnj == 2)
    {
        $quatriemes[] = $detail_perso->perso_cod;
    } else
    {
        $perso_normaux[] = $detail_perso->perso_cod;
    }
}


if (sizeof($quatriemes) == 0 && $ok_4)
{
    $quatriemes[] = false;
}

while (sizeof($perso_normaux) % 3 !== 0)
{
    $perso_normaux[] = false;
}
$premier_perso = (isset($perso_normaux[0])) ? $perso_normaux[0] : -1;
if ($premier_perso == -1)
{
    $premier_perso = (isset($quatriemes[0])) ? $quatriemes[0] : -1;
}

echo '<div class="row row-eq-height">';     //Debut ligne des persos
$numero_quatrieme = -1;
$cpt              = 0;
while ($cpt_normaux < sizeof($perso_normaux) || $cpt_quatriemes < sizeof($quatriemes))
{
    // Est-on sur la case réservée au quatrième ?
    $case_quatrieme = $ok_4 && ($cpt % $nb_perso_ligne == $nb_perso_ligne - 1);

    echo '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';

    // Une case normale
    if (!$case_quatrieme)
    {
        // On a un perso à afficher
        if (!empty($perso_normaux[$cpt_normaux]))
        {
            affiche_perso($perso_normaux[$cpt_normaux]);
        } else
        {
            affiche_case_perso_vide();
        }

        $cpt_normaux++;
    }

    // Une case de 4ème perso
    if ($case_quatrieme)
    {
        // On a un perso à afficher
        if (!empty($quatriemes[$cpt_quatriemes]))
        {
            affiche_perso($quatriemes[$cpt_quatriemes]);
        } elseif ($type_4 != 2)
        {
            affiche_case_perso_vide();
        } else
        {
            affiche_case_monstre_vide();
        }

        $cpt_quatriemes++;
    }

    echo '</div>';      // fin de case!
    $cpt++;
}
echo '</div>';               //Fin de ligne des persos


global $twig;
$template     = $twig->load('_tab_switch_visu_evt.twig');
$options_twig = array(
    'G_IMAGES'       => G_IMAGES,
    'G_URL'          => G_URL,
    'PREMIER_PERSO'  => $premier_perso,
    'IS_LOG'         => $is_log,
    'NB_PERSO_LIGNE' => $nb_perso_ligne
);
echo $template->render($options_twig);


/*************/
/* Familiers */
/*************/
$tab_fam = $compte->getPersosActifs(true, false);


if (count($tab_fam) != 0)
{
    //echo '<tr><td colspan="3"><hr><div class="titre">Familiers : </div></td></tr>';
    echo '<div class="row" style="padding-left: 4px; padding-right: 4px;"><div class="col-lg-12 titre">Familiers : </div></div>';

    echo '<div class="row row-eq-height">';   //Debut ligne des familiers

    $alias_perso = 0;
    //for ($cpt = 0; $cpt < count($tab_fam); $cpt++)
    foreach ($tab_fam as $fam)
    {

        echo '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';


        affiche_perso($fam->perso_cod);

        echo '</div>';

    }
    echo '</div>';
}

/******************************************/
/* Comptes sittés ?					   */
/******************************************/
$tab_perso_sit = $compte->getPersosSittes(false, true);

if (count($tab_perso_sit) != 0)
{
    //
    // là on a des persos sittés, donc, on va quand même regarder ce qui se passe
    //
    //echo '<tr><td colspan="3"><hr><div class="titre">Persos sittés : </div></td></tr>';

    echo '<div class="row" style="padding-left: 4px; padding-right: 4px;"><div class="col-lg-12 titre">Persos sittés : </div></div>';

    echo '<div class="row row-eq-height">';   //Debut ligne des persos+familiers sittés


    foreach ($tab_perso_sit as $sit)
    {
        echo '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';
        affiche_perso($sit->perso_cod);
        echo '</div>';

    }
    //
    // bon, on sait qu'on a sitté des persos, maintenant, on va quand même voir s'il y a des familiers
    //

    $tab_fam_sit = $compte->getPersosSittes(true, false);

    if (count($tab_fam_sit) != 0)
    {

        $nb_perso    = $nb_perso_max;
        $alias_perso = 0;
        foreach ($tab_fam_sit as $fam_sit)
        {
            // 2019-02-07@Marlyza : Je ne sais pas trop pourquoi il y a ce compteur, mais il duplique les familiers sittés!
            //for ($cpt = 0; $cpt < $nb_perso_max; $cpt++)
            //{
                echo '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';
                affiche_perso($fam_sit->perso_cod);
                echo '</div>';
            //}
        }
    }
    echo '</div>';               //Fin de ligne des persos+familiers sittés
}
