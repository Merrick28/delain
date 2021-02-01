<?php // on définit une variable pour savoir que c'est cette page qui appelle les "sous pages"
DEFINE('APPEL_VUE', 1);
include "blocks/_header_page_jeu.php";


if (!isset($t_frdr) || $t_frdr === '')
    $t_frdr = 0;

include('vue_gauche.php');


include('fr_dr.php');


include('tableau_vue3.php');



$template     = $twig->load('vue.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PHP_SELF'     => $_SERVER['PHP_SELF'],
    'VUE_GAUCHE'   => $vue_gauche,
    'VUE_DROITE'   => $vue_droite,
    'VUE_BAS'      => $vue_bas,
    'VUE_RESULTAT' => (isset($resultat_dep) ? $resultat_dep : '')

);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));
