<?php

include "blocks/_header_page_jeu.php";
include_once '../includes/tools.php';

?>

    <script>//# sourceURL=admin_objet_bm.js

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

        function editObjetBM(row, objbm_cod) {
            //executer le service asynchrone
            $('tr[id^="bmlist-"]').removeClass("soustitre2");
            $('#bouton-supprimer').hide();

            if (row >= 0) {
                $('#bmlist-' + row).addClass("soustitre2");
                $('#bouton-supprimer').show();

                runAsync({request: "get_table_info", data: {info: "objets_bm", objbm_cod: objbm_cod}}, function (d) {
                    if (d.resultat == 0) {
                        var data = d.data;
                        $("#objbm_cod").val(data.objbm_cod ? data.objbm_cod : 0);
                        $("#sort-0-misc_cod").val(data.objbm_tbonus_cod ? data.objbm_tbonus_cod : "");
                        if ($("#sort-0-misc_cod").val() > 0) {
                            setNomByBMCod('sort-0-misc_nom', 'bonus_type', $("#sort-0-misc_cod").val());
                        }
                        else {
                            $("#sort-0-misc_nom").val("");
                            $("#sort-0-libc").val("");
                        }
                        $("#objbm_nom").val(data.objbm_nom ? data.objbm_nom : "");
                        $("#objbm_bonus_valeur").val(data.objbm_bonus_valeur ? data.objbm_bonus_valeur : "");
                        $("#objbm_equip_requis").val((!data.objbm_equip_requis || data.objbm_equip_requis =='false') ? 'N' : 'O');
                    }
                });
            } else {
                $("#objbm_cod").val(0);
                $("#sort-0-misc_cod").val("");
                $("#sort-0-libc").val("");
                $("#sort-0-misc_nom").text("");
                $("#objbm_nom").val("");
                $("#objbm_bonus_valeur").val("");
                $("#objbm_equip_requis").val('O');
            }
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
            $log =
                date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) ajout/modification de sort sur objets:\n";

            $objbm     = new objets_bm();
            $objbm_cod = (1 * (int)$_REQUEST["objbm_cod"]);

            if ($objbm_cod > 0)
            {
                $objbm->charge($objbm_cod);
                $new = false;
            } else
            {
                $new = true;
            }

            // Cas d'une suppression
            if (($_REQUEST["supprimer"] == "supprimer") && ($objbm_cod > 0))
            {
                // On retire les bonus/malus avant de supprimer pour
                $nb_obj = 0;
                $perobj = new perso_objets();
                $list   = $perobj->getByObjetGenerique($objbm->objbm_gobj_cod);
                if (count($list) != 0)
                {
                    $pdo = new bddpdo;

                    foreach ($list as $pobj)
                    {
                        if ($pobj->perobj_equipe == 'O' || !$objbm->objbm_equip_requis )
                        {
                            // Mise à jour des BM d'équipement pour l'objet de ce joueur!
                            $req    = "select retire_bonus_equipement(:perso_cod,:obj_cod,:objbm_cod)  as modif;";
                            $stmt   = $pdo->prepare($req);
                            $stmt   = $pdo->execute(array(
                                ":perso_cod" => $pobj->perobj_perso_cod,
                                ":obj_cod"   => $pobj->perobj_obj_cod,
                                ":objbm_cod" => $objbm->objbm_cod), $stmt);
                            $result = $stmt->fetch();

                            $nb_obj++;
                        }
                    }
                }

                $log .= "supression de l'objet_bm #" . $objbm->objbm_cod . "\n" . obj_diff(new objets_bm, $objbm);
                $objbm->delete($objbm_cod);
                if ($nb_obj > 0) echo "<div class='bordiv'><pre><strong><u>ATTENTION</u></strong>: Les {$nb_obj} objet(s) équipé(s) par les joueurs ont été impactés par cette supression!</pre></div>";

            } else
            {
                // Cas d'une creation/modification
                $clone_os = clone $objbm;
                $objbm->objbm_gobj_cod     = 1 * (int)$_REQUEST["objbm_gobj_cod"];
                $objbm->objbm_obj_cod      = null;
                $objbm->objbm_tbonus_cod   = 1 * (int)$_REQUEST["objbm_tbonus_cod"];
                $objbm->objbm_nom          = $_REQUEST["objbm_nom"] == '' ? null : $_REQUEST["objbm_nom"];
                $objbm->objbm_bonus_valeur = (int)$_REQUEST["objbm_bonus_valeur"];
                $objbm->objbm_equip_requis = $_REQUEST["objbm_equip_requis"]=="O" ? 1 : 0 ;

                $objbm->stocke($new);

                // Vérification de l'ipact sur les objets en jeu!
                if (($clone_os->objbm_tbonus_cod != $objbm->objbm_tbonus_cod) || ($clone_os->objbm_bonus_valeur != $objbm->objbm_bonus_valeur) || ($clone_os->objbm_equip_requis != $objbm->objbm_equip_requis))
                {

                    $bonus = new bonus_type();
                    $bonus->charge($objbm->objbm_tbonus_cod);

                    $nb_obj = 0;
                    $perobj = new perso_objets();
                    $list   = $perobj->getByObjetGenerique($objbm->objbm_gobj_cod);
                    if (count($list) != 0)
                    {
                        $pdo = new bddpdo;

                        foreach ($list as $pobj)
                        {

                            // modification complex du BM, le supprime pour eventuellement le recréer
                            $req    = "select retire_bonus_equipement(:perso_cod,:obj_cod,:objbm_cod)  as modif;";
                            $stmt   = $pdo->prepare($req);
                            $stmt   = $pdo->execute(array(
                                ":perso_cod" => $pobj->perobj_perso_cod,
                                ":obj_cod"   => $pobj->perobj_obj_cod,
                                ":objbm_cod" => $objbm->objbm_cod), $stmt);
                            $result = $stmt->fetch();
                            $nb_obj++;

                            // redonner le bonus si les conditions sont toujours là ou elles le sont devenues
                            if ( $pobj->perobj_equipe == 'O' || ! $objbm->objbm_equip_requis ) {
                                $req    = "select ajoute_bonus_equipement(:perso_cod,:tbonus_libc,:objbm_cod, :obj_cod, :valeur)  as modif;";
                                $stmt   = $pdo->prepare($req);
                                $stmt   = $pdo->execute(array(
                                    ":perso_cod" => $pobj->perobj_perso_cod,
                                    ":tbonus_libc"  => $bonus->tbonus_libc,
                                    ":objbm_cod" => $objbm->objbm_cod,
                                    ":obj_cod"   => $pobj->perobj_obj_cod,
                                    ":valeur"   => $objbm->objbm_bonus_valeur), $stmt);

                                $result = $stmt->fetch();
                            }

                        }
                    }
                    if ($nb_obj > 0) echo "<div class='bordiv'><pre><strong><u>ATTENTION</u></strong>: Les {$nb_obj} objet(s) équipé(s) par les joueurs ont été impactés par cette modification!</pre></div>";

                    /*$stmt2 = $pdo->prepare("select f_modif_carac_perso(:bonus_perso_cod, :tbonus_libc); ");
                    while ($result = $stmt->fetch())
                    {
                        $bonus_perso_cod = $result['corig_perso_cod'];
                        $perso_list      .= '#' . $result['perso_cod'] . ' (' . $result['perso_nom'] . '), ';

                        // On recalcule le changement des limites pour ce perso
                        $pdo->execute(array(":bonus_perso_cod" => $bonus_perso_cod, ":tbonus_libc"     => $tbonus_libc), $stmt2);
                    }*/

                }

                // Logger les infos pour suivi admin
                $log .= "ajoute/modifie de l'objet_bm #" . $objbm->objbm_cod . "\n" . obj_diff($clone_os, $objbm);
            }

            writelog($log, 'objet_edit', true);
            //echo "<div class='bordiv'><pre>$log</pre></div>";
        }
    }
    //print_r($_REQUEST);

    echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: #800000;\'>SELECTION D\'OBJET</strong>&nbsp;&nbsp;</div>';

    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $row_id = "obj-generique-";
    echo '<form name="selection-objet" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
    echo '<br><strong>Sélection d’un objet générique</strong><br>Code de l\'objet générique :
                    <input data-entry="val" name="objbm_gobj_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\'' . $row_id . 'misc_nom\', \'objet_generique\', $(\'#' . $row_id . 'misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","objet_generique","Rechercher un objet générique");\'>
                    &nbsp;<input type="submit" value="Voir/Modifier les bonus/malus de cet objet" class="test"></form>';


    echo "<hr>";

    $objbm_gobj_cod = 1 * (int)$_REQUEST["objbm_gobj_cod"];
    if ($objbm_gobj_cod > 0)
    {
        $gobj = new objet_generique();
        $gobj->charge($objbm_gobj_cod);
        echo "Détail des sorts sur l'objet générique: <strong>#{$gobj->gobj_cod} - {$gobj->gobj_nom}</strong><br>";
        $exemplaires = $gobj->getNombreExemplaires();
        echo "Nombre d'exemplaire basé sur cet objet générique:<br>";
        echo "&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>" . $exemplaires->total . "</strong><br>";
        echo "&nbsp;&nbsp;&nbsp;Inventaire : <strong>" . $exemplaires->inventaire . "</strong> <em style='font-size: x-small'>(possédés par les joueurs, monstres ou PNJ)</em><br>";
        echo "<br>";

        echo "<strong>Ajouter/Modifier un sort sur l'objet</strong> :";
        $row_id = "sort-0-";
        echo '<form name="mod-objet-sort" action="' . $_SERVER['PHP_SELF'] . '" method="post">
             <input type="hidden" name="methode" value="sauve">
             <input type="hidden" id="objbm_cod" name="objbm_cod" value="0">
             <input type="hidden" id="objbm_gobj_cod" name="objbm_gobj_cod" value="' . $objbm_gobj_cod . '">
             <input type="hidden" id="objbm_obj_cod" name="objbm_obj_cod" value="">
             ';
        echo '<table width="100%" class=\'bordiv\'><tr><td>Sélection du type de bonus/malus CODE (<em> ou tbonus_cod</em>) :</td><td>
                <input data-entry="val" name="objbm_tbonus_libc" id="' . $row_id . 'libc" type="text" size="5" value="" onChange="setNomByBMLibc(\'' . $row_id . 'misc_nom\', \'bonus_type2\', $(\'#' . $row_id . 'libc\').val().toUpperCase());">
                &nbsp;OU&nbsp;<input data-entry="val" name="objbm_tbonus_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByBMCod(\'' . $row_id . 'misc_nom\', \'bonus_type\', $(\'#' . $row_id . 'misc_cod\').val());">
                &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","bonus_type","Rechercher un bonus/malus");\'><br>
                </td></tr>
                <tr><td>Valeur du bonus/malus :</td><td><input type="text" id="objbm_bonus_valeur" name="objbm_bonus_valeur" size="50">&nbsp;<em></em></td></tr>
                <tr><td>Equip. requis :</td><td>'.create_selectbox("objbm_equip_requis", array("O"=>"Oui","N"=>"Non"), 'O', array("id"=>"objbm_equip_requis")).'&nbsp;<em> l\'objet doit-t-il être équipé pour subir le bonus/malus?</em></td></tr>
                <tr style="display:none;"><td>Nom du bonus/malus :</td><td><input type="text" id="objbm_nom" name="objbm_nom" size="50">&nbsp;<em></em></td></tr>
                <tr><td></td><td><input type="submit" name="valider" value="valider" class="test">&nbsp;&nbsp;<input style="display:none" id="bouton-supprimer" type="submit" name="supprimer" value="supprimer" class="test"></td></tr>
                 </table>
                </form>';

        echo "<strong><br>Liste des bonus/malus sur l'objet</strong> :<br>";
        $objbm = new objets_bm();
        $lbm   = $objbm->getBy_objbm_gobj_cod($gobj->gobj_cod);
        if ($lbm)
        {
            echo '<table width="100%" class=\'bordiv\'>';
            echo "<tr><td><input type='button' class='test' value='nouveau' onclick='editObjetBM(-1,0);'></td>
                      <td><strong>objbm_cod</strong></td>
                      <td><strong>Bonus/malus</strong></td>
                      <td><strong>Valeur</strong></td>
                      <td><strong>Type</strong></td>
                      <td><strong>Equip.</strong></td>
                      <td style=\"display:none;\"><strong>Nom sur l'objet</strong></td>
                    </tr>";
            foreach ($lbm as $k => $os)
            {
                $bm = new bonus_type();
                $bm->charge($os->objbm_tbonus_cod);

                echo "<tr id='bmlist-{$k}'><td><input type='button' class='test' value='modifier' onclick='editObjetBM({$k}, {$os->objbm_cod});'></td>
                      <td>{$os->objbm_cod}</td>
                      <td>{$bm->tonbus_libelle} / #{$os->objbm_tbonus_cod} ({$bm->tbonus_libc})</td>
                      <td>{$os->objbm_bonus_valeur}</td>
                      <td>" . (($bm->tbonus_gentil_positif == 't') ? ($os->objbm_bonus_valeur > 0 ? "BONUS" : "MALUS") : ($os->objbm_bonus_valeur > 0 ? "MALUS" : "BONUS")) . "</td>
                      <td>".( $os->objbm_equip_requis ? "O" : "N" )."</td></tr>
                      <td style=\"display:none;\">" . $os->getNom() . "</td>
                     </tr>";
            }
            echo "</table>";
        } else
        {
            echo "<em>Il n'y a pas de bonus/malus sur cet objet</em><br>";
        }
    }
    ?>
    <br> <strong><u>Remarques</u></strong>:<br>
    * Pensez à ne pas déséquilibrer le jeu (avec des objets trop puissants)<br>
    * N'oubliez pas que TOUS les exemplaires d'un objet générique possèdederont immédiatement ces bonus/malus<br>
    <br><p style="text-align:center;"><a
            href="admin_objet_generique_edit.php?&gobj_cod=<?php echo $_REQUEST["objbm_gobj_cod"]; ?>">Retour au
        modification d'objets génériques</a>
    <?php

}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";