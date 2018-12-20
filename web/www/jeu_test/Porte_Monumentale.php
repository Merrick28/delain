<?php
include "blocks/_tests_appels_page_externe.php";

include "../includes/constantes.php";
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.css" rel="stylesheet">
<head>
    <title>Porte monumentale</title>
</head>
<body>
<div class="bordiv">
    <?php

    $type_lieu = 36;
    $nom_lieu = 'une porte monumentale';

    include "blocks/_test_lieu.php";

    if (!isset($methode)) {
        $methode = 'debut';
    }
    if ($erreur == 0) {
        $req = "select perso_pnj from perso where perso_cod = $perso_cod";
        $db->query($req);
        $db->next_record();
        $quatrieme = $db->f("perso_pnj") == 2;

        $req = "select lpos_lieu_cod,pos_etage, pos_cod from lieu_position,perso_position,positions
		where ppos_perso_cod = $perso_cod 
			and ppos_pos_cod = lpos_pos_cod 
			and ppos_pos_cod = pos_cod";
        $db->query($req);
        $db->next_record();
        $lieu_cod = $db->f("lpos_lieu_cod");
        $etage_cod = $db->f("pos_etage");
        $pos_cod = $db->f("pos_cod");
        switch ($methode) {
            case "entrer_donjon":

                $req = "select entrer_donjon(" . $perso_cod . "," . $etage_num . "," . $pos_cod . ") as res";
                $db->query($req);
                $db->next_record();

                $res = $db->f("res");
                $libelle = explode(";", $res);
                echo $libelle[1];

                $break = 'O';

                break;

            case "debut":
                ?>
                <p>
                    <img src="../images/batadmin.gif"
                         alt="Bâtiment administratif"><strong><?php echo($tab_lieu['nom'] . '</strong> - ' . $tab_lieu['description']); ?>
                <p>Bonjour,<br>
                    Voici ce que vous pouvez faire ici :<br>
                <hr><br>
                Entrer dans un donjon : <br>

                <?php
                echo("<table cellspacing=\"2\" cellpadding=\"2\">");
                echo("<tr><td class=\"soustitre2\" colspan=\"4\"><p style=\"text-align:center;\">Répartition par Donjon : </td></tr>");
                echo("<tr><td class=\"soustitre2\"><p>Donjon</td>
			<td class=\"soustitre2\"><p>Personnages</td>
			<td class=\"soustitre2\"><p>Niveau moyen</td>
			<td class=\"soustitre2\"><p>Niveau maximum</td>
			</tr>");
                $req = "select etage_libelle, carene_level_max, ";
                $req = $req . "(select count(parene_perso_cod) from perso_arene ";
                $req = $req . " where parene_etage_numero = etage_numero) as joueur,";
                $req = $req . "(select sum(perso_niveau) from perso, perso_arene ";
                $req = $req . "where parene_etage_numero = etage_numero ";
                $req = $req . "and parene_perso_cod = perso_cod ) as jnv ";
                $req = $req . "from etage, carac_arene ";
                $req = $req . "where etage_arene = 'O' ";
                $req = $req . "and etage_type_arene = 2 ";
                $req = $req . "and etage_numero = carene_etage_numero ";
                if ($quatrieme)
                    $req = $req . "and etage_quatrieme_perso = 'O' ";
                else
                    $req = $req . "and etage_quatrieme_perso = 'N' ";
                $db->query($req);

                while ($db->next_record()) {
                    echo "<tr><td class=\"soustitre2\"><p>" . $db->f("etage_libelle") . "</p></td>
				<td><p>" . $db->f("joueur") . "</td>
				<td><p>" . ($db->f("joueur") != 0 ?
                            round($db->f("jnv") / $db->f("joueur"), 0) :
                            0) . "</td>
				<td><p>" . ($db->f("carene_level_max") != 0 ?
                            $db->f("carene_level_max") : 'Tous niveaux') . "</td></tr>";

                }

                echo("</table>");


                echo "<form name=\"ea\" method=\"post\" action=" . $PHP_SELF . ">";
                echo "<input type=\"hidden\" name=\"methode\" value=\"entrer_donjon\">";
                echo "<select name=\"etage_num\">";
                $req = "select etage_numero, etage_libelle from etage where etage_arene = 'O' and etage_type_arene = 2 ";
                if ($quatrieme)
                    $req = $req . "and etage_quatrieme_perso = 'O' ";
                else
                    $req = $req . "and etage_quatrieme_perso = 'N' ";
                $db->query($req);

                while ($db->next_record()) {
                    echo "<option value=" . $db->f("etage_numero") . ">" . $db->f("etage_libelle") . "</option>";
                }
                echo "</select>";
                echo "<input type=\"submit\" value=\"Entrer (4 PA)\" />";
                echo "</form>";
        }
    }

    if (!isset($break)) {
        echo "</form>";
        include_once "quete.php";
    }


    ?>

</div>
</body>
</html>
