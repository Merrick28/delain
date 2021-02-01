<?php

// par défaut, on n'est pas authentifié
$verif_auth      = false;
$verif_connexion = new verif_connexion();
$verif_connexion->ident();
$verif_auth = $verif_connexion->verif_auth;
$compte     = $verif_connexion->compte;

$finances = new finances;


$minDate  = $finances->getMinDate();
$minYear  = date('Y', strtotime($minDate));
$minMonth = date('m', strtotime($minDate));

$currentYear  = date('Y');
$currentMonth = date('m');

// on regarde si on travaille sur la date du jour
// ou sur une autre date
if (!isset($_REQUEST['change_date']))
{
    $workMonth = $currentMonth;
    $workYear  = $currentYear;
}
else
{
    $workDate  = explode('-', $_REQUEST['change_date']);
    $workMonth = $workDate[1];
    $workYear  = $workDate[0];
}

// on prend les datas existantes s'il y en a
$TableauFinances = $finances->getSyntheseByDate($workMonth, $workYear);
$total           = $finances->getTotalByDate($workMonth, $workYear);
$date_maj        = $finances->getDateUpdate();


$template = $twig->load('finances.twig');

$options_twig = array(
    'TABFIN'           => $TableauFinances,
    'MIN_YEAR'         => $minYear,
    'CURRENT_YEAR'     => $currentYear,
    'WORK_MONTH'       => $workMonth,
    'WORK_YEAR'        => $workYear,
    'TOTAL'            => $total,
    'DATE_MAJ'         => $date_maj,
    'COMPTE'           => $compte
);
echo $template->render(array_merge($options_twig_defaut,$options_twig));


