<?php
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;
if ($db->is_milice($perso_cod) == 0) {
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0) {
    if (!isset($methode)) {
        $methode = 'debut';
    }
    if (!$db->is_bernardo($perso_cod)) {
        switch ($methode) {
            case "debut":
                ?>
                <form name="nouveau_message" method="post" action="<?php echo $PHP_SELF; ?>">
                    <input type="hidden" name="methode" value="envoi">
                    <table cellpadding="2" cellspacing="2">

                        <tr>
                            <td class="titre" colspan="2"><p class="titre">Utilisation du mégaphone</p></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="soustitre2"><p><em>Rappel : </em>Merci de bien vouloir éviter les
                                    insultes, et de rester dans le cadre de la courtoisie dans vos messages. Tout abus
                                    pourra amener à une cloture du compte sans préavis.</p></td>
                        </tr>

                        <tr>
                            <td class="soustitre2"><p>Régalge du volume </p></td>
                            <td>
                                <select name="volume">
                                    <?php
                                    for ($i = 0; $i <= 2; $i++) {
                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="soustitre2"><p>Message que vous allez hurler dans le megaphone : </p></td>
                            <td>
                                <textarea name="corps" cols="40" rows="10"></textarea>
                            </td>
                        </tr>


                        <tr>
                            <td colspan="2" class="soustitre2"><input type="submit" accesskey="s" class="test centrer"
                                                                      value="Envoyer le message !"></td>
                        </tr>

                    </table>
                </form>
                <?php
                break;
            case "envoi";
                // titre
                $titre = "C\'est la milice qui vous parle !";
                $titre = htmlspecialchars($titre);
                // numéro du message
                $req_msg_cod = "select nextval('seq_msg_cod') as numero";
                $stmt = $pdo->query($req_msg_cod);
                $result = $stmt->fetch();
                $num_mes = $result['numero'];
                // encodage du texte
                $corps = htmlspecialchars($corps);
                $corps = nl2br($corps);
                $corps = str_replace(";", chr(127), $corps);
                $corps = pg_escape_string($corps);
                // enregistrement du message
                $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps) ";
                $req_ins_mes = $req_ins_mes . "values ($num_mes,now(),now(),e'$titre',e'$corps') ";
                $stmt = $pdo->query($req_ins_mes);
                // enregistrement de l'expéditeur
                $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
                $req_ins_exp = $req_ins_exp . "values (nextval('seq_emsg_cod'),$num_mes,$perso_cod,'N')";
                $stmt = $pdo->query($req_ins_exp);
                // enregistrement des destinataires
                // recherche de la position
                $req_pos = "select ppos_pos_cod,pos_etage,pos_x,pos_y from perso_position,perso,positions where ppos_perso_cod = $perso_cod and perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
                $stmt = $pdo->query($req_pos);
                $result = $stmt->fetch();
                $pos_actuelle = $result['ppos_pos_cod'];
                $v_x = $result['pos_x'];
                $v_y = $result['pos_y'];
                $etage = $result['pos_etage'];
                // rechreche des dest
                $req_vue = "select perso_cod,perso_nom,distance(ppos_pos_cod,$pos_actuelle) from perso, perso_position, positions ";
                $req_vue = $req_vue . "where pos_x >= ($v_x - $volume) and pos_x <= ($v_x + $volume) ";
                $req_vue = $req_vue . "and pos_y >= ($v_y - $volume) and pos_y <= ($v_y + $volume) ";
                $req_vue = $req_vue . "and ppos_perso_cod = perso_cod ";
                $req_vue = $req_vue . "and perso_cod != $perso_cod  ";
                $req_vue = $req_vue . "and perso_actif = 'O' ";
                $req_vue = $req_vue . "and ppos_pos_cod = pos_cod ";
                $req_vue = $req_vue . "and pos_etage = $etage ";
                $stmt = $pdo->query($req_vue);
                
                while ($result = $stmt->fetch()) {
                    $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) ";
                    $req_ins_dest = $req_ins_dest . "values (nextval('seq_dmsg_cod'),$num_mes, " . $result['perso_cod'] . ",'N','N')";
                    $stmt2 = $pdo->query($req_ins_dest);
                    $liste_expedie = $liste_expedie . $result['perso_nom'] . ",";
                }
                echo "<p>Votre message a été envoyé à toutes les personnes présents à $volume de distance de vous.";
                break;
        }
    } else {
        echo("<p>Vous êtes sous l'effet du sort Bernardo. Vous ne pouvez pas poster de message.");
    }
} else {
    ?>
    <p>Erreur ! vous n'avez pas accès à cette page !
    <?php
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";


