<?php 
include "../includes/classes.php";

$req = "select * from hits_voix";
$db->query($req);
$contenu_page .= '<table cellspacing="2"><tr><td class="titre">Page</td><td class="titre">Compteur</td></tr>';
while($db->next_record())
{
	$contenu_page .= '<tr><td class="soustitre2">' . $db->f("page") . '</td><td>' . $db->f("counter") . '</td></tr>';
}
$contenu_page .= '</table>';
$template     = $twig->load('page_generique.twig');
$options_twig = array(
	'CONTENU' => $contenu_page
);
echo $template->render(array_merge($options_twig_defaut, $options_twig));