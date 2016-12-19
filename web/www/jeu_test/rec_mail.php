<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$req = "select compt_envoi_mail, compt_envoi_mail_message, compt_envoi_mail_frequence from compte where compt_cod = $compt_cod ";
$db->query($req);
$db->next_record();
//
//Contenu de la div de droite
//
$contenu_page = '<p class="titre">Réception des événements par courriel</p>';
if ($db->f("compt_envoi_mail") == 1)
    $contenu_page .= '<p>Vous recevez actuellement les comptes rendus <b>événements</b> par courriel</p>
		<p><a href="change_rec_mail.php?type=e&met=n">Ne plus les recevoir ?</a></p>';
else
	$contenu_page .= '<p>Vous ne recevez pas les comptes rendus <b>événement</b> par courriel</p>
		<p><a href="change_rec_mail.php?type=e&met=o">Les recevoir ?</a></p>';
$contenu_page .= '<br><br>';
if ($db->f("compt_envoi_mail_message") == 1)
	$contenu_page .= '<p>Vous recevez actuellement les <b>avis de messages</b> par courriel</p>
		<p><a href="change_rec_mail.php?type=m&met=n">Ne plus les recevoir ?</a></p>';
else
	$contenu_page .= '<p>Vous ne recevez pas les <b>avis de messages</b> par courriel</p>
		<p><a href="change_rec_mail.php?type=m&met=o">Les recevoir ?</a></p>';

$contenu_page .= '<br><br>';

$contenu_page .= '<p>Fréquence d’envoi des courriels</p>
    	<p><form name="frequence" method="POST" action="change_rec_mail.php">
            <input type="hidden" value="frequence" name="methode" />
            Toutes les <select name="frequence">
                <option value="5" ' . (($db->f("compt_envoi_mail_frequence") == 5) ? 'selected="selected"' : ''). '>5 minutes</option>
                <option value="10" ' . (($db->f("compt_envoi_mail_frequence") == 10) ? 'selected="selected"' : ''). '>10 minutes</option>
                <option value="15" ' . (($db->f("compt_envoi_mail_frequence") == 15) ? 'selected="selected"' : ''). '>15 minutes</option>
                <option value="20" ' . (($db->f("compt_envoi_mail_frequence") == 20) ? 'selected="selected"' : ''). '>20 minutes</option>
                <option value="30" ' . (($db->f("compt_envoi_mail_frequence") == 30) ? 'selected="selected"' : ''). '>30 minutes</option>
                <option value="45" ' . (($db->f("compt_envoi_mail_frequence") == 45) ? 'selected="selected"' : ''). '>45 minutes</option>
                <option value="60" ' . (($db->f("compt_envoi_mail_frequence") == 60) ? 'selected="selected"' : ''). '>1 heure</option>
                <option value="120" ' . (($db->f("compt_envoi_mail_frequence") == 120) ? 'selected="selected"' : ''). '>2 heures</option>
                <option value="360" ' . (($db->f("compt_envoi_mail_frequence") == 360) ? 'selected="selected"' : ''). '>6 heures</option>
                <option value="720" ' . (($db->f("compt_envoi_mail_frequence") == 720) ? 'selected="selected"' : ''). '>12 heures</option>
                <option value="1440" ' . (($db->f("compt_envoi_mail_frequence") == 1440) ? 'selected="selected"' : ''). '>24 heures</option>
            </select>. <input type="submit" value="valider" class="bouton" name="bouton"/>
        </form></p>';

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
