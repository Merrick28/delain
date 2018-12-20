<?php
include "blocks/_header_page_jeu.php";
// on regarde si le joueur est bien sur un centre d’entrainement
$type_lieu = 6;
$nom_lieu = 'un centre d\'entraînement';

include "blocks/_test_lieu.php";

if ($erreur == 0)
{
    $multiplicateur_prix = 4;

    include "blocks/_test_amel_competence.php";

    if ($type_comp != 2 && $type_comp != 6 && $type_comp != 7 && $type_comp != 8 && $type_comp != 19)
    {
        $contenu_page .= "<p>Cette compétence ne peut pas être améliorée dans ce centre.";
        $erreur = 1;
    }

    if ($erreur == 0)
    {
        include "blocks/_amel_comp.php";
    }
    $contenu_page .= '<br><br><p><a href="lieu.php">Retour au centre d’entrainement</a>';
}

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

include "blocks/_footer_page_jeu.php";