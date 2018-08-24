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
ob_start();
// choix d'affichage
if (!isset($type))
	$type = 0;
$aff[0] = 'Tous les évènements';
$aff[1] = 'Attaques portées';
$aff[2] = 'Attaques reçues';
$aff[3] = 'Sorts';
$aff[4] = 'Améliorations et PX';
$aff[5] = 'Autres';
$nb_af = count($aff);
switch ($type)
{
	case 0:
		$restr = '';
		break;
	case 1:
		$restr = " and (levt_attaquant = $perso_cod or levt_attaquant is null) and levt_tevt_cod in (9, 10) ";
		break;
	case 2:
		$restr = " and levt_cible = $perso_cod and levt_tevt_cod in (8, 9, 10) ";
		break;
	case 3:
		$restr = " and levt_tevt_cod in (14, 28) ";
		break;
    case 4:
		$restr = " and levt_tevt_cod in (10, 11, 12, 18, 48, 63) ";
		break;
    case 5:
		$restr = " and levt_tevt_cod not in (8, 9, 10, 11, 12, 14, 18, 28, 48, 63) ";
		break;
}
if (!isset($evt_start))
{
	$evt_start = 0;
}
if ($evt_start < 0)
{
	$evt_start = 0;
}
$req_evt = "select levt_cod,to_char(levt_date,'DD/MM/YYYY hh24:mi:ss') as evt_date,tevt_libelle,levt_texte,levt_perso_cod1,levt_attaquant,levt_cible, 
att.perso_nom as attaquant, def.perso_nom as cible, soi.perso_nom as soimeme
	from ligne_evt
	inner join type_evt ON levt_tevt_cod = tevt_cod
	inner join perso soi ON levt_perso_cod1 = soi.perso_cod
	left outer join perso att ON levt_attaquant = att.perso_cod
	left outer join perso def ON levt_cible = def.perso_cod
	where levt_perso_cod1 = $perso_cod
	$restr
	order by levt_date desc,levt_cod desc
	limit 20
	offset $evt_start ";
$db->query($req_evt);
?>
<form name="visu_evt" method="post" action="visu_evt_perso.php">
<input type="hidden" name="visu">
<table cellspacing="0" cellpadding="0">
<tr>
<td colspan="3" class="titre">Événements</td>
</tr>
<tr>
<td colspan="3">
	<table cellspacing="0" cellpadding="0" width="100%">
	<?php 
		for($cpt=0; $cpt < $nb_af; $cpt++)
		{
			if($cpt == $type)
			{
				$style = 'onglet';
			}
			else
			{
				$style = 'pas_onglet';
			}
			echo '<td class="' . $style .'" style="text-align:center"><a href="' . $PHP_SELF . '?type=' . $cpt . '">' . $aff[$cpt] . '</a></td>';
		}
	?>
	</table>
</td>
</tr>
<tr><td colspan="3" class="reste_onglet"><table>


<?php 
while($db->next_record())
{
	echo("<tr>");
	printf("<td class=\"soustitre3\" style=\"white-space:nowrap;\">%s</td>",$db->f("evt_date"));
	printf("<td class=\"soustitre3\"><b>%s</b></td>",$db->f("tevt_libelle"));

	$texte_evt = str_replace('[perso_cod1]',"<b><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_perso_cod1") . ";document.visu_evt.submit();\">". $db->f("soimeme") ."</a></b>",$db->f("levt_texte"));
	if ($db->f("levt_attaquant") != '')
	{
		$texte_evt = str_replace('[attaquant]',"<b><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_attaquant") . ";document.visu_evt.submit();\">".$db->f("attaquant")."</a></b>",$texte_evt);
	}
	if ($db->f("levt_cible") != '')
	{
		$texte_evt = str_replace('[cible]',"<b><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_cible") . ";document.visu_evt.submit();\">".$db->f("cible")."</a></b>",$texte_evt);
	}

	echo("<td>$texte_evt</td>");
	echo("</tr>");
}
?>
</table></td></tr>
<tr>
<td>
</form>
<tr><td colspan="2">
<?php 
if ($evt_start != 0)
{
	$start = $evt_start - 20;
	echo "<div align=\"left\"><a href=\"" , $PHP_SELF , "?evt_start=" , $start , "&type=" , $type , "\"><== Précédent</a></div>";
}
?>
</td><td>
<?php 
$start = $evt_start + 20;
echo "<div align=\"right\"><a href=\"" , $PHP_SELF , "?evt_start=" , $start , "&type=" , $type , "\">Suivant ==></a></div>";
?>
</td></tr>
</form>
</table>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
