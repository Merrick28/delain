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

// on regarde si le joueur est bien sur une banque
$erreur = 0;
$db = new base_delain;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 18)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
	}
}
if ($erreur == 0)
{
	$tab_lieu_cod = $db->get_lieu($perso_cod);
	$lieu_cod = $tab_lieu_cod['lieu_cod'];
	$req = "select lieu_dieu_cod from lieu where lieu_cod = $lieu_cod ";
	$db->query($req);
	$db->next_record();
	$index = $db->f("lieu_dieu_cod");
	$desc[1] = "Vous voyez un batiment tout en rondeur, avec des domes et des minarets. Le Jade et L'Obsidienne sont omniprésent donnant au batiment des couleurs vertes et noires. ";
	$desc[2] = "Vous voyez une construction aux murs épais, machicoulis, avec des meurtrières, une Tour de garde, le tout protégé par un pont levis";
	$desc[3] = "Vous voyez une sorte de batiment plat en matérieux naturels avec de nombreux coins, avec des silos sur les cotés.";
	$desc[4] = "Vous voyez une contruction tout en bois. Des rodins style cabane de bucherons. ";
	$desc[5] = "Vous voyez un grand batiment qui ressemble à de multiples hangars collés les uns aux autres.";
	echo "<p>" , $desc[$index];
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
