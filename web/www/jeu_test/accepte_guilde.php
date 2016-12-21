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
if (!$db->is_revolution($num_guilde))
{
	$req = "update guilde_perso set pguilde_valide = 'O' where pguilde_guilde_cod = $num_guilde and pguilde_perso_cod = $vperso ";
	
	$db->query($req);
    
    $msg = new message;
    $msg->corps = "Vous avez été validé dans la guilde pour laquelle vous demandiez une admission.<br />";
    $msg->sujet = "Demande d’admission dans une guilde.";
    $msg->expediteur = $perso_cod;
    $msg->ajouteDestinataire($vperso);
    
    $msg->envoieMessage();

	$contenu_page = '<p>Le joueur a été rajouté à votre guilde.</p>
		<p><a href="admin_guilde.php">Retourner à l’administration de la guilde</a></p>';
}
else
{
	$contenu_page = '<p>Vous ne pouvez pas intervenir dans la gestion de la guilde pendant une révolution !</p>';
}
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
