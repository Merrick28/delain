<?php

$perso = new perso;
if (!$perso->charge($perso_cod))
{
    die('Erreur sur le chargement de perso');
}

$typecomp    = new type_competences();
$perso_comp  = new perso_competences();
$alltypecomp = $typecomp->getAll();

foreach ($alltypecomp as $key => $val)
{

    $comp_perso                    = $perso_comp->getByPersoTypeComp($perso->perso_cod, $val->typc_cod);
    $alltypecomp[$key]->liste_comp = $comp_perso;
}



$template     = $twig->load('_perso2_comp.twig');
$options_twig = array(

    'PERSO'       => $perso,
    'PHP_SELF'    => $_SERVER['PHP_SELF'],
    'ALLTYPECOMP' => $alltypecomp

);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));

