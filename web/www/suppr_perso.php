<?php
include "includes/classes.php";
include "ident.php";

// ces deux lignes sont temporaires
// sans ça, le variable_menu ne fonctionne pas
include_once 'includes/template.inc';
$t = new template;

include(G_CHE . '/jeu_test/variables_menu.php');
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
$compte = new compte();
$compte->charge($compt_cod);


if (count($compte->getPersosActifs()) != 0)
{


    ob_start();
    include "tab_switch.php";
    $detail_perso = ob_get_contents();
    ob_clean();


}

$template     = $twig->load('suppr_perso.twig');
$options_twig = array(
    'COMPTE'       => $compte,
    'NB_PERSOS'    => count($compte->getPersosActifs()),
    'DETAIL_PERSO' => $detail_perso
);


echo $template->render(array_merge($var_twig_defaut, $options_twig));

