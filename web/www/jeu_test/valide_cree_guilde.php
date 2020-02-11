<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = 9;
$nom_lieu = 'un bâtiment administratif';

include "blocks/_test_lieu.php";

if ($erreur == 0) {

    $req = "delete from guilde_perso where pguilde_perso_cod = $perso_cod ";
    $stmt = $pdo->query($req);
    $nom_guilde = htmlspecialchars($nom_guilde);
    $desc = htmlspecialchars($desc);
    $desc = nl2br($desc);
    $req_existe = "select guilde_cod from guilde where lower(guilde_nom) = lower('" . pg_escape_string($nom_guilde) . "')";
    $stmt = $pdo->query($req_existe);
    $nb_guilde = $stmt->rowCount();
    if ($nb_guilde != 0) {
        echo("<p>Une guilde porte déjà ce nom <br />");
        echo("<a href=\"cree_guilde.php\">Retour !</a>");
    } else {
        //$desc = str_replace("'","\'",$desc);
        $req_cree = "select cree_guilde($perso_cod,e'" . pg_escape_string($nom_guilde) . "',e'" . pg_escape_string($desc) . "') as cree";
        $stmt = $pdo->query($req_cree);
        $result = $stmt->fetch();
        $resultat = $result['cree'];
        echo("<!-- $resultat -->");
        if ($resultat == '0') {
            echo("<p>Votre guilde a été créée avec succès !<br />");
            echo("<a href=\"admin_guilde.php\">Administrer la guilde !</a>");
        } else {
            echo("<p>Une anomalie est survenue = $resultat");
        }
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";