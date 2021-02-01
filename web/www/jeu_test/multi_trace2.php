<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (!isset($evt_start)) {
    $evt_start = 0;
}
if ($evt_start < 0) {
    $evt_start = 0;
}
$req = "select multi_cpt1 as compte,compt_nom from multi_trace,compte ";
$req = $req . "where (multi_cpt1 = $v_compte or multi_cpt2 = $v_compte) ";
$req = $req . "and multi_cpt1 = compt_cod ";
$req = $req . "and compt_confiance = 'N' ";
$req = $req . "union ";
$req = $req . "select multi_cpt2 as compte,compt_nom from multi_trace,compte ";
$req = $req . "where (multi_cpt1 = $v_compte or multi_cpt2 = $v_compte) ";
$req = $req . "and multi_cpt2 = compt_cod ";
$req = $req . "and compt_confiance = 'N' ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() != 0) {
    echo "<p class=\"titre\">Comptes impliqués :</p>";
    while ($result = $stmt->fetch()) {
        echo "<a href=\"detail_compte.php?compte=" . $result['compte'] . "\">" . $result['compt_nom'] . "</A><br>";
    }
} else {
    echo "<p>Aucune donnée trouvée !";
}


$req_evt = "select c1.compt_nom as ancien_nom,c1.compt_cod as ancien_cod,c2.compt_nom as nouveau_nom,c2.compt_cod as nouveau_cod,to_char(multi_date,'DD/MM/YYYY hh24:mi:ss') as date, multi_ip ";
$req_evt = $req_evt . "from multi_trace,compte c1, compte c2 ";
$req_evt = $req_evt . "where multi_cpt1 = c1.compt_cod ";
$req_evt = $req_evt . "and multi_cpt2 = c2.compt_cod ";
$req_evt = $req_evt . "and (c1.compt_cod = $v_compte or c2.compt_cod = $v_compte) ";
$req_evt = $req_evt . "and c1.compt_confiance = 'N' ";
$req_evt = $req_evt . "and c2.compt_confiance = 'N' ";
$req_evt = $req_evt . "order by multi_cod desc ";
$req_evt = $req_evt . "limit 40 ";
$req_evt = $req_evt . "offset $evt_start ";
$stmt = $pdo->query($req_evt);
?>
    <table cellspacing="2">
        <tr>
            <td colspan="5" class="titre"><p class="titre">Multi</p></td>
        </tr>
        <?php
        echo "<tr>";
        echo "<td class=\"soustitre3\"><p><strong>Date</strong></p></td>";
        echo "<td class=\"soustitre3\"><p><strong>Ancien</strong></p></td>";
        echo "<td class=\"soustitre3\"><p><strong>Nouveau</strong></p></td>";
        echo "<td class=\"soustitre3\"><p><strong>IP</strong></p></td>";
        echo "<td class=\"soustitre3\"><p><strong>Hôte</strong></p></td>";
        echo "</tr>";
        ?>
        <form name="visu_evt" method="post" action="multi_trace.php">
            <input type="hidden" name="visu">
            <?php
            while ($result = $stmt->fetch()) {
                echo "<tr>";
                echo "<td class=\"soustitre3\"><p>" . $result['date'] . "</p></td>";
                echo "<td class=\"soustitre3\"><p><a href=\"detail_compte.php?compte=" . $result['ancien_cod'] . "\">" . $result['ancien_nom'] . "</A></p></td>";
                echo "<td class=\"soustitre3\"><p><a href=\"detail_compte.php?compte=" . $result['nouveau_cod'] . "\">" . $result['nouveau_nom'] . "</A></p></td>";
                $ip = $result['multi_ip'];
                echo "<td class=\"soustitre3\"><p>" . $ip . "</td>";
                echo "<td class=\"soustitre3\"><p>" . gethostbyaddr($ip) . "</p></td>";

                echo("</tr>");
            }
            ?>
            <tr>
                <td>
        </form>
        <form name="evt" method="post" action="multi_trace2.php">
            <input type="hidden" name="evt_start">
            <input type="hidden" name="v_compte" value="<?php echo $v_compte; ?>">
            <?php
            if ($evt_start != 0) {
                echo("<div align=\"left\"><a href=\"javascript:document.evt.evt_start.value=$evt_start-40;document.evt.submit();\"><== Précédent</a></div>");
            }
            ?></td>
            <td></td>
            <td></td>
            <td></td>
            <?php
            echo("<td><div align=\"right\"><a href=\"javascript:document.evt.evt_start.value=$evt_start+40;document.evt.submit();\">Suivant ==></a></div></td>");
            ?>
            </tr>
        </form>
    </table>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

