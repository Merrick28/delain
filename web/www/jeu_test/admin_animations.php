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

function writelog($textline)
{
	$filename="../logs/animations.log";
	if (is_writable($filename))
	{
		if (!$handle = fopen($filename, 'a'))
		{
			echo "Cannot open file ($filename)";
			exit;
		}
		if (fwrite($handle, $textline) === FALSE)
		{
			echo "Cannot write to file ($filename)";
			exit;
		}
		fclose($handle);
	}
	else
		echo "The file $filename is not writable";
}

//
//Contenu de la div de droite
//

ob_start();

?>
<script type="text/javascript">
	function viderListe(listeObjets)
	{
		while (listeObjets.hasChildNodes())
			listeObjets.removeChild(listeObjets.firstChild);
	}

	function ajouterElement(clef, valeur, listeObjets, selected)
	{
		listeObjets.options[listeObjets.options.length] = new Option();
		listeObjets.options[listeObjets.options.length-1].text = valeur;
		listeObjets.options[listeObjets.options.length-1].value = clef;
		listeObjets.options[listeObjets.options.length-1].selected = selected;
	}

	function filtrer_gobj(tobj_cod, selected_gobj, destination, tableau)
	{
		var listeObjets = document.getElementById(destination);
		viderListe(listeObjets);
		for (var gobj_cod in tableau[tobj_cod])
			ajouterElement(gobj_cod, tableau[tobj_cod][gobj_cod], listeObjets, (selected_gobj == gobj_cod));
	}
</script>
<?php 
// Liste des animations possibles
$erreur = 0;
$req = "select dcompt_animations from compt_droit where dcompt_compt_cod = $compt_cod";
$db->query($req);
if ($db->nf() == 0)
{
	echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
	$erreur = 1;
}
else
{
	$db->next_record();
	if ($db->f("dcompt_animations") != 'O')
	{
		echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
		$erreur = 1;
	}
}
if ($erreur == 0)
{
	define("APPEL",1);

	if (!isset($methode))
		$methode = 'debut';

	// Choix de l’onglet
	$script = '';
	$lesMethodes = array(
		'barde' => array(
			'barde_modif',
			'barde_creation',
			'barde_visu',
			'barde_nouvelle'
		),
		'pochettes' => array(
			'pochette_suppression',
			'pochette_distribution'
		),
		'invasion' => array(
			'cree_invasion'
		),
		'medaillons' => array(
			'medaillon_redistribution'
		),
		'collections' => array(
			'collection_modif',
			'collection_creation',
			'collection_visu',
			'collection_nouvelle'
		),
		'distribution' => array(
			'distribution_creation'
		)
	);

	$onglet = 'aucun';
	$onglet = (in_array($methode, $lesMethodes['barde'])) ? 'barde' : $onglet;
	$onglet = (in_array($methode, $lesMethodes['pochettes'])) ? 'pochettes' : $onglet;
	$onglet = (in_array($methode, $lesMethodes['invasion'])) ? 'invasion' : $onglet;
	$onglet = (in_array($methode, $lesMethodes['medaillons'])) ? 'medaillons' : $onglet;
	$onglet = (in_array($methode, $lesMethodes['collections'])) ? 'collections' : $onglet;
	$onglet = (in_array($methode, $lesMethodes['distribution'])) ? 'distribution' : $onglet;

	if ($onglet == 'aucun' && isset($_GET['onglet']))
		$onglet = $_GET['onglet'];

	switch ($onglet)
	{
		case 'barde':	// Concours de barde
			$page_include = 'admin_animations_concours_barde.php';
			$style_barde = 'style="font-weight:bold;"';
		break;

		case 'pochettes':	// Pochettes surprises
			$page_include = 'admin_animations_pochettes_surprises.php';
			$style_pochettes = 'style="font-weight:bold;"';
		break;

		case 'medaillons':	// Quête des médaillons
			$page_include = 'admin_animations_medaillons.php';
			$style_medaillons = 'style="font-weight:bold;"';
		break;

		case 'invasion':	// Lancer une invasion ?
			$page_include = 'admin_animations_invasion.php';
			$style_invasion = 'style="font-weight:bold;"';
		break;

		case 'collections':	// Lancer une collection ?
			$page_include = 'admin_animations_quete_collection.php';
			$style_collections = 'style="font-weight:bold;"';
		break;

		case 'distribution':	// Lancer une distribution collective ?
			$page_include = 'admin_animations_distributions.php';
			$style_distribution = 'style="font-weight:bold;"';
		break;
	}

	echo "<h1><b><big>Gestion des animations</big></b></h1><table width='100%'><tr>
		<td width='16%'><a href='?onglet=barde' $style_barde>Concours de bardes</a></td>
		<td width='16%'><a href='?onglet=pochettes' $style_pochettes>Pochettes surprise</a></td>
		<td width='17%'><a href='?onglet=medaillons' $style_medaillons>Quête des médaillons</a></td>
		<td width='17%'><a href='?onglet=invasion' $style_invasion>Invasion de monstres</a></td>
		<td width='17%'><a href='?onglet=collections' $style_collections>Concours de collection</a></td>
		<td width='17%'><a href='?onglet=distribution' $style_distribution>Distribution générale</a></td></tr></table></div><br />";

	if ($page_include != '')
		include_once $page_include;
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
