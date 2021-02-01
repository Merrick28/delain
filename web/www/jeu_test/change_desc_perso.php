<?php
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
echo("<form name=\"desc\" method=\"post\" action=\"valide_change_desc_perso.php\">");
echo '<input type="hidden" name="methode" value="desc">';
echo("<table><tr><td>");
echo("<textarea name=\"corps\" cols=\"40\" rows=\"5\">");

echo $perso->perso_description;
echo("</textarea>");
echo("</td></tr>");
echo("<tr><td><center><input type=\"submit\" value=\"Enregistrer cette description courte\" class=\"test\">
			<br>Limitation à 254 caractères</td></tr>");
echo("</table></form>");

echo("<form name=\"desc2\" method=\"post\" action=\"valide_change_desc_perso.php\">");
echo '<input type="hidden" name="methode" value="desc_long">';
echo("<table><tr><td>");
echo("<textarea name=\"corps\" cols=\"100\" rows=\"10\">");
echo $perso->perso_desc_long;
echo("</textarea>");
echo("</td></tr>");
echo("<tr><td><center><input type=\"submit\" value=\"Enregistrer cette description longue\" class=\"test\"></td>
			<br>Limitation à 10000 caractères</tr>");
echo("</table></form>");

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";