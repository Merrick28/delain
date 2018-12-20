<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");
if (!isset($db))
    include_once "verif_connexion.php";

$type_lieu = 37;
$nom_lieu = 'une sortie de donjon';

include "blocks/_test_lieu.php";


if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    $tab_lieu = $db->get_lieu($perso_cod);
    $nom_lieu = $tab_lieu['nom'];
    $desc_lieu = $tab_lieu['description'];
    echo("<p><strong>$nom_lieu</strong> - $desc_lieu ");
    echo("<p>Vous voyez la sortie de ce donjon.");
    echo("<p><a href=\"action.php?methode=sortir_donjon\">Prendre la sortie ! (4PA)</a></p>");
}
