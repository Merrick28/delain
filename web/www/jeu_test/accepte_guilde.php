<?php
include "blocks/_header_page_jeu.php";



$guilde = new guilde();
$guilde_perso = new guilde_perso();

$guilde_perso->get_by_perso($perso_cod);

$guilde->charge($guilde_perso->pguilde_guilde_cod);
$guilderev = new guilde_revolution();


if(!$guilderev->getBy_revguilde_guilde_cod($guilde->guilde_cod))
{
    $gperso2 = new guilde_perso();


	if($gperso2->get_by_perso_guilde($_REQUEST['vperso'],$guilde->guilde_cod))
    {
        $guilde_perso->pguilde_valide = 'O';
        $guilde_perso->stocke();
        $msg = new message;
        $msg->corps = "Vous avez été validé dans la guilde pour laquelle vous demandiez une admission.<br />";
        $msg->sujet = "Demande d’admission dans une guilde.";
        $msg->expediteur = $perso_cod;
        $msg->ajouteDestinataire($_REQUEST['vperso']);

        $msg->envoieMessage();

        $contenu_page = '<p>Le joueur a été rajouté à votre guilde.</p>
		<p><a href="admin_guilde.php">Retourner à l’administration de la guilde</a></p>';
    }
	else
    {
        $contenu_page = "Une erreur est survenue sur le numéro de guilde";
    }


    

}
else
{
	$contenu_page = '<p>Vous ne pouvez pas intervenir dans la gestion de la guilde pendant une révolution !</p>';
}

include "blocks/_footer_page_jeu.php";