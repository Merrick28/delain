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

    function get_rumeur()
    {
        $pdo    = new bddpdo;
        $req    = "select choix_rumeur() as rumeur ";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        return $result['rumeur'];
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

    function ligne_login_monstre($monstre, $compt_cod)
    {
        $pdo = new bddpdo();
        if ($monstre['perso_dirige_admin'] == 'O')
        {
            $ia = "<strong>Hors IA</strong>";
        } else if ($monstre['perso_pnj'] == 1)
        {
            $ia = "<strong>PNJ</strong>";
        } else
        {
            $ia = "IA";
        }
        echo("<tr>");
        echo "<td class=\"soustitre2\"><p><a href=\"" . CHEMIN_COMPLET . "/validation_login_monstre.php?numero=" .
             $monstre['perso_cod'] . "&compt_cod=" . $compt_cod . "\">" . $monstre['perso_nom'] . "</a></td>";
        echo "<td class=\"soustitre2\"><p>" . $ia . "</td>";
        echo "<td class=\"soustitre2\"><p>", $monstre['perso_pa'], "</td>";
        echo "<td class=\"soustitre2\"><p>", $monstre['perso_pv'], " PV sur ", $monstre['perso_pv_max'];
        if ($monstre['etat'] != "indemne")
        {
            echo " - (<strong>", $monstre['etat'], "</strong>)";
        }
        echo "</td>";
        echo "<td class=\"soustitre2\"><p>";
        if ($monstre['messages'] != 0)
        {
            echo "<strong>";
        }
        echo $monstre['messages'] . " msg non lus.";
        if ($monstre['messages'] != 0)
        {
            echo "</strong>";
        }
        echo "</td>";
        echo "<td class=\"soustitre2\"><p>";
        if ($monstre['dlt_passee'] == 1)
        {
            echo("<strong>");
        }
        echo $monstre['dlt'];
        if ($monstre['dlt_passee'] == 1)
        {
            echo("</strong>");
        }
        echo "</td>";
        echo "<td class=\"soustitre2\"><p>X=", $monstre['pos_x'], ", Y=", $monstre['pos_y'], ", E=", $monstre['pos_etage'], "</td>";
        $req  =
            "select compt_nom from perso_compte,compte where pcompt_perso_cod = :monstre  and pcompt_compt_cod = compt_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":monstre" => $monstre['perso_cod']), $stmt);

        if ($result = $stmt->fetch())
        {
            echo "<td class=\"soustitre2\">Joué par <strong>", $result['compt_nom'], "</strong></td>";
        } else
        {
            echo "<td></td>";
        }


        echo("</tr>");
    }

    function format_date($input)
    {
        $date = new DateTime($input);
        return $date->format('d/m/Y H:i:s');
    }
}