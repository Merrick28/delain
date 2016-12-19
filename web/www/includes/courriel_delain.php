<?php

// /!\ vérifier les includes ! Tous requis ??
require_once '/home/delain/public_html/www/includes/class.smtp.inc';
require_once('Mail.php');
require_once('Mail/mime.php');

class courriel_delain
{

    var $destinataires;
    var $corps;
    var $sujet;
    var $expediteur;
    var $nomExpediteur;
    var $erreurs;

    function courriel_delain()
    {
        $this->corps         = '';
        $this->sujet         = '';
        $this->expediteur    = 'noreply@jdr-delain.net';
        $this->nomExpediteur = 'Les Souterrains de Delain';
        $this->destinataires = array();
    }

    function ajouteDestinataire($adresse)
    {
        if (false === array_search($adresse, $this->destinataires))
        {
            $this->destinataires[] = $adresse;
        }
    }

    function envoyer()
    {
        /*         * *************************************
         * * Setup some parameters which will be 
         * * passed to the smtp::connect() call.
         * ************************************* */
        $params         = array();
        $params['host'] = 'localhost';       // The smtp server host/ip
        $params['port'] = 25;                // The smtp server port
        $params['helo'] = 'jdr-delain.net';  // What to use when sending the helo command. Typically, your domain/hostname
        $params['auth'] = false;             // Whether to use basic authentication or not
        $params['user'] = 'testuser';        // Username for authentication
        $params['pass'] = 'testuser';        // Password for authentication

        /*         * *************************************
         * * These parameters get passed to the 
         * * smtp->send() call.
         * ************************************* */
        $send_params               = array();
        $send_params['recipients'] = $this->destinataires;
        $send_params['headers']    = array(
           'From: ' . $this->nomExpediteur . ' <' . $this->expediteur . '>',
           'To: ' . implode(',', $this->destinataires),
           'Reply-to: ' . $this->expediteur,
           'Return-Path: ' . $this->expediteur,
           'Subject: =?UTF-8?B?' . base64_encode($this->sujet) . '?=',
           'Content-Type: text/plain; charset="UTF-8"'
        );
        $send_params['from']       = $this->expediteur; // This is used as in the MAIL FROM: cmd
        // It should end up as the Return-Path: header
        $send_params['body']       = $this->corps;      // The body of the email

        $smtp = new smtp($params);

        $ok = ($smtp->connect() && $smtp->send($send_params));

        if (!$ok)
        {
            $this->erreurs = $smtp->errors;
        }
        return $ok;
    }

}

?>
