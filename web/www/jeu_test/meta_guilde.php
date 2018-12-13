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
switch($g)
{
	case "n":
		$champ = 'guilde_meta_noir';
		$champ_perso = 'pguilde_meta_noir';
		break;
	case "c":
		$champ = 'guilde_meta_caravane';
		$champ_perso = 'pguilde_meta_caravane';
		break;
	
}
$erreur = 0;
$req_guilde = "select pguilde_meta_noir,guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod,rguilde_admin,pguilde_message ";
$req_guilde = $req_guilde . "from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_rang_cod = pguilde_rang_cod ";
$req_guilde = $req_guilde . "and pguilde_valide = 'O' ";
$req_guilde = $req_guilde . "and " . $champ . " = 'O' ";
$db->query($req_guilde);
if ($db->nf() == 0)
{
	echo "<p>Erreur ! Vous ne pouvez pas intervenir sur ce meta guildage !";
	$erreur = 1;
}
if ($erreur == 0)
{
	switch($r)
	{
		case "O":
			$texte = "Vous avez été <strong>rattaché</strong> à ce meta guildage.";
			break;
		case "N":
			$texte = "Vous avez été <strong>supprimé</strong> de ce meta guildage.";
			break;		
	}	
	$req = "update guilde_perso set " . $champ_perso . " = '$r' where pguilde_perso_cod = $perso_cod ";
	$db->query($req);
	echo "<p>$texte";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');