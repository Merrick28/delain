<?php
include "blocks/_header_page_jeu.php";
ob_start();

// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod)) {
    echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    $erreur = 1;
}
if ($erreur == 0) {
    $tab_lieu = $db->get_lieu($perso_cod);
    if ($tab_lieu['type_lieu'] != 1) {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    }
}
if ($quantite <= 0) {
    $erreur = 1;
    echo("<p>Vous ne pouvez pas déposer une somme inférieure ou égale à 0 !");
}

if ($erreur == 0) {

    echo("<img src=\"../images/banque3.png\"><br />");
    $req_depot = "select depot_banque($perso_cod,$quantite) as depot";
    $db->query($req_depot);
    $db->next_record();
    $tab_depot = pg_fetch_array($res_depot, 0);
    if ($db->f("depot") == 0) {
        echo("<p>Vous venez de déposer <strong>$quantite</strong> brouzoufs sur votre compte en banque.");
    } else {
        printf("<p>Une anomalie est survenue : <strong>%s</strong>", $db->f("depot"));
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";