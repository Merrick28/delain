<?php
$verif_connexion::verif_appel();
while ($result = $stmt->fetch())
{
    echo "<tr><td class=\"soustitre2\"><p>" . $result['etage_libelle'] . "</p></td>
				<td><p>" . $result['joueur'] . "</td>
				<td><p>" . ($result['joueur'] != 0 ?
            round($result['jnv'] / $result['joueur'], 0) :
            0) . "</td>
				<td><p>" . ($result['carene_level_min'] != 0 ?
            $result['carene_level_min'] : 'Tous niveaux') . "</td>
				<td><p>" . ($result['carene_level_max'] != 0 ?
            $result['carene_level_max'] : 'Tous niveaux') . "</td></tr>";

}

echo("</table>");


echo "<form name=\"ea\" method=\"post\" action=" . $_SERVER['PHP_SELF'] . ">";
