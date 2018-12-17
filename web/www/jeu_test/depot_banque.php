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
	echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 1)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
	}
}

if ($erreur == 0)
{
	echo("<img src=\"../images/banque3.png\"><br />");
	// on recherche l'or en banque
	$req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
	$db->query($req_or);
	$nb_or = $db->nf();
	if ($nb_or == 0)
	{
		$qte_or = 0;
	}
	else
	{
		$db->next_record();
		$qte_or = $db->f("pbank_or");
	}
	echo("<p>Vous avez $qte_or brouzoufs sur votre compte.");
	?>
	<hr />
	<form name="depot" method="post" action="valide_depot_banque.php">
	<p>Déposer <input type="text" name="quantite"> brouzoufs sur mon compte.
	<p><input type="submit" value="Valider !" class="test centrer">
	</form>
	<?php 
	
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
