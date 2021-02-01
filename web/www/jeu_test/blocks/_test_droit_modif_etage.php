<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 20/12/18
 * Time: 17:24
 */
$verif_connexion::verif_appel();
$req = "select dcompt_modif_carte from compt_droit where dcompt_compt_cod = $compt_cod ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0) {
    $droit['carte'] = 'N';
} else {
    $result = $stmt->fetch();
    $droit['carte'] = $result['dcompt_modif_carte'];
}
if ($droit['carte'] != 'O') {
    die("<p>Erreur ! Vous n'avez pas accès à cette page !");
}