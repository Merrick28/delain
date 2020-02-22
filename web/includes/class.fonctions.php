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

    function ecrireResultatEtLoguer($texte, $sql = '')
    {
        global $compt_cod;
        $pdo = new bddpdo;

        if ($texte)
        {
            $log_sql = false;    // Mettre à true pour le debug des requêtes

            if (!$log_sql || $sql == '')
                $sql = "\n";
            else
                $sql = "\n\t\tRequête : $sql\n";

            $req       = "select compt_nom from compte where compt_cod = $compt_cod";
            $stmt      = $pdo->query($req);
            $result    = $stmt->fetch();
            $compt_nom = $result['compt_nom'];

            $en_tete = date("d/m/y - H:i") . "\tCompte $compt_nom ($compt_cod)\t";
            echo "<div style='padding:10px;'>$texte<pre>$sql</pre></div><hr />";
            writelog($en_tete . $texte . $sql, 'factions');
        }
    }

    function ecrireResultatEtLoguerLoguer($texte, $loguer, $sql = '')
    {
        global $pdo, $compt_cod;

        if ($texte)
        {
            $log_sql = false;    // Mettre à true pour le debug des requêtes

            if (!$log_sql || $sql == '')
                $sql = "\n";
            else
                $sql = "\n\t\tRequête : $sql\n";

            $req       = "select compt_nom from compte where compt_cod = $compt_cod";
            $stmt      = $pdo->query($req);
            $result    = $stmt->fetch();
            $compt_nom = $result['compt_nom'];

            $en_tete = date("d/m/y - H:i") . "\tCompte $compt_nom ($compt_cod)\t";
            echo "<div style='padding:10px;'>$texte<pre>$sql</pre></div><hr />";
            if ($loguer)
                writelog($en_tete . $texte . $sql, 'lieux_etages');
        }
    }
}