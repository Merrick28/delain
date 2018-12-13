<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$erreur = 0;
$req = "select dper_dieu_cod,dper_niveau from dieu_perso where dper_perso_cod = $perso_cod";
$db->query($req);
if ($db->nf() == 0)
{
	echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !1";
	$erreur = 1;
}
else
{
	$db->next_record();
}
if ($db->f("dper_niveau") < 4)
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !2";
	$erreur = 1;
}
if ($erreur == 0)
{
	$dieu_perso = $db->f("dper_dieu_cod");
	// pour vérif, on récupère les coordonnées du temple
	$req = "select pos_x,pos_y,etage_libelle ";
	$req = $req . "from lieu_position,positions,etage ";
	$req = $req . "where lpos_lieu_cod = $lieu ";
	$req = $req . "and lpos_pos_cod = pos_cod ";
	$req = $req . "and pos_etage = etage_numero ";
	$db->query($req);
	$db->next_record();
	echo "<p class=\"titre\">Gestion du temple " . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</p>";
	switch($methode)
	{
		case "ajout":
			echo "<form name=\"gerant\" method=\"post\" action=\"$PHP_SELF\">";
			echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"ajout2\">";
			echo "<p>Ajout d'un responsable du temple :";
			$req = "select perso_cod,perso_nom,dper_niveau,dniv_libelle
						from perso,dieu_perso,dieu_niveau
						where perso_cod = dper_perso_cod
						and dper_dieu_cod = $dieu_perso
						and dniv_dieu_cod = dper_dieu_cod
						and dniv_niveau = dper_niveau
						and dper_niveau > 2
						and perso_actif = 'O'
						order by perso_nom ";
			$db->query($req);
			echo "<select name=\"perso\">";
			while ($db->next_record())
			{
				echo "<option value=\"" . $db->f("perso_cod") . "\">" . $db->f("perso_nom") . " <em>(" . $db->f("dniv_libelle") . ")</em></option>";	
			}
			echo "</select>";
			echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
			break;
		case "ajout2":
			$req = "insert into temple_fidele (tfid_perso_cod,tfid_lieu_cod) values ($perso_cible,$lieu) ";
			if ($db->query($req))
			{
				echo "<p>Modif effectuée !";
			}
			else
			{
				echo "<p>Anomalie sur la requête !";
			}
			break;
		case "modif":
			$req = "select tfid_perso_cod from temple_fidele where tfid_lieu_cod = $lieu ";
			$db->query($req);
			$db->next_record();
			$actuel = $db->f("tfid_perso_cod");
			echo "<form name=\"gerant\" method=\"post\" action=\"$PHP_SELF\">";
			echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"modif2\">";
			echo "<p>Modification d'un responsable de temple :";
			$req = "select perso_cod,perso_nom,dper_niveau,dniv_libelle
						from perso,dieu_perso,dieu_niveau
						where perso_cod = dper_perso_cod
						and dper_dieu_cod = $dieu_perso
						and dniv_dieu_cod = dper_dieu_cod
						and dniv_niveau = dper_niveau
						and dper_niveau > 2
						and perso_actif = 'O'
						order by perso_nom ";
			$db->query($req);
			echo "<select name=\"perso\">";
			while ($db->next_record())
			{
				echo "<option value=\"" . $db->f("perso_cod") . "\"";
				if ($db->f("perso_cod") == $actuel)
				{
					echo " selected";
				}
				echo ">" . $db->f("perso_nom") . " <em>(" . $db->f("dniv_libelle") . ")</em></option>";	
			}
			echo "</select>";
			echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
			break;
		case "modif2":
			$req = "update temple_fidele set tfid_perso_cod = $perso_cible where tfid_lieu_cod = $lieu ";
			if ($db->query($req))
			{
				echo "<p>Modif effectuée !";
			}
			else
			{
				echo "<p>Anomalie sur la requête !";
			}
			break;
		case "supprime":
			$req = "select tfid_perso_cod,perso_nom from temple_fidele,perso where tfid_lieu_cod = $lieu and tfid_perso_cod = perso_cod";
			$db->query($req);
			$db->next_record();
			echo "<p>Voulez-vous rééllement enlever à " . $db->f("perso_nom") . " la gestion de ce temple ?";
			echo "<form name=\"gerant\" method=\"post\" action=\"$PHP_SELF\">";
			echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"supprime2\">";
			echo "<p><a href=\"javascript:document.gerant.submit();\">OUI ! je le veux ! </a><br>";
			echo "<a href=\"gerant_temple.php\">Non, je ne le veux pas.</a>";
			echo "</form>";
			break;
		case "supprime2":
			$req = "delete from  temple_fidele where tfid_lieu_cod = $lieu ";
			if ($db->query($req))
			{
				echo "<p>Modif effectuée !";
			}
			else
			{
				echo "<p>Anomalie sur la requête !";
			}
			break;
	}
}
				echo "<p><a href=\"gerant_temple.php\">Retour à l'affectation des temples</a>";
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');

