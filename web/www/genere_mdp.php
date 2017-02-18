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



$template = $twig->load('genere_mdp.twig');

$options_twig = array(
   'COMPTE' => $compte,
);
echo $template->render(array_merge($options_twig_defaut,$options_twig));


