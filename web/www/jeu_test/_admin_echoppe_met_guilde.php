<?php
$verif_connexion::verif_appel();

$req  =
    "select lower(guilde_nom) as minusc,guilde_nom,guilde_cod," . $champ . " from guilde order by minusc ";
$stmt = $pdo->query($req);

?>
<form name="guilde" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="methode" value="guilde">
    <input type="hidden" name="met_guilde" value="suite">
    <table>
        <tr>
            <td class="soustitre2"><strong>Nom</strong></td>
            <td class="soustitre2"><strong>Autorisée ?</strong></td>
            <td class="soustitre2"><strong>Refusée</strong></td>
        </tr>
        <?php
        while ($result = $stmt->fetch())
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><strong>", $result['guilde_nom'], "</strong></td>";

            if ($result[$champ] == 'O')
            {
                $coche  = " checked";
                $ncoche = "";
            } else
            {
                $coche  = "";
                $ncoche = " checked";
            }
            echo "<td>";
            echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $result['guilde_cod'] . "]\" value=\"O\"", $coche, ">";
            echo "</td>";
            echo "<td>";
            echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $result['guilde_cod'] . "]\" value=\"N\"", $ncoche, ">";
            echo "</td>";
            echo "</tr>";
        }
        ?>
        <tr>
            <td colspan="2">
                <div class="centrer"><input type="submit" class="test" value="Valider !"></div>
            </td>
        </tr>
    </table>