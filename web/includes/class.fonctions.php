<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 11/02/2020
 * Time: 11:35
 */

class fonctions
{
    function distance($pos1, $pos2)
    {
        $pdo          = new bddpdo;
        $req_distance = 'select distance(:pos1,:pos2) as distance';
        $stmt         = $pdo->prepare($req_distance);
        $stmt         = $pdo->execute(array(":pos1" => $pos1, ":pos2" => $pos2), $stmt);
        $result       = $stmt->fetch();
        return $result['distance'];
    }

    static function format($chaine, $apostrophes = true, $nl2br = true, $bloque_html = true)
    {
        if ($apostrophes)
        {
            $chaine = str_replace('\'', '’', $chaine);
        }
        if ($bloque_html)
        {
            $chaine = htmlspecialchars($chaine);
        }

        if ($nl2br)
        {
            $chaine = nl2br($chaine);
        }

        $chaine = pg_escape_string($chaine);
        return $chaine;
    }
}