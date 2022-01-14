<?php

$faq_type = new faq_type();
$faq      = new faq();

$tab_faq_type = $faq_type->getAll();

foreach($tab_faq_type as $key => $tfaq)
{
    $tfaq->detail = $faq->getBy_faq_tfaq_cod($tfaq->tfaq_cod);
}


$template = $twig->load('faq_v2.twig');
$options_twig = array(
    'FAQ'  => $tab_faq_type
);
echo $template->render(array_merge($options_twig_defaut,$options_twig));