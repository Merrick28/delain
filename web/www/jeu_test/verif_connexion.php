<?php
include_once 'classes.php';
include_once "constantes.php";
include_once G_CHE . 'ident.php';
if (!$verif_auth)
{
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}


$perso_cod = $perso->perso_cod;
if (empty($perso_cod))
{
    if (isset($auth))
    {
        $auth->logout();
    }
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}

if ($compte->compt_hibernation == 'O')
{
    $phrase = 'Votre compte est en hibernation ! ';
    header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
    die();
}

page_close();
$nom_template = 'general';
