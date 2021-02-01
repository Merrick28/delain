<?php
$verif_connexion::verif_appel();
$modif_possible = 0;

$req    = "select lieu_compte, lieu_marge, lieu_prelev, lieu_alignement ";
$req    = $req . "from lieu ";
$req    = $req . "where lieu_cod = $mag ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
echo "<div class='centrer'><table>";

echo "<tr>";
echo "<td colspan=\"2\" class=\"soustitre2\"><p><strong>Financier :</td>";
echo "</tr>";

echo "<td class=\"soustitre2\"><p>Etat de la caisse</td>";
echo "<td><p>" . $result['lieu_compte'] . " brouzoufs</td>";
echo "</tr>";

echo "<td class=\"soustitre2\"><p>Marge effectuée</td>";
echo "<td><p>" . $result['lieu_marge'] . " %</td>";
echo "</tr>";

echo "<td class=\"soustitre2\"><p>Prélèvements par l'administration</td>";
echo "<td><p>" . $result['lieu_prelev'] . " %</td>";
echo "</tr>";

echo "<td class=\"soustitre2\"><p>Protection</td>";
if ($result['lieu_prelev'] == 15)
{
    $protection = "Votre magasin n'est pas un refuge";
} else
{
    $protection = "Votre magasin est un refuge";
}
echo "<td><p>" . $protection . "</td>";
echo "</tr>";

echo "<td class=\"soustitre2\"><p>Alignement</td>";
echo "<td><p>" . $result['lieu_alignement'] . "</td>";
echo "</tr>";

if ($tab_lieu['lieu']->type_lieu == 11)
{
    $modif_possible = 1;
}
if ($tab_lieu['lieu']->type_lieu == 9)
{
    $modif_possible = 1;
}
$modif_possible = 1;
$php_self       = $_SERVER['PHP_SELF'];
if ($modif_possible == 1)
{
    echo "<tr>";
    echo "<td colspan=\"2\" class=\"soustitre2\"><strong><a class='centrer' href=\"" .
         $php_self . "?mag=$mag&methode=mod\">Modifier ces données</a></strong></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan=\"2\" class=\"soustitre2\"><strong><a class='centrer' href=\"" .
         $php_self . "?mag=$mag&methode=nom\">Changer le nom et la description</a></strong></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan=\"2\" class=\"soustitre2\"><strong><a class='centrer' href=\"" .
         $php_self . "?mag=$mag&methode=vente_adm\">Vendre du matériel à l'administration</a></strong></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan=\"2\" class=\"soustitre2\"><strong><a class='centrer' href=\"" .
         $php_self . "?mag=$mag&methode=achat_adm\">Acheter du matériel à l'administration</a></strong></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan=\"2\" class=\"soustitre2\"><strong><a class='centrer' href=\"" .
         $php_self . "?mag=$mag&methode=fix_prix\">Fixer les tarifs</a></strong></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan=\"2\" class=\"soustitre2\"><strong><a class='centrer' href=\"" .
         $php_self . "?mag=$mag&methode=stats\">Voir les stats</a></strong></td>";
    echo "</tr>";
}


echo "</table></div>";

echo "<div class='centrer'><table>";

echo "<tr>";
echo "<td colspan=\"3\" class=\"soustitre2\"><p><strong>Etat des stocks : </td>";
echo "</tr>";

$req  = "select gobj_nom,tobj_libelle,count(obj_cod) as qte ";
$req  = $req . "from objets,objet_generique,stock_magasin,type_objet ";
$req  = $req . "where mstock_lieu_cod = $mag ";
$req  = $req . "and mstock_obj_cod = obj_cod ";
$req  = $req . "and obj_gobj_cod = gobj_cod ";
$req  = $req . "and gobj_tobj_cod = tobj_cod ";
$req  = $req . "group by gobj_nom,tobj_libelle ";
$req  = $req . "order by tobj_libelle,gobj_nom ";
$stmt = $pdo->query($req);
echo "<tr>";
echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Quantité</strong></td>";
while ($result = $stmt->fetch())
{
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>" . $result['gobj_nom'] . "</td>";
    echo "<td><p>" . $result['tobj_libelle'] . "</td>";
    echo "<td><p>" . $result['qte'] . "</td>";
    echo "</tr>";
}

echo "</table></div>";

