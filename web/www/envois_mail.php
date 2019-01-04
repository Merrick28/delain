<?php
ini_set('include_path', '.:/home/delain/delain/web/phplib-7.4a/php:/home/delain/delain/web/www/includes:/usr/share/php');

include "delain_header.php";
include "base_delain.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new base_delain;
//
// Etate 1 : on regarde tous les mail en instance
//
$req = 'select distinct menv_compt_cod, compt_monstre, compt_admin, compt_mail from envois_mail
    inner join compte on compt_cod = menv_compt_cod
    where ((compt_envoi_mail_frequence - 1)::text || \' minutes\')::interval < (now() - compt_envoi_mail_dernier)::interval';
$db->query($req);
if ($db->nf() == 0)
{
    echo "Pas de mail à envoyer.\r\n";
}
else
{
    $i = 0;
    while ($db->next_record())
    {
        $compte[$i] = $db->f('menv_compt_cod');
        $compteAdmin[$i] = ($db->f('compt_admin') == 'O');
        $compteMonstre[$i] = ($db->f('compt_monstre') == 'O');
        $compteMail[$i] = $db->f('compt_mail');
        $i++;
    }
    foreach ($compte as $key => $val)
    {
        $compl_sujet = '';

        if ($val == 22115)
        {
            $compl_sujet = ' - COMPTE CONTRÔLEUR';
        }

        if ($compteAdmin[$key])
        {
            $compl_sujet .= ' - COMPTE ADMINISTRATEUR';
        }

        if ($compteMonstre[$key])
        {
            $compl_sujet .= ' - COMPTE MONSTRE';
        }

        $persoNomPrec = "";
        $texte_mail = '';
        $req = 'select menv_perso_cod, perso_nom, menv_texte from envois_mail
            inner join perso on perso_cod = menv_perso_cod
            where menv_compt_cod = ' . $val . ' order by menv_perso_cod, menv_date';

        $db->query($req);

        while ($db->next_record())
        {
            $persoNom = $db->f('perso_nom');

            if ($persoNom != $persoNomPrec)
            {
                $texte_mail .= "------------------------\r\n";
                $texte_mail .= 'Pour le perso ' . $persoNom . " : \r\n";
            }
            $persoNomPrec = $persoNom;

            $texte_mail .= $db->f("menv_texte") . "\r\n";
        }
        $adr_mail = $compteMail[$key];

        $texte_mail = "Bonjour,

Voici les derniers événements qui ont impactés vos personnages dans les souterrains de Delain.

" . $texte_mail . "------------------------

(Attention, ce courriel est envoyé par un robot, inutile d’y répondre. En cas de problème, merci de signaler l’anomalie sur le forum ( http://forum.jdr-delain.net/ ))
Vous pouvez à tout moment choisir de ne plus recevoir ces courriels, ou d’en augmenter la fréquence, en configurant votre compte sur le jeu.\r\n";

        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('noreply@jdr-delain.net', 'Robot des souterrains');
        $mail->IsHTML(false);
//$mail->FromName = $header;
        $mail->addAddress($adr_mail);
        $mail->Subject = 'Événements dans les souterrains de Delain' . $compl_sujet;
        $mail->Body = $texte_mail;
        try{
            $mail->Send();
            echo 'Email bien envoyé à ' . $adr_mail . "\r\n";

            // Any recipients that failed (relaying denied for example) will be logged in the errors variable.
            //print_r($smtp->errors);
            $req = 'delete from envois_mail where menv_compt_cod = ' . $val;
            $db->query($req);
        }
        catch (Exception $e) {
            echo 'Anomalie sur l’adresse ' . $adr_mail;

            // The reason for failure should be in the errors variable
            //print_r($smtp->errors);
            $req = 'update envois_mail set menv_anomalie = 1 where menv_compt_cod = ' . $val;
            $db->query($req);
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }


    }
    $req = 'update compte set compt_envoi_mail_dernier = now() where compt_cod IN (' . implode(',', $compte) . ')';
    $db->query($req);
}
