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
if ($quantite <= 0)
{
	$erreur = 1;
	?>
	<p>Vous ne pouvez pas retirer une somme inférieure ou égale à 0 !
	<?php 
}

if ($erreur == 0)
{
	
	?>
	<img src="../images/banque3.png"><br />
	<?php 
	$req_depot = "select retrait_banque($perso_cod,$quantite) as retrait";
	$db->query($req_depot);
	$db->next_record();
	$tab_depot = $db->f("retrait");
	if ($tab_depot == 0)
	{
		?>
		<p>Vous venez de retirer <strong><?php  echo $quantite; ?></strong> brouzoufs de votre compte en banque.
		<?php 
	}
	else
	{
		?>
		<p>Une anomalie est survenue : <strong><?php  echo $tab_depot ?>
		<?php 
	}
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
