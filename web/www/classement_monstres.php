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
$limite = 30;

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

function gras($crit1, $crit2, $debut)
{
	$resultat = ($debut) ? '<b>' : '</b>';
	return ($crit1 == $crit2) ? $resultat : '';
}

$contenu_page .= '<h1>Classement des monstres joués par des joueurs</h1>
	<p>Ce classement ne prend en compte que les gains effectués par un monstre alors qu’il était joué par un joueur.</p>
	<p>Seuls les monstres qui ne sont plus joués y sont comptabilisés (morts, relachés ou ayant servi pour la durée maximale autorisée...).</p>';

if (isset($compt_cod) && $compt_cod)
	$contenu_page .= '<p><a href="classement_monstres.php?debut=0&sort=compte&sens=asc">Voir les miens !</a></p>';
else
    $compt_cod = -1;

$contenu_page .= '
	<script type="text/javascript">
		function trier(critere, sens)
		{
			document.fsort.sort.value = critere;
			document.fsort.sens.value = sens;
			document.fsort.debut.value = 0;
			document.fsort.submit();
		}
	</script>
';

$req_monstre = "select count(cmon_cod) as nb from compte_monstre_historique";
$db->query($req_monstre);
$db->next_record();
$nb_monstre = $db->f("nb");

$nb_pages = ceil($nb_monstre / 30);

$req_monstre = "select cmon_compt_cod, cmon_nom, 
	trim(to_char(cmon_renommee,'999999990.9')) as cmon_renommee_txt, 
	trim(to_char(cmon_renommee_magique,'999999990.9')) as cmon_renommee_magique_txt, 
	trim(to_char(cmon_karma,'999999990.9')) as cmon_karma_txt, 
	cmon_kill_perso, cmon_kill_monstres, 
	trim(to_char(cmon_px,'999999990.9')) as cmon_px_txt, 
	cmon_description, 
	case cmon_fin when 0 then 'Survivant' when 1 then 'Tué dans l’exercice du devoir' when 2 then 'Lâchement abandonné' end as cmon_fin
	from compte_monstre_historique";
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
else
	$autresens = 'desc';

if (($sens != 'desc') && ($sens != 'asc'))
{
	$contenu_page .= "<p>Anomalie sur sens !";
	exit();
}
$lesTris = array(
	'nom' => 'cmon_nom',
	'renommée' => 'cmon_renommee',
	'renommée_magique' => 'cmon_renommee_magique',
	'karma' => 'cmon_karma',
	'tue_perso' => 'cmon_kill_perso',
	'tue_monstre' => 'cmon_kill_monstres',
	'px' => 'cmon_px',
	'description' => 'cmon_description',
	'fin' => 'cmon_fin',
	'compte' => "case when cmon_compt_cod = $compt_cod then 0 else 1 end"
	);
	
if (!isset($lesTris[$sort]))
{
	$contenu_page .= "<p>Anomalie sur tri ! sort=$sort</p>";
	exit();
}
$req_monstre .= " order by " . $lesTris[$sort] . " $sens";

$autresens = $sens;
$sens = ($sens == 'desc') ? 'asc' : 'desc';

$req_monstre = $req_monstre . " offset $debut ";
$req_monstre = $req_monstre . "limit $limite ";
$db->query($req_monstre);

$nb_monstre = $db->nf();
$contenu_page .= "
<form name='fsort' method='post' action='$PHP_SELF'>
<input type='hidden' name='debut'>
<input type='hidden' name='sort'>
<input type='hidden' name='sens' value='$sens'>
<input type='hidden' name='autresens'>
<input type='hidden' name='visu'>
<table>
<tr>
<td class='soustitre2'><p><a href='javascript:trier(\"nom\", \"$sens\");'>";
$contenu_page .= gras($sort, 'nom', true) . "Nom" . gras($sort, 'nom', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"renommée\", \"$sens\");'>";
$contenu_page .= gras($sort, 'renommée', true) . "Renommée" . gras($sort, 'renommée', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"renommée_magique\", \"$sens\");'>";
$contenu_page .= gras($sort, 'renommée_magique', true) . "Renommée magique" . gras($sort, 'renommée_magique', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"karma\", \"$sens\");'>";
$contenu_page .= gras($sort, 'karma', true) . "Karma" . gras($sort, 'karma', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"tue_perso\", \"$sens\");'>";
$contenu_page .= gras($sort, 'tue_perso', true) . "Nombre d’aventuriers tués" . gras($sort, 'tue_perso', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"tue_monstre\", \"$sens\");'>";
$contenu_page .= gras($sort, 'tue_monstre', true) . "Nombre de monstres tués" . gras($sort, 'tue_monstre', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"px\", \"$sens\");'>";
$contenu_page .= gras($sort, 'px', true) . "PX" . gras($sort, 'px', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"description\", \"$sens\");'>";
$contenu_page .= gras($sort, 'description', true) . "Description" . gras($sort, 'description', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "<td class='soustitre2'><p><a href='javascript:trier(\"fin\", \"$sens\");'>";
$contenu_page .= gras($sort, 'fin', true) . "Destin aux mains du joueur" . gras($sort, 'fin', false);
$contenu_page .= '</a></p></td>';

$contenu_page .= "</tr>";

while($db->next_record())
{
	$g1 = ($compt_cod == $db->f('cmon_compt_cod')) ? '<b>' : "";
	$g2 = ($compt_cod == $db->f('cmon_compt_cod')) ? '</b>' : "";
	
	$contenu_page .= "<tr>";
	$contenu_page .= "<td class=\"soustitre2\">$g1" . $db->f("cmon_nom") . "$g2</td>";
	$contenu_page .= "<td>" . $db->f("cmon_renommee_txt") . "</td>";
	$contenu_page .= "<td>" . $db->f("cmon_renommee_magique_txt") . "</td>";
	$contenu_page .= "<td>" . $db->f("cmon_karma_txt") . "</td>";
	$contenu_page .= "<td>" . $db->f("cmon_kill_perso") . "</td>";
	$contenu_page .= "<td>" . $db->f("cmon_kill_monstres") . "</td>";
	$contenu_page .= "<td>" . $db->f("cmon_px_txt") . "</td>";
	$contenu_page .= "<td>" . $db->f("cmon_description") . "</td>";
	$contenu_page .= "<td>" . $db->f("cmon_fin") . "</td>";
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
$page_en_cours = ($debut / 30) + 1;
for($cpt = 1; $cpt <= $nb_pages; $cpt++)
{
	$v_debut = ($cpt - 1) * 30;
	if ($cpt == 1)
	{
		if ($cpt != $page_en_cours)
		{
			$contenu_page .= "<a href=\"classement_monstres.php?debut=$v_debut&sort=$sort&sens=$autresens\">1</a>";
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
		$contenu_page .= " ... <a href=\"classement_monstres.php?debut=$v_debut&sort=$sort&sens=$autresens\">$cpt</a>";
	}
	if (($cpt == $nb_pages) && ($cpt != $page_en_cours) )
	{
		$contenu_page .= " ... <a href=\"classement_monstres.php?debut=$v_debut&sort=$sort&sens=$autresens\">$cpt</a>";
	}
	
}
$contenu_page .= "<p><a href=\"classement_v2.php\">Classement des aventuriers</a></p>";
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
