<?php
$verif_connexion::verif_appel();
$req_x  =
    "select distinct pos_x from positions where pos_x between ($x_actuel - $distance_vue) and ($x_actuel + $distance_vue) and pos_etage = $etage_actuel order by pos_x";
$stmt   = $pdo->query($req_x);
$result = $stmt->fetch();
$ssize  = ($distance_vue * 2 + 2) * 30;
// echo "<tr><td style=\"coord2\"><a href=\"javascript:parent.set('".$ssize.",*','".$ssize.",*');\" class=\"coord\"><img alt=\"Cliquez ici pour élargir la vue\" title=\"Cliquez ici pour élargir la vue\" src=\"../images/agrandir.gif\" border=\"0\"></a></td>";
echo "<tr><td class='coord'></td>";

$min_x = $result['pos_x'];
echo '<td class="coord">' . $result['pos_x'] . '</td>';
while ($result = $stmt->fetch())
{
    echo '<td class="coord">' . $result['pos_x'] . '</td>';
}
//echo '</tr>';
$req_y  =
    "select distinct pos_y from positions where pos_y between ($y_actuel - $distance_vue) and ($y_actuel + $distance_vue) and pos_etage = $etage_actuel order by pos_y desc";
$stmt   = $pdo->query($req_y);
$result = $stmt->fetch();
$min_y  = $result['pos_y'];

$y_encours = -2000;
