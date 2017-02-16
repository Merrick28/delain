<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$compte = new compte;
if (!$compte->charge($_GET['compt_cod']))
{
    die('Compte non trouvé');
}
if ($compte->compt_validation != $_GET['token'])
{
    die('Code de validation non correct');
}

Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');

$twig     = new Twig_Environment($loader, array());
$template = $twig->loadTemplate('genere_mdp.twig');

$options_twig = array(
   'URL'        => G_URL,
   'URL_IMAGES' => G_IMAGES,
   'COMPTE' => $compte,
   'HTTPS' => $type_flux
);
echo $template->render($options_twig);


