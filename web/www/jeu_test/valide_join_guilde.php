<?php
include "blocks/_header_page_jeu.php";
require_once G_CHE . 'includes/message.php';
ob_start();
$num_guilde = $_REQUEST['num_guilde'];

// on efface si autre guilde
$req_eff = "delete from guilde_perso where pguilde_perso_cod = $perso_cod ";
$stmt = $pdo->query($req_eff);

// on inscrit dans la guilde
$req_ins = "insert into guilde_perso (pguilde_guilde_cod,pguilde_perso_cod,pguilde_rang_cod,pguilde_valide,pguilde_message) 
values($num_guilde,$perso_cod,1,'N','O')";
$stmt    = $pdo->query($req_ins);

// on envoie un message à l'admin de guilde
// on prépare le texte du message
$texte = "$perso_nom a demandé à faire partie de la guilde que vous administrez.<br />";
$texte = $texte . "Vous pouvez aller <a href=\"admin_guilde.php\">valider ou refuser</a> son inscription.";

$msg = new message;
$msg->corps = $texte;
$msg->sujet = "Demande d’admission dans votre guilde.";
$msg->expediteur = 1;

// on cherche les admins
$req_admin = "select pguilde_perso_cod from guilde_perso, guilde_rang ";
$req_admin = $req_admin . "where pguilde_guilde_cod = $num_guilde ";
$req_admin = $req_admin . "and rguilde_guilde_cod = $num_guilde ";
$req_admin = $req_admin . "and pguilde_rang_cod = rguilde_rang_cod ";
$req_admin = $req_admin . "and rguilde_admin = 'O' ";
$stmt = $pdo->query($req_admin);
while ($result = $stmt->fetch()) {
    $msg->ajouteDestinataire($result['pguilde_perso_cod']);
}

$msg->envoieMessage();

echo "<p>Un message a été envoyé au gestionnaire de guilde afin de valider ou non votre demande." ;
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
