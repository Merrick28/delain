<?php
$template = $twig->load('formu_cree_compte.twig');

echo $template->render(array_merge($options_twig_defaut,$options_twig));
?>