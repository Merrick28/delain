<?php
define('NO_DEBUG', true);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xml");
$pdo = new bddpdo();

$req     =
    "select creappro_gobj_cod,gobj_nom 
      from cachette_reappro,objet_generique 
      where creappro_gobj_cod = gobj_cod 
      and creappro_cache_liste_respawn = :valeur order by gobj_cod";
$stmt    = $pdo->prepare($req);
$stmt    = $pdo->execute(array(":valeur" => $_REQUEST['foo']), $stmt);
$allval  = $stmt->fetchAll();
$nb_tobj = 0;
$xml     = "";
$xml     = '<resultats nb="' . count($allval) . '">';
foreach($allval as $detail)
{
    $xml .= "\n" . '<resultat titre="' . str_replace('"', "", $detail['creappro_gobj_cod']) . ' - (' . $detail['gobj_nom'] . ')" url="javascript:supprimervaleur(\'' . $detail['creappro_gobj_cod'] . '\',\'' . $detail['gobj_nom'] . '\')" />';
}


$xml .= "\n</resultats>";
/*$xml .= "\n";*/
echo utf8_encode($xml);

