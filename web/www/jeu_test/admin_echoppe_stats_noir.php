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
<?php
$erreur = 0;
if (!isset($methode)) {
    $methode = "debut";
}
$req = "select perso_admin_echoppe_noir from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
if ($db->f("perso_admin_echoppe_noir") != 'O') {
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0) {
    switch ($methode) {
        case "debut":
            //
            // en premier on liste les magasins et leur gérant éventuel
            //
            // on commence par les magasins avec gérants
            echo "<p class=\"titre\">Magasins avec gérants</p>";
            $req = "select lieu_cod,lieu_marge,lieu_prelev,lieu_compte,pos_x,pos_y,etage_libelle,perso_nom ";
            $req = $req . "from lieu,lieu_position,positions,etage,perso,magasin_gerant ";
            $req = $req . "where lieu_cod = lpos_lieu_cod ";
            $req = $req . "and lieu_tlieu_cod in (21) ";
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
                echo "<td class=\"soustitre2\"><p>Nom magasin</td>";
                echo "<td class=\"soustitre2\"><p>Gérant</td>";
                echo "<td class=\"soustitre2\"><p>Compte</td>";
                echo "<td></td>";
                echo "</tr>";

                while ($db->next_record()) {
                    echo "<tr>";
                    echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p>" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
                    echo "<td class=\"soustitre2\"><p><strong>" . $db->f("perso_nom") . "</strong></td>";
                    echo "<td class=\"soustitre2\"><p>" . $db->f("lieu_compte") . " brouzoufs</td>";
                    echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"", $PHP_SELF, "?methode=stats&lieu=" . $db->f("lieu_cod") . "\">Voir les stats !</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            // on fait les magasins sans gérance
            echo "<p class=\"titre\">Magasins hors gérance</p>";
            $req = "select lieu_cod,pos_x,pos_y,etage_libelle ";
            $req = $req . "from lieu,lieu_position,positions,etage ";
            $req = $req . "where lieu_cod = lpos_lieu_cod ";
            $req = $req . "and lieu_tlieu_cod in (21) ";
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
                    echo "<tr>";
                    echo "<td id=\"cell" . $db->f("lieu_cod") . "\" class=\"soustitre2\"><p>" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . "</td>";
                    echo "<td><p><a onMouseOver=\"changeStyles('cell" . $db->f("lieu_cod") . "',1)\" onMouseOut=\"changeStyles('cell" . $db->f("lieu_cod") . "',0)\" href=\"", $PHP_SELF, "?methode=stats&lieu=" . $db->f("lieu_cod") . "\">Voir les stats !</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            break;
        case "stats":
            $sens[1] = "vente (magasin vers aventurier) ";
            $sens[2] = "achat (aventurier vers magasin) ";
            $req = "select obj_nom,mtra_sens,sum(mtra_montant) as somme,count(mtra_cod) as nombre ";
            $req = $req . "from objet_generique,objets,mag_tran ";
            $req = $req . "where mtra_lieu_cod = $lieu ";
            $req = $req . "and mtra_obj_cod = obj_cod ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "group by obj_nom,mtra_sens ";
            $db->query($req);
            if ($db->nf() == 0) {
                echo "<p>Aucune transaction enregistrée dans votre échoppe.";
            } else {
                ?>
                <table>
                <tr>
                    <td class="soustitre2"><strong>Nom</strong></td>
                    <td class="soustitre2"><strong>Sens</strong></td>
                    <td class="soustitre2"><strong>Montant global</strong></td>
                    <td class="soustitre2"><strong>Nombre</strong></td>
                </tr>
                <?php
                while ($db->next_record()) {
                    echo "<tr>";
                    echo "<td class=\"soustitre2\">", $db->f("obj_nom"), "</td>";
                    $idx_sens = $db->f("mtra_sens");
                    echo "<td>", $sens[$idx_sens], "</td>";
                    echo "<td class=\"soustitre2\">", $db->f("somme"), "</td>";
                    echo "<td>", $db->f("nombre"), "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<p style=\"text-align:center\"><a href=\"", $PHP_SELF, "?lieu=$lieu&methode=stats2\">Voir le détail</a>";
            }


            break;
        case "stats2":
            $sens[1] = "vente (magasin vers aventurier) ";
            $sens[2] = "achat (aventurier vers magasin) ";
            $req = "select mtra_date,obj_nom,mtra_sens,mtra_montant,perso_nom,to_char(mtra_date,'DD/MM/YYYY hh24:mi:ss') as date_tran ";
            $req = $req . "from objet_generique,objets,mag_tran,perso ";
            $req = $req . "where mtra_lieu_cod = $lieu ";
            $req = $req . "and mtra_obj_cod = obj_cod ";
            $req = $req . "and obj_gobj_cod = gobj_cod ";
            $req = $req . "and mtra_perso_cod = perso_cod ";
            $req = $req . "order by mtra_date ";
            $db->query($req);
            if ($db->nf() == 0) {
                echo "<p>Aucune transaction enregistrée dans votre échoppe.";
            } else {
                ?>
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Objet</strong></td>
                        <td class="soustitre2"><strong>Perso</strong></td>
                        <td class="soustitre2"><strong>Sens</strong></td>
                        <td class="soustitre2"><strong>Montant</strong></td>
                        <td class="soustitre2"><strong>Date</strong></td>
                    </tr>
                <?php
                while ($db->next_record()) {
                    echo "<tr>";
                    echo "<td class=\"soustitre2\">", $db->f("obj_nom"), "</td>";
                    echo "<td>", $db->f("perso_nom"), "</td>";
                    $idx_sens = $db->f("mtra_sens");
                    echo "<td class=\"soustitre2\">", $sens[$idx_sens], "</td>";
                    echo "<td>", $db->f("mtra_montant"), "</td>";
                    echo "<td class=\"soustitre2\">", $db->f("date_tran"), "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }


            break;


    }


}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
