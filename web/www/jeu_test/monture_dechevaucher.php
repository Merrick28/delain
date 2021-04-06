<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';

$perso = new perso();
$perso->charge($perso_cod);
$liste_monture = $perso->monture_chevauchable();

if (! $perso->perso_monture){
    $contenu_page .= "<br><p>Vous n'êtes pas sur une monture!</p><br>";
} else {

    $contenu_page .= "<br><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voulez-vous <a href=\"action.php?methode=dechevaucher\">mettre pied à terre?</a></p>";
    $contenu_page .= "<br><strong>NOTA</strong>: Cette action nécessite 4 PA.<br>";
    $contenu_page .= "<br>";
}


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
