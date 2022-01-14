<?php

$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

$perso = new perso;
$perso = $verif_connexion->perso;

$desc = $perso->get_lieu();
echo "<p><strong>" . $tab_lieu['lieu']->lieu_nom . "</strong> - " . $tab_lieu['lieu']->lieu_description;
