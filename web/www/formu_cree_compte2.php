<?php
if (!isset($_POST['nom']) || !isset($_POST['mail']))
{
    $template = $twig->load('formu_cree_compte.twig');
    $options_twig = array(
        'ERROR_MESSAGE' => "Erreur de paramètres : nom de compte ou adresse électronique non renseignés"
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
    die('');
}
$compte = new compte;
if($compte->getByNom($nom))
{
    $template = $twig->load('formu_cree_compte.twig');
    $options_twig = array(
        'ERROR_MESSAGE' => "Un aventurier porte déjà ce nom"
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
    die('');
}


if ($compte->getBy_compt_mail(strtolower($mail)))
{
    $template = $twig->load('formu_cree_compte.twig');
    $options_twig = array(
        'ERROR_MESSAGE' => "Un compte existe déjà avec cette adresse mail"
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
    die('');
}


if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
{
    $template = $twig->load('formu_cree_compte.twig');
    $options_twig = array(
        'ERROR_MESSAGE' => "Adresse mail non valide"
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
    die('');
}

if (!isset($regles) || $regles != 1)
{
    $template = $twig->load('formu_cree_compte.twig');
    $options_twig = array(
        'ERROR_MESSAGE' => "Vous devez accepter la charte des joueurs pour continuer"
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
    die('');
}

$validation = rand(1000,9999);
// calcul du nombre aléatoire pour validation
$compte->compt_nom = $nom;
$compte->compt_mail = strtolower($mail);
$compte->compt_password = '';
$compte->compt_validation = $validation;
$compte->compt_actif = 'N';
$compte->compt_dcreat = date('Y-m-d H:i:s');
$compte->compt_acc_charte = 'O';
$compte->compt_type_quatrieme = 2;
$compte->compt_passwd_hash = crypt($pass1, sha1(microtime(true)));
$compte->stocke(true);


// on prépare le texte du mail
$template = $twig->load('mails/forum_cree_compte2.twig');

$options_twig = array(
    'TYPE_FLUX'  => $type_flux,
    'URL'        => G_URL,
    'NOM'        => $nom,
    'VALIDATION' => $validation
);
$corps_mail = $template->render(array_merge($options_twig_defaut, $options_twig));
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);
// smtp
$mail->Host = SMTP_HOST;
$mail->Port = SMTP_PORT;
if(defined('SMTP_USER'))
{
    if (!empty(SMTP_USER))
    {
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASSWORD;
    }
}


$mail->IsHTML(true);
$mail->IsHTML(true);
$mail->CharSet = 'utf-8';
$mail->From = 'noreply@jdr-delain.net';
$mail->FromName = 'Le robot des souterrains';
$mail->AddAddress($compte->compt_mail);
$mail->Subject = 'Inscription à Delain';
$mail->Body = $corps_mail;
try{
    $mail->Send();
    $template = $twig->load('formu_cree_compte2.twig');
    $options_twig = array(
        'MAIL' => $compte->compt_mail
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
}
catch (Exception $e) {
    $template = $twig->load('formu_cree_compte2.twig');
    $options_twig = array(
        'ERROR_MESSAGE' => print_r($mail->ErrorInfo, true)
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
}
unset($mail);


