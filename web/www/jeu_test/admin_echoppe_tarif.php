<?php
include "blocks/_header_page_jeu.php";
ob_start();
define('APPEL', 1);
include "blocks/_test_admin_echoppe.php";

$methode           = get_request_var('methode', 'entree');
if ($erreur == 0)
{
    switch ($methode)
    {
        case "entree":
            $req  = "select tobj_libelle,gobj_nom,gobj_valeur,gobj_cod 
									from objet_generique,type_objet 
									where gobj_tobj_cod = tobj_cod 
									and gobj_visible = 'O' 
									and gobj_deposable = 'O' 
									and gobj_tobj_cod in (1,2,4,15,17,18,19,22) 
									order by tobj_libelle,gobj_nom ";
            $stmt = $pdo->query($req);
            echo "<table>";
            echo "<tr>";
            echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Type d'objet</strong></td>";
            echo "<td class=\"soustitre2\"><p><strong>Valeur</strong></td>";
            echo "<td></td>";
            while ($result = $stmt->fetch())
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p><strong><a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=a\">" . $result['gobj_nom'] . "</a></strong></td>";
                echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
                echo "<td class=\"soustitre2\"><p>" . $result['gobj_valeur'] . "</td>";
                echo "<td><p><a href=\"admin_echoppe_tarif.php?methode=e1&objet=" . $result['gobj_cod'] . "\">Modifier !</a></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p style=\"text-align:center;\"><strong><a href=\"admin_echoppe_tarif.php?methode=detail\">Afficher le détail complet (long !)</A></strong>";
            break;
        case "detail":
            $req = "select tobj_libelle,gobj_nom,gobj_valeur,gobj_cod,
			f_num_obj_perso(gobj_cod) as persos, 
			f_num_obj_sol(gobj_cod) as sol, 
			f_num_obj_echoppe(gobj_cod) as echoppe 
			from objet_generique,type_objet 
			where gobj_tobj_cod = tobj_cod
			and gobj_visible = 'O' 
			and gobj_deposable = 'O' 
			and gobj_tobj_cod in (1,2,4,15,17,18,19,22)
			order by tobj_libelle,gobj_nom ";
            require "blocks/_tab_ligne_echoppe.php";
            break;
        case "e1":
            echo "<p><strong>Attention !</strong> Modifier le prix générique d'un objet aura un impact sur TOUTES les échoppes.<br>";
            echo "Si un gérant a fixé un prix spécial pour cet objet dans une échoppe, votre modification sera également appliquée à son tarif.<br>";
            echo "Exemple : un objet coute 100br, et un gérant l'a fixé à 80 chez lui. Vous changez le prix en 200 br, pour l'échoppe du gérant, cela passera à 160 (règle de 3).";
            echo "<form name=\"prix\" method=\"post\" action=\"admin_echoppe_tarif.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"e2\">";
            echo '<input type="hidden" name="objet" value="' . $_REQUEST['objet'] . '\">';
            $gobj = new objet_generique();
            $gobj->charge($_REQUEST['objet']);

            echo "<p>Fixer le tarif de <strong>" . $gobj->gobj_nom . "</strong> à ";
            echo "<input type=\"text\" name=\"montant\"value=\"" . $gobj->gobj_valeur . "\">";
            echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center>";
            echo "</form>";
            echo "<p style=\"text-align:center\"><a href=\"admin_echoppe_tarif.php\">Revenir au menu</a>";
            break;
        case "e2":
            $erreur  = 0;
            $montant = $_REQUEST['montant'];
            $objet   = $_REQUEST['objet'];
            if (!preg_match('/^[0-9]*$/i', $montant))
            {
                echo "<p>Anomalie sur le montant : il ne doit contenir que des chiffres !";
                $erreur = 1;
            }
            if ($montant <= 0)
            {
                echo "<p>Erreur ! Le montant doit être strictement positif !";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $gobj = new objet_generique();
                $gobj->charge($objet);
                // etape 1 : on cherche le premier prix
                $prix1   = $gobj->gobj_valeur;
                $rapport = $montant / $prix1;
                //etape 2 : on modifie le prix générique
                $gobj->gobj_valeur = $montant;
                $gobj->stocke();

                // etape 3 : on modifie les prix particulieurs
                $req  =
                    "update magasin_tarif set mtar_prix = round(mtar_prix * $rapport) where mtar_gobj_cod = $objet ";
                $stmt = $pdo->query($req);

                // etape 4 : on modifie les prix des objets individualisé des souterrains, à l'exception des objets "individualisé"
                $req  = "update objets set obj_valeur = $montant where gobj_cod = $objet and obj_modifie = 0";
                $stmt = $pdo->query($req);

                echo "<p>Modif effectuée !";
            }
            break;
    }

}
$contenu_page = ob_get_contents();
ob_end_clean();


$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'PERSO'        => $perso,
    'PHP_SELF'     => $_SERVER['PHP_SELF'],
    'CONTENU_PAGE' => $contenu_page

);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));