<?php
include "blocks/_header_page_jeu.php";
ob_start();

// on regarde si le joueur est bien sur une banque
$type_lieu = 18;
$nom_lieu = 'une banque';

include "blocks/_test_lieu.php";

$perso = new perso;
$perso = $verif_connexion->perso;

if ($erreur == 0)
{
    $tab_lieu_cod = $perso->get_lieu();
    $lieu_cod = $tab_lieu_cod['lieu']->lieu_cod;
    $req = "select lieu_dieu_cod from lieu where lieu_cod = $lieu_cod ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $index = $result['lieu_dieu_cod'];
    $desc[1] = "Vous voyez un batiment tout en rondeur, avec des domes et des minarets. Le Jade et L'Obsidienne sont omniprésent donnant au batiment des couleurs vertes et noires. ";
    $desc[2] = "Vous voyez une construction aux murs épais, machicoulis, avec des meurtrières, une Tour de garde, le tout protégé par un pont levis";
    $desc[3] = "Vous voyez une sorte de batiment plat en matérieux naturels avec de nombreux coins, avec des silos sur les cotés.";
    $desc[4] = "Vous voyez une contruction tout en bois. Des rodins style cabane de bucherons. ";
    $desc[5] = "Vous voyez un grand batiment qui ressemble à de multiples hangars collés les uns aux autres.";
    echo "<p>", $desc[$index];
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";