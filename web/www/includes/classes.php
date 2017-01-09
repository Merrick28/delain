<?php 
$maintenance = false;
//ini_set('zlib.output_compression','1');
//die ('Maintenance, merci de patienter');
if (isset($ip_bad))
{
    if ( array_key_exists("REMOTE_ADDR", $_SERVER) && !isset($from_forum))
    { // Existe toujours sauf quand le script est appelé en local
        $adr_ip = $_SERVER["REMOTE_ADDR"];
        $filtre_bad = array_search($adr_ip,$ip_bad);
        if($filtre_bad)
        {
            die('Votre adresse ip a été bannie du jeu.');
        }
        if($maintenance)
        {
            $filtre_ok = array_search($adr_ip,$ip_ok);
            if($filtre_ok)
            {
                echo '<!-- acces autorise pendant maintenance -->';
            }
            else
            {
                die('Redemerrage prévu ce soir entre 20h et 21h pour les joueurs.<br />Merrick.<br /><br />');
            }
        }
    }
}


// Gère l’accès à la base de données.
require_once dirname(__FILE__) . '/base_delain.php';
$db = new base_delain;

// Gère l’affichage standardisé d’éléments HTML.
require_once dirname(__FILE__) . '/html.php';
$html = new html;

// Gère l’envoi de messages dans le jeu.
require_once dirname(__FILE__) . '/message.php';

class DB_Article_CT extends CT_Sql
{
	var $compt_sess = true; // on logue les sessions dans une seconde table pour éviter les abus
	var $table_log = 'sessions_log'; // uniquement si $compt_sess = true;
	var $classname = 'DB_Article_CT';
	var $database_class = 'base_delain';          // Which database to connect...
	var $database_table = 'sessions_active'; // and find our session data in this
	var $encoding_mode = 'base64';

	function ac_gc($gc_time, $name)
	{
		$timeout = time();
		$sqldate = date('YmdHis', $timeout - ($gc_time * 60));
		$this->db->query('DELETE FROM ' . $this->database_table . ' WHERE changed < \'' . $sqldate . '\' AND name = \'' . pg_escape_string($name) . '\'');
	}

	function ac_store($id, $name, $str)
	{
		$ret = true;

		switch ( $this->encoding_mode )
		{
			case 'slashes':
				$str = pg_escape_string($name . ':' . $str);
			break;

			case 'base64':
			default:
				$str = base64_encode($name . ':' . $str);
		}

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
			&& !$this->db->query($iquery))
		{
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
