<?php
$verif_connexion::verif_appel();
echo "<tr>";
echo "<td class=\"soustitre2\"><p><strong><a href=\"visu_desc_objet2.php?objet=" . $result['gobj_cod'] . "&origine=a\">" . $result['gobj_nom'] . "</a></strong></td>";
echo "<td class=\"soustitre2\"><p>" . $result['tobj_libelle'] . "</td>";
echo "<td class=\"soustitre2\"><p>" . $result['gobj_valeur'] . "</td>";
echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $result['persos'] . "</td>";
echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $result['sol'] . "</td>";
echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $result['echoppe'] . "</td>";
$total = $result['persos'] + $result['sol'] + $result['echoppe'];
echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $total . "</td>";
echo "<td><p><a href=\"voir_tarif_echoppe.php?methode=e1&objet=" . $result['gobj_cod'] . "\">Modifier !</a></td>";
echo "</tr>";