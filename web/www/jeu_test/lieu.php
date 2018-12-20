<?php

include "blocks/_header_page_jeu.php";
ob_start();
define("APPEL", 1);
$is_lieu = $db->is_lieu($perso_cod);
if ($is_lieu) {



    $tab_lieu = $db->get_lieu($perso_cod);
    $url = $tab_lieu['url'];

    $evo = $tab_lieu['evo_niveau'];
    $lieu_cod = $tab_lieu['lieu_cod'];
    $position = $tab_lieu['position'];

    if (empty($url)) {
        $nom = $tab_lieu['nom'] . ' (' . $tab_lieu['libelle'] . ')';
        $description = $tab_lieu['description'];

        echo "<p><strong>$nom</strong></p><p>$description</p>";
    } else {
        require_once $url;
    }


    include_once 'lieu.factions.php';
} else {
    echo "<p>Anomalie, vous n’êtes pas sur un lieu !</p>";
}
if ($contenu_page == '')
    $contenu_page = ob_get_contents();

ob_end_clean();
include "blocks/_footer_page_jeu.php";
