<?php
$template = $twig->load('merrick.twig');
$options_twig = array(
    'PERCENT_FINANCES'  => $percent_finances,
    'PUB'               => $pub,
);
echo $template->render(array_merge($options_twig_defaut,$options_twig));
