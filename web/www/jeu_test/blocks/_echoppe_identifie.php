<?php
$verif_connexion::verif_appel();
$lieu_cod = $tab_lieu['lieu_cod'];
echo "<p class=\"titre\">Identification d'équipement</p>";
$req    = "select perso_po from perso where perso_cod = $perso_cod ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
echo "<p>Vous avez actuellement <strong>" . $result['perso_po'] . "</strong> brouzoufs. ";
$req    = "select lieu_marge from lieu where lieu_cod = $lieu_cod ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
$prix   = $result['lieu_marge'] + 100;
$req    = "select obj_cod,gobj_nom_generique,tobj_libelle ";
$req    = $req . "from objet_generique,objets,perso_objets,type_objet ";
$req    = $req . "where perobj_perso_cod = $perso_cod ";
$req    = $req . "and perobj_obj_cod = obj_cod ";
$req    = $req . "and perobj_identifie != 'O' ";
$req    = $req . "and obj_gobj_cod = gobj_cod ";
$req    = $req . "and gobj_tobj_cod = tobj_cod ";
$stmt   = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
    echo "<p>Vous n'avez aucun équipement à faire identifier pour l'instant.";
} else
{
    echo "<form name=\"identifie\" action=\"action.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_identifie\">";
    echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
    echo "<input type=\"hidden\" name=\"objet\">";

    echo "<div class='centrer'><table>";
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
    echo "<td class=\"soustitre2\"><p><strong>Prix</strong></td>";
    echo "<td></td>";
    while ($result = $stmt->fetch())
    {

        echo "<tr>";
        echo "<td class=\"soustitre2\"><p><strong>" . $result['gobj_nom_generique'] . "</strong></td>";
        echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
        echo "<td class=\"soustitre2\"><p>" . $prix . " brouzoufs</td>";
        echo "<td><p><input type=\"checkbox\" name=\"obj[", $result['obj_cod'], "]\"></td>";
    }
    echo "</table></div>";
    echo "<input type=\"submit\" class=\"test centrer\" value=\"Identifier les objets sélectionnées !\">";
    echo "</form>";

}