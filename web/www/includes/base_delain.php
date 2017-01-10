<?php

class base_delain extends DB_Sql
{

    var $begin;
    var $commit;
    var $transaction  = true;
    var $classname    = 'base_delain';
    var $Database     = 'delain';
    var $parm_n_cache = array();
    var $parm_t_cache = array();

    function connect()
    {
        if (0 == $this->Link_ID)
        {
            if (SERVER_PROD)
            {
                $cstr = "dbname=" . $this->Database .
                   $this->ifadd($this->Host, "host=") .
                   $this->ifadd($this->Port, "port=") .
                   $this->ifadd($this->User, "user=") .
                   $this->ifadd($this->Password, "password=");
                $cstr = "service=delain";
            }
            else
            {
                $cstr = "dbname=" . SERVER_DBNAME .
                   $this->ifadd(SERVER_HOST, "host=") .
                   $this->ifadd($this->Port, "port=") .
                   $this->ifadd(SERVER_USERNAME, "user=") .
                   $this->ifadd(SERVER_PASSWORD, "password=");
            }
            /* if(!$this->PConnect) {
              $this->Link_ID = pg_connect($cstr);
              } else {
              $this->Link_ID = pg_pconnect($cstr);
              } */
            // modif par sd pour fiorcer les connections permanenentes
            $this->Link_ID = pg_pconnect($cstr);
            if (!$this->Link_ID)
            {
                $this->connect_failed();
            }
        }
    }

    function begin()
    {
        if ($this->transaction)
        {
            $this->begin = pg_exec($this->Link_ID, 'begin;');
        }
    }

    function commit()
    {
        if ($this->transaction)
        {
            $this->commit = pg_exec($this->Link_ID, 'commit;');
        }
    }

    function f($Name)
    {
        if (!is_array($this->Record))
            $this->log_message("db->f('$Name') appelé alors qu’il n’y a pas ou plus de résultats.");
        else if (!array_key_exists($Name, $this->Record))
            $this->log_message("Le champ « $Name » n’existe pas dans le résultat de la requête.");
        return $this->Record[$Name];
    }

    function log_message($msg)
    {
        global $auth;
        $ip        = getenv('REMOTE_ADDR');
        $message   = $msg;
        $requete   = $this->Errno;
        $libelle   = $this->Error;
        $texte_log = chr(10) . '--------' . chr(10) . '   ' . date('y-m-d H:i:s') . chr(10) . '   Page ' . $_SERVER['PHP_SELF'] . '
	IP : ' . $ip . '
	Message : [' . $message . ']
	Erreur : ' . $requete . '
	Libelle : [' . $libelle . ']
	Compte = ' . ((isset($auth)) ? $auth->compt_cod : '0') . '
	Perso = ' . ((isset($auth)) ? $auth->perso_cod : '0');

        $e         = new Exception;
        $pileAppel = chr(10) . '    Context : [' . chr(10) . $e->getTraceAsString() . '  ]';

        writelog_class_sql($texte_log . $pileAppel);

    }

    function query($Query_String)
    {
        /* No empty queries, please, since PHP4 chokes on them. */
        if ($Query_String == '')
        /* The empty query string is passed on from the constructor,
         * when calling the class without a query, e.g. in situations
         * like these: '$db = new DB_Sql_Subclass;'
         */
            return 0;
        /* Preventing from execution of 2 simultaneous queries (often used in
         * sql code injections).
         */
        $Query_String = str_replace(';', ' ', $Query_String);
        //$Query_String = str_replace('pg_tables',' ',$Query_String);
        /* $pos = strpos($Query_String,';');
          if ($pos != false)
          {
          $Query_String =str_replace(';',' ',$Query_String);
          }

          $pos = strpos($Query_String,'pg_tables');
          if ($pos != false)
          {
          $Query_String = substr($Query_String,0,$pos);
          } */
        $this->connect();

        if ($this->Debug)
            echo '<br>Debug: query = ', $Query_String, '<br>', chr(10);

        $this->Query_ID = pg_exec($this->Link_ID, $Query_String);
        $retour         = $this->Query_ID;
        $this->Row      = 0;

        $this->Error = pg_errormessage($this->Link_ID);
        $this->Errno = ($this->Error == '') ? 0 : 1;

        if (!$this->Query_ID)
        {
            $this->halt('Invalid SQL: #' . $Query_String . '#');
        }
        return $this->Query_ID;
    }

    function get_value($query, $field)
    {
        $result = false;
        $this->query($query);
        if ($this->next_record())
            $result = $this->f($field);
        return $result;
    }

    function get_one_record($query)
    {
        $this->query($query);
        return $this->next_record();
    }

    function halt($msg)
    {
        $ip      = getenv('REMOTE_ADDR');
        $message = $msg;
        $requete = $this->Errno;
        $libelle = $this->Error;

        $this->log_message($message);
        //writelog_class_sql($texte_log);
        echo '<p class="titre"><b>Erreur base de données:</b><br>Votre requète n’a pas pu être effectuée.</p>';

        $envoi = 1;
        if ($message == 'Invalid SQL: select compt_admin from compte where compt_cod =  ')
        {
            $envoi = 0;
        }
        //
        // mettre dans la ligne suivante les destinataires des mails d'erreur
        //
		$mail       = 'elodie.nietzsche@laposte.net';
        //
        // décommenter la ligne suivante pour les envois de mail
        //
		$envoi      = 0;
        /* $message = str_replace(';',str(127),$message);
          $requete = str_replace(';',str(127),$requete);
          $libelle = str_replace(';',str(127),$libelle); */
        $texte_mail = 'Erreur Delain :' . chr(13) . chr(10);
        $texte_mail = $texte_mail . 'message : *' . $message . '*' . chr(13) . chr(10);
        $texte_mail = $texte_mail . 'requete : *' . $requete . '*' . chr(13) . chr(10);
        $texte_mail = $texte_mail . 'libelle : *' . $libelle . '*' . chr(13) . chr(10);
        $texte_mail .= 'Page : ' . $_SERVER['PHP_SELF'] . '*' . chr(13) . chr(10);
        $texte_mail .= 'IP : ' . $ip . '*' . chr(13) . chr(10);
        $entete     = 'From: merrick@jdr-delain.net' . chr(13) . chr(10);
        $entete     = $entete . 'Reply-To: merrick@jdr-delain.net' . chr(13) . chr(10);
        $entete     = $entete . 'Error-To: merrick@jdr-delain.net' . chr(10);
        $sujet      = 'Erreur SQL Delain ' . chr(13) . chr(10);
        if ($envoi == 1)
        {
            $mail = 'elodie.nietzsche@laposte.net';
            if (mail($mail, $sujet, $texte_mail, $entete))
            {
                $ok = 1;
            }
            else
            {
                $ok = 0;
            }
        }
        die('Session arrêtée.');
    }

    static function format($chaine, $apostrophes = true, $nl2br = true, $bloque_html = true)
    {
        if ($apostrophes)
            $chaine = str_replace('\'', '’', $chaine);
        if ($bloque_html)
            $chaine = htmlspecialchars($chaine);
        if ($nl2br)
            $chaine = nl2br($chaine);
        $chaine = pg_escape_string($chaine);
        return $chaine;
    }

    function nb_obj_sur_case($perso_cod)
    {
        $req      = 'select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod;
        $this->query($req);
        $this->next_record();
        $position = $this->f('ppos_pos_cod');
        $req      = 'select pobj_cod as nombre from objet_position where pobj_pos_cod = ' . $position;
        $this->query($req);
        return $this->nf();
    }

    function nb_or_sur_case($perso_cod)
    {
        $req      = 'select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod;
        $this->query($req);
        $this->next_record();
        $position = $this->f('ppos_pos_cod');
        $req      = 'select por_cod from or_position where por_pos_cod = ' . $position;
        $this->query($req);
        return $this->nf();
    }

    function arme_distance($perso_cod)
    {
        $req_arme = 'select gobj_distance from objet_generique,objets,perso_objets ';
        $req_arme = $req_arme . 'where perobj_perso_cod = ' . $perso_cod;
        $req_arme = $req_arme . 'and perobj_equipe = \'O\' ';
        $req_arme = $req_arme . 'and perobj_obj_cod = obj_cod ';
        $req_arme = $req_arme . 'and obj_gobj_cod = gobj_cod ';
        $req_arme = $req_arme . 'and gobj_tobj_cod = 1 ';
        $this->query($req_arme);
        $nb_parm  = $this->nf();
        if ($nb_parm == 0)
        {
            return false;
        }
        else
        {
            $this->next_record();
            $retour = $this->f('gobj_distance');
            return $retour == 'O';
        }
    }

    /**
     * @deprecated
     * ATTENTION n'utilisez plus cette fonction pour les devs
     *
     * $param = new parametres;
     * $param->getparm(64);
     *
     * @param $parm
     * @param bool $utilise_cache
     * @return int|mixed|string
     */
    function getparm_n($parm, $utilise_cache = true)
    {
        $retour = -1;
        if ($utilise_cache && isset($this->parm_n_cache[$parm]))
        {
            // On a en cache le paramètre en question
            $retour = $this->parm_n_cache[$parm];
        }
        else
        {
            $req_parm = 'select parm_valeur from parametres where parm_cod = ' . $parm;
            $this->query($req_parm);
            if ($this->next_record())
            {
                $retour                    = $this->f('parm_valeur');
                $this->parm_n_cache[$parm] = $retour;
            }
            else
                $this->log_message("Paramètre « $parm » non fixé !!");
        }
        return $retour;
    }

    function getparm_t($parm, $utilise_cache = true)
    {
        $retour = -1;
        if ($utilise_cache && isset($this->parm_t_cache[$parm]))
        {
            // On a en cache le paramètre en question
            $retour = $this->parm_t_cache[$parm];
        }
        else
        {
            $req_parm = 'select parm_valeur_texte from parametres where parm_cod = ' . $parm;
            $this->query($req_parm);
            if ($this->next_record())
            {
                $retour                    = $this->f('parm_valeur_texte');
                $this->parm_t_cache[$parm] = $retour;
            }
            else
                $this->log_message("Paramètre « $parm » non fixé !!");
        }
        return $retour;
    }

    function is_milice($perso_cod)
    {
        $req_parm = 'select is_milice(' . $perso_cod . ') as resultat ';
        $this->query($req_parm);
        $this->next_record();
        return $this->f('resultat');
    }

    function compte_objet($perso_cod, $objet)
    {
        $req_obj = 'select count(perobj_cod) as nombre from perso_objets,objets ';
        $req_obj = $req_obj . 'where perobj_perso_cod = ' . $perso_cod;
        $req_obj = $req_obj . ' and perobj_obj_cod = obj_cod ';
        $req_obj = $req_obj . ' and obj_gobj_cod = ' . $objet;
        $this->query($req_obj);
        $this->next_record();
        $retour  = $this->f('nombre');
        return $retour;
    }

    function distance($pos1, $pos2)
    {
        $req_distance = 'select distance(' . $pos1 . ',' . $pos2 . ') as distance';
        $this->query($req_distance);
        $this->next_record();
        $retour       = $this->f('distance');
        return $retour;
    }

    function existe_competence($perso_cod, $competence)
    {
        $req_comp = 'select count(*) as nombre from perso_competences where pcomp_perso_cod = ' . $perso_cod . ' and pcomp_pcomp_cod = ' . $competence . ' and pcomp_modificateur != 0';
        $this->query($req_comp);
        $this->next_record();
        $tab_comp = $this->f('nombre');
        return $tab_comp == 1;
    }

    function is_enchanteur($perso_cod)
    {
        $enchanteur1 = $this->existe_competence($perso_cod, '88');
        $enchanteur2 = $this->existe_competence($perso_cod, '102');
        $enchanteur3 = $this->existe_competence($perso_cod, '103');
        return ($enchanteur1 || $enchanteur2 || $enchanteur3);
    }

    function is_enlumineur($perso_cod)
    {
        $enchanteur1 = $this->existe_competence($perso_cod, '91');
        $enchanteur2 = $this->existe_competence($perso_cod, '92');
        $enchanteur3 = $this->existe_competence($perso_cod, '93');
        return ($enchanteur1 || $enchanteur2 || $enchanteur3);
    }

    function is_locked($perso_cod)
    {
        $retour   = false;
        $req_lock = 'select count(lock_cod) as nombre from lock_combat where lock_cible = ' . $perso_cod;
        $this->query($req_lock);
        $this->next_record();
        $tab_lock = $this->f('nombre');
        if ($tab_lock != 0)
        {
            $retour = true;
        }
        $req_lock = 'select count(lock_cod) as nombre from lock_combat where lock_attaquant = ' . $perso_cod;
        $this->query($req_lock);
        $this->next_record();
        $tab_lock = $this->f('nombre');
        if ($tab_lock != 0)
        {
            $retour = true;
        }
        return $retour;
    }

    function is_identifie_objet($perso_cod, $v_objet)
    {
        $req_comp = 'select pio_perso_cod as test from perso_identifie_objet where pio_perso_cod = ' . $perso_cod . ' and pio_obj_cod = ' . $v_objet . ' limit 1';
        $this->query($req_comp);
        return $this->nf() > 0;
    }

    function is_admin_guilde($perso_cod)
    {
        $req_comp = 'select count(*) as nombre from guilde_perso,guilde_rang where pguilde_perso_cod = ' . $perso_cod;
        $req_comp = $req_comp . 'and pguilde_guilde_cod = rguilde_guilde_cod ';
        $req_comp = $req_comp . 'and pguilde_rang_cod = rguilde_rang_cod ';
        $req_comp = $req_comp . 'and rguilde_admin = \'O\' ';
        $this->query($req_comp);
        $this->next_record();
        return $this->f('nombre') > 0;
    }

    function auth_mess($perso_cod, $mid)
    {
        $retour = false;
        $req    = 'select dmsg_cod from messages_dest where dmsg_msg_cod = ' . $mid . ' and dmsg_perso_cod = ' . $perso_cod;
        $this->query($req);
        if ($this->nf() != 0)
            $retour = true;
        $req    = 'select emsg_cod from messages_exp where emsg_msg_cod = ' . $mid . ' and emsg_perso_cod = ' . $perso_cod;
        $this->query($req);
        if ($this->nf() != 0)
            $retour = true;
        return $retour;
    }

    function is_in_guilde($perso_cod)
    {
        $req_comp = 'select count(*) as nombre from guilde_perso,guilde_rang where pguilde_perso_cod = ' . $perso_cod;
        $req_comp = $req_comp . 'and pguilde_guilde_cod = rguilde_guilde_cod ';
        $req_comp = $req_comp . 'and pguilde_rang_cod = rguilde_rang_cod ';
        $req_comp = $req_comp . 'and pguilde_valide = \'O\' ';
        $this->query($req_comp);
        $this->next_record();
        return $this->f('nombre') > 0;
    }

    function get_nom_guilde($perso)
    {
        $req_comp = 'select guilde_nom from guilde,guilde_perso where pguilde_perso_cod = ' . $perso;
        $req_comp = $req_comp . 'and pguilde_valide = \'O\' ';
        $req_comp = $req_comp . 'and pguilde_guilde_cod = guilde_cod ';
        $this->query($req_comp);
        $nb_comp  = $this->nf();
        if ($nb_comp == 0)
        {
            return '';
        }
        else
        {
            $this->next_record();
            $tab_comp = $this->f('guilde_nom');
            return $tab_comp;
        }
    }

    function get_pos($perso_cod)
    {
        $req                    = 'select pos_cod,pos_x,pos_y,pos_etage,etage_libelle,etage_reference from positions,perso_position,etage ';
        $req                    = $req . 'where ppos_perso_cod = ' . $perso_cod;
        $req                    = $req . 'and ppos_pos_cod = pos_cod ';
        $req                    = $req . 'and etage_numero = pos_etage';
        $this->query($req);
        $this->next_record();
        $tab['pos_cod']         = $this->f('pos_cod');
        $tab['x']               = $this->f('pos_x');
        $tab['y']               = $this->f('pos_y');
        $tab['etage']           = $this->f('pos_etage');
        $tab['etage_libelle']   = $this->f('etage_libelle');
        $tab['etage_reference'] = $this->f('etage_reference');
        return $tab;
    }

    function nb_admin_guilde($guilde_cod)
    {
        $req_comp = 'select count(*) as nombre from guilde_perso,guilde_rang,perso where pguilde_guilde_cod = ' . $guilde_cod;
        $req_comp = $req_comp . 'and pguilde_guilde_cod = rguilde_guilde_cod ';
        $req_comp = $req_comp . 'and pguilde_rang_cod = rguilde_rang_cod ';
        $req_comp = $req_comp . 'and rguilde_admin = \'O\' ';
        $req_comp = $req_comp . 'and pguilde_perso_cod = perso_cod ';
        $req_comp = $req_comp . 'and perso_actif = \'O\' ';
        $this->query($req_comp);
        $this->next_record();
        $tab_comp = $this->f('nombre');
        return $tab_comp;
    }

    function get_stats_guilde($guilde_cod)
    {
        $req_comp                = 'select sum(perso_nb_joueur_tue) as joueur_tue,sum(perso_nb_monstre_tue) as monstre_tue,sum(perso_nb_mort) as nb_mort,get_renommee_guilde(' . $guilde_cod . ') as renommee,get_karma_guilde(' . $guilde_cod . ') as karma ';
        $req_comp                = $req_comp . 'from guilde_perso,perso where pguilde_guilde_cod = ' . $guilde_cod;
        $req_comp                = $req_comp . 'and pguilde_perso_cod = perso_cod ';
        //	$req_comp = $req_comp . 'and perso_type_perso = 1 ';
        $req_comp                = $req_comp . 'and perso_actif != \'N\' ';
        $req_comp                = $req_comp . 'and pguilde_valide = \'O\' ';
        $this->query($req_comp);
        $this->next_record();
        $tab_comp['joueur_tue']  = $this->f('joueur_tue');
        $tab_comp['monstre_tue'] = $this->f('monstre_tue');
        $tab_comp['nb_mort']     = $this->f('nb_mort');
        $tab_comp['renommee']    = $this->f('renommee');
        $tab_comp['karma']       = $this->f('karma');
        return $tab_comp;
    }

    function get_pa_attaque($perso_cod)
    {
        $req_comp = 'select nb_pa_attaque(' . $perso_cod . ') as pa';
        $this->query($req_comp);
        $this->next_record();
        $pa       = $this->f('pa');
        return $pa;
    }

    function get_pa_dep($perso_cod)
    {
        $req_comp = 'select get_pa_dep(' . $perso_cod . ') as pa';
        $this->query($req_comp);
        $this->next_record();
        $pa       = $this->f('pa');
        return $pa;
    }

    function get_pa_foudre($perso_cod)
    {
        $req_comp = 'select nb_pa_foudre(' . $perso_cod . ') as pa';
        $this->query($req_comp);
        $this->next_record();
        $pa       = $this->f('pa');
        return $pa;
    }

    function is_lieu($perso_cod)
    {
        $req_pos = 'select * from lieu_position,perso_position ';
        $req_pos = $req_pos . 'where ppos_perso_cod = ' . $perso_cod;
        $req_pos = $req_pos . 'and ppos_pos_cod = lpos_pos_cod ';
        $this->query($req_pos);
        $nb_pos  = $this->nf();

        return $nb_pos != 0;
    }

    function is_perso_quete($perso_cod)
    {
        $req_quete = 'select count(perso_cod) as nombre from perso,perso_position
			where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod . ')
				and perso_quete in (\'quete_ratier.php\',\'enchanteur.php\',\'quete_alchimiste.php\',\'quete_chasseur.php\',\'quete_dispensaire.php\',\'quete_dame_cygne.php\',\'quete_forgeron.php\',\'quete_groquik.php\')
				and perso_cod = ppos_perso_cod';
        $this->query($req_quete);
        $this->next_record();
        $nb_pos    = $this->f("nombre");

        return $nb_pos != 0;
    }

    function get_perso_quete($perso_cod)
    {
        $req_quete = 'select perso_quete,perso_cod from perso,perso_position
			where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod . ')
				and perso_quete in (\'quete_ratier.php\',\'enchanteur.php\',\'quete_alchimiste.php\',\'quete_chasseur.php\',\'quete_dispensaire.php\',\'quete_dame_cygne.php\',\'quete_forgeron.php\',\'quete_groquik.php\')
				and perso_cod = ppos_perso_cod
			order by perso_quete';
        $this->query($req_quete);
        $tab_quete = array();
        while ($this->next_record())
        {
            $perso             = $this->f("perso_cod");
            $tab_quete[$perso] = $this->f("perso_quete");
        }
        return $tab_quete;
    }

    function is_temple($perso_cod)
    {
        $req_pos = 'select* from lieu_position,perso_position,lieu ';
        $req_pos = $req_pos . 'where ppos_perso_cod = ' . $perso_cod;
        $req_pos = $req_pos . 'and ppos_pos_cod = lpos_pos_cod ';
        $req_pos = $req_pos . 'and lpos_lieu_cod = lieu_cod ';
        $req_pos = $req_pos . 'and lieu_tlieu_cod = 2 ';
        $this->query($req_pos);
        $nb_pos  = $this->nf();

        return $nb_pos != 0;
    }

    function init_temple($perso_cod)
    {
        $tab_pos  = get_pos($perso_cod);
        $position = $tab_pos[pos_cod];
        $req_ins  = 'delete from perso_temple where ptemple_perso_cod = ' . $perso_cod;
        $this->query($req_ins);
        $req_ins  = 'insert into perso_temple (ptemple_perso_cod,ptemple_pos_cod) values (' . $perso_cod . ',' . $position . ')';
        $this->query($req_ins);
        return true;
    }

    function is_intangible($perso_cod)
    {
        $req = 'select perso_tangible from perso where perso_cod = ' . $perso_cod;
        $this->query($req);
        $this->next_record();

        return $this->f('perso_tangible') != 'O';
    }

    function get_lieu($perso_cod)
    {
        // Lieu standard
        $tab_lieu['nom']         = "";
        $tab_lieu['description'] = "";
        $tab_lieu['url']         = "";
        $tab_lieu['libelle']     = "";
        $tab_lieu['type_lieu']   = "";
        $tab_lieu['position']    = "";
        $tab_lieu['lieu_cod']    = "";
        $tab_lieu['pos_cod']     = "";
        $tab_lieu['lieu_refuge'] = "";
        $tab_lieu['lieu_prelev'] = "";
        $tab_lieu['evo_niveau']  = "";

        $req_lieu = 'select lieu_nom,lieu_description,lieu_url,tlieu_libelle,tlieu_cod,ppos_pos_cod,lieu_cod,lpos_pos_cod,lieu_refuge,lieu_prelev,lieu_levo_niveau ';
        $req_lieu = $req_lieu . 'from lieu,lieu_type,lieu_position,perso_position ';
        $req_lieu = $req_lieu . 'where ppos_perso_cod = ' . $perso_cod;
        $req_lieu = $req_lieu . 'and ppos_pos_cod = lpos_pos_cod ';
        $req_lieu = $req_lieu . 'and lpos_lieu_cod = lieu_cod ';
        $req_lieu = $req_lieu . 'and lieu_tlieu_cod = tlieu_cod ';
        $this->query($req_lieu);
        if ($this->next_record())
        {
            $tab_lieu['nom']         = $this->f('lieu_nom');
            $tab_lieu['description'] = $this->f('lieu_description');
            $tab_lieu['url']         = $this->f('lieu_url');
            $tab_lieu['libelle']     = $this->f('tlieu_libelle');
            $tab_lieu['type_lieu']   = $this->f('tlieu_cod');
            $tab_lieu['position']    = $this->f('ppos_pos_cod');
            $tab_lieu['lieu_cod']    = $this->f('lieu_cod');
            $tab_lieu['pos_cod']     = $this->f('lpos_pos_cod');
            $tab_lieu['lieu_refuge'] = $this->f('lieu_refuge');
            $tab_lieu['lieu_prelev'] = $this->f('lieu_prelev');
            $tab_lieu['evo_niveau']  = $this->f('lieu_levo_niveau');
            // Lieu avancé
            if (!empty($tab_lieu['type_lieu']))
            {
                $req_evo_lieu = 'SELECT levo_libelle, levo_url, levo_override 
					FROM lieu_evolution WHERE levo_tlieu_cod=' . $tab_lieu['type_lieu'] . ' 
						AND levo_niveau=' . $tab_lieu['evo_niveau'];
                $this->query($req_evo_lieu);
                if ($this->next_record())
                {
                    $tab_lieu['evo_override'] = $this->f('levo_override');
                    if ($tab_lieu['evo_override'] == 'O')
                    {
                        $tab_lieu['ini_libelle'] = $tab_lieu['libelle'];
                        $tab_lieu['ini_url']     = $tab_lieu['url'];
                        $tab_lieu['libelle']     = $this->f('levo_libelle');
                        $tab_lieu['url']         = $this->f('levo_url');
                    }
                    else
                    {
                        $tab_lieu['evo_libelle'] = $this->f('levo_libelle');
                        $tab_lieu['evo_url']     = $this->f('levo_url');
                    }
                }
            }
        }
        // Retour
        return $tab_lieu;
    }

    function is_refuge($perso_cod)
    {
        $req_pos = 'select lieu_cod from lieu_position,perso_position,lieu ';
        $req_pos = $req_pos . 'where ppos_perso_cod = ' . $perso_cod;
        $req_pos = $req_pos . 'and ppos_pos_cod = lpos_pos_cod ';
        $req_pos = $req_pos . 'and lpos_lieu_cod = lieu_cod ';
        $req_pos = $req_pos . 'and lieu_refuge = \'O\' ';
        $this->query($req_pos);
        $nb_pos  = $this->nf();

        return $nb_pos != 0;
    }

    function has_artefact($perso_cod, $objet)
    {
        $req_pos = 'select count(*) as nombre from perso_objets where perobj_perso_cod = ' . $perso_cod . ' and perobj_obj_cod = ' . $objet;
        $this->query($req_pos);
        $tab_pos = $this->f('nombre');

        return $tab_pos != 0;
    }

    function is_bernardo($perso_cod)
    {
        $req_pos = 'select valeur_bonus(' . $perso_cod . ' , \'BER\') as nombre';
        $this->query($req_pos);
        $this->next_record();

        return $this->f('nombre') != 0;
    }

    function is_revolution($guilde_cod)
    {
        $req = 'select revguilde_cod from guilde_revolution where revguilde_guilde_cod = ' . $guilde_cod;
        $this->query($req);

        return $this->nf() != 0;
    }

    function is_admin_monstre($compt_cod)
    {
        $req      = 'select compt_monstre from compte where compt_cod = ' . $compt_cod;
        $this->query($req);
        $this->next_record();
        $resultat = $this->f('compt_monstre');

        return $resultat == 'O';
    }

    function is_admin($compt_cod)
    {
        $req      = 'select compt_admin from compte where compt_cod = ' . $compt_cod;
        $this->query($req);
        $this->next_record();
        $resultat = $this->f('compt_admin');

        return $resultat == 'O';
    }

    function is_monstre($perso_cod)
    {
        $req      = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
        $this->query($req);
        $this->next_record();
        $resultat = $this->f('perso_type_perso');
        return ($resultat == 2);
    }

    function is_pnj($perso_cod)
    {
        $req      = 'select perso_pnj from perso where perso_cod = ' . $perso_cod;
        $this->query($req);
        $this->next_record();
        $resultat = $this->f('perso_pnj');
        return ($resultat == 1);
    }

    function is_fam($perso_cod)
    {
        $req      = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
        $this->query($req);
        $this->next_record();
        $resultat = $this->f('perso_type_perso');
        return ($resultat == 3);
    }

    function mess_chef_coterie($titre, $corps, $coterie, $perso_cod)
    {
        $req = 'select groupe_chef from groupe, perso where groupe_cod = ' . $coterie . ' and groupe_chef = perso_cod';
        $this->query($req);
        if ($this->next_record())
        {
            $chef            = $this->f('groupe_chef');
            $msg             = new message();
            $msg->corps      = $corps;
            $msg->sujet      = $titre;
            $msg->expediteur = $perso_cod;
            $msg->ajouteDestinataire($chef);
            $msg->envoieMessage();
        }
        return 'ok';
    }

    function mess_all_coterie($titre, $corps, $coterie, $perso_cod)
    {
        $titre   = pg_escape_string($titre);
        $corps   = pg_escape_string($corps);
        $req     = 'select groupe_chef from groupe where groupe_cod = ' . $coterie;
        $this->query($req);
        $this->next_record();
        $chef    = $this->f('groupe_chef');
        $this->query('select nextval(\'seq_msg_cod\') as num_mes');
        $this->next_record();
        $num_mes = $this->f('num_mes');
        $req_mes = 'insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
			values (' . $num_mes . ', now(), e\'' . $titre . '\', e\'' . $corps . '\', now()) ';
        $this->query($req_mes);
        // on renseigne l'expéditeur
        $req2    = 'insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)
			values (' . $num_mes . ',' . $chef . ',\'N\') ';
        $this->query($req2);
        $req2    = 'insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
			select ' . $num_mes . ',pgroupe_perso_cod,\'N\',\'N\'
			from groupe_perso
			where pgroupe_groupe_cod = ' . $coterie . '
			and pgroupe_statut =  1
			and pgroupe_messages = 1';
        $this->query($req2);
        return 'ok';
    }

    // Récupère la liste des missions relevées par un perso.
    // Possibilité de préciser si on ne veut que celles non terminées (échouées ou réussies), et si on se retreint à une seule faction.
    function missions_du_perso($perso_cod, $fac_cod = -1, $inclure_anciennes = FALSE, $tri = 'statut')
    {
        $resultat    = array();
        $critere_tri = 'mpf_statut desc';
        switch ($tri)
        {
            case 'faction': $critere_tri = 'fac_nom';
                break;
            case 'statut': $critere_tri = 'mpf_statut desc';
                break;
            case 'date': $critere_tri = 'mpf_date_debut desc';
                break;
            default: break;
        }

        $req = "SELECT miss_nom, fac_nom, mpf_fac_cod, mission_texte(mpf_cod) as libelle,
				to_char(mpf_date_debut, 'DD/MM/YYYY') as mpf_date_debut,
				to_char(mpf_date_fin, 'DD/MM/YYYY') as mpf_date_fin,
				mpf_obj_cod, mpf_pos_cod, mpf_gobj_cod, mpf_cible_perso_cod, mpf_nombre, mpf_gmon_cod,
				miss_fonction_init, miss_fonction_valide, miss_fonction_releve, mpf_statut,
				mpf_cod, mpf_texte, mpf_delai, mpf_recompense
			FROM mission_perso_faction_lieu
			INNER JOIN factions ON fac_cod = mpf_fac_cod
			INNER JOIN missions ON miss_cod = mpf_miss_cod
			WHERE mpf_perso_cod = $perso_cod ";

        if (!$inclure_anciennes)    // Statut ni validé, ni échoué
            $req .= ' AND mpf_statut < 40';

        if ($fac_cod != -1)
            $req .= " AND mpf_fac_cod = $fac_cod";

        $req .= ' ORDER BY ' . $critere_tri;

        $this->query($req);
        while ($this->next_record())
        {
            $uneMission               = array();
            $uneMission['Code']       = $this->f('mpf_cod');
            $uneMission['Nom']        = $this->f('miss_nom');
            $uneMission['Faction']    = $this->f('fac_nom');
            $uneMission['FactionCod'] = $this->f('mpf_fac_cod');
            $uneMission['Libellé']    = $this->f('libelle');

            $uneMission['DateDébut']   = $this->f('mpf_date_debut');
            $uneMission['DateFin']     = $this->f('mpf_date_fin');
            $uneMission['Statut']      = $this->f('mpf_statut');
            $uneMission['Objet']       = $this->f('mpf_obj_cod');
            $uneMission['Position']    = $this->f('mpf_pos_cod');
            $uneMission['PersoCible']  = $this->f('mpf_cible_perso_cod');
            $uneMission['TypeObjet']   = $this->f('mpf_gobj_cod');
            $uneMission['TypeMonstre'] = $this->f('mpf_gmon_cod');
            $uneMission['Quantité']    = $this->f('mpf_nombre');
            $uneMission['FctInit']     = $this->f('miss_fonction_init');
            $uneMission['FctValide']   = $this->f('miss_fonction_valide');
            $uneMission['FctReleve']   = $this->f('miss_fonction_releve');
            $uneMission['Texte']       = $this->f('mpf_texte');
            $uneMission['Délai']       = $this->f('mpf_delai');
            $uneMission['Récompense']  = $this->f('mpf_recompense');

            $statut                          = $this->f('mpf_statut');
            $uneMission['MissionPassee']     = $statut >= 40;
            $uneMission['Relevée']           = $statut > 0;
            $uneMission['EnCours']           = $statut >= 10 && $statut < 20;
            $uneMission['Réussie']           = $statut == 20;
            $uneMission['Ratée']             = $statut >= 30 && $statut < 40;
            $uneMission['Validée']           = $statut == 40;
            $uneMission['Échouée']           = $statut >= 50;
            $uneMission['ÀValider']          = $statut >= 20 && $statut < 40;
            $uneMission['RéussitePartielle'] = $statut % 10 > 0;
            $resultat[]                      = $uneMission;
        }
        return $resultat;
    }

    /**
     *
     * Fonction get_image_default_equipement
     *
     * Retourne le nom de l'image 'ombre' relative à la compétence et au type de l'équipement
     *
     *
     * @var $comp: compétence de l'équipement
     * @var $type: type de l'équipement
     *
     * @return $img:  nom de l'image
     * */
    function get_image_default_equipement($comp, $type)
    {
        $img  = "croix.png";
        $req2 = "SELECT img_image FROM image_objet_generique WHERE img_comp_cod = " . $comp . " AND img_tobj_cod = " . $type;
        //echo $req2;

        $this->query($req2);
        if ($this->next_record())
            $img = $this->f('img_image');
        else
            $img = "croix.png";
        return $img;
    }
    function verification_vote($code_compte)
    {
        $req ="select count(*) as nombre
                from compte_vote_ip 
                where compte_vote_ip.compte_vote_compte_cod = ".$code_compte."
		and compte_vote_date = current_date;";

         $this->query($req);
         $this->next_record();
         $nbr = $this->f('nombre');
         if($nbr==0)
         {
             return true;
         }
         
        return false;
    }
    

}


function writelog_class_sql($textline,$namefile = 'sql.log')
{
    $filename = G_CHE . '/debug/' . $namefile; // or whatever your path and filename
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
        if (fwrite($handle, $textline) === FALSE)
        {
            echo 'Cannot write to file (', $filename, ')';
            exit;
        }

        fclose($handle);
    }
    else
    {
        echo 'The file ', $filename, ' is not writable';
    }
}

