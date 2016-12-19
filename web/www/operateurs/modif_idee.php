<?php 
include "../includes/classes.php";
$db = new base_delain;
$db2 = new base_delain;
?>
<html>
<head>
<title>Les souterrains de Delain : partie opérateurs</title>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
</head>
<body background="../images/fond5.gif">
<?php 

$req_tot = "select idee_etat from idees group BY idee_etat ";
$db2->query($req_tot);
$req = 'select * from idees where idee_cod = ' . $idee;
$db->query($req);
$db->next_record();
$user = $_SERVER["REMOTE_USER"];
include "../jeu/tab_haut.php";
echo("<table>");
echo("<form name=\"ajout_faq\" method=\"post\" action=\"valide_modif_idee.php\">");
echo '<input type="hidden" name="idee" value="' . $idee . '">';

echo("<td class=\"soustitre3\"><p>Priorité :</td>");
?>
<td><p><select  name="nouveau">
				<OPTION value ="" <?php  if ($db->f("idee_new") == ""){echo "selected";}	?>><-- idée ancienne --></OPTION>
				<option value ="new" <?php  if ($db->f("idee_new") == 0){echo "selected";}	?>>Nouveauté</option>
<?php 					echo "</select></td>";
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Domaine :</td>");
?>
<td><p><select  name="domaine">
				<OPTION value =""><-- Sélectionner un domaine --></OPTION>
				<OPTION value ="Magie"<?php  if ($db->f("idee_domaine") == "Magie"){echo "selected";}	?>>Magie</OPTION>
				<option value ="Combat"<?php  if ($db->f("idee_domaine") == "Combat"){echo "selected";}	?>>Combat</option>
				<option value ="Modification de l'interface" <?php  if ($db->f("idee_domaine") == "Modification de l'interface"){echo "selected";}	?>>Modification de l'interface</option>
				<option value ="Religion" <?php  if ($db->f("idee_domaine") == "Religion"){echo "selected";}	?>>Religion</option>							
				<option value ="Quête" <?php  if ($db->f("idee_domaine") == "Quête"){echo "selected";}	?>>Quête</option>
				<option value ="Nouveau concept ou modification d'un concept du jeu" <?php  if ($db->f("idee_domaine") == "Nouveau concept ou modification d'un concept du jeu"){echo "selected";}	?>>Nouveau concept ou modification d'un concept du jeu</option>					
<?php 					echo "</select></td>";
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Nom :</td>");
echo("<td><p><textarea name=\"nom\" cols=\"40\" rows=\"10\">" . $db->f("idee_nom"). "</textarea></td>");
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Lien :</td>");
echo("<td><p><textarea name=\"lien\" cols=\"40\" rows=\"10\">" . $db->f("idee_lien"). "</textarea></td>");
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Priorité :</td>");
?>
<td><p><select  name="priorite">
				<OPTION value =""><-- Priorité --></OPTION>
				<option value ="0" <?php  if ($db->f("idee_priorite") == 0){echo "selected";}	?>>Priorité 0</option>
				<option value ="1" <?php  if ($db->f("idee_priorite") == 1){echo "selected";}	?>>Priorité 1</option>
				<option value ="2" <?php  if ($db->f("idee_priorite") == 2){echo "selected";}	?>>Priorité 2</option>
				<option value ="3" <?php  if ($db->f("idee_priorite") == 3){echo "selected";}	?>>Priorité 3</option>
<?php 					echo "</select></td>";
echo("</tr>");
echo("<tr>");
echo("<td class=\"soustitre3\"><p>Etat :</td>");
echo("<td>					<select name=\"etat\" >
					<OPTION value =\"\"><-- Toutes les idées --></OPTION>");
						while($db2->next_record())
						{
							$sel = "";
							$etat = $db2->f("idee_etat");
							if ($etat == $db->f("idee_etat"))
							{
								$sel = "selected";
							}
							echo "<OPTION value='". $etat ."'\" $sel>". $etat ."</OPTION>\n";
						}
					echo "</select></td>";
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Commentaire :</td>");
echo("<td><p><textarea name=\"commentaire\" cols=\"40\" rows=\"10\">" . $db->f("idee_comment"). "</textarea></td>");
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre3\"><p>Avancement : <br><i>(en %)</i></td>");
echo("<td><p><textarea name=\"avancement\">" . $db->f("idee_avancement"). "</textarea></td>");
echo("</tr>");

echo("<tr>");
echo("<td colspan=\"2\"><center><input type=\"submit\" value=\"valider\" class=\"test\"></center></td>");
echo("</tr>");


echo("</table>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

