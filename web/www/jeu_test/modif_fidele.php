<?php
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;
$req = "select dper_dieu_cod,dper_niveau from dieu_perso where dper_perso_cod = $perso_cod";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
	echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !1";
	$erreur = 1;
}
else
{
	$result = $stmt->fetch();
}
if ($result['dper_niveau'] < 4)
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !2";
	$erreur = 1;
}
if ($erreur == 0)
{
	$dieu_perso = $result['dper_dieu_cod'];
	// pour vérif, on récupère les coordonnées du temple
	$req = "select pos_x,pos_y,etage_libelle ";
	$req = $req . "from lieu_position,positions,etage ";
	$req = $req . "where lpos_lieu_cod = $lieu ";
	$req = $req . "and lpos_pos_cod = pos_cod ";
	$req = $req . "and pos_etage = etage_numero ";
	$stmt = $pdo->query($req);
	$result = $stmt->fetch();
	echo "<p class=\"titre\">Gestion du temple " . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['etage_libelle'] . "</p>";
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
			$stmt = $pdo->query($req);
			echo "<select name=\"perso\">";
			while ($result = $stmt->fetch())
			{
				echo "<option value=\"" . $result['perso_cod'] . "\">" . $result['perso_nom'] . " <em>(" . $result['dniv_libelle'] . ")</em></option>";	
			}
			echo "</select>";
			echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
			break;
		case "ajout2":
			$req = "insert into temple_fidele (tfid_perso_cod,tfid_lieu_cod) values ($perso_cible,$lieu) ";
			if ($stmt = $pdo->query($req))
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
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$actuel = $result['tfid_perso_cod'];
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
			$stmt = $pdo->query($req);
			echo "<select name=\"perso\">";
			while ($result = $stmt->fetch())
			{
				echo "<option value=\"" . $result['perso_cod'] . "\"";
				if ($result['perso_cod'] == $actuel)
				{
					echo " selected";
				}
				echo ">" . $result['perso_nom'] . " <em>(" . $result['dniv_libelle'] . ")</em></option>";	
			}
			echo "</select>";
			echo "<p><center><input type=\"submit\" value=\"Valider !\" class=\"test\"></center></form>";
			break;
		case "modif2":
			$req = "update temple_fidele set tfid_perso_cod = $perso_cible where tfid_lieu_cod = $lieu ";
			if ($stmt = $pdo->query($req))
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
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			echo "<p>Voulez-vous rééllement enlever à " . $result['perso_nom'] . " la gestion de ce temple ?";
			echo "<form name=\"gerant\" method=\"post\" action=\"$PHP_SELF\">";
			echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"supprime2\">";
			echo "<p><a href=\"javascript:document.gerant.submit();\">OUI ! je le veux ! </a><br>";
			echo "<a href=\"gerant_temple.php\">Non, je ne le veux pas.</a>";
			echo "</form>";
			break;
		case "supprime2":
			$req = "delete from  temple_fidele where tfid_lieu_cod = $lieu ";
			if ($stmt = $pdo->query($req))
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
include "blocks/_footer_page_jeu.php";

