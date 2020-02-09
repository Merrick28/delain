<?php
//
// en premier on liste les magasins et leur gérant éventuel
//
// on commence par les magasins avec gérants
echo "<p class=\"titre\">Magasins avec gérants</p>";
$req    = "select lieu_cod,lieu_marge,lieu_prelev,lieu_compte,pos_x,pos_y,etage_libelle,perso_nom 
                from lieu,lieu_position,positions,etage,perso,magasin_gerant 
                where lieu_cod = lpos_lieu_cod 
                and lieu_tlieu_cod in (" . $type_lieu . ") 
                and lpos_pos_cod = pos_cod 
                and pos_etage = etage_numero 
                and mger_lieu_cod = lieu_cod 
                and mger_perso_cod = perso_cod 
                order by pos_etage desc ";
$stmt   = $pdo->query($req);
$allmag = $stmt->fetchAll();
if (count($allmag) == 0)
{
    echo "<p>Aucun magasin n'est en gérance.";
} else
{
    echo "<table cellspacing=\"2\" cellpadding=\"2\">";
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>Nom magasin</td>";
    echo "<td class=\"soustitre2\"><p>Gérant</td>";
    echo "<td class=\"soustitre2\"><p>Compte</td>";
    echo "<td></td>";
    echo "</tr>";

    foreach ($allmag as $result)
    {
        echo "<tr>";
        echo "<td id=\"cell" . $result['lieu_cod'] . "\" class=\"soustitre2\"><p>" . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['etage_libelle'] . "</td>";
        echo "<td class=\"soustitre2\"><p><strong>" . $result['perso_nom'] . "</strong></td>";
        echo "<td class=\"soustitre2\"><p>" . $result['lieu_compte'] . " brouzoufs</td>";
        echo "<td><p><a class='change_class_on_hover' data-class-dest='cell" . $result['lieu_cod'] . "' data-class-onhover='navon'  href=\"admin_echoppe_stats.php?methode=stats&lieu=" . $result['lieu_cod'] . "\">Voir les stats !</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}
// on fait les magasins sans gérance
echo "<p class=\"titre\">Magasins hors gérance</p>";
$req    = "select lieu_cod,pos_x,pos_y,etage_libelle 
                from lieu,lieu_position,positions,etage 
                where lieu_cod = lpos_lieu_cod 
                and lieu_tlieu_cod in (" . $type_lieu . ") 
                and lpos_pos_cod = pos_cod
                and pos_etage = etage_numero
                and not exists 
                (select 1 from magasin_gerant where mger_lieu_cod = lieu_cod) 
                order by pos_etage desc ";
$stmt   = $pdo->query($req);
$allmag = $stmt->fetchAll();
if (count($allmag) == 0)
{
    echo "<p>Aucun magasin n'est en gérance.";
} else
{
    echo "<table cellspacing=\"2\" cellpadding=\"2\">";
    foreach ($allmag as $result)
    {
        echo "<tr>";
        echo "<td id=\"cell" . $result['lieu_cod'] . "\" class=\"soustitre2\"><p>" . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['etage_libelle'] . "</td>";
        echo "<td><p><a class='change_class_on_hover' data-class-dest='cell" . $result['lieu_cod'] . "' data-class-onhover='navon' href=\"admin_echoppe_stats.php?methode=stats&lieu=" . $result['lieu_cod'] . "\">Voir les stats !</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}