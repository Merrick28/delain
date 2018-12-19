<?php
include "blocks/_header_page_jeu.php";
ob_start();

$req = "select dcompt_etage from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	die("Erreur sur les étages possibles !");
}
else
{
	$db->next_record();
	$droit['etage'] = $db->f("dcompt_etage");
}
if ($droit['etage'] == 'A')
{
	$restrict = '';
	$restrict2 = '';
}
else
{
	$restrict = 'where etage_numero in (' . $droit['etage'] . ') ';
	$restrict2 = 'and pos_etage in (' . $droit['etage'] . ') ';
}

$req = "select etage_libelle,etage_numero,etage_reference from etage " . $restrict . "order by etage_reference desc, etage_numero asc";
$db->query($req);
echo("<p>");
while($db->next_record())
{
	$bold = ($db->f("etage_numero") == $db->f("etage_reference"));
	echo ($bold?'<p /><strong>':'')."<a href=\"tab_vue_total.php?num_etage=" . $db->f("etage_numero") . '">' . $db->f("etage_libelle") . "</a>".($bold?'</strong>':'')."<br />";
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

