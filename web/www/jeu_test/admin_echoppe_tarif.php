<?php
include "blocks/_header_page_jeu.php";
ob_start();
include "blocks/_test_admin_echoppe.php";
if (!isset($methode))
{
    $methode = "entree";
}
if ($erreur == 0)
{
	switch($methode)
	{
		case "entree":
			$req = "select tobj_libelle,gobj_nom,gobj_valeur,gobj_cod 
									from objet_generique,type_objet 
									where gobj_tobj_cod = tobj_cod 
									and gobj_visible = 'O' 
									and gobj_deposable = 'O' 
									and gobj_tobj_cod in (1,2,4,15,17,18,19,22) 
									order by tobj_libelle,gobj_nom ";
			$db->query($req);
			echo "<table>";
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Type d'objet</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Valeur</strong></td>";
			echo "<td></td>";
			while($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p><strong><a href=\"visu_desc_objet2.php?objet=" . $db->f("gobj_cod") . "&origine=a\">" . $db->f("gobj_nom") . "</a></strong></td>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("tobj_libelle") . "</td>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("gobj_valeur") . "</td>";
				echo "<td><p><a href=\"admin_echoppe_tarif.php?methode=e1&objet=" . $db->f("gobj_cod") . "\">Modifier !</a></td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<p style=\"text-align:center;\"><strong><a href=\"admin_echoppe_tarif.php?methode=detail\">Afficher le détail complet (long !)</A></strong>";
			break;
		case "detail":
			$req = "select tobj_libelle,gobj_nom,gobj_valeur,gobj_cod, ";
			$req = $req . "f_num_obj_perso(gobj_cod) as persos, ";
			$req = $req . "f_num_obj_sol(gobj_cod) as sol, ";
			$req = $req . "f_num_obj_echoppe(gobj_cod) as echoppe ";
			$req = $req . "from objet_generique,type_objet ";
			$req = $req . "where gobj_tobj_cod = tobj_cod ";
			$req = $req . "and gobj_visible = 'O' ";
			$req = $req . "and gobj_deposable = 'O' ";
			$req = $req . "and gobj_tobj_cod in (1,2,4,15,17,18,19,22) ";
			$req = $req . "order by tobj_libelle,gobj_nom ";
			echo "<!-- $req -->";
			$db->query($req);
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
			while($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p><strong><a href=\"visu_desc_objet2.php?objet=" . $db->f("gobj_cod") . "&origine=a\">" . $db->f("gobj_nom") . "</a></strong></td>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("tobj_libelle") . "</td>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("gobj_valeur") . "</td>";
				echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $db->f("persos") . "</td>";
				echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $db->f("sol") . "</td>";
				echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $db->f("echoppe") . "</td>";
				$total = $db->f("persos") + $db->f("sol") + $db->f("echoppe");
				echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $total . "</td>";
				echo "<td><p><a href=\"admin_echoppe_tarif.php?methode=e1&objet=" . $db->f("gobj_cod") . "\">Modifier !</a></td>";
				echo "</tr>";
			}
			echo "</table>";
			break;
		case "e1":
			echo "<p><strong>Attention !</strong> Modifier le prix générique d'un objet aura un impact sur TOUTES les échoppes.<br>";
			echo "Si un gérant a fixé un prix spécial pour cet objet dans une échoppe, votre modification sera également appliquée à son tarif.<br>";
			echo "Exemple : un objet coute 100br, et un gérant l'a fixé à 80 chez lui. Vous changez le prix en 200 br, pour l'échoppe du gérant, cela passera à 160 (règle de 3).";
			echo "<form name=\"prix\" method=\"post\" action=\"admin_echoppe_tarif.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"e2\">";
			echo "<input type=\"hidden\" name=\"objet\" value=\"$objet\">";
			$req = "select gobj_nom,gobj_valeur from objet_generique where gobj_cod = $objet ";
			$db->query($req);
			$db->next_record();
			echo "<p>Fixer le tarif de <strong>" . $db->f("gobj_nom") . "</strong> à ";
			echo "<input type=\"text\" name=\"montant\"value=\"" . $db->f("gobj_valeur") . "\">";
			echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center>";
			echo "</form>";
			echo "<p style=\"text-align:center\"><a href=\"admin_echoppe_tarif.php\">Revenir au menu</a>";
			break;
		case "e2":
			$erreur = 0;
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
				// etape 1 : on cherche le premier prix
				$req = "select gobj_valeur from objet_generique where gobj_cod = $objet ";
				$db->query($req);
				$db->next_record();
				$prix1 = $db->f("gobj_valeur");
				$rapport = $montant/$prix1;
				//etape 2 : on modifie le prix générique
				$req = "update objet_generique set gobj_valeur = $montant where gobj_cod = $objet ";
				$db->query($req);
				// etape 3 : on modifie les prix particulieurs
				$req = "update magasin_tarif set mtar_prix = round(mtar_prix * $rapport) where mtar_gobj_cod = $objet ";
				$db->query($req);
				// etape 4 : on modifie les prix des objets individualisé des souterrains, à l'exception des objets "individualisé"
				$req = "update objets set obj_valeur = $montant where gobj_cod = $objet and obj_modifie = 0";
				$db->query($req);
				echo "<p>Modif effectuée !";
			}		
			break;
	}

}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
