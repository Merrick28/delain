<?php 
//ini_set('zlib.output_compression','1');
//die ('Maintenance, merci de patienter');

function writelog_class_sql($textline)
{
	$filename='/home/sdewitte/public_html/debug/sql.log'; // or whatever your path and filename
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
	chmod($filename,0777);
	if (is_writable($filename))
	{

   // In our example we're opening $filename in append mode.
   // The file pointer is at the bottom of the file hence
   // that's where $somecontent will go when we fwrite() it.
   if (!$handle = fopen($filename, 'a'))
   {
         echo 'Cannot open file (' , $filename , ')';
         exit;
   }

   // Write $somecontent to our opened file.
   if (fwrite($handle, $textline) === FALSE)
   {
       echo 'Cannot write to file (' , $filename , ')';
       exit;
   }

   //echo 'Success, wrote (' . $textline . ') to file (' . $filename . ')';

   fclose($handle);

	}
	else
	{
	   echo 'The file ' , $filename , ' is not writable';
	}
}


class base_delain extends DB_Sql {
var $begin;
var $commit;
var $transaction = true;
var $classname = 'base_delain';
var $Database = 'sdewitte';

function connect() {
    if ( 0 == $this->Link_ID ) {
      $cstr = "dbname=".$this->Database.
      $this->ifadd($this->Host, "host=").
      $this->ifadd($this->Port, "port=").
      $this->ifadd($this->User, "user=").
      $this->ifadd($this->Password, "password=");
      $cstr="service=delain";
      if(!$this->PConnect) {
        $this->Link_ID = pg_connect($cstr);
      } else {
        $this->Link_ID = pg_pconnect($cstr);
      }
      if (!$this->Link_ID) {
        $this->connect_failed();
      }
    }
  }


function begin() {
	if ($this->transaction)
   {
		$this->begin = pg_exec($this->Link_ID, 'begin;');
	}
}
function commit() {
	if ($this->transaction)
   {
		$this->commit = pg_exec($this->Link_ID, 'commit;');
	}
}

function query($Query_String) {
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
      $Query_String = str_replace(';',' ',$Query_String);
      //$Query_String = str_replace('pg_tables',' ',$Query_String);
      /*$pos = strpos($Query_String,';');
     	if ($pos != false)
     	{
     		$Query_String =str_replace(';',' ',$Query_String);
     	}

		$pos = strpos($Query_String,'pg_tables');
		if ($pos != false)
     	{
     		$Query_String = substr($Query_String,0,$pos);
     	}*/
    $this->connect();

    if ($this->Debug)
      echo '<br>Debug: query = ' , $Query_String , '<br>' , chr(10);

    $this->Query_ID = pg_exec($this->Link_ID, $Query_String);
    $retour = $this->Query_ID;
    $this->Row   = 0;

    $this->Error = pg_errormessage($this->Link_ID);
    $this->Errno = ($this->Error == '')?0:1;

    if (!$this->Query_ID) {
    	$this->halt('Invalid SQL: #'.$Query_String.'#');
    }
    return $this->Query_ID;
  }


  function halt($msg) {
  	$ip = getenv('REMOTE_ADDR');
  	$message = $msg;
  	$requete = $this->Errno;
  	$libelle = $this->Error;
  	$texte_log = chr(10) . '--------' . chr(10) . date('d/m/Y H:i:s') . chr(10) . 'Erreur SQL sur page ' . $_SERVER['PHP_SELF'] . '
IP : ' . $ip . '
Message : ' . $message . '
Numéro d\'erreur : ' . $requete . '
Libelle erreur : ' . $libelle . '
Compte = ' . $_SESSION['compt_cod'] . '
Perso = ' . $_SESSION['perso_cod'];

  	writelog_class_sql($texte_log);
    echo '<p class="titre"><b>Erreur base de données:</b><br>Votre requète n\'a pas pu être effectuée.</p>';

   $envoi = 1;
   if ($message == 'Invalid SQL: select compt_admin from compte where compt_cod =  ')
   {
   	$envoi = 0;
	}
   $mail = 'merrick@jdr-delain.net';
   /*$message = str_replace(';',str(127),$message);
   $requete = str_replace(';',str(127),$requete);
   $libelle = str_replace(';',str(127),$libelle);  */
	$texte_mail = 'Erreur Delain :' . chr(13) . chr(10);
	$texte_mail = $texte_mail . 'message : *' . $message . '*' . chr(13) . chr(10);
	$texte_mail = $texte_mail . 'requete : *' . $requete . '*' . chr(13) . chr(10);
	$texte_mail = $texte_mail . 'libelle : *' . $libelle . '*' . chr(13) . chr(10);
	$entete = 'From: merrick@jdr-delain.net' . chr(13) . chr(10);
	$entete = $entete . 'Reply-To: merrick@jdr-delain.net' . chr(13) . chr(10);
	$entete = $entete . 'Error-To: merrick@jdr-delain.net' . chr(10);
	$sujet = 'Erreur SQL Delain' . chr(13) . chr(10);
	if ($envoi == 1)
	{
		if(mail($mail,$sujet,$texte_mail,$entete))
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
function nb_obj_sur_case($perso_cod)
{
	$req = 'select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod;
	$this->query($req);
	$this->next_record();
	$position = $this->f('ppos_pos_cod');
	$req = 'select pobj_cod as nombre from objet_position where pobj_pos_cod = ' . $position;
	$this->query($req);
	return $this->nf();
}

function nb_or_sur_case($perso_cod)
{
	$req = 'select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod;
	$this->query($req);
	$this->next_record();
	$position = $this->f('ppos_pos_cod');
	$req = 'select por_cod from or_position where por_pos_cod = ' . $position;
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
	$nb_parm = $this->nf();
	if ($nb_parm == 0)
	{
		return false;
	}
	else
	{
		$this->next_record();
		$retour = $this->f('gobj_distance');
		if ($retour == 'O')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

function getparm_n($parm)
{
	$req_parm = 'select parm_valeur from parametres where parm_cod = ' . $parm;
	$this->query($req_parm);
	$nb_parm = $this->nf();
	if ($nb_parm == 0)
	{
		$retour = -1;
		echo('Paramètre non fixé !!');
		return $retour;
	}
	else
	{
		$this->next_record();
		$retour = $this->f('parm_valeur');
		return $retour;
	}
}
function is_milice($perso_cod)
{
	$req_parm = 'select is_milice(' . $perso_cod . ') as resultat ';
	$this->query($req_parm);
	$this->next_record();
	return $this->f('resultat');
}

function compte_objet($perso_cod,$objet)
{
	$req_obj = 'select count(perobj_cod) as nombre from perso_objets,objets ';
	$req_obj = $req_obj . 'where perobj_perso_cod = ' . $perso_cod ;
	$req_obj = $req_obj . ' and perobj_obj_cod = obj_cod ';
	$req_obj = $req_obj . ' and obj_gobj_cod = ' . $objet ;
	$this->query($req_obj);
	$this->next_record();
	$retour = $this->f('nombre');
	return $retour;
}

function distance($pos1,$pos2)
{
	$req_distance = 'select distance(' . $pos1 . ',' . $pos2 . ') as distance';
	$this->query($req_distance);
	$this->next_record();
	$retour = $this->f('distance');
	return $retour;
}

function getparm_t($parm)
{
	$req_parm = 'select parm_valeur_texte from parametres where parm_cod = ' . $parm;
	$this->query($req_parm);
	$nb_parm = $this->nf();
	if ($nb_parm == 0)
	{
		$retour = -1;
		echo('Paramètre non fixé !!');
		return $retour;
	}
	else
	{
		$this->next_record();
		$retour = $this->f('parm_valeur_texte');
		return $retour;
	}
}

function existe_competence($perso_cod,$competence)
{
	$req_comp = 'select count(*) as nombre from perso_competences where pcomp_perso_cod = ' . $perso_cod . ' and pcomp_pcomp_cod = ' . $competence . ' and pcomp_modificateur != 0';
	$this->query($req_comp);
	$this->next_record();
	$tab_comp = $this->f('nombre');
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
	$retour = false;
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

function is_identifie_objet($perso_cod,$v_objet)
{
	$req_comp = 'select count(*) as test from perso_identifie_objet where pio_perso_cod = ' . $perso_cod . ' and pio_obj_cod = ' . $v_objet;
	$this->query($req_comp);
	$this->next_record();
	$tab_comp = $this->f('test');
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
	$req_comp = 'select count(*) as nombre from guilde_perso,guilde_rang where pguilde_perso_cod = ' . $perso_cod ;
	$req_comp = $req_comp . 'and pguilde_guilde_cod = rguilde_guilde_cod ';
	$req_comp = $req_comp . 'and pguilde_rang_cod = rguilde_rang_cod ';
	$req_comp = $req_comp . 'and rguilde_admin = \'O\' ';
	$this->query($req_comp);
	$this->next_record();
	$tab_comp = $this->f('nombre');
	if ($tab_comp != 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function auth_mess($perso_cod,$mid)
{
	$retour = false;
	$req = 'select dmsg_cod from messages_dest where dmsg_msg_cod = ' . $mid . ' and dmsg_perso_cod = ' . $perso_cod;
	$this->query($req);
	if($this->nf() != 0)
		$retour = true;
	$req = 'select emsg_cod from messages_exp where emsg_msg_cod = ' . $mid . ' and emsg_perso_cod = ' . $perso_cod;
	$this->query($req);
	if($this->nf() != 0)
		$retour = true;
	return $retour;
}

function is_in_guilde($perso_cod)
{
	$req_comp = 'select count(*) as nombre from guilde_perso,guilde_rang where pguilde_perso_cod = ' . $perso_cod ;
	$req_comp = $req_comp . 'and pguilde_guilde_cod = rguilde_guilde_cod ';
	$req_comp = $req_comp . 'and pguilde_rang_cod = rguilde_rang_cod ';
	$req_comp = $req_comp . 'and pguilde_valide = \'O\' ';
	$this->query($req_comp);
	$this->next_record();
	$tab_comp = $this->f('nombre');
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
	$req_comp = 'select guilde_nom from guilde,guilde_perso where pguilde_perso_cod = ' . $perso;
	$req_comp = $req_comp . 'and pguilde_valide = \'O\' ';
	$req_comp = $req_comp . 'and pguilde_guilde_cod = guilde_cod ';
	$this->query($req_comp);
	$nb_comp = $this->nf();
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
	$req = 'select pos_cod,pos_x,pos_y,pos_etage,etage_libelle from positions,perso_position,etage ';
	$req = $req . 'where ppos_perso_cod = ' . $perso_cod ;
	$req = $req . 'and ppos_pos_cod = pos_cod ';
	$req = $req . 'and etage_numero = pos_etage';
	$this->query($req);
	$this->next_record();
	$tab['pos_cod'] = $this->f('pos_cod');
	$tab['x'] = $this->f('pos_x');
	$tab['y'] = $this->f('pos_y');
	$tab['etage'] = $this->f('pos_etage');
	$tab['etage_libelle'] = $this->f('etage_libelle');
	return $tab;
}

function nb_admin_guilde($guilde_cod)
{
	$req_comp = 'select count(*) as nombre from guilde_perso,guilde_rang,perso where pguilde_guilde_cod = ' . $guilde_cod ;
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
	$req_comp = 'select sum(perso_nb_joueur_tue) as joueur_tue,sum(perso_nb_monstre_tue) as monstre_tue,sum(perso_nb_mort) as nb_mort,get_renommee_guilde(' . $guilde_cod . ') as renommee,get_karma_guilde(' . $guilde_cod . ') as karma ';
	$req_comp = $req_comp . 'from guilde_perso,perso where pguilde_guilde_cod = ' . $guilde_cod ;
	$req_comp = $req_comp . 'and pguilde_perso_cod = perso_cod ';
//	$req_comp = $req_comp . 'and perso_type_perso = 1 ';
	$req_comp = $req_comp . 'and perso_actif != \'N\' ';
	$req_comp = $req_comp . 'and pguilde_valide = \'O\' ';
	$this->query($req_comp);
	$this->next_record();
	$tab_comp['joueur_tue'] = $this->f('joueur_tue');
	$tab_comp['monstre_tue'] = $this->f('monstre_tue');
	$tab_comp['nb_mort'] = $this->f('nb_mort');
	$tab_comp['renommee'] = $this->f('renommee');
	$tab_comp['karma'] = $this->f('karma');
	return $tab_comp;
}

function get_pa_attaque($perso_cod)
{
	$req_comp = 'select nb_pa_attaque(' . $perso_cod . ') as pa';
	$this->query($req_comp);
	$this->next_record();
	$pa = $this->f('pa');
	return $pa;
}

function get_pa_dep($perso_cod)
{
	$req_comp = 'select get_pa_dep(' . $perso_cod . ') as pa';
	$this->query($req_comp);
	$this->next_record();
	$pa = $this->f('pa');
	return $pa;
}
function get_pa_foudre($perso_cod)
{
	$req_comp = 'select nb_pa_foudre(' . $perso_cod . ') as pa';
	$this->query($req_comp);
	$this->next_record();
	$pa = $this->f('pa');
	return $pa;
}
function is_lieu($perso_cod)
{
	$req_pos = 'select * from lieu_position,perso_position ';
	$req_pos = $req_pos . 'where ppos_perso_cod = ' . $perso_cod ;
	$req_pos = $req_pos . 'and ppos_pos_cod = lpos_pos_cod ';
	$this->query($req_pos);
	$nb_pos = $this->nf();
	if ($nb_pos == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
function is_perso_quete($perso_cod)
{
$req_quete = 'select count(perso_cod) as nombre from perso,perso_position
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod .')
										and perso_quete in (\'quete_ratier.php\',\'enchanteur.php\',\'quete_alchimiste.php\',\'quete_chasseur.php\',\'quete_dispensaire.php\',\'quete_groquik.php\')
										and perso_cod = ppos_perso_cod';
	$this->query($req_quete);
	$this->next_record();
	$nb_pos = $this->f("nombre");
	if ($nb_pos == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
function get_perso_quete($perso_cod)
{
$req_quete = 'select perso_quete,perso_cod from perso,perso_position
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = ' . $perso_cod .')
										and perso_quete in (\'quete_ratier.php\',\'enchanteur.php\',\'quete_alchimiste.php\',\'quete_chasseur.php\',\'quete_dispensaire.php\',\'quete_groquik.php\')
										and perso_cod = ppos_perso_cod
										order by perso_quete';
	$this->query($req_quete);
	$tab_quete = array();
	while($this->next_record())
	{
		$perso =  $this->f("perso_cod");
		$tab_quete[$perso] = $this->f("perso_quete");
	}
	return $tab_quete;
}
function is_temple($perso_cod)
{
	$req_pos = 'select* from lieu_position,perso_position,lieu ';
	$req_pos = $req_pos . 'where ppos_perso_cod = ' . $perso_cod ;
	$req_pos = $req_pos . 'and ppos_pos_cod = lpos_pos_cod ';
	$req_pos = $req_pos . 'and lpos_lieu_cod = lieu_cod ';
	$req_pos = $req_pos . 'and lieu_tlieu_cod = 2 ';
	$this->query($req_pos);
	$nb_pos = $this->nf();
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
	$tab_pos = get_pos($perso_cod);
	$position = $tab_pos[pos_cod];
	$req_ins = 'delete from perso_temple where ptemple_perso_cod = ' . $perso_cod ;
	$this->query($req_ins);
	$req_ins = 'insert into perso_temple (ptemple_perso_cod,ptemple_pos_cod) values (' . $perso_cod . ',' . $position . ')';
	$this->query($req_ins);
	return true;
}
function is_intangible($perso_cod)
{
	$req = 'select perso_tangible from perso where perso_cod = ' . $perso_cod ;
	$this->query($req);
	$this->next_record();
	if (($this->f('perso_tangible')) == 'O')
	{
		return false;
	}
	else
	{
		return true;
	}
}
function get_lieu($perso_cod)
{
	$req_lieu = 'select lieu_nom,lieu_description,lieu_url,tlieu_libelle,tlieu_cod,lieu_cod,lpos_pos_cod,lieu_refuge,lieu_prelev ';
	$req_lieu = $req_lieu . 'from lieu,lieu_type,lieu_position,perso_position ';
	$req_lieu = $req_lieu . 'where ppos_perso_cod = ' . $perso_cod ;
	$req_lieu = $req_lieu . 'and ppos_pos_cod = lpos_pos_cod ';
	$req_lieu = $req_lieu . 'and lpos_lieu_cod = lieu_cod ';
	$req_lieu = $req_lieu . 'and lieu_tlieu_cod = tlieu_cod ';
	$this->query($req_lieu);
	$this->next_record();
	$tab_lieu['nom'] = $this->f('lieu_nom');
	$tab_lieu['description'] = $this->f('lieu_description');
	$tab_lieu['url'] = $this->f('lieu_url');
	$tab_lieu['libelle'] = $this->f('tlieu_libelle');
	$tab_lieu['type_lieu'] = $this->f('tlieu_cod');
	$tab_lieu['position'] = $this->f('ppos_pos_cod');
	$tab_lieu['lieu_cod'] = $this->f('lieu_cod');
	$tab_lieu['pos_cod'] = $this->f('lpos_pos_cod');
	$tab_lieu['lieu_refuge'] = $this->f('lieu_refuge');
	$tab_lieu['lieu_prelev'] = $this->f('lieu_prelev');
	return $tab_lieu;
}

function is_refuge($perso_cod)
{
	$req_pos = 'select lieu_cod from lieu_position,perso_position,lieu ';
	$req_pos = $req_pos . 'where ppos_perso_cod = ' . $perso_cod ;
	$req_pos = $req_pos . 'and ppos_pos_cod = lpos_pos_cod ';
	$req_pos = $req_pos . 'and lpos_lieu_cod = lieu_cod ';
	$req_pos = $req_pos . 'and lieu_refuge = \'O\' ';
	$this->query($req_pos);
	$nb_pos = $this->nf();
	if ($nb_pos == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
function has_artefact($perso_cod,$objet)
{
	$req_pos = 'select count(*) as nombre from perso_objets where perobj_perso_cod = ' . $perso_cod . ' and perobj_obj_cod = ' . $objet ;
	$this->query($req_pos);
	$tab_pos = $this->f('nombre');
	if ($tab_pos != 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function is_bernardo($perso_cod)
{
    $req_pos = 'select valeur_bonus(' . $perso_cod . ' , \'BER\') as nombre';
	$this->query($req_pos);
	$this->next_record();
	if ($this->f('nombre') != 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function is_revolution($guilde_cod)
{
	$req = 'select revguilde_cod from guilde_revolution where revguilde_guilde_cod = ' . $guilde_cod ;
	$this->query($req);
	if ($this->nf() == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function is_admin_monstre($compt_cod)
{
	$req = 'select compt_monstre from compte where compt_cod = ' . $compt_cod ;
	$this->query($req);
	$this->next_record();
	$resultat = $this->f('compt_monstre');
	if ($resultat == 'O')
	{
		return true;
	}
	else
	{
		return false;
	}
}

function is_admin($compt_cod)
{
	$req = 'select compt_admin from compte where compt_cod = ' . $compt_cod ;
	$this->query($req);
	$this->next_record();
	$resultat = $this->f('compt_admin');
	if ($resultat == 'O')
	{
		return true;
	}
	else
	{
		return false;
	}
}

function is_monstre($perso_cod)
{
    $req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod ;
    $this->query($req);
    $this->next_record();
    $resultat = $this->f('perso_type_perso');
    return ($resultat == 2);
}

function mess_chef_coterie($titre,$corps,$coterie,$perso_cod)
{
	$titre = pg_escape_string($titre);
	$corps = pg_escape_string($corps);
	$req = 'select groupe_chef from groupe,perso where groupe_cod = ' . $coterie .' and groupe_chef = perso_cod';
	$this->query($req);
	$this->next_record();
	$chef = $this->f('groupe_chef');
	$this->query('select nextval(\'seq_msg_cod\') as num_mes');
	$this->next_record();
	$num_mes = $this->f('num_mes');
	$req_mes = 'insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
		values (' . $num_mes . ', now(), e\'' . $titre . '\', e\'' . $corps . '\', now()) ';
	$this->query($req_mes);
	// on renseigne l'expéditeur
	$req2 = 'insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)
		values (' . $num_mes . ',' . $perso_cod . ',\'N\') ';
	$this->query($req2);
	if ($this->nf() == 0)
	{
		return 'KO Pas de chef';
	}
	$req2 = 'insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		values (' . $num_mes . ',' . $chef . ',\'N\',\'N\') ';
	$this->query($req2);
	return 'ok';
}

function mess_all_coterie($titre,$corps,$coterie,$perso_cod)
{
	$titre = pg_escape_string($titre);
	$corps = pg_escape_string($corps);
	$req = 'select groupe_chef from groupe where groupe_cod = ' . $coterie;
	$this->query($req);
	$this->next_record();
	$chef = $this->f('groupe_chef');
	$this->query('select nextval(\'seq_msg_cod\') as num_mes');
	$this->next_record();
	$num_mes = $this->f('num_mes');
	$req_mes = 'insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2)
		values (' . $num_mes . ', now(), e\'' . $titre . '\', e\'' . $corps . '\', now()) ';
	$this->query($req_mes);
	// on renseigne l'expéditeur
	$req2 = 'insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive)
		values (' . $num_mes . ',' . $chef . ',\'N\') ';
	$this->query($req2);
	$req2 = 'insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
		select ' . $num_mes . ',pgroupe_perso_cod,\'N\',\'N\'
		from groupe_perso
		where pgroupe_groupe_cod = ' . $coterie . '
		and pgroupe_statut =  1 ';
	$this->query($req2);
	return 'ok';
}


}

class DB_Article_CT extends CT_Sql {
  var $compt_sess = true; // on logue les sessions dans une seconde table pour éviter les abus
  var $table_log = 'sessions_log'; // uniquement si $compt_sess = true;
  var $classname = 'DB_Article_CT';
  var $database_class = 'base_delain';          // Which database to connect...
  var $database_table = 'sessions_active'; // and find our session data in this
  var $encoding_mode = 'base64';


  function ac_gc($gc_time, $name) {
    $timeout = time();
    $sqldate = date('YmdHis', $timeout - ($gc_time * 60));

   $this->db->query('DELETE FROM ' . $this->database_table . ' WHERE changed < \'' . $sqldate . '\' AND name = \'' . pg_escape_string($name) . '\'');
    }

  function ac_store($id, $name, $str) {
    $ret = true;

    switch ( $this->encoding_mode ) {
      case 'slashes':
        $str = pg_escape_string($name . ':' . $str);
      break;

      case 'base64':
      default:
        $str = base64_encode($name . ':' . $str);
    };

    $name = pg_escape_string($name);

    ## update duration of visit
    $now = date('YmdHis', time());
    $maintenant = date('ymdhis');
    //$maintenant = round($maintenant/5);
    //$maintenant = $maintenant * 5;
    $uquery = 'update ' . $this->database_table . ' set val=\'' . $str . '\', changed=\'' . $now . '\' where sid=\'' . $id . '\' and name=\'' . $name . '\'';
    $squery = 'select count(*) from ' . $this->database_table . ' where val=\'' . $str . '\' and changed=\'' . $now . '\' and sid=\'' . $id . '\' and name=\'' . $name . '\'';
    $iquery = 'insert into ' . $this->database_table . ' ( sid, name, val, changed ) values (\'' . $id . '\', \'' . $name . '\', \'' . $str . '\', \'' . $now . '\')';
	 $this->db->begin();
    $this->db->query($uquery);

    # FIRST test to see if any rows were affected.
    #   Zero rows affected could mean either there were no matching rows
    #   whatsoever, OR that the update statement did match a row but made
    #   no changes to the table data (i.e. UPDATE tbl SET col = 'x', when
    #   'col' is _already_ set to 'x') so then,
    # SECOND, query(SELECT...) on the sid to determine if the row is in
    #   fact there,
    # THIRD, verify that there is at least one row present, and if there
    #   is not, then
    # FOURTH, insert the row as we've determined that it does not exist.

    if ( $this->db->affected_rows() == 0
        && $this->db->query($squery)
	&& $this->db->next_record() && $this->db->f(0) == 0
        && !$this->db->query($iquery)) {
		   $ret = false;
    }
    # seconde étape : on loggue chacune des sessions dans une autre base
    $ip = getenv('REMOTE_ADDR');
    $luquery = 'update ' . $this->table_log . ' set sessl_nombre = sessl_nombre + 1,sessl_ip = \'' . $ip . '\' where sessl_sid = \'' . $id . '\' and sessl_date = \'' . $maintenant . '\'';
    $this->db->query($luquery);
    if ( $this->db->affected_rows() == 0)
    {
    	$liquery = 'insert into ' . $this->table_log . ' (sessl_sid,sessl_date,sessl_nombre,sessl_ip) values (\'' . $id . '\',\'' . $maintenant . '\',1,\'' . $ip . '\')';
    	$this->db->query($liquery);
    }
    $this->db->commit();

    return $ret;
  }


}

$db = new base_delain;
