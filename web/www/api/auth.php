<?php


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // on récupère les variables json
    $inputJSON = file_get_contents('php://input');
    $input     = json_decode($inputJSON, TRUE);
    $login     = $input['login'];
    $password  = $input['password'];

    $compte = new compte;
    if ($compte->getByLoginPassword($login, $password))
    {
        // on est authentifié
        $token = uniqid('',true);
        die('');
    }
    else
    {
        header('HTTP/1.0 403 Forbidden');
        die('Authentification échouée.');
    }
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');


