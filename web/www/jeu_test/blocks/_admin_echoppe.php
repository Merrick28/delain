<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:24
 */
$verif_connexion::verif_appel();
include G_CHE . "jeu_test/blocks/_test_admin_echoppe.php";

if ($erreur == 0) {
    // pour vérif, on récupère les coordonnées du magasin
    $req = "select pos_x,pos_y,etage_libelle ";
    $req = $req . "from lieu_position,positions,etage ";
    $req = $req . "where lpos_lieu_cod = $lieu ";
    $req = $req . "and lpos_pos_cod = pos_cod ";
    $req = $req . "and pos_etage = etage_numero ";
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    echo "<p class=\"titre\">Gestion de l'échoppe " . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['etage_libelle'] . "</p>";
    switch ($_REQUEST['methode'])
    {
        case "ajout":
            echo "<form name=\"gerant\" method=\"post\" action=\"valide_gerant{$_admin_echoppe_type}.php\">";
            echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"ajout\">";
            echo "<p>Ajout d'un gérant :";
            $req = "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_guilde_cod = 211 ";
            $req = $req . "and pguilde_valide = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "union ";
            $req = $req . "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_valide = 'O' ";
            $req = $req . "and pguilde_meta_caravane = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "union ";
            $req = $req . "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_valide = 'O' ";
            $req = $req . "and pguilde_meta_noir = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "union ";
            $req = $req . "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_guilde_cod = getparm_n(75) ";
            $req = $req . "and pguilde_valide = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "order by perso_nom ";
            $stmt = $pdo->query($req);
            echo "<select name=\"perso_cible\">";
            while ($result = $stmt->fetch()) {
                echo "<option value=\"" . $result['perso_cod'] . "\">" . $result['perso_nom'] . "</option>";
            }
            echo "</select>";
            echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
            break;
        case "modif":
            $req = "select mger_perso_cod from magasin_gerant where mger_lieu_cod = $lieu ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $actuel = $result['mger_perso_cod'];
            echo "<form name=\"gerant\" method=\"post\" action=\"valide_gerant{$_admin_echoppe_type}.php\">";
            echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"modif\">";
            echo "<p>Modification d'un gérant :";
            $req = "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_guilde_cod = 211 ";
            $req = $req . "and pguilde_valide = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "union ";
            $req = $req . "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_valide = 'O' ";
            $req = $req . "and pguilde_meta_caravane = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "union ";
            $req = $req . "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_valide = 'O' ";
            $req = $req . "and pguilde_meta_noir = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "union ";
            $req = $req . "select perso_cod,perso_nom ";
            $req = $req . "from perso,guilde_perso ";
            $req = $req . "where pguilde_guilde_cod = getparm_n(75) ";
            $req = $req . "and pguilde_valide = 'O' ";
            $req = $req . "and pguilde_perso_cod = perso_cod ";
            $req = $req . "order by perso_nom ";
            $stmt = $pdo->query($req);
            echo "<select name=\"perso_cible\">";
            while ($result = $stmt->fetch()) {
                echo "<option value=\"" . $result['perso_cod'] . "\"";
                if ($result['perso_cod'] == $actuel) {
                    echo " selected";
                }
                echo ">" . $result['perso_nom'] . "</option>";
            }
            echo "</select>";
            echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
            break;
        case "supprime":
            $req = "select mger_perso_cod,perso_nom from magasin_gerant,perso where mger_lieu_cod = $lieu and mger_perso_cod = perso_cod";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>Voulez-vous rééllement enlever à " . $result['perso_nom'] . " la gestion de ce magasin ?";
            echo "<form name=\"gerant\" method=\"post\" action=\"valide_gerant_noir.php\">";
            echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"supprime\">";
            echo "<p><a href=\"javascript:document.gerant.submit();\">OUI ! je le veux ! </a><br>";
            echo "<a href=\"gestion_gerant.php\">Non, je ne le veux pas.</a>";
            echo "</form>";
            break;
    }


}
$contenu_page = ob_get_contents();
ob_end_clean();
