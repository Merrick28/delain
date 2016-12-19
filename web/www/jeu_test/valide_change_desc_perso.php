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
$erreur = 0;
$corps = htmlspecialchars($corps);
$corps = str_replace(";",chr(127),$corps);
$corps = str_replace("\\"," ",$corps);
$corps = pg_escape_string($corps);
	if (!isset($methode))
	{
		$methode = 'desc';
	}
	// 
	// phrase à modifier par la suite en fonction des alignements
	//
	switch ($methode)
	{
		case "desc":
			if (strlen($corps)>=254)
			{
				echo "<p>Votre description est trop longue (max 254 caractères), merci de la raccourcir !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$req_desc = "update perso set perso_description = e'$corps' where perso_cod = $perso_cod ";
				$db->query($req_desc);
				echo("<p>La description de votre personnage est enregistrée !");
			}
			echo("<p><a href=\"change_profil_perso.php\">Retour !</a>");
		break;
		case "desc_long";
			if (strlen($corps)>=10000)
			{
				echo "<p>Votre description est trop longue (max 10000 caractères), merci de la raccourcir !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$req_desc = "update perso set perso_desc_long = e'$corps' where perso_cod = $perso_cod ";
				$db->query($req_desc);
				echo("<p>La description longue de votre personnage est enregistrée !");
			}
			echo("<p><a href=\"change_profil_perso.php\">Retour !</a>");
		break;
	}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
