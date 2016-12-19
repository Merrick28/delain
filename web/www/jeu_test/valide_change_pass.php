<?php

include_once "verif_connexion.php";
include '../includes/template.inc';

$compte = new compte;

$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$db           = new base_delain;
$erreur       = 0;
if ($nouveau1 != $nouveau2)
{
    echo("<p>Erreur ! Le nouveau mot de passe et sa vérification sont différents !");
    $erreur = 1;
    echo("<p><a href=\"change_pass.php\">Retour !</a>");
}

 if (crypt($ancien, $compte->compt_passwd_hash)!= $compte->compt_passwd_hash)
{
    echo("<p>Erreur ! L'ancien mot de passe est erronné !");
    echo "<!--" . $compte->compt_passwd_hash . " - " . crypt($ancien, $compte->compt_passwd_hash) . " -->";
    $erreur = 1;
    echo("<p><a href=\"change_pass.php\">Retour !</a>");
}

if ($erreur == 0)
{
    $compte->compt_passwd_hash = crypt($nouveau1);
    $compte->sotcke();
   

    echo("<p>Le mot de passe a été changé avec succès !");

    echo("<p><a href=\"change_pass.php\">Retour !</a>");
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');

