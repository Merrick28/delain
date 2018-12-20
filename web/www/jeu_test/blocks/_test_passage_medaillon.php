<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 19/12/18
 * Time: 18:39
 */
if ($db->compte_objet($perso_cod,86) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
    $erreur = 1;
}
if ($db->compte_objet($perso_cod,87) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
    $erreur = 1;
}
if ($db->compte_objet($perso_cod,88) != 0)
{
    echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
    $erreur = 1;
}