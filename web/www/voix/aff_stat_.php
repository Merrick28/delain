<?php 
include "../includes/classes.php";
include "../includes/template.inc";
$t = new template;
$t->set_file("FileRef","../template/classic/general.tpl");
// chemins
$t->set_var("g_url",G_URL);
$t->set_var("g_che",G_CHE);
$t->set_var("img_path",G_IMAGES);
$db = new base_delain;
$req = "select * from hits_voix";
$db->query($req);
$contenu_page .= '<table cellspacing="2"><tr><td class="titre">Page</td><td class="titre">Compteur</td></tr>';
while($db->next_record())
{
	$contenu_page .= '<tr><td class="soustitre2">' . $db->f("page") . '</td><td>' . $db->f("counter") . '</td></tr>';
}
$contenu_page .= '</table>';
$t->set_var("contenu_page",$contenu_page);
$t->parse("Sortie","FileRef");
//echo $test;
$t->p("Sortie");
?>
