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
$db = new base_delain;
// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur une auberge !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 4)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur une auberge !!!");
	}
}

if ($erreur == 0)
{
	$req_pa = "select perso_pa,perso_pv,perso_pv_max,perso_po,perso_sex from perso where perso_cod = $perso_cod ";
	$db->query($req_pa);
	$nb_pa = $db->f("perso_pa");
	$prix = $nb_pa * 2;
	$sexe = $db->f("perso_sex");
	
	if ($db->f("perso_po") < $prix)
	{
		echo("<p>Vous savez, $nom_sexe[$sexe], n'ous n'apprécions pas vraiment le genre de personnes qui n'ont pas de quoi payer ce qu'elles demandent.<br />");
		echo("Revenez quand vous poches seront plus pleines, ou bien allez dormir dehors, au milieu des monstres.");
		$erreur = 1;
	}
	else
	{
		$gain_pv = $nb_pa*1.5;
		$gain_pv = round($gain_pv);
		$diff_pv = $db->f("perso_pv_max") - $db->f("perso_pv");
		if ($gain_pv > $diff_pv)
		{
			$gain_pv = $diff_pv;
		}
		$req_repos = "update perso ";
		$req_repos = $req_repos . "set perso_pv = perso_pv + $gain_pv, ";
		$req_repos = $req_repos . "perso_pa = 0, ";
		$req_repos = $req_repos . "perso_po = perso_po - $prix ";
		$req_repos = $req_repos . "where perso_cod = $perso_cod ";
		$db->query($req_repos);
		
			echo("<p>Vous vous êtes bien reposé. Vous avez regagné <b>$gain_pv</b> PV");

	}
	echo("<p><a href=\"auberge.php\">Retour</a>");	
	
	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
