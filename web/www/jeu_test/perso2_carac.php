<?php
include "../includes/constantes.php";
require_once G_CHE . "includes/fonctions.php";


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
$stmt         = $pdo->prepare($req_bm_carac);
$stmt         = $pdo->execute(array(":perso" => $perso->perso_cod), $stmt);
$bm_caracs    = array();
while ($result = $stmt->fetch())
{
    $bm_caracs[$result['corig_type_carac']] = [
        "base"  => $result['valeur_orig'],
        "texte" => " : base " . $result['valeur_orig'] . ($result['corig_valeur'] > 0 ? " + bonus " : " - malus ")
                   . abs($result['corig_valeur'])
    ];
}

/* Calcul des carac modifié par l'équipement, les bonues, amélioration */
$req_carac = "select f_armure_perso(:perso) as armure, distance_vue(:perso) as vue, f_regen_perso(:perso) as regen, f_temps_tour_perso(:perso) as temps_tour, valeur_bonus(:perso, 'DEG') as bonus_degats";
$stmt     = $pdo->prepare($req_carac);
$stmt     = $pdo->execute(array(":perso" => $perso->perso_cod), $stmt);
$carac_final  = $stmt->fetch();


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

// ne sert plus calculé par => f_regen_perso(:perso)
//if ($perso->perso_niveau_vampire == 0)
//{
//    $bonus_pv_reg = min(25, floor($perso->perso_des_regen * $perso->perso_pv_max / 100));
//}

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

$dlt_tab_amelioration = [ 720	=> 0 , 690	=> 1 , 660	=> 2 , 635	=> 3 , 610	=> 4 , 585	=> 5 , 565	=> 6 , 545	=> 7 , 525	=> 8 , 510	=> 9 , 495	=> 10, 480	=> 11, 470	=> 12, 460	=> 13, 450	=> 14, 445	=> 15, 440	=> 16, 435	=> 17, 430	=> 18, 425	=> 19, 420	=> 20, 415	=> 21, 410	=> 22, 405	=> 23, 400	=> 24, 395	=> 25, 390	=> 26, 385	=> 27, 380	=> 28, 375	=> 29, 370	=> 30, 365	=> 31, 360	=> 32];
$dlt_amelioration = isset( $dlt_tab_amelioration[$perso->perso_temps_tour]) ? $dlt_tab_amelioration[$perso->perso_temps_tour] : 0 ;

$template     = $twig->load('_perso2_carac.twig');
$options_twig = array(

    'PERSO'            => $perso,
    'PHP_SELF'         => $_SERVER['PHP_SELF'],
    'CARAC'            => $carac_final,
    'RACE'             => $race,
    'NIVEAU_BLESSURES' => $niveau_blessures,
    'BM_CARACS'        => $bm_caracs,
    'NB_DES'           => $nb_des,
    'VAL_DES'          => $val_des,
    'BONUS'            => $bonus,
//    'BONUS_PV_REG'     => $bonus_pv_reg,
    'BONUS_TEMPS_PA'   => $bonus_temps_pa,
    'IS_FORGEAMAGE'    => $is_forgeamage,
    'DIEU_PERSO'       => $dieu_perso,
    'DLT_AMELIORATION' => $dlt_amelioration
);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));