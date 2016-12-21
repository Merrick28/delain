<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$req_desc = "select perso_description,perso_desc_long from perso where perso_cod = $perso_cod ";
$db->query($req_desc);
$db->next_record();
echo("<form name=\"desc\" method=\"post\" action=\"valide_change_desc_perso.php\">");
echo '<input type="hidden" name="methode" value="desc">';
echo("<table><tr><td>");
echo("<textarea name=\"corps\" cols=\"40\" rows=\"5\">");
$desc = str_replace(chr(128),";",$db->f("perso_description"));
$desc = str_replace(chr(127),";",$desc);
echo $desc;
echo("</textarea>");
echo("</td></tr>");
echo("<tr><td><center><input type=\"submit\" value=\"Enregistrer cette description courte\" class=\"test\">
			<br>Limitation à 254 caractères</td></tr>");
echo("</table></form>");

echo("<form name=\"desc2\" method=\"post\" action=\"valide_change_desc_perso.php\">");
echo '<input type="hidden" name="methode" value="desc_long">';
echo("<table><tr><td>");
echo("<textarea name=\"corps\" cols=\"100\" rows=\"10\">");
$desc = str_replace(chr(128),";",$db->f("perso_desc_long"));
$desc = str_replace(chr(127),";",$desc);
echo $desc;
echo("</textarea>");
echo("</td></tr>");
echo("<tr><td><center><input type=\"submit\" value=\"Enregistrer cette description longue\" class=\"test\"></td>
			<br>Limitation à 10000 caractères</tr>");
echo("</table></form>");

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');