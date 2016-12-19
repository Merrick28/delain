<html>
    <?php
//include "connexion.php";
    include "includes/classes.php";
    include "includes/incl_mail.php";
    $compte = new compte;
    ?>
    <link rel="stylesheet" type="text/css" href="style.css" title="essai">
    <head>
    </head>
    <body background="images/fond5.gif">
        <?php
// Affichage du tableau de résultat 
        echo("<table background=\"images/fondparchemin.gif\" width = \"90%\" bgcolor=\"#EBE7E7\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n");
        echo("<tr>\n");
        echo("<td width=\"10\" background=\"images/coin_hg.gif\"><img src=\"images/del.gif\" height=\"8\" width=\"10\"></td>\n");
        echo("<td background=\"images/ligne_haut.gif\"><img src=\"images/del.gif\" height=\"8\" width=\"10\"></td>\n");
        echo("<td width=\"10\" background=\"images/coin_hd.gif\"><img src=\"images/del.gif\" height=\"8\" width=\"10\"></td>\n");
        echo("</tr>\n");


        echo("<tr>\n");
        echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
        echo("<td>\n");

        $erreur = 0;

        if ($erreur == 0)
        {
            if (!isset($nom) || !isset($mail))
            {
                $erreur = -1;
                echo("<p>Erreur de paramètres : nom de compte ou adresse électronique non renseignés ou perdus.</p>\n");
                echo("<form name=\"retour\" method=\"post\" action=\"formu_cree_compte.php\">\n");
                echo("<Input type=\"submit\" class=\"test\" value=\"Retourner à l’étape précédente\">\n");
            }
        }
        if ($erreur == 0)
        {
            $recherche  = "SELECT f_cherche_compte('$nom') as recherche";
            $db->query($recherche);
            $db->next_record();
            $tab_nom[0] = $db->f("recherche");
            if ($tab_nom[0] != -1)
            {
                $erreur = -1;
                echo("<p>Un aventurier porte déjà ce nom !!!</p>\n");
                echo("<form name=\"retour\" method=\"post\" action=\"formu_cree_compte.php\">\n");
                echo("<Input type=\"submit\" class=\"test\" value=\"Retourner à l’étape précédente\">\n");
            }
        }
        if ($erreur == 0)
        {
            if ($compte->getBy_compt_mail(strtolower($mail)))
            {
                $erreur = -1;
                echo("<p>Un compte existe déjà avec cette adresse e-mail !!!</p>\n");
                echo("<form name=\"retour\" method=\"post\" action=\"formu_cree_compte.php\">\n");
                echo("<Input type=\"submit\" class=\"test\" value=\"Retourner à l’étape précédente\">\n");
            }
        }
        if ($erreur == 0)
        {
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
            {
                $erreur = -1;
                echo("<p>Adresse électronique non valide !</p>\n");
                echo $valide[1];
                echo("<form name=\"retour\" method=\"post\" action=\"formu_cree_compte.php\">\n");
                echo("<Input type=\"submit\" class=\"test\" value=\"Retourner à l’étape précédente\">\n");
            }
        }
        if (!isset($regles) || $regles != 1)
        {
            $erreur = -1;
            echo("<p>Vous devez accepter la charte pour jouer !</p>\n");
            echo("<form name=\"retour\" method=\"post\" action=\"formu_cree_compte.php\">\n");
            echo("<Input type=\"submit\" class=\"test\" value=\"Retourner à l’étape précédente\">\n");
        }
        if ($erreur == 0)
        {
            $recherche                    = "select  lancer_des(1, 10000) as des";
            $db->query($recherche);
            $db->next_record();
            $validation                   = $db->f("des");
            // calcul du nombre aléatoire pour validation 
            $compte->compt_nom            = $nom;
            $compte->compt_mail           = strtolower($mail);
            $compte->compt_password       = '';
            $compte->compt_validation     = $validation;
            $compte->compt_actif          = 'N';
            $compte->compt_dcreat         = date('Y-m-d H:i:s');
            $compte->compt_acc_charte     = 'O';
            $compte->compt_type_quatrieme = 2;
            $compte->compt_passwd_hash    = crypt($pass1);
            $compte->stocke(true);


            $texte_mail = "Bonjour,\r\n\r\n";
            $texte_mail = $texte_mail . "Vous venez de créer un compte dans les mondes souterrains de Delain.\r\n";
            $texte_mail = $texte_mail . "Afin de pouvoir valider ce compte, merci d’aller sur le lien suivant :\r\n";
            $texte_mail = $texte_mail . $type_flux . G_URL . "validation_compte2.php?nom=$nom&validation=$validation\r\n";
            $texte_mail = $texte_mail . "(Si vous avez des espaces dans le lien, il faut faire un copier coller du lien en entier, il doit se terminer par une série de 4 chiffres)\r\n";
            $texte_mail = $texte_mail . "Vos aventuriers ne pourront être créés qu’après cette formalité.\r\n";
            $texte_mail = $texte_mail . "\r\nBon jeu, et n’oubliez pas de venir nous faire un petit bonjour sur le forum !\r\n";
            $texte_mail = $texte_mail . "\r\nL’équipe des Souterrains de Delain\r\n";
            $texte_mail = $texte_mail . $type_flux . G_URL . "\r\n";
            include 'includes/class.smtp.inc';

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
               'From: "Merrick" <merrick@jdr-delain.net>', // Headers
               'To: ' . $mail, 'Subject: Inscription à Delain',
               'Content-Type: text/plain; charset="UTF-8"'
            );
            $send_params['from']       = 'merrick@jdr-delain.net';         // This is used as in the MAIL FROM: cmd
            // It should end up as the Return-Path: header
            $send_params['body']       = $texte_mail;          // The body of the email

            $smtp = new smtp($params);

            if ($smtp->connect() && $smtp->send($send_params))
            {
                echo("<br><p>Un mail vous a été adressé à l’adresse $mail pour validation\n");
                echo("<p>Vous ne pourrez jouer qu’une fois ce mail reçu</p>\n");
                //print_r($smtp->errors);
            }
            else
            {
                echo 'Le mail n’a pas pu vous être envoyé, à cause des erreurs ci dessous : ';
                echo $texte_mail;
                // The reason for failure should be in the errors variable
                print_r($smtp->errors);
            }
        }

        echo("</td>\n");
        echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
        echo("</tr>\n");
        echo("<tr>\n");
        echo("<td width=\"10\" background=\"images/coin_bg.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
        echo("<td background=\"images/ligne_bas.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
        echo("<td width=\"10\" background=\"images/coin_bd.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
        echo("</tr>\n");
        echo("</table>\n");
        ?>
        <!-- Google Code for inscriptiotn Conversion Page -->
        <script type="text/javascript">
            /* <![CDATA[ */
            var google_conversion_id = 1067650967;
            var google_conversion_language = "fr";
            var google_conversion_format = "3";
            var google_conversion_color = "ffffff";
            var google_conversion_label = "73PkCJ2BhwIQl5-M_QM";
            var google_conversion_value = 0;
            if (0) {
                google_conversion_value = 0;
            }
            /* ]]> */
        </script>
        <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
        </script>
        <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1067650967/?value=0&label=73PkCJ2BhwIQl5-M_QM&guid=ON&script=0"/>
        </div>
        </noscript>
    </div>
</body>
</html>
