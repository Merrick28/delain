<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';

$perso = new perso();
$perso->charge($perso_cod);
$liste_monture = $perso->monture_chevauchable();

if ($perso->perso_monture){
    $contenu_page .= "<br><p>Vous êtes déjà sur une monture</p><br>";
}else if (count($liste_monture)==0  ){
    $contenu_page .= "<br><p>Il n'y a pas de monture ici!</p><br>";
} else {
    if (count($liste_monture)>1)
        $contenu_page .= "<br><p>Choisissez votre monture:</p><br>";
    else
        $contenu_page .= "<br><p>Voulez-vous chevaucher cette monture:</p><br>";
    foreach ($liste_monture as $m => $monture) {
        $contenu_page .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"action.php?methode=chevaucher&monture={$monture["perso_cod"]}\">{$monture["perso_nom"]}</a></p>";
    }
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
