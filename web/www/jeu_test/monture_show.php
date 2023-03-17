<?php

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

$arr_img = [
    "1:0"   => "N.png",
    "-1:0"  => "S.png",
    "0:1"   => "E.png",
    "0:-1"  => "W.png",
    "1:1"   => "NE.png",
    "1:-1"  => "NW.png",
    "-1:1"  => "SE.png",
    "-1:-1" => "SW.png",
    "0:0"   => "Q.png",
    "TALONNER"   => "kick.png",
    "SAUTER"     => "jump.png",
];

$contenu_page = '';

$perso = new perso();
$perso->charge($perso_cod);
$cavalier_cod = $perso->est_chevauche() ;

if ( $perso->perso_type_perso != 2  ) {
    $contenu_page .= "<br><p>Vous n'êtes pas une monture !!! </p><br>";
}else if ( ! $cavalier_cod ){
    $contenu_page .= "<br><p>Vous n'êtes pas chevauché par un cavalier!</p><br>";
} else {
    //$directions = [ "N"=>"N (nord)", "NE"=>"NE (nord-est)", "E"=>"E (est)", "SE"=>"SE (sud-est)", "S"=>"S (sud)", "SO"=>"SO (sud-ouest)", "O"=>"O (ouest)","NO"=>"NO (nord-ouest)"  ];

    $cavalier = new perso();
    $cavalier->charge( $cavalier_cod )  ;

    $contenu_page .= "<br><p>Vous êtes actuellement chevauché par : <a href=\"visu_desc_perso.php?visu=".$cavalier->perso_cod."\">".$cavalier->perso_nom."</a></p>";
    $contenu_page .= "<hr>";

    // affichage des ordres actifs =====================================================================================
    $contenu_page .= "<br><b><u>Liste des ordres actifs</u></b>: <br> <br>";
    $ordres = json_decode($perso->perso_misc_param) ;
    if (isset($ordres->ia_monture_ordre) && is_array($ordres->ia_monture_ordre) && sizeof($ordres->ia_monture_ordre) >0)
    {
        $contenu_page .= '<form name="monture_dep" id="monture_dep" method="post" action="monture_ordre.php"><table style="border: 1px solid black;">';
        $contenu_page .= '<input type="hidden" name="num_ordre" id="num_ordre" value="">';
        $a_ordres = [] ;
        foreach ($ordres->ia_monture_ordre as $k => $o)
        {
            $a_ordres[$o->ordre] = $k ;
        }
        ksort($a_ordres);
        foreach ($a_ordres as $k)
        {
            $o = $ordres->ia_monture_ordre[$k] ;
            $img = "<img style='margin:3px; vertical-align: middle;' src='/images/interface/".$arr_img[$o->dir_y.":".$o->dir_x]."'>";
            $contenu_page .=  "<tr><td><span>&nbsp;&nbsp;N° {$o->ordre} : ";

            if ($o->type_ordre=="TALONNER") {
                $contenu_page .= "<img style='margin:3px; vertical-align: middle;' src='/images/interface/".$arr_img["TALONNER"]."'>";;
            } else {
                if ($o->type_ordre=="SAUTER") {
                    $contenu_page .= "<img style='margin:3px; vertical-align: middle;' src='/images/interface/" . $arr_img["SAUTER"] . "'>";;
                }
                for($i=0; $i<$o->dist; $i++) $contenu_page .= $img;
            }
            $contenu_page .= "&nbsp;&nbsp;</span></td></tr>" ;
        }
        $contenu_page .= '</table></form>';
    }
    $contenu_page .= "<hr>";

}


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
