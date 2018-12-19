<?php
if (!DEFINED("APPEL")) {
    die("Erreur d'appel de page !");
}
if (!isset($db)) {
    include "verif_connexion.php";
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
    <title>Escalier fermé</title>
</head>
<body>
<div class="bordiv">
    <?php

    // on regarde si le joueur est bien sur une banque
    $erreur = 0;
    if (!$db->is_lieu($perso_cod)) {
        echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
        $erreur = 1;
    }
    if ($erreur == 0) {
        $tab_lieu = $db->get_lieu($perso_cod);
        if ($tab_lieu['type_lieu'] != 3) {
            $erreur = 1;
            echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
        }
    }

    if ($erreur == 0) {
        $tab_lieu = $db->get_lieu($perso_cod);
        echo "<p><strong>" . $tab_lieu['nom'] . "</strong>  - " . $tab_lieu['description'];
        echo("<p>Vous voyez un escalier qui descend vers le niveau inférieur, mais son accès est bloqué par une barrière magique infranchissable.<br />");
        echo("Il y a un mot gravé sur la pierre ; <br />");
        echo("<em>Toi le fou qui veut accéder à ces souterrains, porte ici l'amulette de souvenir");
        if ($db->has_artefact($perso_cod, 636)) {
            echo("<p>Souhaitez vous poser l'amulette sur l'escalier ?<br />");
            echo("<a href=\"valide_escalier_d_ferme.php\">Oui !</a><br />");
            echo("<a href=\"perso2.php\">Non merci !</a><br />");
        }
    }

    ?>
</div>
</body>
</html>
