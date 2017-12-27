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
        die('');
    }
    else
    {
        error_validation("mauvais code validation");
    }
}
else
{
    error_validation('Compte non trouvé');
}
?>

