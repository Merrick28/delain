<?php include_once "verif_connexion.php";
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



$erreur = false;

//
// verif droits
//
$req = "select dcompt_gere_droits from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	$droit['dcompt_gere_droits'] = 'N';
}
else
{
	$db->next_record();
	$droit['dcompt_gere_droits'] = $db->f("dcompt_gere_droits");
}
if ($droit['dcompt_gere_droits'] != 'O')
{
	echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
	$erreur = true;
}
if (!$erreur)
{
	$log = '';
	// initialisation de la méthode
	if(!isset($methode))
		$methode = 'debut';
	else
	{
		$req = "select compt_nom from compte where compt_cod = $compt_cod";
		$db->query($req);
		$db->next_record();
		$compt_nom = $db->f("compt_nom");
		$log = date("d/m/y - H:i")." Compte $compt_nom (n°$compt_cod) modifie les paramètres.\n";
	}
	define("APPEL",1);

	// Choix de l’onglet
	$lesMethodes = array(
		'messagerie' => array(
			'mess_add',
			'mess_del'
		),
		'globaux' => array(
			'glob_modif',
			'glob_creation'
		),
		'renommee' => array(
			'ren_g_creation',
			'ren_g_modif',
			'ren_g_supp',
			'ren_a_creation',
			'ren_a_modif',
			'ren_a_supp',
			'ren_m_creation',
			'ren_m_modif',
			'ren_m_supp',
			'ren_k_creation',
			'ren_k_modif',
			'ren_k_supp'
		)
	);

	$onglet = 'aucun';
	$onglet = (in_array($methode, $lesMethodes['globaux'])) ? 'globaux' : $onglet;
	$onglet = (in_array($methode, $lesMethodes['renommee'])) ? 'renommee' : $onglet;
	$onglet = (in_array($methode, $lesMethodes['messagerie'])) ? 'messagerie' : $onglet;

	if ($onglet == 'aucun' && isset($_GET['onglet']))
		$onglet = $_GET['onglet'];

	switch ($onglet)
	{
		case 'globaux':    // Modifs des paramètres globaux
			$page_include = 'admin_params.globaux.php';
			$style_glob = 'style="font-weight:bold;"';
		break;

		case 'renommee':   // Modifs de renommées
			$page_include = 'admin_params.renommee.php';
			$style_renom = 'style="font-weight:bold;"';
		break;

		case 'messagerie':   // Modifs de renommées
			$page_include = 'admin_params.messagerie.php';
            $style_messagerie = 'style="font-weight:bold;"';
		break;
	}

	echo "<h1><b><big>Gestion des paramètres</big></b></h1><div>
			<a href='?onglet=globaux' $style_glob>Paramètres globaux</a>
			 - <a href='?onglet=renommee' $style_renom>Renommées / karma</a>
			 - <a href='?onglet=messagerie' $style_messagerie>Messagerie</a></div></div><br />";

	if ($page_include != '')
		include_once $page_include;
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
