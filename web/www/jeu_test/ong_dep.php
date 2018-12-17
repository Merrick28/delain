<?php
if (!isset($position))
    $position = '';
$req_position_actuelle = "select perso_nom,pos_cod,pos_x,pos_y,perso_pa,pos_etage from perso_position,positions,perso ";
$req_position_actuelle = $req_position_actuelle . "where ppos_perso_cod = $perso_cod ";
$req_position_actuelle = $req_position_actuelle . "and ppos_pos_cod = pos_cod ";
$req_position_actuelle = $req_position_actuelle . "and perso_cod = $perso_cod";
$db->query($req_position_actuelle);
if ($db->nf())
{
    $db->next_record();
    $x[1] = $db->f("pos_x");
    $y[1] = $db->f("pos_y");
    $x[0] = $x[1] - 1;
    $x[2] = $x[1] + 1;
    $y[0] = $y[1] - 1;
    $y[2] = $y[1] + 1;
    $nom = $db->f("perso_nom");
    $etage = $db->f("pos_etage");
    $position_actuelle = $db->f("pos_cod");
    ?>
    <div class="centrer">
        <form name="ong_dep" id="ong_dep" method="post" action="action.php">
            <input type="hidden" name="methode" value="deplacement">
            <table width="100">
                <?php

                for ($cpty = 2;
                $cpty >= 0;
                $cpty--)
                {
                ?>
                <tr>
                    <?php
                    for ($cptx = 0; $cptx < 3; $cptx++)
                    {
                        $req_pos = "select pos_cod,pos_x,pos_y from positions ";
                        $req_pos = $req_pos . "where pos_x = $x[$cptx] ";
                        $req_pos = $req_pos . "and pos_y = $y[$cpty] ";
                        $req_pos = $req_pos . "and pos_etage = $etage ";
                        $req_pos = $req_pos . "and not exists (select 1 from murs where mur_pos_cod = pos_cod) ";
                        $db->query($req_pos);
                        $num_pos = $db->nf();
                        echo '<td class="soustitre2" class="centrer">';
                        if ($num_pos != 0)
                        {
                            $db->next_record();
                            if ($db->f("pos_cod") != $position_actuelle)
                            {
                                echo '<input type="radio" name="position" value="' . $db->f("pos_cod") . '" class="vide" ';
                                if ($db->f("pos_cod") == $position)
                                {
                                    echo("checked ");
                                }
                                echo(">");
                            } else
                            {
                                echo "$nom";

                            }
                        } else
                        {
                            echo '<input type="radio" name="position" class="vide" disabled>';
                        }
                        echo("</td>");
                    }
                    echo("</tr>");
                    }
                    ?>
                <tr>
                    <td colspan="4"><input type="submit" class="test centrer" value="Bouger !!"></td>
                </tr>
            </table>
        </form>
    </div>


    <?php
}
