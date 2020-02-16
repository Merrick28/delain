<?php
/**
 * Verif connexion
 *
 * @return integer $perso_cod Numero de perso
 */
include_once 'classes.php';
include_once "constantes.php";
include_once G_CHE . 'ident.php';
if (!$verif_auth)
{
    /** @var string $type_flux */
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
    /** @var string $type_flux */
    header('Location:' . $type_flux . G_URL . 'inter.php');
    die();
}

if ($compte->compt_hibernation == 'O')
{
    $phrase = 'Votre compte est en hibernation ! ';
    /** @var string $type_flux */
    header('Location:' . $type_flux . G_URL . 'jeu_test/fin_session2.php?motif=' . $phrase);
    die();
}

