<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 19/12/18
 * Time: 18:37
 */

include G_CHE . "/jeu_test/blocks/_tests_appels_page_externe.php";

$param = new parametres();
// on regarde si le joueur est bien sur une banque
include G_CHE . "/jeu_test/blocks/_test_lieu.php";
include G_CHE . "/jeu_test/blocks/_test_passage_medaillon.php";

if ($erreur == 0)
{
    $tab_lieu = $db->get_lieu($perso_cod);
    $tab_lieu = $db->get_lieu($perso_cod);
    $nom_lieu = $tab_lieu['nom'];
    $desc_lieu = $tab_lieu['description'];
    echo "<p><strong>$nom_lieu</strong><br>$desc_lieu ";
    echo "<p><a href=\"action.php?methode=passage\">Prendre cet escalier ! (" . $param->getparm(13) . " PA)</a></p>";
}

