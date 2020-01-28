<?php

$perso = new perso;
$perso->charge($perso_cod);
if (isset($_REQUEST['ch_util']))
{
	$perso->perso_utl_pa_rest = $_REQUEST['ch_util'];
	$perso->stocke();
}

if ($perso->perso_utl_pa_rest == 1)
{
	$util = $perso->perso_nom . " <strong>utilise</strong> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 0;
}
else
{
	$util = $perso->perso_nom . " <strong>n’utilise pas</strong> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 1;
}
$contenu_page .= '<div class="titre">Utilisation des PA restants</div>
' . $util . '<a href="' . $PHP_SELF . '?m=2&ch_util=' . $ch_util . '">(changer ?)</a></br>';
//
/* Concentration */
//
$contenu_page .= '<div class="titre">Concentration</div>';
$concentration = new concentrations();
$has_concentration = false;

if ($concentration->getByPerso($perso->perso_cod))
{
    $has_concentration = true;
    $contenu_page .= 'Vous êtes concentré(e) pendant ' . $concentration->concentration_nb_tours . ' tours. ';
}
else
{
    $contenu_page .= 'Vous n’avez effectué aucune concentration. ';
}
$contenu_page .= '<br /><a href="valide_concentration.php">Se concentrer ! (4 PA)</a>';
if ($has_concentration != 0)
{
	$contenu_page .= '<br /><em>Attention !! Les concentrations ne se cumulent pas. Si vous vous concentrez de nouveau, la concentration précédente sera annulée !</em>';
}
//
/* BONUS PERMANENTS */
//

$contenu_page .= '<div class="titre">Bonus permanents</div>';
$req_bonus = "select bonus_degats_melee($perso_cod) as melee,bonus_arme_distance($perso_cod) as distance";
$db->query($req_bonus);
$db->next_record();
$contenu_page .= 'Bonus aux dégâts en corps-à-corps : <strong>' . $db->f("melee") . ' dégât(s)</strong><br>';

// Loop=0 pour bonus equipement  Loop=1 bonus temporaire
for ($loop=0; $loop<2; $loop++) {


    $contenu_page .= '<div class="titre">'.($loop==0 ? "Bonus d'équipement" : "Bonus temporaires").'</div>
<table ><tr valign="top" ><td style="padding:15px;">';

    $req_bonus = "select tbonus_libc, tonbus_libelle, case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end as bonus_nb_tours, bonus_mode, sum(bonus_valeur) as bonus_valeur 
	from bonus
	inner join bonus_type on tbonus_libc = bonus_tbonus_libc
	where bonus_perso_cod = $perso_cod 
		and
			(tbonus_gentil_positif = 't' and bonus_valeur > 0
			or tbonus_gentil_positif = 'f' and bonus_valeur < 0)
		and bonus_mode ".($loop==0 ? "=" : "!=")." 'E'
    group by tbonus_libc, tonbus_libelle, case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end, bonus_mode
	order by tbonus_libc";

    $req_malus = "select tbonus_libc, tonbus_libelle, case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end as bonus_nb_tours, bonus_mode, sum(bonus_valeur) as bonus_valeur 
	from bonus
	inner join bonus_type on tbonus_libc = bonus_tbonus_libc
	where bonus_perso_cod = $perso_cod 
		and
			(tbonus_gentil_positif = 't' and bonus_valeur < 0
			or tbonus_gentil_positif = 'f' and bonus_valeur > 0)
        and bonus_mode ".($loop==0 ? "=" : "!=")." 'E'
    group by tbonus_libc, tonbus_libelle, case when bonus_mode='E' then 'Equipement' else bonus_nb_tours::text end, bonus_mode			
	order by tbonus_libc";

    $req_bm_carac = "select corig_type_carac,
        sum(corig_valeur) as bonus_carac,
		to_char(corig_dfin,'dd/mm/yyyy hh24:mi:ss') as corig_dfin, 
		coalesce(corig_nb_tours, 0) as corig_nb_tours,
		case when corig_mode='E' then 'Equipement' else corig_nb_tours::text end as corig_mode
	from carac_orig
	inner join perso on perso_cod = corig_perso_cod
	where perso_cod = $perso_cod
	  and corig_mode ".($loop==0 ? "=" : "!=")." 'E'
	group by corig_type_carac, to_char(corig_dfin,'dd/mm/yyyy hh24:mi:ss'), coalesce(corig_nb_tours, 0), case when corig_mode='E' then 'Equipement' else corig_nb_tours::text end
	order by corig_type_carac";
    $bonus_carac = array();
    $malus_carac = array();
    $db->query($req_bm_carac);
    $record = 0;
    while ($db->next_record()) {
        $carac = $db->f('corig_type_carac');
        $bm = $db->f('bonus_carac');
        $corig_mode = $db->f('corig_mode');
        if ($db->f('corig_mode') == 'E') {
            $duree = "Equipement";
        } else {
            $duree = ($db->f('corig_nb_tours') == 0) ? $db->f('corig_dfin') : $db->f('corig_nb_tours') . ' tour(s)';
        }
        if ($db->f('bonus_carac') > 0) {
            $bonus_carac[$record] = array();
            $bonus_carac[$record][0] = $bm;
            $bonus_carac[$record][1] = $duree;
            $bonus_carac[$record][2] = $carac;
            $bonus_carac[$record][3] = $corig_mode;
        } else {
            $malus_carac[$record] = array();
            $malus_carac[$record][0] = $bm;
            $malus_carac[$record][1] = $duree;
            $malus_carac[$record][2] = $carac;
            $malus_carac[$record][3] = $corig_mode;
        }
        $record++;
    }


    $db->query($req_bonus);
    if ($db->nf() + sizeof($bonus_carac) == 0) {
        $contenu_page .= '<p>Vous n’avez aucun bonus '.($loop==0 ? "d'équipement " : "").'en ce moment.</p>';
    } else {
        $contenu_page .= '<table><tr>
	<td class="soustitre2"><strong>Bonus</strong></td>
	<td class="soustitre2"><strong>Valeur</strong></td>
	<td class="soustitre2"><strong>Échéance</strong></td>
	<td class=""></td>
	</tr>';

        while ($db->next_record()) {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $db->f('tbonus_libc') . ".png")) {
                $img = '<img src="/../images/interface/bonus/' . $db->f('tbonus_libc') . '.png">';
            } else {
                $img = '<img src="/../images/interface/bonus/MALUS.png">';
            }
            $contenu_page .= '<tr><td class="soustitre2"><strong>' . $db->f('tonbus_libelle') . ($db->f('bonus_mode') == "C" ? " (cumulatif)" : "") . '</strong></td>';
            $signe = ($db->f("bonus_valeur") >= 0) ? '+' : '';
            $contenu_page .= '<td><div style="text-align:center;">' . $signe . $db->f("bonus_valeur") . '</div></td>
		<td><div style="text-align:center;">' . ($db->f('bonus_mode') == "E" ? " Equipement" : $db->f("bonus_nb_tours") . " tour(s)") . '</div></td>
		<td style="text-align:center">' . $img . '</td></tr>';
        }
        foreach ($bonus_carac as $bonus) {
            $valeur = $bonus[0];
            $duree = $bonus[1];
            $carac = $bonus[2];
            $corig_mode = $bonus[3];
            $signe = ($valeur >= 0) ? '+' : '';
            $lib_carac = '';
            switch ($carac) {
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
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png")) {
                $img = '<img src="/../images/interface/bonus/' . $carac . '.png">';
            } else {
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

    $db->query($req_malus);
    if ($db->nf() + sizeof($malus_carac) == 0) {
        $contenu_page .= '<p>Vous n’avez aucun malus '.($loop==0 ? "d'équipement " : "").'en ce moment.</p>';
    } else {
        $contenu_page .= '<table><tr>
	<td class="soustitre2"><strong>Malus</strong></td>
	<td class="soustitre2"><strong>Valeur</strong></td>
	<td class="soustitre2"><strong>Échéance</strong></td>
	<td class=""></td>
	</tr>';

        while ($db->next_record()) {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $db->f('tbonus_libc') . ".png")) {
                $img = '<img src="/../images/interface/bonus/' . $db->f('tbonus_libc') . '.png">';
            } else {
                $img = '<img src="/../images/interface/bonus/MALUS.png">';
            }

            $contenu_page .= '<tr><td class="soustitre2"><strong>' . $db->f('tonbus_libelle') . ($db->f('bonus_mode') == "C" ? " (cumulatif)" : "") . '</strong></td>';
            $signe = ($db->f("bonus_valeur") >= 0) ? '+' : '';
            $contenu_page .= '<td><div style="text-align:center;">' . $signe . $db->f("bonus_valeur") . '</div></td>
		<td><div style="text-align:center;">' . ($db->f('bonus_mode') == "E" ? " Equipement" : $db->f("bonus_nb_tours") . " tour(s)") . '</div></td>
		<td style="text-align:center">' . $img . '</td></tr>';
        }
        foreach ($malus_carac as $malus) {
            $valeur = $malus[0];
            $duree = $malus[1];
            $carac = $malus[2];
            $signe = ($valeur >= 0) ? '+' : '';
            $lib_carac = '';
            switch ($carac) {
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
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png")) {
                $img = '<img src="/../images/interface/bonus/' . $carac . '.png">';
            } else {
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


