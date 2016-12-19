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
if (!isset($compte))
{
	$req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod ";
	$db->query($req);

	// Compte trouvé
	if ($db->next_record())
		$compte = $db->f("pcompt_compt_cod");
	// Compte non trouvé ; peut-être un familier ?
	else
	{
		$req = "select pcompt_compt_cod from perso_compte
			inner join perso_familier on pfam_perso_cod = pcompt_perso_cod
			where pfam_familier_cod = $perso_cod ";
		$db->query($req);
		if ($db->next_record())
			$compte = $db->f("pcompt_compt_cod");
		else
		{
			$compte = -1;
		}
	}
}
if (!isset($_GET['etat']))
{
	$etat = 'N';
}
else
{
	$etat = $_GET['etat'];
}
if ($db->is_admin($compt_cod))
{
	$req = "select compt_nom from compte where compt_cod = $compte ";
	$db->query($req);
	$db->next_record();
	$nom_compte = $db->f("compt_nom");
	if ($etat == 'N')
	{
		$req = "update compte set compt_confiance = 'O' where compt_cod = $compte ";
		$db->query($req);
		echo "<p>Le compte <b>" . $nom_compte . "</b> a été passé en compte confiant. Il n’apparaîtra plus dans la liste des multi ";
	}
	if ($etat == 'O')
	{
		$req = "update compte set compt_confiance = 'N' where compt_cod = $compte ";
		$db->query($req);
		echo "<p>Le compte <b>" . $nom_compte . "</b> a été passé en compte NON confiant. Il apparaîtra dans la liste des multi ";
	}
	if ($etat == 'S')
	{
		$req = "update compte set compt_confiance = 'S' where compt_cod = $compte ";
		$db->query($req);
		echo "<p>Le compte <b>" . $nom_compte . "</b> a été passé en compte SURVEILLÉ. Un message sera envoyé aux Contôleurs à son sortir d’hibernation.";
	}
}
else
{
	echo "<p>Erreur ! Vous n’êtes pas administrateur !</p>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
