<?php

// par défaut, on n'est pas authentifié
$verif_auth = false;
include G_CHE . "ident.php";

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


require_once CHEMIN . '../includes/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');

$twig     = new Twig_Environment($loader, array());
$template = $twig->loadTemplate('finances.twig');

$options_twig = array(
    'URL'              => G_URL,
    'URL_IMAGES'       => G_IMAGES,
    'HTTPS'            => $type_flux,
    'ISAUTH'           => $verif_auth,
    'PERCENT_FINANCES' => $percent_finances,
    'TABFIN'           => $TableauFinances,
    'MIN_YEAR'         => $minYear,
    'CURRENT_YEAR'     => $currentYear,
    'WORK_MONTH'       => $workMonth,
    'WORK_YEAR'        => $workYear,
    'TOTAL'            => $total,
    'DATE_MAJ'         => $date_maj,
    'COMPTE'           => $compte
);
echo $template->render($options_twig);


