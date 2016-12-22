<?php 
/* Affichage des erreurs sur chaque page */
/*ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set("html_errors","1");
ini_set("pgsql.ignore_notice","0");
ini_set("error_reporting","E_ALL");
error_reporting(E_ALL);*/

/* Script de connexion à la base smeweb nécessaire avant toute requête */
$dbconnect = pg_connect("service=delain") or die("En cours de maintenance");; 
  if(!$dbconnect)
        {
         echo("Une erreur est survenue.\n");
		 exit;
       }

/* fonctions génériques */
function getparm_n($parm)
{
    /**
     * NE PLUS UTILISER CETTE FONCTION !!
     *
     * * $param = new parametres;
     * $param->getparm(64);
     */
    $dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_parm = "select parm_valeur from parametres where parm_cod = $parm";
	$res_parm = pg_exec($dbconnect,$req_parm);
	$nb_parm = pg_numrows($res_parm);
	if ($nb_parm == 0)
	{
		$retour = -1;
		echo("Paramètre non fixé !!");
		return $retour;
	}
	else
	{
		$tab_parm = pg_fetch_array($res_parm,0);
		$retour = $tab_parm[0];
		return $retour;
	}
}
function debut_tran($parm)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_begin = "begin work";
	$res_begin = pg_exec($dbconnect,$req_begin);
	return true;
}
function fin_tran($parm)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_fin = "commit";
	$req_fin = pg_exec($dbconnect,$req_fin);
	return true;
}
function distance($pos1,$pos2)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_begin = "select distance($pos1,$pos2)";
	$res_begin = pg_exec($dbconnect,$req_begin);
	$tab = pg_fetch_array($res_begin,0);
	return $tab[0];
}
function getparm_t($parm)
{
    /**
     * NE PLUS UTILISER CETTE FONCTION !!
     *
     * * $param = new parametres;
     * $param->getparm(64);
     */
    $dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_parm = "select parm_valeur_texte from parametres where parm_cod = $parm";
	$res_parm = pg_exec($dbconnect,$req_parm);
	$nb_parm = pg_numrows($res_parm);
	if ($nb_parm == 0)
	{
		$retour = -1;
		echo("Paramètre non fixé !!");
		return $retour;
	}
	else
	{
		$tab_parm = pg_fetch_array($res_parm,0);
		$retour = $tab_parm[0];
		return $retour;
	}
}
function existe_competence($perso_cod,$competence)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select count(*) from perso_competences where pcomp_perso_cod = $perso_cod and pcomp_pcomp_cod = $competence and pcomp_modificateur != 0";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	if ($tab_comp[0]==1)
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
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select count(*) from lock_combat where lock_cible = $perso_cod";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	if ($tab_comp[0]!=0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function is_identifie_objet($perso_cod,$obj_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select count(*) from perso_identifie_objet where pio_perso_cod = $perso_cod and pio_obj_cod = $obj_cod ";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	if ($tab_comp[0]!=0)
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
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select count(*) from guilde_perso,guilde_rang where pguilde_perso_cod = $perso_cod ";
	$req_comp = $req_comp . "and pguilde_guilde_cod = rguilde_guilde_cod ";
	$req_comp = $req_comp . "and pguilde_rang_cod = rguilde_rang_cod ";
	$req_comp = $req_comp . "and rguilde_admin = 'O' "; 
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	if ($tab_comp[0]!=0)
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
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select guilde_nom from guilde,guilde_perso where pguilde_perso_cod = $perso ";
	$req_comp = $req_comp . "and pguilde_valide = 'O' ";
	$req_comp = $req_comp . "and pguilde_guilde_cod = guilde_cod ";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$nb_comp = pg_numrows($res_comp);
	if ($nb_comp == 0)
	{
		return '';
	}
	else
	{
		$tab_comp = pg_fetch_array($res_comp,0);
		return $tab_comp[0];
	}
}
function get_pos($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req = "select pos_cod,pos_x,pos_y,pos_etage from positions,perso_position ";
	$req = $req . "where ppos_perso_cod = $perso_cod ";
	$req = $req . "and ppos_pos_cod = pos_cod ";
	$res = pg_exec($dbconnect,$req);
	$tab = pg_fetch_array($res,0);
	return $tab;
}
function nb_admin_guilde($guilde_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select count(*) from guilde_perso,guilde_rang,perso where pguilde_guilde_cod = $guilde_cod ";
	$req_comp = $req_comp . "and pguilde_guilde_cod = rguilde_guilde_cod ";
	$req_comp = $req_comp . "and pguilde_rang_cod = rguilde_rang_cod ";
	$req_comp = $req_comp . "and rguilde_admin = 'O' "; 
	$req_comp = $req_comp . "and pguilde_perso_cod = perso_cod ";
	$req_comp = $req_comp . "and perso_actif = 'O' ";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	return $tab_comp[0];
}
function get_stats_guilde($guilde_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select sum(perso_nb_joueur_tue),sum(perso_nb_monstre_tue),sum(perso_nb_mort),get_reputation_guilde($guilde_cod),get_renommee_guilde($guilde_cod),get_karma_guilde($guilde_cod) ";
	$req_comp = $req_comp . "from guilde_perso,perso where pguilde_guilde_cod = $guilde_cod ";
	$req_comp = $req_comp . "and pguilde_perso_cod = perso_cod ";
	$req_comp = $req_comp . "and perso_type_perso = 1 ";
	$req_comp = $req_comp . "and perso_actif = 'O' ";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	return $tab_comp;
}
function get_pa_attaque($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select nb_pa_attaque($perso_cod)";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	$pa = $tab_comp[0];
	return $pa;
}
function get_pa_dep($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select get_pa_dep($perso_cod)";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	$pa = $tab_comp[0];
	return $pa;
}
function get_pa_foudre($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_comp = "select nb_pa_foudre($perso_cod)";
	$res_comp = pg_exec($dbconnect,$req_comp);
	$tab_comp = pg_fetch_array($res_comp,0);
	$pa = $tab_comp[0];
	return $pa;
}
function is_lieu($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_pos = "select* from lieu_position,perso_position ";
	$req_pos = $req_pos . "where ppos_perso_cod = $perso_cod ";
	$req_pos = $req_pos . "and ppos_pos_cod = lpos_pos_cod ";
	$res_pos = pg_exec($dbconnect,$req_pos);
	$nb_pos = pg_numrows($res_pos);
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
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_pos = "select* from lieu_position,perso_position,lieu ";
	$req_pos = $req_pos . "where ppos_perso_cod = $perso_cod ";
	$req_pos = $req_pos . "and ppos_pos_cod = lpos_pos_cod ";
	$req_pos = $req_pos . "and lpos_lieu_cod = lieu_cod ";
	$req_pos = $req_pos . "and lieu_tlieu_cod = 2 ";
	$res_pos = pg_exec($dbconnect,$req_pos);
	$nb_pos = pg_numrows($res_pos);
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
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_pos = "select ppos_pos_cod from perso_position ";
	$req_pos = $req_pos . "where ppos_perso_cod = $perso_cod ";
	$res_pos = pg_exec($dbconnect,$req_pos);
	$tab_pos = pg_fetch_array($res_pos,0);
	$position = $tab_pos[0];
	$req_ins = "delete from perso_temple where ptemple_perso_cod = $perso_cod ";
	$res_ins = pg_exec($dbconnect,$req_ins);
	$req_ins = "insert into perso_temple (ptemple_perso_cod,ptemple_pos_cod) values ($perso_cod,$position)";
	$res_ins = pg_exec($dbconnect,$req_ins);
	return true;
}
function get_lieu($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_lieu = "select lieu_nom,lieu_description,lieu_url,tlieu_libelle ";
	$req_lieu = $req_lieu . "from lieu,lieu_type,lieu_position,perso_position ";
	$req_lieu = $req_lieu . "where ppos_perso_cod = $perso_cod ";
	$req_lieu = $req_lieu . "and ppos_pos_cod = lpos_pos_cod ";
	$req_lieu = $req_lieu . "and lpos_lieu_cod = lieu_cod ";
	$req_lieu = $req_lieu . "and lieu_tlieu_cod = tlieu_cod ";
	$res_lieu = pg_exec($dbconnect,$req_lieu);
	$tab_lieu = pg_fetch_array($res_lieu,0);
	return $tab_lieu;
}
function is_refuge($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_pos = "select lieu_cod from lieu_position,perso_position,lieu ";
	$req_pos = $req_pos . "where ppos_perso_cod = $perso_cod ";
	$req_pos = $req_pos . "and ppos_pos_cod = lpos_pos_cod ";
	$req_pos = $req_pos . "and lpos_lieu_cod = lieu_cod ";
	$req_pos = $req_pos . "and lieu_refuge = 'O' ";
	$res_pos = pg_exec($dbconnect,$req_pos);
	$nb_pos = pg_numrows($res_pos);
	if ($nb_pos == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
function is_bloque_poids($perso_cod)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_pos = "select get_poids($perso_cod),perso_enc_max from perso where perso_cod = $perso_cod ";
	$res_pos = pg_exec($dbconnect,$req_pos);
	$tab_pos = pg_fetch_array($res_pos,0);
	if ($tab_pos[0] >= (2*$tab_pos[1]))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function has_artefact($perso_cod,$objet)
{
	$dbconnect = pg_connect("service=delain") or die("On cherche pourquoi on arrive pas à se connecter");
	$req_pos = "select count(*) from perso_objets where perobj_perso_cod = $perso_cod and perobj_obj_cod = $objet ";
	$res_pos = pg_exec($dbconnect,$req_pos);
	$tab_pos = pg_fetch_array($res_pos,0);
	if ($tab_pos[0] != 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/* variables globales */
$tab_blessures[0] = 'touché';
$tab_blessures[1] = 'blessé';
$tab_blessures[2] = 'gravement touché';
$tab_blessures[3] = 'presque mort';

$perso_type_perso[1] = 'joueur';
$perso_type_perso[2] = 'monstre';

$nom_sexe['M'] = 'Messire';
$nom_sexe['F'] = 'Damoiselle';