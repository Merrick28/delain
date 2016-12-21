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
$contenu_page = '<p class="titre">Réception des événements par courriel</p>';
if ($type == 'e')
{
	if ($met == 'n')
	{
		$req = "update compte set compt_envoi_mail = 0 where compt_cod = $compt_cod ";
		$db->query($req);
		$contenu_page .= 'Vous ne recevez plus les comptes rendus d’événements par courriel<br>';
	}
	else
	{
		$req = "update compte set compt_envoi_mail = 1 where compt_cod = $compt_cod ";
		$db->query($req);
		$contenu_page .= 'Vous recevrez les comptes rendus d’événements par courriel<br>';
	}
}
if ($type == 'm')
{
	if ($met == 'n')
	{
		$req = "update compte set compt_envoi_mail_message = 0 where compt_cod = $compt_cod ";
		$db->query($req);
		$contenu_page .= 'Vous ne recevez plus les avis de messages par courriel<br>';
	}
	else
	{
		$req = "update compte set compt_envoi_mail_message = 1 where compt_cod = $compt_cod ";
		$db->query($req);
		$contenu_page .= 'Vous recevrez les avis de messages par courriel<br>';
	}
}

if (isset($_POST["methode"]) && $_POST["methode"] == 'frequence')
{
        $frequence = pg_escape_string($_POST['frequence']);
        $req = "update compte set compt_envoi_mail_frequence = $frequence where compt_cod = $compt_cod ";
		$db->query($req);
		$contenu_page .= 'Fréquence de réception des courriels mise à jour';
}

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");