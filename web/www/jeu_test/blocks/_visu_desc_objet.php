<?php

if ($stmt->rowCount() != 0)
{
    $result      = $stmt->fetch();
    $t_etat      = 0;
    $comp        = $result['gobj_comp_cod'];
    $desc        = $result['gobj_description'];
    $distance    = $result['gobj_distance'];
    $type_objet  = $result['gobj_tobj_cod'];
    $seuil_force = $result['gobj_seuil_force'];
    $seuil_dex   = $result['gobj_seuil_dex'];
    $niveau_min  = $result['gobj_niveau_min'];
    echo "<p class=\"titre\">" . $result['gobj_nom'] . "</p>";
    echo "<div class='centrer'><table>";
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>Type d’objet :</td>";
    echo "<td><p>" . $result['tobj_libelle'];
    if ($result['gobj_deposable'] == 'N')
    {
        echo " <strong>non déposable !</strong>";
    }
    echo "</p></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>Poids :</td>";
    echo "<td><p>" . $result['gobj_poids'] . "</td>";
    echo "</tr>";
    if ($type_objet == 1)
    {
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque normale :</td>";
        echo "<td><p>" . $result['gobj_pa_normal'] . "</td>";
        echo "</tr>";

        if ($result['gobj_distance'] == 'N')
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque foudroyante :</td>";
            echo "<td><p>" . $result['gobj_pa_eclair'] . "</td>";
            echo "</tr>";
        }

        $req = "select obcar_des_degats,obcar_val_des_degats,obcar_bonus_degats ";
        $req = $req . "from objets_caracs,objet_generique ";
        $req = $req . "where gobj_cod = $objet ";
        //$req = $req . "and obj_gobj_cod = gobj_cod ";
        $req    = $req . "and gobj_obcar_cod = obcar_cod ";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Dégats</td>";
        echo "<td><p>" . $result['obcar_des_degats'] . "D" . $result['obcar_val_des_degats'] . "+" . $result['obcar_bonus_degats'] . "</td>";
        echo "</tr>";


        $req    = "select comp_libelle from competences where comp_cod = $comp ";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Compétence utilisée</td>";
        echo "<td><p>" . $result['comp_libelle'] . "</td>";
        echo "</tr>";

        if ($affichage_plus)
        {
            if ($seuil_force > 0)
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Seuil de force</p></td>";
                echo "<td><p>" . $seuil_force . "</p></td>";
                echo "</tr>";
            }
            if ($seuil_dex > 0)
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Seuil de dextérité</p></td>";
                echo "<td><p>" . $seuil_dex . "</p></td>";
                echo "</tr>";
            }
            if ($niveau_min > 0)
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Niveau minimum pour équiper</p></td>";
                echo "<td><p>" . $niveau_min . "</p></td>";
                echo "</tr>";
            }
        }


        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Seuil de force</td>";
        echo "<td><p>" . $seuil_force . "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Seuil de dextérité</td>";
        echo "<td><p>" . $seuil_dex . "</td>";
        echo "</tr>";
    }
    if ($type_objet == 2)
    {
        $req    = "select obcar_armure ";
        $req    = $req . "from objets_caracs,objet_generique,objets ";
        $req    = $req . "where gobj_cod = $objet ";
        $req    = $req . "and obj_gobj_cod = gobj_cod ";
        $req    = $req . "and gobj_obcar_cod = obcar_cod ";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Armure</td>";
        echo "<td><p>" . $result['obcar_armure'] . "</td>";
        echo "</tr>";
    }
    if ($type_objet == 1 and $distance == 'O')
    {
        $req    = "select obcar_chute ";
        $req    = $req . "from objets_caracs,objet_generique,objets ";
        $req    = $req . "where gobj_cod = $objet ";
        $req    = $req . "and obj_gobj_cod = gobj_cod ";
        $req    = $req . "and gobj_obcar_cod = obcar_cod ";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Chute</td>";
        echo "<td><p>" . $result['obcar_chute'] . "</td>";
        echo "</tr>";
    }
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>Description</td>";
    echo "<td><p>" . $desc . "</td>";
    echo "</tr>";

    if (isset($bon))
    {
        $req    = "select obon_libelle from bonus_objets where obon_cod = $bon ";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Bonus</td>";
        echo "<td><p>" . $result['obon_libelle'] . "</td>";
        echo "</tr>";
    }

    echo "</table></div>";
}
