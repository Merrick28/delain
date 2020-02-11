<?php
include "blocks/_header_page_jeu.php";
ob_start();

$droit_modif = 'dcompt_controle';
include "blocks/_test_droit_modif_generique.php";

if ($erreur != 0)
{
    echo "<p>Erreur ! Vous n'êtes pas admin !";
    exit();
}
echo "<p class=\"titre\">Liste des améliorations pour ce perso</p>";
$req = "select perso_niveau,perso_amelioration_armure,perso_amelioration_degats,perso_amelioration_vue,perso_des_regen,calcul_temps(perso_temps_tour) as temps_tour,perso_amel_deg_dex,";
$req = $req . "perso_nb_amel_repar,perso_amelioration_nb_sort,perso_nb_receptacle,perso_nb_amel_chance_memo ";
$req = $req . "from perso where perso_cod = $perso_cod ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
echo "<center><table>";

echo "<tr><td class=\"soustitre2\" colspan=\"2\"><p><strong>Perso $perso_cod : niveau " . $result['perso_niveau'] . "</strong></td></tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Dégats corps à corps : </td>";
echo "<td><p>" . $result['perso_amelioration_degats'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Dégats distance : </td>";
echo "<td><p>" . $result['perso_amel_deg_dex'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Armure : </td>";
echo "<td><p>" . $result['perso_amelioration_armure'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Vue : </td>";
echo "<td><p>" . $result['perso_amelioration_vue'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Capacité de réparation : </td>";
echo "<td><p>" . $result['perso_nb_amel_repar'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Nombre de sorts : </td>";
echo "<td><p>" . $result['perso_amelioration_nb_sort'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Réceptacles : </td>";
echo "<td><p>" . $result['perso_nb_receptacle'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Chances de mémorisation : </td>";
echo "<td><p>" . $result['perso_nb_amel_chance_memo'] . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Régénération : </td>";
$regen = $result['perso_des_regen'] - 1;
echo "<td><p>" . $regen . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Temps de tour : </td>";
$tab_normal = explode(";", $result['temps_tour']);
echo "<td><p>$tab_normal[0] h $tab_normal[1] m</p></td>";
echo "</tr>";
$req = "select comp_libelle from competences,perso_competences where comp_cod = pcomp_pcomp_cod and pcomp_perso_cod = $perso_cod and comp_cod IN (61,62,63,64,65,66,67,68,72,73,74,75,76,77)";
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
    echo "<tr>";
    echo "<td class=\"soustitre2\"><p>" . $result['comp_libelle'] . " </td>";
    echo "<td><p></p></td>";
    echo "</tr>";
}


echo "</table></center>";


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";