<?php
include "blocks/_header_page_jeu.php";
ob_start();
$req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle ";
$req = $req . "from lieu,lieu_position,positions,etage,magasin_gerant ";
$req = $req . "where lieu_cod = lpos_lieu_cod ";
$req = $req . "and lieu_tlieu_cod in (11,14,21) ";
$req = $req . "and lpos_pos_cod = pos_cod ";
$req = $req . "and pos_etage = etage_numero ";
$req = $req . "and mger_lieu_cod = lieu_cod ";
$req = $req . "and mger_perso_cod = $perso_cod ";
$req = $req . "order by pos_etage desc ";
$db->query($req);
if ($db->nf() == 0) {
    echo "<p>Vous n'avez aucun magasin en gérance !";
} else {
    echo "<p>Magasins en gérance :<br>";
    while ($db->next_record()) {
        echo "<p><a href=\"gere_echoppe3.php?mag=" . $db->f("lieu_cod") . "\">" . $db->f("lieu_nom") . "</a> (" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . ")<br>";
    }

}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
