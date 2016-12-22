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
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un batiment administratif !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 9)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un batiment administratif !!!");
	}
}
if ($erreur == 0)
{
	
        $req = "delete from guilde_perso where pguilde_perso_cod = $perso_cod ";
	$db->query($req);
	$nom_guilde = htmlspecialchars($nom_guilde);
	$desc = htmlspecialchars($desc);
	$desc = nl2br($desc);
	$req_existe = "select guilde_cod from guilde where lower(guilde_nom) = lower('" . pg_escape_string($nom_guilde) ."')";
	$db->query($req_existe);
	$nb_guilde = $db->nf();
	if ($nb_guilde != 0)
	{
		echo("<p>Une guilde porte déjà ce nom <br />");
		echo("<a href=\"cree_guilde.php\">Retour !</a>");
	}
	else
	{	
                //$desc = str_replace("'","\'",$desc);
		$req_cree = "select cree_guilde($perso_cod,e'" . pg_escape_string($nom_guilde) ."',e'" . pg_escape_string($desc) . "') as cree";
		$db->query($req_cree);
		$db->next_record();
		$resultat = $db->f("cree");
		echo("<!-- $resultat -->");
		if ($resultat == '0')
		{
			echo("<p>Votre guilde a été créée avec succès !<br />");
			echo("<a href=\"admin_guilde.php\">Administrer la guilde !</a>");
		}
		else
		{
			echo("<p>Une anomalie est survenue = $resultat");
		}
	}
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
