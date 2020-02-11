<?php
include "blocks/_header_page_jeu.php";

$req = "select compt_envoi_mail, compt_envoi_mail_message, compt_envoi_mail_frequence from compte where compt_cod = $compt_cod ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
//
//Contenu de la div de droite
//
$contenu_page = '<p class="titre">Réception des événements par courriel</p>';
if ($result['compt_envoi_mail'] == 1)
    $contenu_page .= '<p>Vous recevez actuellement les comptes rendus <strong>événements</strong> par courriel</p>
		<p><a href="change_rec_mail.php?type=e&met=n">Ne plus les recevoir ?</a></p>';
else
    $contenu_page .= '<p>Vous ne recevez pas les comptes rendus <strong>événement</strong> par courriel</p>
		<p><a href="change_rec_mail.php?type=e&met=o">Les recevoir ?</a></p>';
$contenu_page .= '<br><br>';
if ($result['compt_envoi_mail_message'] == 1)
    $contenu_page .= '<p>Vous recevez actuellement les <strong>avis de messages</strong> par courriel</p>
		<p><a href="change_rec_mail.php?type=m&met=n">Ne plus les recevoir ?</a></p>';
else
    $contenu_page .= '<p>Vous ne recevez pas les <strong>avis de messages</strong> par courriel</p>
		<p><a href="change_rec_mail.php?type=m&met=o">Les recevoir ?</a></p>';

$contenu_page .= '<br><br>';

$contenu_page .= '<p>Fréquence d’envoi des courriels</p>
    	<p><form name="frequence" method="POST" action="change_rec_mail.php">
            <input type="hidden" value="frequence" name="methode" />
            Toutes les <select name="frequence">
                <option value="5" ' . (($result['compt_envoi_mail_frequence'] == 5) ? 'selected="selected"' : '') . '>5 minutes</option>
                <option value="10" ' . (($result['compt_envoi_mail_frequence'] == 10) ? 'selected="selected"' : '') . '>10 minutes</option>
                <option value="15" ' . (($result['compt_envoi_mail_frequence'] == 15) ? 'selected="selected"' : '') . '>15 minutes</option>
                <option value="20" ' . (($result['compt_envoi_mail_frequence'] == 20) ? 'selected="selected"' : '') . '>20 minutes</option>
                <option value="30" ' . (($result['compt_envoi_mail_frequence'] == 30) ? 'selected="selected"' : '') . '>30 minutes</option>
                <option value="45" ' . (($result['compt_envoi_mail_frequence'] == 45) ? 'selected="selected"' : '') . '>45 minutes</option>
                <option value="60" ' . (($result['compt_envoi_mail_frequence'] == 60) ? 'selected="selected"' : '') . '>1 heure</option>
                <option value="120" ' . (($result['compt_envoi_mail_frequence'] == 120) ? 'selected="selected"' : '') . '>2 heures</option>
                <option value="360" ' . (($result['compt_envoi_mail_frequence'] == 360) ? 'selected="selected"' : '') . '>6 heures</option>
                <option value="720" ' . (($result['compt_envoi_mail_frequence'] == 720) ? 'selected="selected"' : '') . '>12 heures</option>
                <option value="1440" ' . (($result['compt_envoi_mail_frequence'] == 1440) ? 'selected="selected"' : '') . '>24 heures</option>
            </select>. <input type="submit" value="valider" class="bouton" name="bouton"/>
        </form></p>';

include "blocks/_footer_page_jeu.php";
