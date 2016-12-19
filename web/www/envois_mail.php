<?php require_once '/usr/local/phplib-7.4a/php/prepend.php';
#require '/home/delain/public_html/www/includes/delain_header.php';
require_once '/home/delain/public_html/www/includes/classes.php';
require_once '/home/delain/public_html/www/includes/class.smtp.inc';
require_once('Mail.php');
require_once('Mail/mime.php');
$db = new base_delain;
//
// Etate 1 : on regarde tous les mail en instance
//
$req = 'select distinct menv_compt_cod, compt_monstre, compt_admin, compt_mail from envois_mail
    inner join compte on compt_cod = menv_compt_cod
    where ((compt_envoi_mail_frequence - 1)::text || \' minutes\')::interval < (now() - compt_envoi_mail_dernier)::interval';
$db->query($req);
if($db->nf() == 0)
	echo "Pas de mail à envoyer.\r\n";
else
{
	$i = 0;
	while($db->next_record())
	{
    	$compte[$i] = $db->f('menv_compt_cod');
    	$compteAdmin[$i] = ($db->f('compt_admin') == 'O');
        $compteMonstre[$i] = ($db->f('compt_monstre') == 'O');
        $compteMail[$i] = $db->f('compt_mail');
		$i++;
	}
	foreach($compte as $key=>$val)
	{
		$compl_sujet = '';

		if ($val == 22115)
			$compl_sujet = ' - COMPTE CONTRÔLEUR';

        if ($compteAdmin[$key])
    		$compl_sujet .= ' - COMPTE ADMINISTRATEUR';

        if ($compteMonstre[$key])
    		$compl_sujet .= ' - COMPTE MONSTRE';

    	$persoNomPrec = "";
		$texte_mail = '';
		$req = 'select menv_perso_cod, perso_nom, menv_texte from envois_mail
            inner join perso on perso_cod = menv_perso_cod
            where menv_compt_cod = ' . $val . ' order by menv_perso_cod, menv_date';

		$db->query($req);
        
		while($db->next_record())
		{
    		$persoNom = $db->f('perso_nom');
            
            if ($persoNom != $persoNomPrec)
            {
			    $texte_mail .= "------------------------\r\n";
    		    $texte_mail .=	 'Pour le perso ' . $persoNom . " : \r\n";
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

/* Méthode MailMime. Pose des problèmes d’encodage
		$hdrs = array(
			'From'    => 'Robot des souterrains <noreply@jdr-delain.net>',
			'Reply-to'	=> 'noreply@jdr-delain.net',
			'Return-Path' => 'noreply@jdr-delain.net',
			'Subject' => 'Événements dans les souterrains de Delain' . $compl_sujet
		);
		
		$parametres = array(
			'eol'    => "\n",
			'head_charset'	=> 'utf-8',
			'text_charset' => 'utf-8'
		);

		$mime = new Mail_mime($parametres);
		$mime->setTXTBody($texte_mail);
		$body = $mime->get();

		$hdrs = $mime->headers($hdrs);

		$mail =& Mail::factory('smtp');


		//
		//$adr_mail = 'stephane.dewitte@gmail.com';
		//
		$res = $mail->send($adr_mail, $hdrs, $body);
		if (PEAR::isError($res))
		{
			echo 'Anomalie sur l’adresse ' . $adr_mail;

			// The reason for failure should be in the errors variable
			//print_r($smtp->errors);
			$req = 'update envois_mail set menv_anomalie = 1 where menv_compt_cod = ' . $val;
			$db->query($req);
		}
		else
		{
			echo 'Email bien envoyé à '.$adr_mail."\r\n";

			// Any recipients that failed (relaying denied for example) will be logged in the errors variable.
			//print_r($smtp->errors);
			$req = 'delete from envois_mail where menv_compt_cod = ' . $val;
			$db->query($req);	
		}
Fin Méthode MailMime*/
/* Méthode classe smtp, utilisée pour l'envoi de mail à la création du compte, et qui semble fonctionner. */

		/***************************************
		** Setup some parameters which will be 
		** passed to the smtp::connect() call.
		***************************************/
		$params['host'] = 'localhost';				// The smtp server host/ip
		$params['port'] = 25;						// The smtp server port
		$params['helo'] = 'jdr-delain.net';			// What to use when sending the helo command. Typically, your domain/hostname
		$params['auth'] = false;						// Whether to use basic authentication or not
		$params['user'] = 'testuser';				// Username for authentication
		$params['pass'] = 'testuser';				// Password for authentication

		/***************************************
		** These parameters get passed to the 
		** smtp->send() call.
		***************************************/

		$send_params['recipients']	= array($adr_mail);							// The recipients (can be multiple)
		$send_params['headers']		= array(
			'From: Robot des souterrains <noreply@jdr-delain.net>',	// Headers
			'To: ' . $adr_mail,
			'Reply-to: noreply@jdr-delain.net',
			'Return-Path: noreply@jdr-delain.net',
			'Subject: =?UTF-8?B?' . base64_encode('Événements dans les souterrains de Delain' . $compl_sujet) . '?=',
			'Content-Type: text/plain; charset="UTF-8"'
			);
		$send_params['from']		= 'noreply@jdr-delain.net';									// This is used as in the MAIL FROM: cmd
																								// It should end up as the Return-Path: header
		$send_params['body']		= $texte_mail;										// The body of the email

		$smtp = new smtp($params);

		if($smtp->connect() && $smtp->send($send_params))
		{
			echo 'Email bien envoyé à '.$adr_mail."\r\n";

			// Any recipients that failed (relaying denied for example) will be logged in the errors variable.
			//print_r($smtp->errors);
			$req = 'delete from envois_mail where menv_compt_cod = ' . $val;
			$db->query($req);
		}
		else
		{
			echo 'Anomalie sur l’adresse ' . $adr_mail;

			// The reason for failure should be in the errors variable
			//print_r($smtp->errors);
			$req = 'update envois_mail set menv_anomalie = 1 where menv_compt_cod = ' . $val;
			$db->query($req);
		}
	}
    $req = 'update compte set compt_envoi_mail_dernier = now() where compt_cod IN (' . implode(',', $compte) . ')';
	$db->query($req);
}
