<?php 
$perso = new perso;
$joueurActifs = $perso->getPersosActifs();

print_r($joueurActifs);

$template = $twig->load('classement_v2.twig');
$options_twig = array(
    'TAB_JOUEUR'  => $joueurActifs
);
echo $template->render(array_merge($options_twig_defaut,$options_twig));

