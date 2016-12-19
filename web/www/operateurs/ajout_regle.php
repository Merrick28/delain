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
echo("<form name=\"ajout_regle\" method=\"post\" action=\"valide_ajout_regle.php\">");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Titre :</td>");
echo("<td><p><input type=\"text\" name=\"titre\"></td>");
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Texte :</td>");
echo("<td><p><textarea name=\"texte\" cols=\"80\" rows=\"30\"></textarea></td>");
echo("</tr>");

echo("<tr>");
echo("<td colspan=\"2\"><center><input type=\"submit\" value=\"valider\" class=\"test\"></center></td>");
echo("</tr>");


echo("</table>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

