<?php
$verif_connexion::verif_appel();
$type_perso = tag_perso($result['perso_pnj'], $result['perso_type_perso'], $result['perso_actif']);

echo "<tr>";
echo "<form name=\"login\" method=\"post\" action=\"index.php\">";
echo "<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">";
echo "<input type=\"hidden\" name=\"num_perso\" value=\"" . $result['perso_cod'] . "\">";
echo '<input type="hidden" name="idsessadm" value="$compt_cod">';
echo "<td class=\"soustitre2\"><p>$type_perso</p></td>";
echo "<td class=\"soustitre2\"><p><strong>" . $result['perso_nom'] . "</strong> (n° " . $result['perso_cod'] . ")</p></td>";
echo "<td class=\"soustitre2\"><p>" . $result['perso_px'] . " PX</p></td>";
echo "<td class=\"soustitre2\"><p>Niveau " . $result['perso_niveau'] . "</p></td>";
if ($visu_pos)
{
    echo "<td class=\"soustitre2\"><p>" . $result['pos_x'] . ", " . $result['pos_y'] . ", " . $result['pos_etage'] . " (" . $result['etage_libelle'] . ")</p></td>";

}
echo "<td class=\"soustitre2\"><p>Créé le " . $result['crea'] . "</p></td>";
echo "<td class=\"soustitre2\"><input type=\"submit\" value=\"Voir !\" class=\"test\"></td>";
echo "</form>";
echo "</tr>";
