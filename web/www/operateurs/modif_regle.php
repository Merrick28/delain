<?php 
include "../connexion.php";
?>
<html>
<head>
<title>Les souterrains de Delain : partie opérateurs</title>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
</head>
<body background="../images/fond5.gif">
<?php 
$user = $_SERVER["REMOTE_USER"];
include "../jeu/tab_haut.php";
?>
<p><a href="ajout_regle.php">Ajouter une règle</a>

<?php 
echo("<form name=\"gestion_regles\" method=\"post\" action=\"valide_modif_regle.php\">");
echo("<input type=\"hidden\" name=\"code\" value=\"$code\">");
echo("<table cellspacing=\"2\" cellpadding=\"2\" border=\"1\" bordercolor=\"#000000\">");
echo("<tr>");
echo("<td class=\"soustitre3\"><p><b>Code</b></td>");
echo("<td class=\"soustitre2\"><p><b>Titre</b></td>");
echo("<td class=\"soustitre2\"><p><b>Texte</b></td>");
echo("</tr>");

$req_type = "select regle_cod,regle_titre,regle_texte from regles where regle_cod = $code ";
$res_type = pg_exec($dbconnect,$req_type);
$nb_type = pg_numrows($res_type);

	$tab = pg_fetch_array($res_type,0);
	echo("<tr>");
	echo("<td class=\"soustitre2\"><p>$tab[0]</td>");
	echo("<td class=\"soustitre2\"><input type=\"text\" name=\"titre\" value=\"$tab[1]\"></td>");
	echo("<td><p><textarea name=\"texte\" cols=\"80\" rows=\"30\">$tab[2]</textarea></td>");
	echo("</tr>");
	
echo("<tr>");
echo("<td colspan=\"3\"><p style=\"text-align:center\"><input type=\"submit\" value=\"Valider !\"></td></tr>");

echo("</table>");
echo("</form>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

