<?php
include "blocks/_header_page_jeu.php";
ob_start();

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
    $compte->compt_passwd_hash = crypt($nouveau1, sha1(microtime(true)));
    $compte->stocke();


    echo("<p>Le mot de passe a été changé avec succès !");

    echo("<p><a href=\"change_pass.php\">Retour !</a>");
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
