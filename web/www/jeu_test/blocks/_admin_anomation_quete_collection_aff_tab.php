<?php
$verif_connexion::verif_appel();
$txt_table = str_replace('#g1#', '', $txt_table);
$txt_table = str_replace('#g2#', '', $txt_table);
echo $txt_table;
while ($result = $stmt->fetch())
{
    $nom    = $result['perso_nom'];
    $nombre = $result['nombre'];
    echo "\n<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
}
echo $fin_table;
