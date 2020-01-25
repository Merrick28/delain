<?php
//if(!isset($db))
include_once "verif_connexion.php";
// on regarde si le joueur est bien sur un point de passage

$type_lieu = 38;
$nom_lieu  = 'un point de passage';

include "blocks/_test_lieu.php";

if ($erreur == 0)
{

    $tab_lieu  = $db->get_lieu($perso_cod);
    $nom_lieu  = $tab_lieu['nom'];
    $desc_lieu = $tab_lieu['description'];
    echo("<p><strong>$nom_lieu</strong> - $desc_lieu ");

    // Recherche d'une inscription dans les registres pour retour rapide en arene
    $req = "select preg_date_inscription from perso_registre where preg_perso_cod=$perso_cod ";
    $db->query($req);
    if ($db->next_record())
    {
        $date_inscription = $db->f("preg_date_inscription");
        if ($date_inscription != '')
        {
            echo "<br>Vous vous êtes déjà inscrit(e) dans nos registres à la date du " . date("d/m/Y à H:i:s", strtotime($date_inscription)) . " <br><br>";
        }
    }


    echo("<p>Vous voyez un vieux registre poussiéreux.");
    echo("<p><a href=\"action.php?methode=enreg_pos_donjon\">Inscrire votre nom sur le registre?</a></p>");
}