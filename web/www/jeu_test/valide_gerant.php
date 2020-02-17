<?php
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
define('APPEL', 1);
include "blocks/_test_admin_echoppe.php";
if ($erreur == 0)
{
    // Conversion en numeric pour minimiser l'injection sql
    $perso_cible = 1 * (int)$perso_cible;
    $lieu        = 1 * (int)$lieu;

    // pour vérif, on récupère les coordonnées du magasin
    $tmplieu = new lieu;
    $tmplieu->charge($lieu);
    $pos = $tmplieu->getPos();
    echo "<p class=\"titre\">Gestion de l'échoppe " . $tmplieu['pos']->pos_x . ", " . $tmplieu['pos']->pos_y . ", " .
         $tmplieu['etage']->etage_libelle . "</p>";
    switch ($_REQUEST['methode'])
    {
        case "ajout":
            $req = "insert into magasin_gerant (mger_perso_cod,mger_lieu_cod) values ($perso_cible,$lieu) ";
            if ($stmt = $pdo->query($req))
            {
                echo "<p>Modif effectuée !";
            } else
            {
                echo "<p>Anomalie sur la requête !";
            }
            break;
        case "modif":
            $req = "update magasin_gerant set mger_perso_cod = $perso_cible where mger_lieu_cod = $lieu ";
            if ($stmt = $pdo->query($req))
            {
                echo "<p>Modif effectuée !";
            } else
            {
                echo "<p>Anomalie sur la requête !";
            }
            break;
        case "supprime":
            $req = "delete from  magasin_gerant where mger_lieu_cod = $lieu ";
            if ($stmt = $pdo->query($req))
            {
                echo "<p>Modif effectuée !";
            } else
            {
                echo "<p>Anomalie sur la requête !";
            }
            break;
    }


}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
