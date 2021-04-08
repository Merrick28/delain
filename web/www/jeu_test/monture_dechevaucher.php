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

    // on vérifie si c'est raisonnaible de vouloir descendre ici (terrain pourrait-être critique)

    $ppos = new perso_position();
    $ppos->getByPerso($perso_cod);
    $pos = new positions();
    $pos->charge($ppos->ppos_pos_cod);

    $param = new parametres();
    $nb_pa_dep = $param->getparm(9) + $pos->pos_modif_pa_dep;

    if ($nb_pa_dep>=12)
    {
        $contenu_page .= "<br><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Voulez nous pouvez pas descendre ici!!</strong></a></p>";
        $contenu_page .= "<br>Le terrain est tel que, sans votre monture, il vous serait peut-être impossible d'en repartir vivant!!!<br>";
        $contenu_page .= "<br>";
    }
    else
    {
        $contenu_page .= "<br><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Voulez-vous <a href=\"action.php?methode=dechevaucher\">mettre pied à terre?</a></p>";
        $contenu_page .= "<br><strong>NOTA</strong>: Cette action nécessite 4 PA.<br>";
        $contenu_page .= "<br>";
    }
}


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
