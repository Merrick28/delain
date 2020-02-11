<?php

if (!defined("APPEL"))
    die("Erreur d'appel de page !");

include_once "verif_connexion.php";

$perso = new perso;
$perso->charge($perso_cod);

$tab_lieu = $perso->get_lieu();
echo "<p>Vous voyez une pancarte qui indique : ";
echo "<p><strong><em>" . $tab_lieu['lieu']->lieu_description . "</em></strong>";
