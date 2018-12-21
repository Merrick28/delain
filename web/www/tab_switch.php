<?php
if (!isset($is_log))
{
    $is_log = 'N';
}
$db = new base_delain;
/***************************************************************/
/* Fonctions pour l'affichage des barres de santé et XP		*/
/***************************************************************/
// barre XP
function barre_xp($perso_px, $limite_niveau_actuel, $limite_niveau)
{
    $barre_xp = '0';
    if (($perso_px - $limite_niveau_actuel) < 0)
    {
        //$barre_xp = 'negative';   // Gestion de la barre au % pres
        return $barre_xp;
    }
    $niveau_xp = ($perso_px - $limite_niveau_actuel);
    $div_xp = ($limite_niveau - $limite_niveau_actuel);

    $barre_xp = round(100 * $niveau_xp / $div_xp);
    if (($barre_xp >= 98) && ($niveau_xp < $div_xp))
    {
        $barre_xp = 98;
    } else if (($barre_xp <= 2) && ($niveau_xp > 0))
    {
        $barre_xp = 2;
    } else if ($barre_xp < 0)
    {
        $barre_xp = 0;
    } else if ($barre_xp >= 100)
    {
        $barre_xp = 100;
    }
    return $barre_xp;
}

// barre de sante
function barre_hp($perso_pv, $perso_pv_max)
{
    //$barre_hp = floor(($perso_pv / $perso_pv_max) * 10) * 10;        // Gestion de la barre au % pres
    $barre_hp = round(($perso_pv / $perso_pv_max) * 100);
    if (($barre_hp >= 98) && ($perso_pv < $perso_pv_max))
    {
        $barre_hp = 98;
    } else if (($barre_hp <= 2) && ($perso_pv > 0))
    {
        $barre_hp = 2;
    } else if ($barre_hp < 0)
    {
        $barre_hp = 0;
    } else if ($barre_hp >= 100)
    {
        $barre_hp = 100;
    }
    return $barre_hp;
}

// barre d'énergie
function barre_energie($perso_energie)
{
    //$barre_energie = floor(($perso_energie / 100) * 10) * 10;       // Gestion de la barre au % pres
    $barre_energie = round($perso_energie);
    if ($barre_energie <= 0)
    {
        $barre_energie = 0;
    } else if ($barre_energie >= 100)
    {
        $barre_energie = 100;
    } else if ($barre_energie >= 98)
    {
        $barre_energie = 98;
    } else if ($barre_energie <= 2)
    {
        $barre_energie = 2;
    }
    return $barre_energie;
}

// barre d'énergie divine (pour familiers divins uniquement)
function barre_divine($perso_divine)
{
    $barre_divine = round(100 * $perso_divine / 200);
    if ($barre_divine <= 0)
    {
        $barre_divine = 0;
    } else if ($barre_divine >= 100)
    {
        $barre_divine = 100;
    } else if ($barre_divine >= 98)
    {
        $barre_divine = 98;
    } else if ($barre_divine <= 2)
    {
        $barre_divine = 2;
    }
    return $barre_divine;
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
    $debut_1avril = mktime(0, 0, 1, 4, 1, $annee_en_cours);
    $fin_1avril = mktime(0, 0, 1, 4, 2, $annee_en_cours);
    $is1avril = false; //en 2018 on change la blague!// time() > $debut_1avril && time() < $fin_1avril;
    //
    // fin 1er avril
    //

    $limite_niveau_actuel = $perso->px_limite_actuel();
    $limite_niveau = $perso->px_limite();
    $barre_xp = barre_xp($perso->perso_px, $limite_niveau_actuel, $limite_niveau);
    $barre_hp = barre_hp($perso->perso_pv, $perso->perso_pv_max);
    $barre_energie = barre_energie($perso->perso_energie);
    $dlt_passee = $perso->dlt_passee();

    // récupération énergie divine pour les familiers divins
    $barre_divine = -1;
    $energie_divine = -1;
    if ($perso->perso_gmon_cod == 441)
    {
        $dieu_perso = new dieu_perso();
        $dieu_perso->getByPersoCod($perso->perso_cod);
        $barre_divine = barre_divine($dieu_perso->dper_points);
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
    $impalpable = ($perso->perso_tangible == 'N') ? '<br /><em>Impalpable (' . $perso->perso_nb_tour_intangible . $tours_impalpable . ')</em>' : '';

    /**
     * Sauvegarde du code du premier avril
     *
     * if ($is1avril)
     * {
     * $niveau = mt_rand(1, 10);
     * $impalpable = ' - <em>Impalpable</em>';
     * $dbavril = new base_delain;
     * $dbavril->query('select gmon_nom from monstre_generique where gmon_cod < 50 and gmon_niveau < 5 and gmon_cod <> 7
     * order by random()
     * limit 1');
     * $dbavril->next_record();
     * $impalpable .= '<br /><em>Tué par un(e) ' . $dbavril->f('gmon_nom') . '</em>';
     * $barre_hp = barre_hp($db->f("perso_pv") / 3, $db->f("perso_pv_max"));
     * }*/

    $template = $twig->load('_tab_switch_perso.twig');
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
    $template = $twig->load('_tab_switch_perso_vide.twig');
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
    $template = $twig->load('_tab_switch_monstre_vide.twig');
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


$req = "select compt_ligne_perso, autorise_4e_perso(compt_quatre_perso, compt_dcreat) OR autorise_4e_monstre(compt_quatre_perso, compt_dcreat) as autorise_quatrieme, compt_type_quatrieme ";
$req .= " from compte where compt_cod = " . $compt_cod;
$db->query($req);
$db->next_record();

$nb_perso_max = $compte->compt_ligne_perso * 3;
$nb_perso_ligne = 3;
$ok_4 = $compte->autorise_4e_global();


if ($ok_4)
{
    $nb_perso_max = $compte->compt_ligne_perso * 4;
    $nb_perso_ligne = 4;
}
$taille = 100 / $nb_perso_ligne;
$type_4 = $compte->compt_type_quatrieme;

/*********************/
/* Persos classiques */
/*********************/
$tab_perso = $compte->getPersosActifsSansFam();
$perso_normaux = array();
$quatriemes = array();

$cpt_normaux = 0;
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
$cpt = 0;
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


echo '<div class="row"><div class="col-lg-12">';
echo '<center><table>';
echo "<tr><td colspan='$nb_perso_ligne' ><span class='bouton'>";
echo '<input type="button" class="bouton" onClick="javascript:window.open(\'' . $type_flux . G_URL . 'visu_derniers_evt.php?visu_perso=' . $premier_perso . '&is_log=' . $is_log . '&voir_tous=1\',\'evenements\',\'scrollbars=yes,resizable=yes,width=500,height=300\');" title="Voir les derniers événements de tous les personnages" value="Voir tous les événements" style="width:200px;"/></span>&nbsp;&nbsp;';
echo "<span class='bouton'><input type='button' class='bouton' onClick='javascript:document.login.perso.value=$premier_perso; document.login.activeTout.value=1; document.login.submit();' title='Activer toutes les DLT' value='Activer toutes les DLT' style='width:200px;'/></span></td></tr>";
echo "</table></center>";
echo '</div></div>';


/*************/
/* Familiers */
/*************/
$req_perso = "select pfam_familier_cod,perso_cod
	from perso,perso_compte,perso_familier
	where pcompt_compt_cod = $compt_cod
	and pcompt_perso_cod = pfam_perso_cod
	and pfam_familier_cod = perso_cod
	and perso_actif = 'O'
	and perso_type_perso = 3
	order by pfam_perso_cod ";
$db->query($req_perso);
if ($db->nf() != 0)
{
    //echo '<tr><td colspan="3"><hr><div class="titre">Familiers : </div></td></tr>';
    echo '<div class="row" style="padding-left: 4px; padding-right: 4px;"><div class="col-lg-12 titre">Familiers : </div></div>';

    echo '<div class="row row-eq-height">';   //Debut ligne des familiers
    $nb_perso = $db->nf();
    $alias_perso = 0;
    for ($cpt = 0; $cpt < $nb_perso_max; $cpt++)
    {
        //if (fmod($cpt, $nb_perso_ligne) == 0)
        //{
        //    echo '<tr>';
        //}
        //echo '<td valign="top" width="' . $taille . '%">';
        echo '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';

        //tableau intérieur
        if ($cpt < $nb_perso)
        {
            $db->next_record();
            affiche_perso($db->f('perso_cod'));
        }
        //fin tableau intérieur
        echo '</div>';

        //echo '</td>';
        //if (fmod(($cpt + 1), $nb_perso_ligne) == 0)
        //{
        //    echo '</tr>';
        //}
    }
    echo '</div>';   //Fin ligne des familiers
}

/******************************************/
/* Comptes sittés ?					   */
/******************************************/
$req_perso = "select pcompt_perso_cod
	from perso,perso_compte,compte_sitting
	where csit_compte_sitteur = $compt_cod
	and csit_compte_sitte = pcompt_compt_cod
	and csit_ddeb <= now()
	and csit_dfin >= now()
	and pcompt_perso_cod = perso_cod
	and perso_actif = 'O'
	and perso_type_perso = 1
	order by perso_cod ";
$db->query($req_perso);
if ($db->nf() != 0)
{
    //
    // là on a des persos sittés, donc, on va quand même regarder ce qui se passe
    //
    //echo '<tr><td colspan="3"><hr><div class="titre">Persos sittés : </div></td></tr>';

    echo '<div class="row" style="padding-left: 4px; padding-right: 4px;"><div class="col-lg-12 titre">Persos sittés : </div></div>';

    echo '<div class="row row-eq-height">';   //Debut ligne des persos+familiers sittés

    $nb_perso_max = $db->nf();
    $nb_perso = $nb_perso_max;
    for ($cpt = 0; $cpt < $nb_perso_max; $cpt++)
    {
        if ($cpt < $nb_perso)
        {
            $db->next_record();
        }
        //if (fmod($cpt, $nb_perso_ligne) == 0)
        //{
        //    echo '<tr>';
        //}
        //echo '<td valign="top" width="' . $taille . '%">';

        echo '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';

        //tableau intérieur
        if ($cpt >= $nb_perso)
        {
            $nom = 'Pas de perso';
            $image = '';
            $barre_pa = '';
            $barre_hp = '';
            $barre_xp = '';
            $enc = '';
            echo '<table width="100%" height="100%" border="0">
				<tr><td height="100%" valign="center" class="soustitre2" style="text-align:center;">Pas de personnage<br></td></tr>
				<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
				<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
				<tr><td><center><img src="' . G_IMAGES . 'noperso.gif"></center></td></tr></table>';
        } else
        {
            affiche_perso($db->f('pcompt_perso_cod'));
        }
        //fin tableau intérieur

        echo '</div>';
        //echo '</td>';
        //if (fmod(($cpt + 1), $nb_perso_ligne) == 0)
        //{
        //    echo '</tr>';
        //}
    }
    //
    // bon, on sait qu'on a sitté des persos, maintenant, on va quand même voir s'il y a des familiers
    //
    $req_perso = "select pfam_familier_cod,perso_cod
		from perso,perso_compte,perso_familier,compte_sitting
		where csit_compte_sitteur = $compt_cod
		and csit_compte_sitte = pcompt_compt_cod
		and csit_ddeb <= now()
		and csit_dfin >= now()
		and pcompt_perso_cod = pfam_perso_cod
		and pfam_familier_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso = 3
		order by pfam_perso_cod ";
    $db->query($req_perso);
    if ($db->nf() != 0)
    {

        $nb_perso_max = $db->nf();
        $nb_perso = $nb_perso_max;
        $alias_perso = 0;
        for ($cpt = 0; $cpt < $nb_perso_max; $cpt++)
        {
            if ($cpt < $nb_perso)
            {
                $db->next_record();
            }
            //if (fmod($cpt, $nb_perso_ligne) == 0)
            //{
            //    echo '<tr>';
            //}
            //echo '<td valign="top" width="' . $taille . '%">';
            //echo '<!--' . $cpt . '-' . $nb_perso_max . '-' . $nb_perso . '-->';

            echo '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';

            //tableau intérieur
            if ($cpt < $nb_perso)
            {
                affiche_perso($db->f('perso_cod'));
            }
            //fin tableau intérieur
            echo '</div>';

            //echo '</td>';
            //if (fmod(($cpt + 1), $nb_perso_ligne) == 0)
            //{
            //    echo '</tr>';
            //}
        }
    }
    echo '</div>';               //Fin de ligne des persos+familiers sittés
}
