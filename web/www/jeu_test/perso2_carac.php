<?php
$perso = new perso;
if (!$perso->charge($perso_cod))
{
    die('Erreur sur le chargement de perso');
}
$pdo = new bddpdo();


// Cacul des bonus de caracs == pour affichage dans la feuille
$req_bm_carac = "select corig_type_carac, 
       avg(tbonus_degressivite)::integer as limite, 
       avg(corig_carac_valeur_orig)::integer as valeur_orig, 
       sum(corig_valeur)::integer as corig_valeur
	from carac_orig 
	join bonus_type on tbonus_libc=corig_type_carac 
    where corig_perso_cod = :perso
	group by corig_type_carac ";
$pdo->prepare($req_bm_carac);
$pdo->execute(array(":perso" => $perso->perso_cod), $stmt);


while ($result = $stmt->fetch())
{
    $bm_caracs[$result['corig_type_carac']] = [
        "base"  => $$result['valeur_orig'],
        "texte" => " : base " . $result['valeur_orig'] . ($result['corig_valeur'] > 0 ? " + bonus " : " - malus ")
                   . abs($result['corig_valeur'])
    ];
}

$arme_distance = $perso->has_arme_distance();
$race          = new race;
$race->charge($perso->perso_race_cod);


$requete = "select perso_description, perso_redispatch, perso_type_perso, perso_niveau_vampire, perso_vampirisme, perso_nom, 
		perso_capa_repar, to_char(perso_dcreat,'DD/MM/YYYY hh24:mi:ss') as date_cre, race_nom, perso_sex, perso_pv, perso_pv_max, 
		to_char(perso_dlt,'dd/mm/yyyy hh24:mi:ss') as dlt, perso_pa, perso_nb_esquive, perso_niveau, floor(perso_px) as perso_px,
		limite_niveau(perso_cod) as limite_niveau, perso_amelioration_armure, perso_amelioration_degats, perso_amelioration_regen,
		perso_amelioration_vue, perso_des_regen, perso_valeur_regen, perso_vue, calcul_temps(perso_temps_tour) as temps_tour,
		allonge_temps(perso_cod) as allonge_temps_blessures, calcul_temps((perso_temps_tour*perso_pa)/24) as bonus_pa_temps_tour,
		perso_utl_pa_rest, get_poids(perso_cod) as poids, perso_enc_max, allonge_temps_poids(perso_cod) as allonge_temps_poids,
		xp_dispo(perso_cod) as xp_dispo, perso_amel_deg_dex, perso_for, perso_dex, perso_int, perso_con, 
		floor(perso_kharma) as perso_kharma, get_karma(perso_kharma) as karma, perso_nb_receptacle, perso_renommee,
		get_renommee(perso_renommee) as renommee, get_renommee_magie(perso_renommee_magie) as renommee_magie,perso_renommee_magie, 
		perso_energie, perso_renommee_artisanat, get_renommee_artisanat(perso_renommee_artisanat) as renommee_artisanat,
		f_armure_perso_physique(perso_cod) as obj_armure
	from perso
	inner join race  on race_cod = perso_race_cod
	where perso_cod = $perso_cod";

$db->query($requete);
$db->next_record();
$sexe   = $perso->perso_sex;
$is_fam = $perso->is_fam();
$redist = $perso->perso_redispatch;

$pv               = $db->f("perso_pv");
$pv_max           = $db->f("perso_pv_max");
$niveau_blessures = '';
if ($pv / $pv_max < 0.75)
{
    $niveau_blessures = ' - ' . $tab_blessures[0];
}
if ($pv / $pv_max < 0.5)
{
    $niveau_blessures = ' - ' . $tab_blessures[1];
}
if ($pv / $pv_max < 0.25)
{
    $niveau_blessures = ' - ' . $tab_blessures[2];
}
if ($pv / $pv_max < 0.15)
{
    $niveau_blessures = ' - ' . $tab_blessures[3];
}
$contenu_page .= '<td class="soustitre2">Nombre d’esquives ce tour</td>
<td>' . $db->f('perso_nb_esquive') . '</td>
</tr>
<tr><td class="soustitre2">Renommée </td>
<td>' . round($db->f('perso_renommee'), 2) . ' (' . $db->f('renommee') . ')</td>
<td class="soustitre2">Renommée magique </td>
<td nowrap>' . round($db->f("perso_renommee_magie"), 2) . ' (' . $db->f("renommee_magie") . ')</td>
</tr>
<tr>
<td class="soustitre2">Renommée artisanale </td>
<td>' . round($db->f('perso_renommee_artisanat'), 2) . ' (' . $db->f('renommee_artisanat') . ')</td>
<td class="soustitre2">Karma </td>
<td>' . $db->f('perso_kharma') . ' (' . $db->f('karma') . ')</td>
</tr>
<tr>
<td height="3" colspan="4"><hr /></td>
</tr>


<tr>
<td class="soustitre2">Force</td>
<td>' . $db->f('perso_for') . (isset($bm_caracs["FOR"]) ? $bm_caracs["FOR"]["texte"] . " (" . ($db->f('perso_for') - $bm_caracs["FOR"]["base"]) . ")" : "") . '</td>
<td class="soustitre2">Intelligence</td>
<td>' . $db->f('perso_int') . (isset($bm_caracs["INT"]) ? $bm_caracs["INT"]["texte"] . " (" . ($db->f('perso_int') - $bm_caracs["INT"]["base"]) . ")" : "") . '</td>
</tr>
<tr>
<td class="soustitre2">Dextérité</td>
<td>' . $db->f('perso_dex') . (isset($bm_caracs["DEX"]) ? $bm_caracs["DEX"]["texte"] . " (" . ($db->f('perso_dex') - $bm_caracs["DEX"]["base"]) . ")" : "") . '</td>
<td class="soustitre2">Constitution</td>
<td>' . $db->f('perso_con') . (isset($bm_caracs["CON"]) ? $bm_caracs["CON"]["texte"] . " (" . ($db->f('perso_con') - $bm_caracs["CON"]["base"]) . ")" : "") . '</td>
</tr>';
// affichage des bonus
$contenu_page .= '
<tr>
<td height="3" colspan="4"><hr /></td>
</tr>
<tr>
<td class="soustitre2">Dégâts <em>(+ amélioration)</em></td>';
$req_arme     = "select max(obj_des_degats) as obj_des_degats,
		max(obj_val_des_degats) as obj_val_des_degats,
		sum(obj_bonus_degats) as obj_bonus_degats,
		count(*) as nombre
	from perso_objets
	inner join objets on obj_cod = perobj_obj_cod
	where perobj_perso_cod = $perso_cod 
		and perobj_equipe = 'O'";
$db_arme      = new base_delain;
$db_arme->query($req_arme);
$db_arme->next_record();
$nb_arme = $db_arme->f('nombre');
if ($nb_arme == 0)
{
    $nb_des  = 1;
    $val_des = 3;
    $bonus   = 0;
} else
{
    $nb_des  = $db_arme->f("obj_des_degats");
    $val_des = $db_arme->f("obj_val_des_degats");
    $bonus   = $db_arme->f("obj_bonus_degats");
}
$contenu_page .= '<td>' . $nb_des . 'D' . $val_des . '+' . $bonus;
if (!$arme_distance)
{
    $contenu_page .= '<em>(+' . $db->f('perso_amelioration_degats') . ')</em>';
} else
{
    $contenu_page .= '<em>(+' . $db->f('perso_amel_deg_dex') . ')</em>';
}
$contenu_page .= '</td>';

if ($db->f("perso_niveau_vampire") == 0)
{
    $regAmelTxt   = ($db->f('perso_amelioration_regen') != 0 ?
        '<em>(+ amélioration)</em>' :
        '');
    $regAmelVal   = ($db->f('perso_amelioration_regen') != 0 ?
        '<em>(+ ' . $db->f('perso_amelioration_regen') . ')</em>' :
        '');
    $bonus_pv_reg = min(25, floor($db->f('perso_des_regen') * $db->f("perso_pv_max") / 100));
    $contenu_page .= '<td class="soustitre2">Régénération ' . $regAmelTxt . ' </td>
	<td>' . $db->f('perso_des_regen') . 'D' . $db->f('perso_valeur_regen') . '+' . $bonus_pv_reg . $regAmelVal . '</td></tr>';
} else
{
    $vamp         = $db->f("perso_vampirisme") * 10;
    $contenu_page .= '<td class="soustitre2">Vampirisme  </td>
	<td>' . $vamp . '</td></tr>';
}

$armure = $db->f("obj_armure");

$contenu_page .= '<tr>
<td class="soustitre2">Armure <em>(+ amélioration)</em></td>
<td>' . $armure . '<em>(+ ' . $db->f('perso_amelioration_armure') . ')</em></td>
<td class="soustitre2">Vue  <em>(+ amélioration)</em></td>
<td>' . $db->f('perso_vue') . '<em>(+ ' . $db->f('perso_amelioration_vue') . ')</em></td>
</tr>

<tr>
<td class="soustitre2">Temps normal de tour</td>';
$tab_normal   = explode(";", $db->f('temps_tour'));
$contenu_page .= '<td>' . $tab_normal[0] . ' h ' . $tab_normal[1] . ' m</td>
<td class="soustitre2">Malus temps lié aux blessures (à cet instant)</td>';
$tab_allonge  = explode(";", $db->f('allonge_temps_blessures'));
$contenu_page .= '<td>' . $tab_allonge[0] . ' h ' . $tab_allonge[1] . ' m</td>
</tr>

<tr>
<td class="soustitre2">Encombrement </td>
<td>' . $db->f('poids') . '/' . $db->f('perso_enc_max') . '</td>
<td class="soustitre2">Malus lié au poids transporté</td>';
$tab_allonge  = explode(";", $db->f('allonge_temps_poids'));
$contenu_page .= '<td>' . $tab_allonge[0] . ' h ' . $tab_allonge[1] . ' m</td>
</tr>
<tr>
<td></td><td></td><td class="soustitre2">Bonus liés aux PA restant <em>(à cet instant précis !)</em></td>';
if ($db->f("perso_utl_pa_rest") == 1)
{
    $tab_diminue = explode(";", $db->f('bonus_pa_temps_tour'));
} else
{
    $tab_diminue[0] = 0;
    $tab_diminue[1] = 0;
}

$contenu_page .= '<td>' . $tab_diminue[0] . ' h ' . $tab_diminue[1] . ' m</td>
</tr>
<tr>
<td height="3" colspan="4"><hr /></td>
</tr>

<tr>
<td class="soustitre2" colspan="2">Capacité de réparation </td>
<td colspan="2">' . $db->f('perso_capa_repar') . '</td>
</tr>
<tr>
<td class="soustitre2" colspan="2">Nombre de réceptacles </td>
<td colspan="2">' . $db->f('perso_nb_receptacle') . '</td>
</tr>';

$req_forgeamage = 'select count(*) as nombre from perso_competences
where pcomp_pcomp_cod in (88, 102, 103) and pcomp_perso_cod=' . $perso_cod;
$db_forgeamage  = new base_delain;
$db_forgeamage->query($req_forgeamage);
$db_forgeamage->next_record();

if ($db_forgeamage->f('nombre') > 0)
{
    $contenu_page .= '<tr><td class="soustitre2" colspan="2">Énergie</td>
		<td colspan="2">' . $db->f('perso_energie') . '</td></tr>';
}

if ($is_fam)
{
    $req_fam_divin = "select perso_gmon_cod from perso where perso_cod=$perso_cod";
    $db_fam_divin  = new base_delain;
    $db_fam_divin->query($req_fam_divin);
    $db_fam_divin->next_record();
    if ($db_fam_divin->f('perso_gmon_cod') == 441)
    {
        $req_fam_divin = "select dper_points from dieu_perso where dper_perso_cod = $perso_cod";
        $db_fam_divin->query($req_fam_divin);
        $db_fam_divin->next_record();
        $contenu_page .= '<tr><td class="soustitre2" colspan="2">Énergie divine</td>
			<td colspan="2">' . $db_fam_divin->f('dper_points') . '</td></tr>';
    }
}
$contenu_page .= '</table>';

$template     = $twig->load('_perso2_carac.twig');
$options_twig = array(

    'PERSO'            => $perso,
    'PHP_SELF'         => $PHP_SELF,
    'RACE'             => $race,
    'NIVEAU_BLESSURES' => $niveau_blessures,
    'BM_CARACS'        => $bm_caracs

);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));