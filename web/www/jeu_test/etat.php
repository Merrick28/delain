<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <table width="100%">
    <!-- PA RESTANTS -->
<?php
$req = "select perso_nom,perso_utl_pa_rest from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
if ($db->f("perso_utl_pa_rest") == 1) {
    $util = $db->f("perso_nom") . " <strong>utilise</strong> ses PA restants pour réduire le temps de tour suivant. ";
    $ch_util = 0;
} else {
    $util = $db->f("perso_nom") . " <strong>n'utilise pas</strong> ses PA restants pour réduire le temps de tour suivant. ";
    $ch_util = 1;
}
?>
    <tr>
        <td class="titre"><p class="titre">Utilisation des PA restants</p></td>
    </tr>
    <tr>
        <td><p>
            <?php
            echo $util . "<a href=\"etat.php?ch_util=$ch_util\">(changer ?)</a>";
            ?></td>
    </tr>


    <!-- CONCENTRATION -->
    <tr>
        <td class="titre"><p class="titre">Concentration</p></td>
    </tr>
    <tr>
        <td>
            <?php
            $req_concentration = "select concentration_nb_tours from concentrations where concentration_perso_cod = $perso_cod";
            $db->query($req_concentration);
            $nb_concentration = $db->nf();
            if ($nb_concentration == 0) {
                echo("<p>Vous n'avez effectué aucune concentration. ");
            } else {
                $db->next_record();
                echo "<p>Vous êtes concentré(e) pendant " . $db->f("concentration_nb_tours") . " tours. ";
            }
            ?>
            <?php
            echo("<a href=\"valide_concentration.php\">Se concentrer ! (4 PA)</a>");
            if ($nb_concentration != 0) {
                echo("<p><em>Attention !! Les concentrations ne se cumulent pas. Si vous vous concentrez de nouveau, la concentration précédente sera annulée !</em></p>");
            }
            ?>
        </td>
    </tr>
    <!-- COMBAT -->
    <tr>
        <td class="titre"><p class="titre">Combat</p></td>
    </tr>
<?php
if ($db->is_locked($perso_cod)) {
    $combat = "Vous êtes actuellement engagé en combat.";
} else {
    $combat = "Vous êtes actuellement hors combat.";
}
?>
    <tr>
        <td><p style="text-align:center;"><?php echo $combat; ?></td>
    </tr>
<?php
$req = "select perso_test from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
if ($db->f("perso_test") == 1) {
    $req = "select mcom_nom from perso,mode_combat
		where perso_cod = $perso_cod
		and perso_mcom_cod = mcom_cod";
    $db->query($req);
    $db->next_record();
    ?>
    <tr>
        <td><p style="text-align:center;">Vous êtes en mode <strong><?php echo $db->f("mcom_nom"); ?></strong><br>
                <a href="mode_combat.php">Changer de mode ?</a></td>
    </tr>

    <?php
}
?>

    <!-- LEGITIMES DEFENSES -->
    <tr>
    <td>
    <table width="100%" border="1">
    <tr>
    <td valign="top">
    <table width="100%">
    <!-- BLOCAGES DE COMBAT -->
    <tr>
        <td class="titre"><p class="titre">Blocages de combat</p></td>
    </tr>
    <tr>
        <td>
            <?php
            $cout_des = $param->getparm(60);
            echo("<p><strong>En tant que cible :</strong>");
            $req_at = "select lock_attaquant,lock_nb_tours,perso_nom from perso,lock_combat ";
            $req_at = $req_at . "where lock_cible = $perso_cod ";
            $req_at = $req_at . "and lock_attaquant = perso_cod ";
            $req_at = $req_at . "and perso_actif = 'O' ";
            $db->query($req_at);
            $nb_at = $db->nf();
            if ($nb_at == 0)
            {
                echo " Vous n'êtes pas bloqué en tant que cible.";
            }
            else
            {
            ?>
            <form name="visu_evt3" method="post" action="visu_evt_perso.php">
                <input type="hidden" name="visu">
                <input type="hidden" name="num_guilde">
                <table cellspacing="2" cellpadding="2">
                    <tr>
                        <td class="soustitre2"><p><strong>Nom</strong></td>
                        <td class="soustitre2"><p><strong>Tours</strong></td>
                        <td></td>
                    </tr>
                    <?php
                    while ($db->next_record()) {
                        echo "<tr>";
                        echo "<td class=\"soustitre2\"><p><strong><a href=\"javascript:document.visu_evt3.visu.value='" . $db->f("lock_attaquant") . "';document.visu_evt3.submit();\">" . $db->f("perso_nom") . "</a></strong></td>";
                        echo "<td><p style=\"text-align:center;\">" . $db->f("lock_nb_tours") . "</td>";
                        echo "<td><p><a href=\"action.php?methode=desengagement&cible=", $db->f("lock_attaquant"), "&valide=O\">Se désengager ? ($cout_des PA)</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</form>";
                    }
                    // 2e partie
                    echo("<p><strong>En tant qu'attaquant :</strong>");
                    $req_at = "select lock_cible,lock_nb_tours,perso_nom from perso,lock_combat ";
                    $req_at = $req_at . "where lock_attaquant = $perso_cod ";
                    $req_at = $req_at . "and lock_cible = perso_cod ";
                    $req_at = $req_at . "and perso_actif = 'O' ";
                    $db->query($req_at);
                    $nb_at = $db->nf();
                    if ($nb_at == 0) {
                        echo " Vous n'êtes pas bloqué en tant qu'attaquant.";
                    } else {
                        ?>
                        <form name="visu_evt4" method="post" action="visu_evt_perso.php"><input type="hidden"
                                                                                                name="visu"><input
                                    type="hidden" name="num_guilde">
                            <table cellspacing="2" cellpadding="2">
                                <tr>
                                    <td class="soustitre2"><p><strong>Nom</strong></td>
                                    <td class="soustitre2"><p><strong>Tours</strong></td>
                                    <td></td>
                                </tr>
                                <?php
                                while ($db->next_record()) {
                                    echo "<tr>";
                                    echo "<td class=\"soustitre2\"><p><strong><a href=\"javascript:document.visu_evt4.visu.value='" . $db->f("lock_cible") . "';document.visu_evt4.submit();\">" . $db->f("perso_nom") . "</a></strong></td>";
                                    echo "<td><p style=\"text-align:center;\">" . $db->f("lock_nb_tours") . "</td>";
                                    echo "<td><p><a href=\"action.php?methode=desengagement&cible=", $db->f("lock_cible"), "&valide=O\">Se désengager ? ($cout_des PA)</a></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </form>
                        <?php
                    }
                    ?>
                </table>
        </td>
        <td valign="top">
            <table width="100%">
                <tr>
                    <td class="titre"><p class="titre">Légitimes défenses</p></td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo("<p><strong>En tant que cible :</strong>");
                        $req_at = "select perso_cod,perso_nom,riposte_nb_tours from perso,riposte ";
                        $req_at = $req_at . "where riposte_cible = $perso_cod ";
                        $req_at = $req_at . "and riposte_attaquant = perso_cod ";
                        $req_at = $req_at . "and perso_actif = 'O' ";
                        $req_at = $req_at . "and perso_type_perso = 1 ";
                        $db->query($req_at);
                        $nb_at = $db->nf();
                        if ($nb_at == 0)
                        {
                            echo(" Vous n'avez aucune légitime défense.");
                        }
                        else
                        {
                        ?>
                        <form name="visu_evt" method="post" action="visu_desc_perso.php">
                            <input type="hidden" name="visu">
                            <input type="hidden" name="num_guilde">
                            <table cellspacing="2" cellpadding="2">
                                <tr>
                                    <td class="soustitre2"><p><strong>Nom</strong></td>
                                    <td class="soustitre2"><p><strong>Tours</strong></td>
                                </tr>
                                <?php
                                while ($db->next_record()) {
                                    echo "<tr>";
                                    echo "<td class=\"soustitre2\"><p><strong><a href=\"javascript:document.visu_evt.visu.value='" . $db->f("perso_cod") . "';document.visu_evt.submit();\">" . $db->f("perso_nom") . "</a></strong></td>";
                                    echo "<td><p style=\"text-align:center;\">" . $db->f("riposte_nb_tours") . "</td>";
                                    echo "</tr>";
                                }
                                echo "</table>";
                                echo "</form>";
                                }
                                // 2e partie
                                echo("<p><strong>En tant qu'attaquant :</strong>");
                                $req_at = "select perso_cod,perso_nom,riposte_nb_tours from perso,riposte ";
                                $req_at = $req_at . "where riposte_cible = perso_cod ";
                                $req_at = $req_at . "and riposte_attaquant = $perso_cod ";
                                $req_at = $req_at . "and perso_actif = 'O' ";
                                $req_at = $req_at . "and perso_type_perso = 1 ";
                                $db->query($req_at);
                                $nb_at = $db->nf();
                                if ($nb_at == 0) {
                                    echo(" Aucun perso ne peut utiliser la légitime défense contre vous.");
                                } else {
                                    ?>
                                    <form name="visu_evt2" method="post" action="visu_desc_perso.php"><input
                                                type="hidden" name="visu"><input type="hidden" name="num_guilde">
                                        <table cellspacing="2" cellpadding="2">
                                            <tr>
                                                <td class="soustitre2"><p><strong>Nom</strong></td>
                                                <td class="soustitre2"><p><strong>Tours</strong></td>
                                            </tr>
                                            <?php
                                            while ($db->next_record()) {
                                                echo "<tr>";
                                                echo "<td class=\"soustitre2\"><p><strong><a href=\"javascript:document.visu_evt2.visu.value='" . $db->f("perso_cod") . "';document.visu_evt2.submit();\">" . $db->f("perso_nom") . "</a></strong></td>";
                                                echo "<td><p style=\"text-align:center;\">" . $db->f("riposte_nb_tours") . "</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </table>
                                    </form>
                                    <?php
                                }
                                ?>
                            </table>
                    </td>
                </tr>
                <?php


                $req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
                $db->query($req_or);
                $nb_or = $db->nf();
                if ($nb_or == 0) {
                    $qte_or = 0;
                } else {
                    $db->next_record();
                    $qte_or = $db->f("pbank_or");
                }
                ?>
            </table>
        </td>
    </tr>
    <!-- BANQUE -->
    <tr>
        <td class="titre"><p class="titre">Banque</p></td>
    </tr>
    <tr>
        <td><p>Vous avez <?php echo $qte_or; ?> brouzoufs à la banque.</td>
    </tr>
    <!-- TEMPLE -->
    <tr>
        <td class="titre"><p class="titre">Temple choisi</p></td>
    </tr>
<?php
$req_temple = "select lieu_nom,pos_x,pos_y,etage_libelle,ptemple_nombre ";
$req_temple = $req_temple . "from perso_temple,positions,lieu_position,lieu,etage ";
$req_temple = $req_temple . "where ptemple_perso_cod = $perso_cod ";
$req_temple = $req_temple . "and ptemple_pos_cod = pos_cod ";
$req_temple = $req_temple . "and lpos_pos_cod = pos_cod ";
$req_temple = $req_temple . "and lpos_lieu_cod = lieu_cod ";
$req_temple = $req_temple . "and pos_etage = etage_numero ";
$db->query($req_temple);
$nb = $db->nf();
if ($nb == 0) {
    echo "<tr><td><p>Vous n'avez pas de temple spécifique pour vous ramener en cas de mort.</td></tr>";
} else {
    $db->next_record();
    echo "<tr><td>";
    echo "<table width=\"100%\">";
    echo "<tr><td class=\"soustitre2\"><p><strong>Nom</strong></td><td class=\"soustitre2\"><p style=\"text-align:center;\"><strong>X</strong></td><td class=\"soustitre2\"><p style=\"text-align:center;\"><strong>Y</n></td><td class=\"soustitre2\"><p style=\"text-align:center;\"><strong>Etage</strong></td><td class=\"soustitre2\"><p>Probabilité de retour</td></tr>";
    echo "<tr><td class=\"soustitre2\"><p><strong>" . $db->f("lieu_nom") . "</strong></td>";
    echo "<td><p style=\"text-align:center;\">" . $db->f("pos_x") . "</td>";
    echo "<td><p style=\"text-align:center;\">" . $db->f("pos_y") . "</td>";
    echo "<td><p style=\"text-align:center;\">" . $db->f("etage_libelle") . "</td>";
    $chance = $db->f("ptemple_nombre");
    $chance = 100 - ($chance * $param->getparm(32));
    echo "<td><p>" . $chance . " %</td>";
    echo "</tr>";
    echo "</table>";
    echo "</td></tr>";
}
/* BONUS PERMANENTS */
echo("<tr><td class=\"titre\"><p class=\"titre\">Bonus permanents</p></td></tr>");
echo("<tr><td>");
$req_bonus = "select bonus_degats_melee($perso_cod) as melee,bonus_arme_distance($perso_cod) as distance";
$db->query($req_bonus);
$db->next_record();

printf("<p>Bonus aux dégats en corps à corps : <strong>%s dégat</strong><br />", $db->f("melee"));
printf("Bonus en compétence des armes à distance : <strong>%s", $db->f("distance"));
echo(" %</strong>");
echo("</td></tr>");

echo("<tr><td class=\"titre\"><p class=\"titre\">Bonus temporaires</p></td></tr>");
echo("<tr><td>");

$req_bonus = "select tonbus_libelle,bonus_valeur,bonus_nb_tours ";
$req_bonus = $req_bonus . "from bonus,bonus_type ";
$req_bonus = $req_bonus . "where bonus_perso_cod = $perso_cod ";
$req_bonus = $req_bonus . "and bonus_tbonus_libc = tbonus_libc ";
$db->query($req_bonus);
$nb_bonus = $db->nf();
if ($nb_bonus == 0) {
    echo("<p>Vous n'avez aucun bonus/malus en ce moment.");
} else {
    echo("<table>");

    echo("<tr>");
    echo("<td class=\"soustitre2\"><p><strong>Bonus</strong></td>");
    echo("<td class=\"soustitre2\"><p><strong>Valeur</strong></td>");
    echo("<td class=\"soustitre2\"><p><strong>Nombre de tours</strong></td>");
    echo("</tr>");

    while ($db->next_record()) {
        echo "<tr>";
        echo "<td class=\"soustitre2\"><p><strong>" . $db->f("tonbus_libelle") . "</strong></td>";
        if ($db->f("bonus_valeur") >= 0) {
            $signe = '+';
        } else {
            $signe = '';
        }
        echo "<td><p style=\"text-align:center;\">" . $signe . $db->f("bonus_valeur") . "</td>";
        echo "<td><p style=\"text-align:center;\">" . $db->f("bonus_nb_tours") . "</td>";
        echo "</tr>";
    }
    echo "</table>";


}

echo "</td></tr></table>";

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

