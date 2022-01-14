<?php
$verif_connexion::verif_appel();
?>
<p>Les montants ci dessous sont des modificateurs à l'achat pour les guildes. Un modificateur négatif
    signifie
    une
    remise, un positif un surplus.<br>
    Les modificateurs doivent être compris entre -20 et 20.
<form name="guilde" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
    <div class="centrer">
        <table>
            <input type="hidden" name="methode" value="suite">
            <?php

            $guilde    = new guilde;
            $allguilde = $guilde->getAll();
            foreach ($allguilde as $detailguilde)
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\">", $detailguilde['guilde_nom'], "</td>";
                echo "<td><input type=\"text\" name=\"modif[", $detailguilde['guilde_cod'], "]\" value=\"", $detailguilde['guilde_modif'], "\"></td>";
                echo "</tr>";
            }
            ?>


        </table>
    </div>
    <input type="submit" class="test" value="Valider !"></div>
</form>