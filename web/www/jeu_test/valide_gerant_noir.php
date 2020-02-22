<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;
$req = "select perso_admin_echoppe_noir from perso where perso_cod = $perso_cod ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
if ($result['perso_admin_echoppe_noir'] != 'O') {
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
require "blocks/_valide_gerant.php";