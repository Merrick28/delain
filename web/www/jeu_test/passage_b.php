<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = new perso;
$perso     = $verif_connexion->perso;
$tab_lieu  = $perso->get_lieu();
echo "<p><strong>" . $tab_lieu['lieu']->lieu_nom . "</strong> - " . $tab_lieu['lieu']->lieu_description;
echo "<p><em>" . $tab_lieu['description'];
