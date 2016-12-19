<?php 
//include "../connexion.php";
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

$is_guilde = $db->is_in_guilde($perso_cod);

$req_guilde = "select guilde_cod,guilde_nom,guilde_description,count(pguilde_perso_cod) as nb_perso,
		sum(perso_nb_joueur_tue) as tot_perso_tue,sum(perso_nb_monstre_tue) as tot_monstre_tue,
		sum(perso_nb_mort) as tot_nb_mort,get_renommee_guilde(guilde_cod) as renommee,get_karma_guilde(guilde_cod) as karma,
		get_renommee_guilde_moy(guilde_cod),get_karma_guilde_moy(guilde_cod) 
	from guilde,guilde_perso,perso 
	where pguilde_valide = 'O' 
		and pguilde_guilde_cod = guilde_cod 
		and pguilde_perso_cod = perso_cod 
		and perso_actif != 'N' 
	group by guilde_cod,guilde_nom,guilde_description ";


if (!isset($sort))
{
	$sort = 'code';
	$sens = 'desc';
	$nv_sens = 'desc';
}
if (!isset($sens))
{
	$sens = 'desc';
}
if ($sort == 'code')
{
    $req_guilde = $req_guilde . "order by guilde_cod $sens";
}
if ($sort == 'nom')
{
	$req_guilde = $req_guilde . "order by guilde_nom $sens";
}
if ($sort == 'nbre')
{
	$req_guilde = $req_guilde . "order by count(pguilde_perso_cod) $sens ";
}
if ($sort == 'renommee')
{
	$req_guilde = $req_guilde . "order by get_renommee_guilde_moy(guilde_cod) $sens";
}
if ($sort == 'karma')
{
	$req_guilde = $req_guilde . "order by get_karma_guilde_moy(guilde_cod) $sens";
}
if ($sort == 'monstre')
{
	$req_guilde = $req_guilde . "order by sum(perso_nb_monstre_tue) $sens";
}
if ($sort == 'joueur')
{
	$req_guilde = $req_guilde . "order by sum(perso_nb_joueur_tue)  $sens";
}
if ($sort == 'mort')
{
	$req_guilde = $req_guilde . "order by sum(perso_nb_mort)  $sens";
}

if ($sens == 'desc')
{
	$sens = 'asc';
}
else
{
	$sens = 'desc';
}

$db = new base_delain;
$db->query($req_guilde);

if ($is_guilde === false)
{
	?>
	<p><i>Attention ! Toute demande d’affiliation à une guilde supprimera automatiquement les demandes qui sont en attente de validation pour les autres guildes !</i></p>
	<p align="center"><br><br><b>AVANT DE POSTULER A UNE GUILDE, MERCI D’EN LIRE SA DESCRIPTION. 
	<br>Vous risqueriez d’être mal reçu si tel n’était pas le cas ...</b></p><br><br>
	
	<p>Guildes disponibles : 
	<?php 
}
?>

<table>
<form name="fsort" method="post" action="voir_toutes_guildes.php">
<input type="hidden" name="sort">
<input type="hidden" name="sens" value="$sens">
<tr>
<?php 
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nom';document.fsort.sens.value='$sens';document.fsort.submit();\">");
if ($sort == 'nom')
{
	echo("<b>");
}
echo("Nom");
if ($sort == 'nom')
{
	echo("</b>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nbre';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'nbre')
{
	echo("<b>");
}
echo("Nombre d’inscrits");
if ($sort == 'nbre')
{
	echo("</b>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='renommee';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'renommee')
{
	echo("<b>");
}
echo("Renommee");
if ($sort == 'renommee')
{
	echo("</b>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='karma';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'karma')
{
	echo("<b>");
}
echo("Karma");
if ($sort == 'karma')
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
echo("</tr>");
echo("</form>");

echo("<form name=\"guilde\" method=\"post\">");
echo("<input type=\"hidden\" name=\"num_guilde\">");
while($db->next_record())
{
	echo("<tr>");
	printf("<td class=\"soustitre2\"><p><b><a href=\"javascript:document.guilde.action='visu_guilde.php';document.guilde.num_guilde.value=%s;document.guilde.submit();\">%s</a></b></p></td>",$db->f("guilde_cod"),$db->f("guilde_nom"));
	printf("<td><p>%s</td>",$db->f("nb_perso"));
	printf("<td><p>%s</td>",$db->f("renommee"));
	printf("<td><p>%s</td>",$db->f("karma"));
	printf("<td><p>%s</td>",$db->f("tot_monstre_tue"));
	printf("<td><p>%s</td>",$db->f("tot_perso_tue"));
	printf("<td><p>%s</td>",$db->f("tot_nb_mort"));

    if ($is_guilde === false)
    {
    		printf("<td><a href=\"javascript:document.guilde.action='valide_join_guilde.php';document.guilde.num_guilde.value=%s;document.guilde.submit();\">S’inscrire !</a></td>",$db->f("guilde_cod"));
    }

	echo("</tr>");	
	
}

echo("</table>");
echo("</form>");
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
