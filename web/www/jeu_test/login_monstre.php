<?php
include "blocks/_header_page_jeu.php";
ob_start();

$is_log = 1;
$req = "select compt_nom, dcompt_etage,dcompt_monstre_carte,dcompt_modif_perso from compte, compt_droit where compt_cod = dcompt_compt_cod and dcompt_compt_cod = $compt_cod ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0) {
    die("Erreur sur les étages possibles !");
} else {
    $result = $stmt->fetch();
    $droit['etage'] = $result['dcompt_etage'];
    $monstre_carte = $result['dcompt_monstre_carte'];
    $modif_perso = $result['dcompt_modif_perso'];
    $compt_nom = $result['compt_nom'];
}
if ($droit['etage'] == 'A') {
    $restrict = '';
    $restrict2 = '';
} else {
    $restrict = 'where etage_numero in (' . $droit['etage'] . ') ';
    $restrict2 = 'and pos_etage in (' . $droit['etage'] . ') ';
}
$req = "select etage_libelle,etage_numero,etage_reference from etage " . $restrict . "order by etage_reference desc, etage_numero asc";
$stmt = $pdo->query($req);
?>
    <div>
        <form name="edit" method="post" action="#">
            Selectionnez un étage pour consulter les monstres
            <select name="etage">
                <?php while ($result = $stmt->fetch()) {
                    $reference = ($result['etage_numero'] == $result['etage_reference']);
                    $etage_num = $result['etage_numero'];
                    $selected = ($etage == $etage_num) ? "selected='selected'" : '';
                    echo "<OPTION value='$etage_num' $selected>" . ($reference ? '' : ' |-- ') . $result['etage_libelle'] . "</OPTION>\n";
                }
                ?>
            </select>
            <input type="submit" value="Sélectionner cet étage">
        </form>
    </div>

    <p class="titre">Monstres et PNJ de l’étage</p>

<?php


$req_monstre = "select dlt_passee(perso_cod) as dlt_passee, etat_perso(perso_cod) as etat, perso_cod, perso_nom, ";
$req_monstre = $req_monstre . "perso_pa, perso_pv, perso_pv_max, to_char(perso_dlt,'DD/MM/YYYY HH24:mi:ss') as dlt, ";
$req_monstre = $req_monstre . "pos_x, pos_y, pos_etage, ";
$req_monstre = $req_monstre . "(select count(dmsg_cod) from messages_dest where dmsg_perso_cod = perso_cod and dmsg_lu = 'N') as messages, ";
$req_monstre = $req_monstre . "perso_dirige_admin, perso_pnj, coalesce(compt_nom, '') as compt_nom ";
$req_monstre = $req_monstre . "from perso ";
$req_monstre = $req_monstre . "inner join perso_position on ppos_perso_cod = perso_cod ";
$req_monstre = $req_monstre . "inner join positions on pos_cod = ppos_pos_cod ";
$req_monstre = $req_monstre . "left outer join perso_compte on pcompt_perso_cod = perso_cod ";
$req_monstre = $req_monstre . "left outer join compte on compt_cod = pcompt_compt_cod ";
$req_monstre = $req_monstre . "where (perso_type_perso = 2 or perso_pnj = 1) and perso_actif = 'O' ";
$req_monstre = $req_monstre . "and pos_etage = $etage ";
$req_monstre = $req_monstre . "order by pos_x,pos_y,perso_nom ";
$stmt = $pdo->query($req_monstre);
$nb_monstre = $stmt->rowCount();
if ($nb_monstre == 0) {
    echo("<p>pas de monstre</p>");
} else {
    echo("<table>");
    while ($result = $stmt->fetch()) {
        if ($result['perso_dirige_admin'] == 'O') {
            $ia = "<strong>Hors IA</strong>";
        } else if ($result['perso_pnj'] == 1) {
            $ia = "<strong>PNJ</strong>";
        } else {
            $ia = "IA";
        }
        echo("<tr>");
        echo "<td class=\"soustitre2\"><p><a href=\"../validation_login_monstre.php?numero=" . $result['perso_cod'] . "&compt_cod=" . $compt_cod . "\">" . $result['perso_nom'] . "</a></td>";
        echo "<td class=\"soustitre2\"><p>" . $ia . "</td>";
        echo "<td class=\"soustitre2\"><p>", $result['perso_pa'], "</td>";
        echo "<td class=\"soustitre2\"><p>", $result['perso_pv'], " PV sur ", $result['perso_pv_max'];
        if ($result['etat'] != "indemne") {
            echo " - (<strong>", $result['etat'], "</strong>)";
        }
        echo "</td>";
        echo "<td class=\"soustitre2\"><p>";
        if ($result['messages'] != 0) {
            echo "<strong>";
        }
        echo $result['messages'] . " msg non lus.";
        if ($result['messages'] != 0) {
            echo "</strong>";
        }
        echo "</td>";
        echo "<td class=\"soustitre2\"><p>";
        if ($result['dlt_passee'] == 1) {
            echo("<strong>");
        }
        echo $result['dlt'];
        if ($result['dlt_passee'] == 1) {
            echo("</strong>");
        }
        echo "</td>";
        echo "<td class=\"soustitre2\"><p>X=", $result['pos_x'], ", Y=", $result['pos_y'], ", E=", $result['pos_etage'], "</td>";
        if ($result['compt_nom'] != '') {
            echo "<td class=\"soustitre2\">Joué par <strong>", $result['compt_nom'], "</strong></td>";
        } else
            echo "<td></td>";
        echo("</tr>");
    }

    echo("</table>");

}

if ($etage == -100) {
    ?>    Suppression de monstres<br/>
    <em>Entrez les numéros séparés par des ";"</em><br/>
    <form name="delete" method="post" action="../supprime_monstre.php">
        <input type="text" name="monstres"><br/>
        <input type="submit" value="Supprimer"><br/>
    </form>
<?php }


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

