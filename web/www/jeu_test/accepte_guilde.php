<?php
include "blocks/_header_page_jeu.php";

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

include "blocks/_footer_page_jeu.php";