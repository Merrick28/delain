<?php


$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
?>

<p>Ce bâtiment est en cours de construction.</p>

