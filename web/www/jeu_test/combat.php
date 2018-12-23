<?php
include "blocks/_header_page_jeu.php";
$param = new parametres();
//ob_start();

$erreur = 0;
if ($is_intangible)
{
    echo "Vous ne pouvez pas attaquer en étant impalpable !";
    $erreur = 1;
}
if ($is_refuge)
{
    echo "Vous ne pouvez pas attaquer sur un refuge !";
    $erreur = 1;
}
if ($erreur == 0)
{
    $arme_dist = $perso->has_arme_distance();
    //$arme_dist = $db->arme_distance($perso_cod);
    $mc = new mode_combat();
    $mc->charge($perso->perso_mcom_cod);
    $mode = $mc->mcom_nom;


    // Arme équipée
    if (!$obj = $perso->get_arme_equipee())
    {
        $obj_nom = 'aucune';
    } else
    {
        $obj_nom = $obj->obj_nom;
    }


    // Méthode de combat
    include('inc_competence_combat.php');



    $template     = $twig->load('combat_debut.twig');
    $options_twig = array(
        'ARME_UTILISEE' => $obj_nom,
        'COMP_COMBAT'   => $resultat_inc_competence_combat,
        'MODE'          => $mode
    );
    $page =  $template->render(array_merge($var_twig_defaut, $options_twig));

    ob_start();
    include "include_tab_attaque3.php"; // ) mettre en twig un de ces jours
    $page  .= ob_get_contents();
    ob_end_clean();

    $page .= '<input type="submit" class="test centrer" value="Attaquer !">';

}

$template     = $twig->load('combat.twig');
$options_twig = array(
    'CONTENU_PAGE' => $page,
);
echo $template->render(array_merge($var_twig_defaut, $options_twig));



//include "blocks/_footer_page_jeu.php";

