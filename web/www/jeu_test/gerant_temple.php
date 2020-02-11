<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <script language="javascript" src="javascripts/changestyles.js"></script>
<?php
$erreur = 0;
$req = "select dper_dieu_cod,dper_niveau from dieu_perso where dper_perso_cod = $perso_cod";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0) {
    echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !1";
    $erreur = 1;
} else {
    $result = $stmt->fetch();
}
if ($result['dper_niveau'] < 4) {
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !2";
    $erreur = 1;
}
if ($erreur == 0) {
    $dieu_perso = $result['dper_dieu_cod'];
    //
    // en premier on liste les temples et leur fidèle associé éventuel
    //
    // on commence par les temples avec fidèle
    echo "<p class=\"titre\">Temples avec fidèles</p>";
    $req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle,perso_nom 
								from lieu,lieu_position,positions,etage,perso,temple_fidele
								where lieu_cod = lpos_lieu_cod
								and lieu_tlieu_cod = 17
								and lpos_pos_cod = pos_cod
								and pos_etage = etage_numero
								and tfid_lieu_cod = lieu_cod
								and tfid_perso_cod = perso_cod 
								and lieu_dieu_cod = $dieu_perso
								order by pos_etage desc ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() == 0) {
        echo "<p>Aucun temple n'est administré par des fidèles.";
    } else {
        echo "<table cellspacing=\"2\" cellpadding=\"2\">";
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Emplacement</td>";
        echo "<td class=\"soustitre2\"><p>Nom du temple</td>";
        echo "<td class=\"soustitre2\"><p>Fidèle</td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "</tr>";

        while ($result = $stmt->fetch()) {
            echo "<tr>";
            echo "<td id=\"cell" . $result['lieu_cod'] . "\" class=\"soustitre2\"><p>" . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['etage_libelle'] . "</td>";
            echo "<td class=\"soustitre2\"><p><a href=\"gere_temple3.php?mag=" . $result['lieu_cod'] . "\"><strong>" . $result['lieu_nom'] . "</strong></a></td>";
            echo "<td class=\"soustitre2\"><p><strong>" . $result['perso_nom'] . "</strong></td>";
            echo "<td><p><a onMouseOver=\"changeStyles('cell" . $result['lieu_cod'] . "',1)\" onMouseOut=\"changeStyles('cell" . $result['lieu_cod'] . "',0)\" href=\"modif_fidele.php?methode=modif&lieu=" . $result['lieu_cod'] . "\">Modifier</a></td>";
            echo "<td><p><a onMouseOver=\"changeStyles('cell" . $result['lieu_cod'] . "',1)\" onMouseOut=\"changeStyles('cell" . $result['lieu_cod'] . "',0)\" href=\"modif_fidele.php?methode=supprime&lieu=" . $result['lieu_cod'] . "\">Supprimer</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    // on fait les temples sans gérance
    echo "<p class=\"titre\">Temples sans affectation</p>";
    $req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle
					from lieu,lieu_position,positions,etage
					where lieu_cod = lpos_lieu_cod
					and lieu_dieu_cod = $dieu_perso
					and lieu_tlieu_cod = 17
					and lpos_pos_cod = pos_cod
					and pos_etage = etage_numero
					and not exists
					(select 1 from temple_fidele where tfid_lieu_cod = lieu_cod)
					order by pos_etage desc ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() == 0) {
        echo "<p>Aucun temple n'est administré par des fidèles.";
    } else {
        echo "<table cellspacing=\"2\" cellpadding=\"2\">";
        while ($result = $stmt->fetch()) {
            echo "<tr>";
            echo "<td id=\"cell" . $result['lieu_cod'] . "\" class=\"soustitre2\"><p><a href=\"gere_temple3.php?mag=" . $result['lieu_cod'] . "\"><strong>" . $result['lieu_nom'] . "</strong></a></td><td class=\"soustitre2\"> " . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['etage_libelle'] . "</td>";
            echo "<td><p><a onMouseOver=\"changeStyles('cell" . $result['lieu_cod'] . "',1)\" onMouseOut=\"changeStyles('cell" . $result['lieu_cod'] . "',0)\" href=\"modif_fidele.php?methode=ajout&lieu=" . $result['lieu_cod'] . "\">Ajouter un fidèle pour gérer ce temple</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
