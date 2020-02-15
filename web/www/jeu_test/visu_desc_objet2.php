<?php
include "blocks/_header_page_jeu.php";
ob_start();
$objet = $_GET['objet'];
if (!preg_match('/^[0-9]*$/i', $objet))
{
    echo "<p>Anomalie sur numéro objet !";
    exit();
}
$autorise = 0;
$req      = "select perobj_cod from perso_objets,objets
	where perobj_perso_cod = $perso_cod
	and perobj_obj_cod = obj_cod
	and perobj_identifie = 'O' 
	and obj_gobj_cod = $objet ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() != 0)
    $autorise = 1;
// on regarde si l'objet est dans une échoppe sur laquelle on est

$perso = new perso;
$perso->charge($perso_cod);

if ($perso->is_lieu())
{
    $tab_lieu = $perso->get_lieu();
    $lieu_cod = $tab_lieu['lieu']->lieu_cod;
    $req      = "select mstock_obj_cod from stock_magasin,objets
		where mstock_lieu_cod = $lieu_cod
		and mstock_obj_cod = obj_cod
		and obj_gobj_cod = $objet";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
        $autorise = 1;

    $req = "select mgstock_cod from  	stock_magasin_generique
		where mgstock_lieu_cod = $lieu_cod
		and mgstock_gobj_cod = $objet";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
        $autorise = 1;
}
if ($autorise == 1)
{
    $req = "select gobj_nom, gobj_tobj_cod, tobj_libelle, gobj_poids, gobj_pa_normal, gobj_pa_eclair, gobj_distance, gobj_deposable, gobj_comp_cod,
				gobj_description, gobj_seuil_dex, gobj_seuil_force, gobj_niveau_min 
			from objet_generique,type_objet 
			where gobj_cod = $objet 
				and gobj_tobj_cod = tobj_cod 
				and (gobj_visible is null or gobj_visible != 'N') ";
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0)
    {
        $result = $stmt->fetch();
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
        echo "<td class=\"soustitre2\"><p>Type d’objet</p></td>";
        echo "<td><p>" . $result['tobj_libelle'];
        if ($result['gobj_deposable'] == 'N')
        {
            echo " <strong>non déposable !</strong>";
        }
        echo "</p></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Poids</p></td>";
        echo "<td><p>" . $result['gobj_poids'] . "</p></td>";
        echo "</tr>";

        if ($type_objet == 1)
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque normale</p></td>";
            echo "<td><p>" . $result['gobj_pa_normal'] . "</p></td>";
            echo "</tr>";

            if ($distance == 'N')
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque foudroyante</p></td>";
                echo "<td><p>" . $result['gobj_pa_eclair'] . "</p></td>";
                echo "</tr>";
            }

            $req = "select obcar_des_degats,obcar_val_des_degats,obcar_bonus_degats ";
            $req = $req . "from objets_caracs,objet_generique ";
            $req = $req . "where gobj_cod = $objet ";
            //$req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Dégâts</p></td>";
            echo "<td><p>" . $result['obcar_des_degats'] . "D" . $result['obcar_val_des_degats'] . "+" . $result['obcar_bonus_degats'] . "</p></td>";
            echo "</tr>";


            $req = "select comp_libelle from competences where comp_cod = $comp ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Compétence utilisée</p></td>";
            echo "<td><p>" . $result['comp_libelle'] . "</p></td>";
            echo "</tr>";
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
        if ($type_objet == 2)
        {
            $req = "select obcar_armure ";
            $req = $req . "from objets_caracs,objet_generique,objets ";
            $req = $req . "where gobj_cod = $objet ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Armure</p></td>";
            echo "<td><p>" . $result['obcar_armure'] . "</p></td>";
            echo "</tr>";
        }
        if ($type_objet == 1 and $distance == 'O')
        {
            $req = "select obcar_chute ";
            $req = $req . "from objets_caracs,objet_generique,objets ";
            $req = $req . "where gobj_cod = $objet ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and gobj_obcar_cod = obcar_cod ";
            $stmt = $pdo->query($req);
            if($result = $stmt->fetch())
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Chute</p></td>";
                echo "<td><p>" . $result['obcar_chute'] . "</p></td>";
                echo "</tr>";
            }

        }
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p>Description</p></td>";
        echo "<td><p>" . $desc . "</p></td>";
        echo "</tr>";

        if (isset($bon))
        {
            $req = "select obon_libelle from bonus_objets where obon_cod = $bon ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p>Bonus</p></td>";
            echo "<td><p>" . $result['obon_libelle'] . "</p></td>";
            echo "</tr>";
        }

        echo "</table></div>";
    } else
    {
        echo "<p>Aucun objet trouvé !";
    }
} else
{
    echo "Vous n'avez pas accès au détail de cet objet !";
}
$retour  = "inventaire.php";
$origine = $_REQUEST['origine'];
if ($origine == 'e')
{
    $retour = "lieu.php?methode=acheter";
}
if ($origine == 'a')
{
    $retour = "admin_echoppe_tarif.php";
}
echo "<a class='centrer' href=\"$retour\">Retour !</a>";


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

