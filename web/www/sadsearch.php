<?php
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xml");
$pdo = new bddpdo();
if (!empty($_REQUEST["foo"]))
{
    $_REQUEST["foo"] = utf8_encode(html_entity_decode($_REQUEST["foo"]));
    $req  =
        "select perso_nom,perso_cod from perso where perso_nom ilike :nom and perso_actif = 'O' and perso_type_perso = 1";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(":nom" => '%' . $_REQUEST['foo'] . '%'), $stmt);


    $i = 0;
    while ($result = $stmt->fetch())
    {
        $i++;
        $tmpxml .= "\n" . '<resultat titre="' . str_replace('"', "", $result['perso_nom']) . ' - (' . $result['perso_cod'] . ')" url="javascript:mettrevaleur(\'' . $result['perso_cod'] . '\')" />';
    }
    $xml = "<resultats nb=\"" . $i . "\">" . $tmpxml;

} else
{
    $xml = "<resultats nb=\"0\">";
}
$xml .= "\n</resultats>";
echo ($xml);

