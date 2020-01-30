<?php
define('NO_DEBUG', true);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xml");

$compte = new compte;
$allcompte = $compte->getByNomLike($_REQUEST['foo']);


$xml = '<resultats nb="' . count($allcompte) . '">';

foreach ($allcompte as $detail_compte)
{
    $xml .= "\n" . '<resultat titre="' . str_replace('"', "", $detail_compte->compt_nom) . ' - (' . $detail_compte->compt_cod . ')" url="javascript:mettrevaleur2(\'' . $detail_compte->compt_cod . '\')" />';
}
$xml .= "\n</resultats>";
echo utf8_encode($xml);