<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <link rel="stylesheet" type="text/css" href="style_vue.php?num_etage=<?php echo $etage; ?>" title="essai">

    <SCRIPT LANGUAGE="JavaScript" src="base2.js"></SCRIPT>
    <script type="text/javascript">

        function changeFond(id1, style1) {
            var obj1 = document.getElementById(id1);
            obj1.className = 'caseVue v' + style1;
            return true;
        }

        function changeDec(id1, style1) {
            var obj1 = document.getElementById(id1);
            obj1.className = 'caseVue decor' + style1;
            return true;
        }

        function changeMur(id1, style1) {
            var obj1 = document.getElementById(id1);
            obj1.className = 'caseVue mur_' + style1;
            return true;
        }

        function active(champ) {
            champ.disabled = false;
            champ.style.backgroundColor = "white";
        }

    </script>
<?php
include "blocks/_test_droit_modif_etage.php";
$db2 = new base_delain;
$db3 = new base_delain;
if (!isset($methode)) {
    $methode = 'debut';
}

if (!isset($etage)) {
    $etage = 0;
}

$menus = "<p>Choisissez l’étage à modifier :</p>
	<form method='post' action='$PHP_SELF'>
	<select name='etage'>" .
    $html->etage_select($etage) .
    "</select>&nbsp;<input type='submit' value='Valider' class='test'/></form>

	<p><strong>Pour l’étage sélectionné :</strong></p>
	<p>

	<a href='modif_etage.php?methode=fond&etage=$etage'>Modifier les fonds.</a><br />
	<a href='modif_etage.php?methode=decor&etage=$etage'>Modifier les décors.</a><br />
	<a href='modif_etage.php?methode=a_mur&etage=$etage'>Ajouter/supprimer des murs.</a><br />
	<a href='modif_etage.php?methode=valide&etage=$etage'>Valider les changements ?</a> (Met à jour les automaps ; à faire si on a enlevé ou rajouté des murs, des lieux...)<br />
	<a href='vue_etage.php?num_etage=$etage'>Visualiser les changements ? </a><br /></p>
	<hr />
	<p><strong>Autres outils</strong><br />
	<a href='modif_etage3_styles.php'>Modifier les styles.</a><br />	
	<a href='modif_etage3_decors.php'>Modifier les décors.</a><br />	
	<a href='modif_etage3.php'>Créer / modifier un étage (caractéristiques générales)</a><br />
	<a href='modif_etage3bis.php'>Créer / modifier les lieux</a><br />
	<hr />
	<a href='modif_etage2.php'>Mines, changement des décors et modification du nombre de PA des déplacements (genre routes)</a><br />
	<a href='modif_etage4.php'>Positionnement des composants de potion</a> / <a href='Composants_positions.php'>Modif en masse des Positions des composants de potion</a><br />
	<a href='modif_etage5.php'>Mines, murs tangibles ou non et Interdiction du sort passage</a><br /></p>";

// Récupération des images existantes
// On y va à la bourrin : on regarde quels fichiers existent entre 0 et 999 pour chaque type.
$pref = '';
$suff = '';
$rep = '../../images/';
switch ($methode) {
    case "fond":
        $req_etage = "select etage_affichage from etage where etage_numero = $etage ";
        $db->query($req_etage);
        $db->next_record();
        $etage_affichage = $db->f("etage_affichage");
        $pref = "f_{$etage_affichage}_";
        $suff = ".png";
        break;

    case "decor":
        $pref = "dec_";
        $suff = ".gif";
        break;

    case "a_mur":
        $req_etage = "select etage_affichage from etage where etage_numero = $etage ";
        $db->query($req_etage);
        $db->next_record();
        $etage_affichage = $db->f("etage_affichage");
        $pref = "t_{$etage_affichage}_mur_";
        $suff = ".png";
        break;
}
$tableau = array();
$n = 0;
for ($i = 0; $i < 1000; $i++) {
    $nom = $rep . $pref . $i . $suff;
    if (is_file($nom)) {
        $tableau[$n] = array($i, $pref . $i . $suff);
        $n++;
    }
}

// Affichage des images existantes
if ($pref != '') {
    echo "<div class='bordiv' style='overflow:auto'>";
    echo "<div style='width:" . (40 * $n) . "px;'>";
    for ($i = 0; $i < $n; $i++) {
        $numero = $tableau[$i][0];
        $nom = $tableau[$i][1];
        echo "<div style=\"float:left; width:28px; height:38px; margin:6px;\">
			<img src='" . G_IMAGES . "$nom'/><br />$numero</div>\n";
    }
    echo "</div></div>";
}

switch ($methode) {
    case "debut":
        echo $menus;
        break;

    case "fond":
        echo "<form name=\"plateau\" action=\"modif_etage.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"etage\" value=\"$etage\">";
        echo "<input type=\"hidden\" name=\"methode\" value=\"fond2\">";
        echo "<input type=\"hidden\" name=\"style\" value=\"fond2\">";
        echo "<table>";
        $req_y = "select distinct pos_y from positions where pos_etage = $etage order by pos_y desc";
        $db->query($req_y);
        while ($db->next_record()) {
            echo "<tr>";
            $req_x = "select pos_decor, pos_cod, pos_x, pos_type_aff, coalesce(mur_type, 0) as mur_type
				from positions
				left outer join murs on mur_pos_cod = pos_cod
				where pos_etage = $etage ";
            $req_x = $req_x . "and pos_y = " . $db->f("pos_y") . " order by pos_x ";
            $db2->query($req_x);
            while ($db2->next_record()) {
                $mur = $db2->f("mur_type");
                $decor = $db2->f("pos_decor");
                $fond = $db2->f("pos_type_aff");
                $code = $db2->f("pos_cod");

                echo "<td class=\"caseVue v$fond\" onclick='active(document.plateau.pos$code)' id=\"sc$code\"><p>";

                echo "<div class=\"caseVue mur_$mur\" >";
                echo "<div class=\"caseVue decor$decor\">";
                echo "<img src=\"" . G_IMAGES . "del.gif\" width=\"28\" height=\"28\">";
                echo "</div>";
                echo "</div>";

                echo "<input size=\"2\" type=\"text\" disabled='disabled' style='background-color:#777777'
					name=\"pos$code\"
					value=\"$fond\"
					id=\"pos$code\"
					onChange=\"changeFond('sc$code', document.plateau.pos$code.value)\">";

                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<center><input type=\"submit\" class=\"test\" value=\"Modifier !\"></center></form>";
        echo "<hr />$menus";
        break;

    case "fond2":
        $cpt_upd = 0;
        foreach ($_POST as $key => $val) {
            if (substr($key, 0, 3) == 'pos') {
                $req = "update positions set pos_type_aff = $val where pos_cod = " . substr($key, 3);
                $db->query($req);
                $cpt_upd++;
            }
        }
        echo "<div class='bordiv'><p>Changements effectués :<br>$cpt_upd fonds modifiés.<br><a href=\"modif_etage.php?etage=$etage\">Retour !</a></p></div>";
        echo "<hr />$menus";
        break;

    case "decor":
        echo "<form name=\"plateau\" action=\"modif_etage.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"etage\" value=\"$etage\">";
        echo "<input type=\"hidden\" name=\"methode\" value=\"decor2\">";
        echo "<input type=\"hidden\" name=\"style\" value=\"decor2\">";
        echo "<table>";
        $req_y = "select distinct pos_y from positions where pos_etage = $etage order by pos_y desc";
        $db->query($req_y);
        while ($db->next_record()) {
            echo "<tr>";
            $req_x = "select pos_decor, pos_cod, pos_x, pos_type_aff, coalesce(mur_type, 0) as mur_type
				from positions
				left outer join murs on mur_pos_cod = pos_cod
				where pos_etage = $etage ";
            $req_x = $req_x . "and pos_y = " . $db->f("pos_y") . " order by pos_x ";
            $db2->query($req_x);
            while ($db2->next_record()) {
                $mur = $db2->f("mur_type");
                $decor = $db2->f("pos_decor");
                $fond = $db2->f("pos_type_aff");
                $code = $db2->f("pos_cod");

                echo "<td class=\"caseVue v$fond\" onclick='active(document.plateau.pos$code)'><p>";

                echo "<div class=\"caseVue mur_$mur\" >";
                echo "<div class=\"caseVue decor$decor\" id=\"sc$code\" >";
                echo "<img src=\"" . G_IMAGES . "del.gif\" width=\"28\" height=\"28\">";
                echo "</div>";
                echo "</div>";

                echo "<input size=\"2\" type=\"text\" disabled='disabled' style='background-color:#777777'
					name=\"pos$code\"
					value=\"$decor\"
					id=\"pos$code\"
					onChange=\"changeDec('sc$code', document.plateau.pos$code.value)\">";

                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<center><input type=\"submit\" class=\"test\" value=\"Modifier !\"></center></form>";
        echo "<hr />$menus";
        break;

    case "decor2":
        $cpt_upd = 0;
        foreach ($_POST as $key => $val) {
            if (substr($key, 0, 3) == 'pos') {
                $req = "update positions set pos_decor = $val where pos_cod = " . substr($key, 3);
                $db->query($req);
                $cpt_upd++;
            }
        }
        echo "<div class='bordiv'><p>Changements effectués :<br>$cpt_upd décors modifiés.<br><a href=\"modif_etage.php?etage=$etage\">Retour !</a></p></div>";
        echo "<hr />$menus";
        break;

    case "a_mur":
        echo "<p>Pour retirer un mur, mettre sa valeur à 0. Pour en rajouter un, tapez sa valeur (habituellement 990->999)<br></p>";
        echo "<form name=\"plateau\" action=\"modif_etage.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"etage\" value=\"$etage\" />";
        echo "<input type=\"hidden\" name=\"methode\" value=\"a_mur2\" />";
        echo "<input type=\"hidden\" name=\"style\" value=\"fond2\" />";
        echo "<table>";
        $req_y = "select distinct pos_y from positions where pos_etage = $etage order by pos_y desc";
        $db->query($req_y);
        while ($db->next_record()) {
            echo "<tr>";
            $req_x = "select pos_decor, pos_cod, pos_x, pos_type_aff, coalesce(mur_type, 0) as mur_type
				from positions
				left outer join murs on mur_pos_cod = pos_cod
				where pos_etage = $etage ";
            $req_x = $req_x . "and pos_y = " . $db->f("pos_y") . " order by pos_x ";
            $db2->query($req_x);
            while ($db2->next_record()) {
                $mur = $db2->f("mur_type");
                $decor = $db2->f("pos_decor");
                $fond = $db2->f("pos_type_aff");
                $code = $db2->f("pos_cod");

                echo "<td class=\"caseVue v$fond\"><p>";
                echo "<div id=\"idmur$code\" class=\"caseVue mur_$mur\" onclick='active(document.plateau.mur$code);'>";
                echo "<img src=\"" . G_IMAGES . "del.gif\" width=\"28\" height=\"28\">";
                echo "</div>";
                echo "<input size=\"2\" type=\"text\" disabled='disabled'
					id=\"mur$code\" name=\"mur$code\" value=\"$mur\" 
					onChange=\"changeMur('idmur$code', document.plateau.mur$code.value)\"
					style='background-color:#777777'>";

                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<center><input type=\"submit\" class=\"test\" value=\"Modifier !\"></center></form>";
        echo "<hr />$menus";
        break;

    case "a_mur2":
        $del_mur = '0';
        $cpt_del = 0;
        $cpt_upd = 0;
        $cpt_aj = 0;
        $murs_actuels = array();
        $req_murs = "select mur_pos_cod, mur_type from murs inner join positions on pos_cod = mur_pos_cod where pos_etage = $etage";
        $db->query($req_murs);
        while ($db->next_record()) {
            $mur_cod = $db->f('mur_pos_cod');
            $mur_type = $db->f('mur_type');
            $murs_actuels[$mur_cod] = $mur_type;
        }
        foreach ($_POST as $key => $val) {
            if (substr($key, 0, 3) == 'mur') {
                $mur = substr($key, 3);
                if ($val == 0 && isset($murs_actuels[$mur])) {
                    $del_mur .= ',' . $mur;
                    $cpt_del++;
                }

                if ($val != 0 && !isset($murs_actuels[$mur])) {
                    $req = "insert into murs (mur_pos_cod,mur_type,mur_tangible) values ($mur, $val, 'O') ";
                    $db->query($req);
                    $cpt_aj++;
                }

                if (isset($murs_actuels[$mur]) && $val != $murs_actuels[$mur] && $val != 0) {
                    $req = "update murs set mur_type = $val where mur_pos_cod = $mur";
                    $db->query($req);
                    $cpt_upd++;
                }
            }
        }
        $req = 'delete from murs where mur_pos_cod in (' . $del_mur . ')';
        $db->query($req);

        echo "<div class='bordiv'><p>Changements effectués :<br>$cpt_del murs supprimés,<br>$cpt_aj murs créés,<br>$cpt_upd murs modifiés.<br><a href=\"modif_etage.php?etage=$etage\">Retour !</a></p></div>";
        echo "<hr />$menus";
        break;

    case "valide":
        $req = "select init_automap($etage) ";
        $db->query($req);
        echo "<div class='bordiv'><p>Changements validés.<br><a href=\"modif_etage.php?etage=$etage\">Retour !</a></p></div>";
        echo "<hr />$menus";
        break;
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
