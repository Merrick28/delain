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

    $carac_orig     = new carac_orig;
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
            $bonus_carac[$record]    = array();
            $bonus_carac[$record][0] = $bm;
            $bonus_carac[$record][1] = $duree;
            $bonus_carac[$record][2] = $carac;
            $bonus_carac[$record][3] = $corig_mode;
        } else
        {
            $malus_carac[$record]    = array();
            $malus_carac[$record][0] = $bm;
            $malus_carac[$record][1] = $duree;
            $malus_carac[$record][2] = $carac;
            $malus_carac[$record][3] = $corig_mode;
        }
        $record++;
    }

    $tab_bonus = $perso->perso_bonus_equipement($equipement);

    if (count($tab_bonus) + count($bonus_carac) == 0)
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
        foreach ($tab_bonus as $detail_bonus)
        {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $db->f('tbonus_libc') . ".png"))
            {
                $img = '<img src="/../images/interface/bonus/' . $db->f('tbonus_libc') . '.png">';
            } else
            {
                $img = '<img src="/../images/interface/bonus/MALUS.png">';
            }
            $contenu_page .= '<tr><td class="soustitre2"><strong>' . $detail_bonus['tonbus_libelle'] . $detail_bonus['bonus_mode'] == "C" ? " (cumulatif)" : "" . '</strong></td>';
            $signe        = ($detail_bonus['bonus_valeur'] >= 0) ? '+' : '';
            $contenu_page .= '<td><div style="text-align:center;">' . $signe . $detail_bonus['bonus_valeur'] . '</div></td>
		<td><div style="text-align:center;">' . ($detail_bonus['bonus_mode'] == "E" ? " Equipement" : $detail_bonus['bonus_nb_tours'] . " tour(s)") . '</div></td>
		<td style="text-align:center">' . $img . '</td></tr>';
        }
        foreach ($bonus_carac as $bonus)
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
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png"))
            {
                $img = '<img src="/../images/interface/bonus/' . $carac . '.png">';
            } else
            {
                $img = '<img src="/../images/interface/bonus/MALUS.png">';
            }
            $contenu_page .= "<tr><td class='soustitre2'><strong>$lib_carac</strong></td>
			<td><div style='text-align:center;'>$signe" . "$valeur</div></td>
			<td><div style='text-align:center;'>" . ($corig_mode == "Equipement" ? "Equipement" : $duree) . "</div></td>
			<td><div style='text-align:center;'>$img</div></td>
			</tr>";
        }
        $contenu_page .= '</table>';
    }

    $contenu_page .= '</td><td style="padding:15px;">';

    $tab_malus = $perso->perso_malus_equipement($equipement);

    if (count($tab_malus) + count($malus_carac) == 0)
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
        foreach ($tab_malus as $detail_malus)
        {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $detail_malus['tbonus_libc'] . ".png"))
            {
                $img = '<img src="/../images/interface/bonus/' . $detail_malus['tbonus_libc'] . '.png">';
            } else
            {
                $img = '<img src="/../images/interface/bonus/MALUS.png">';
            }

            $contenu_page .= '<tr><td class="soustitre2"><strong>' . $detail_malus['tonbus_libelle'] . ($detail_malus['bonus_mode'] == "C" ? " (cumulatif)" : "") . '</strong></td>';
            $signe        = ($detail_malus['bonus_valeur'] >= 0) ? '+' : '';
            $contenu_page .= '<td><div style="text-align:center;">' . $signe . $detail_malus['bonus_valeur'] . '</div></td>
		<td><div style="text-align:center;">' . ($detail_malus['bonus_mode'] == "E" ? " Equipement" : $detail_malus['bonus_nb_tours'] . " tour(s)") . '</div></td>
		<td style="text-align:center">' . $img . '</td></tr>';
        }
        foreach ($malus_carac as $malus)
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
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png"))
            {
                $img = '<img src="/../images/interface/bonus/' . $carac . '.png">';
            } else
            {
                $img = '<img src="/../images/interface/bonus/MALUS.png">';
            }
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
    'HAS_CONCENTRATION' => $has_concentration


);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));


