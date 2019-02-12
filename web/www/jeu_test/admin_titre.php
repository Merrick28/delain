<?php

include "blocks/_header_page_jeu.php";

include_once '../includes/tools.php';


?>

    <script>//# sourceURL=admin_titre.js

        function setNomAndPosPerso(divname, cod) {
            //executer le service asynchrone
            $("#" + divname).text("");
            runAsync({request: "get_table_info", data: {info: "perso_pos", perso_cod: cod}}, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.perso_nom)) {
                    if (s_option == "monstre" && d.data.perso_type_perso != 2)
                        $("#" + divname).html('Vous n\'avez pas les droits pour titrer ce perso.');
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
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
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
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
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
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
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

        function listPersoPX(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_gain_px", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
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
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce monstre...");
            });
        }

        function listPersoTitre(titre) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_titre", titre: titre}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
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
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce titre...");
            });
        }

        function listPersoListe(liste) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_liste", liste: liste}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
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
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour cette liste...");
            });
        }

        function listPersoEtage(etage_numero) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_etage_pos", etage_numero: etage_numero}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso) && (nb_perso<100))     // si pas déjà dans la list et autorisé
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

        function isInTitrageList(cod) {
            var isInList = false;
            $('tr[id^="row-0-"]').each(function () {
                if ($('#' + this.id + 'aqelem_misc_cod').val() == cod) isInList = true;
            });
            return isInList;
        }

        function isAuthorized(type_perso) { // l'admin monstre ne peut titrer que les monstres et on peut titrer les familiers
            if (s_option == "monstre" && type_perso != 2) return false; else return true;
        }

        function countTitrageList() {
            var count = 0;
            $('tr[id^="row-0-"]').each(function () {
                if ($('#' + this.id + 'aqelem_misc_cod').val() > 0) count++;
            });
            return count;
        }

        function checkForm() {
            if ($("#titre").val() == "")  {
                alert("Vous devez saisir un titre à attribuer ou supprimer aux persos!");
                return false;
            }
            var nb_perso = countTitrageList();
            if (nb_perso == 0) {
                alert("Vous devez sélectionner des persos à titrer!");
                return false;
            }
            return true; //confirm("Etes-vous sûr de vouloir titrer "+nb_perso+" perso(s) avec le titre: X="+$("#titre").val()+"?");
        }

    </script>

<?php

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
    <title>ATTRIBUTION DE TITRES</title>
<?php

$droit_modif = 'dcompt_modif_perso';
include "blocks/_test_droit_modif_generique.php";


if ($erreur == 0)
{
    $pdo = new bddpdo;

    $s_option = ($droit['modif_perso'] == 'N') ? "monstre" : "";                               // droit de tp de monstre uniquement
    echo "<script> var s_option='{$s_option}'; </script>"; // Injection javascript

    //=======================================================================================
    // == Main
    //=======================================================================================
    //-- traitement des actions
    if (isset($_REQUEST['methode']))
    {
        // Traitement des actions de téléportation
        if (($_REQUEST['methode'] == "titrage") && ($_REQUEST['action'] == "add"))
        {
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; die();
            $titre = $_REQUEST['titre'];
            $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) Ajout de titre en masse : $titre\n";

            // Récupération du code traitement_perso_admin (dev mode flash : on réfléchi pas on reprend ce qui marche ailleurs) !!!
            foreach ($_REQUEST["aqelem_misc_cod"][0] as $k => $mod_perso_cod)
            {
                if ($mod_perso_cod > 0)
                {
                    // Caracs du perso concerné
                    $req = "select perso_nom, perso_type_perso from perso where perso_cod = :perso_cod ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":perso_cod"=>$mod_perso_cod),$stmt);

                    if($result = $stmt->fetch())
                    {
                        $mod_perso_nom = $result['perso_nom'];
                        $mod_perso_type_perso = $result['perso_type_perso'];

                        if ($mod_perso_type_perso != 2 && $s_option == "monstre")
                        {
                            // Un admin monstre qui force le passage pour ajouter un perso!!!
                            $log .= "vous n'avez pas les droits suffisants pour ce perso!";
                        } else
                        {
                            // empecher le double titrage (le même jour)

                            $req = "select count(*) as count from perso_titre where ptitre_perso_cod=:ptitre_perso_cod and ptitre_titre ilike :ptitre_titre and ptitre_date::date=now()::date ";
                            $stmt = $pdo->prepare($req);
                            $pdo->execute(array(":ptitre_perso_cod"=>$mod_perso_cod, ":ptitre_titre" => $titre),$stmt);
                            $result = $stmt->fetch();

                            // titrage
                            if ((int)$result["count"]==0)
                            {
                                $req = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date) values (:ptitre_perso_cod,:ptitre_titre,now()) ";
                                $stmt = $pdo->prepare($req);
                                $pdo->execute(array(":ptitre_perso_cod"=>$mod_perso_cod, ":ptitre_titre" => $titre),$stmt);
                                $log .= "Titrage du perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                            }
                            else
                            {
                                $log .= "Perso {$mod_perso_nom} ({$mod_perso_cod}) : Non titré, il a déjà reçu le titre aujourd'hui!\n";
                            }
                        }
                    }
                    else
                    {
                        $log .= "Perso #{$mod_perso_cod} : Non trouvé!\n";
                    }
                }
            }
            writelog($log, 'perso_edit');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        }
        else if (($_REQUEST['methode'] == "titrage") && ($_REQUEST['action'] == "del"))
        {
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; die();
            $titre = $_REQUEST['titre'];
            $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) Suppression de titre en masse : $titre\n";

            // Récupération du code traitement_perso_admin (dev mode flash : on réfléchi pas on reprend ce qui marche ailleurs) !!!
            foreach ($_REQUEST["aqelem_misc_cod"][0] as $k => $mod_perso_cod)
            {
                if ($mod_perso_cod > 0)
                {
                    // Caracs du perso concerné
                    $req = "select perso_nom, perso_type_perso from perso where perso_cod = :perso_cod ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":perso_cod"=>$mod_perso_cod),$stmt);

                    if($result = $stmt->fetch())
                    {
                        $mod_perso_nom = $result['perso_nom'];
                        $mod_perso_type_perso = $result['perso_type_perso'];

                        if ($mod_perso_type_perso != 2 && $s_option == "monstre")
                        {
                            // Un admin monstre qui force le passage pour ajouter un perso!!!
                            $log .= "vous n'avez pas les droits suffisants pour ce perso!";
                        } else
                        {
                            // dé-titrage
                            $req = "delete from perso_titre where ptitre_perso_cod=:ptitre_perso_cod and ptitre_titre ilike :ptitre_titre ";
                            $stmt = $pdo->prepare($req);
                            $stmt = $pdo->execute(array(":ptitre_perso_cod"=>$mod_perso_cod, ":ptitre_titre" => $titre),$stmt);

                            if( $stmt->rowCount()>0)
                            {
                                $log .= "Suppression du titre sur le perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                            }
                            else
                            {
                                $log .= "Perso {$mod_perso_nom} ({$mod_perso_cod}) : Titre non trouvé!\n";
                            }
                        }
                    }
                    else
                    {
                        $log .= "Perso #{$mod_perso_cod} : Non trouvé!\n";
                    }
                }
            }
            writelog($log, 'perso_edit');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        }
    }
    //print_r($_REQUEST);

    echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: #800000;\'>GESTION DE TITRE EN MASSE</strong>&nbsp;&nbsp;</div>';

    //=======================================================================================
    // == Constantes quete_auto
    //=======================================================================================
    //$request_select_etage_ref = "SELECT null etage_cod, 'Aucune restriction' etage_libelle, null etage_numero UNION SELECT etage_cod, etage_libelle, etage_numero from etage where etage_reference = etage_numero order by etage_numero desc" ;
    $request_select_etage = "SELECT etage_numero, case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle from etage order by etage_reference desc, etage_numero";

    echo 'Liste des persos :<br><br>';

    echo '<form  method="post" onkeypress="return event.keyCode != 13;" onSubmit="return checkForm()"><input type="hidden" name="methode" value="titrage" />';
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
    echo 'Titre à attribuer ou à supprimer :<br><br>
                                    <input name="titre" id="titre" type="text" size="80" value="">&nbsp;
                                    <input name="action" id="action" type="hidden" value="add">&nbsp;
                                    <!--Date : <input name="date" id="date" type="text" size="10" value="" >&nbsp<em style="font-size: x-small">Au format jj/mm/aaaa (date du jour si vide)</em>-->
                                   <br><br>';
    echo '<input class="test" type="submit" value="Attribuer le titre aux persos" />';
    echo '&nbsp;&nbsp;<input class="test" type="submit" value="Supprimer le titre aux persos" onclick="$(\'#action\').val(\'del\')"/>';
    echo '</form>';

    echo '<hr><strong>Section de recherche de persos</strong>:';
    echo '&nbsp;&nbsp;<input type="button" class="" value="chercher mes persos" onClick="listControleur(' . $perso_cod . ');"><br>';
    echo 'N° de Monstre <input id="perso_px" style="margin-top: 5px;" type=""text" size="5">&nbsp;:&nbsp;chercher les persos qui ont participé à sa mort&nbsp;<input type="button" class="" value="chercher par gain de px" onClick="listPersoPX($(\'#perso_px\').val());"><br>';
    echo 'Titre <input id="perso_titre" style="margin-top: 5px;" type=""text" size="50">&nbsp;:&nbsp;<input type="button" class="" value="chercher par titre" onClick="listPersoTitre($(\'#perso_titre\').val());"><br>';
    echo 'Liste <input id="perso_liste" style="margin-top: 5px;" type=""text" size="50">&nbsp;:&nbsp;<input type="button" class="" value="chercher par liste de noms" onClick="listPersoListe($(\'#perso_liste\').val());"> <em style="font-size: x-small">(liste de nom séparés par des ; comme pour la messagerie)</em><br>';
    echo create_selectbox_from_req("perso_etage", $request_select_etage, 0, array('id' => "perso_etage", 'style' => 'style="margin:5px; width: 350px;')) ;
    echo '&nbsp;&nbsp;<input type="button" class="" value="tous les persos de l\'étage" onClick="listPersoEtage($(\'#perso_etage\').val());"><br>';
    echo '<div id="liste-ajout-rapide"></div>';
    echo '<hr>';

    echo "<script> listControleur({$perso_cod}); </script>"; // Injection javascript => perso par défaut.

}
?>
    <p style="text-align:center;"><a href="<?php echo $PHP_SELF ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";