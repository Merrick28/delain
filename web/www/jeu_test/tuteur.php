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
if(!isset($methode))
	$methode = 'debut';
switch($methode)
{
	case 'debut':
		$req = 'select perso_niveau,perso_tuteur 
			from perso
			where perso_cod = ' . $perso_cod;
		$db->query($req);
		$db->next_record();
		if($db->f('perso_niveau') >= 15)
		{
			if($db->f('perso_tuteur') == 'f')
			{
				$contenu_page .= 'Vous n\'êtes pas inscrit en tant que tuteur. <a href="tuteur.php?methode=inscr">Cliquez-ici pour vous inscrire.</a><br /		>';
			}
			else
			{
				$contenu_page .= 'Vous êtes inscrit en tant que tuteur. <a href="tuteur.php?methode=desabo">Cliquez-ici pour vous désinscrire.</a><br />';
			}
			$req = 'select perso_nom,perso_cod
				from perso,tutorat
				where tuto_tuteur = ' . $perso_cod . '
				and tuto_filleul = perso_cod';
			$db->query($req);
			if($db->nf() == 0)
			{
				$contenu_page .= 'Vous n\'avez aucun filleul pour le moment.<br />';
			}
			else
			{
				$contenu_page .= 'Liste de vos filleuls :<br />';
				while($db->next_record())
				{
					$contenu_page .= '- <a href="visu_desc_perso.php?visu=' . $db->f('perso_cod') . '">' . $db->f('perso_nom') . '</a><br />';
				}
			}
		}
		break;
	case 'inscr':
		$req = 'update perso
			set perso_tuteur = true
			where perso_cod = ' . $perso_cod;
		$db->query($req);
		$contenu_page .= 'Vous êtes maintenant inscrit en tant que tuteur.';
		break;
	case 'desabo':
		$req = 'select perso_nom,perso_cod
			from perso,tutorat
			where tuto_tuteur = ' . $perso_cod . '
			and tuto_filleul = perso_cod';
		$db->query($req);
		if($db->nf() != 0)
		{
			$contenu_page = 'Vous avez des filleuls à votre charge. En vous désinscrivant, vous ne les abandonnez pas pour autant, et restez leur tuteur.<br />';
		}
		$req = 'update perso
			set perso_tuteur = false
			where perso_cod = ' . $perso_cod;
		$db->query($req);
		$contenu_page .= 'Vous êtes maintenant désinscrit en tant que tuteur. Aucun filleul ne vous sera attribué.';
	
		break;
}


$t->set_var('CONTENU_COLONNE_DROITE',$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
