<?php
$verif_connexion::verif_appel();
$stmt = $pdo->query($req);
echo "<table>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Type d'objet</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Valeur</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Persos/monstres</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Au sol</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Stock échoppes</strong></td>";
echo "<td class=\"soustitre2\"><p><strong>Total</strong></td>";
echo "<td></td>";
while ($result = $stmt->fetch())
{
    require G_CHE . "/web/www/jeu_test/blocks/_ligne_echoppe_1.php";
}
echo "</table>";