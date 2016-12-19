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
<p><a href="ajout_faq.php">Ajouter une question/réponse</a>

<?php 
echo("<form name=\"gestion_faq\" method=\"post\">");
echo("<input type=\"hidden\" name=\"code\">");
echo("<table cellspacing=\"2\" cellpadding=\"2\">");
echo("<tr>");
echo("<td class=\"soustitre3\"><p><b>Code</b></td>");
echo("<td class=\"soustitre2\"><p><b>Type</b></td>");
echo("<td class=\"soustitre2\"><p><b>Question</b></td>");
echo("<td class=\"soustitre2\"><p><b>Réponse</b></td>");
echo("<td></td>");
echo("</tr>");

$req_type = "select tfaq_cod,tfaq_libelle from faq_type order by tfaq_cod";
$res_type = pg_exec($dbconnect,$req_type);
$nb_type = pg_numrows($res_type);
for ($cpt_type=0;$cpt_type<$nb_type;$cpt_type++)
{
	$tab_type = pg_fetch_array($res_type,$cpt_type);
	$req_faq = "select faq_cod,faq_question,faq_reponse from faq where faq_tfaq_cod = $tab_type[0] order by faq_cod";
	$res_faq = pg_exec($dbconnect,$req_faq);
	$nb_faq = pg_numrows($res_faq);
	for ($cpt2=0;$cpt2<$nb_faq;$cpt2++)
	{
		$tab_faq = pg_fetch_array($res_faq,$cpt2);
		echo("<tr>");
		echo("<td class=\"soustitre3\"><p>$tab_faq[0]</td>");
		echo("<td class=\"soustitre3\"><p>$tab_type[1]</td>");
		echo("<td class=\"soustitre3\"><p>$tab_faq[1]</td>");
		echo("<td class=\"soustitre3\"><p>$tab_faq[2]</td>");
		echo("<td><input type=\"button\" value=\"Effacer !\" class=\"test\" onClick=\"javascript:document.gestion_faq.action='efface_faq.php';document.gestion_faq.code.value=$tab_faq[0];document.gestion_faq.submit();\"></td>");
		echo("</tr>");
		
	}
}
echo("</table>");
echo("</form>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

