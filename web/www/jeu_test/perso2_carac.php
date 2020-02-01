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

$race = new race;
$race->charge($perso->perso_race_cod);

$pv               = $perso->perso_pv;
$pv_max           = $perso->perso_pv_max;
$niveau_blessures = niveau_blessures($pv,$pv_max);


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
if (count($pc88) + count($pc102) + count($pc103) != 0)
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