<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (!isset($evt_start)) {
    $evt_start = 0;
}
if ($evt_start < 0) {
    $evt_start = 0;
}
$req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
$compt_an = $db->f("pcompt_compt_cod");
$req_evt = "select levt_cod,to_char(levt_date,'DD/MM/YYYY hh24:mi:ss') as evt_date,tevt_libelle,levt_texte,levt_perso_cod1,levt_attaquant,levt_cible ";
$req_evt = $req_evt . "from ligne_evt,type_evt ";
$req_evt = $req_evt . "where levt_perso_cod1 in (select pcompt_perso_cod from perso_compte where pcompt_compt_cod = $compt_an) ";
$req_evt = $req_evt . "and levt_tevt_cod = tevt_cod ";
$req_evt = $req_evt . "and levt_tevt_cod in (18,10,11,63) ";
$req_evt = $req_evt . "order by levt_cod desc ";
$req_evt = $req_evt . "limit 20 ";
$req_evt = $req_evt . "offset $evt_start ";
$db->query($req_evt);
?>
    <table cellspacing="2">
        <tr>
            <td colspan="3" class="titre"><p class="titre">Evènements</p></td>
        </tr>


        <form name="visu_evt" method="post" action="evts_px_compte.php">
            <input type="hidden" name="visu">
            <?php
            while ($db->next_record()) {
                echo("<tr>");
                printf("<td class=\"soustitre3\"><p>%s</p></td>", $db->f("evt_date"));
                printf("<td class=\"soustitre3\"><p><strong>%s</strong></p></td>", $db->f("tevt_libelle"));
                $req_nom_evt = "select perso1.perso_nom as nom1 ";
                if ($db->f("levt_attaquant") != '') {
                    $req_nom_evt = $req_nom_evt . ",attaquant.perso_nom as nom2";
                }
                if ($db->f("levt_cible") != '') {
                    $req_nom_evt = $req_nom_evt . ",cible.perso_nom as nom3 ";
                }
                $req_nom_evt = $req_nom_evt . " from perso perso1";
                if ($db->f("levt_attaquant") != '') {
                    $req_nom_evt = $req_nom_evt . ",perso attaquant";
                }
                if ($db->f("levt_cible") != '') {
                    $req_nom_evt = $req_nom_evt . ",perso cible";
                }
                $req_nom_evt = $req_nom_evt . " where perso1.perso_cod = " . $db->f("levt_perso_cod1") . " ";
                if ($db->f("levt_attaquant") != '') {
                    $req_nom_evt = $req_nom_evt . " and attaquant.perso_cod = " . $db->f("levt_attaquant") . " ";
                }
                if ($db->f("levt_cible") != '') {
                    $req_nom_evt = $req_nom_evt . " and cible.perso_cod = " . $db->f("levt_cible") . " ";
                }
                $db_detail->query($req_nom_evt);
                $db_detail->next_record();
                $res_nom_evt = pg_exec($dbconnect, $req_nom_evt);
                $tab_nom_evt = pg_fetch_array($res_nom_evt, 0);
                $texte_evt = str_replace('[perso_cod1]', "<strong><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_perso_cod1") . ";document.visu_evt.submit();\">" . $db_detail->f("nom1") . "</a></strong>", $db->f("levt_texte"));
                if ($db->f("levt_attaquant") != '') {
                    $texte_evt = str_replace('[attaquant]', "<strong><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_attaquant") . ";document.visu_evt.submit();\">" . $db_detail->f("nom2") . "</A></strong>", $texte_evt);
                }
                if ($db->f("levt_cible") != '') {
                    $texte_evt = str_replace('[cible]', "<strong><a href=\"javascript:document.visu_evt.visu.value=" . $db->f("levt_cible") . ";document.visu_evt.submit();\">" . $db_detail->f("nom3") . "</a></strong>", $texte_evt);
                }

                echo("<td><p>$texte_evt</p></td>");
                echo("</tr>");
            }
            ?>
            <tr>
                <td>
        </form>
        <form name="evt" method="post" action="evts_px_compte.php">
            <input type="hidden" name="evt_start">
            <?php
            if ($evt_start != 0) {
                echo("<div align=\"left\"><a href=\"javascript:document.evt.evt_start.value=$evt_start-20;document.evt.submit();\"><== Précédent</a></div>");
            }
            ?></td>
            <td></td>
            <?php
            echo("<td><div align=\"right\"><a href=\"javascript:document.evt.evt_start.value=$evt_start+20;document.evt.submit();\">Suivant ==></a></div></td>");
            ?>
            </tr>
        </form>
    </table>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
