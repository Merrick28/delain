<?php
$verif_connexion::verif_appel();
echo "<p class=\"titre\">Vendre du matériel à l'administration</p>";
echo "<form name=\"echoppe\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
echo "<input type=\"hidden\" name=\"methode\" value=\"vente_adm2\">";
echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
$req  = "select gobj_nom,gobj_cod,gobj_valeur,tobj_libelle,count(obj_cod) as qte ";
$req  = $req . "from objets,objet_generique,stock_magasin,type_objet ";
$req  = $req . "where mstock_lieu_cod = $mag ";
$req  = $req . "and mstock_obj_cod = obj_cod ";
$req  = $req . "and obj_gobj_cod = gobj_cod ";
$req  = $req . "and gobj_tobj_cod = tobj_cod ";
$req  = $req . "group by gobj_nom,gobj_cod,gobj_valeur,tobj_libelle ";
$req  = $req . "order by tobj_libelle,gobj_nom ";
$stmt = $pdo->query($req);
echo "<div class='centrer'><table>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Quantité</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Prix de vente</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Qte à vendre ?</strong></td>";
echo "</tr>";
while ($result = $stmt->fetch())
{
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>" . $result['gobj_nom'] . "</td>";
    echo "<td><p>" . $result['tobj_libelle'] . "</td>";
    echo "<td class=\"soustitre2\"><p>" . $result['qte'] . "</td>";
    echo "<td><p>" . $result['gobj_valeur'] . "</td>";
    echo "<td><input type=\"text\" name=\"obj[" . $result['gobj_cod'] . "]\" value=\"0\"></td>";
    echo "</tr>";
}
echo "<tr>";
echo "<td colspan=\"5\"><input type=\"submit\" class=\"test centrer\" value=\"Vendre !\"></td>";
echo "</tr>";
echo "</table></div>";
echo "</form>";