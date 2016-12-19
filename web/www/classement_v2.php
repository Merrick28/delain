<?php 
include "classes.php";
include 'includes/template.inc';
$t = new template;
$t->set_file('FileRef','template/delain/index.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
//
// identification
//
ob_start();
include G_CHE . "ident.php";
$ident = montre_formulaire_connexion($verif_auth, ob_get_contents());
ob_end_clean();
$t->set_var("IDENT",$ident);

//
//Contenu de la div de droite
//
$contenu_page = '';
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
	$contenu_page .= "<p>Anomalie sur Offset !";
	exit();
}

$req_joueur = "select count(perso_cod) as nb from perso where perso_type_perso = 1 and perso_actif = 'O' ";
$db->query($req_joueur);
$db->next_record();
$nb_joueur = $db->f("nb");

$nb_pages = ceil($nb_joueur/20);

$req_joueur = "select lower(perso_nom) as minusc,perso_cod,perso_nom,perso_nb_joueur_tue,perso_nb_monstre_tue,perso_nb_mort,get_renommee(perso_renommee) as renommee,get_karma(perso_kharma)as karma,perso_renommee,perso_kharma,get_renommee_magie(perso_renommee_magie) as renommee_magie,perso_renommee_magie,perso_nb_joueur_tue_arene,perso_nb_mort_arene, get_renommee_artisanat(perso_renommee_artisanat) as renommee_artisanat ";
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
$autresens = (isset($_POST['autresens'])) ? $_POST['autresens'] : 'desc';

if (($sens != 'desc') && ($sens != 'asc'))
{
	$contenu_page .= "<p>Anomalie sur sens !";
	exit();
}
if (($sort != 'code') && ($sort != 'nom') && ($sort != 'renommee') && ($sort != 'karma') && ($sort != 'monstre') && ($sort != 'joueur') && ($sort != 'mort') && ($sort != 'magie') && ($sort != 'joueurArene') && ($sort != 'mortArene') && ($sort != 'artisanat'))
{
	$contenu_page .= "<p>Anomalie sur tri ! sort=$sort</p>";
	exit();
}
switch ($sort)
{
	case 'code':
		$req_joueur = $req_joueur . "order by perso_cod $sens";
		break;
	case 'nom':
		$req_joueur = $req_joueur . "order by minusc $sens";
		break;
	case 'renommee':
		$req_joueur = $req_joueur . "order by perso_renommee $sens";
		break;
	case 'karma':
		$req_joueur = $req_joueur . "order by perso_kharma $sens";
		break;
	case 'monstre':
		$req_joueur = $req_joueur . "order by perso_nb_monstre_tue $sens";
		break;
	case 'joueur':
		$req_joueur = $req_joueur . "order by perso_nb_joueur_tue $sens";
		break;
	case 'mort':
		$req_joueur = $req_joueur . "order by perso_nb_mort $sens";
		break;
	case 'joueurArene':
		$req_joueur = $req_joueur . "order by perso_nb_joueur_tue_arene $sens";
		break;
	case 'mortArene':
		$req_joueur = $req_joueur . "order by perso_nb_mort_arene $sens";
		break;
	case 'magie':
		$req_joueur = $req_joueur . "order by perso_renommee_magie $sens";
		break;
	case 'artisanat':
		$req_joueur = $req_joueur . "order by perso_renommee_artisanat $sens";
		break;
}
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

$req_joueur = $req_joueur . " offset $debut ";
$req_joueur = $req_joueur . "limit $limite ";
$db->query($req_joueur);

//$res_joueur = pg_exec($dbconnect,$req_joueur);

$nb_joueur = $db->nf();
$contenu_page .= '
<form name="fsort" method="post" action="' . $PHP_SELF . '">
<input type="hidden" name="debut">
<input type="hidden" name="sort">
<input type="hidden" name="sens" value="' . $sens. '">
<input type="hidden" name="autresens">
<input type="hidden" name="visu">
<table>
<tr>
<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'nom\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'nom')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Nom";
if ($sort == 'nom')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";
$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'renommee\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'renommee')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Renommée";
if ($sort == 'renommee')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";

$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'magie\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'magie')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Renommée magique";
if ($sort == 'magie')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";

$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'artisanat\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'artisanat')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Renommée artisanale";
if ($sort == 'artisanat')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";

$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'karma\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'karma')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Karma";
if ($sort == 'karma')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";
$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'monstre\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'monstre')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Nombre de monstres tués";
if ($sort == 'monstre')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";
$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'joueur\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'joueur')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Nombre d'aventuriers tués";
if ($sort == 'joueur')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";
$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'mort\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'mort')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Nombre de morts";
if ($sort == 'mort')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";
$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'joueurArene\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'joueurArene')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Nombre d'aventuriers tués en arène";
if ($sort == 'joueurArene')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";
$contenu_page .= '<td class="soustitre2"><p><a href="javascript:document.fsort.sort.value=\'mortArene\';document.fsort.sens.value=\'' .  $sens . '\';document.fsort.debut.value=0;document.fsort.submit();">';
if ($sort == 'mortArene')
{
	$contenu_page .= "<b>";
}
$contenu_page .= "Nombre de morts en arène";
if ($sort == 'mortArene')
{
	$contenu_page .= "</b>";
}
$contenu_page .= "</a></td>";



$contenu_page .= "</tr>";

while($db->next_record())
{
	$contenu_page .=("<tr>");
	$contenu_page .= "<td class=\"soustitre2\"><b><a href=\"javascript:document.fsort.action='jeu_test/visu_desc_perso_hc.php';document.fsort.visu.value=" . $db->f("perso_cod") . ";document.fsort.submit();\">" .$db->f("perso_nom") . "</a></b></td>";
	$contenu_page .= "<td>" . $db->f("renommee") . "</td>";
	$contenu_page .= "<td>" . $db->f("renommee_magie") . "</td>";
	$contenu_page .= "<td>" . $db->f("renommee_artisanat") . "</td>";
	$contenu_page .= "<td>" . $db->f("karma") . "</td>";
	$contenu_page .= "<td>" . $db->f("perso_nb_monstre_tue") . "</td>";
	$contenu_page .= "<td>" . $db->f("perso_nb_joueur_tue") . "</td>";
	$contenu_page .= "<td>" . $db->f("perso_nb_mort") . "</td>";
	$contenu_page .= "<td>" . $db->f("perso_nb_joueur_tue_arene") . "</td>";
	$contenu_page .= "<td>" . $db->f("perso_nb_mort_arene") . "</td>";
	$contenu_page .= "</tr>";	
}
$contenu_page .=("<tr><td>");
if ($debut != 0)
{
	$contenu_page .=("<p><a href=\"javascript:document.fsort.debut.value=$debut-$limite;document.fsort.sens.value='$autresens';document.fsort.sort.value='$sort';document.fsort.submit();\"><== précédent</a>");
}
$contenu_page .=("</td>");
$contenu_page .=("<td colspan=\"5\"></td>");
$contenu_page .=("<td><p><a href=\"javascript:document.fsort.debut.value=$debut+$limite;document.fsort.sens.value='$autresens';document.fsort.sort.value='$sort';document.fsort.submit();\">suivant ==></a></td>");
$contenu_page .=("</tr>");

$contenu_page .=("</table>");
$contenu_page .=("</form>");
$contenu_page .= "<p>";
$page_en_cours = ($debut/20) + 1;
for($cpt=1;$cpt<=$nb_pages;$cpt++)
{
	$v_debut = ($cpt-1)*20;
	if ($cpt == 1)
	{
		if ($cpt != $page_en_cours)
		{
			$contenu_page .= "<a href=\"classement_v2.php?debut=$v_debut&sort=$sort&sens=$autresens\">1</a>";
		}
		else
		{
			$contenu_page .= "<b>1</b>";
		} 	
		
	}
	if (($cpt == $page_en_cours) && ($cpt != 1) && ($cpt != $nb_pages))
	{
		$contenu_page .= " ... <b>$page_en_cours</b>";
	}
	if (($cpt%10 == 0) && ($cpt != $page_en_cours))
	{
		$contenu_page .= " ... <a href=\"classement_v2.php?debut=$v_debut&sort=$sort&sens=$autresens\">$cpt</a>";
	}
	if (($cpt == $nb_pages) && ($cpt != $page_en_cours) )
	{
		$contenu_page .= " ... <a href=\"classement_v2.php?debut=$v_debut&sort=$sort&sens=$autresens\">$cpt</a>";
	}
	
}
$contenu_page .= "<p style=\"text-align:center;\"><a href=\"rech_class.php\">Faire une recherche !</a> - <a href=\"classement_monstres.php\">Classement des monstres</a></p>";
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
