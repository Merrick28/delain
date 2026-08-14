<?php

include "blocks/_header_page_jeu.php";
include_once '../includes/tools.php';

// Nombre maximum de Bonus/Malus qu'on peut rattacher à un même objet (générique ou spécifique)
define('OBJUSEBM_NB_MAX', 1);

?>

    <script>//# sourceURL=admin_objet_usage_bm.js

        function setNomByBMCod(divname, table, cod) { // fonction de mise à jour d'un champ nom quand on connait le cod
            //executer le service asynchrone
            $("#" + divname).text("");
            $("#div_aide_bonus").css("display", "none");
            $("#div_aide_malus").css("display", "none");
            runAsync({request: "get_table_nom", data: {table: table, cod: cod}}, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.nom)) {
                    $("#" + divname).text(d.data.nom);
                    $("#" + divname.substr(0, divname.length - 8) + 'libc').val((d.data.nom.substr(0, 3)));
                    if (arr_bonmal[d.data.nom.substr(0, 3)] == 'MAL'){
                        $("#div_aide_bonus").css("display", "none");
                        $("#div_aide_malus").css("display", "block");
                    } else {
                        $("#div_aide_bonus").css("display", "block");
                        $("#div_aide_malus").css("display", "none");
                    }
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
            $("#div_aide_bonus").css("display", "none");
            $("#div_aide_malus").css("display", "none");
            runAsync({request: "get_table_nom", data: {table: table, cod: cod}}, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.nom)) {
                    $("#" + divname).text(d.data.nom);
                    $("#" + divname.substr(0, divname.length - 8) + 'misc_cod').val((d.data.cod));
                    if (arr_bonmal[d.data.nom.substr(0, 3)] == 'MAL'){
                        $("#div_aide_bonus").css("display", "none");
                        $("#div_aide_malus").css("display", "block");
                    } else {
                        $("#div_aide_bonus").css("display", "block");
                        $("#div_aide_malus").css("display", "none");
                    }
                }
                else {
                    $("#" + divname).text('');
                    $("#" + divname.substr(0, divname.length - 8) + 'misc_cod').val((''));
                }
            });
        }

        function editObjetUsageBM(row, objusebm_cod) {
            //executer le service asynchrone
            $('tr[id^="usagelist-"]').removeClass("soustitre2");
            $('#bouton-supprimer').hide();
            if (row>=0)
            {
                $('#usagelist-'+row).addClass("soustitre2");
                $('#bouton-supprimer').show();
            }

            runAsync({request: "get_table_info", data: {info: "objets_usage_bm", objusebm_cod: objusebm_cod}}, function (d) {
                if (d.resultat == 0)
                {
                    var data = d.data ;
                    $("#objusebm_cod").val(data.objusebm_cod ? data.objusebm_cod : 0);
                    $("#usage-0-misc_cod").val(data.objusebm_tbonus_cod ? data.objusebm_tbonus_cod : "");
                    if ($("#usage-0-misc_cod").val()>0)
                    {
                        //setNomByTableCod('usage-0-misc_nom', 'bonus_type', $("#usage-0-misc_cod").val());
                        setNomByBMCod('usage-0-misc_cod', 'bonus_type', $("#usage-0-misc_cod").val());
                    }
                    else
                    {
                        $("#usage-0-misc_cod").val("");
                        $("#usage-0-libc").val("");
                        $("#div_aide_bonus").css("display", "none");
                        $("#div_aide_malus").css("display", "none");
                    }
                    $("#objusebm_cout").val(data.objusebm_cout ? data.objusebm_cout : "");
                    $("#objusebm_bonus_valeur").val(data.objusebm_bonus_valeur ? data.objusebm_bonus_valeur : "1");
                    $("#objusebm_bonus_nb_tours").val(data.objusebm_bonus_nb_tours ? data.objusebm_bonus_nb_tours : "1");
                    $("#objusebm_bonus_soi_meme").val((!data.objusebm_bonus_soi_meme || data.objusebm_bonus_soi_meme =='N') ? 'N' : 'O');
                    $("#objusebm_bonus_monstre").val((!data.objusebm_bonus_monstre || data.objusebm_bonus_monstre =='N') ? 'N' : 'O');
                    $("#objusebm_bonus_familier").val((!data.objusebm_bonus_familier || data.objusebm_bonus_familier =='N') ? 'N' : data.objusebm_bonus_familier);
                    $("#objusebm_bonus_joueur").val((!data.objusebm_bonus_joueur || data.objusebm_bonus_joueur =='N') ? 'N' : data.objusebm_bonus_joueur);
                    $("#objusebm_bonus_case").val((!data.objusebm_bonus_case || data.objusebm_bonus_case =='N') ? 'N' : 'O');
                    $("#objusebm_bonus_distance").val(data.objusebm_bonus_distance ? data.objusebm_bonus_distance : "0");
                    $("#objusebm_bonus_aggressif").val((!data.objusebm_bonus_aggressif || data.objusebm_bonus_aggressif =='N') ? 'N' : 'O');
                    $("#objusebm_bonus_soutien").val((!data.objusebm_bonus_soutien || data.objusebm_bonus_soutien =='N') ? 'N' : 'O');
                    $("#objusebm_bonus_mode").val(data.objusebm_bonus_mode ? data.objusebm_bonus_mode : "S");
                    $("#objusebm_malchance").val(data.objusebm_malchance ? data.objusebm_malchance : "0");
                    $("#objusebm_nb_utilisation_max").val(data.objusebm_nb_utilisation_max ? data.objusebm_nb_utilisation_max : "");
                    $("#objusebm_vide_detruit").val((!data.objusebm_vide_detruit || data.objusebm_vide_detruit =='N') ? 'N' : 'O');
                }
            });
        }

        <?php

        // LISTE DES Bonus/Malus
        $req_bm = "select tbonus_libc, CASE WHEN tbonus_compteur='O' THEN '[compteur] ' ELSE '' END || tonbus_libelle || CASE WHEN tbonus_cumulable='O' THEN ' - [cumulable]' ELSE '' END as tonbus_libelle, tbonus_gentil_positif
                        from bonus_type
                        order by tonbus_libelle ";

        // Écriture du JS qui dit si on a un bonus ou un malus
        $stmt = $pdo->query($req_bm);
        echo "var arr_bonmal = [];\n";
        while ($result = $stmt->fetch())
        {
            $clef   = $result['tbonus_libc'];
            $valeur = ($result['tbonus_gentil_positif'] == 't') ? 'BON' : 'MAL';
            echo "arr_bonmal['$clef'] = '$valeur';\n";
        }
        echo "</script>";

        //
        //Contenu de la div de droite
        //
        $contenu_page = '';
        ob_start();
        ?>
        <title>AFFECTATION DE BM SUR L'UTILISATION DES OBJETS / OBJETS GENERIQUES</title>
<?php

$droit_modif = 'dcompt_objet';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{
    //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; die();

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
            $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) ajout/modification de BM sur l'utilisation des objets:\n";

            $objusebm = new objets_usage_bm();
            $objusebm_cod = (1*(int)$_REQUEST["objusebm_cod"]);

            if ($objusebm_cod>0)
            {
                $objusebm->charge($objusebm_cod);
                $new = false ;
            }
            else
            {
                $new = true ;
            }

            // Cas d'une suppression
            if (($_REQUEST["supprimer"] == "supprimer") && ($objusebm_cod>0))
            {
                $log.="supression de l'objet_usage #".$objusebm->objusebm_cod."\n".obj_diff(new objets_usage_bm, $objusebm);
                $objusebm->delete($objusebm_cod);
            }
            else
            {
                // Cas d'une creation/modification

                // Contrôle du nombre max de BM rattachables (uniquement lors d'un AJOUT, pas d'une modification)
                $limite_bm_atteinte = false;
                if ($new)
                {
                    $bm_gobj_cod = $_REQUEST["objusebm_gobj_cod"]== "" ? 0 : 1*(int)$_REQUEST["objusebm_gobj_cod"];
                    $bm_obj_cod  = $_REQUEST["objusebm_obj_cod"]== "" ? 0 : 1*(int)$_REQUEST["objusebm_obj_cod"];

                    if ($bm_gobj_cod>0) {
                        $lusages_existants = $objusebm->getBy_objusebm_gobj_cod($bm_gobj_cod);
                    } else {
                        $lusages_existants = $objusebm->getBy_objusebm_obj_cod($bm_obj_cod);
                    }

                    if ($lusages_existants && count($lusages_existants) >= OBJUSEBM_NB_MAX)
                    {
                        $limite_bm_atteinte = true;
                    }
                }

                if ($limite_bm_atteinte)
                {
                    $log.="tentative d'ajout de BM refusée sur gobj_cod={$bm_gobj_cod} / obj_cod={$bm_obj_cod} : nombre maximum de ".OBJUSEBM_NB_MAX." BM déjà atteint\n";
                    echo '<div class="hr" style="color:red;"><strong>Impossible d\'ajouter ce Bonus/Malus : le nombre maximum de '.OBJUSEBM_NB_MAX.' BM par objet est déjà atteint.</strong></div>';
                }
                else
                {
                    $clone_os = clone $objusebm;

                    $objusebm->objusebm_parent_cod = null ;
                    $objusebm->objusebm_gobj_cod = $_REQUEST["objusebm_gobj_cod"]== "" ? null : 1*(int)$_REQUEST["objusebm_gobj_cod"];
                    $objusebm->objusebm_obj_cod = $_REQUEST["objusebm_obj_cod"]== "" ? null : 1*(int)$_REQUEST["objusebm_obj_cod"] ;
                    $objusebm->objusebm_tbonus_cod = 1*(int)$_REQUEST["objusebm_tbonus_cod"];
                    $objusebm->objusebm_cout = $_REQUEST["objusebm_cout"]=='' ? 4 : 1*(int)$_REQUEST["objusebm_cout"];
                    $objusebm->objusebm_bonus_valeur = $_REQUEST["objusebm_bonus_valeur"]=='' ? "1" : $_REQUEST["objusebm_bonus_valeur"];
                    $objusebm->objusebm_bonus_nb_tours = $_REQUEST["objusebm_bonus_nb_tours"]=='' ? "1" : $_REQUEST["objusebm_bonus_nb_tours"];
                    $objusebm->objusebm_bonus_distance = $_REQUEST["objusebm_bonus_distance"]=='' ?  0 : (int)$_REQUEST["objusebm_bonus_distance"];
                    $objusebm->objusebm_bonus_aggressif = $_REQUEST["objusebm_bonus_aggressif"]=='' ? "N" : $_REQUEST["objusebm_bonus_aggressif"];
                    $objusebm->objusebm_bonus_soutien = $_REQUEST["objusebm_bonus_soutien"]=='' ? "N" : $_REQUEST["objusebm_bonus_soutien"];
                    $objusebm->objusebm_bonus_soi_meme = $_REQUEST["objusebm_bonus_soi_meme"]=='' ? "O" : $_REQUEST["objusebm_bonus_soi_meme"];
                    $objusebm->objusebm_bonus_monstre = $_REQUEST["objusebm_bonus_monstre"]=='' ? "O" : $_REQUEST["objusebm_bonus_monstre"];
                    $objusebm->objusebm_bonus_familier = $_REQUEST["objusebm_bonus_familier"]=='' ? "O" : $_REQUEST["objusebm_bonus_familier"];
                    $objusebm->objusebm_bonus_joueur = $_REQUEST["objusebm_bonus_joueur"]=='' ? "O" : $_REQUEST["objusebm_bonus_joueur"];
                    $objusebm->objusebm_bonus_case = $_REQUEST["objusebm_bonus_case"]=='' ? "N" : $_REQUEST["objusebm_bonus_case"];
                    $objusebm->objusebm_bonus_mode = $_REQUEST["objusebm_bonus_mode"]=='' ? "S" : $_REQUEST["objusebm_bonus_mode"];
                    $objusebm->objusebm_malchance = $_REQUEST["objusebm_malchance"]=='' ? 0 : 1*(float)$_REQUEST["objusebm_malchance"];
                    $objusebm->objusebm_nb_utilisation_max = $_REQUEST["objusebm_nb_utilisation_max"]=='' ? null : 1*(int)$_REQUEST["objusebm_nb_utilisation_max"];
                    $objusebm->objusebm_nb_utilisation = 0 ;
                    $objusebm->objusebm_vide_detruit = $_REQUEST["objusebm_vide_detruit"]=="O" ? "O" : "N" ;
                    $objusebm->stocke($new);

                    // dans le cas d'un generique mise à jour des repliques déjà en jeu !
                    if ($_REQUEST["objusebm_gobj_cod"]!="")
                    {
                        $req = "UPDATE objets_usage_bm osb1 SET 
                                    objusebm_tbonus_cod=osb2.objusebm_tbonus_cod,
                                    objusebm_cout=osb2.objusebm_cout,
                                    objusebm_bonus_valeur=osb2.objusebm_bonus_valeur,
                                    objusebm_bonus_nb_tours=osb2.objusebm_bonus_nb_tours,
                                    objusebm_bonus_distance=osb2.objusebm_bonus_distance,
                                    objusebm_bonus_aggressif=osb2.objusebm_bonus_aggressif,
                                    objusebm_bonus_soutien=osb2.objusebm_bonus_soutien,
                                    objusebm_bonus_soi_meme=osb2.objusebm_bonus_soi_meme,
                                    objusebm_bonus_monstre=osb2.objusebm_bonus_monstre,
                                    objusebm_bonus_familier=osb2.objusebm_bonus_familier,
                                    objusebm_bonus_joueur=osb2.objusebm_bonus_joueur,
                                    objusebm_bonus_case=osb2.objusebm_bonus_case,
                                    objusebm_bonus_mode=osb2.objusebm_bonus_mode,
                                    objusebm_malchance=osb2.objusebm_malchance,
                                    objusebm_nb_utilisation_max=osb2.objusebm_nb_utilisation_max
                                    FROM objets_usage_bm osb2
                                    WHERE osb2.objusebm_cod=:objusebm_cod and osb1.objusebm_parent_cod=osb2.objusebm_cod";
                        $stmt = $pdo->prepare($req);
                        $stmt = $pdo->execute(array(":objusebm_cod" => $objusebm->objusebm_cod), $stmt);
                    }
                    // Logger les infos pour suivi admin
                    $log.="ajoute/modifie de l'objet_usage #".$objusebm->objusebm_cod."\n".obj_diff($clone_os, $objusebm);
                }
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
                    <input data-entry="val" name="objusebm_gobj_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\'' . $row_id . 'misc_nom\', \'objet_generique\', $(\'#' . $row_id . 'misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","objet_generique","Rechercher un objet générique");\'>
                    &nbsp;<input type="submit" value="Voir/Modifier les utilisation BM de cet objet" class="test"></form>';


    echo "<hr>";

    $objusebm_gobj_cod = 1*(int)$_REQUEST["objusebm_gobj_cod"] ;
    $objusebm_obj_cod = 1*(int)$_REQUEST["objusebm_obj_cod"] ;
    if ($objusebm_gobj_cod>0 || $objusebm_obj_cod>0)
    {
        if ($objusebm_gobj_cod>0)
        {
            $gobj = new objet_generique();
            $gobj->charge($objusebm_gobj_cod);
            echo "Détail des utilisations sur l'objet générique: <strong>#{$gobj->gobj_cod} - {$gobj->gobj_nom}</strong><br>";
            $exemplaires = $gobj->getNombreExemplaires();
            echo "Nombre d'exemplaire basé sur cet objet générique:<br>";
            echo "&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>" . $exemplaires->total . "</strong><br>";
            echo "&nbsp;&nbsp;&nbsp;Inventaire : <strong>" . $exemplaires->inventaire . "</strong> <em style='font-size: x-small'>(possédés par les joueurs, monstres ou PNJ)</em><br>";
            echo "<br>";
        }
        else
        {
            $obj = new objets();
            $obj->charge($objusebm_obj_cod);
            echo "Détail des conditions d'équipement sur l'<u>OBJET SPECIFIQUE</u>: <strong>#{$obj->obj_cod} - {$obj->obj_nom}</strong><br>";
            echo "L'objet:<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>".$obj->trouve_objet()."</strong><br>";
            echo "<br>";
        }

        echo "<strong>Ajouter/Modifier une utilisation de l'objet</strong> :";
        $row_id = "usage-0-";
        echo '<form name="mod-objet-usage" action="' . $_SERVER['PHP_SELF'] . '" method="post">
             <input type="hidden" name="methode" value="sauve">
             <input type="hidden" id="objusebm_cod" name="objusebm_cod" value="0">
             <input type="hidden" id="objusebm_gobj_cod" name="objusebm_gobj_cod" value="' . ($objusebm_gobj_cod>0 ? $objusebm_gobj_cod : "") . '">
             <input type="hidden" id="objusebm_obj_cod" name="objusebm_obj_cod" value="'. ($objusebm_obj_cod>0 ? $objusebm_obj_cod : "") .'">
             ';
        echo '<table width="100%" class=\'bordiv\'><tr><td>Sélection du type de bonus/malus CODE (<em> ou tbonus_cod</em>) :</td><td>
                <input data-entry="val" name="objusebm_tbonus_libc" id="' . $row_id . 'libc" type="text" size="5" value="" onChange="setNomByBMLibc(\'' . $row_id . 'misc_nom\', \'bonus_type2\', $(\'#' . $row_id . 'libc\').val().toUpperCase());">
                &nbsp;OU&nbsp;<input data-entry="val" name="objusebm_tbonus_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByBMCod(\'' . $row_id . 'misc_nom\', \'bonus_type\', $(\'#' . $row_id . 'misc_cod\').val());">
                &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","bonus_type","Rechercher un bonus/malus");\'><br>
                            <div id=\'div_aide_bonus\' style=\'display: none;\'>Une valeur <strong>positive</strong> est
                                <strong>bénéfique</strong>, et une valeur <strong>négative</strong> est
                                <strong>délétère</strong>
                            </div>
                            <div id=\'div_aide_malus\' style=\'display: none;\'>Une valeur <strong>positive</strong> est
                                <strong>délétère</strong>, et une valeur <strong>négative</strong> est
                                <strong>bénéfique</strong>
                            </div>
                </td></tr>
                <tr><td>Cout (en PA) :</td><td><input type="text" id="objusebm_cout" name="objusebm_cout" size="4">&nbsp;</td></tr>
                <tr><td>Puissance :</td><td><input type="text" id="objusebm_bonus_valeur" name="objusebm_bonus_valeur" size="4">&nbsp;<em> (format Dé rolliste, dans le cas ou le nombre de tour est 0, 0 pour supprimer bonus+malus ou -X/+X pour reduire le bonus/malus de X)</em></td></tr>
                <tr><td>Nombre de tour(s):</td><td><input type="text" id="objusebm_bonus_nb_tours" name="objusebm_bonus_nb_tours" size="4">&nbsp;<em> (format Dé rolliste, mettre <b>0 pour retirer/supprimer</b> un bonus/malus au lieu de le donner) </em></td></tr>              
                <tr><td>Ciblage:</td><td>
                        Soi-même: '.create_selectbox("objusebm_bonus_soi_meme", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objusebm_bonus_soi_meme")).'
                        Monstres: '.create_selectbox("objusebm_bonus_monstre", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objusebm_bonus_monstre")).'
                        Familiers: '.create_selectbox("objusebm_bonus_familier", array("O"=>"Oui","3"=>"Triplette","C"=>"Coterie","N"=>"Non"), 'O', array("id"=>"objusebm_bonus_familier")).'
                        Joueurs: '.create_selectbox("objusebm_bonus_joueur", array("O"=>"Oui","3"=>"Triplette","C"=>"Coterie","N"=>"Non"), 'O', array("id"=>"objusebm_bonus_joueur")).'
                        <input type="hidden" name="objusebm_bonus_case" value="N"><!--Une case: '.create_selectbox("objusebm_bonus_case", array("O"=>"Oui","N"=>"Non"), 'N', array("id"=>"objusebm_bonus_case")).'-->
                    </td></tr>
                <tr><td>Distance de la cible:</td><td><input type="text" id="objusebm_bonus_distance" name="objusebm_bonus_distance" size="4">&nbsp;</td></tr>                                  
                <tr><td>Type de Bonus/Malus:</td><td>
                        Mode: '.create_selectbox("objusebm_bonus_mode", array("S"=>"Standard","C"=>"Cumulatif"), 'S', array("id"=>"objusebm_bonus_mode")).'
                        Soutien: '.create_selectbox("objusebm_bonus_soutien", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objusebm_bonus_soutien")).'
                        Agressif: '.create_selectbox("objusebm_bonus_aggressif", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objusebm_bonus_aggressif")).'
                    </td></tr>                
                <tr><td>Malchance :</td><td><input type="text" id="objusebm_malchance" name="objusebm_malchance" size="4">&nbsp;<em> au format 99.99 c\'est le % d\'échec possible (0 ou vide = toujours réussi)</em></td></tr>
                <tr><td>Nb Utilisation :</td><td><input type="text" id="objusebm_nb_utilisation_max" name="objusebm_nb_utilisation_max" size="4">&nbsp;<em> nombre d\'utilisation possible (illimité si vide)</em></td></tr>
                <tr><td>Destruction de l\'objet? :</td><td>'.create_selectbox("objusebm_vide_detruit", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objusebm_vide_detruit")).'&nbsp;<em> l\'objet doit-t-il être détruit s\'il est vide?</em></td></tr>
                <tr><td></td><td><input type="submit" name="valider" value="valider" class="test">&nbsp;&nbsp;<input style="display:none" id="bouton-supprimer" type="submit" name="supprimer" value="supprimer" class="test"></td></tr>
                </table>
                </form>';

        echo "<strong><br>Liste des utilisations BM de l'objet</strong> :<br>";
        $objusebm = new objets_usage_bm();

        if ( $objusebm_gobj_cod>0) {
            $lusages = $objusebm->getBy_objusebm_gobj_cod($objusebm_gobj_cod);
        } else {
            $lusages = $objusebm->getBy_objusebm_obj_cod($objusebm_obj_cod);
        }
        if ($lusages)
        {
            $nb_bm_actuel = count($lusages);
            $quota_bm_atteint = ($nb_bm_actuel >= OBJUSEBM_NB_MAX);

            echo '<table width="100%" class=\'bordiv\'>';
            if ($quota_bm_atteint)
            {
                echo "<tr><td><input type='button' class='test' value='nouveau' onclick=\"alert('Quota atteint : ".OBJUSEBM_NB_MAX." BM maximum sont deja rattaches a cet objet. Supprimez-en un avant d’en ajouter un nouveau.');\"></td>
                      <td><strong>objusebm_cod</strong></td>";
            }
            else
            {
                echo "<tr><td><input type='button' class='test' value='nouveau' onclick='editObjetUsageBM(-1,0);'></td>
                      <td><strong>objusebm_cod</strong></td>";
            }
            echo "
                      <td><strong>Bonus</strong></td>
                      <td><strong>Coût</strong></td>
                      <td><strong>Puissance</strong></td>
                      <td><strong>Nb de tours(s)</strong></td>
                      <td><strong>Ciblage</strong></td>
                      <td><strong>Distance</strong></td>
                      <td><strong>Type</strong></td>
                      <td><strong>Malchance</strong></td>
                      <td><strong>Utilis.</strong></td>
                      <td><strong>Destruction?.</strong></td></tr>";
            foreach ($lusages as $k => $os)
            {
                $bonus = new bonus_type();
                $bonus->charge($os->objusebm_tbonus_cod);
                if((int)$os->objusebm_gobj_cod==0 && (int)$os->objusebm_parent_cod>0){
                    echo "<tr id='usagelist-{$k}'><td>Générique</td>";
                } else{
                    echo "<tr id='usagelist-{$k}'><td><input type='button' class='test' value='modifier' onclick='editObjetUsageBM({$k}, {$os->objusebm_cod});'></td>";
                }
                echo "<td>{$os->objusebm_cod}</td>
                      <td>{$os->objusebm_tbonus_cod} ({$bonus->tonbus_libelle}) </td>
                      <td>".$os->objusebm_cout." PA</td>
                      <td>".$os->objusebm_bonus_valeur."</td>
                      <td>".$os->objusebm_bonus_nb_tours."</td>
                      <td>" .( $os->objusebm_bonus_soi_meme =="O" ? "Soit-même," : "" )
                        .( $os->objusebm_bonus_monstre =="O" ? "Monstres," : "" )
                        .( $os->objusebm_bonus_familier =="O" ? "Familiers," : ( $os->objusebm_bonus_familier =="C" ? "Fam. Coterie," : ( $os->objusebm_bonus_familier =="3" ? "Fam. Triplette," : "" ) ) )
                        .( $os->objusebm_bonus_joueur =="O" ? "Joueurs," : ( $os->objusebm_bonus_joueur =="C" ? "Coterie," : ( $os->objusebm_bonus_joueur =="3" ? "Triplette," : "" ) ) )
                        .( $os->objusebm_bonus_case =="O" ? "Case," : "" )."</td>
                      <td>" .$os->objusebm_bonus_distance."</td>
                      <td>" .( $os->objusebm_bonus_mode != "S" ? "Cumulatif," : "" )
                        .( $os->objusebm_bonus_soutien =="O" ? "Soutien," : "" )
                        .( $os->objusebm_bonus_aggressif =="O" ? "Agressif," : "" )."</td>                                          
                      <td>{$os->objusebm_malchance}</td>
                      <td>{$os->objusebm_nb_utilisation_max}</td>
                      <td>".( $os->objusebm_vide_detruit ? "O" : "N" )."</td></tr>";
            }
            echo "</table>";
        }
        else
        {
            echo "<em>Il n'y a pas d'usage BM sur cet objet</em>";
        }
    }
    if ($objusebm_gobj_cod>0)
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * On limite à <strong>1 seul rattachement</strong> de BM par générique<br>
            * Pensez à ne pas déséquilibrer le jeu (avec des objets trop puissants)<br>
            * N’oubliez pas que TOUS les exemplaires d’un objet générique seront immédiatement modifiés<br>
            * Il y a des objets qui ne peuvent pas être utilisé <em>(ce n’est pas contrôlé ici)</em><br>
            * Les familiers pourront aussi utiliser l’objet<br>
            * L’IA des monstres ne sait pas utiliser ces objets<br>
        <br><p style="text-align:center;"><a href="admin_objet_generique_edit.php?&gobj_cod='.$_REQUEST["objusebm_gobj_cod"].'">Retour au modification d’objets génériques</a>';
    }
    else
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * On limite à <strong>1 seul rattachement</strong> de BM par objet (<i>l’utilisation objet remplace celle du générique</i>) <br>
            * Pensez à ne pas déséquilibrer le jeu (avec des objets trop puissants)<br>
            * N’oubliez pas que l’utilisation ajoutée ici, le seront en plus de ceux du générique<br>
            * Il y a des objets qui ne peuvent pas être utilisé <em>(ce n’est pas contrôlé ici)</em><br>
            * Les familiers pourront aussi utiliser l’objet<br> 
            * L’IA des monstres ne sait pas utiliser ces objets<br>
            * La suppression de bonus/malus ne fonctionne pas pour les caracs de perso<br>
        <br><p style="text-align:center;"><a href="admin_objet_edit.php?&methode=objet&num_objet='.$_REQUEST["objusebm_obj_cod"].'">Retour aux modifications de l’objets</a>';
    }
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";