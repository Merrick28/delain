<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 19/12/18
 * Time: 18:37
 */
$verif_connexion::verif_appel();

$param = new parametres();
// on regarde si le joueur est bien sur une banque
include G_CHE . "/jeu_test/blocks/_test_lieu.php";
include G_CHE . "/jeu_test/blocks/_test_passage_medaillon.php";

if(!isset($desc_passage))
{
    $desc_passage = 'cet escalier';
}

if ($erreur == 0)
{

    if ($type_lieu == 16)
    {
        // Cas d'un grand escalier standard
        $tab_lieu  = $perso->get_lieu();
        $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
        $desc_lieu = $tab_lieu['lieu']->lieu_description;
        echo("<p><strong>$nom_lieu</strong><br>$desc_lieu ");
        echo("<p><a href=\"valide_grand_escalier_a.php\">Prendre cet escalier ! (" . $param->getparm(43) . " PA)</a></p>");
    }
    else
    {
        // Cas d'un escalier standard
        $tab_lieu  = $perso->get_lieu();
        $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
        $desc_lieu = $tab_lieu['lieu']->lieu_description;
        echo "<p><strong>$nom_lieu</strong><br>$desc_lieu ";
        echo "<p><a href=\"action.php?methode=passage\">Prendre " . $desc_passage . " ! (" . $param->getparm(13) . " PA)</a></p>";
    }
}

