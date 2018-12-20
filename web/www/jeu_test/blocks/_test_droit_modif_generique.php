<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 20/12/18
 * Time: 17:33
 */
$erreur = 0;
$req = "select * from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
    $droit['modif_perso'] = 'N';
    $droit['modif_gmon'] = 'N';
    $droit['controle'] = 'N';
    $droit['objet'] = 'N';
} else
{
    $db->next_record();
    $droit['modif_perso'] = $db->f("dcompt_modif_perso");
    $droit['modif_gmon'] = $db->f("dcompt_modif_gmon");
    $droit['controle'] = $db->f("dcompt_controle");
    $droit['objet'] = $db->f("dcompt_objet");
}
if ($droit[$droit_modif] != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}