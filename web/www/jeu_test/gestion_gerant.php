<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <script language="javascript" src="javascripts/changestyles.js"></script>
<?php
$erreur = 0;
$req = "select pguilde_guilde_cod ";
$req = $req . "from guilde_perso,guilde_rang ";
$req = $req . "where pguilde_perso_cod = $perso_cod ";
$req = $req . "and pguilde_valide = 'O' ";
$req = $req . "and pguilde_guilde_cod = rguilde_guilde_cod ";
$req = $req . "and pguilde_rang_cod = rguilde_rang_cod ";
$req = $req . "and rguilde_admin = 'O' ";
$db->query($req);
if ($db->nf() == 0) {
    echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
} else {
    $db->next_record();
}
if ($db->f("pguilde_guilde_cod") != 211) {
    echo "<p>Erreur" . $db->f("pguilde_guilde_cod") . " ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0) {
    //
    // en premier on liste les magasins et leur gérant éventuel
    //
    // on commence par les magasins avec gérants
    echo "<p class=\"titre\">Magasins avec gérants</p>";
    $req = "select lieu_cod,pos_x,pos_y,etage_libelle,perso_nom ";
    $req = $req . "from lieu,lieu_position,positions,etage,perso,magasin_gerant ";
    $req = $req . "where lieu_cod = lpos_lieu_cod ";
    $req = $req . "and lieu_tlieu_cod = 11 ";
    $req = $req . "and lpos_pos_cod = pos_cod ";
    $req = $req . "and pos_etage = etage_numero ";
    $req = $req . "and mger_lieu_cod = lieu_cod ";
    $req = $req . "and mger_perso_cod = perso_cod ";
    $req = $req . "order by pos_etage desc ";
    $db->query($req);
    if ($db->nf() == 0) {
        echo "<p>Aucun magasin n'est en gérance.";
    } else {
        echo "<table cellspacing=\"2\" cellpadding=\"2\">";
        while ($db->next_record()) {
            echo "<tr>";
            echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p>" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
            echo "<td class=\"soustitre2\"><p><strong>" . $db->f("perso_nom") . "</strong></td>";
            echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_gerant.php?methode=modif&lieu=" . $db->f("lieu_cod") . "\">Modifier</a></td>";
            echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_gerant.php?methode=supprime&lieu=" . $db->f("lieu_cod") . "\">Supprimer</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    // on fait les magasins sans gérance
    echo "<p class=\"titre\">Magasins hors gérance</p>";
    $req = "select lieu_cod,pos_x,pos_y,etage_libelle ";
    $req = $req . "from lieu,lieu_position,positions,etage ";
    $req = $req . "where lieu_cod = lpos_lieu_cod ";
    $req = $req . "and lieu_tlieu_cod = 11 ";
    $req = $req . "and lpos_pos_cod = pos_cod ";
    $req = $req . "and pos_etage = etage_numero ";
    $req = $req . "and not exists ";
    $req = $req . "(select 1 from magasin_gerant where mger_lieu_cod = lieu_cod) ";
    $req = $req . "order by pos_etage desc ";
    $db->query($req);
    if ($db->nf() == 0) {
        echo "<p>Aucun magasin n'est hors gérance.";
    } else {
        echo "<table cellspacing=\"2\" cellpadding=\"2\">";
        while ($db->next_record()) {
            echo "<tr>";
            echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p>" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
            echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_gerant.php?methode=ajout&lieu=" . $db->f("lieu_cod") . "\">Ajouter un gérant</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
