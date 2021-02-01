<?php
include "blocks/_header_page_jeu.php";
include_once G_CHE . 'includes/message.php';
ob_start();

$erreur = 0;
if (!isset($vperso))
{
    $vperso = $_POST['vperso'];
}
if (!isset($num_guilde))
{
    $num_guilde = $_POST['num_guilde'];
}
$req = "select rguilde_admin from guilde_perso,guilde_rang
								where pguilde_perso_cod = $vperso
								and pguilde_guilde_cod = rguilde_guilde_cod
								and pguilde_rang_cod = rguilde_rang_cod ";

$stmt   = $pdo->query($req);
$result = $stmt->fetch();
if ($result['rguilde_admin'] == 'O')
{
    echo "<p>Erreur ! Vous ne pouvez pas renvoyer un admin !";

    die('</div>');
}

$guilde = new guilde;
$guilde->charge($_REQUEST['num_guilde']);

$revguilde = new guilde_revolution();
if ($revguilde->getByGuilde($guilde->guilde_cod))
{
    echo "<p>Vous ne pouvez pas intervenir dans la gestion de la guilde pendant une révolution !";

    die('</div>');
}

$ancienne_guilde = $guilde->guilde_nom;
$ancienne_guilde = "[Ancien membre de la guilde " . pg_escape_string($ancienne_guilde) . "]";

$methode = get_request_var('methode', 'debut');
switch ($methode)
{
    case 'debut':
        echo "Etes-vous sûr de vouloir virer ce membre de votre guilde ?
				<br><a href=\"" . $_SERVER['PHP_SELF'] . "?methode=validation&vperso=" . $vperso . "&num_guilde=" . $num_guilde . "\">Oui !</a>
				<br><br><a href=\"admin_guilde.php\">Non, retourner à l'administration de la guilde</a>";
        break;
    case 'validation':
        $req = "insert into perso_titre values(default,$vperso,e'$ancienne_guilde',now(),'2')";
        $stmt    = $pdo->query($req);
        $result  = $stmt->fetch();
        $req     = "delete from guilde_perso where pguilde_guilde_cod = $num_guilde and pguilde_perso_cod = $vperso ";
        $stmt    = $pdo->query($req);


        $texte = "Vous avez été renvoyé de votre guilde par l\'administrateur de celle-ci.<br />";
        $titre = "Renvoi de guilde.";

        $message             = new message();
        $message->sujet      = $titre;
        $message->corps      = $texte;
        $message->expediteur = 1;
        $message->ajouteDestinataire($vperso);
        $message->envoieMessage();


        ?>
        <p>Le personnage a été supprimé de votre guilde.
        <p><a href="admin_guilde.php">Retourner à l'administration de la guilde</a></p>
        <?php

        break;
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
