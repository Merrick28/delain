<?php
include "blocks/_header_page_jeu.php";
ob_start();
$methode = get_request_var('methode', 'entree');
$req     = "select perso_niveau_vampire,race_nom from perso,race where perso_cod = $perso_cod ";
$req     = $req . "and perso_race_cod = race_cod ";
$stmt    = $pdo->query($req);
$result  = $stmt->fetch();
$lvl     = $result['perso_niveau_vampire'];
if ($lvl != 0)
{
    switch ($methode)
    {
        case "entree":
            // on commence par afficher un résumé
            echo "<p class=\"titre\">", $result['race_nom'], " - ", $niveau[$lvl], "</p>";
            // on n'affiche pas les infos d'ascendance si c'est un first, bien entendu
            if ($lvl != 100)
            {
                $req    = "select vamp_perso_pere,vamp_nom_ppere from vampire_hist ";
                $req    = $req . "where vamp_perso_fils = $perso_cod ";
                $stmt   = $pdo->query($req);
                $result = $stmt->fetch();
                echo "<p>Votre ascendant est : ";
                if ($result['vamp_perso_pere'] != '')
                {
                    echo "<a href=\"visu_desc_perso.php?visu=", $result['vamp_perso_pere'], "\">";
                }
                echo "<strong>", $result['vamp_nom_ppere'], "</strong>";
                if ($result['vamp_perso_pere'] != '')
                {
                    echo "</a>";
                }
                echo "<br><br>";
            }

            if ($lvl >= 60)
            {
                $req  =
                    "select vamp_perso_fils,vamp_nom_pfils,to_char(vamp_date,'DD/MM/YYYY hh24:mi:ss') as dvamp from vampire_hist ";
                $req  = $req . "where vamp_perso_pere = $perso_cod ";
                $req  = $req . "order by vamp_date ";
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() == 0)
                {
                    echo "<p>Vous n'avez aucune descendance";
                } else
                {
                    echo "<p class=\"soustitre2\">Liste de descendance :</p><p>";
                    while ($result = $stmt->fetch())
                    {
                        if ($result['vamp_perso_fils'] != '')
                        {
                            echo "<a href=\"visu_desc_perso.php?visu=", $result['vamp_perso_fils'], "\">";
                        }
                        echo "<strong>", $result['vamp_nom_pfils'], "</strong>";
                        if ($result['vamp_perso_fils'] != '')
                        {
                            echo "</a>";
                        }
                        echo " (", $result['dvamp'], ")<br>";
                    }
                }
                $req  =
                    "select to_char(tvamp_date,'dd/mm/yyyy hh24:mi:ss') as dvamp,tvamp_perso_fils,perso_nom from vampire_tran,perso ";
                $req  = $req . "where tvamp_perso_pere = $perso_cod ";
                $req  = $req . "and tvamp_perso_fils = perso_cod ";
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() == 0)
                {
                    echo "<p>Pas de création de descendance en cours";
                } else
                {
                    echo "<p>Descendances en cours :<br>";
                    while ($result = $stmt->fetch())
                    {
                        echo "<a href=\"visu_desc_perso.php?visu=", $result['tvamp_perso_fils'], "\">";
                        echo "<strong>", $result['perso_nom'], "</strong>";
                        echo "</a>";
                        echo " (", $result['dvamp'], ")<br>";
                    }
                }
                echo '<p><a href="vampirisme.php?methode=cree1">Créer une descendance !</a>';
            } else
            {
                echo "<p>Vous ne pouvez pas créér de descendance tant que vous n'êtes pas au minimum Maitre Vampire";
            }
            break;
        case "cree1":
            $erreur = 0;
            if ($lvl < 60)
            {
                echo "<p>Erreur, vous ne pouvez créer de descendance tant que vous n'êtes pas au minimum Maitre Vampire";
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $req_pos      = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
                $stmt         = $pdo->query($req_pos);
                $result       = $stmt->fetch();
                $pos_actuelle = $result['ppos_pos_cod'];
                $req_vue      = "select perso_nom,perso_cod from perso, perso_position ";
                $req_vue      = $req_vue . "where ppos_pos_cod = $pos_actuelle  ";
                $req_vue      = $req_vue . "and ppos_perso_cod = perso_cod ";
                //$req_vue = $req_vue . "and perso_cod != $perso_cod  ";
                $req_vue = $req_vue . "and perso_type_perso = 1 ";
                $req_vue = $req_vue . "and perso_actif = 'O' ";
                $req_vue = $req_vue . "order by perso_nom ";
                $stmt    = $pdo->query($req_vue);
                ?>
                <form name="cree" method="post" action="vampirisme.php">
                    <input type="hidden" name="methode" value="cree2">
                    <table>
                        <tr>
                            <td>
                                <p>Choisissez le perso :</td>
                            <td><p>
                                    <select name="cible">
                                        <?php
                                        while ($result = $stmt->fetch())
                                        {
                                            echo "<option value=\"", $result['perso_cod'], "\">", $result['perso_nom'], "</option>";
                                        }
                                        ?>
                                    </select></td>
                        </tr>
                        <tr>
                            <td>
                                <p>Ajoutez votre texte :</td>
                            <td><p>
                                    <textarea name="message" cols="40" rows="10"></textarea></td>
                        </tr>

                        <tr>
                            <td colspan="2"><p>Le perso recevra un message contenant votre texte, ainsi qu'un lien pour
                                    valider ou refuser votre proposition</td>
                        </tr>
                        <tr>
                            <td colspan="2"><p style="text-align:center;"><input type="submit" class="test"
                                                                                 value="Valider l'envoi"></td>
                        </tr>

                    </table>
                </form>
                <?php
            }
            break;
        case "cree2":
            //
            // on stocke les infos en base
            //
            $cible = $_REQUEST['cible'];
            $req   = "insert into vampire_tran (tvamp_perso_pere,tvamp_perso_fils) ";
            $req   = $req . "values ($perso_cod,$cible) ";
            $stmt  = $pdo->query($req);
            //
            // on envoie le message kivabien
            //

            $mymessage = nl2br($_REQUEST['message']);
            $corps     =
                "Un vampire vient de vous proposer de faire partie de sa descendance. Vous trouverez les instructions pour accepter ou refuser à la fin de ce message.<br>";
            $corps     = $corps . "-------------<br>" . $mymessage . "<br>-------------<br>";
            $corps     =
                $corps . "Pour voir les conséquences, accepter ou refuser cette proposition, <a href=\"tran_vamp.php\">cliquez-ici</a>";


            $message             = new message();
            $message->corps      = $corps;
            $message->sujet      = "Proposition de vampirisme";
            $message->expediteur = $perso_cod;
            $message->ajouteDestinataire($cible);
            $message->envoieMessage();


            echo "<p>Le message a été envoyé.";


            break;
    }
} else
{
    echo "<p>Vous n'avez pas accès à cette page !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
