<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:26
 * @include G_CHE . "/jeu_test/verif_connexion.php"
 */
//include_once G_CHE . "/jeu_test/verif_connexion.php";

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

// on va maintenant charger toutes les variables liées au menu
include(G_CHE . '/jeu_test/variables_menu.php');
include(G_CHE . '/includes/constantes.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
