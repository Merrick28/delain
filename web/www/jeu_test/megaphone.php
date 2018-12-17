<?php
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$erreur = 0;
if ($db->is_milice($perso_cod) == 0)
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
    if (!isset($methode))
    {
        $methode = 'debut';
    }
    if (!$db->is_bernardo($perso_cod))
    {
        switch ($methode)
        {
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
                                    for ($i = 0; $i <= 2; $i++)
                                    {
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
                $db->query($req_msg_cod);
                $db->next_record();
                $num_mes = $db->f("numero");
                // encodage du texte
                $corps = htmlspecialchars($corps);
                $corps = nl2br($corps);
                $corps = str_replace(";", chr(127), $corps);
                $corps = pg_escape_string($corps);
                // enregistrement du message
                $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps) ";
                $req_ins_mes = $req_ins_mes . "values ($num_mes,now(),now(),e'$titre',e'$corps') ";
                $db->query($req_ins_mes);
                // enregistrement de l'expéditeur
                $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
                $req_ins_exp = $req_ins_exp . "values (nextval('seq_emsg_cod'),$num_mes,$perso_cod,'N')";
                $db->query($req_ins_exp);
                // enregistrement des destinataires
                // recherche de la position
                $req_pos = "select ppos_pos_cod,pos_etage,pos_x,pos_y from perso_position,perso,positions where ppos_perso_cod = $perso_cod and perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
                $db->query($req_pos);
                $db->next_record();
                $pos_actuelle = $db->f("ppos_pos_cod");
                $v_x = $db->f("pos_x");
                $v_y = $db->f("pos_y");
                $etage = $db->f("pos_etage");
                // rechreche des dest
                $req_vue = "select perso_cod,perso_nom,distance(ppos_pos_cod,$pos_actuelle) from perso, perso_position, positions ";
                $req_vue = $req_vue . "where pos_x >= ($v_x - $volume) and pos_x <= ($v_x + $volume) ";
                $req_vue = $req_vue . "and pos_y >= ($v_y - $volume) and pos_y <= ($v_y + $volume) ";
                $req_vue = $req_vue . "and ppos_perso_cod = perso_cod ";
                $req_vue = $req_vue . "and perso_cod != $perso_cod  ";
                $req_vue = $req_vue . "and perso_actif = 'O' ";
                $req_vue = $req_vue . "and ppos_pos_cod = pos_cod ";
                $req_vue = $req_vue . "and pos_etage = $etage ";
                $db->query($req_vue);
                $db2 = new base_delain;
                while ($db->next_record())
                {
                    $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) ";
                    $req_ins_dest = $req_ins_dest . "values (nextval('seq_dmsg_cod'),$num_mes, " . $db->f("perso_cod") . ",'N','N')";
                    $db2->query($req_ins_dest);
                    $liste_expedie = $liste_expedie . $db->f("perso_nom") . ",";
                }
                echo "<p>Votre message a été envoyé à toutes les personnes présents à $volume de distance de vous.";
                break;
        }
    } else
    {
        echo("<p>Vous êtes sous l'effet du sort Bernardo. Vous ne pouvez pas poster de message.");
    }
} else
{
    ?>
    <p>Erreur ! vous n'avez pas accès à cette page !
    <?php
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');

