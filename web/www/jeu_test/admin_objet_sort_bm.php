<?php

include "blocks/_header_page_jeu.php";
include_once '../includes/tools.php';

?>

    <script>//# sourceURL=admin_objet_sorts_bm.js

        function setNomByBMCod(divname, table, cod) { // fonction de mise à jour d'un champ nom quand on connait le cod
            //executer le service asynchrone
            $("#" + divname).text("");
            runAsync({request: "get_table_nom", data: {table: table, cod: cod}}, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.nom)) {
                    $("#" + divname).text(d.data.nom);
                    $("#" + divname.substr(0, divname.length - 8) + 'libc').val((d.data.nom.substr(0, 3)));
                }
                else {
                    $("#" + divname).text('');
                    $("#" + divname.substr(0, divname.length - 8) + 'libc').val((''));
                }
            });
        }

        function setNomByBMLibc(divname, table, cod) { // fonction de mise à jour d'un champ nom quand on connait le cod
            //executer le service asynchrone
            $("#" + divname).text("");
            runAsync({request: "get_table_nom", data: {table: table, cod: cod}}, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.nom)) {
                    $("#" + divname).text(d.data.nom);
                    $("#" + divname.substr(0, divname.length - 8) + 'misc_cod').val((d.data.cod));
                }
                else {
                    $("#" + divname).text('');
                    $("#" + divname.substr(0, divname.length - 8) + 'misc_cod').val((''));
                }
            });
        }

        function editObjetSortBM(row, objsortbm_cod) {
            //executer le service asynchrone
            $('tr[id^="sortlist-"]').removeClass("soustitre2");
            $('#bouton-supprimer').hide();
            if (row>=0)
            {
                $('#sortlist-'+row).addClass("soustitre2");
                $('#bouton-supprimer').show();
            }

            runAsync({request: "get_table_info", data: {info: "objets_sorts_bm", objsortbm_cod: objsortbm_cod}}, function (d) {
               if (d.resultat == 0)
               {
                   var data = d.data ;
                   $("#objsortbm_cod").val(data.objsortbm_cod ? data.objsortbm_cod : 0);
                   $("#sort-0-misc_cod").val(data.objsortbm_tbonus_cod ? data.objsortbm_tbonus_cod : "");
                   if ($("#sort-0-misc_cod").val()>0)
                   {
                        setNomByTableCod('sort-0-misc_nom', 'bonus_type', $("#sort-0-misc_cod").val());
                   }
                   else
                   {
                       $("#sort-0-misc_nom").val("");
                   }
                   $("#objsortbm_nom").val(data.objsortbm_nom ? data.objsortbm_nom : "");
                   $("#objsortbm_cout").val(data.objsortbm_cout ? data.objsortbm_cout : "");
                   $("#objsortbm_bonus_valeur").val(data.objsortbm_bonus_valeur ? data.objsortbm_bonus_valeur : "1");
                   $("#objsortbm_bonus_nb_tours").val(data.objsortbm_bonus_nb_tours ? data.objsortbm_bonus_nb_tours : "1");
                   $("#objsortbm_bonus_soi_meme").val((!data.objsortbm_bonus_soi_meme || data.objsortbm_bonus_soi_meme =='N') ? 'N' : 'O');
                   $("#objsortbm_bonus_monstre").val((!data.objsortbm_bonus_monstre || data.objsortbm_bonus_monstre =='N') ? 'N' : 'O');
                   $("#objsortbm_bonus_joueur").val((!data.objsortbm_bonus_joueur || data.objsortbm_bonus_joueur =='N') ? 'N' : 'O');
                   $("#objsortbm_bonus_case").val((!data.objsortbm_bonus_case || data.objsortbm_bonus_case =='N') ? 'N' : 'O');
                   $("#objsortbm_bonus_distance").val(data.objsortbm_bonus_distance ? data.objsortbm_bonus_distance : "0");
                   $("#objsortbm_bonus_aggressif").val((!data.objsortbm_bonus_aggressif || data.objsortbm_bonus_aggressif =='N') ? 'N' : 'O');
                   $("#objsortbm_bonus_soutien").val((!data.objsortbm_bonus_soutien || data.objsortbm_bonus_soutien =='N') ? 'N' : 'O');
                   $("#objsortbm_bonus_mode").val(data.objsortbm_bonus_mode ? data.objsortbm_bonus_mode : "S");
                   $("#objsortbm_malchance").val(data.objsortbm_malchance ? data.objsortbm_malchance : "0");
                   $("#objsortbm_nb_utilisation_max").val(data.objsortbm_nb_utilisation_max ? data.objsortbm_nb_utilisation_max : "");
                   $("#objsortbm_equip_requis").val((!data.objsortbm_equip_requis || data.objsortbm_equip_requis =='false') ? 'N' : 'O');
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
    <title>AFFECTATION DE SORT UTILSANT DES BM SUR DES OBJETS / OBJETS GENERIQUES</title>
<?php

$droit_modif = 'dcompt_objet';
define('APPEL', 1);
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

            $objsortsbm = new objets_sorts_bm();
            $objsortbm_cod = (1*(int)$_REQUEST["objsortbm_cod"]);

            if ($objsortbm_cod>0)
            {
                $objsortsbm->charge($objsortbm_cod);
                $new = false ;
            }
            else
            {
                $new = true ;
            }

            // Cas d'une suppression
            if (($_REQUEST["supprimer"] == "supprimer") && ($objsortbm_cod>0))
            {
                $log.="supression de l'objet_sort #".$objsortsbm->objsortbm_cod."\n".obj_diff(new objets_sorts_bm, $objsortsbm);
                $objsortsbm->delete($objsortbm_cod);
            }
            else
            {
                // Cas d'une creation/modification
                $clone_os = clone $objsortsbm;

                $objsortsbm->objsortbm_parent_cod = null ;
                $objsortsbm->objsortbm_gobj_cod = $_REQUEST["objsortbm_gobj_cod"]== "" ? null : 1*(int)$_REQUEST["objsortbm_gobj_cod"];
                $objsortsbm->objsortbm_obj_cod = $_REQUEST["objsortbm_obj_cod"]== "" ? null : 1*(int)$_REQUEST["objsortbm_obj_cod"] ;
                $objsortsbm->objsortbm_tbonus_cod = 1*(int)$_REQUEST["objsortbm_tbonus_cod"];
                $objsortsbm->objsortbm_nom = $_REQUEST["objsortbm_nom"]=='' ? null : $_REQUEST["objsortbm_nom"] ;
                $objsortsbm->objsortbm_cout = $_REQUEST["objsortbm_cout"]=='' ? null : 1*(int)$_REQUEST["objsortbm_cout"];
                $objsortsbm->objsortbm_bonus_valeur = $_REQUEST["objsortbm_bonus_valeur"]=='' ? "1" : $_REQUEST["objsortbm_bonus_valeur"];
                $objsortsbm->objsortbm_bonus_nb_tours = $_REQUEST["objsortbm_bonus_nb_tours"]=='' ? "1" : $_REQUEST["objsortbm_bonus_nb_tours"];
                $objsortsbm->objsortbm_bonus_distance = $_REQUEST["objsortbm_bonus_distance"]=='' ?  0 : (int)$_REQUEST["objsortbm_bonus_distance"];
                $objsortsbm->objsortbm_bonus_aggressif = $_REQUEST["objsortbm_bonus_aggressif"]=='' ? "N" : $_REQUEST["objsortbm_bonus_aggressif"];
                $objsortsbm->objsortbm_bonus_soutien = $_REQUEST["objsortbm_bonus_soutien"]=='' ? "N" : $_REQUEST["objsortbm_bonus_soutien"];
                $objsortsbm->objsortbm_bonus_soi_meme = $_REQUEST["objsortbm_bonus_soi_meme"]=='' ? "O" : $_REQUEST["objsortbm_bonus_soi_meme"];
                $objsortsbm->objsortbm_bonus_monstre = $_REQUEST["objsortbm_bonus_monstre"]=='' ? "O" : $_REQUEST["objsortbm_bonus_monstre"];
                $objsortsbm->objsortbm_bonus_joueur = $_REQUEST["objsortbm_bonus_joueur"]=='' ? "O" : $_REQUEST["objsortbm_bonus_joueur"];
                $objsortsbm->objsortbm_bonus_case = $_REQUEST["objsortbm_bonus_case"]=='' ? "N" : $_REQUEST["objsortbm_bonus_case"];
                $objsortsbm->objsortbm_bonus_mode = $_REQUEST["objsortbm_bonus_mode"]=='' ? "S" : $_REQUEST["objsortbm_bonus_mode"];
                $objsortsbm->objsortbm_malchance = $_REQUEST["objsortbm_malchance"]=='' ? 0 : 1*(float)$_REQUEST["objsortbm_malchance"];
                $objsortsbm->objsortbm_nb_utilisation_max = $_REQUEST["objsortbm_nb_utilisation_max"]=='' ? null : 1*(int)$_REQUEST["objsortbm_nb_utilisation_max"];
                $objsortsbm->objsortbm_nb_utilisation = 0 ;
                $objsortsbm->objsortbm_equip_requis = $_REQUEST["objsortbm_equip_requis"]=="O" ? "true" : "false" ;

                $objsortsbm->stocke($new);

                // dans le cas d'un generique mise à jour des repliques déjà en jeu !
                if ($_REQUEST["objsortbm_gobj_cod"]!="")
                {
                    $req = "UPDATE objets_sorts_bm osb1 SET 
                                    objsortbm_tbonus_cod=osb2.objsortbm_tbonus_cod,
                                    objsortbm_nom=osb2.objsortbm_nom,
                                    objsortbm_cout=osb2.objsortbm_cout,
                                    objsortbm_bonus_valeur=osb2.objsortbm_bonus_valeur,
                                    objsortbm_bonus_nb_tours=osb2.objsortbm_bonus_nb_tours,
                                    objsortbm_bonus_distance=osb2.objsortbm_bonus_distance,
                                    objsortbm_bonus_aggressif=osb2.objsortbm_bonus_aggressif,
                                    objsortbm_bonus_soutien=osb2.objsortbm_bonus_soutien,
                                    objsortbm_bonus_soi_meme=osb2.objsortbm_bonus_soi_meme,
                                    objsortbm_bonus_monstre=osb2.objsortbm_bonus_monstre,
                                    objsortbm_bonus_joueur=osb2.objsortbm_bonus_joueur,
                                    objsortbm_bonus_case=osb2.objsortbm_bonus_case,
                                    objsortbm_bonus_mode=osb2.objsortbm_bonus_mode,
                                    objsortbm_malchance=osb2.objsortbm_malchance,
                                    objsortbm_nb_utilisation_max=osb2.objsortbm_nb_utilisation_max
                                    FROM objets_sorts_bm osb2
                                    WHERE osb2.objsortbm_cod=:objsortbm_cod and osb1.objsortbm_parent_cod=osb2.objsortbm_cod";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":objsortbm_cod" => $objsortsbm->objsortbm_cod), $stmt);
                }
                // Logger les infos pour suivi admin
                $log.="ajoute/modifie de l'objet_sort #".$objsortsbm->objsortbm_cod."\n".obj_diff($clone_os, $objsortsbm);
            }

            writelog($log,'objet_edit', true);
            //echo "<div class='bordiv'><pre>$log</pre></div>";
        }
    }
    //print_r($_REQUEST);

    echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: #800000;\'>SELECTION D\'OBJET</strong>&nbsp;&nbsp;</div>';

    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $row_id = "obj-generique-";
    echo '<form name="selection-objet" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
    echo '<br><strong>Sélection d’un objet générique</strong><br>Code de l\'objet générique :
                    <input data-entry="val" name="objsortbm_gobj_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\'' . $row_id . 'misc_nom\', \'objet_generique\', $(\'#' . $row_id . 'misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","objet_generique","Rechercher un objet générique");\'>
                    &nbsp;<input type="submit" value="Voir/Modifier les sorts BM de cet objet" class="test"></form>';


    echo "<hr>";

    $objsortbm_gobj_cod = 1*(int)$_REQUEST["objsortbm_gobj_cod"] ;
    $objsortbm_obj_cod = 1*(int)$_REQUEST["objsortbm_obj_cod"] ;
    if ($objsortbm_gobj_cod>0 || $objsortbm_obj_cod>0)
    {
        if ($objsortbm_gobj_cod>0)
        {
            $gobj = new objet_generique();
            $gobj->charge($objsortbm_gobj_cod);
            echo "Détail des sorts sur l'objet générique: <strong>#{$gobj->gobj_cod} - {$gobj->gobj_nom}</strong><br>";
            $exemplaires = $gobj->getNombreExemplaires();
            echo "Nombre d'exemplaire basé sur cet objet générique:<br>";
            echo "&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>" . $exemplaires->total . "</strong><br>";
            echo "&nbsp;&nbsp;&nbsp;Inventaire : <strong>" . $exemplaires->inventaire . "</strong> <em style='font-size: x-small'>(possédés par les joueurs, monstres ou PNJ)</em><br>";
            echo "<br>";
        }
        else
        {
            $obj = new objets();
            $obj->charge($objsortbm_obj_cod);
            echo "Détail des conditions d'équipement sur l'<u>OBJET SPECIFIQUE</u>: <strong>#{$obj->obj_cod} - {$obj->obj_nom}</strong><br>";
            echo "L'objet:<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>".$obj->trouve_objet()."</strong><br>";
            echo "<br>";
        }

        echo "<strong>Ajouter/Modifier un sort sur l'objet</strong> :";
        $row_id = "sort-0-";
        echo '<form name="mod-objet-sort" action="' . $_SERVER['PHP_SELF'] . '" method="post">
             <input type="hidden" name="methode" value="sauve">
             <input type="hidden" id="objsortbm_cod" name="objsortbm_cod" value="0">
             <input type="hidden" id="objsortbm_gobj_cod" name="objsortbm_gobj_cod" value="' . ($objsortbm_gobj_cod>0 ? $objsortbm_gobj_cod : "") . '">
             <input type="hidden" id="objsortbm_obj_cod" name="objsortbm_obj_cod" value="'. ($objsortbm_obj_cod>0 ? $objsortbm_obj_cod : "") .'">
             ';
        echo '<table width="100%" class=\'bordiv\'><tr><td>Sélection du type de bonus/malus CODE (<em> ou tbonus_cod</em>) :</td><td>
                <input data-entry="val" name="objsortbm_tbonus_libc" id="' . $row_id . 'libc" type="text" size="5" value="" onChange="setNomByBMLibc(\'' . $row_id . 'misc_nom\', \'bonus_type2\', $(\'#' . $row_id . 'libc\').val().toUpperCase());">
                &nbsp;OU&nbsp;<input data-entry="val" name="objsortbm_tbonus_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByBMCod(\'' . $row_id . 'misc_nom\', \'bonus_type\', $(\'#' . $row_id . 'misc_cod\').val());">
                &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","bonus_type","Rechercher un bonus/malus");\'><br>
                </td></tr>
                <tr><td>Nom du sort BM :</td><td><input type="text" id="objsortbm_nom" name="objsortbm_nom" size="50">&nbsp;<em> si vide, le nom réel du BM sera utilisé</em></td></tr>
                <tr><td>Cout (en PA) :</td><td><input type="text" id="objsortbm_cout" name="objsortbm_cout" size="4">&nbsp;</td></tr>
                <tr><td>Puissance :</td><td><input type="text" id="objsortbm_bonus_valeur" name="objsortbm_bonus_valeur" size="4">&nbsp;<em> (format Dé rolliste) </em></td></tr>
                <tr><td>Nombre de tour(s):</td><td><input type="text" id="objsortbm_bonus_nb_tours" name="objsortbm_bonus_nb_tours" size="4">&nbsp;<em> (format Dé rolliste) </em></td></tr>              
                <tr><td>Ciblage:</td><td>
                        Soi-même: '.create_selectbox("objsortbm_bonus_soi_meme", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objsortbm_bonus_soi_meme")).'
                        Monstres: '.create_selectbox("objsortbm_bonus_monstre", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objsortbm_bonus_monstre")).'
                        Joueurs: '.create_selectbox("objsortbm_bonus_joueur", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objsortbm_bonus_joueur")).'
                        <input type="hidden" name="objsortbm_bonus_case" value="N"><!--Une case: '.create_selectbox("objsortbm_bonus_case", array("O"=>"Oui","N"=>"Non"), 'N', array("id"=>"objsortbm_bonus_case")).'-->
                    </td></tr>
                <tr><td>Distance de la cible:</td><td><input type="text" id="objsortbm_bonus_distance" name="objsortbm_bonus_distance" size="4">&nbsp;</td></tr>                                  
                <tr><td>Type de Bonus/Malus:</td><td>
                        Mode: '.create_selectbox("objsortbm_bonus_mode", array("S"=>"Standard","C"=>"Cumulatif"), 'S', array("id"=>"objsortbm_bonus_mode")).'
                        Soutien: '.create_selectbox("objsortbm_bonus_soutien", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objsortbm_bonus_soutien")).'
                        Agressif: '.create_selectbox("objsortbm_bonus_aggressif", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objsortbm_bonus_aggressif")).'
                    </td></tr>                
                <tr><td>Malchance :</td><td><input type="text" id="objsortbm_malchance" name="objsortbm_malchance" size="4">&nbsp;<em> au format 99.99 c\'est le % d\'échec possible (0 ou vide = toujours réussi)</em></td></tr>
                <tr><td>Nb Utilisation :</td><td><input type="text" id="objsortbm_nb_utilisation_max" name="objsortbm_nb_utilisation_max" size="4">&nbsp;<em> nombre d\'utilisation possible (illimité si vide)</em></td></tr>
                <tr><td>Equip. requis :</td><td>'.create_selectbox("objsortbm_equip_requis", array("O"=>"Oui","N"=>"Non"), 'N', array("id"=>"objsortbm_equip_requis")).'&nbsp;<em> l\'objet doit-t-il être équipé pour pourvoir utiliser le sort?</em></td></tr>
                <tr><td></td><td><input type="submit" name="valider" value="valider" class="test">&nbsp;&nbsp;<input style="display:none" id="bouton-supprimer" type="submit" name="supprimer" value="supprimer" class="test"></td></tr>
                </table>
                </form>';

        echo "<strong><br>Liste des sorts BM sur l'objet</strong> :<br>";
        $objsortsbm = new objets_sorts_bm();

        if ( $objsortbm_gobj_cod>0) {
            $lsorts = $objsortsbm->getBy_objsortbm_gobj_cod($objsortbm_gobj_cod);
        } else {
            $lsorts = $objsortsbm->getBy_objsortbm_obj_cod($objsortbm_obj_cod);
        }
        if ($lsorts)
        {
            echo '<table width="100%" class=\'bordiv\'>';
            echo "<tr><td><input type='button' class='test' value='nouveau' onclick='editObjetSortBM(-1,0);'></td>
                      <td><strong>objsortbm_cod</strong></td>
                      <td><strong>Bonus</strong></td>
                      <td><strong>Nom sur l'objet</strong></td>
                      <td><strong>Coût</strong></td>
                      <td><strong>Puissance</strong></td>
                      <td><strong>Nb de tours(s)</strong></td>
                      <td><strong>Ciblage</strong></td>
                      <td><strong>Distance</strong></td>
                      <td><strong>Type</strong></td>
                      <td><strong>Malchance</strong></td>
                      <td><strong>Utilis.</strong></td>
                      <td><strong>Equip.</strong></td></tr>";
            foreach ($lsorts as $k => $os)
            {
                $bonus = new bonus_type();
                $bonus->charge($os->objsortbm_tbonus_cod);
                if((int)$os->objsortbm_gobj_cod==0 && (int)$os->objsortbm_parent_cod>0){
                    echo "<tr id='sortlist-{$k}'><td>Générique</td>";
                } else{
                    echo "<tr id='sortlist-{$k}'><td><input type='button' class='test' value='modifier' onclick='editObjetSortBM({$k}, {$os->objsortbm_cod});'></td>";
                }
                echo "<td>{$os->objsortbm_cod}</td>
                      <td>{$os->objsortbm_tbonus_cod} ({$bonus->tonbus_libelle}) </td>
                      <td>".$os->getNom()."</td>
                      <td>".$os->objsortbm_cout." PA</td>
                      <td>".$os->objsortbm_bonus_valeur."</td>
                      <td>".$os->objsortbm_bonus_nb_tours."</td>
                      <td>" .( $os->objsortbm_bonus_soi_meme =="O" ? "Soit-même," : "" )
                            .( $os->objsortbm_bonus_monstre =="O" ? "Monstre," : "" )
                            .( $os->objsortbm_bonus_joueur =="O" ? "Joueurs," : "" )
                            .( $os->objsortbm_bonus_case =="O" ? "Case," : "" )."</td>
                      <td>" .$os->objsortbm_bonus_distance."</td>
                      <td>" .( $os->objsortbm_bonus_mode != "S" ? "Cumulatif," : "" )
                            .( $os->objsortbm_bonus_soutien =="O" ? "Soutien," : "" )
                            .( $os->objsortbm_bonus_aggressif =="O" ? "Agressif," : "" )."</td>                                          
                      <td>{$os->objsortbm_malchance}</td>
                      <td>{$os->objsortbm_nb_utilisation_max}</td>
                      <td>".( $os->objsortbm_equip_requis ? "O" : "N" )."</td></tr>";
            }
            echo "</table>";
        }
        else
        {
            echo "<em>Il n'y a pas de sort BM sur cet objet</em>";
        }
    }
    if ($objsortbm_gobj_cod>0)
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * Pensez à ne pas déséquilibrer le jeu (avec des objets trop puissants)<br>
            * N’oubliez pas que TOUS les exemplaires d’un objet générique seront immédiatement ensorcellés<br>
            * Il y a des objets qui ne peuvent pas être équipé <em>(ce n’est pas contrôlé ici)</em><br>
            * Les familiers pourront aussi lancer les sorts BM si l’objet n’a pas besoin d’être équipé<br>
            * L’IA des monstres ne sait pas utiliser ces objets<br>
        <br><p style="text-align:center;"><a href="admin_objet_generique_edit.php?&gobj_cod='.$_REQUEST["objsortbm_gobj_cod"].'">Retour au modification d’objets génériques</a>';
    }
    else
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * Pensez à ne pas déséquilibrer le jeu (avec des objets trop puissants)<br>
            * N’oubliez pas que sort ajouté ici, le seront en plus de ceux du générique<br>
            * Il y a des objets qui ne peuvent pas être équipé <em>(ce n’est pas contrôlé ici)</em><br>
            * Les familiers pourront aussi lancer les sorts BM si l’objet n’a pas besoin d’être équipé<br>
            * L’IA des monstres ne sait pas utiliser ces objets<br>
        <br><p style="text-align:center;"><a href="admin_objet_edit.php?&methode=objet&num_objet='.$_REQUEST["objsortbm_obj_cod"].'">Retour aux modifications de l’objets</a>';
    }
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";