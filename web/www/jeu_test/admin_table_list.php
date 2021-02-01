<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/tools.php';

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');


$droit_modif = 'dcompt_animations';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ( $erreur == 0 ) {
    $template = $twig->load('admin_table_list.twig');
    $options_twig = array(

        '__VERSION' => $__VERSION,
        'TABLE' => $_REQUEST["table"],
    );
    echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));

} else {

    $contenu_page = '<p>Vous n\'avez pas accès à cette page!</p>';

    include "blocks/_footer_page_jeu.php";
}


