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
$req_guilde = "select guilde_cod,guilde_nom,guilde_description,count(pguilde_perso_cod) as nb_perso,sum(perso_nb_joueur_tue) as nb_joueur_tue,sum(perso_nb_monstre_tue) as nb_monstre_tue,sum(perso_nb_mort) as nb_mort ";
$req_guilde = $req_guilde . "from guilde,guilde_perso,perso ";
$req_guilde = $req_guilde . "where pguilde_valide = 'O' ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and pguilde_perso_cod = perso_cod ";
$req_guilde = $req_guilde . "and perso_actif != 'N' ";
$req_guilde = $req_guilde . "and perso_type_perso = 1";
$req_guilde = $req_guilde . "group by guilde_cod,guilde_nom,guilde_description ";


if (!isset($sort))
{
	$sort = 'code';
	$sens = 'asc';
	$nv_sens = 'asc';
}
if (!isset($sens))
{
	$sens = 'desc';
}
if ($sort == 'code')
{
	$req_guilde = $req_guilde . "order by guilde_cod $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
	}
	else
	{
		$sens = 'desc';
	}
}
if ($sort == 'nom')
{
	$req_guilde = $req_guilde . "order by guilde_nom $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
	}
	else
	{
		$sens = 'desc';
	}
}
if ($sort == 'nbre')
{
	$req_guilde = $req_guilde . "order by count(pguilde_perso_cod) $sens ";
	if ($sens == 'desc')
	{
		$sens = 'asc';
	}
	else
	{
		$sens = 'desc';
	}
}
if ($sort == 'rep')
{
	$req_guilde = $req_guilde . "order by get_reputation_guilde_n(guilde_cod) $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
	}
	else
	{
		$sens = 'desc';
	}
}
if ($sort == 'monstre')
{
	$req_guilde = $req_guilde . "order by sum(perso_nb_monstre_tue) $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
	}
	else
	{
		$sens = 'desc';
	}

}
if ($sort == 'joueur')
{
	$req_guilde = $req_guilde . "order by sum(perso_nb_joueur_tue)  $sens";
	if ($sens = 'desc')
	{
		$sens == 'asc';
	}
	else
	{
		$sens = 'desc';
	}
}
if ($sort == 'mort')
{
	$req_guilde = $req_guilde . "order by sum(perso_nb_mort)  $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
	}
	else
	{
		$sens = 'desc';
	}

}
$db->query($req_guilde);
?>
<p><i>Attention ! Toute demande d'affiliation à une guilde supprimera automatiquement les demandes qui sont en attente de validation pour les autres guildes !</i>
<p>Guildes disponibles : 
<form name="fsort" method="post" action="join_guilde.php">
<input type="hidden" name="sort">
<input type="hidden" name="sens" value="$sens">
<input type="hidden" name="num_guilde">
<table>
<tr>
<?php 
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nom';document.fsort.sens.value='$sens';document.fsort.submit();\">");
if ($sort == 'nom')
{
	?>
	<b>
	<?php 
}
echo("Nom");
if ($sort == 'nom')
{
	?>
	</b>
	<?php 
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nbre';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'nbre')
{
	echo("<b>");
}
echo("Nombre d'inscrits");
if ($sort == 'nbre')
{
	echo("</b>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='monstre';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'monstre')
{
	echo("<b>");
}
echo("Nombre de monstres tués");
if ($sort == 'monstre')
{
	echo("</b>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='joueur';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'joueur')
{
	echo("<b>");
}
echo("Nombre de joueurs tués");
if ($sort == 'joueur')
{
	echo("</b>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='mort';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'mort')
{
	echo("<b>");
}
echo("Nombre de morts");
if ($sort == 'mort')
{
	echo("</b>");
}
echo("</a></td>");
echo("<td></td>");
echo("</tr>");

while($db->next_record())
{
	//$tab_guilde = pg_fetch_array($res_guilde,$cpt);
	echo("<tr>");
	printf("<td class=\"soustitre2\"><p><b><a href=\"javascript:document.fsort.action='visu_guilde.php';document.fsort.num_guilde.value=%s;document.fsort.submit();\">%s</a></b></p></td>",$db->f("guilde_cod"),$db->f("guilde_nom"));
	printf("<td><p>%s</td>",$db->f("nb_perso"));
	printf("<td><p>%s</td>",$db->f("nb_monstre_tue"));
	printf("<td><p>%s</td>",$db->f("nb_joueur_tue"));
	printf("<td><p>%s</td>",$db->f("nb_mort"));
	
	printf("<td><a href=\"javascript:document.fsort.action='valide_join_guilde.php';document.fsort.num_guilde.value=%s;document.fsort.submit();\">S'inscrire !</a></td>",$db->f("guilde_cod"));
	echo("</tr>");	
}
echo("</table>");
echo("</form>");
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
