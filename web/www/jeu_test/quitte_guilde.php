<?php
include "blocks/_header_page_jeu.php";

ob_start();


if (!isset($methode))
    $methode = "debut";

switch ($methode) {
    case "debut":
        ?>
        <br>Êtes-vous sûr de vouloir quitter votre guilde ?
        <br><a href="<?php echo $PHP_SELF; ?>?methode=quitte">Oui</a>, laissez-moi partir !
        <br><br><a
            href="http://www.jdr-delain.net/jeu/guilde.php">Non</a>, c’était une erreur, je me sens bien dans cette guilde.
        <?php
        break;

    case "quitte":

        $req1 = "select pguilde_guilde_cod,guilde_nom from guilde_perso,guilde where pguilde_perso_cod = $perso_cod and pguilde_guilde_cod = guilde_cod";
        $stmt = $pdo->query($req1);
        $result = $stmt->fetch();
        $ancienne_guilde = $result['guilde_nom'];
        $num_guilde = $result['pguilde_guilde_cod'];
        $req = "delete from guilde_perso where pguilde_guilde_cod = $num_guilde and pguilde_perso_cod = $perso_cod ";
        $stmt = $pdo->query($req);

        $msg = new message;
        $msg->corps = "$perso_nom a quitté la guilde dont vous êtes administrateur.";
        $msg->sujet = "Départ d’un membre de la guilde.";
        $msg->expediteur = $perso_cod;

        // On recherche les admins / destinataires.
        $req_admin = "select pguilde_perso_cod from guilde_perso, guilde_rang
            where pguilde_guilde_cod = $num_guilde
                and pguilde_rang_cod = rguilde_rang_cod
                and rguilde_guilde_cod = pguilde_guilde_cod
                and rguilde_admin = 'O' ";
        $stmt = $pdo->query($req_admin);

        while ($result = $stmt->fetch()) {
            $msg->ajouteDestinataire($result['pguilde_perso_cod']);
        }

        $msg->envoieMessage();

        //on note l’historique dans les titres
        $ancienne_guilde = "[Ancien membre de la guilde " . pg_escape_string($ancienne_guilde) . "]";
        $req = "insert into perso_titre values(default,$perso_cod,e'$ancienne_guilde',now(),'2')";
        $stmt = $pdo->query($req);
        ?>
        Votre départ de la guilde est enregistré. Les administrateurs ont été prévenus.
        <?php
        break;
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
