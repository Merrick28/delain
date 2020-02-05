<?php
include "../includes/constantes.php";
require_once 'fonctions.php';

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
$stmt = $pdo->prepare($req_bm_carac);
$stmt = $pdo->execute(array(":perso" => $perso->perso_cod), $stmt);

while ($result = $stmt->fetch())
{
    $bm_caracs[$result['corig_type_carac']] = [
        "base"  => $result['valeur_orig'],
        "texte" => " : base " . $result['valeur_orig'] . ($result['corig_valeur'] > 0 ? " + bonus " : " - malus ")
                   . abs($result['corig_valeur'])
    ];
}

$race = new race;
$race->charge($perso->perso_race_cod);

$pv               = $perso->perso_pv;
$pv_max           = $perso->perso_pv_max;
$niveau_blessures = niveau_blessures($pv,$pv_max);

$db->query($requete);
$db->next_record();
$sexe   = $perso->perso_sex;
$is_fam = $perso->is_fam();
$redist = $perso->perso_redispatch;
// Commenté par Reivax -- cause des problèmes avec le passage à l’UTF-8
// Est-ce une protection anti-scripts ? Dans ce cas, je ne comprends pas pourquoi les Ç (chr(128)) seraient impactés... Dans le doute, je place htmlspecialchars.
/*$desc = str_replace(chr(128),";",$db->f("perso_description"));
$desc = str_replace(chr(127),";",$desc);*/


/*if (($redist == 'P') && !$is_fam)
	$contenu_page .= '<p style="text-align:center;"><strong><a href="action.php?methode=redist">Redistribuer les améliorations</a></strong><br>
	ATTENTION ! ACTION IMMEDIATE ET DEFINITIVE !<br>(entre autres, les sorts mis dans les réceptacles sont perdus)';*/

$contenu_page .= '
<table width="100%" cellspacing="2">

<tr>
<td class="soustitre2">Niveau </td>
<td>' . $db->f("perso_niveau") . '<em>(prochain niveau à ' . $db->f("limite_niveau") . ' PX)</em></td>
<td class="soustitre2">Date limite de tour <a href="decalage_dlt.php">(Décaler sa DLT)</a></td>
<td>' . $db->f("dlt") . '</td></tr>

<tr><td class="soustitre2">Expérience</td>
<td>' . $db->f('perso_px');

$contenu_page     .= '
</td>
<td class="soustitre2">Points d’action</td>
<td>' . $db->f('perso_pa') . '</td>
</tr>

<tr>
<td class="soustitre2">Points de vie</td>';
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
$contenu_page .= '<td>' . $db->f('perso_pv') . '/' . $db->f('perso_pv_max') . $niveau_blessures . '</td>';
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

$req_arme = "select max(obj_des_degats) as obj_des_degats,
		max(obj_val_des_degats) as obj_val_des_degats,
		sum(obj_bonus_degats) as obj_bonus_degats,
		count(*) as nombre
	from perso_objets
	inner join objets on obj_cod = perobj_obj_cod
	where perobj_perso_cod = :perso
		and perobj_equipe = 'O'";
$stmt     = $pdo->prepare($req_arme);
$stmt     = $pdo->execute(array(":perso" => $perso->perso_cod), $stmt);
$result   = $stmt->fetch();
if ($result['nombre'] == 0)
{
    $nb_des  = 1;
    $val_des = 3;
    $bonus   = 0;
} else
{
    $nb_des  = $result['obj_des_degats'];
    $val_des = $result['obj_val_des_degats'];
    $bonus   = $result['obj_bonus_degats'];
}


if ($perso->perso_niveau_vampire == 0)
{
    $bonus_pv_reg = min(25, floor($perso->perso_des_regen * $perso->perso_pv_max / 100));
}

if ($perso->perso_utl_pa_rest == 1)
{
    $bonus_temps_pa = ($perso->perso_temps_tour * $perso->perso_pa) / 24;
}

$perso_competence = new perso_competences();

$pc88             = $perso_competence->getByPersoComp($perso->perso_cod, 88);
$pc102            = $perso_competence->getByPersoComp($perso->perso_cod, 102);
$pc103            = $perso_competence->getByPersoComp($perso->perso_cod, 103);


$is_forgeamage = false;
if (!$pc88 || !$pc102 || ! $pc102)
{
    $is_forgeamage = true;
}

$dieu_perso = new dieu_perso();
if ($perso->is_fam())
{
    if ($perso->perso_gmon_cod == 441)
    {
        $dieu_perso->getByPersoCod($perso->perso_cod);
    }
}

$template     = $twig->load('_perso2_carac.twig');
$options_twig = array(

    'PERSO'            => $perso,
    'PHP_SELF'         => $PHP_SELF,
    'RACE'             => $race,
    'NIVEAU_BLESSURES' => $niveau_blessures,
    'BM_CARACS'        => $bm_caracs,
    'NB_DES'           => $nb_des,
    'VAL_DES'          => $val_des,
    'BONUS'            => $bonus,
    'BONUS_PV_REG'     => $bonus_pv_reg,
    'BONUS_TEMPS_PA'   => $bonus_temps_pa,
    'IS_FORGEAMAGE'    => $is_forgeamage,
    'DIEU_PERSO'       => $dieu_perso
);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));