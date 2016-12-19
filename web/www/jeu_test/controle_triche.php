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

if (!isset($evt_start))
	$evt_start = 0;

$req_evt = "select triche_perso_cod1, triche_perso_cod2, triche_cas_cod, triche_date, 
		p1.perso_nom as perso_nom1,
		c1.compt_nom as compt_nom1, c1.compt_cod as compt_cod1
	from triche
	inner join perso p1 on p1.perso_cod = triche_perso_cod1::integer
	inner join perso_compte pc1 on pc1.pcompt_perso_cod = p1.perso_cod
	inner join compte c1 on c1.compt_cod = pc1.pcompt_compt_cod
	order by triche_cod desc 
	limit 40 
	offset $evt_start"; 
$db->query($req_evt);
?>
<p> Cet affichage est très rudimentaire, les données stockées en base étant de toute nature, il est difficile de rendre les choses plus propres.</p>
<table cellspacing="2">
<tr>
<td colspan="4" class="titre"><p class="titre">Triches référencées</p></td>
</tr>
<?php 
echo "<tr>";
	echo "<td class=\"soustitre3\"><p><b>Date</b></p></td>";
	echo "<td class=\"soustitre3\"><p><b>Triche_perso_cod1</b></p></td>";
	echo "<td class=\"soustitre3\"><p><b>Triche_perso_cod2</b></p></td>";
	echo "<td class=\"soustitre3\"><p><b>Triche_cas_cod</b></p></td>";
echo "</tr>";
?>
<form name="visu_evt" method="post" action="multi_trace.php">
<input type="hidden" name="visu">
<?php 
while($db->next_record())
{	
	$triche_perso_cod1 = $db->f("triche_perso_cod1");
	$triche_perso_cod2 = $db->f("triche_perso_cod2");
	$triche_cas_cod = $db->f("triche_cas_cod");
	$triche_date = $db->f("triche_date");
	$perso_nom1 = $db->f("perso_nom1");
	$compt_nom1 = $db->f("compt_nom1");
	$compt_cod1 = $db->f("compt_cod1");
	echo "<tr>";
	echo "<td class=\"soustitre3\">$triche_date</td>";
	echo "<td class=\"soustitre3\">$perso_nom1 (n° $triche_perso_cod1, compte <a href=\"detail_compte.php?compte=$compt_cod1\">$compt_nom1</a>)</td>";
	echo "<td class=\"soustitre3\">$triche_perso_cod2</td>";
	echo "<td class=\"soustitre3\">$triche_cas_cod</td>";
	echo("</tr>");
}
?>
<tr>
<td>
</form>
<form name="evt" method="post" action="multi_trace.php">
<input type="hidden" name="evt_start">
<?php 
if ($evt_start != 0)
{
	echo("<div align=\"left\"><a href=\"javascript:document.evt.evt_start.value=$evt_start-40;document.evt.submit();\"><== Précédent</a></div>");
}
?></td><td></td><td></td>
<?php 
echo("<td><div align=\"right\"><a href=\"javascript:document.evt.evt_start.value=$evt_start+40;document.evt.submit();\">Suivant ==></a></div></td>");
?>
</tr>
</form>
</table>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
