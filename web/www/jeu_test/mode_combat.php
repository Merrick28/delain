<?php

$perso = new perso;
if (!$perso->charge($perso_cod))
{
    die ("Erreur d’appel de la page");
}
$mode_combat = new mode_combat();
$all_mode    = $mode_combat->getAll();


if (isset($_REQUEST['methode']) && $_REQUEST['methode'] == 'mode_combat')
{
    $perso->change_mode_combat($_REQUEST['mode']);
    $perso->charge($perso->perso_cod);
}
$mode_combat->charge($perso->perso_mcom_cod);


$perso_dchange_mcom = new DateTime($perso->perso_dchange_mcom);
date_add($perso_dchange_mcom, date_interval_create_from_date_string('1 days'));

$perso_dcreat = new DateTime($perso->perso_dcreat);
date_add($perso_dcreat, date_interval_create_from_date_string('1 days'));

$now = date_create();

$is_depasse = false;
if ($perso_dchange_mcom > $now)
{
    $is_depasse = true;
}

$is_date_creation = false;
if ($perso_dcreat > $now)
{
    $is_date_creation = true;
}

$template        = $twig->load('_mode_combat.twig');
$options_twig    = array(

    'PERSO'              => $perso,
    'PHP_SELF'           => $PHP_SELF,
    'MODE_COMBAT'        => $mode_combat,
    'IS_DEPASSE'         => $is_depasse,
    'IS_DATE_CREATION'   => $is_date_creation,
    'PERSO_DCHANGE_MCOM' => $perso_dchange_mcom,
    'ALL_MODE'           => $all_mode

);
$contenu_include = $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));

