<p>Les montants ci dessous sont des modificateurs à l'achat pour les guildes. Un modificateur négatif
    signifie
    une
    remise, un positif un surplus.<br>
    Les modificateurs doivent être compris entre -20 et 20.
<form name="guilde" method="post" action="<? echo PHP_SELF; ?>">
    <div class="centrer">
        <table>
            <input type="hidden" name="methode" value="suite">
            <?php
            $req  =
                "select guilde_cod,guilde_nom,guilde_modif,lower(guilde_nom) as minuscule from guilde order by minuscule";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\">", $result['guilde_nom'], "</td>";
                echo "<td><input type=\"text\" name=\"modif[", $result['guilde_cod'], "]\" value=\"", $result['guilde_modif'], "\"></td>";
                echo "</tr>";
            }
            ?>


        </table>
    </div>
    <input type="submit" class="test" value="Valider !"></div>
</form>