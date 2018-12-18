<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:23
 */


$erreur = 0;
$req = "select perso_admin_echoppe from perso where perso_cod = $perso_cod ";
$db->query($req);
if ($db->nf() == 0)
{
    echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
else
{
    $db->next_record();
}
if ($db->f("perso_admin_echoppe") != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if (!isset($methode))
{
    $methode = "entree";
}