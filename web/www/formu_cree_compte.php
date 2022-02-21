<?php
$template = $twig->load('formu_cree_compte.twig');
$options_twig = array();
echo $template->render(array_merge($options_twig_defaut,$options_twig));
?>