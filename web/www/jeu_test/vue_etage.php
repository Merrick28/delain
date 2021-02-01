<?php

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
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

$action = "action.php";

$compte = new compte;
$compte = $verif_connexion->compte;

/* Deb AJOUT GoodWin */
$bool_admin_monstre = $compte->is_admin_monstre();
$bool_admin         = $compte->is_admin();

if (!$bool_admin_monstre && !$bool_admin)
{
    $sess->delete();
    $auth->logout();
    $auth->auth_loginform();
    exit();
}

/* Fin AJOUT GoodWin */

$req_distance = "select distance_vue($perso_cod) as distance";
$stmt         = $pdo->query($req_distance);
$result       = $stmt->fetch();
$distance_vue = $result['distance'];

// on cherche la position
$req_etage    = "select pos_etage,pos_cod,pos_x,pos_y,etage_affichage from perso_position,positions,etage ";
$req_etage    = $req_etage . "where ppos_perso_cod = $perso_cod ";
$req_etage    = $req_etage . "and ppos_pos_cod = pos_cod ";
$req_etage    = $req_etage . "and pos_etage = etage_numero ";
$stmt         = $pdo->query($req_etage);
$result       = $stmt->fetch();
$aff_etage    = $result['etage_affichage'];
$etage_actuel = $result['pos_etage'];
$pos_actuelle = $result['pos_cod'];
$x_actuel     = $result['pos_x'];
$y_actuel     = $result['pos_y'];


echo("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" ID=\"tab_vue\" bgcolor=\"#FFFFFF\" >");
echo("<form name=\"deplacement\" method=\"post\" action=\"$action\">");
?>
<input type="hidden" name="methode" value="deplacement">
<input type="hidden" name="position">
<?php
// on cherche la distance de vue
$req_x  = "select distinct pos_x from positions where pos_etage = $num_etage order by pos_x";
$stmt   = $pdo->query($req_x);
$result = $stmt->fetch();
echo("<tr><td></td>");
$min_x = $result['pos_x'];
$stmt  = $pdo->query($req_x);
while ($result = $stmt->fetch())
{
    echo "<td style=\"coord2\"><p class=\"coord\">", $result['pos_x'], "</p></td>\r\n";
}
echo("</tr>\r\n");
// on rajoute la ligne des "y"
$req_y = "select distinct pos_y from positions where pos_etage = $num_etage order by pos_y desc";
$stmt  = $pdo->query($req_y);
?>
<script language="JavaScript" type="text/JavaScript">
    var carte = [];
    $i = 0;
    $depart = 0;
    <?php
    while ($result = $stmt->fetch())
    {
        $req_map_vue = "select vue_etage2($num_etage," . $result['pos_y'] . "," . $depart . ") as vue ";
        $stmt2       = $pdo->query($req_map_vue);
        $result2     = $stmt2->fetch();
        $tab         = explode("#", $result2['vue']);
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
        texte = texte + '<img src="../img_temp/del.gif" width="28" height="28" alt="' + comment + '" title="' + comment + '">';
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
