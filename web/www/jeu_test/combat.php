<?php
include "blocks/_header_page_jeu.php";
$param = new parametres();
ob_start();

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
    ?>

    <form name="attaque" method="post" action="action.php">
    <input type="hidden" name="methode" value="attaque2">
    <?php

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

    echo "Arme utilisée : <strong>" . $obj_nom . "</strong>. ";
    echo "Choisissez votre méthode de combat : <select name=\"type_at\">";
    echo $resultat_inc_competence_combat;

    echo "</select> - mode " . $mode . ' <a href="perso2.php?m=3">(changer ?)</a>';

    echo "<br>";
    echo "Souhaitez-vous <a href='perso2.php?m=5'>défier un aventurier ?</a>";
    echo "<br>";

    if ($param->getparm(56) == 1)
    {
        include "include_tab_attaque3.php";
    } else
    {
        include "include_tab_attaque2.php";
    }
    ?>
    <input type="submit" class="test centrer" value="Attaquer !">
    <?php
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

