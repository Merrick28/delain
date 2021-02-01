<div id="liste_objets" class="liste_objets">

<?php /* Les includes */
define('APPEL', 1);
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$perso     = $verif_connexion->perso;
$compt_cod = $verif_connexion->compt_cod;
$obj_req   = 11;
require "blocks/_popup_quete_composant.php";