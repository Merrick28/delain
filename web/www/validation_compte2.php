<?php

function error_validation($message='Compte non trouvé ou code inccorect')
{
    global $twig;
    global $options_twig_defaut;
    $template = $twig->load('validation_compte2.twig');
    $options_twig = array(
        'ERROR_MESSAGE' => $message
    );
    echo $template->render(array_merge($options_twig_defaut, $options_twig));
    die('');
}
$compte = new compte;
if($compte->getByNom($_GET['nom']))
{
    if($compte->compt_actif == 'O')
    {
        error_validation('Compte déjà activé !');
    }
    if($compte->compt_validation == $_GET['validation'])
    {
        $compte->compt_actif = 'O';
        $compte->compt_dcreat = date('Y-m-d H:i:s');
        $compte->stocke();
        $template = $twig->load('validation_compte2.twig');
        $options_twig = array();
        echo $template->render(array_merge($options_twig_defaut, $options_twig));

        /***************************************************************************/
        /* Marlyza - 2018-08-30 - Envoi d'un message à l'attention des admins      */
        /***************************************************************************/
        $titre = "Nouvelle création de compte";
        $corps = "Chers amis,<br>Je vous informe de la création d'un nouveau compte: <a href=\"/jeu_test/detail_compte.php?vcompte={$compte->compt_cod}\">{$compte->compt_nom}</a> pour <a href=\"mailto:{$compte->compt_mail}\">{$compte->compt_mail}</a><br>Amicalement,<br>Gildwen.";
        $mess_admin = new messagerie_admin();
        $mess_admin->send_message($titre, $corps);

        // et ajout de la creation du compte dans un fihier de log pour suivi dans la partie admin
        $log = "Activation du compte  <a href=\"/jeu_test/detail_compte.php?vcompte={$compte->compt_cod}\">{$compte->compt_nom}</a> pour <a href=\"mailto:{$compte->compt_mail}\">{$compte->compt_mail}</a>";
        writelog($log,'compte_creation');
        die('');
    }
    else
    {
        error_validation();
    }
}
else
{
    error_validation();
}
?>

