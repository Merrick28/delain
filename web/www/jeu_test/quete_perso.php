<?php
include "blocks/_header_page_jeu.php";

define("APPEL",1);

$perso = $verif_connexion->perso;

$erreur = 0;
//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
if ($perso->is_perso_quete())
{
	$erreur = 0;
}
else
{
	$erreur = 1;
	$contenu_page .= 'Vous n’avez pas accès à cette page !';
}
$methode = get_request_var('methode', 'debut');
if ($erreur == 0)
{
    // Page de démarrage des quetes autos
    $quete = new aquete;
    $tab_quete = $quete->get_debut_quete($perso_cod);

    //echo "<pre>"; print_r($tab_quete);echo "</pre>";
    //die();

    $contenu_quete_auto = "" ;
    foreach ($tab_quete["quetes"] as $k => $quete)
    {

        $etape = new aquete_etape();
        $etape->charge($quete->aquete_etape_cod);

        $contenu_quete_auto .= "<!-- début $k => $quete->aquete_etape_cod -->";
        //$contenu_page .= "<strong>{$quete->aquete_nom}</strong><br><br>";

        $contenu_quete_auto .= "<div class=\"titre\" style=\"padding:5px;\"><center><strong>{$tab_quete["triggers"][$k]["nom"]}</strong></center></div>" ;
        $contenu_quete_auto .= "<br><u>Description de la quête</u> : ".$quete->aquete_description."<br><br>" ;

        //$contenu_quete_auto .= $etape->get_initial_texte($perso, $tab_quete["triggers"][$k]["nom"]);
        $contenu_quete_auto .= $etape->get_initial_texte($perso, $tab_quete["triggers"][$k]);
        $contenu_quete_auto .= "<br><br><hr><!-- fin $k => $quete->aquete_etape_cod -->";

    }

    // Les quêtes standards
    $is_perso_quete = $perso->is_perso_quete();
    if ($is_perso_quete)
    {
        $type_appel = 2;
        $tab_quete  = $perso->get_perso_quete();
        foreach($tab_quete as $key=>$val)
        {
            $contenu_page .= "<!-- début $key => $val -->";
            ob_start();
            $contenu_page .= "<div class=\"titre\" style=\"padding:5px;\"><center><strong>".ucfirst(str_replace("quete_", "", str_replace(".php", "", $val)))."</strong></center></div>" ;
            require_once $val;
            $contenu_page .= ob_get_contents();
            ob_end_clean();
            $contenu_page .= "<!-- fin $key => $val -->";
        }
    }
}

// Ajouter les nouvelles QA avant les anciennes.
$contenu_page = $contenu_quete_auto.$contenu_page;

include "blocks/_footer_page_jeu.php";
