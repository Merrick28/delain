<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:30
 */
include(G_CHE . '/jeu_test/variables_menu.php');
/*$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");*/



$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));