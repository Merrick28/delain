<?php
// visu_evt_perso
include "blocks/_header_page_jeu.php";
require_once G_CHE . "includes/fonctions.php";


if (!isset($_REQUEST['visu']))
{
    $visu = '';
} else
{
    $visu = $_REQUEST['visu'];
}
$memeperso = false;
if ($visu == $perso_cod)
{
    $memeperso = true;
}

if (filter_var($visu, FILTER_VALIDATE_INT) === false)
{
    echo "Anomalie sur numéro perso !";
    exit();
}

$visu_perso = new perso();
$visu_perso->charge($visu);

/*****************************/
/* GESTION DE LA DESCRIPTION */
/*****************************/
if (!isset($_REQUEST['met']))
{
    $met = 'vide';
} else
{
    $met = $_REQUEST['met'];
}

if ($met == 'aff')
{
    $compte->compt_vue_desc = 1;
    $compte->stocke();
}

if ($met == 'masq')
{
    $compte->compt_vue_desc = 0;
    $compte->stocke();
}

if ($compte->compt_vue_desc == 1)
{
    include "perso2_description.php";
}
/****************************************/
/* CONTENU                              */
/****************************************/

if (!isset($_REQUEST['pevt_start']))
{
    $pevt_start = 0;
} else
{
    $pevt_start = $_REQUEST['pevt_start'];
}
if ($pevt_start < 0)
{
    $pevt_start = 0;
}

$race = new race;
$race->charge($visu_perso->perso_race_cod);

$levt        = new ligne_evt();
$withvisible = false;
if ($memeperso)
{
    $withvisible = true;
}
if ($compte->is_admin())
{
    $withvisible = true;
}
$tab_evt = $levt->getByPerso($visu_perso->perso_cod, $pevt_start, 20, $withvisible);


$moins20      = 0;
if ($pevt_start != 0)
{
    $moins20      = $pevt_start - 20;
}
$plus20       = $pevt_start + 20;



$template     = $twig->load('_visu_evt_perso.twig');
$options_twig = array(

    'VISU_PERSO' => $visu_perso,
    'COMPTE'     => $compte,
    'PHP_SELF'   => $PHP_SELF,
    'RACE'       => $race,
    'MEMEPERSO'  => $memeperso,
    'EVTS'       => $tab_evt,
    'MOINS20'    => $moins20,
    'PLUS20'     => $plus20,
    'PEVT_START' => $pevt_start


);
$contenu_page = $template->render(array_merge($options_twig_defaut, $options_twig));


include "blocks/_footer_page_jeu.php";
