<?php 
if (isset($ch_util))
{
	$req = "update perso set perso_utl_pa_rest = $ch_util where perso_cod = $perso_cod ";
	$db->query($req);
}


$req = "select perso_nom,perso_utl_pa_rest from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
if ($db->f("perso_utl_pa_rest") == 1)
{
	$util = $db->f("perso_nom") . " <strong>utilise</strong> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 0;
}
else
{
	$util = $db->f("perso_nom") . " <strong>n’utilise pas</strong> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 1;
}
$contenu_page .= '<div class="titre">Utilisation des PA restants</div>
' . $util . '<a href="' . $PHP_SELF . '?m=2&ch_util=' . $ch_util . '">(changer ?)</a></br>';
//
/* Concentration */
//
$contenu_page .= '<div class="titre">Concentration</div>';

$req_concentration = "select concentration_nb_tours from concentrations where concentration_perso_cod = $perso_cod";
$db->query($req_concentration);
$nb_concentration = $db->nf();
if ($nb_concentration == 0)
{
	$contenu_page .= 'Vous n’avez effectué aucune concentration. ';
}
else
{
	$db->next_record();
	$contenu_page .= 'Vous êtes concentré(e) pendant ' . $db->f("concentration_nb_tours") . ' tours. ';
}
$contenu_page .= '<br /><a href="valide_concentration.php">Se concentrer ! (4 PA)</a>';
if ($nb_concentration != 0)
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

$contenu_page .= 'Bonus aux dégâts en corps-à-corps : <strong>' . $db->f("melee") . ' dégât(s)</strong><br>
<div class="titre">Bonus temporaires</div>
<table><tr valign="top"><td style="padding:15px;">';

$req_bonus = "select tbonus_libc, tonbus_libelle, bonus_nb_tours, bonus_mode, sum(bonus_valeur) as bonus_valeur 
	from bonus
	inner join bonus_type on tbonus_libc = bonus_tbonus_libc
	where bonus_perso_cod = $perso_cod 
		and
			(tbonus_gentil_positif = 't' and bonus_valeur > 0
			or tbonus_gentil_positif = 'f' and bonus_valeur < 0)
    group by tbonus_libc, tonbus_libelle, bonus_nb_tours, bonus_mode
	order by tbonus_libc";

$req_malus = "select tbonus_libc, tonbus_libelle, bonus_nb_tours, bonus_mode, sum(bonus_valeur) as bonus_valeur 
	from bonus
	inner join bonus_type on tbonus_libc = bonus_tbonus_libc
	where bonus_perso_cod = $perso_cod 
		and
			(tbonus_gentil_positif = 't' and bonus_valeur < 0
			or tbonus_gentil_positif = 'f' and bonus_valeur > 0)
    group by tbonus_libc, tonbus_libelle, bonus_nb_tours, bonus_mode			
	order by tbonus_libc";

$req_bm_carac = "select corig_type_carac,
        sum(corig_valeur) as bonus_carac,
		to_char(corig_dfin,'dd/mm/yyyy hh24:mi:ss') as corig_dfin, coalesce(corig_nb_tours, 0) as corig_nb_tours
	from carac_orig
	inner join perso on perso_cod = corig_perso_cod
	where perso_cod = $perso_cod
	group by corig_type_carac, to_char(corig_dfin,'dd/mm/yyyy hh24:mi:ss'), coalesce(corig_nb_tours, 0)
	order by corig_type_carac";
$bonus_carac = array();
$malus_carac = array();
$db->query($req_bm_carac);
while ($db->next_record())
{
	$carac = $db->f('corig_type_carac');
	$bm = $db->f('bonus_carac');
	$duree = ($db->f('corig_nb_tours') == 0) ? $db->f('corig_dfin') : $db->f('corig_nb_tours') . ' tour(s)';
	if ($db->f('bonus_carac') > 0)
	{
		$bonus_carac[$carac] = array();
		$bonus_carac[$carac][0] = $bm;
		$bonus_carac[$carac][1] = $duree;
	}
	else
	{
		$malus_carac[$carac] = array();
		$malus_carac[$carac][0] = $bm;
		$malus_carac[$carac][1] = $duree;
	}
}


$db->query($req_bonus);
if ($db->nf() + sizeof($bonus_carac) == 0)
{
	$contenu_page .= '<p>Vous n’avez aucun bonus en ce moment.</p>';
}
else
{
	$contenu_page .= '<table><tr>
	<td class="soustitre2"><strong>Bonus</strong></td>
	<td class="soustitre2"><strong>Valeur</strong></td>
	<td class="soustitre2"><strong>Échéance</strong></td>
	<td class="soustitre2"><strong>Picto.</strong></td>
	</tr>';

	while($db->next_record())
	{
        $img = "" ;
        if (is_file(__DIR__ . "/../images/interface/bonus/".$db->f('tbonus_libc').".png"))
        {
            $img = '<img src="/../images/interface/bonus/'.$db->f('tbonus_libc').'.png">' ;
        }
		$contenu_page .= '<tr><td class="soustitre2"><strong>' . $db->f('tonbus_libelle') .  ($db->f('bonus_mode') == "C" ? " (cumulatif)": "") . '</strong></td>';
		$signe = ($db->f("bonus_valeur") >= 0) ? '+' : '';
		$contenu_page .= '<td><div style="text-align:center;">' . $signe . $db->f("bonus_valeur") . '</div></td>
		<td><div style="text-align:center;">' . ($db->f('bonus_mode') == "E" ? " Equipement" : $db->f("bonus_nb_tours"))  . ' tour(s)</div></td>
		<td style="text-align:center">'.$img.'</td></tr>';
	}
	foreach ($bonus_carac as $carac => $bonus)
	{
		$valeur = $bonus[0];
		$duree = $bonus[1];
		$signe = ($valeur >= 0) ? '+' : '';
		$lib_carac = '';
		switch ($carac)
		{
			case 'FOR': $lib_carac = 'Force'; break;
			case 'INT': $lib_carac = 'Intelligence'; break;
			case 'CON': $lib_carac = 'Constitution'; break;
			case 'DEX': $lib_carac = 'Dextérité'; break;
		}
		$contenu_page .= "<tr><td class='soustitre2'><strong>$lib_carac</strong></td>
			<td><div style='text-align:center;'>$signe" . "$valeur</div></td>
			<td><div style='text-align:center;'>$duree</div></td></tr>";
	}
	$contenu_page .= '</table>';
}

$contenu_page .= '</td><td style="padding:15px;">';

$db->query($req_malus);
if ($db->nf() + sizeof($malus_carac) == 0)
{
	$contenu_page .= '<p>Vous n’avez aucun malus en ce moment.</p>';
}
else
{
	$contenu_page .= '<table><tr>
	<td class="soustitre2"><strong>Malus</strong></td>
	<td class="soustitre2"><strong>Valeur</strong></td>
	<td class="soustitre2"><strong>Échéance</strong></td>
	<td class="soustitre2"><strong>Picto.</strong></td>
	</tr>';

	while($db->next_record())
	{
	    $img = "" ;
        if (is_file(__DIR__ . "/../images/interface/bonus/".$db->f('tbonus_libc').".png"))
        {
            $img = '<img src="/../images/interface/bonus/'.$db->f('tbonus_libc').'.png">' ;
        }

		$contenu_page .= '<tr><td class="soustitre2"><strong>' . $db->f('tonbus_libelle') . ($db->f('bonus_mode') == "C" ? " (cumulatif)": "") . '</strong></td>';
		$signe = ($db->f("bonus_valeur") >= 0) ? '+' : '';
		$contenu_page .= '<td><div style="text-align:center;">' . $signe . $db->f("bonus_valeur") . '</div></td>
		<td><div style="text-align:center;">' . ($db->f('bonus_mode') == "E" ? " Equipement" : $db->f("bonus_nb_tours")) . ' tour(s)</div></td>
		<td style="text-align:center">'.$img.'</td></tr>';
	}
	foreach ($malus_carac as $carac => $malus)
	{
		$valeur = $malus[0];
		$duree = $malus[1];
		$signe = ($valeur >= 0) ? '+' : '';
		$lib_carac = '';
		switch ($carac)
		{
			case 'FOR': $lib_carac = 'Force'; break;
			case 'INT': $lib_carac = 'Intelligence'; break;
			case 'CON': $lib_carac = 'Constitution'; break;
			case 'DEX': $lib_carac = 'Dextérité'; break;
		}
		$contenu_page .= "<tr><td class='soustitre2'><strong>$lib_carac</strong></td>
			<td><div style='text-align:center;'>$signe" . "$valeur</div></td>
			<td><div style='text-align:center;'>$duree</div></td></tr>";
	}
	$contenu_page .= '</table>';
}
$contenu_page .= '</td></tr></table>';

