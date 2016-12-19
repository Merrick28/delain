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
echo("<form name=\"gestion_regles\" method=\"post\">");
echo("<input type=\"hidden\" name=\"code\">");
echo("<table cellspacing=\"2\" cellpadding=\"2\" border=\"1\" bordercolor=\"#000000\">");
echo("<tr>");
echo("<td class=\"soustitre3\"><p><b>Code</b></td>");
echo("<td class=\"soustitre2\"><p><b>Titre</b></td>");
echo("<td class=\"soustitre2\"><p><b>Texte</b></td>");
echo("<td></td>");
echo("<td></td>");
echo("</tr>");

$req_type = "select regle_cod,regle_titre,regle_texte from regles order by regle_cod ";
$res_type = pg_exec($dbconnect,$req_type);
$nb_type = pg_numrows($res_type);
for ($cpt=0;$cpt<$nb_type;$cpt++)
{
	$tab = pg_fetch_array($res_type,$cpt);
	echo("<tr>");
	echo("<td class=\"soustitre2\"><p>$tab[0]</td>");
	echo("<td class=\"soustitre2\"><p>$tab[1]</td>");
	echo("<td><p>$tab[2]</td>");
	echo("<td><input type=\"button\" value=\"Modifier !\" class=\"test\" onClick=\"javascript:document.gestion_regles.action='modif_regle.php';document.gestion_regles.code.value=$tab[0];document.gestion_regles.submit();\"></td>");
	echo("<td><input type=\"button\" value=\"Effacer !\" class=\"test\" onClick=\"javascript:document.gestion_regles.action='efface_regle.php';document.gestion_regles.code.value=$tab[0];document.gestion_regles.submit();\"></td>");
	echo("</tr>");
	
}
echo("</table>");
echo("</form>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

