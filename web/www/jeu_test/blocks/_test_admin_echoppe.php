<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:23
 */
$verif_connexion::verif_appel();

$erreur = 0;

if ($perso->perso_admin_echoppe != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
