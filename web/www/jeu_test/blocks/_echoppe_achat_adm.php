<?php
$verif_connexion::verif_appel();
echo "<p class=\"titre\">Achat de matériel à l'administration</p>";
$req    = "select lieu_compte from lieu where lieu_cod = $mag ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
echo "<p>Vous diposez de <strong>" . $result['lieu_compte'] . "</strong> pour acheter des objets à l'administration.";
$req = "select gobj_cod,tobj_libelle,gobj_nom,gobj_valeur, ";
$req = $req . "(select count(obj_cod) ";
$req = $req . "from objets,stock_magasin ";
$req = $req . "where obj_gobj_cod = gobj_cod ";
$req = $req . "and mstock_obj_cod = obj_cod ";
$req = $req . "and mstock_lieu_cod = $mag) as stock ";
$req = $req . "from objet_generique,type_objet ";
$req = $req . "where gobj_echoppe_stock = 'O' ";
//$req = $req . "and gobj_deposable = 'O' ";
//$req = $req . "and gobj_visible = 'O' ";
$req = $req . "and gobj_tobj_cod = tobj_cod ";
//$req = $req . "and tobj_cod in (1,2,4) ";
$req  = $req . "order by tobj_cod,gobj_nom ";
$stmt = $pdo->query($req);
echo "<form name=\"echoppe\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
echo "<input type=\"hidden\" name=\"methode\" value=\"achat_adm2\">";
echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
echo "<div class='centrer'><table>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Valeur</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Stock</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Quantité</strong></td>";
while ($result = $stmt->fetch())
{
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>" . $result['gobj_nom'] . "</td>";
    echo "<td><p>" . $result['tobj_libelle'] . "</td>";
    echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $result['gobj_valeur'] . "</td>";
    echo "<td><p style=\"text-align:right;\">" . $result['stock'] . "</td>";
    echo "<td class=\"soustitre2\"><p><input type=\"text\" name=\"obj[" . $result['gobj_cod'] . "]\" value=\"0\"></td>";
    echo "</tr>";
}
echo "<tr>";
echo "<td colspan=\"5\"><input type=\"submit\" value=\"Valider les achats !\" class=\"test centrer\"></td>";
echo "</tr>";

echo "</table></div>";


echo "</form>";
