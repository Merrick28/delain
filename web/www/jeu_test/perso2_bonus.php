<?php

$perso = new perso;
$perso->charge($perso_cod);
if (isset($_REQUEST['ch_util']))
{
    $perso->perso_utl_pa_rest = $_REQUEST['ch_util'];
    $perso->stocke();
}

//
/* Concentration */
//
$contenu_page      .= '<div class="titre">Concentration</div>';
$concentration     = new concentrations();
$has_concentration = false;

if ($concentration->getByPerso($perso->perso_cod))
{
    $has_concentration = true;
}

//
/* BONUS PERMANENTS */
//


// Loop=0 pour bonus equipement  Loop=1 bonus temporaire
for ($loop = 0; $loop < 2; $loop++)
{
    $equipement = false;
    if ($loop == 0)
    {
        $equipement = true;
    }
    $contenu_page .= '<div class="titre">' . ($equipement ? "Bonus d'équipement" : "Bonus temporaires") . '</div>
<table ><tr valign="top" ><td style="padding:15px;">';

    $carac_orig            = new carac_orig;
    $tab_carac_orig[$loop] = $carac_orig->getByPersoCumul($perso->perso_cod, $equipement);

    $bonus_carac = array();
    $malus_carac = array();
    $record      = 0;
    //while ($db->next_record()) {
    foreach ($tab_carac_orig[$loop] as $detail_carac_orig)
    {
        $carac      = $detail_carac_orig['corig_type_carac'];
        $bm         = $detail_carac_orig['bonus_carac'];
        $corig_mode = $detail_carac_orig['corig_mode'];
        if ($detail_carac_orig['corig_mode'] == 'E')
        {
            $duree = "Equipement";
        } else
        {
            $duree = ($detail_carac_orig['corig_nb_tours'] == 0) ? $detail_carac_orig['corig_dfin'] :
                $detail_carac_orig['corig_nb_tours'] . ' tour(s)';
        }
        if ($detail_carac_orig['bonus_carac'] > 0)
        {
            $bonus_carac[$loop][$record]    = array();
            $bonus_carac[$loop][$record][0] = $bm;
            $bonus_carac[$loop][$record][1] = $duree;
            $bonus_carac[$loop][$record][2] = $carac;
            $bonus_carac[$loop][$record][3] = $corig_mode;
        } else
        {
            $malus_carac[$loop][$record]    = array();
            $malus_carac[$loop][$record][0] = $bm;
            $malus_carac[$loop][$record][1] = $duree;
            $malus_carac[$loop][$record][2] = $carac;
            $malus_carac[$loop][$record][3] = $corig_mode;
        }
        $record++;
    }

    $tab_bonus[$loop] = $perso->perso_bonus_equipement($equipement);

    if (count($tab_bonus[$loop]) + count($bonus_carac[$loop]) == 0)
    {
        $contenu_page .= '<p>Vous n’avez aucun bonus ' . ($loop == 0 ? "d'équipement " : "") . 'en ce moment.</p>';
    } else
    {
        $contenu_page .= '<table><tr>
	<td class="soustitre2"><strong>Bonus</strong></td>
	<td class="soustitre2"><strong>Valeur</strong></td>
	<td class="soustitre2"><strong>Échéance</strong></td>
	<td class=""></td>
	</tr>';
        foreach ($tab_bonus[$loop] as $key => $detail_bonus)
        {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $db->f('tbonus_libc') . ".png"))
            {
                $img = '/../images/interface/bonus/' . $detail_bonus['tbonus_libc'] . '.png';
            } else
            {
                $img = '/../images/interface/bonus/MALUS.png';
            }
            $tab_bonus[$loop][$key]['img'] = $img;

        }
        foreach ($bonus_carac[$loop] as $key => $bonus)
        {
            $valeur     = $bonus[0];
            $duree      = $bonus[1];
            $carac      = $bonus[2];
            $corig_mode = $bonus[3];
            $signe      = ($valeur >= 0) ? '+' : '';
            $lib_carac  = '';
            switch ($carac)
            {
                case 'FOR':
                    $lib_carac = 'Force';
                    break;
                case 'INT':
                    $lib_carac = 'Intelligence';
                    break;
                case 'CON':
                    $lib_carac = 'Constitution';
                    break;
                case 'DEX':
                    $lib_carac = 'Dextérité';
                    break;
            }
            $bonus_carac[$loop][$key]['lib_carac'] = $lib_carac;
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png"))
            {
                $img = '/../images/interface/bonus/' . $carac . '.png';
            } else
            {
                $img = "/../images/interface/bonus/MALUS.png";
            }
            $bonus_carac[$loop][$key]['img'] = $img;
            $contenu_page                    .= "<tr><td class='soustitre2'><strong>$lib_carac</strong></td>
			<td><div style='text-align:center;'>$signe" . "$valeur</div></td>
			<td><div style='text-align:center;'>" . ($corig_mode == "Equipement" ? "Equipement" : $duree) . "</div></td>
			<td><div style='text-align:center;'>$img</div></td>
			</tr>";
        }
        $contenu_page .= '</table>';
    }

    $contenu_page .= '</td><td style="padding:15px;">';

    $tab_malus[$loop] = $perso->perso_malus_equipement($equipement);

    if (count($tab_malus[$loop]) + count($malus_carac[$loop]) == 0)
    {
        $contenu_page .= '<p>Vous n’avez aucun malus ' . ($loop == 0 ? "d'équipement " : "") . 'en ce moment.</p>';
    } else
    {
        $contenu_page .= '<table><tr>
	<td class="soustitre2"><strong>Malus</strong></td>
	<td class="soustitre2"><strong>Valeur</strong></td>
	<td class="soustitre2"><strong>Échéance</strong></td>
	<td class=""></td>
	</tr>';
        foreach ($tab_malus[$loop] as $key => $detail_malus)
        {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $detail_malus['tbonus_libc'] . ".png"))
            {
                $img = '/../images/interface/bonus/' . $detail_malus['tbonus_libc'] . '.png';
            } else
            {
                $img = '/../images/interface/bonus/MALUS.png';
            }
            $tab_malus[$loop][$key]['img'] = $img;
        }
        foreach ($malus_carac[$loop] as $key => $malus)
        {
            $valeur    = $malus[0];
            $duree     = $malus[1];
            $carac     = $malus[2];
            $signe     = ($valeur >= 0) ? '+' : '';
            $lib_carac = '';
            switch ($carac)
            {
                case 'FOR':
                    $lib_carac = 'Force';
                    break;
                case 'INT':
                    $lib_carac = 'Intelligence';
                    break;
                case 'CON':
                    $lib_carac = 'Constitution';
                    break;
                case 'DEX':
                    $lib_carac = 'Dextérité';
                    break;
            }
            $malus_carac[$loop][$key]['lib_carac'] = $lib_carac;
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png"))
            {
                $img = '/../images/interface/bonus/' . $carac . '.png';
            } else
            {
                $img = '/../images/interface/bonus/MALUS.png';
            }
            $malus_carac[$loop][$key]['img'] = $img;
            $contenu_page .= "<tr><td class='soustitre2'><strong>$lib_carac</strong></td>
			<td><div style='text-align:center;'>$signe" . "$valeur</div></td>
			<td><div style='text-align:center;'>" . ($corig_mode == "Equipement" ? "Equipement" : $duree) . "</div></td>
			<td><div style='text-align:center;'>$img</div></td>
			</tr>";
        }
        $contenu_page .= '</table>';
    }
    $contenu_page .= '</td></tr></table>';
}

$template     = $twig->load('_perso2_bonus.twig');
$options_twig = array(

    'PERSO'             => $perso,
    'PHP_SELF'          => $PHP_SELF,
    'CONENTRATION'      => $concentration,
    'HAS_CONCENTRATION' => $has_concentration,
    'TAB_CARAC_ORIG'    => $tab_carac_orig,
    'TAB_BONUS'         => $tab_bonus,
    'TAB_MALUS'         => $tab_malus,
    'BONUS_CARAC'       => $bonus_carac,
    'MALUS_CARAC'       => $malus_carac


);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));


