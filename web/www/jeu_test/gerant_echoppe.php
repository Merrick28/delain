<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <script language="javascript">
        ns4 = document.layers;
        ie = document.all;
        ns6 = document.getElementById && !document.all;

        function changeStyles(id, mouse) {
            if (ns4) {
                alert("Sorry, but NS4 does not allow font changes.");
                return false;
            }
            else if (ie) {
                obj = document.all[id];
            }
            else if (ns6) {
                obj = document.getElementById(id);
            }
            if (!obj) {
                alert("unrecognized ID");
                return false;
            }

            if (mouse == 1) {
                obj.className = "navon";
            }

            if (mouse == 0) {
                obj.className = "navoff";
            }
            return true;
        }

    </script>
    <!DOCTYPE html>
    <html>
    <link rel="stylesheet" type="text/css" href="../style.css" title="essai">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="../css/delain.css" rel="stylesheet">
    <head>
        <title>Gestion d'échoppe</title>
    </head>
    <body>
    <div class="bordiv">
        <?php
        $erreur = 0;
        $req = "select perso_admin_echoppe from perso where perso_cod = $perso_cod ";
        $db->query($req);
        if ($db->nf() == 0) {
            echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !";
            $erreur = 1;
        } else {
            $db->next_record();
        }
        if ($db->f("perso_admin_echoppe") != 'O') {
            echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
            $erreur = 1;
        }
        if ($erreur == 0) {
            //
            // en premier on liste les magasins et leur gérant éventuel
            //
            // on commence par les magasins avec gérants
            echo "<p class=\"titre\">Magasins avec gérants</p>";
            $req = "select lieu_cod,lieu_nom,lieu_marge,lieu_prelev,lieu_compte,pos_x,pos_y,etage_libelle,perso_nom ";
            $req = $req . "from lieu,lieu_position,positions,etage,perso,magasin_gerant ";
            $req = $req . "where lieu_cod = lpos_lieu_cod ";
            $req = $req . "and lieu_tlieu_cod in (21,11,14) ";
            $req = $req . "and lpos_pos_cod = pos_cod ";
            $req = $req . "and pos_etage = etage_numero ";
            $req = $req . "and mger_lieu_cod = lieu_cod ";
            $req = $req . "and mger_perso_cod = perso_cod ";
            $req = $req . "order by pos_etage desc ";
            $db->query($req);
            if ($db->nf() == 0) {
                echo "<p>Aucun magasin n'est en gérance.";
            } else {
                echo "<table cellspacing=\"2\" cellpadding=\"2\">";
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p>Emplacement</td>";
                echo "<td class=\"soustitre2\"><p>Nom magasin</td>";
                echo "<td class=\"soustitre2\"><p>Gérant</td>";
                echo "<td class=\"soustitre2\"><p>Marge</td>";
                echo "<td class=\"soustitre2\"><p>Prélèvements</td>";
                echo "<td class=\"soustitre2\"><p>Compte</td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";

                while ($db->next_record()) {
                    echo "<tr>";
                    echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p>" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
                    echo "<td class=\"soustitre2\"><p><a href=\"gere_echoppe3.php?mag=" . $db->f("lieu_cod") . "\"><strong>" . $db->f("lieu_nom") . "</strong></a></td>";
                    echo "<td class=\"soustitre2\"><p><strong>" . $db->f("perso_nom") . "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $db->f("lieu_marge") . " %</td>";
                    echo "<td class=\"soustitre2\"><p>" . $db->f("lieu_prelev") . " %</td>";
                    echo "<td class=\"soustitre2\"><p>" . $db->f("lieu_compte") . " brouzoufs</td>";
                    echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_gerant.php?methode=modif&lieu=" . $db->f("lieu_cod") . "\">Modifier</a></td>";
                    echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_gerant.php?methode=supprime&lieu=" . $db->f("lieu_cod") . "\">Supprimer</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            // on fait les magasins sans gérance
            echo "<p class=\"titre\">Magasins hors gérance</p>";
            $req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle ";
            $req = $req . "from lieu,lieu_position,positions,etage ";
            $req = $req . "where lieu_cod = lpos_lieu_cod ";
            $req = $req . "and lieu_tlieu_cod in (21,11,14) ";
            $req = $req . "and lpos_pos_cod = pos_cod ";
            $req = $req . "and pos_etage = etage_numero ";
            $req = $req . "and not exists ";
            $req = $req . "(select 1 from magasin_gerant where mger_lieu_cod = lieu_cod) ";
            $req = $req . "order by pos_etage desc ";
            $db->query($req);
            if ($db->nf() == 0) {
                echo "<p>Aucun magasin n'est en gérance.";
            } else {
                echo "<table cellspacing=\"2\" cellpadding=\"2\">";
                while ($db->next_record()) {
                    if ($db->f("lieu_cod") == 11) {
                        $statut = 'Echoppe classique';
                    }
                    if ($db->f("lieu_cod") == 21) {
                        $statut = 'Marché noir';
                    }
                    if ($db->f("lieu_cod") == 14) {
                        $statut = 'Echoppe Runique';
                    }
                    echo "<tr>";
                    echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p><a href=\"gere_echoppe3.php?mag=" . $db->f("lieu_cod") . "\"><strong>" . $db->f("lieu_nom") . "</strong></a></td><td class=\"soustitre2\"> " . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
                    echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"modif_gerant.php?methode=ajout&lieu=" . $db->f("lieu_cod") . "\">Ajouter un gérant</a></td>
			<td>" . $statut . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        ?>
    </div>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

