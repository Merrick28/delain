<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/template.inc';
if (!isset($included) || !$included)
{
    include "blocks/_header_page_jeu.php";
    ob_start();
}
?>
<link rel="stylesheet" type="text/css" href="../styles/onglets.css" title="essai">
<script type="text/javascript" src="../scripts/onglets.js"></script>
<script type="text/javascript" src="../scripts/pop-in.js"></script>
<div id='informations_case' class='bordiv' style='width:300px; padding:5px; display:none; position:absolute;'></div>
<?php 



$droit_modif = 'dcompt_modif_perso';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0) {

// POSITION DU JOUEUR
    $req_matos = "select pos_x,pos_y,pos_etage,pos_cod "
        . "from perso_position,positions "
        . "where ppos_perso_cod = $perso_cod"
        . "and ppos_pos_cod = pos_cod ";
    $stmt = $pdo->query($req_matos);
    $result = $stmt->fetch();
    $perso_pos_x = $result['pos_x'];
    $perso_pos_y = $result['pos_y'];
    $perso_pos_etage = $result['pos_etage'];
    $perso_pos_cod = $result['pos_cod'];
    ?>
    <p> Visualisation des fonctions d’arrivée (pièges, cachettes ...)</p>
    <hr>
    Choix de l’étage:
    <form method="post">
        Étage : <select name="pos_etage">
            <?php
            echo $html->etage_select($perso_pos_etage);
            ?>
        </select><br>
        <input type="submit" value="Valider">
    </form>
    <hr>
    <?php
    $sel_etage = $perso_pos_etage;
    if (isset($_POST['pos_etage'])) {
        $sel_etage = $_POST['pos_etage'];
    }
    ?>
    <br>En cliquant sur une case déjà remplie par une cachette, on peut modifier son contenu (cachette), ou si c’est un piège, les effets associés.
    <br>
    En cliquant sur une case vide, deux choix sont proposés pour rajouter piège ou cachette<br><br>
    <table>
        <tr>
            <td width="20" height="20">
                <div style="width:25px;height:25px;background:#FFCCFF"></div>
            </td>
            <td>Présence d’une cachette</td>
            <td width="20" height="20">
                <div style="width:25px;height:25px;background:#663399"></div>
            </td>
            <td>Présence d’un piège</td>
            <td width="20" height="20">
                <div style="width:25px;height:25px;background:#C0C0C0"></div>
            </td>
            <td>Cachette et mur creusable</td>
            <td width="20" height="20">
                <div style="width:25px;height:25px;background:#FFCC00"></div>
            </td>
            <td>Piège et mur creusable</td>
            <td width="20" height="20">
                <div style="width:25px;height:25px;background:#FFFF99"></div>
            </td>
            <td>Fontaine de jouvence</td>
        </tr>
    </table>
    <table border="1">
        <tr>
            <?php
            $req_murs = "select pos_cod, pos_x,pos_y,pos_etage,mur_creusable,mur_usure,mur_richesse,pos_fonction_arrivee from positions "
                . " LEFT JOIN murs ON positions.pos_cod = murs.mur_pos_cod"
                . " where"
//." pos_x > $perso_pos_x-10 and pos_x < $perso_pos_x+10"
//." and pos_y > $perso_pos_y-10 and pos_y < $perso_pos_y+10"
                . " pos_etage = $sel_etage"
                . " order by pos_y desc,pos_x asc";
            $stmt = $pdo->query($req_murs);
            while ($result = $stmt->fetch()){
            if (!isset($p_y)) {
                $p_y = $result['pos_y'];
            }
            if ($result['pos_y'] != $p_y){
            ?>
        </tr>
        <tr>
            <?php
            }
            $color = "#FFFFFF";
            $piege = '';
            $cachette = '';
            $presence_texte = '';
            $texte = '';
            $comment = '';
            $lien_px = "&pos_x=" . $result['pos_x'];
            $lien_py = "&pos_y=" . $result['pos_y'];
            $lien_pe = "&pos_e=" . $result['pos_etage'];
            $lien_piege = "<a href=admin_piege.php?methode=cre&mode=popup" . $lien_py . $lien_px . $lien_pe . " target=_blank>Créer un piège sur cette case</a>";
            $lien_cachette = "<a href=admin_cachette.php?methode=cre&mode=popup" . $lien_py . $lien_px . $lien_pe . " target=_blank>Créer une cachette sur cette case</a>";
            $lien_fontaine = "<a href=admin_fontaine.php?methode=cre&mode=popup" . $lien_py . $lien_px . $lien_pe . " target=_blank>Créer une fontaine de jouvence sur cette case</a>";
            $onclick = "onClick=\"changeInfo(document.getElementById('informations_case'), '$lien_piege<br>$lien_cachette<br>$lien_fontaine<br>')\"";

            if (substr($result['pos_fonction_arrivee'], 0, 17) == 'decouvre_cachette') {
                $color = '#FFCCFF';
                $cachette = 'O';
                $presence_texte = 'O';
                $req2 = 'select cache_cod from cachettes where cache_pos_cod = ' . $result['pos_cod'];
                $stmt2 = $pdo->query($req2);
                $result2 = $stmt2->fetch();
                $cache_cod = $result2['cache_cod'];
                $onclick = "onClick=\"window.open('admin_cachette.php?cache_cod=" . $cache_cod . "&methode=update_cache&mode=popup','','fullscreen,scrollbars');return(false)\"";
            }
            if (substr($result['pos_fonction_arrivee'], 0, 11) == 'piege_param') {
                $color = '#663399';
                $piege = 'O';
                $presence_texte = 'O';
                $onclick = "onClick=\"window.open('admin_piege.php?pos_cod=" . $result['pos_cod'] . "&methode=mod&mode=popup','','fullscreen,scrollbars');return(false)\"";
            }
            if (substr($result['pos_fonction_arrivee'], 0, 16) == 'deplace_fontaine') {
                $color = '#FFFF99';
                $fontaine = 'O';
                $presence_texte = 'O';
                $onclick = "onClick=\"window.open('admin_fontaine.php?pos_cod=" . $result['pos_cod'] . "&methode=mod&mode=popup','','fullscreen,scrollbars');return(false)\"";
            }
            if ($result['mur_creusable'] == 'O') {
                $color = "#00FF00";
                $presence_texte = 'O';
            }
            if ($result['mur_creusable'] == 'N') {
                $color = "#FF0000";
            }
            if ($result['mur_creusable'] == 'O' and $cachette == 'O') {
                $color = "#C0C0C0";
                $presence_texte = 'O';
            }
            if ($result['mur_creusable'] == 'O' and $piege == 'O') {
                $color = "#FFCC00";
                $presence_texte = 'O';
            }
            if ($presence_texte == 'O') {
                $texte = 'X:' . $result['pos_x'] . ' Y:' . $result['pos_y'];
            }
            ?>

            <td width="20" height="20">
                <div id="pos_<?php echo $result['pos_cod']; ?>"
                     style="width:25px;height:25px;background:<?php echo $color ?>;" <?php echo $onclick ?>><span
                            style="font-size:10px;"><?php echo $texte; ?></span></div>
            </td>
            <?php

            $p_y = $result['pos_y'];
            }
            ?>
        </tr>
    </table>

    <?php
}
if (!isset($included) || !$included) {
    $contenu_page = ob_get_contents();
    ob_end_clean();
    include "blocks/_footer_page_jeu.php";

}
?>
