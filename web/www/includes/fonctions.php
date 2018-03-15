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
        }
        else
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
    }
    else
    {
        echo 'The file ', $filename, ' is not writable';
    }
}


function getparm_n($parm)
{
    /**
     * NE PLUS UTILISER !
     */
    $db       = new base_delain;
    $req_parm = "select parm_valeur from parametres where parm_cod = $parm";
    $db->query($req_parm);
    $nb_parm  = $db->nf();
    if ($nb_parm == 0)
    {
        $retour = -1;
        echo("Paramètre non fixé !!");
        return $retour;
    }
    else
    {
        $db->next_record();
        $retour = $db->f("parm_valeur");
        return $retour;
    }
}

function distance($pos1, $pos2)
{
    $db           = new base_delain;
    $req_distance = "select distance($pos1,$pos2) as distance";
    $db->query($req_distance);
    $db->next_record();
    $retour       = $db->f("distance");
    return $retour;
}

function getparm_t($parm)
{
    $db       = new base_delain;
    $req_parm = "select parm_valeur_texte from parametres where parm_cod = $parm";
    $db->query($req_parm);
    $nb_parm  = $db->nf();
    if ($nb_parm == 0)
    {
        $retour = -1;
        echo("Paramètre non fixé !!");
        return $retour;
    }
    else
    {
        $db->next_record();
        $retour = $db->f("parm_valeur_texte");
        return $retour;
    }
}

function existe_competence($perso_cod, $competence)
{
    $db       = new base_delain;
    $req_comp = "select count(*) as nombre from perso_competences where pcomp_perso_cod = $perso_cod and pcomp_pcomp_cod = $competence and pcomp_modificateur != 0";
    $db->query($req_comp);
    $db->next_record();
    $tab_comp = $db->f("nombre");
    if ($tab_comp == 1)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function is_locked($perso_cod)
{
    $db       = new base_delain;
    $req_lock = "select count(*) as nombre from lock_combat where lock_cible = $perso_cod";
    $db->query($req_lock);
    $db->next_record();
    $tab_lock = $db - f("nombre");
    if ($tab_lock != 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function is_identifie_objet($perso_cod, $obj_cod)
{
    $db       = new base_delain;
    $req_comp = "select count(*) as nombre from perso_identifie_objet where pio_perso_cod = $perso_cod and pio_obj_cod = $obj_cod ";
    $db->query($req_comp);
    $db->next_record();
    $tab_comp = $db->f("nombre");
    ;
    if ($tab_comp != 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function is_admin_guilde($perso_cod)
{
    $db       = new base_delain;
    $req_comp = "select count(*) as nombre from guilde_perso,guilde_rang where pguilde_perso_cod = $perso_cod ";
    $req_comp = $req_comp . "and pguilde_guilde_cod = rguilde_guilde_cod ";
    $req_comp = $req_comp . "and pguilde_rang_cod = rguilde_rang_cod ";
    $req_comp = $req_comp . "and rguilde_admin = 'O' ";
    $db->query($req_comp);
    $db->next_record();
    $tab_comp = $db->f("nombre");
    if ($tab_comp != 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function get_nom_guilde($perso)
{
    $db       = new base_delain;
    $req_comp = "select guilde_nom from guilde,guilde_perso where pguilde_perso_cod = $perso ";
    $req_comp = $req_comp . "and pguilde_valide = 'O' ";
    $req_comp = $req_comp . "and pguilde_guilde_cod = guilde_cod ";
    $db->query($req_comp);
    $nb_comp  = $db->nf();
    if ($nb_comp == 0)
    {
        return '';
    }
    else
    {
        $db->next_record();
        $tab_comp = $db->f("guilde_nom");
        return $tab_comp;
    }
}

function get_pos($perso_cod)
{
    $db             = new base_delain;
    $req            = "select pos_cod,pos_x,pos_y,pos_etage from positions,perso_position ";
    $req            = $req . "where ppos_perso_cod = $perso_cod ";
    $req            = $req . "and ppos_pos_cod = pos_cod ";
    $db->query($req);
    $db->next_record();
    $tab['pos_cod'] = $db->f("pos_cod");
    $tab['x']       = $db->f("pos_x");
    $tab['y']       = $db->f("pos_y");
    $tab['etage']   = $db->f("pos_etage");
    return $tab;
}

function nb_admin_guilde($guilde_cod)
{
    $db       = new base_delain;
    $req_comp = "select count(*) as nombre from guilde_perso,guilde_rang,perso where pguilde_guilde_cod = $guilde_cod ";
    $req_comp = $req_comp . "and pguilde_guilde_cod = rguilde_guilde_cod ";
    $req_comp = $req_comp . "and pguilde_rang_cod = rguilde_rang_cod ";
    $req_comp = $req_comp . "and rguilde_admin = 'O' ";
    $req_comp = $req_comp . "and pguilde_perso_cod = perso_cod ";
    $req_comp = $req_comp . "and perso_actif = 'O' ";
    $db->query($req_comp);
    $db->next_record();
    $tab_comp = $db->f("nombre");
    return $tab_comp;
}

function get_stats_guilde($guilde_cod)
{
    $db                      = new base_delain;
    $req_comp                = "select sum(perso_nb_joueur_tue) as joueur_tue,sum(perso_nb_monstre_tue) as monstre_tue,sum(perso_nb_mort) as nb_mort,get_renommee_guilde($guilde_cod) as renommee,get_karma_guilde($guilde_cod) as karma ";
    $req_comp                = $req_comp . "from guilde_perso,perso where pguilde_guilde_cod = $guilde_cod ";
    $req_comp                = $req_comp . "and pguilde_perso_cod = perso_cod ";
    $req_comp                = $req_comp . "and perso_type_perso = 1 ";
    $req_comp                = $req_comp . "and perso_actif = 'O' ";
    $db->query($req_comp);
    $db->next_record();
    $tab_comp['joueur_tue']  = $db->f("joueur_tue");
    $tab_comp['monstre_tue'] = $db->f("monstre_tue");
    $tab_comp['nb_mort']     = $db->f("nb_mort");
    $tab_comp['renommee']    = $db->f("renommee");
    $tab_comp['karma']       = $db->f("karma");
    return $tab_comp;
}

function get_pa_attaque($perso_cod)
{
    $db       = new base_delain;
    $req_comp = "select nb_pa_attaque($perso_cod) as pa";
    $db->query($req_comp);
    $db->next_record();
    $pa       = $db->f("pa");
    return $pa;
}

function get_pa_dep($perso_cod)
{
    $db       = new base_delain;
    $req_comp = "select get_pa_dep($perso_cod) as pa";
    $db->query($req_comp);
    $db->next_record();
    $pa       = $db->f("pa");
    return $pa;
}

function get_pa_foudre($perso_cod)
{
    $db       = new base_delain;
    $req_comp = "select nb_pa_foudre($perso_cod) as pa";
    $db->query($req_comp);
    $db->next_record();
    $pa       = $db->f("pa");
    return $pa;
}

function is_lieu($perso_cod)
{
    $db      = new base_delain;
    $req_pos = "select* from lieu_position,perso_position ";
    $req_pos = $req_pos . "where ppos_perso_cod = $perso_cod ";
    $req_pos = $req_pos . "and ppos_pos_cod = lpos_pos_cod ";
    $db->query($req_pos);
    $nb_pos  = $db->nf();
    if ($nb_pos == 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function is_temple($perso_cod)
{
    $db      = new base_delain;
    $req_pos = "select* from lieu_position,perso_position,lieu ";
    $req_pos = $req_pos . "where ppos_perso_cod = $perso_cod ";
    $req_pos = $req_pos . "and ppos_pos_cod = lpos_pos_cod ";
    $req_pos = $req_pos . "and lpos_lieu_cod = lieu_cod ";
    $req_pos = $req_pos . "and lieu_tlieu_cod = 2 ";
    $db->query($req_pos);
    $nb_pos  = $db->nf();
    if ($nb_pos == 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function init_temple($perso_cod)
{
    $db       = new base_delain;
    $tab_pos  = get_pos($perso_cod);
    $position = $tab_pos[pos_cod];
    $req_ins  = "delete from perso_temple where ptemple_perso_cod = $perso_cod ";
    $db->query($req_ins);
    $req_ins  = "insert into perso_temple (ptemple_perso_cod,ptemple_pos_cod) values ($perso_cod,$position)";
    $db->query($req_ins);
    return true;
}

function get_lieu($perso_cod)
{
    $db                      = new base_delain;
    $req_lieu                = "select lieu_nom,lieu_description,lieu_url,tlieu_libelle ";
    $req_lieu                = $req_lieu . "from lieu,lieu_type,lieu_position,perso_position ";
    $req_lieu                = $req_lieu . "where ppos_perso_cod = $perso_cod ";
    $req_lieu                = $req_lieu . "and ppos_pos_cod = lpos_pos_cod ";
    $req_lieu                = $req_lieu . "and lpos_lieu_cod = lieu_cod ";
    $req_lieu                = $req_lieu . "and lieu_tlieu_cod = tlieu_cod ";
    $db->query($req_lieu);
    $db->next_record();
    $tab_lieu['nom']         = $db->f("lieu_nom");
    $tab_lieu['description'] = $db->f("lieu_description");
    $tab_lieu['url']         = $db->f("lieu_url");
    $tab_lieu['libelle']     = $db->f("tlieu_libelle");
    return $tab_lieu;
}

function is_refuge($perso_cod)
{
    $db      = new base_delain;
    $req_pos = "select lieu_cod from lieu_position,perso_position,lieu ";
    $req_pos = $req_pos . "where ppos_perso_cod = $perso_cod ";
    $req_pos = $req_pos . "and ppos_pos_cod = lpos_pos_cod ";
    $req_pos = $req_pos . "and lpos_lieu_cod = lieu_cod ";
    $req_pos = $req_pos . "and lieu_refuge = 'O' ";
    $db->query($req_pos);
    $nb_pos  = $db->nf();
    if ($nb_pos == 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function has_artefact($perso_cod, $objet)
{
    $db      = new base_delain;
    $req_pos = "select count(*) as nombre from perso_objets where perobj_perso_cod = $perso_cod and perobj_obj_cod = $objet ";
    $db->query($req_pos);
    $tab_pos = $db->f("nombre");
    if ($tab_pos != 0)
    {
        return true;
    }
    else
    {
        return false;
    }
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
        $req        = 'SELECT a.attname
            FROM   pg_index i
            JOIN   pg_attribute a ON a.attrelid = i.indrelid
                                 AND a.attnum = ANY(i.indkey)
            WHERE  i.indrelid = ?::regclass
            AND    i.indisprimary;';
        $stmt       = $pdo->prepare($req);
        $stmt       = $pdo->execute(array($table), $stmt);
        $result     = $stmt->fetch();
        $pk         = $result['attname'];
        $i          = 0;
        $champDate = array();
        foreach ($tempChamps as $key => $val)
        {

            if ($val['column_name'] != $pk)
            {
                $champsHorsPk[] = $val['column_name'];
            }
            $champs[$i]['name']    = $val['column_name'];
            $temp = explode('::',$val['column_default']);
            if($temp[0] == 'now()')
            {
                $temp[0] = '';
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
