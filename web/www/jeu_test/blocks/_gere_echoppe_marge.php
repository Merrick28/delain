<?php
$verif_connexion::verif_appel();
$req    = "select lieu_marge,lieu_prelev ";
$req    = $req . "from lieu ";
$req    = $req . "where lieu_cod = $mag ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
if ($v_ancienne_marge)
{
    $ancienne_marge = $result['lieu_marge'];
}

echo "<p>La marge actuelle est de " . $result['lieu_marge'] . " %.";
echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe4.php\">";
echo "<input type=\"hidden\" name=\"methode\" value=\"marge2\">";
echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
if ($v_ancienne_marge)
{
    echo "<input type=\"hidden\" name=\"ancienne_marge\" value=\"$ancienne_marge\">";
}
echo "<p>Mettre la marge à  <input type=\"text\" name=\"qte\" value=\"" . $result['lieu_marge'] . "\"> % ?";
echo "<p><em>nb : vous ne pouvez pas descendre la marge en dessous de " . $result['lieu_prelev'] . " %.</em>";

echo "<input type=\"submit\" class=\"test centrer\" value=\"Valider le changement ?\">";
echo "</form>";
