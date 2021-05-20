<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 18/12/2018
 * Time: 16:26
 */

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

// on va maintenant charger toutes les variables liées au menu
include(G_CHE . '/jeu_test/variables_menu.php');
include(G_CHE . '/includes/constantes.php');


// perso/$compte sont chargés par variables_menu.php, restrictions de certaines pages aux montures (si le compte n'est pas admin)
if ($perso->est_chevauche() && !$compte->is_admin() && !$compte->is_admin_monstre())
{
   if( ! in_array($_SERVER["PHP_SELF"], [   "/jeu_test/switch.php",
                                            "/jeu_test/frame_vue.php",
                                            "/jeu_test/perso2.php",
                                            "/jeu_test/evenements.php",
                                            "/jeu_test/visu_desc_perso.php",
                                            "/jeu_test/visu_evt_perso.php",
                                        ]))
   {
       // Rediriger la page vers un message dédié
       $template     = $twig->load('template_jeu.twig');

       $options_twig = array(

           'CONTENU_PAGE'             => $contenu_page."<p class=\"bubble speech\">Vous ne pouvez pas faire ça avec une monture!<p><br><br>&nbsp;&nbsp;<img src=". G_IMAGES."/interface/bonus/EQI.png> "
       );
       echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
       die();
   }

}


//
//Contenu de la div de droite
//
$contenu_page = '';
