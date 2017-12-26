<?php
include_once "jeu_test/verif_connexion.php";
if ($compte->compt_cod != 4) {
    die("Accès réservé");
}

$finances = new finances;

// est-ce qu'on a un formulaire qui arrive ?
if (isset($_POST['methode'])) {
    if ($_POST['methode'] == "nouveau") {
        $finances->fin_desc = $_POST['desc'];
        $finances->fin_date = $_POST['date'];
        $finances->fin_montant = $_POST['montant'];
        $finances->stocke(true);
    }
    if ($_POST['methode'] == "existant") {
        $finances->charge($_POST['id']);
        $finances->fin_desc = $_POST['desc'];
        $finances->fin_date = $_POST['date'];
        $finances->fin_montant = $_POST['montant'];
        $finances->stocke();
    }

}

$minDate = $finances->getMinDate();
$minYear = date('Y', strtotime($minDate));
$minMonth = date('m', strtotime($minDate));

$maxDate = $finances->getMaxDate();

$maxYear = date('Y', strtotime($maxDate));
$maxMonth = date('m', strtotime($maxDate));

$currentYear = date('Y');
$currentMonth = date('m');

// on regarde si on travaille sur la date du jour
// ou sur une autre date
if (!isset($_REQUEST['change_date'])) {
    $workMonth = $currentMonth;
    $workYear = $currentYear;
} else {
    $workDate = explode('-', $_REQUEST['change_date']);
    $workMonth = $workDate[1];
    $workYear = $workDate[0];
}

// on prend les datas existantes s'il y en a
$TableauFinances = $finances->getByDate($workMonth, $workYear);


$template = $twig->load('saisie_finances.twig');

$options_twig = array(
    'TABFIN' => $TableauFinances,
    'MIN_YEAR' => $minYear,
    'CURRENT_YEAR' => $currentYear,
    'WORK_MONTH' => $workMonth,
    'WORK_YEAR' => $workYear,
    'MAX_MONTH' => $maxMonth,
    'MAX_YEAR' => $maxYear
);
echo $template->render(array_merge($options_twig_defaut, $options_twig));


