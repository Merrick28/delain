<?php
// les chemins sont en dur car appelés par un script, et non par apache
// du coup, le .htaccess qui change le include_path et le header
// n'est pas exécuté
ini_set('include_path', '.' . PATH_SEPARATOR . '/home/delain/delain/web/phplib-7.4a/php' . PATH_SEPARATOR . 'home/delain/delain/web/www/includes' . PATH_SEPARATOR . '/usr/share/php');
require '/home/delain/delain/web/www/includes/delain_header.php';
require G_CHE . '/includes/classes.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoie_mail($adr_mail, $sujet, $texte_mail, $val)
{
    global $db;
    $mail = new PHPMailer(true);
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    if (!empty(SMTP_USER))
    {
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASSWORD;
    }

    $mail->IsHTML(true);
    $mail->IsHTML(true);

    $mail->CharSet = 'utf-8';
    $mail->From = 'noreply@jdr-delain.net';
    $mail->FromName = 'Le robot des souterrains';
    $mail->AddAddress($adr_mail);
    $mail->Subject = $sujet;
    $mail->Body = $texte_mail;

    try
    {
        $mail->Send();
        echo 'Email bien envoyé à ' . $adr_mail . "\r\n";
        // Any recipients that failed (relaying denied for example) will be logged in the errors variable.
        //print_r($smtp->errors);
        $req = 'delete from envois_mail where menv_compt_cod = ' . $val;
        $db->query($req);
    } catch (Exception $e)
    {
        echo 'Anomalie sur l\'adresse ' . $adr_mail;
        // 	The reason for failure should be in the errors variable
        //print_r($smtp->errors);
        $req = 'update envois_mail set menv_anomalie = 1 where menv_compt_cod = ' . $val;
        $db->query($req);
    }
    unset($mail);
}

function prepare_envoi($req,$texte_comp)
{
    global $db;
    global $db2;
    $db->query($req);
    if ($db->nf() != 0)
    {
        while ($db->next_record())
        {
            $req = 'select compt_mail,compt_cod
			from compte,perso_compte
			where pcompt_perso_cod = ' . $db->f('perso_cod') . '
			and pcompt_compt_cod = compt_cod ';
            $db2->query($req);
            if ($db2->nf() == 0)
            {
                echo 'Erreur sur perso n ' . $db->f('perso_cod') . ', compte non trouvé !';
            } else
            {
                $db2->next_record();
                $adr_mail = $db2->f("compt_mail");
                $val = $db2->f("compt_cod");
                $sujet = 'Aventurier inactif dans les souterrains de Delain';
                $texte_mail = "Bonjour,<br />
l'aventurier " . $db->f("perso_nom") . " (n " . $db->f("perso_cod") . ") est " . $texte_comp . "<br />
Sans connection de votre part, il sera supprimé dans 10 jours.<br />
Si vous souhaitez le conserver, il vous suffit de vous loguer sur votre compte, et de cliquer sur l'aventurier pour l'activer.\r\n
<br />
Cordialement,<br />
Merrick.<br />
<br />
(Attention, ce mail est envoyé par un robot, inutile d'y répondre. En cas de problème, merci de signaler l'anomalie sur le forum ( <a href=\"http://www.jdr-delain.net/forum/\">http://www.jdr-delain.net/forum/</a> ))";
                envoie_mail($adr_mail, $sujet, $texte_mail, $val);

            }
        }
    }
}

//
$db = new base_delain;
$db2 = new base_delain;
//
// Etape 1 : on regarde tous les persos pas loin d'être finis....
// On commence par les non hibernés
//
$req = 'select perso_cod,perso_nom from perso where perso_type_perso = 1
		and perso_actif = \'O\'
		and perso_der_connex + (\'20 days\')::interval < now()
		and perso_pnj != 1
		and perso_mail_inactif_envoye = 0 ';
$texte_comp = 'inactif depuis plus de 20 jours';
prepare_envoi($req,$texte_comp);


//
// Etape 2 : on regarde tous les persos pas loin d'être finis....
// On finit par les hibernés
//
$req = 'select perso_cod,perso_nom from perso where perso_type_perso = 1
		and perso_actif = \'H\'
		and perso_der_connex + (\'80 days\')::interval < now()
		and perso_pnj != 1
		and perso_mail_inactif_envoye = 0 ';
$texte_comp = 'en hibernation depuis plus de 80 jours';
prepare_envoi($req,$texte_comp);
