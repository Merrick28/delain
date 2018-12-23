<?php
include "../includes/constantes.php";
$db = new base_delain;

// position
$ppos  = new perso_position();
$pos   = new positions();
$etage = new etage();

$ppos->getByPerso($perso_cod);
$pos->charge($ppos->ppos_pos_cod);
$etage->getByNumero($pos->pos_etage);

$x            = $pos->pos_x;
$y            = $pos->pos_y;
$distance_vue = $perso->distance_vue();
$portee       = $perso->portee_attaque();
if ($distance_vue <= $portee)
{
    $portee = $distance_vue;
}

if ($perso->type_arme() == 2)
{
    $type_arme = 2;
} else
{
    $type_arme = 1;
}

$pos_cod = $pos->pos_cod;
$etage   = $pos->pos_etage;

$lc             = new lock_combat();
$tab_lock_cible = $lc->getBy_lock_cible($perso_cod);

if (!$tab_lock_cible)
{
    $tab_vue = $perso->get_vue_non_lock();
} else
{
    $tab_vue = $perso->get_vue_lock();
}
echo("<input type=\"hidden\" name=\"type_arme\" value=\"$type_arme\">");

if (count($tab_vue) != 0)
{
    ?>
    <table width="100%" cellspacing="2" cellapdding="2">
        <tr>
            <td colspan="6" class="soustitre"><p class="soustitre">Cibles</td>
        </tr>
        <tr>
            <td></td>
            <td class="soustitre2"><strong>Nom</strong></td>
            <td class="soustitre2"><strong>Race</strong></td>
            <td class="soustitre2"><strong>X</strong></td>
            <td class="soustitre2"><strong>Y</strong></td>
            <td class="soustitre2"><strong>Distance</strong></td>
        </tr>

        <script language="JavaScript" type="text/JavaScript">
            var liste = new Array();
            <?php
            $i = 0;
            $jAttaquable = 0;
            foreach ($tab_vue as $detail_vue)
            {
                if ($detail_vue["traj"] == 1)
                {
                    $pv               = $detail_vue["perso_pv"];
                    $pv_max           = $detail_vue["perso_pv_max"];
                    $niveau_blessures = '';
                    if ($pv / $pv_max < 0.75)
                    {
                        $niveau_blessures = ' - ' . $tab_blessures[0];
                    }
                    if ($pv / $pv_max < 0.5)
                    {
                        $niveau_blessures = ' - ' . $tab_blessures[1];
                    }
                    if ($pv / $pv_max < 0.25)
                    {
                        $niveau_blessures = ' - ' . $tab_blessures[2];
                    }
                    if ($pv / $pv_max < 0.15)
                    {
                        $niveau_blessures = ' - ' . $tab_blessures[3];
                    }
                    $nom         = str_replace("\\", " ", $detail_vue["perso_nom"]);
                    $nom         = str_replace("'", "\'", $nom);
                    $type_perso  = $detail_vue["perso_type_perso"];
                    $type        = $perso_type_perso[$type_perso];
                    $perso_cible = $detail_vue["perso_cod"];
                    $race        = $detail_vue["race_nom"];
                    $x           = $detail_vue["pos_x"];
                    $y           = $detail_vue["pos_y"];
                    $distance    = $detail_vue["distance"];
                    if ($detail_vue["distance"] <= $portee)
                    {
                        $attaquable  = 1;
                        $jAttaquable = $jAttaquable + 1;

                        $listePersoAttaquable[$jAttaquable] = $perso_cible;
                    } else
                    {
                        $attaquable = 0;
                    }
                    $style = "soustitre2";
                    if ($detail_vue["surcharge"] == 1)
                    {
                        $style = "surcharge1";
                    }
                    if ($detail_vue["surcharge"] == 2)
                    {
                        $style = "surcharge2";
                    }
                    if ($detail_vue["obstruction"] > 0)
                    {
                        $style = "soustitre2 obstruction1";
                    }
                    if ($detail_vue["obstruction"] > 5)
                    {
                        $style = "soustitre2 obstruction2";
                    }

                    echo("liste[$i] = ['$perso_cible','$nom','$type','$race','$x','$y','$distance','$attaquable','$niveau_blessures','$style'];\r\n");
                    $i = $i + 1;
                }
            }

            ?>

            var i;
            var textetableau = '';
            for (i = 0; i < liste.length; i++) {
                textetableau += '<tr>';
                /*textetableau+='<td><input type="radio" name="cible" value="' + liste[i][0] + '" onClick="changeStyles(\'cell' + liste[i][0] + '\',1)" onBlur="changeStyles(\'cell' +  liste[i][0] + '\',0)" id="bouton' + liste[i][0] + '"></td>';*/
                textetableau += '<td><input type="radio" name="cible" value="' + liste[i][0] + '" class="change_class_on_select" data-class-onclick="navon" data-class-dest="cell' + liste[i][0] + '" id="bouton' + liste[i][0] + '"></td>';
                textetableau += '<td id="cell' + liste[i][0] + '" class="' + liste[i][9] + ' allliste"><label for="bouton' + liste[i][0] + '"><strong>' + liste[i][1] + '</strong> (' + liste[i][2] + '<strong>' + liste[i][8] + '</strong>)</label></td>';
                textetableau += '<td>' + liste[i][3] + '</td>';
                textetableau += '<td>' + liste[i][4] + '</td>';
                textetableau += '<td>' + liste[i][5] + '</td>';
                textetableau += '<td>' + liste[i][6] + '</td>';
                textetableau += '</tr>';
            }
            document.write(textetableau);
        </script>
    </table>

    <?php

    // on regarde si la cible ne subit pas un malus de désorientation (sort Morsure du soleil) pour message de prévention !!!
    if ($perso->get_valeur_bonus('DES') > 0)
    {
        echo "<strong>ATTENTION, vous subissez une désorientation, le choix de votre cible n'est pas assuré!</strong><br>";
    }

} else
{
    echo("Pas de cible en vue !");
}
