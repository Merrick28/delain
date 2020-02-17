<?php
include "blocks/_header_page_jeu.php";
ob_start();


$type_lieu = 3;
$nom_lieu = 'un escalier';
define('APPEL', 1);
include "blocks/_test_lieu.php";

if ($erreur == 0) {
    $req = "update lieu ";
    $req = $req . "set lieu_url = 'escalier_d.php', lieu_description = 'Vous voyez un escalier vous permettant de descendre au niveau -4' ";
    $req = $req . "where lieu_cod in (48,49,50) ";
    $stmt = $pdo->query($req);

    $req2 = "delete from perso_objets where perobj_obj_cod = 636 ";
    $stmt = $pdo->query($req2);
    echo("<p>Vous entendez un bruit qui annonce que les accès au niveau -4 sont ouverts");
    echo("<p><a href=\"escalier_d.php\">Retour !</A>");
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
