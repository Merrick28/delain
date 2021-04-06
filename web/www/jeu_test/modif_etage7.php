<?php
/* Création, modification des lieux */

include "blocks/_header_page_jeu.php";


$contenu_page = '';
ob_start();
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";

$pos_etage = isset($_REQUEST["pos_etage"]) ? $_REQUEST["pos_etage"] : '';
$etage_selector = $html->etage_select($pos_etage);

echo "Choix de l’étage où créer / modifier les terrains
    <form method=\"post\" action=\"modif_etage7.php\">
        <select name=\"pos_etage\"> {$etage_selector}
        </select><br>
        <input type=\"submit\" value=\"Valider\" class='test'/>
    </form><hr>";


// definitions des constante
$chemin = '../images/';

if ( isset($_REQUEST["pos_etage"]) )
{
    //cahrger les type de terrains
    $req_m_terrain= "select ter_cod, ter_nom from terrain where ter_cod>0 order by ter_nom";
    $stmt_m_terrain = $pdo->query($req_m_terrain);
    $terrains = $stmt_m_terrain->fetchAll(PDO::FETCH_ASSOC);


    // afficher la liste des fonds/decors de cet etage!
    $req = "select etage_libelle, etage_affichage from etage where etage_numero = :etage_numero";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(":etage_numero" => $pos_etage), $stmt);
    $result = $stmt->fetch();
    $style = $result["etage_affichage"] ;


    if ( isset($_POST["modifier"]) )
    {
        echo "Prise en compte des modifications<hr>";

        // Traitement des fonds ========================================================================================
        $list_pos_type_aff = "" ;
        foreach ($_POST as $key => $ter_cod)
        {
            if ( substr($key, 0, 10) == "terrain-f-" )
            {
                $type = substr($key, 10 ) ;
                $mpa = $_POST["modif-pa-f-$type"] ;


                // Appliquer les changements !
                if ($ter_cod == 0)
                {
                    // cas d'une case pour laquelle on supprime le terrain, on ne modifie que celles qui avaient déjà été terraformées
                    $req = "update positions set pos_ter_cod=null, pos_modif_pa_dep=0 where pos_etage = :etage_numero and pos_type_aff=:pos_type_aff and pos_ter_cod is not null";
                    $bindings = [":etage_numero"=> $pos_etage, ":pos_type_aff" => $type  ] ;
                }
                else
                {
                    $list_pos_type_aff .= "{$type}," ;
                    $req = "update positions set pos_ter_cod=:pos_ter_cod, pos_modif_pa_dep=:pos_modif_pa_dep where pos_etage = :etage_numero and pos_type_aff=:pos_type_aff";
                    $bindings = [":etage_numero"=> $pos_etage, ":pos_type_aff" => $type, ":pos_ter_cod"=> $ter_cod, "pos_modif_pa_dep" => $mpa  ] ;
                }
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute($bindings, $stmt);
                $result = $stmt->fetch();

            }
        }
        if ($list_pos_type_aff != "") $list_pos_type_aff = substr($list_pos_type_aff, 0, -1);

        // Traitement des décors ========================================================================================
        $list_pos_decor = "" ;
        foreach ($_POST as $key => $ter_cod)
        {
            if ( substr($key, 0, 10) == "terrain-d-" )
            {
                $type = substr($key, 10 ) ;
                $mpa = $_POST["modif-pa-d-$type"] ;


                // Appliquer les changements !
                if ($ter_cod == 0)
                {
                    // cas d'une case pour laquelle on supprime le terrain, on ne modifie que celles qui avaient déjà été terraformées
                    $req = "update positions set pos_ter_cod=null, pos_modif_pa_dep=0 where pos_etage = :etage_numero and pos_type_aff=:pos_type_aff and pos_ter_cod is not null ";
                    if ($list_pos_type_aff != "")  $req.= " and  pos_type_aff not in ({$list_pos_type_aff})";
                    $bindings = [":etage_numero"=> $pos_etage, ":pos_type_aff" => $type  ] ;
                }
                else
                {
                    $list_pos_decor .= "{$type}," ;
                    $req = "update positions set pos_ter_cod=:pos_ter_cod, pos_modif_pa_dep=:pos_modif_pa_dep where pos_etage = :etage_numero and pos_decor=:pos_decor";
                    $bindings = [":etage_numero"=> $pos_etage, ":pos_decor" => $type, ":pos_ter_cod"=> $ter_cod, "pos_modif_pa_dep" => $mpa  ] ;
                }
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute($bindings, $stmt);
                $result = $stmt->fetch();

            }
        }
        if ($list_pos_decor != "") $list_pos_decor = substr($list_pos_decor, 0, -1);

#        // Traitement des décors superposés ========================================================================================
#        $list_pos_decor_dessus = "" ;
#        foreach ($_POST as $key => $ter_cod)
#        {
#            if ( substr($key, 0, 10) == "terrain-s-" )
#            {
#                $type = substr($key, 10 ) ;
#                $mpa = $_POST["modif-pa-s-$type"] ;
#
#
#                // Appliquer les changements !
#                if ($ter_cod == 0)
#                {
#                    // cas d'une case pour laquelle on supprime le terrain, on ne modifie que celles qui avaient déjà été terraformées
#                    $req = "update positions set pos_ter_cod=null, pos_modif_pa_dep=0 where pos_etage = :etage_numero and pos_decor_dessus=:pos_decor_dessus and pos_ter_cod is not null ";
#                    if ($list_pos_type_aff != "")  $req.= " and  pos_type_aff not in ({$list_pos_type_aff})";
#                    if ($list_pos_decor != "")  $req.= " and  pos_decor not in ({$list_pos_decor})";
#                    $bindings = [":etage_numero"=> $pos_etage, ":pos_decor_dessus" => $type  ] ;
#                }
#                else
#                {
#                    $list_pos_decor_dessus .= "{$type}," ;
#                    $req = "update positions set pos_ter_cod=:pos_ter_cod, pos_modif_pa_dep=:pos_modif_pa_dep where pos_etage = :etage_numero and pos_decor_dessus=:pos_decor_dessus";
#                    $bindings = [":etage_numero"=> $pos_etage, ":pos_decor_dessus" => $type, ":pos_ter_cod"=> $ter_cod, "pos_modif_pa_dep" => $mpa  ] ;
#                }
#                $stmt = $pdo->prepare($req);
#                $stmt = $pdo->execute($bindings, $stmt);
#                $result = $stmt->fetch();
#
#            }
#        }
#        if ($list_pos_decor_dessus != "") $list_pos_decor_dessus = substr($list_pos_decor_dessus, -1);

    }

    echo "<b></br>L'étage utilise le style #{$style}</b><br>";
    echo "Définition des terrains sur les <b>fonds</b> (utilisés dans la map):<br>";
    echo '<form method="post" action="modif_etage7.php">
          <input name="pos_etage" type="hidden" value="'.$pos_etage.'" class="test"/>
          ';

    // Affichage des fonds ========================================================================================
    $patron_fond = '/^f_(?P<affichage>[0-9a-zA-Z]+)_(?P<type>\d+)\.png$/';
    $rep = opendir($chemin);
    while (false !== ($fichier = readdir($rep)))
    {
        $correspondances = array();
        $flagNouveauStyle = "";
        if (1 === preg_match($patron_fond, $fichier, $correspondances)) {
            if ($correspondances["affichage"] == $style )
            {
                $type = $correspondances["type"]  ;

                $ter_cod = 0 ;
                $mpa = 0 ;

                $req = "SELECT pos_ter_cod, pos_modif_pa_dep, count(*) count FROM positions where pos_etage=:etage_numero  and pos_type_aff=:pos_type_aff group by pos_ter_cod, pos_modif_pa_dep ORDER BY count desc ";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute([":etage_numero"=> $pos_etage, ":pos_type_aff" => $type], $stmt);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
                if (count($result)>0)
                {
                    if (count($result) == 1)
                    {
                        $ter_cod = $result[0]["pos_ter_cod"] ;
                        $mpa = $result[0]["pos_modif_pa_dep"] ;

                    }
                    $bgcolor = ($ter_cod != 0) ? "style='background-color: gold'" : "";
                    echo "<img height=\"20px;\" src=\"{$chemin}{$fichier}\">&nbsp;-<span {$bgcolor}>&nbsp;#{$type}&nbsp;</span>:&nbsp;";

                    echo select_terrain("terrain-f-$type", $terrains, $ter_cod);

                    echo "&nbsp;&nbsp; Modificateur de PA : <input type='text' size='5' name='modif-pa-f-$type' value='{$mpa}'> <br>";

                }


            }
        }
    }

    // Affichage des décors ========================================================================================
    echo "<br>Définition des terrains sur les <b>décors</b> (utilisés dans la map):<br>";

    $patron_decors = '/^dec_(?P<type>\d+)\.gif$/';
    $rep = opendir($chemin);
    while (false !== ($fichier = readdir($rep)))
    {
        $correspondances = array();
        $flagNouveauStyle = "";
        if (1 === preg_match($patron_decors, $fichier, $correspondances)) {

            $type = $correspondances["type"]  ;

            $ter_cod = 0 ;
            $mpa = 0 ;

            $req = "SELECT pos_ter_cod, pos_modif_pa_dep, count(*) count FROM positions where pos_etage=:etage_numero and pos_decor=:pos_decor group by pos_ter_cod, pos_modif_pa_dep ORDER BY count desc";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute([":etage_numero"=> $pos_etage, ":pos_decor" => $type], $stmt);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ;
            if (count($result)>0)
            {
                if (count($result) == 1)
                {
                    $ter_cod = $result[0]["pos_ter_cod"] ;
                    $mpa = $result[0]["pos_modif_pa_dep"] ;

                }
                $bgcolor = ($ter_cod != 0) ? "style='background-color: gold'" : "";
                echo "<img height=\"20px;\" src=\"{$chemin}{$fichier}\">&nbsp;-<span {$bgcolor}>&nbsp;#{$type}&nbsp;</span>:&nbsp;";

                echo select_terrain("terrain-d-$type", $terrains, $ter_cod);

                echo "&nbsp;&nbsp; Modificateur de PA : <input type='text' size='5' name='modif-pa-d-$type' value='{$mpa}'> <br>";

            }
        }
    }

#    // Affichage des décors superposé========================================================================================
#    echo "<br>Définition des terrains sur les <b>décors superposés</b> (utilisés dans la map):<br>";
#
#    $patron_decors = '/^dec_(?P<type>\d+)\.gif$/';
#    $rep = opendir($chemin);
#    while (false !== ($fichier = readdir($rep)))
#    {
#        $correspondances = array();
#        $flagNouveauStyle = "";
#        if (1 === preg_match($patron_decors, $fichier, $correspondances)) {
#
#            $type = $correspondances["type"]  ;
#
#            $ter_cod = 0 ;
#            $mpa = 0 ;
#
#            $req = "SELECT pos_ter_cod, pos_modif_pa_dep, count(*) count FROM public.positions where pos_etage=:etage_numero  and pos_decor_dessus=:pos_decor_dessus group by pos_ter_cod, pos_modif_pa_dep ORDER BY count desc limit 1";
#            $stmt = $pdo->prepare($req);
#            $stmt = $pdo->execute([":etage_numero"=> $pos_etage, ":pos_decor_dessus" => $type], $stmt);
#            $result = $stmt->fetch() ;
#            if ($result)
#            {
#                $ter_cod = $result["pos_ter_cod"] ;
#                $mpa = $result["pos_modif_pa_dep"] ;
#
#                $bgcolor = ($ter_cod != 0) ? "style='background-color: gold'" : "";
#                echo "<img height=\"20px;\" src=\"{$chemin}{$fichier}\">&nbsp;-<span {$bgcolor}>&nbsp;#{$type}&nbsp;</span>:&nbsp;";
#
#                echo select_terrain("terrain-s-$type", $terrains, $ter_cod);
#
#                echo "&nbsp;&nbsp; Modificateur de PA : <input type='text' size='5' name='modif-pa-s-$type' value='{$mpa}'> <br>";
#
#            }
#        }
#    }

    echo '<br><input type="submit" name="modifier" value="Valider les changements" class="test"/><br>';
    echo '</form>';
    echo '<hr><b><u>NOTA</u></b>: Pour voir les effets des terrains sur la map: (<a target="_blank" href="/jeu_test/modif_etage2.php?pos_etage='.$pos_etage.'">Création / Modification des mines et des routes</a>)';


}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

function select_terrain($name, $terrains, $ter_cod)
{
    $select = '<select name="'.$name.'">';
    $select.= '<option value="0"'.($ter_cod == 0 ? ' selected ' : '').'>Sans terrain spécifique</option>';
    for ($t=0; $t<count($terrains); $t++ )
    {
        $select.= '<option value="'.$terrains[$t]["ter_cod"].'"'.($ter_cod == $terrains[$t]["ter_cod"] ? ' selected ' : '').'>'.$terrains[$t]["ter_nom"].'</option>';
    }
    $select.= '</select>';
    return $select ;
}