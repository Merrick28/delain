<?php 
include 'includes/classes.php';
include 'includes/class.smtp.inc';
//
$db = new base_delain;
$db2 = new base_delain;
//
// Etape 1 : on regarde tous les persos pas loin d'être finis....
// On commence par les non hibernés
//
$req = 'select perso_cod,perso_nom from persowhere perso_type_perso = 1
		and perso_actif = \'O\'
		and perso_der_connex + (\'20 days\')::interval < now()
		and perso_pnj != 1
		and perso_mail_inactif_envoye = 0 ';
$db->query($req);
if($db->nf() != 0)
{
	while($db->next_record())	
	{
		$req = 'select compt_mail
			from compte,perso_compte
			where pcompt_perso_cod = ' . $db->f('perso_cod') . '
			and pcompt_compt_cod = compt_cod ';
		$db2->query($req);
		if ($db2->nf() == 0)
        {
            echo 'Erreur sur perso n°' . $db->f('perso_cod') . ', compte non trouvé !';
        }
        else
        {
            $db2->next_record();
            $mail           = $db2->f("compt_mail");
            $sujet          = 'Aventurier inactif dans les souterrains de Delain';
            $texte_mail = "Bonjour,\r\n
l'aventurier " . $db->f("perso_nom") . " (n°" . $db->f("perso_cod") . ") est inactif depuis plus de 20 jours.\r\n
Sans connection de votre part, il sera supprimé dans 10 jours.\r\n
Si vous souhaitez le conserver, il vous suffit de vous loguer sur votre compte, et de cliquer sur l'aventurier pour l'activer.\r\n
\r\n
Cordialement,\r\n
Merrick.\r\n
\r\n
(Attention, ce mail est envoyé par un robot, inutile d'y répondre. En cas de problème, merci de signaler l'anomalie sur le forum ( http://www.jdr-delain.net/forum/ ))";
            /*             * *************************************
             * * Setup some parameters which will be 
             * * passed to the smtp::connect() call.
             * ************************************* */
            $params['host'] = 'localhost';    // The smtp server host/ip
            $params['port'] = 25;      // The smtp server port
            $params['helo'] = 'jdr-delain.net';   // What to use when sending the helo command. Typically, your domain/hostname
            $params['auth'] = false;      // Whether to use basic authentication or not
            $params['user'] = 'testuser';    // Username for authentication
            $params['pass'] = 'testuser';    // Password for authentication

            /*             * *************************************
             * * These parameters get passed to the 
             * * smtp->send() call.
             * ************************************* */
            $send_params['recipients'] = array($mail);       // The recipients (can be multiple)
            $send_params['headers']    = array(
               'From: "Robot des souterrains" <no-reply@jdr-delain.net>', // Headers
               'To: ' . $mail, 'Subject: ' . $sujet
            );
            $send_params['from']       = 'no-reply@jdr-delain.net';         // This is used as in the MAIL FROM: cmd
            $send_params['body']       = $texte_mail;          // The body of the email
            if (is_object($smtp                      = smtp::connect($params)) AND $smtp->send($send_params))
            {
                echo 'Email bien envoyé à ' . $mail . "\r\n";

                // Any recipients that failed (relaying denied for example) will be logged in the errors variable.
                //print_r($smtp->errors);
                $req = 'update perso set perso_mail_inactif_envoye = 1 where perso_cod = ' . $db->f("perso_cod");
                $db->query($req);
            }
            else
            {
                echo 'Anomalie sur l\'adresse ' . $mail;

                // The reason for failure should be in the errors variable
                print_r($smtp->errors);
            }
        }
    }
}
//
// Etape 2 : on regarde tous les persos pas loin d'être finis....
// On finit par les hibernés
//
$req = 'select perso_cod,perso_nom from persowhere perso_type_perso = 1
		and perso_actif = \'H\'
		and perso_der_connex + (\'80 days\')::interval < now()
		and perso_pnj != 1
		and perso_mail_inactif_envoye = 0 ';
$db->query($req);
if($db->nf() != 0)
{
	while($db->next_record())	
	{
		$req = 'select compt_mail
			from compte,perso_compte
			where pcompt_perso_cod = ' . $db->f('perso_cod') . '
			and pcompt_compt_cod = compt_cod ';
		$db2->query($req);
		if($db2->nf() == 0)
			echo 'Erreur sur perso n°' . 	$db->f('perso_cod') . ', compte non trouvé !';
		else
		{
			$db2->next_record();
			$mail = $db2->f("compt_mail");	
			$sujet = 'Aventurier inactif dans les souterrains de Delain';	
			$texte_mail = "Bonjour,\r\n
l'aventurier " . $db->f("perso_nom") . " (n°" . $db->f("perso_cod") . ") est inactif depuis plus de 20 jours.\r\n
Sans connection de votre part, il sera supprimé dans 10 jours.\r\n
Si vous souhaitez le conserver, il vous suffit de vous loguer sur votre compte, et de cliquer sur l'aventurier pour l'activer.\r\n
\r\n
Cordialement,\r\n
Merrick.\r\n
\r\n
(Attention, ce mail est envoyé par un robot, inutile d'y répondre. En cas de problème, merci de signaler l'anomalie sur le forum ( http://www.jdr-delain.net/forum/ ))";
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
			$send_params['recipients']	= array($mail);							// The recipients (can be multiple)
			$send_params['headers']		= array(
											'From: "Robot des souterrains" <no-reply@jdr-delain.net>',	// Headers
											'To: ' . $mail , 'Subject: ' . $sujet
									   	);
			$send_params['from']		= 'no-reply@jdr-delain.net';									// This is used as in the MAIL FROM: cmd
			$send_params['body']		= $texte_mail;										// The body of the email
			if(is_object($smtp = smtp::connect($params)) AND $smtp->send($send_params))
			{
				echo 'Email bien envoyé à '.$mail."\r\n";
		
				// Any recipients that failed (relaying denied for example) will be logged in the errors variable.
				//print_r($smtp->errors);
				$req = 'update perso set perso_mail_inactif_envoye = 1 where perso_cod = ' . $db->f("perso_cod");
				$db->query($req);	
			}
			else
			{
				echo 'Anomalie sur l\'adresse ' . $mail;
				
				// The reason for failure should be in the errors variable
				print_r($smtp->errors);
			}
		}
	}
}
?>