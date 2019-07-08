<?php

if (!@include "../includes/img_pack.php")
    include "includes/img_pack.php";
include "verif_connexion.php";
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" type="text/css" href="style_vue.php?num_etage=<?php echo $num_etage; ?>" title="essai">
<head>
    <SCRIPT LANGUAGE="JavaScript" src="base2.js"></SCRIPT>
    <title>Vue étage</title>
</head>

<body background="../images/fond5.gif">
<?php
$db2 = new base_delain;
$action = "action.php";

/* Deb AJOUT GoodWin */
$bool_admin_monstre = $db->is_admin_monstre($compt_cod);
$bool_admin = $db->is_admin($compt_cod);

if (!$bool_admin_monstre && !$bool_admin)
{
    $sess->delete();
    $auth->logout();
    $auth->auth_loginform();
    //header('Location: ' . $type_flux.G_URL . 'login2.php');
    exit();
}

/* Fin AJOUT GoodWin */

$req_distance = "select distance_vue($perso_cod) as distance";
$db->query($req_distance);
$db->next_record();
$distance_vue = $db->f("distance");

// on cherche la position
$req_etage = "select pos_etage,pos_cod,pos_x,pos_y,etage_affichage from perso_position,positions,etage ";
$req_etage = $req_etage . "where ppos_perso_cod = $perso_cod ";
$req_etage = $req_etage . "and ppos_pos_cod = pos_cod ";
$req_etage = $req_etage . "and pos_etage = etage_numero ";
$db->query($req_etage);
$db->next_record();
$aff_etage = $db->f("etage_affichage");
$etage_actuel = $db->f("pos_etage");
$pos_actuelle = $db->f("pos_cod");
$x_actuel = $db->f("pos_x");
$y_actuel = $db->f("pos_y");


echo("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" ID=\"tab_vue\" bgcolor=\"#FFFFFF\" >");
echo("<form name=\"deplacement\" method=\"post\" action=\"$action\">");
?>
<input type="hidden" name="methode" value="deplacement">
<input type="hidden" name="position">
<?php
// on cherche la distance de vue
$req_x = "select distinct pos_x from positions where pos_etage = $num_etage order by pos_x";
$db->query($req_x);
$db->next_record();
echo("<tr><td></td>");
$min_x = $db->f("pos_x");
$db->query($req_x);
while ($db->next_record())
{
    echo "<td style=\"coord2\"><p class=\"coord\">", $db->f("pos_x"), "</p></td>\r\n";
}
echo("</tr>\r\n");
// on rajoute la ligne des "y"
$req_y = "select distinct pos_y from positions where pos_etage = $num_etage order by pos_y desc";
$db->query($req_y);
?>
<script language="JavaScript" type="text/JavaScript">
    var carte = new Array();
    $i = 0;
    $depart = 0;
    <?php
    while ($db->next_record())
    {
        $req_map_vue = "select vue_etage2($num_etage," . $db->f("pos_y") . "," . $depart . ") as vue ";
        $db2->query($req_map_vue);
        $db2->next_record();
        $tab = explode("#", $db2->f("vue"));
        echo $tab[0];
        $depart = $tab[1];
    }
    ?>
    var etage;
    var i;
    var y_encours;
    var code_image;
    var comment;
    var isobjet;
    var texte;
    var style;
    var action;
    <?php echo("action='" . $action . "';\r\n"); ?>
    <?php echo("etage='" . $aff_etage . "';\r\n"); ?>
    <?php echo("img_path='" . str_replace(chr(92), chr(92) . chr(92), G_IMAGES) . "';\r\n"); ?>
    y_encours = -2000;
    for (i = 0; i < carte.length; i++) {
        texte = '';
        isobjet = 0;
        comment = carte[i][1] + ', ' + carte[i][2] + ', pos_cod = ' + carte[i][0];
        code_image = 0;
        if (y_encours != carte[i][2]) {
            y_encours = carte[i][2];
            document.write('<tr class="vueoff" height="10"><td height="10" style="coord2"><p class="coord">' + y_encours + '</p></td>');
        }

        style = 'caseVue v' + carte[i][10];

        texte = texte + '<td class="' + style + '">';

        if (carte[i][12] != 0) {
            texte = texte + '<div class="caseVue decor' + carte[i][12] + '" title="' + comment + '">';
        }

        if (carte[i][3] != 0) {
            texte = texte + '<div class="joueur" title="' + comment + '">';
        }

        if (carte[i][4] != 0) {
            texte = texte + '<div class="monstre" title="' + comment + '">';

        }

        if (carte[i][6] != 0) {
            texte = texte + '<div class="objet" title="' + comment + '">';

            isobjet = 1;
        }

        if (carte[i][7] != 0) {
            if (isobjet == 0) {
                texte = texte + '<div class="objet" title="' + comment + '">';
                isobjet = 1;
            }
        }

        if (carte[i][5] == 1) {
            texte = texte + '<div class="caseVue mur_' + carte[i][9] + '" title="' + comment + '">';
        }
        if (carte[i][11] != 0) {
            texte = texte + '<div class="caseVue lieu' + carte[i][11] + '" title="' + comment + '">';
        }
        if (carte[i][8] == 0) {
            texte = texte + '<div class="oncase">';
        }
        texte = texte + '<div title="' + comment + '">';
        texte = texte + '<div id="cell' + carte[i][0] + '" class="pasvu" style="background:url(\'http://www.jdr-delain.net/test_img/c1.gif\')">\r\n';
        texte = texte + '<img src="../img_temp/del.gif" width="28" height="28" alt="' + comment + '" title="' + comment + '">'
        texte = texte + '</div>';
        texte = texte + '</div>';
        if (carte[i][8] == 0) {
            texte = texte + '</div>';
        }
        if (carte[i][11] != 0) {
            texte = texte + '</div>';
        }
        if (carte[i][5] == 1) {
            texte = texte + '</div>';
        }

        if (isobjet == 1) {
            texte = texte + '</div>';
        }

        if (carte[i][4] != 0) {
            texte = texte + '</div>';
        }

        if (carte[i][3] != 0) {
            texte = texte + '</div>';
        }
        if (carte[i][12] != 0) {
            texte = texte + '</div>';
        }

        texte = texte + '</td>';


        document.write(texte);
        if (i < carte.length - 1) {
            j = i + 1;
            if (y_encours != carte[j][2]) {
                document.write('</tr>');
            }
        }
        else {
            document.write('</tr>');
        }
    }
</script>
<?php
echo("</form>");
echo("</table>");
?>
</body>
</html>
