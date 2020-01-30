<?php
define('NO_DEBUG', true);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xml");
require "classes.php";
$db = new base_delain;

$perso    = new perso;
$allperso = $perso->getByNomLike($_REQUEST["foo"]);
$xml = '<resultats nb="' .  count($allperso) . '">';

foreach ($allperso as $detail_perso)
{
    $xml .= "\n" . '<resultat titre="' . str_replace('"', "", $perso->perso_nom) . ' - (' . $perso->perso_cod . ')" url="javascript:mettrevaleur(\'' . $perso->perso_cod . '\')" />';
}
$xml .= "\n</resultats>";
echo utf8_encode($xml);

