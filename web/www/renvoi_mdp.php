<?php
include "includes/classes.php";
$db = new base_delain;
?>
<html>

    <link rel="stylesheet" type="text/css" href="../style.css" title="essai">
    <head>
    </head>

    <body background="../images/fond5.gif">
        <?php
        include "jeu_test/tab_haut.php";
        if (!isset($methode))
        {
            $methode = "debut";
        }
        switch ($methode)
        {
            case "debut":
                echo "<p class=\"titre\">Mot de passe perdu ?</p>";
                echo "<p>Pour vous le faire renvoyer, vous pouvez :<br>";
                echo "<form name=\"mdp\" action=\"renvoi_mdp.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"fin\">";
                echo "<input type=\"hidden\" name=\"type\" value=\"nom\">";
                echo "<p>Entrer votre nom de compte (attention aux différences majuscules/minuscules). Votre mot de passe vous sera envoyé à l'adresse ayant servi à créer le compte.";
                echo "<p>Nom de compte : <input type=\"text\" name=\"nom\"><br>";
                echo "<center><input type=\"submit\" class=\"test\" value=\"Renvoyer !\"></center></form>";

                echo "<form name=\"mdp\" action=\"renvoi_mdp.php\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"fin\">";
                echo "<input type=\"hidden\" name=\"type\" value=\"mail\">";
                echo "<p>Vous pouvez également rentrer l'adresse mail ayant servi à créer le compte.";
                echo "<p>Adresse mail : <input type=\"text\" name=\"mail\"><br>";
                echo "<center><input type=\"submit\" class=\"test\" value=\"Renvoyer !\"></center></form>";
                break;
            case "fin":
                $compte = new compte;
                $ok     = true;
                if ($type == "nom")
                {
                    if (!$result = $compte->getBy_compt_nom($nom))
                    {
                        echo "<p>Aucun compte trouvé à ce nom ";
                        $ok = false;
                    }
                }
                if ($type == "mail")
                {
                    if (!$result = $compte->getBy_compt_mail(strtolower($mail)))
                    {
                        echo "<p>Aucun compte trouvé à ce nom ";
                        $ok = false;
                    }
                }
                if ($ok)
                {

                    // on hydrate avec le bon compte
                    $compte->charge($result[0]->compt_cod);
                    // on met à jour une clé de validation
                    $compte->compt_validation = rand(1000,9999);
                    // et on enregistre
                    $compte->stocke();

                    // on charge la classe des templates
                    Twig_Autoloader::register();
                    $loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');

                    $twig     = new Twig_Environment($loader, array());
                    $template = $twig->loadTemplate('mails/renvoi_mdp1.twig');

                    $options_twig = array(
                       'COMPTE' => $compte,
                       'URL'    => G_URL
                    );
                    $corps_mail   = $template->render($options_twig);
                    //echo $corps_mail;
                    // on charge la classe mail
                    $mail       = new PHPMailer;
                    // smtp
                    $mail->Host = SMTP_HOST;
                    $mail->Port = SMTP_PORT;
                    if (!empty(SMTP_USER))
                    {
                        $mail->Username = SMTP_USER;
                        $mail->Password = SMTP_PASSWORD;
                    }

                    $mail->IsHTML(true);
                    $mail->CharSet  = 'utf-8';
                    $mail->From     = 'noreply@jdr-delain.net';
                    $mail->FromName = 'Le robot des souterrains';
                    $mail->AddAddress($compte->compt_mail);
                    $mail->Subject  = 'Changement de mot de passe';
                    $mail->Body     = $corps_mail;
                    if($mail->Send())
                    {
                        echo "Un mail vous a été envoyé avec les instructions pour générer un nouveau mot de passe";
                    }
                    else
                    {
                        echo "Erreur sur l'envoi du mail " . $mail->ErrorInfo;
                    }
                    unset($mail);
                    
                }
        }


        include "jeu_test/tab_bas.php";
        ?>
    </body>
</html>