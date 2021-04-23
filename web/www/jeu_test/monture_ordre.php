<?php

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;


$contenu_page = '';

$perso = new perso();
$perso->charge($perso_cod);
$liste_cavalier = $perso->monture_ordonable();

if ($perso->perso_type_perso == 3){
    $contenu_page .= "<br><p>Les familiers ne peuvent diriger une monture!</p><br>";
}else if ( ! $perso->monture_ordonable()  ){
    $contenu_page .= "<br><p>Vous ne chevauchez pas (ou plus) de monture ou celle-ci ne sait pas obéir aux ordres!</p><br>";
} else {
    //$directions = [ "N"=>"N (nord)", "NE"=>"NE (nord-est)", "E"=>"E (est)", "SE"=>"SE (sud-est)", "S"=>"S (sud)", "SO"=>"SO (sud-ouest)", "O"=>"O (ouest)","NO"=>"NO (nord-ouest)"  ];

    $monture = new perso();
    $monture->charge( $perso->perso_monture );
    $contenu_page .= "<br><p>Vous chevaucher actuellement: <a href=\"visu_desc_perso.php?visu=".$monture->perso_cod."\">".$monture->perso_nom."</a></p>";

    // traitment des nouveau ordres:
    if (isset($_REQUEST["NOUVEL_ORDRE"]))
    {
        $contenu_page .= "<hr><b>ACTION</b>: Donner un ordre à la monture<br>";
        // check validité :
        $msg = "";
        if ( !isset($_REQUEST["distance"]) || (int)$_REQUEST["distance"]<=0 ) $msg .= "La distance de l'ordre n'est pas valide! ";
        if ( !isset($_REQUEST["direction"]) ) $msg .= "Vous n'avez pas sélectionné de direction! ";
        if ($msg != "")
        {
            $contenu_page .= $msg."<br>L'ordre n'est <b>pas valide</b>, les PA n'ont pas été depensés!<br>";
        }
        else
        {
            $contenu_page .= $perso->monture_ordre( "ADD", [ "dir" => $_REQUEST["direction"], "dist" => $_REQUEST["distance"] ] );
        }

        //print_r($_REQUEST);die();
        $contenu_page .= "<hr>";
    }

    $contenu_page .= "<br><b><u>Liste des ordres actifs</u></b>: <br>";

    $contenu_page .= "<br><b><u>Donner un nouvel ordre</u></b>: <br>Sélectionner la direction et la distance à parcourir:<br><br>";



    //$contenu_page .= 'Direction&nbsp;:&nbsp;'.create_selectbox("monture_dir", $directions, "N");
    //$contenu_page .= '&nbsp;&nbsp;Distance:&nbsp;<input name="monture_dist" size="4" value="1" >';
    //$contenu_page .= '&nbsp;&nbsp;<input type="submit" value="Ordoner (2 PA)"  class="test">';

    $contenu_page .= '<form name="monture_dep" id="monture_dep" method="post" action="monture_ordre.php"><table>';
    for ($l=1; $l<=3; $l++) {
        $contenu_page .= '<tr>';
        for ($c=1; $c<=3; $c++) {
            $contenu_page .= '<td width="50px;" class="soustitre2" style="text-align: center;">';
            if ($l==2 && $c==2){
                $contenu_page .=  '<input name="distance" size="2" value="1"  style="text-align: center;">';
            } else {
                $contenu_page .=  '<input type="radio" name="direction" value="' . ($c-2) . ":" . (2-$l)  . '" class="vide" ';
            }
            $contenu_page .= '</td>';
        }
        if ($l==1 && $c>3) $contenu_page .= '<td rowspan="3" class="soustitre2" style="text-align: center;">&nbsp;&nbsp;<input name="NOUVEL_ORDRE" type="submit" value="Ordoner (2 PA)"  class="test">&nbsp;&nbsp;</td>';
        $contenu_page .= '</tr>';
    }

    $contenu_page .= '</table></form>';

    $contenu_page .= "<br><br><hr><br></br>";
    include('vue_gauche.php');
    $contenu_page .= $vue_gauche ;
}


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));
