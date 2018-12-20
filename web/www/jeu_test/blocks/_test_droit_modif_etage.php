<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 20/12/18
 * Time: 17:24
 */
$req = "select dcompt_modif_carte from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0) {
    $droit['carte'] = 'N';
} else {
    $db->next_record();
    $droit['carte'] = $db->f("dcompt_modif_carte");
}
if ($droit['carte'] != 'O') {
    die("<p>Erreur ! Vous n'avez pas accès à cette page !");
}