<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://www.jdr-delain.net//css/delain.css" rel="stylesheet">
    <title>Charte de Delain</title>
</head>
<body>

<div class="bordiv">

    <?php
    include "includes/classes.php";
    $db = new base_delain;
    if (!isset($methode)) {
        $methode = "debut";
    }
    switch ($methode) {
        case "debut":
            ?>
            Cette charte des joueurs de Delain explicite, comme la précédente, les règles de répartition de PX entre les personnages des souterrains. Cette répartition étant encore manuelle par nécessité, nous sommes obligés de la réglementer afin d'éviter les principaux abus et déséquilibres qui pourraient résulter de dons de PX exotiques. Aussi, vous êtes invités à (re)lire ce qui suit attentivement, les sanctions pour non respect de la charte pouvant aller de l'avertissement à la suppression pure et simple d'un compte.
            <br><br>

            Un système de répartition automatique des PX est à l'étude mais demande un temps conséquent. Aussi une mise à jour de la présente charte est devenue indispensable malgré les changements du système à venir.
            <br><br>

            Une version de cette même charte est disponible sur le <a
                href=http://www.jdr-delain.net/forum/viewtopic.php?t=8606&start=0&postdays=0&postorder=asc&highlight=>forum</a> avec les modifications apportées à la première charte en évidence. Toutefois, nous vous encourageons fortement à relire
            <strong>en entier</strong> ce document que vous allez accepter.<br><br>

            <div class="centrer">
                <IFRAME name="charte des joueurs" SRC="https://www.jdr-delain.net/charte.php" border=0 frameborder=0
                        height=350 width="80%" title="charte"></IFRAME>
            </div><br>
            <form method="post" action="valide_charte.php">
                <input type="hidden" name="methode" value="e1">
                <p>
                <hr>
                <p>Afin de valider cette charte, rentrez votre nom de compte et password<br>
                    <strong>La validation de ce formulaire entraine l'acceptation de la charte !
                        <div class="centrer">
                            <table>
                                <tr>
                                    <td>
                                        <span class="centrer"><strong>Nom du compte</strong></span>
                                    </td>
                                    <td><input type="text" name="nom"></td>
                                    <td>
                                        <strong>Mot de passe</strong></td>
                                    <td>
                                        <input type="password" name="pass"> <em><a href="renvoi_mdp.php">Mot de passe
                                                oublié ? </a></em>
                                    </td>
                                    </td>
                                </tr>
                            </table>
                            <input type="submit" class="test" value="J'accepte !">
            </form>
            <?php
            break;
        case "e1";
            $req = "select compt_cod from compte ";
            $req = $req . "where compt_nom = '$nom' ";
            $req = $req . "and compt_password = '$pass' ";
            $req = $req . "and compt_actif = 'O' ";
            $db->query($req);
            if ($db->nf() == 0) {
                echo "<p>Erreur ! Aucun compte trouvé avec ces coordonées !";
            } else {
                $db->next_record();
                $num = $db->f("compt_cod");
                $req = "update compte set compt_acc_charte = 'O' where compt_cod = $num ";
                $db->query($req);
                echo "<p>Votre compte est validé.";
                echo "<p><a href=\"login2.php\">Retour à l'identification</a>";
            }
            break;

    }
    ?>

</div>
</body>
</html>