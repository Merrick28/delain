<?php
// perso2.php
include "blocks/_header_page_jeu.php";
//
// initialisation tableau
//
$mess[0] = 'Caractéristiques';
$mess[1] = 'Compétences';
$mess[2] = 'Bonus / malus';
$mess[3] = 'Combat';
$mess[4] = 'Description';
$mess[5] = 'Quêtes et trophées';
$mess[6] = 'Divers';
$nb      = count($mess);
$size    = round(100 / $nb);

$perso = $verif_connexion->perso;

//
// Si pas de parametres passés
//

if (!isset($_REQUEST['m']))
{
    $m = 0;

    $bonus = new bonus();
    if ($bonus->getBy_bonus_perso_cod($perso_cod) !== false)
    {
        $m = 2;
    }

    if ($perso->is_locked())
    {
        $m = 3;
    }

} else
{
    $m = $_REQUEST['m'];
}


if ($m == 0)  // Caractéristiques
{
    include "perso2_carac.php";
} else if ($m == 1)  // caracs
{
    include "perso2_comp.php";
} else if ($m == 2)     // compétences
{
    include "perso2_bonus.php";
} else if ($m == 3)   // bonus malus
{
    include "perso2_combat.php";
} else if ($m == 4)                    // combat
{
    $visu = $perso_cod;
    include "perso2_description.php";
} else if ($m == 5) // Chasse et quêtes
{
    include "perso3_divers.php";
} else if ($m == 6)     // Divers
{
    include "perso2_divers.php";
}

$template     = $twig->load('perso2.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PHP_SELF'     => $_SERVER['PHP_SELF'],
    'MESS'         => $mess,
    'SIZE'         => $size,
    'M'            => $m,
    'CONTENU_PAGE' => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));
