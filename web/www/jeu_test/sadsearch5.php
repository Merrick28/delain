<?php
define('NO_DEBUG', true);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xml");
require_once G_CHE . "includes/classes.php";

if (!empty($_REQUEST["foo"]))
{
    $req  = "select mgroupe_perso_cod,perso_nom,mgroupe_statut from quetes.mission_groupe,perso 
     		where mgroupe_groupe_cod =" . (1 * $foo) . " 
     		and mgroupe_perso_cod = perso_cod
     		and mgroupe_statut != 'E'";
    $stmt = $pdo->query($req);
    $xml  = "<resultats nb=\"" . $stmt->rowCount() . "\">";
    if ($stmt->rowCount() != 0)
    {
        $xml .= "<ul>";
        /*$xml .= '<resultat titre="valeur=\'0\' title=\'Sélectionner le résultat désiré\' "/>';*/
        while ($result = $stmt->fetch())
        {
            $chef = '';
            if ($result['mgroupe_statut'] == 'O')
            {
                $chef = '- (chef)';
            }
            $xml .= '<resultat titre="' . str_replace('"', "", $result['perso_nom']) . '' . $chef . '" url="javascript:mettrevaleur2(\'' . $result['perso_cod'] . '\')" />';
        }
        $xml .= "</ul>";
    } else
    {
        $xml = "<resultats nb=\"0\">";
    }
    $xml .= "</resultats>";

    echo utf8_encode($xml);
}
