<?php

function log_debug($textline)
{
    global $perso_cod;
    $dateaff  = date('d/m/Y H:i:s - u');
    $filename = '/home/delain/public_html/www/debug/debug.log'; // or whatever your path and filename
    if (!file_exists($filename))
    {
        echo '<!-- creation -->';
        if (touch($filename))
        {
            echo '<!-- creation OK -->';
        } else
        {
            echo '<!-- ECHEC SUR CREATION -->';
        }
    }
    chmod($filename, 0777);
    if (is_writable($filename))
    {

        // In our example we're opening $filename in append mode.
        // The file pointer is at the bottom of the file hence
        // that's where $somecontent will go when we fwrite() it.
        if (!$handle = fopen($filename, 'a'))
        {
            echo 'Cannot open file (', $filename, ')';
            exit;
        }

        // Write $somecontent to our opened file.
        if (fwrite($handle, $dateaff . ' - ' . $perso_cod . ' - ' . $textline . "\n") === FALSE)
        {
            echo 'Cannot write to file (', $filename, ')';
            exit;
        }

        //echo 'Success, wrote (' . $textline . ') to file (' . $filename . ')';

        fclose($handle);
    } else
    {
        echo 'The file ', $filename, ' is not writable';
    }
}


function distance($pos1, $pos2)
{
    $pdo          = new bddpdo();
    $req_distance = "select distance(:pos1,:pos2) as distance";
    $stmt         = $pdo->prepare($req_distance);
    $stmt         = $pdo->execute(array(
                                      ":pos1" => $pos1,
                                      ":pos2" => $pos2
                                  ), $stmt);
    $result       = $stmt->fetch();
    return $result['distance'];
}

function is_locked($perso_cod)
{
    $tmpperso = new perso;
    $tmpperso->charge($perso_cod);
    unset($tmpperso);
    return $tmpperso->is_locked();

}


function get_pos($perso_cod)
{
    $tmpperso = new perso;
    $tmpperso->charge($perso_cod);
    $tmppos         = $tmpperso->get_position();
    $tab['pos_cod'] = $tmppos['pos']->pos_cod;
    $tab['x']       = $tmppos['pos']->pos_x;
    $tab['y']       = $tmppos['pos']->pos_y;
    $tab['etage']   = $tmppos['pos']->pos_etage;
    return $tab;
}


function is_lieu($perso_cod)
{
    $tmpperso = new perso;
    $tmpperso->charge($perso_cod);
    return $tmpperso->is_lieu();
}


function get_lieu($perso_cod)
{
    $tmpperso = new perso;
    $tmpperso->charge($perso_cod);
    $lieu                    = $tmpperso->get_lieu();
    $tab_lieu['nom']         = $lieu['lieu']->lieu_nom;
    $tab_lieu['description'] = $lieu['lieu']->lieu_description;
    $tab_lieu['url']         = $lieu['lieu']->lieu_url;
    $tab_lieu['libelle']     = $lieu['lieu_type']->tlieu_libelle;
    return $tab_lieu;
}

function is_refuge($perso_cod)
{
    $tmpperso = new perso;
    $tmpperso->charge($perso_cod);
    return $tmpperso->is_refuge();
}

function get_etat($parm)
{
    $retour = 'Comme neuf';
    if ($parm < 90)
    {
        $retour = 'Excellent';
    }
    if ($parm < 70)
    {
        $retour = 'Bon';
    }
    if ($parm < 50)
    {
        $retour = 'Mauvais';
    }
    if ($parm < 35)
    {
        $retour = 'Médiocre';
    }
    if ($parm < 10)
    {
        $retour = 'Déplorable';
    }
    return $retour;
}

function genereClasse($table)
{
    $pdo    = new bddpdo;
    $bdtype = $pdo->returnType();
    // on prend tous les champs
    if ($bdtype == 'pgsql')
    {
        $req        = "select * from 
            information_schema.columns 
            where
            table_name= ?";
        $stmt       = $pdo->prepare($req);
        $stmt       = $pdo->execute(array($table), $stmt);
        $tempChamps = $stmt->fetchAll();
        // on calcule la clé primaire
        $req       = 'SELECT a.attname
            FROM   pg_index i
            JOIN   pg_attribute a ON a.attrelid = i.indrelid
                                 AND a.attnum = ANY(i.indkey)
            WHERE  i.indrelid = ?::regclass
            AND    i.indisprimary;';
        $stmt      = $pdo->prepare($req);
        $stmt      = $pdo->execute(array($table), $stmt);
        $result    = $stmt->fetch();
        $pk        = $result['attname'];
        $i         = 0;
        $champDate = array();
        foreach ($tempChamps as $key => $val)
        {

            if ($val['column_name'] != $pk)
            {
                $champsHorsPk[] = $val['column_name'];
            }
            $champs[$i]['name'] = $val['column_name'];
            $temp               = explode('::', $val['column_default']);
            if ($temp[0] == 'now()')
            {
                $temp[0]     = '';
                $champDate[] = $val['column_name'];
            }
            $champs[$i]['default'] = $temp[0];
            $i++;
        }
    }

    global $twig;

    $template = $twig->load('classes.twig');


    $options_twig = array(
        'TABLE'     => $table,
        'PK'        => $pk,
        'CHAMPS'    => $champs,
        'CONNECTOR' => 'bddpdo',
        'CONN_NAME' => 'pdo',
        'HORSPK'    => $champsHorsPk,
        'EOL'       => PHP_EOL,
        'BDTYPE'    => $bdtype,
        'CHAMPDATE' => $champDate
    );


    echo $template->render($options_twig);
}

function format_date($input)
{
    $date = new DateTime($input);
    return $date->format('d/m/Y H:i:s');
}

function getUserIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function niveau_blessures($pv, $pv_max)
{
    global $tab_blessures;
    $niveau_blessures = '';
    if ($pv / $pv_max < 0.75)
    {
        $niveau_blessures = ' - ' . $tab_blessures[0];
    }
    if ($pv / $pv_max < 0.5)
    {
        $niveau_blessures = ' - ' . $tab_blessures[1];
    }
    if ($pv / $pv_max < 0.25)
    {
        $niveau_blessures = ' - ' . $tab_blessures[2];
    }
    if ($pv / $pv_max < 0.15)
    {
        $niveau_blessures = ' - ' . $tab_blessures[3];
    }
    return $niveau_blessures;
}

function ligne_login_monstre($monstre)
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
    echo "<td class=\"soustitre2\"><p><a href=\"validation_login_monstre.php?numero=" . $monstre['perso_cod'] . "&compt_cod=" . $compt_cod . "\">" . $monstre['perso_nom'] . "</a></td>";
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