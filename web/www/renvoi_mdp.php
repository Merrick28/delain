<?php
include "includes/classes.php";
?>
<!DOCTYPE html>
<html>

<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="css/delain.css" rel="stylesheet">
<head>
    <title>Renvoi de mot de passe</title>
</head>

<body>
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

?>
<div class="bordiv">
    <?php
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
            if ($_REQUEST['type'] == "nom")
            {
                if (!$result = $compte->getBy_compt_nom($nom))
                {
                    echo "<p>Aucun compte trouvé à ce nom ";
                    $ok = false;
                }
            }
            if ($_REQUEST['type'] == "mail")
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
                $compte->compt_validation = rand(1000, 9999);
                // et on enregistre
                $compte->stocke();

                // on charge la classe des templates

                $template = $twig->load('mails/renvoi_mdp1.twig');

                $options_twig = array(
                    'COMPTE' => $compte
                );
                $corps_mail   = $template->render(array_merge($options_twig_defaut, $options_twig));
                //echo $corps_mail;
                // on charge la classe mail


                $mail = new PHPMailer;
                // smtp
                $mail->Host = SMTP_HOST;
                $mail->Port = SMTP_PORT;
                if (defined('SMTP_USER'))
                {
                    if (!empty(SMTP_USER))
                    {
                        $mail->Username = SMTP_USER;
                        $mail->Password = SMTP_PASSWORD;
                    }

                }


                $mail->IsHTML(true);
                $mail->CharSet  = 'utf-8';
                $mail->From     = 'noreply@jdr-delain.net';
                $mail->FromName = 'Le robot des souterrains';
                $mail->AddAddress($compte->compt_mail);
                $mail->Subject = 'Changement de mot de passe';
                $mail->Body    = $corps_mail;

                try
                {
                    $mail->Send();
                    echo "Un mail vous a été envoyé avec les instructions pour générer un nouveau mot de passe";
                } catch (Exception $e)
                {
                    echo "Erreur sur l'envoi du mail " . $mail->ErrorInfo;

                }
                unset($mail);
            }
    }
    ?>
</div>
</body>
</html>