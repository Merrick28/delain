<?php 
include "includes/classes.php";
$db = new base_delain;
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php include "jeu_test/tab_haut.php";
$limite = 20;
if (!isset($debut))
{
	$debut = 0;
}
if (!isset($recherche))
{
	$recherche = 0;
}
if (!preg_match('/^[0-9]*$/i', $debut))
{
	echo "<p>Anomalie sur Offset !";
	exit();
}

$req_joueur = "select count(perso_cod) as nb from perso where perso_type_perso = 1 and perso_actif = 'O' ";
$db->query($req_joueur);
$db->next_record();
$nb_joueur = $db->f("nb");

$nb_pages = ceil($nb_joueur/20);

$req_joueur = "select lower(perso_nom) as minusc,perso_cod,perso_nom,perso_nb_joueur_tue,perso_nb_monstre_tue,perso_nb_mort,get_renommee(perso_renommee) as renommee,get_karma(perso_kharma)as karma,perso_renommee,perso_kharma,get_renommee_magie(perso_renommee_magie) as renommee_magie,perso_renommee_magie,perso_nb_joueur_tue_arene,perso_nb_mort_arene ";
$req_joueur = $req_joueur . "from perso ";
$req_joueur = $req_joueur . "where perso_actif = 'O' ";
$req_joueur = $req_joueur . "and perso_type_perso = 1 ";
$req_joueur = $req_joueur . "and perso_cod not in (1,2,3) and perso_pnj != 1";
if (!isset($sort))
{
	$sort = 'nom';
	$sens = 'asc';
	$nv_sens = 'desc';
}
if (!isset($sens))
{
	$sens = 'asc';
}
if (isset($_POST['autresens']))
    $autresens = $_POST['autresens'];

if (!isset($autresens))
{
	$autresens = 'desc';
}
if (($sens != 'desc') && ($sens != 'asc'))
{
	echo "<p>Anomalie sur sens !";
	exit();
}
if (($sort != 'code') && ($sort != 'nom') && ($sort != 'renommee') && ($sort != 'karma') && ($sort != 'monstre') && ($sort != 'joueur') && ($sort != 'mort') && ($sort != 'magie') && ($sort != 'joueurArene') && ($sort != 'mortArene'))
{
	echo "<p>Anomalie sur tri !";
	exit();
}
if ($sort == 'code')
{
	$req_joueur = $req_joueur . "order by perso_cod $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'nom')
{
	$req_joueur = $req_joueur . "order by minusc $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'renommee')
{
	$req_joueur = $req_joueur . "order by perso_renommee $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'karma')
{
	$req_joueur = $req_joueur . "order by perso_kharma $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'monstre')
{
	$req_joueur = $req_joueur . "order by perso_nb_monstre_tue $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}

}
if ($sort == 'joueur')
{
	$req_joueur = $req_joueur . "order by perso_nb_joueur_tue  $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'mort')
{
	$req_joueur = $req_joueur . "order by perso_nb_mort $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'joueurArene')
{
	$req_joueur = $req_joueur . "order by perso_nb_joueur_tue_arene  $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'mortArene')
{
	$req_joueur = $req_joueur . "order by perso_nb_mort_arene $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
if ($sort == 'magie')
{
	$req_joueur = $req_joueur . "order by perso_renommee_magie $sens";
	if ($sens == 'desc')
	{
		$sens = 'asc';
		$autresens = 'desc';
	}
	else
	{
		$sens = 'desc';
		$autresens = 'asc';
	}
}
$req_joueur = $req_joueur . " offset $debut ";
$req_joueur = $req_joueur . "limit $limite ";
$db->query($req_joueur);

//$res_joueur = pg_exec($dbconnect,$req_joueur);

$nb_joueur = $db->nf();
?>

<form name="fsort" method="post" action="classement.php">
<input type="hidden" name="debut">
<input type="hidden" name="sort">
<input type="hidden" name="sens" value="<?php  echo $sens; ?>">
<input type="hidden" name="autresens">
<input type="hidden" name="visu">
<table>
<tr>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='nom';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
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
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='renommee';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
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
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='magie';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
if ($sort == 'mort')
{
	echo("<b>");
}
echo("Renommée magique");
if ($sort == 'mort')
{
	echo("</b>");
}
echo("</a></td>");
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='karma';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
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
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='monstre';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
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
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='joueur';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
if ($sort == 'joueur')
{
	echo("<b>");
}
echo("Nombre d'aventuriers tués");
if ($sort == 'joueur')
{
	echo("</b>");
}
echo("</a></td>");
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='mort';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
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
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='joueurArene';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
if ($sort == 'joueurArene')
{
	echo("<b>");
}
echo("Nombre d'aventuriers tués en arène");
if ($sort == 'joueur')
{
	echo("</b>");
}
echo("</a></td>");
?>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value='mortArene';document.fsort.sens.value='<?php  echo $sens; ?>';document.fsort.debut.value=0;document.fsort.submit();">
<?php 
if ($sort == 'mort')
{
	echo("<b>");
}
echo("Nombre de morts en arène");
if ($sort == 'mortArene')
{
	echo("</b>");
}
echo("</a></td>");



echo("</tr>");

while($db->next_record())
{
	echo("<tr>");
	echo "<td class=\"soustitre2\"><p><b><a href=\"javascript:document.fsort.action='jeu/visu_desc_perso_hc.php';document.fsort.visu.value=" , $db->f("perso_cod") , ";document.fsort.submit();\">" , $db->f("perso_nom") , "</a></b></p></td>";
	printf("<td><p>%s</td>",$db->f("renommee"));
	printf("<td><p>%s</td>",$db->f("renommee_magie"));
	printf("<td><p>%s</td>",$db->f("karma"));
	printf("<td><p>%s</td>",$db->f("perso_nb_monstre_tue"));
	printf("<td><p>%s</td>",$db->f("perso_nb_joueur_tue"));
	printf("<td><p>%s</td>",$db->f("perso_nb_mort"));
	printf("<td><p>%s</td>",$db->f("perso_nb_joueur_tue_arene"));
	printf("<td><p>%s</td>",$db->f("perso_nb_mort_arene"));
	echo("</tr>");	
	
	
}
echo("<tr><td>");
if ($debut != 0)
{
	echo("<p><a href=\"javascript:document.fsort.debut.value=$debut-$limite;document.fsort.sens.value='$autresens';document.fsort.sort.value='$sort';document.fsort.submit();\"><== précédent</a>");
}
echo("</td>");
echo("<td colspan=\"5\"></td>");
echo("<td><p><a href=\"javascript:document.fsort.debut.value=$debut+$limite;document.fsort.sens.value='$autresens';document.fsort.sort.value='$sort';document.fsort.submit();\">suivant ==></a></td>");
echo("</tr>");

echo("</table>");
echo("</form>");
echo "<p>";
$page_en_cours = ($debut/20) + 1;
for($cpt=1;$cpt<=$nb_pages;$cpt++)
{
	$v_debut = ($cpt-1)*20;
	if ($cpt == 1)
	{
		if ($cpt != $page_en_cours)
		{
			echo "<a href=\"classement.php?debut=$v_debut\">1</a>";
		}
		else
		{
			echo "<b>1</b>";
		} 	
		
	}
	if (($cpt == $page_en_cours) && ($cpt != 1) && ($cpt != $nb_pages))
	{
		echo " ... <b>$page_en_cours</b>";
	}
	if (($cpt%10 == 0) && ($cpt != $page_en_cours))
	{
		echo " ... <a href=\"classement.php?debut=$v_debut\">$cpt</a>";
	}
	if (($cpt == $nb_pages) && ($cpt != $page_en_cours) )
	{
		echo " ... <a href=\"classement.php?debut=$v_debut\">$cpt</a>";
	}
	
}
echo "<p style=\"text-align:center;\"><a href=\"rech_class.php\">Faire une recherche !</a>";
include "jeu_test/tab_bas.php";
?>
</body>
</html>
