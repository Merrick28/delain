<?php
$template = $twig->load('merrick.twig');
$options_twig = array(
    'PUB'               => $pub,
);
echo $template->render(array_merge($options_twig_defaut,$options_twig));
