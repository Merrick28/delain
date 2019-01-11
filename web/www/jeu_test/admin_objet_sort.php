<?php

include "blocks/_header_page_jeu.php";
include_once '../includes/tools.php';

?>

    <script>//# sourceURL=admin_objet_sorts.js

        function editObjetSort(row, objsort_cod) {
            //executer le service asynchrone
            $('tr[id^="sortlist-"]').removeClass("soustitre2");
            $('#bouton-supprimer').hide();
            if (row>=0)
            {
                $('#sortlist-'+row).addClass("soustitre2");
                $('#bouton-supprimer').show();
            }

            runAsync({request: "get_table_info", data: {info: "objets_sorts", objsort_cod: objsort_cod}}, function (d) {
               if (d.resultat == 0)
               {
                   var data = d.data ;
                   $("#objsort_cod").val(data.objsort_cod ? data.objsort_cod : 0);
                   $("#sort-0-misc_cod").val(data.objsort_sort_cod ? data.objsort_sort_cod : "");
                   if ($("#sort-0-misc_cod").val()>0)
                   {
                        setNomByTableCod('sort-0-misc_nom', 'sort', $("#sort-0-misc_cod").val());
                   }
                   else
                   {
                       $("#sort-0-misc_nom").val("");
                   }
                   $("#objsort_nom").val(data.objsort_nom ? data.objsort_nom : "");
                   $("#objsort_cout").val(data.objsort_cout ? data.objsort_cout : "");
                   $("#objsort_malchance").val(data.objsort_malchance ? data.objsort_malchance : "0");
                   $("#objsort_nb_utilisation_max").val(data.objsort_nb_utilisation_max ? data.objsort_nb_utilisation_max : "");
                   $("#objsort_equip_requis").val((!data.objsort_equip_requis || data.objsort_equip_requis =='false') ? 'N' : 'O');
               }
            });
        }

    </script>

<?php

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
    <title>AFFECTATION DE SORT SUR DES OBJETS / OBJETS GENERIQUES</title>
<?php

$droit_modif = 'dcompt_objet';
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{

    //=======================================================================================
    // == Main
    //=======================================================================================
    //-- traitement des actions
    if (isset($_REQUEST['methode']))
    {
        // Traitement des actions de téléportation
        if ($_REQUEST['methode'] == "sauve")
        {
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>";
            $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) ajout/modification de sort sur objets:\n";

            $objsorts = new objets_sorts();
            $objsort_cod = (1*(int)$_REQUEST["objsort_cod"]);

            if ($objsort_cod>0)
            {
                $objsorts->charge($objsort_cod);
                $new = false ;
            }
            else
            {
                $new = true ;
            }

            // Cas d'une suppression
            if (($_REQUEST["supprimer"] == "supprimer") && ($objsort_cod>0))
            {
                $log.="supression de l'objet_sort #".$objsorts->objsort_cod."\n".obj_diff(new objets_sorts, $objsorts);
                $objsorts->delete($objsort_cod);
            }
            else
            {
                // Cas d'une creation/modification
                $clone_os = clone $objsorts;

                $objsorts->objsort_parent_cod = null ;
                $objsorts->objsort_gobj_cod = 1*(int)$_REQUEST["objsort_gobj_cod"];
                $objsorts->objsort_obj_cod = null ;
                $objsorts->objsort_sort_cod = 1*(int)$_REQUEST["objsort_sort_cod"];
                $objsorts->objsort_nom = $_REQUEST["objsort_nom"]=='' ? null : $_REQUEST["objsort_nom"] ;
                $objsorts->objsort_cout = $_REQUEST["objsort_cout"]=='' ? null : 1*(int)$_REQUEST["objsort_cout"];
                $objsorts->objsort_malchance = $_REQUEST["objsort_malchance"]=='' ? 0 : 1*(float)$_REQUEST["objsort_malchance"];
                $objsorts->objsort_nb_utilisation_max = $_REQUEST["objsort_nb_utilisation_max"]=='' ? null : 1*(int)$_REQUEST["objsort_nb_utilisation_max"];
                $objsorts->objsort_nb_utilisation = 0 ;
                $objsorts->objsort_equip_requis = $_REQUEST["objsort_equip_requis"]=="O" ? "true" : "false" ;
                $objsorts->stocke($new);

                // Logger les infos pour suivi admin
                $log.="ajoute/modifie de l'objet_sort #".$objsorts->objsort_cod."\n".obj_diff($clone_os, $objsorts);
            }

            writelog($log,'objet_edit', true);
            //echo "<div class='bordiv'><pre>$log</pre></div>";
        }
    }
    //print_r($_REQUEST);

    echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: #800000;\'>SELECTION D\'OBJET</strong>&nbsp;&nbsp;</div>';

    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $row_id = "obj-generique-";
    echo '<form name="selection-objet" action="' . $PHP_SELF . '" method="post">';
    echo '<br><strong>Sélection d’un objet générique</strong><br>Code de l\'objet générique :
                    <input data-entry="val" name="objsort_gobj_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\''.$row_id.'misc_nom\', \'objet_generique\', $(\'#'.$row_id.'misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","objet_generique","Rechercher un objet générique");\'>
                    &nbsp;<input type="submit" value="Voir/Modifier ses rattachements de cet objet" class="test"></form>';


    echo "<hr>";

    $objsort_gobj_cod = 1*(int)$_REQUEST["objsort_gobj_cod"] ;
    if ($objsort_gobj_cod>0)
    {
        $gobj = new objet_generique();
        $gobj->charge($objsort_gobj_cod);
        echo "Détail des sorts sur l'objet: <strong>#{$gobj->gobj_cod} - {$gobj->gobj_nom}</strong><br>";
        $exemplaires = $gobj->getNombreExemplaires();
        echo "Nombre d'exemplaire de l'objet:<br>";
        echo "&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>".$exemplaires->total."</strong><br>";
        echo "&nbsp;&nbsp;&nbsp;Inventaire : <strong>".$exemplaires->inventaire."</strong> <em style='font-size: x-small'>(possédés par les joueurs, monstres ou PNJ)</em><br>";
        echo "<br>";

        echo "<strong>Ajouter/Modifier un sort sur l'objet</strong> :";
        $row_id = "sort-0-";
        echo '<form name="mod-objet-sort" action="' . $PHP_SELF . '" method="post">
             <input type="hidden" name="methode" value="sauve">
             <input type="hidden" id="objsort_cod" name="objsort_cod" value="0">
             <input type="hidden" id="objsort_gobj_cod" name="objsort_gobj_cod" value="'.$objsort_gobj_cod.'">
             <input type="hidden" id="objsort_obj_cod" name="objsort_obj_cod" value="">
             ';
        echo '<table width="100%" class=\'bordiv\'><tr><td>Sélection de sort (<em>sort_cod</em>) :</td><td>
                <input data-entry="val" name="objsort_sort_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\''.$row_id.'misc_nom\', \'sort\', $(\'#'.$row_id.'misc_cod\').val());">
                &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","sort","Rechercher un sort");\'><br>
                </td></tr>
                <tr><td>Nom du sort :</td><td><input type="text" id="objsort_nom" name="objsort_nom" size="50">&nbsp;<em> si vide, le nom réel du sort sera utilisé</em></td></tr>
                <tr><td>Cout (en PA) :</td><td><input type="text" id="objsort_cout" name="objsort_cout" size="2">&nbsp;<em> si vide, le cout réel du sort sera utilisé</em></td></tr>
                <tr><td>Malchance :</td><td><input type="text" id="objsort_malchance" name="objsort_malchance" size="3">&nbsp;<em> au format 99.99 c\'est le % d\'échec possible (0 ou vide = toujours réussi)</em></td></tr>
                <tr><td>Nb Utilisation :</td><td><input type="text" id="objsort_nb_utilisation_max" name="objsort_nb_utilisation_max" size="2">&nbsp;<em> nombre d\'utilisation possible (illimité si vide)</em></td></tr>
                <tr><td>Equip. requis :</td><td>'.create_selectbox("objsort_equip_requis", array("O"=>"Oui","N"=>"Non"), 'N', array("id"=>"objsort_equip_requis")).'&nbsp;<em> l\'objet doit-t-il être quipé pour pourvoir utiliser le sort?</em></td></tr>
                <tr><td></td><td><input type="submit" name="valider" value="valider" class="test">&nbsp;&nbsp;<input style="display:none" id="bouton-supprimer" type="submit" name="supprimer" value="supprimer" class="test"></td></tr>
                </table>
                </form>';

        echo "<strong><br>Liste des sorts sur l'objet</strong> :<br>";
        $objsorts = new objets_sorts();
        $lsorts = $objsorts->getBy_objsort_gobj_cod($gobj->gobj_cod);
        if ($lsorts)
        {
            echo '<table width="100%" class=\'bordiv\'>';
            echo "<tr><td><input type='button' class='test' value='nouveau' onclick='editObjetSort(-1,0);'></td>
                      <td><strong>objsort_cod</strong></td>
                      <td><strong>sort</strong></td>
                      <td><strong>Nom sur l'objet</strong></td>
                      <td><strong>Coût</strong></td>
                      <td><strong>Malchance</strong></td>
                      <td><strong>Utilis.</strong></td>
                      <td><strong>Equip.</strong></td></tr>";
            foreach ($lsorts as $k => $os)
            {
                $sort = new sorts();
                $sort->charge($os->objsort_sort_cod);
                echo "<tr id='sortlist-{$k}'><td><input type='button' class='test' value='modifier' onclick='editObjetSort({$k}, {$os->objsort_cod});'></td>
                      <td>{$os->objsort_cod}</td>
                      <td>{$os->objsort_sort_cod} ({$sort->sort_nom} - {$sort->sort_cout}PA) </td>
                      <td>".$os->getNom()."</td>
                      <td>".$os->getCout()." PA</td>
                      <td>{$os->objsort_malchance}</td>
                      <td>{$os->objsort_nb_utilisation_max}</td>
                      <td>".( $os->objsort_equip_requis ? "O" : "N" )."</td></tr>";
            }
            echo "</table>";
        }
        else
        {
            echo "<em>Il n'y a pas de sort sur cet objet</em>";
        }
    }

}
?>
    <br><p style="text-align:center;"><a href="admin_objet_generique_edit.php">Retour au modification d'objets génériques</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";