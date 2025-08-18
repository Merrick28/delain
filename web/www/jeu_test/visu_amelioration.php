<?php
include "blocks/_header_page_jeu.php";
ob_start();

$droit_modif = 'dcompt_controle';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur != 0)
{
    echo "<p>Erreur ! Vous n'êtes pas admin !";
    exit();
}
echo "<p class=\"titre\">Liste des améliorations pour ce perso</p>";
$req = "select perso_niveau,perso_amelioration_armure,perso_amelioration_degats,perso_amelioration_vue,perso_des_regen,perso_temps_tour as temps_tour, calcul_temps(perso_temps_tour) as aff_temps_tour,perso_amel_deg_dex,";
$req = $req . "perso_nb_amel_repar,perso_amelioration_nb_sort,perso_nb_receptacle,perso_nb_amel_chance_memo, perso_nb_amel_comp, ";
$req = $req . "perso_race_cod, perso_for_init, f_carac_base(perso_cod,'FOR') as perso_for, perso_int_init, f_carac_base(perso_cod,'INT') as perso_int, perso_dex_init, f_carac_base(perso_cod,'DEX') perso_dex, perso_con_init, f_carac_base(perso_cod,'CON') perso_con ";
$req = $req . "from perso where perso_cod = $perso_cod ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
echo "<center><table>";

echo "<tr><td class=\"soustitre2\" colspan=\"2\"><p><strong>Perso $perso_cod : niveau " . $result['perso_niveau'] . "</strong></td></tr>";
$nbat = $result['perso_niveau'] - 1 ;

$total_amel = 0 ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Force : </td>";
$nba = ((int)($result['perso_for']) - (int)($result['perso_for_init'])) ;
echo "<td><p>" . $nba . " (= {$result['perso_for']} - {$result['perso_for_init']}) </td>";
echo "</tr>";
$total_amel+= $nba;

$nba = ((int)($result['perso_dex']) - (int)($result['perso_dex_init'])) ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Dextérité : </td>";
echo "<td><p>" . $nba . " (= {$result['perso_dex']} - {$result['perso_dex_init']}) </td>";
echo "</tr>";
$total_amel+= $nba;

$nba = ((int)($result['perso_int']) - (int)($result['perso_int_init'])) ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Intelligence : </td>";
echo "<td><p>" . $nba . " (= {$result['perso_int']} - {$result['perso_int_init']}) </td>";
echo "</tr>";
$total_amel+= $nba;

$nba = ((int)($result['perso_con']) - (int)($result['perso_con_init'])) ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Constitution : </td>";
echo "<td><p>" . $nba . " (= {$result['perso_con']} - {$result['perso_con_init']}) </td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_amelioration_degats'] ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Dégats corps à corps : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_amelioration_degats'] ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Dégats corps à corps : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_amel_deg_dex'] ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Dégats distance : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_amelioration_armure']  ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Armure : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_amelioration_vue']  ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Vue : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_nb_amel_repar']  ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Capacité de réparation : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_amelioration_nb_sort']  ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Nombre de sorts : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_nb_receptacle']  ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Réceptacles : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_nb_amel_chance_memo']  ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Chances de mémorisation : </td>";
echo "<td><p>" . $nba . "</td>";
echo "</tr>";
$total_amel+= $nba;

$nba = $result['perso_des_regen'] - 1  ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Régénération : </td>";
echo "<td><p>" .$nba . "</td>";
echo "</tr>";
$total_amel+= $nba;


$dlt_tab_amelioration = [ 720	=> 0 , 690	=> 1 , 660	=> 2 , 635	=> 3 , 610	=> 4 , 585	=> 5 , 565	=> 6 , 545	=> 7 , 525	=> 8 , 510	=> 9 , 495	=> 10, 480	=> 11, 470	=> 12, 460	=> 13, 450	=> 14, 445	=> 15, 440	=> 16, 435	=> 17, 430	=> 18, 425	=> 19, 420	=> 20, 415	=> 21, 410	=> 22, 405	=> 23, 400	=> 24, 395	=> 25, 390	=> 26, 385	=> 27, 380	=> 28, 375	=> 29, 370	=> 30, 365	=> 31, 360	=> 32];
$dlt_amelioration = isset( $dlt_tab_amelioration[$result['temps_tour']]) ? $dlt_tab_amelioration[$result['temps_tour']] : 0 ;
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Temps de tour : </td>";
$tab_normal = explode(";", $result['aff_temps_tour']);
echo "<td><p>$tab_normal[0] h $tab_normal[1] m (<i>$dlt_amelioration améliorations<i>)</p></td>";
echo "</tr>";
$total_amel+= $dlt_amelioration;

echo "<tr>";
echo "<td colspan=2 class=\"soustitre2\"><p><b>COMPETENCES</b></p></td>";
echo "</tr>";

$perso_nb_amel_comp = $result['perso_nb_amel_comp'];
$total_compt_amel = 0 ;
$comp_amel = [25=>1, 61=>2,62=>3,63=>1,64=>2,65=>3,66=>1,67=>2,68=>3,72=>1,73=>2,74=>3,75=>1,76=>2,77=>3];
$req = "select comp_cod, comp_libelle from competences,perso_competences where comp_cod = pcomp_pcomp_cod and pcomp_perso_cod = $perso_cod and comp_cod IN (61,62,63,64,65,66,67,68,72,73,74,75,76,77)";
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
    echo "<tr>";
    echo "<td class=\"soustitre2\" ><p>" . $result['comp_libelle'] . " </td>";
    // Les nain on une amélioration AF de base.
    $nba = (in_array($result['comp_cod'], [25,61,62]) && $result['perso_race_cod']==2) ? ((int)$comp_amel[$result['comp_cod']] - 1) : (int)$comp_amel[$result['comp_cod']] ;
    $total_compt_amel += $nba ;
    echo "<td><p>".$nba."</p></td>";
    echo "</tr>";
    $total_amel+= $nba;
}

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Total Compétences : </td>";
echo "<td><p>" . $total_compt_amel . " / " .$perso_nb_amel_comp . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td colspan=2 class=\"soustitre2\" height='10'><p> </p></td>";
echo "</tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p><b>TOTAL Améliorations</b></p></td>";
echo "<td><p><b>$total_amel</b> <i>(sur $nbat)</i></p></td>";
echo "</tr>";


echo "</table></center>";


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";