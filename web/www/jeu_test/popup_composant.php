<div id="liste_objets" class="liste_objets">
    $verif_connexion::verif_appel();
<?php /* Les includes */
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$obj_req   = 22;
require "blocks/_popup_quete_composant.php";