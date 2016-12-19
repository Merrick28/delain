<?php 
include "../includes/classes.php";
$db= new base_delain;
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
<p><a href="ajout_idee.php">Ajouter une idée</a>

<?php 
echo("<table cellspacing=\"2\" cellpadding=\"2\">");
echo("<tr>");
echo("<td class=\"soustitre3\"><p><b>Code</b></td>");
echo("<td class=\"soustitre3\"><p><b>Nouveauté</b></td>");
echo("<td class=\"soustitre2\"><p><b>Domaine</b></td>");
echo("<td class=\"soustitre2\"><p><b>Nom</b></td>");
echo("<td class=\"soustitre2\"><p><b>Lien</b></td>");
echo("<td class=\"soustitre2\"><p><b>Etat</b></td>");
echo("<td class=\"soustitre2\"><p><b>Priorité</b></td>");
echo("<td class=\"soustitre2\"><p><b>Commentaire</b></td>");
echo("<td class=\"soustitre2\"><p><b>Avancement</b></td>");
echo("<td></td>");
echo("</tr>");
$req_faq = "select * from idees order by idee_etat ";
$db->query($req_faq);
while($db->next_record())
{
	echo '<tr>
	<td class="soustitre3">' . $db->f("idee_cod") . '</td>
	<td class="soustitre3">' . $db->f("idee_new") . '</td>	
	<td class="soustitre3">' . $db->f("idee_domaine") . '</td>
	<td class="soustitre3">' . $db->f("idee_nom") . '</td>
	<td class="soustitre3"><a href="' . $db->f("idee_lien") . '">' . $db->f("idee_lien") . '</a></td>
	<td class="soustitre3">' . $db->f("idee_etat") . '</td>
	<td class="soustitre3">' . $db->f("idee_priorite") . '</td>
		<td class="soustitre3">' . $db->f("idee_comment") . '</td>
	<td class="soustitre3">' . $db->f("idee_avancement") . '</td>
	<td><a href="modif_idee.php?idee=' . $db->f('idee_cod') . '">Modifier</a></td>
	<td><a href="efface_idee.php?idee=' . $db->f('idee_cod') . '">Effacer</a></td>
	</tr>';
		
	
}
echo("</table>");
echo("</form>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

