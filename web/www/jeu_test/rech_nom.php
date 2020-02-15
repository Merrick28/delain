<?php
include "blocks/_header_page_jeu.php";
ob_start();
$methode = get_request_var('methode', 'entree');
switch($methode)
{
	case "entree":
		echo "<form name=\"rech\" method=\"post\" action=\"rech_nom.php\">";
		echo "<input type=\"hidden\" name=\"methode\" value=\"valide\">";
		echo "<p>Entrez le nom du compte ou d'un de ses persos à rechercher : <input type=\"text\" name=\"nom\">";
		echo "<p><center><input type=\"submit\" class=\"test\" value=\"Rechercher !\">";
		echo "</form>";
		break;
	case "valide":
		$req = "select compt_cod, compt_nom, compt_mail, to_char(compt_dcreat,'DD/MM/YYYY hh24:mi:ss') as creation,
			to_char(compt_der_connex,'DD/MM/YYYY hh24:mi:ss') as connex,
			compt_ip, compt_commentaire, compt_actif from compte ";
		$req = $req . "where lower(compt_nom) like '%'||lower('" . str_replace("'", "''", $nom) . "')||'%' order by compt_nom ";
		$stmt = $pdo->query($req);
		if ($stmt->rowCount() > 50)
		{
			echo "<p>Plus de 50 réponses, merci d'affiner votre recherche.";
			echo "<br><a href=\"rech_nom.php\">Retour</a>";
		}
		else
		{
		echo "<table>";
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p><strong>Numéro</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>Nom</strong> (cliquez sur le nom pour détails)</td>";
		echo "<td class=\"soustitre2\"><p><strong>Actif</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>Mail</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>Date création</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>Dernière connexion</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>IP</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>Commentaire</strong></td>";
		echo "</tr>";
		while ($result = $stmt->fetch())
		{
			echo "<tr>";	
			echo "<td><p>" . $result['compt_cod'] . "</td>";
			echo "<td class=\"soustitre2\"><p><strong><a href=\"detail_compte.php?vcompte=" . $result['compt_cod'] . "\">" . $result['compt_nom'] . "</a></strong></td>";
			echo "<td><p>" . $result['compt_actif'] . "</td>";
			echo "<td><p>" . $result['compt_mail'] . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $result['creation'] . "</td>";
			echo "<td><p>" . $result['connex'] . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $result['compt_ip'] . "</td>";
			echo "<td><p>" . $result['compt_commentaire'] . "</td>";
			echo "</tr>";
		}
		}
		$req = "select compt_cod, compt_nom, compt_mail, to_char(compt_dcreat,'DD/MM/YYYY hh24:mi:ss') as creation,
			to_char(compt_der_connex,'DD/MM/YYYY hh24:mi:ss') as connex,
			compt_ip, compt_commentaire, compt_actif from compte, perso_compte, perso ";
		$req = $req . "where lower(perso_nom) like '%'||lower('" . str_replace("'", "''", $nom) . "')||'%' and compt_cod = pcompt_compt_cod and pcompt_perso_cod = perso_cod order by compt_nom ";
		$stmt = $pdo->query($req);
		if ($stmt->rowCount() > 50)
		{
			echo "<p>Plus de 50 réponses, merci d'affiner votre recherche.";
			echo "<br><a href=\"rech_nom.php\">Retour</a>";
		}
		else
		{
		while ($result = $stmt->fetch())
		{
			echo "<tr>";	
			echo "<td><p>" . $result['compt_cod'] . "</td>";
			echo "<td class=\"soustitre2\"><p><strong><a href=\"detail_compte.php?vcompte=" . $result['compt_cod'] . "\">" . $result['compt_nom'] . "</a></strong></td>";
			echo "<td><p>" . $result['compt_actif'] . "</td>";
			echo "<td><p>" . $result['compt_mail'] . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $result['creation'] . "</td>";
			echo "<td><p>" . $result['connex'] . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $result['compt_ip'] . "</td>";
			echo "<td><p>" . $result['compt_commentaire'] . "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
		}
		break;	
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

