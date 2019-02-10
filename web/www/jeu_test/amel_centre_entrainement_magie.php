<?php
include "blocks/_header_page_jeu.php";
// on regarde si le joueur est bien sur un centre d’entrainement magique
$type_lieu = 13;
$nom_lieu = 'un centre d\'entraînement';

include "blocks/_test_lieu.php";

if ($erreur == 0)
{
    $multiplicateur_prix = 15;

    include "blocks/_test_amel_competence.php";

    if ($type_comp != 5 || $comp_cod == 27)
    {
        $contenu_page .= "<p>Cette compétence ne peut pas être améliorée dans ce centre.";
        $erreur = 1;
    }

    if ($erreur == 0)
    {
        include "blocks/_amel_comp.php";
    }
    $contenu_page .= '<br><br><p><a href="lieu.php">Retour au centre d’entrainement magique</a>';
}

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

include "blocks/_footer_page_jeu.php";
