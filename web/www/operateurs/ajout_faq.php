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
echo("<table>");
echo("<form name=\"ajout_faq\" method=\"post\" action=\"valide_ajout_faq.php\">");
echo("<tr>");
echo("<td class=\"soustitre3\"><p>Type :</td>");
echo("<td><select name=\"type\">");
$req_type = "select tfaq_cod,tfaq_libelle from faq_type order by tfaq_cod";
$res_type = pg_exec($dbconnect,$req_type);
$nb_type = pg_numrows($res_type);
for ($cpt_type=0;$cpt_type<$nb_type;$cpt_type++)
{
	$tab_type = pg_fetch_array($res_type,$cpt_type);
	echo("<option value=\"$tab_type[0]\">$tab_type[1]</option>");
}
echo("</select></td></tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Question :</td>");
echo("<td><p><textarea name=\"question\" cols=\"40\" rows=\"10\"></textarea></td>");
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Réponse :</td>");
echo("<td><p><textarea name=\"reponse\" cols=\"40\" rows=\"10\"></textarea></td>");
echo("</tr>");

echo("<tr>");
echo("<td colspan=\"2\"><center><input type=\"submit\" value=\"valider\" class=\"test\"></center></td>");
echo("</tr>");


echo("</table>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

