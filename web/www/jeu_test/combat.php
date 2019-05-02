<?php
include "blocks/_header_page_jeu.php";
include "../includes/constantes.php";
$param = new parametres();


//ob_start();

$erreur = 0;
if ($is_intangible)
{
    //echo "Vous ne pouvez pas attaquer en étant impalpable !";
    $erreur = 1;
}
if ($is_refuge)
{
    //echo "Vous ne pouvez pas attaquer sur un refuge !";
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

    $distance_vue = $perso->distance_vue();
    $portee       = $perso->portee_attaque();
    if ($distance_vue <= $portee)
    {
        $portee = $distance_vue;
    }


    // Méthode de combat
    include('inc_competence_combat.php');


    $lc             = new lock_combat();
    $tab_lock_cible = $lc->getBy_lock_cible($perso_cod);

    if (!$tab_lock_cible)
    {
        $tab_vue = $perso->get_vue_non_lock($compte);
    } else
    {
        $tab_vue = $perso->get_vue_lock($compte);
    }

}

$template     = $twig->load('combat.twig');
$options_twig = array(
    'INCL_TAB_ATTAQUE' => $incl_tab_attaque,
    'ARME_UTILISEE'    => $obj_nom,
    'COMP_COMBAT'      => $resultat_inc_competence_combat,
    'MODE'             => $mode,
    'TAB_VUE'          => $tab_vue,
    'TYPE_PERSO'       => $perso_type_perso,
    'TAB_BLESSURES'    => $tab_blessures,
    'PORTEE'           => $portee,
    'COTERIE'          => $perso->coterie()
);
echo $template->render(array_merge($var_twig_defaut, $options_twig));



