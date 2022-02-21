<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:30
 */
include(G_CHE . '/jeu_test/variables_menu.php');


$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
