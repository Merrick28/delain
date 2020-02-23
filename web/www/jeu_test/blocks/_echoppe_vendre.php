<?php
$verif_connexion::verif_appel();
$taux_rachat = $param->getparm(47);
$lieu_cod    = $tab_lieu['lieu_cod'];
echo "<HR /><p class=\"titre\">Vente d'équipement</p>";
$req =
    "select obj_cod,obj_etat,obj_nom as nom,f_prix_obj_perso_v($perso_cod,$lieu_cod,obj_cod) as valeur,tobj_libelle ";
$req = $req . "from objet_generique,objets,perso_objets,type_objet ";
$req = $req . "where perobj_perso_cod = $perso_cod ";
$req = $req . "and perobj_obj_cod = obj_cod ";
$req = $req . "and perobj_identifie = 'O' ";
$req = $req . "and perobj_equipe != 'O' ";
$req = $req . "and obj_gobj_cod = gobj_cod ";
$req = $req . "and gobj_deposable = 'O' ";
$req = $req . "and gobj_tobj_cod = tobj_cod ";
if ($generique)
{
    if (TYPE_ECHOPPE == "MAGIE")
    {
        $req = $req . "and tobj_cod in (5, 20, 21, 22, 23, 24) ";
    } else if (TYPE_ECHOPPE == "MARCHE_NOIR")
    {
        $req = $req . "and tobj_cod in (1,2,4,5,17,19,25) ";
    } else
    {
        $req = $req . "and tobj_cod in (1,2,4,17,19,25) ";
    }
}
$req = $req . "union all
								select obj_cod,obj_etat,obj_nom as nom,f_prix_obj_perso_v($perso_cod,$lieu_cod,obj_cod) as valeur,tobj_libelle
								from objet_generique,objets,perso_objets,type_objet
								where perobj_perso_cod = $perso_cod
								and perobj_obj_cod = obj_cod
								and perobj_equipe != 'O'
								and obj_gobj_cod = gobj_cod
								and obj_deposable = 'O'
								and gobj_echoppe_vente = 'O'
								and gobj_tobj_cod = tobj_cod
								and tobj_cod = 11 ";


$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
    echo "<p>Vous n'avez aucun équipement à  vendre pour l'instant.</p>";
} else
{ ?>

    <form name="vente" method="post">
    <input type="hidden" name="methode" value="nv_magasin_vente">
    <input type="hidden" name="affichage" value="resultats">
    <input type="hidden" name="lieu" value="<?php echo $lieu ?>">
    <input type="hidden" name="objet">
    <div class="centrer">
        <table>
            <tr>
                <td class="soustitre2"><p><strong>Nom</strong></td>
                <td class="soustitre2"><p><strong>Type</strong></td>
                <td class="soustitre2"><p><strong>Prix</strong></td>
                <td></td>
    <?php
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
        $prix = $result['valeur'] + $prix_bon;
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p><strong>" . $result['nom'] . "</strong></td>";
        echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
        echo "<td class=\"soustitre2\"><p>" . $result['valeur'] . " brouzoufs</td>";
        echo "<td><p><input type=\"checkbox\" name=\"obj[", $result['obj_cod'], "]\"></td>";

    }
    echo "</table></div>";
    echo "<input type=\"submit\" class=\"test centrer\" value=\"Vendre les objets sélectionnées !\">";
    echo "</form>";
}