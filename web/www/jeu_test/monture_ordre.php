<?php

//=======================================================================================
function change_event_name($event, $attaquant, $cible) { return str_replace("[cible]", $cible, str_replace("[attaquant]", $attaquant, $event)); }


//=======================================================================================
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

$arr_img = [
    "1:0"        => "N.png",
    "-1:0"       => "S.png",
    "0:1"        => "E.png",
    "0:-1"       => "W.png",
    "1:1"        => "NE.png",
    "1:-1"       => "NW.png",
    "-1:1"       => "SE.png",
    "-1:-1"      => "SW.png",
    "0:0"        => "Q.png",
    "TALONNER"   => "kick.png",
    "SAUTER"     => "jump.png",
];


$arr_frequence = [
    "0"   => "Interdit",
    "1"   => "1 fois par DLT",
    "2"   => "2 fois par DLT",
    "0.5" => "1 fois toutes les 2 DLT",
];

$arr_reste_frequence = [
    "0"   => ["0" => "Interdit"],
    "1"   => ["1" => "aucune", "0" => "1 fois"],
    "2"   => ["2" => "aucune", "1" => "1 fois", "0" => "2 fois"],
    "0.5" => ["0.5" => "aucune sur 1 DLT", "1" => "aucune sur 2 DLT", "0" => "1 fois"],
];

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

    // on cherche la position du cavalier
    $perso_pos_desc = $perso->get_position();

    // calcul des options de montures sur l'atage
    $etage_monture = isset( $perso_pos_desc["etage"]->etage_monture ) && $perso_pos_desc["etage"]->etage_monture != "" ? json_decode( $perso_pos_desc["etage"]->etage_monture ) : [] ;
    $cout_ordre = isset( $etage_monture->pa_action ) ? $etage_monture->pa_action : 4 ;
    $freq_ordre_talonner = isset( $etage_monture->ordre_talonner ) ? $etage_monture->ordre_talonner : 0 ;
    $freq_ordre_sauter = isset( $etage_monture->ordre_sauter ) ? $etage_monture->ordre_sauter : 0 ;

    // AUTOMAP: Get content ====
    ob_start();
    include("ong_automap.php");
    $automap = ob_get_contents(); // data is now in here
    // supression de la form de changement d'étage
    $automap = substr($automap, strpos($automap, "</form>")+7) ;
    $automap = substr($automap, 0, strpos($automap, "<a")) . substr($automap, strpos($automap, "</a>")+4) ;
    ob_end_clean();

    //====== En of AUTOMAP content

    $monture = new perso();
    $monture->charge( $perso->perso_monture );
    $dist_vue =  max(2, $monture->distance_vue());
    $dist_max = min(8,  max(2, $monture->distance_vue()) ); // ordre entre mini 2 et maxi 8
    $contenu_page .= "<br><p>Vous chevaucher actuellement: <a href=\"visu_desc_perso.php?visu=".$monture->perso_cod."\">".$monture->perso_nom."</a></p>";
    $contenu_page .= "<hr>";

    // traitment des nouveaux ordres: ==================================================================================
    if (isset($_REQUEST["ORDRE_ADD"]))
    {

        // controle des actions speciale
        $perso_misc_param = json_decode($monture->perso_misc_param );
        $ia_monture = isset($perso_misc_param->ia_monture) ? $perso_misc_param->ia_monture : json_decode("{}");
        $nb_sauter = isset($ia_monture->nb_sauter) ? $ia_monture->nb_sauter : 0 ;
        $nb_talonner = isset($ia_monture->nb_talonner) ? $ia_monture->nb_talonner : 0 ;


        $contenu_page .= "<hr><b>ACTION</b>: Donner un ordre à la monture<br>";
        $msg = "";
        $type_ordre = (!isset($_REQUEST["ORDRE_TYPE"]) || ($_REQUEST["ORDRE_TYPE"]=="")) ? "DIRIGER" : $_REQUEST["ORDRE_TYPE"] ;
        $dir=explode(":", isset($_REQUEST["direction"]) ? $_REQUEST["direction"] : "0:0" );
        $dir_x = (int)$dir[0];
        $dir_y = (int)$dir[1];
        $dist = isset($_REQUEST["distance"]) ? (int)$_REQUEST["distance"] : 1 ;
        $type_action = ($_REQUEST["ORDRE_NUM"] == "" || substr($_REQUEST["ORDRE_NUM"], 0, 1) == "A") ? "ADD" : "UPD" ;
        $num = ($_REQUEST["ORDRE_NUM"] == "") ? 0 : (int)substr($_REQUEST["ORDRE_NUM"], 1) ;

        //echo "<pre>"; print_r([$_REQUEST, $type_action, $num, $type_ordre, $dir_x,$dir_y,$dist ]); die();

        // calcul de la distance total d'ordre
        $ordres = json_decode($monture->perso_misc_param) ;
        $distance_ordre = 0;
        $distance_ancien = 0;
        if (sizeof($ordres->ia_monture_ordre) >0) {
            //$contenu_page .= print_r($ordres, true);
            foreach ($ordres->ia_monture_ordre as $k => $o) {
                $distance_ordre += $o->dist;
                if (($o->ordre == $num) && ($type_action == "UPD")) $distance_ancien = $o->dist ;
            }
        }
        $distance_total = $distance_ordre - $distance_ancien + $dist ;

        if ( $dir_x <-1 || $dir_x >1 || $dir_y <-1 || $dir_y >1  || ($dir_y==0 && $dir_x==0 && $type_ordre!="TALONNER" )) $msg .= "<br>Vous avez donné un <b>mauvaise ordre de direction</b>! ";
        if ( $dist >  $dist_max ) $msg .= "<br>Vous ne pouvez pas donner une distance supérieur <b>la vue</b> de votre monture (limité à 8)! ";
        if ( $distance_total >  $dist_vue ) $msg .= "<br>La distance totale des ordres ne doit pas dépasser <b>la vue</b> de votre monture ! ";
        if ( $dist <= 0 ) $msg .= "<br>Vous ne pouvez pas donner un ordre avec une distance nulle ! ";
        if ( $nb_sauter>=$freq_ordre_sauter && $type_ordre=="SAUTER" ) $msg .= "<br>Vous ne pouvez plus SAUTER sur cette DLT ! ";
        if ( $nb_talonner>=$freq_ordre_talonner && $type_ordre=="TALONNER" ) $msg .= "<br>Vous ne pouvez plus TALONNER sur cette DLT ! ";
        if ($msg != "")
        {
            $contenu_page .= $msg."<br><u>L'ordre n'est <b style=\"color:red;\">pas valide</u></b>, les PA n'ont pas été depensés!<br>";
        }
        else
        {
            //echo "<pre>"; print_r([ $type_action, [ "type_ordre" => $type_ordre, "dir_x" => $dir_x, "dir_y" => $dir_y, "dist" => $dist, "num_ordre" => $num ] ]); die();

            $evt = new ligne_evt(); // charger le dernier evenement afin de montrer les evenements produit lors du donnage d'ordre
            $listEvt = $evt->getByPerso($perso_cod, 0, 1);
            $lastEvt = $listEvt[0]->levt_cod ;

            $contenu_page .= $perso->monture_ordre( $type_action, [ "type_ordre" => $type_ordre, "dir_x" => $dir_x, "dir_y" => $dir_y, "dist" => $dist, "num_ordre" => $num ] );
            $monture->charge( $perso->perso_monture ); // recharger le perso montures (avec les nouveaux ordres)

            $listEvt = $evt->getByPerso($perso_cod, 0, 10);
            $contenu_page .= "<br>";
            for ( $e=sizeof($listEvt)-1; $e>=0; $e--) {
                $l_evt = $listEvt[$e] ;
                if (($l_evt->levt_cod > $lastEvt) &&  ($l_evt->levt_texte != "[attaquant] a donné un ordre à [cible].")) {
                    $contenu_page .= change_event_name($l_evt->levt_texte, "<b>".$perso->perso_nom."</b>", "<b>".$monture->perso_nom."</b>")."<br>";
                }
            }
        }

        $contenu_page .= "<hr>";
    }

    // traitment de la supression d'ordres =============================================================================
    if (isset($_REQUEST["ORDRE_DEL"]))
    {
        $contenu_page .= "<hr><b>ACTION</b>: Supprimer un ordre de la monture<br>";
        $msg = "";

        $foundOrdre = false ;
        $num_ordre = $_REQUEST["num_ordre"];
        $ordres = json_decode($monture->perso_misc_param) ;
        if (sizeof($ordres->ia_monture_ordre) >0)
        {

            foreach ($ordres->ia_monture_ordre as $k => $o)
            {
                if ($o->ordre == $num_ordre)
                {
                    $foundOrdre = true;
                    break;
                }
            }
        }
        if ( !$foundOrdre  ) $msg .= "<br>L'ordre que vous essayez de supprimer est <b>innexistant</b>! ";

        if ($msg != "")
        {
            $contenu_page .= $msg."<br>L'ordre n'est <b>pas valide</b>, les PA n'ont pas été depensés!<br>";
        }
        else
        {
            $contenu_page .= $perso->monture_ordre( "DEL", [ "num_ordre" => $num_ordre ] );
            $monture->charge( $perso->perso_monture ); // recharger le perso montures (avec les nouveaux ordres)
        }

        $contenu_page .= "<hr>";
    }

    // affichage des ordres actifs =====================================================================================
    $perso->charge($perso_cod); // recharger au cas ou le nombre de PA a changé
    $contenu_page .= "<br><b><u>Liste des ordres actifs</u></b>: <br> <br>";
    $ordres = json_decode($monture->perso_misc_param) ;
    $distance_ordre = 0;
    if (sizeof($ordres->ia_monture_ordre) >0)
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
            $distance_ordre += $o->dist;
            $img = "<img style='margin:3px; vertical-align: middle;' src='/images/interface/".$arr_img[$o->dir_y.":".$o->dir_x]."'>";
            if ($perso->perso_pa<2)
            {
                $contenu_page .=  "<tr><td><span>Supprimer (2 PA requis) : ";
            } else {
                $contenu_page .=  "<tr><td><span><input onclick=\"$('#num_ordre').val(".($o->ordre).");\" name=\"ORDRE_DEL\" type=\"submit\" value=\"Supprimer (2 PA)\"  class=\"test\">&nbsp;&nbsp;&nbsp;&nbsp;N° {$o->ordre} : ";
            }
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
        $contenu_page .= '</table><span style="font-size: 10px;">Distance total des ordres: '.$distance_ordre.' case(s).<span></form>';
    }
    $contenu_page .= "<hr>";

    // PDonner un ordre ================================================================================================

    $selector = '<select style="width:90%;" name="ORDRE_NUM">';
    if (is_array($a_ordres) && sizeof($a_ordres)>0)
    {
        foreach ($a_ordres as $k)
        {
            $o = $ordres->ia_monture_ordre[$k] ;
            $selector.= '<option value="M'.$o->ordre.'">Modifier ordre #'.$o->ordre.'</option>';
        }
        foreach ($a_ordres as $k)
        {
            $o = $ordres->ia_monture_ordre[$k] ;
            $selector.= '<option value="A'.$o->ordre.'">Ajouter avant ordre #'.$o->ordre.'</option>';
        }
    }
    $selector.= '<option selected value="">Ajouter à la fin</option></select>';

    // affichage des actions speciale
    $perso_misc_param = json_decode($monture->perso_misc_param );
    $ia_monture = isset($perso_misc_param->ia_monture) ? $perso_misc_param->ia_monture : json_decode("{}");
    $nb_sauter = isset($ia_monture->nb_sauter) ? $ia_monture->nb_sauter : 0 ;
    $nb_talonner = isset($ia_monture->nb_talonner) ? $ia_monture->nb_talonner : 0 ;

    //echo "<pre>"; print_r([$ia_monture->nb_talonner, $freq_ordre_talonner, $nb_talonner]); die();

    $selector_action = '';
    if (($freq_ordre_talonner>0)||($freq_ordre_sauter>0))
    {
        $selector_action .= '<select onchange="ordonnerMontureChangeAction(this);"; style="width:90%; margin-bottom:5px;" name="ORDRE_TYPE"><option value="DIRIGER">Diriger</option>';
        if ($freq_ordre_sauter>0 && $freq_ordre_sauter>$nb_sauter) { $selector_action .= '<option '.($freq_ordre_sauter<=$nb_sauter ? "disabled" : "").' value="SAUTER">Sauter</option>'; }
        if ($freq_ordre_talonner>0 && $freq_ordre_talonner>$nb_talonner) { $selector_action .= '<option '.($freq_ordre_talonner<=$nb_talonner ? "disabled" : "").' value="TALONNER">Talonner</option>'; }
        $selector_action.= '</select><br>&nbsp;&nbsp;';
    }

    if ($freq_ordre_sauter>0) {
        $reste = isset($arr_reste_frequence["$freq_ordre_sauter"]["$nb_sauter"]) ? $arr_reste_frequence["$freq_ordre_sauter"]["$nb_sauter"] : "";
        $contenu_page .=  "<b>SAUTER</b>: Vous pouvez sauter <b>". $arr_frequence["$freq_ordre_sauter"]."</b> <span style=\"font-size: 10px;\">(de monture)</span>, reste : <b>{$reste}</b><br>";
    }
    if ($freq_ordre_talonner>0) {
        $reste = isset($arr_reste_frequence["$freq_ordre_talonner"]["$nb_talonner"]) ? $arr_reste_frequence["$freq_ordre_talonner"]["$nb_talonner"] : "";
        $contenu_page .=  "<b>TALONNER</b>: Vous pouvez talonner <b>". $arr_frequence["$freq_ordre_talonner"]."</b> <span style=\"font-size: 10px;\">(de monture)</span>, reste : <b>{$reste}</b><br>";
    }


    $contenu_page .= "<br><b><u>Donner un nouvel ordre</u></b>: <br>Sélectionner la direction et la distance<sup>*</sup> à parcourir:<br><br>";
    $contenu_page .= '<form name="monture_order" id="monture_order" method="post" action="monture_ordre.php"><table style="border: 1px solid black;">';
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
        if ($l==1 && $c>3) {
            if ($perso->perso_pa<$cout_ordre) {
                $contenu_page .= '<td rowspan="3" class="soustitre2" style="text-align: center;">&nbsp;&nbsp;Ordonner ('.$cout_ordre.' PA requis)</td>';
            } else {
                $contenu_page .= '<td rowspan="3" class="soustitre2" style="text-align: left;">&nbsp;&nbsp;'.$selector_action.$selector.'<br><br>&nbsp;&nbsp;<input '.($perso->perso_pa<$cout_ordre ? "disabled" : "").' name="ORDRE_ADD" type="submit" value="Donner/Modifier l\'ordre('.$cout_ordre.' PA)"  class="test">&nbsp;&nbsp;</td>';
            }
        }
        $contenu_page .= '</tr>';
    }
    $contenu_page .= '</table><br>';
    $contenu_page .= '<span style="font-size: 10px;">* la distance de chaque ordre est limité à 8 et à la vue de la monture, distance d’ordre max est: <b>'.$dist_max.'</b> cases(s)<br>* la distance total d’ordre doit être inférieure ou égale à la vue de la monture. Il reste une distance de <b>'.max(0, $dist_vue-$distance_ordre).'</b> case(s).<span>';
    $contenu_page .= "</form>";
    $contenu_page .= "<br><br><hr>";

    // charger la liste des terrains innacessible à la monture
    $terrain = [] ;
    $mt = new monstre_terrain();
    $monture_terrain = $mt->getBy_tmon_gmon_cod($monture->perso_gmon_cod);
    foreach ($monture_terrain as $t)
    {
        $terrain[$t->tmon_ter_cod] = $t->tmon_chevauchable ;
    }
    //$contenu_page.= print_r($terrain, true);
    $contenu_page .= "<b><u>La carte</u></b>: <br> <br><table style=\"border: 1px solid black;\"><tr><td>";
    include('vue_gauche.php');
    $contenu_page .= $vue_gauche ;
    $contenu_page .= "</td><td>{$automap}</td></tr>";

    $contenu_page .= '<tr><td colspan="2"><div style="font-size: 10px;">&nbsp;&nbsp;<div style="display:inline-block;" class="horseBlink"><div class="pasvu caseVue" title=""><img src="/images/del.gif" width="18" height="18" alt=""></div></div>= Terrain inaccessible avec votre monture.</div>';
    $contenu_page .= "</td></tr></table>";
}


// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');


$template     = $twig->load('template_jeu.twig');
$options_twig = array(

    'CONTENU_PAGE'             => $contenu_page
);
echo $template->render(array_merge($var_twig_defaut,$options_twig_defaut, $options_twig));





