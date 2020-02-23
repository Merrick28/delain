<?php
$verif_connexion::verif_appel();
$lieu_cod = $tab_lieu['lieu_cod'];
echo "<p class=\"titre\">Réparation d'équipement</p>";
$req    = "select perso_po from perso where perso_cod = $perso_cod ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
echo "<p>Vous avez actuellement <strong>" . $result['perso_po'] . "</strong> brouzoufs. ";
$req  = "select obj_cod,obj_etat,gobj_nom as nom,f_prix_objet($lieu_cod,obj_cod) as valeur,tobj_libelle ";
$req  = $req . "from objet_generique,objets,perso_objets,type_objet ";
$req  = $req . "where perobj_perso_cod = $perso_cod ";
$req  = $req . "and perobj_obj_cod = obj_cod ";
$req  = $req . "and perobj_identifie = 'O' ";
$req  = $req . "and obj_gobj_cod = gobj_cod ";
$req  = $req . "and gobj_deposable = 'O' ";
$req  = $req . "and gobj_tobj_cod = tobj_cod ";
$req  = $req . "and tobj_cod in (1,2,4) ";
$req  = $req . "and obj_etat < 100 ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
    echo "<p>Vous n'avez aucun équipement à  réparer pour l'instant.";
} else
{
    echo "<form name=\"vente\" action=\"action.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_repare\">";
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
        $req   = "SELECT obon_cod,obon_libelle,obon_prix FROM bonus_objets,objets ";
        $req   = $req . "where obj_cod = " . $result['obj_cod'] . " and obj_obon_cod = obon_cod ";
        $stmt2 = $pdo->query($req);
        if ($stmt2->rowCount() != 0)
        {
            $result2  = $stmt2->fetch();
            $bonus    = " (" . $result2['obon_libelle'] . ")";
            $prix_bon = $result2['obon_prix'];
            $url_bon  = "&bon=" . $result2['obon_cod'];
        } else
        {
            $bonus    = "";
            $prix_bon = 0;
            $url_bon  = "";
        }
        $etat = $result['obj_etat'];
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p><strong>" . $result['nom'] . "</strong></td>";
        echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
        $prix = ($result['valeur'] + $prix_bon) * 0.2 / $modif;
        $prix = $prix * (100 - $etat);
        $prix = $prix / 100;
        echo "<td class=\"soustitre2\"><p>" . floor($prix) . " brouzoufs</td>";
        echo "<td><p><input type=\"checkbox\" name=\"obj[", $result['obj_cod'], "]\"></td>";

    }
    echo "</table></div>";
    echo "<input type=\"submit\" class=\"test centrer\" value=\"Réparer les objets sélectionnées !\">";
    echo "</form>";
}
