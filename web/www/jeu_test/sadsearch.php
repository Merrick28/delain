<?php
define('NO_DEBUG', true);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xml");

$perso    = new perso;
$allperso = $perso->getByNomLike($_REQUEST["foo"]);
$xml = '<resultats nb="' .  count($allperso) . '">';

foreach ($allperso as $detail_perso)
{
    $xml .= "\n" . '<resultat titre="' . str_replace('"', "", $detail_perso->perso_nom) . ' - (' . $detail_perso->perso_cod . ')" url="javascript:mettrevaleur(\'' . $detail_perso->perso_cod . '\')" />';
}
$xml .= "\n</resultats>";
echo utf8_encode($xml);

