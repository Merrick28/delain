<?php

include "blocks/_header_page_jeu.php";

include_once '../includes/tools.php';


?>

    <script>//# sourceURL=admin_teleportation.js
        function setPositionDesc() {
            //executer le service asynchrone
            $("#position").text("");
            var pos_x = 1 * $("#pos_x").val();
            var pos_y = 1 * $("#pos_y").val();
            var pos_etage = 1 * $("#pos_etage").val();
            runAsync({
                request: "get_table_info",
                data: {info: "position_description", pos_x: pos_x, pos_y: pos_y, pos_etage: pos_etage}
            }, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.pos_cod)) {
                    $("#position_desc").html(d.data.position_desc);
                }
                else {
                    $("#position_desc").text("ATTENTION: position innexistante!!!");
                }
            });
        }

        function setNomAndPosPerso(divname, cod) {
            //executer le service asynchrone
            $("#" + divname).text("");
            runAsync({request: "get_table_info", data: {info: "perso_pos", perso_cod: cod}}, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.perso_nom)) {
                    if (s_option == "monstre" && d.data.perso_type_perso != 2)
                        $("#" + divname).html('Vous n\'avez pas les droits pour téléporter ce perso.');
                    else
                        $("#" + divname).html(d.data.perso_nom + ' <em style="font-size:10px;"> (X=' + d.data.pos_x + ' X=' + d.data.pos_y + ' ' + d.data.etage_libelle + ')</em>');
                }
            });
        }

        function listCoterie(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_coterie_pos", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTeleportationtList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) dans sa coterie...");
            });
        }

        function listSurZone(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_zone_pos", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTeleportationtList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) sur sa position...");
            });
        }

        function listControleur(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_compte_pos", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTeleportationtList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce controleur...");
            });
        }
        function listPersoEtage(etage_numero) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_etage_pos", type_perso: 1, etage_numero: etage_numero}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTeleportationtList(data.perso_cod) && isAuthorized(data.perso_type_perso) && (nb_perso<100))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (nb_perso==100) content += "<br>L'affichage a été limité aux 100 premiers persos.";
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce controleur...");
            });
        }

        function addFromSearchListAll(type) {
            $('div[id^="s-list-"]').each(function () {
                var type_perso = $('#s-list-' + this.id.substr(7)).attr("data-type_perso");
                if (type == 0 || type == type_perso) addFromSearchList(this.id.substr(7))
            });
        }

        function addFromSearchList(k) {
            var perso_cod = $('#s-list-' + k).attr("data-perso_cod");
            $("#add-button").trigger("click");  // simuler le click sur le bouton ajouter
            var id = $('tr[id^="row-0-"]:last').attr("id");
            $('#' + id + 'aqelem_misc_cod').val(perso_cod);
            $('#' + id + 'aqelem_misc_cod').trigger("change");
            $('#s-list-' + k).remove();
        }

        function isInTeleportationtList(cod) {
            var isInList = false;
            $('tr[id^="row-0-"]').each(function () {
                if ($('#' + this.id + 'aqelem_misc_cod').val() == cod) isInList = true;
            });
            return isInList;
        }

        function isAuthorized(type_perso) { // l'admin monstre ne peut téléporter que les monstres et on ne téléporte aucun familier
            if (type_perso == 3) return false; else if (s_option == "monstre" && type_perso != 2) return false; else return true;
        }

        function countTeleportationtList() {
            var count = 0;
            $('tr[id^="row-0-"]').each(function () {
                if ($('#' + this.id + 'aqelem_misc_cod').val() > 0) count++;
            });
            return count;
        }

        function checkForm() {
            if (($("#pos_x").val() == "") || ($("#pos_y").val() == "")) {
                alert("Vous devez saisir des coordonnées X et Y de destination!");
                return false;
            }
            var nb_perso = countTeleportationtList();
            if (nb_perso == 0) {
                alert("Vous devez sélectionner des persos à téléporter!");
                return false;
            }
            return true; //confirm("Etes-vous sûr de vouloir téléporter "+nb_perso+" perso(s) en X="+$("#pos_x").val()+" Y="+$("#pos_y").val()+" "+$("#pos_etage option:selected").text()+"?");
        }

    </script>

<?php

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
    <title>TÉLÉPORTATION PERSOS / MONSTRES</title>
<?php

$droit_modif = 'dcompt_modif_perso';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{
    $s_option = ($droit['modif_perso'] == 'N') ? "monstre" : "";                               // droit de tp de monstre uniquement
    echo "<script> var s_option='{$s_option}'; </script>"; // Injection javascript

    //=======================================================================================
    // == Main
    //=======================================================================================
    //-- traitement des actions
    if (isset($_REQUEST['methode']))
    {
        // Traitement des actions de téléportation
        if ($_REQUEST['methode'] == "teleporte")
        {
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>";
            $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) Téléportation:\n";

            // Récupération du code traitement_perso_admin (dev mode flash : on réfléchi pas on reprend ce qui marche ailleurs) !!!
            $new_etage = $_REQUEST['pos_etage'];
            $pos_x = $_REQUEST['pos_x'];
            $pos_y = $_REQUEST['pos_y'];
            $err_depl = 0;
            $req = "select pos_cod, etage_arene,
                    pos_x::text || ', ' || pos_y::text || ', ' || pos_etage::text || ' (' || etage_libelle || ')' as position from positions
                inner join etage on etage_numero = pos_etage
                where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $new_etage ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() == 0)
            {
                echo "<div class='bordiv'>Erreur ! Aucune position trouvée à ces coordonnées</div>";
                $err_depl = 1;
            }
            $result = $stmt->fetch();
            $pos_cod = $result['pos_cod'];
            $arene = $result['etage_arene'];
            $nv_position = $result['position'];
            $req = "select mur_pos_cod from murs where mur_pos_cod = $pos_cod ";
            $stmt = $pdo->query($req);
            if ($stmt->rowCount() != 0)
            {
                echo "<div class='bordiv'>Erreur ! Impossible de déplacer le perso : un mur en destination.</div>";
                $err_depl = 1;
            }
            if ($err_depl == 0)
            {
                foreach ($_REQUEST["aqelem_misc_cod"][0] as $k => $mod_perso_cod)
                    if ($mod_perso_cod > 0)
                    {
                        // Position de départ
                        $req_perso = "select perso_nom, perso_type_perso from perso where perso_cod = $mod_perso_cod ";
                        $stmt = $pdo->query($req_perso);
                        $result = $stmt->fetch();
                        $mod_perso_nom = $result['perso_nom'];
                        $mod_perso_type_perso = $result['perso_type_perso'];
                        $log .= "Téléportation du perso {$mod_perso_nom} ({$mod_perso_cod}) : ";

                        if ($mod_perso_type_perso != 2 && $s_option == "monstre")
                        {
                            // Un admin monstre qui force le passage pour ajouter un perso!!!
                            $log .= "vous n'avez pas les droits suffisants pour ce perso!";
                        } else
                        {
                            // insertion dun évènement
                            $texte_evt = "[perso_cod1] a été déplacé par un admin quête.";
                            $req       = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible) 
                            values(43,now(),$mod_perso_cod,'$texte_evt','N','N') ";
                            $stmt      = $pdo->query($req);
                            // effacement des locks
                            $req  = "delete from lock_combat where lock_cible = $mod_perso_cod ";
                            $stmt = $pdo->query($req);
                            $req  = "delete from lock_combat where lock_attaquant = $mod_perso_cod ";
                            $stmt = $pdo->query($req);

                            // Position de départ
                            $req_position = "select pos_cod, pos_x::text || ', ' || pos_y::text || ', ' || pos_etage::text || ' (' || etage_libelle || ')' as position,
                                    etage_arene
                                from perso_position
                                inner join positions on pos_cod = ppos_pos_cod
                                inner join etage on etage_numero = pos_etage
                                where ppos_perso_cod = $mod_perso_cod ";
                            $stmt = $pdo->query($req_position);
                            $result = $stmt->fetch();
                            $anc_pos_cod = $result['pos_cod'];
                            $anc_arene = $result['etage_arene'];
                            $anc_position = $result['position'];
                            $log .= "Déplacement de $anc_position vers $nv_position.";

                            // déplacement
                            $req = "update perso_position set ppos_pos_cod = $pos_cod where ppos_perso_cod = $mod_perso_cod ";
                            $stmt = $pdo->query($req);

                            switch ($anc_arene . $arene)
                            {
                                case 'NO':    // D’un étage normal vers une arène
                                    $req = "delete from perso_arene where parene_perso_cod = $mod_perso_cod ";
                                    $stmt = $pdo->query($req);
                                    $req = "insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
                                        values($mod_perso_cod, $new_etage, $anc_pos_cod, now()) ";
                                    $stmt = $pdo->query($req);
                                    $log .= "\nCette position est en arène : le personnage en ressortira à sa position d’origine.";
                                    break;

                                case 'OO':    // D’une arène vers une autre
                                    $req = "update perso_arene set parene_etage_numero = $new_etage where parene_perso_cod = $mod_perso_cod";
                                    $stmt = $pdo->query($req);
                                    $log .= "\nCette position est en arène, le perso était déjà dans une arène : il ressortira à la position d’où il est rentré dans la première arène.";
                                    break;

                                case 'ON':    // D’une arène vers un étage normal
                                    $req = "delete from perso_arene where parene_perso_cod = $mod_perso_cod ";
                                    $stmt = $pdo->query($req);
                                    $log .= "\nAttention ! Le perso était en arène : sa position d’entrée dans l’arène est perdue !";
                                    // Si on ne le supprimait pas, on empêcherait le perso de rentrer à nouveau en arène...
                                    break;

                                case 'NN':    // D’un étage normal vers un étage normal
                                    // Rien à faire
                                    break;
                            }
                        }
                        $log .= "\n";
                    }
                writelog($log, 'perso_edit');
                echo "<div class='bordiv'><pre>$log</pre></div>";
            }
        }
    }
    //print_r($_REQUEST);

    echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: #800000;\'>TÉLÉPORTATION DE GROUPE</strong>&nbsp;&nbsp;</div>';

    //=======================================================================================
    // == Constantes quete_auto
    //=======================================================================================
    //$request_select_etage_ref = "SELECT null etage_cod, 'Aucune restriction' etage_libelle, null etage_numero UNION SELECT etage_cod, etage_libelle, etage_numero from etage where etage_reference = etage_numero order by etage_numero desc" ;
    $request_select_etage = "SELECT etage_numero, case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle from etage order by etage_reference desc, etage_numero";

    echo 'Liste des persos à téléporter:<br><br>';

    echo '<form  method="post" onkeypress="return event.keyCode != 13;" onSubmit="return checkForm()"><input type="hidden" name="methode" value="teleporte" />';
    echo '<table width="95%" align="center">';

    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $style_tr = "display: block;";
    $param_id = 0;
    $row = 0;
    $row_id = "row-$param_id-$row-";
    $aqelem_misc_nom = "";
    echo '<tr id="' . $row_id . '" style="' . $style_tr . '">';
    echo '<td><input type="button" class="test" value="Retirer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
    echo '<td>Perso :
                    <input data-entry="val" id="' . $row_id . 'aqelem_cod" name="aqelem_cod[' . $param_id . '][]" type="hidden" value="">
                    <input name="aqelem_type[' . $param_id . '][]" type="hidden" value="">
                    <input data-entry="val" name="aqelem_misc_cod[' . $param_id . '][]" id="' . $row_id . 'aqelem_misc_cod" type="text" size="5" value="" onChange="setNomAndPosPerso(\'' . $row_id . 'aqelem_misc_nom\', $(\'#' . $row_id . 'aqelem_misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'aqelem_misc_nom">' . $aqelem_misc_nom . '</span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'aqelem_misc","perso","Rechercher un perso", "' . $s_option . '");\'>
                    &nbsp;<input type="button" class="" value="sa coterie" onClick=\'listCoterie($("#' . $row_id . 'aqelem_misc_cod").val());\'>
                    &nbsp;<input type="button" class="" value="sa position" onClick=\'listSurZone($("#' . $row_id . 'aqelem_misc_cod").val());\'>
                    &nbsp;<input type="button" class="" value="controleur" onClick=\'listControleur($("#' . $row_id . 'aqelem_misc_cod").val());\'>
                    </td>';
    echo '<tr id="add-' . $row_id . '" style="' . $style_tr . '"><td> <input id="add-button" type="button" class="test" value="ajouter" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(),0);"> </td></tr>';
    echo '</table>';
    echo 'Téléportation à destination de :<br><br>
                                    X = <input name="pos_x" id="pos_x" type="text" size="5" value="" onChange="setPositionDesc()">&nbsp;
                                    Y = <input name="pos_y" id="pos_y" type="text" size="5" value="" onChange="setPositionDesc()">&nbsp;
                                    Etage&nbsp;:' . create_selectbox_from_req("pos_etage", $request_select_etage, 0, array('id' => "pos_etage", 'style' => 'style="width: 350px;" onChange="setPositionDesc()"')) . '                                   
                                    &nbsp;<em><span id="position_desc"></span></em>
                                    <br><br>';
    echo '<input class="test" type="submit" value="Téléporter les persos" />';
    echo '</form>';

    echo '<hr>Section de recherche de persos liés:';
    echo '&nbsp;&nbsp;<input type="button" class="" value="chercher mes persos" onClick="listControleur(' . $perso_cod . ');"><br>';
    echo create_selectbox_from_req("perso_etage", $request_select_etage, 0, array('id' => "perso_etage", 'style' => 'style="margin:5px; width: 350px;'));
    echo '&nbsp;&nbsp;<input type="button" class="" value="tous les persos de l\'étage" onClick="listPersoEtage($(\'#perso_etage\').val());"><br>';
    echo '<div id="liste-ajout-rapide"></div>';
    echo '<hr>';

    echo "<script> listControleur({$perso_cod}); </script>"; // Injection javascript => perso par défaut.

}
?>
    <p style="text-align:center;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";