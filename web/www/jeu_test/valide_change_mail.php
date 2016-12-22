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
include("../includes/incl_mail.php");

//
//Contenu de la div de droite
//
$contenu_page = '<p class="titre">Changement d’adresse électronique</p>';
$ok = 1;
if ($_POST['mail1'] != $_POST['mail2'])
{
	$contenu_page .= '<p>Les deux adresses ne correspondent pas !</p>';
	$ok = 0;
}
$req = "select compt_cod from compte where compt_mail = '" . $_POST['mail1'] . "' and compt_cod != $compt_cod ";
$db->query($req);
if ($db->nf() != 0)
{
	$ok = 0;
	$contenu_page .= '<p>Un autre compte existe déjà avec cette adresse !</p>';
}
if ($ok == 1)
{
	$valide = validateEmail($_POST['mail1']);	
	if (!$valide[0])
	{
		$ok = 0;
		$contenu_page .= '<p>Adresse électronique non valide !</p>';
	}
}
if ($ok == 0)
{
	$contenu_page .= '
	<p>Le changement d’adresse électronique n’a pas pu être fait.<br>
	<p class="text-align:center;"><a href="change_mail.php">Retour !</a></p>';
}
else
{
	$contenu_page .= '
	<form method="post" action="valide_change_mail2.php" name="final">
	<input type="hidden" name="mail1" value="' . $_POST['mail1'] . '">
	<p>Le changement est prêt à être effectué.<br>
	En cliquant sur <b>j’accepte</b>, je valide le changement, et je serai déconnecté du jeu jusqu’à réception du mail.
	<center><input type="submit" class="test" value="J’accepte !"></center>
	</form>';
}

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
