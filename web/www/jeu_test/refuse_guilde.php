<?php
include "blocks/_header_page_jeu.php";
ob_start();

$guilde = new guilde;
$guilde->charge($_REQUEST['num_guilde']);

$revguilde = new guilde_revolution();

if (!$revguilde->getByGuilde($guilde->guilde_cod))
{
    $req = "delete from guilde_perso where pguilde_guilde_cod = $guilde->guilde_cod and pguilde_perso_cod = " .
           $_REQUEST['vperso'];
    $pdo->query($req);

    $message             = new message();
    $message->corps      =
        "Votre demande d\'admission dans une guilde a été rejetée par l\'administrateur de la guilde.<br />";
    $message->sujet      = "Demande d\'admission dans une guilde.";
    $message->expediteur = 1;
    $message->ajouteDestinataire($_REQUEST['vperso']);
    $message->envoieMessage();

    echo("<p>Le joueur a été supprimé de votre guilde.");
    echo("<p><a href=\"admin_guilde.php\">Retourner à l'administration de la guilde</a></p>");

} else
{
    echo "<p>Vous ne pouvez pas intervenir dans la gestion de la guilde pendant une révolution !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
