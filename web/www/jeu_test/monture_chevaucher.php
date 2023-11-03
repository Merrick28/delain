<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';

$perso = new perso();
$perso->charge($perso_cod);
$liste_monture = $perso->monture_chevauchable();

if ($perso->perso_type_perso == 3){
    $contenu_page .= "<br><p>Les familiers ne peuvent pas chevaucher de monture!</p><br>";
} else if ($perso->perso_monture){
    $contenu_page .= "<br><p>Vous êtes déjà sur une monture</p><br>";
}else if (count($liste_monture)==0  ){
    $contenu_page .= "<br><p>Il n'y a pas de monture ici!</p><br>";
} else {
    if (count($liste_monture)>1)
        $contenu_page .= "<br><p>Choisissez votre monture:</p><br>";
    else
        $contenu_page .= "<br><p>Voulez-vous chevaucher cette monture:</p><br>";
    $contenu_chevaucher = "" ;
    $contenu_siffler = "" ;
    foreach ($liste_monture as $m => $monture) {
        if ($monture["dist"]==0){
            $contenu_chevaucher .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"action.php?methode=chevaucher&monture={$monture["perso_cod"]}\">{$monture["perso_nom"]}</a></p>";
        } else {
            $contenu_siffler .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"action.php?methode=chevaucher&monture={$monture["perso_cod"]}\">{$monture["perso_nom"]}</a></p>";
        }
    }
    if ($contenu_chevaucher!="") {
        $contenu_page .= $contenu_chevaucher;
        $contenu_page .= "<br><strong>NOTA</strong>: Cette action nécessite 4 PA.<br><br>";
    }
    if ($contenu_siffler!="") {
        $contenu_page .= "Il y a une de vos anciennes monture est à proximité, elle vous reconnait!<br>";
        $contenu_page .= "Vous pouvez la <b>siffler</b> pour la fire venir, puis la <b>chevaucher</b> au passage:<br><br>";
        $contenu_page .= $contenu_siffler;
        $contenu_page .= "<br><strong>NOTA</strong>: Cette action nécessite <b>6 PA</b>.<br>";
    }
    $contenu_page .= "<br>";
}


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
