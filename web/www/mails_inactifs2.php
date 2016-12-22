<?php 
require '/usr/local/phplib-7.4a/php/prepend.php';
#require '/home/delain/public_html/www/includes/delain_header.php';
require '/home/delain/public_html/www/includes/classes.php';
require '/home/delain/public_html/www/includes/class.smtp.inc';
require('Mail.php');
require('Mail/mime.php');
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
$db->query($req);
if($db->nf() != 0)
{
	while($db->next_record())	
	{
		$req = 'select compt_mail,compt_cod
			from compte,perso_compte
			where pcompt_perso_cod = ' . $db->f('perso_cod') . '
			and pcompt_compt_cod = compt_cod ';
		$db2->query($req);
		if($db2->nf() == 0)
			echo 'Erreur sur perso n ' . 	$db->f('perso_cod') . ', compte non trouvé !';
		else
		{
			$db2->next_record();
			$adr_mail = $db2->f("compt_mail");	
			$val = $db2->f("compt_cod");	
			$sujet = 'Aventurier inactif dans les souterrains de Delain';	
			$texte_mail = "Bonjour,\r\n
l'aventurier " . $db->f("perso_nom") . " (n " . $db->f("perso_cod") . ") est inactif depuis plus de 20 jours.\r\n
Sans connection de votre part, il sera supprimé dans 10 jours.\r\n
Si vous souhaitez le conserver, il vous suffit de vous loguer sur votre compte, et de cliquer sur l'aventurier pour l'activer.\r\n
\r\n
Cordialement,\r\n
Merrick.\r\n
\r\n
(Attention, ce mail est envoyé par un robot, inutile d'y répondre. En cas de problème, merci de signaler l'anomalie sur le forum ( http://www.jdr-delain.net/forum/ ))";
			
			$hdrs = array(
              'From'    => 'Robot des souterrains <noreply@jdr-delain.net>',
              'Reply-to'	=> 'noreply@jdr-delain.net',
              'Return-Path' => 'noreply@jdr-delain.net',
              'Subject' => $sujet
              );
	      $crlf = "\n";
	      $mime = new Mail_mime($crlf);
			$mime->setTXTBody($texte_mail);
			$body = $mime->get();
			
			$hdrs = $mime->headers($hdrs);
			
			$mail =& Mail::factory('mail');			
				
			$res = $mail->send($mail, $hdrs, $body);
			if (PEAR::isError($res))
			{
				
				echo 'Email bien envoyé à '.$adr_mail."\r\n";
				// Any recipients that failed (relaying denied for example) will be logged in the errors variable.
				//print_r($smtp->errors);
				$req = 'delete from envois_mail where menv_compt_cod = ' . $val;
				$db->query($req);	
			
			}
			else
			{
				echo 'Anomalie sur l\'adresse ' . $adr_mail;
				// 	The reason for failure should be in the errors variable
				//print_r($smtp->errors);
				$req = 'update envois_mail set menv_anomalie = 1 where menv_compt_cod = ' . $val;
				$db->query($req);
			}	
		}
	}
}
//
// Etape 2 : on regarde tous les persos pas loin d'être finis....
// On finit par les hibernés
//
$req = 'select perso_cod,perso_nom from perso where perso_type_perso = 1
		and perso_actif = \'H\'
		and perso_der_connex + (\'80 days\')::interval < now()
		and perso_pnj != 1
		and perso_mail_inactif_envoye = 0 ';
$db->query($req);
if($db->nf() != 0)
{
	while($db->next_record())	
	{
		$req = 'select compt_mail,compt_cod
			from compte,perso_compte
			where pcompt_perso_cod = ' . $db->f('perso_cod') . '
			and pcompt_compt_cod = compt_cod ';
		$db2->query($req);
		if($db2->nf() == 0)
			echo 'Erreur sur perso n ' . 	$db->f('perso_cod') . ', compte non trouvé !';
		else
		{
			$db2->next_record();
			$adr_mail = $db2->f("compt_mail");	
			$val = $db2->f("compt_cod");	
			$sujet = 'Aventurier inactif dans les souterrains de Delain';	
			$texte_mail = "Bonjour,\r\n
l'aventurier " . $db->f("perso_nom") . " (n " . $db->f("perso_cod") . ") est en hibernation depuis plus de 80 jours.\r\n
Sans connection de votre part, il sera supprimé dans 10 jours.\r\n
Si vous souhaitez le conserver, il vous suffit de vous loguer sur votre compte, et de cliquer sur l'aventurier pour l'activer.\r\n
\r\n
Cordialement,\r\n
Merrick.\r\n
\r\n
(Attention, ce mail est envoyé par un robot, inutile d'y répondre. En cas de problème, merci de signaler l'anomalie sur le forum ( http://www.jdr-delain.net/forum/ ))";
			$hdrs = array(
              'From'    => 'Robot des souterrains <noreply@jdr-delain.net>',
              'Reply-to'	=> 'noreply@jdr-delain.net',
              'Return-Path' => 'noreply@jdr-delain.net',
              'Subject' => $sujet
              );
      	$crlf = "\n";
	      $mime = new Mail_mime($crlf);
			$mime->setTXTBody($texte_mail);
			$body = $mime->get();
			
			$hdrs = $mime->headers($hdrs);
			
			$mail =& Mail::factory('mail');			
				
			$res = $mail->send($mail, $hdrs, $body);
			if (PEAR::isError($res))
			{
				echo 'Anomalie sur l\'adresse ' . $adr_mail;
				
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
		}
	}
}
