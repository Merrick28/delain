<?php

include "blocks/_header_page_jeu.php";
ob_start();
define("APPEL", 1);

$perso = new perso;
$perso->charge($perso_cod);
if ($perso->is_lieu())
{
    $tab_lieu = $perso->get_lieu();
    $url      = $tab_lieu['lieu']->lieu_url;
    $evo      = $tab_lieu['lieu']->lieu_levo_niveau;
    $lieu_cod = $tab_lieu['lieu']->lieu_cod;
    $position = $tab_lieu['lieu_position']->lpos_pos_cod;

    if (empty($url))
    {
        $nom         = $tab_lieu['lieu']->lieu_nom . ' (' . $tab_lieu['lieu_type']->tlieu_libelle . ')';
        $description = $tab_lieu['lieu']->lieu_description;

        echo "<p><strong>$nom</strong></p><p>$description</p>";
    } else
    {
        require_once $url;
    }


    include_once 'lieu.factions.php';
} else
{
    echo "<p>Anomalie, vous n’êtes pas sur un lieu !</p>";
}
if ($contenu_page == '')
    $contenu_page = ob_get_contents();

ob_end_clean();
include "blocks/_footer_page_jeu.php";
