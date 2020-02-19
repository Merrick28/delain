<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;
$compte = new compte;
$compte = $verif_connexion->compte;

if ($compte->is_admin())
{
    echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
$methode = get_request_var('methode', 'entree');
if ($erreur == 0)
{
    switch ($methode)
    {
        case "entree":
            $req = "select tobj_libelle,gobj_nom,gobj_valeur,gobj_cod, ";
            $req = $req . "f_num_obj_perso(gobj_cod) as persos, ";
            $req = $req . "f_num_obj_sol(gobj_cod) as sol, ";
            $req = $req . "f_num_obj_echoppe(gobj_cod) as echoppe ";
            $req = $req . "from objet_generique,type_objet ";
            $req = $req . "where gobj_tobj_cod = tobj_cod ";
            $req = $req . "and gobj_visible = 'O' ";
            $req = $req . "and gobj_deposable = 'O' ";
            $req = $req . "and gobj_tobj_cod not in (12,7,9,10,6,8,13,11) ";
            $req = $req . "order by tobj_libelle,gobj_nom ";
            echo "<!-- $req -->";
            $stmt = $pdo->query($req);
            echo "<table>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Type d'objet</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Valeur</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Persos/monstres</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Au sol</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Stock échoppes</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Total</strong></td>";
            echo "<td></td>";
            while ($result = $stmt->fetch())
            {
                require "blocks/_ligne_echoppe_1.php";
            }
            echo "</table>";
            break;
        case "e1":
            $objet = $_REQUEST['objet'];
            echo "<p><strong>Attention !</strong> Modifier le prix générique d'un objet aura un impact sur TOUTES les échoppes.<br>";
            echo "Si un gérant a fixé un prix spécial pour cet objet dans une échoppe, votre modification sera également appliquée à son tarif.<br>";
            echo "Exemple : un objet coute 100br, et un gérant l'a fixé à 80 chez lui. Vous changez le prix en 200 br, pour l'échoppe du gérant, cela passera à 160 (règle de 3).";
            echo "<form name=\"prix\" method=\"post\" action=\"voir_tarif_echoppe.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"e2\">";
            echo "<input type=\"hidden\" name=\"objet\" value=\"$objet\">";
            $req    = "select gobj_nom,gobj_valeur from objet_generique where gobj_cod = $objet ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            echo "<p>Fixer le tarif de <strong>" . $result['gobj_nom'] . "</strong> à ";
            echo "<input type=\"text\" name=\"montant\"value=\"" . $result['gobj_valeur'] . "\">";
            echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center>";
            echo "</form>";
            echo "<p style=\"text-align:center\"><a href=\"voir_tarif_echoppe.php\">Revenir au menu</a>";
            break;
        case "e2":
            $objet   = $_REQUEST['objet'];
            $montant = $_REQUEST['montant'];
            $erreur  = 0;
            if (!preg_match('/^[0-9]*$/i', $montant))
            {
                echo "<p>Anomalie sur le motnant : il ne doit contenir que des chiffres !";
                $erreur = 1;
            }
            if ($montant <= 0)
            {
                echo "<p>Erreur ! Le montant doit être sitrctement positif !";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                // etape 1 : on cherche le premier prix
                $req     = "select gobj_valeur from objet_generique where gobj_cod = $objet ";
                $stmt    = $pdo->query($req);
                $result  = $stmt->fetch();
                $prix1   = $result['gobj_valeur'];
                $rapport = $montant / $prix1;
                //etape 2 : on modifie le prix générique
                $req  = "update objet_generique set gobj_valeur = $montant where gobj_cod = $objet ";
                $stmt = $pdo->query($req);
                // etape 3 : on modifie les prix particulieurs
                $req  =
                    "update magasin_tarif set mtar_prix = round(mtar_prix * $rapport) where mtar_gobj_cod = $objet ";
                $stmt = $pdo->query($req);
                echo "<p>Modif effectuée !";
            }
            break;
    }

}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
