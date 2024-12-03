<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:24
 */
$verif_connexion::verif_appel();
include G_CHE . "jeu_test/blocks/_test_admin_echoppe.php";

if ($erreur == 0)
{
    $req_get_perso = "select perso_cod,perso_nom 
            from perso,guilde_perso
            where pguilde_guilde_cod = 211
            and pguilde_valide = 'O'
            and pguilde_perso_cod = perso_cod
            union
            select perso_cod,perso_nom
            from perso,guilde_perso
            where pguilde_valide = 'O'
            and pguilde_meta_caravane = 'O'
            and pguilde_perso_cod = perso_cod
            union
            select perso_cod,perso_nom
            from perso,guilde_perso
            where pguilde_valide = 'O'
            and pguilde_meta_noir = 'O'
            and pguilde_perso_cod = perso_cod
            union
            select perso_cod,perso_nom
            from perso,guilde_perso
            where pguilde_guilde_cod = getparm_n(75)
            and pguilde_valide = 'O'
            and pguilde_perso_cod = perso_cod
            order by perso_nom ";


    // pour vérif, on récupère les coordonnées du magasin
    $tmplieu = new lieu;
    $tmplieu->charge($lieu);
    $pos = $tmplieu->getPos();
    echo "<p class=\"titre\">Gestion de l'échoppe " . $pos['pos']->pos_x . ", " . $pos['pos']->pos_y . ", " . $pos['etage']->etage_libelle . "</p>";
    switch ($_REQUEST['methode'])
    {
        case "ajout":
            echo "<form name=\"gerant\" method=\"post\" action=\"valide_gerant{$_admin_echoppe_type}.php\">";
            echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"ajout\">";
            echo "<p>Ajout d'un gérant :";

            $stmt = $pdo->query($req_get_perso);
            echo " <select name = \"perso_cible\">";
            while ($result = $stmt->fetch())
            {
                echo "<option value=\"" . $result['perso_cod'] . "\">" . $result['perso_nom'] . "</option>";
            }
            echo "</select>";
            echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
            break;
        case "modif":
            $req    = "select mger_perso_cod from magasin_gerant where mger_lieu_cod = $lieu ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $actuel = $result['mger_perso_cod'];
            echo "<form name=\"gerant\" method=\"post\" action=\"valide_gerant{$_admin_echoppe_type}.php\">";
            echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"modif\">";
            echo "<p>Modification d'un gérant :";

            $stmt = $pdo->query($req_get_perso);
            echo "<select name=\"perso_cible\">";
            while ($result = $stmt->fetch())
            {
                echo "<option value=\"" . $result['perso_cod'] . "\"";
                if ($result['perso_cod'] == $actuel)
                {
                    echo " selected";
                }
                echo ">" . $result['perso_nom'] . "</option>";
            }
            echo "</select>";
            echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
            break;
        case "supprime":
            $req    =
                "select mger_perso_cod,perso_nom from magasin_gerant,perso where mger_lieu_cod = $lieu and mger_perso_cod = perso_cod";
            $stmt   = $pdo->query($req);
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
