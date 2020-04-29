<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.less" rel="stylesheet/less" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
    <title>Escalier fermé</title>
</head>
<body>
<div class="bordiv">
    <?php


    $type_lieu = 3;
    $nom_lieu  = 'un escalier';

    define('APPEL', 1);
    include "blocks/_test_lieu.php";


    if ($erreur == 0)
    {
        $perso    = new perso;
        $perso    = $verif_connexion->perso;
        $tab_lieu = $perso->get_lieu();
        echo "<p><strong>" . $tab_lieu['lieu']->lieu_nom . "</strong>  - " . $tab_lieu['lieu']->lieu_description;
        echo("<p>Vous voyez un escalier qui descend vers le niveau inférieur, mais son accès est bloqué par une barrière magique infranchissable.<br />");
        echo("Il y a un mot gravé sur la pierre ; <br />");
        echo("<em>Toi le fou qui veut accéder à ces souterrains, porte ici l'amulette de souvenir");
        if ($perso->has_artefact(636))
        {
            echo("<p>Souhaitez vous poser l'amulette sur l'escalier ?<br />");
            echo("<a href=\"valide_escalier_d_ferme.php\">Oui !</a><br />");
            echo("<a href=\"perso2.php\">Non merci !</a><br />");
        }
    }

    ?>
</div>
</body>
<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js"></script>
</html>
