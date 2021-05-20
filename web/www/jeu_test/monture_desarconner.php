<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';

$perso = new perso();
$perso->charge($perso_cod);
$liste_cavalier = $perso->monture_desarconnable();

if ($perso->perso_type_perso == 3){
    $contenu_page .= "<br><p>Les familiers ne peuvent pas désarçonner un cavalier!</p><br>";
}else if (count($liste_cavalier)==0  ){
    $contenu_page .= "<br><p>Il n'y a pas de cavalier ici!</p><br>";
} else {
    if (count($liste_cavalier)>1)
        $contenu_page .= "<br><p>Choisissez le cavalier à désarçonner:</p><br>";
    else
        $contenu_page .= "<br><p>Voulez-vous désarçonner ce cavalier:</p><br>";
    foreach ($liste_cavalier as $c => $cavalier) {
        $contenu_page .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"action.php?methode=desarconner&cavalier={$cavalier["perso_cod"]}\">{$cavalier["perso_nom"]}</a> monté sur {$cavalier["monture_perso_nom"]}</p>";
    }
    $contenu_page .= "<br><strong>NOTA</strong>: Cette action nécessite 6 PA.<br>";
    $contenu_page .= "<br>";
}


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
