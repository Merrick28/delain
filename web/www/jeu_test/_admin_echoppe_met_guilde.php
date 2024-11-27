<?php
$verif_connexion::verif_appel();

$guilde    = new guilde;
$allguilde = $guilde->getAll(); //echo "<pre>"; print_r($allguilde); die();

?>
<form name="guilde" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="methode" value="guilde">
    <input type="hidden" name="met_guilde" value="suite">
    <table>
        <tr>
            <td class="soustitre2"><strong>Id</strong></td>
            <td class="soustitre2"><strong>Nom</strong></td>
            <td class="soustitre2"><strong>Autorisée ?</strong></td>
            <td class="soustitre2"><strong>Refusée</strong></td>
        </tr>
        <?php
        foreach ($allguilde as $detailguilde)
        {
            echo "<tr>";
            echo "<td class=\"soustitre2\"><strong>", $detailguilde->guilde_cod, "</strong></td>";
            echo "<td class=\"soustitre2\"><strong>", $detailguilde->guilde_nom, "</strong></td>";

            if ($detailguilde->$champ == 'O')
            {
                $coche  = " checked";
                $ncoche = "";
            } else
            {
                $coche  = "";
                $ncoche = " checked";
            }
            echo "<td>";
            echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $detailguilde->guilde_cod . "]\" value=\"O\"", $coche, ">";
            echo "</td>";
            echo "<td>";
            echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $detailguilde->guilde_cod . "]\" value=\"N\"", $ncoche, ">";
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