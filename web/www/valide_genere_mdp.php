<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$compte = new compte;
if (!$compte->charge($_POST['compt_cod']))
{
    die('Compte non trouvé');
}
if ($compte->compt_validation != $_POST['token'])
{
    die('Code de validation non correct');
}


$loader = new Twig_Loader_Filesystem(CHEMIN . '/../templates');

// on efface le password "normal", au cas où
$compte->compt_password = '';
// on met le nouveau
$compte->compt_passwd_hash = crypt($nouveau1);
// on enregistre
$compte->stocke();

$twig     = new Twig_Environment($loader, array());
$template = $twig->loadTemplate('valide_genere_mdp.twig');

$options_twig = array(
   'URL'        => G_URL,
   'URL_IMAGES' => G_IMAGES
);
echo $template->render($options_twig);
