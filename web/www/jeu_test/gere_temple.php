<?php
include "blocks/_header_page_jeu.php";
ob_start();
$req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle
							from lieu,lieu_position,positions,etage,temple_fidele 
							where lieu_cod = lpos_lieu_cod 
							and lieu_tlieu_cod = 17 
							and lpos_pos_cod = pos_cod 
							and pos_etage = etage_numero 
							and tfid_lieu_cod = lieu_cod 
							and tfid_perso_cod = $perso_cod 
							order by pos_etage desc";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0) {
    echo "<p>Vous n'avez aucun temple à votre charge !";
} else {
    echo "<p>Temples à votre charge :<br>";
    while ($result = $stmt->fetch()) {
        echo "<p><a href=\"gere_temple3.php?mag=" . $result['lieu_cod'] . "\">" . $result['lieu_nom'] . "</a> (" . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['etage_libelle'] . ")<br>";
    }

}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
