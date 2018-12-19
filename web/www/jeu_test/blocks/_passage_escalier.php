<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 19/12/18
 * Time: 18:37
 */

if(!defined("APPEL"))
    die("Erreur d'appel de page !");
if(!isset($db))
    include "verif_connexion.php";
$param = new parametres();
// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
    echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
    $erreur = 1;
}
if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['type_lieu'] != $type_lieu)
    {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
    }
}
include "_test_passage_medaillon.php";

if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    $tab_lieu = $db->get_lieu($perso_cod);
    $nom_lieu = $tab_lieu['nom'];
    $desc_lieu = $tab_lieu['description'];
    echo("<p><strong>$nom_lieu</strong><br>$desc_lieu ");
    echo("<p><a href=\"action.php?methode=passage\">Prendre cet escalier ! (" . $param->getparm(13) . " PA)</a></p>");
}

