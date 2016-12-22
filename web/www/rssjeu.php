<?php 
$auth_text = "Utilisez votre login et mot de passe du jeu";
include 'classes.php';
$db = new base_delain;
$autorise = 0;
if(!(empty($PHP_AUTH_USER) || empty($PHP_AUTH_PW)))
{
	$req = 'select compt_cod from compte where compt_nom = \'' . pg_escape_string($PHP_AUTH_USER) . '\' and compt_password = \'' . pg_escape_string($PHP_AUTH_PW) . '\'';
	$db->query($req);
	if($db->nf() != 0)
	{
		$db->next_record();
		$compt_cod = $db->f('compt_cod');
		$autorise = 1;
	}
}
if($autorise == 0)
{
	header("www-authenticate: basic realm=\"$auth_text\"");
	header("http/1.0 401 unauthorized");
}
else
{
	$db_detail = new base_delain;
	header ('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
	header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header ('Content-Type: text/xml');
	$sortie = '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.00">
<channel>
	<title>Evenements des persos dans Delain</title>
	<link>http://www.jdr-delain.net/</link>
	<description>Evenements des persos dans les souterrains de Delain</description>
	<language>fr</language>
	<docs>http://backend.userland.com/rss</docs>

	<managingEditor>merrick@jdr-delain.net</managingEditor>
	<webMaster>merrick@jdr-delain.net</webMaster>';
	//
	// a priori tout est bon
	// on va commencer à boucler sur les persos et familiers
	//
	$req = "select levt_cod,
		to_char(levt_date,'DD/MM/YYYY hh24:mi:ss') as evt_date,
		to_char(levt_date,'Dy, DD Mon YYYY hh24:mi:ss +0200') as pubdate,
		tevt_libelle,
		replace((replace(replace(levt_texte,'[perso_cod1]',per1.perso_nom),'[attaquant]',coalesce(per2.perso_nom,''))),'[cible]',coalesce(per3.perso_nom,'')) as levt_texte,
		levt_perso_cod1,
		levt_attaquant,
		levt_cible,
		levt_date as tri_date
		from ligne_evt
		left join type_evt  on levt_tevt_cod = tevt_cod
		left outer join perso per1 on per1.perso_cod = levt_perso_cod1
		left outer join perso per2 on per2.perso_cod = levt_attaquant
		left outer join perso per3 on per3.perso_cod = levt_cible
		where levt_perso_cod1 in
		(
		select perso_cod
				from perso,perso_compte
				where pcompt_compt_cod = " . $compt_cod . "
				and pcompt_perso_cod = perso_cod
				and perso_actif != 'N'
				UNION
				select perso_cod
				from perso,perso_compte,perso_familier
				where pcompt_compt_cod = " . $compt_cod . "
				and pcompt_perso_cod = pfam_perso_cod
				and pfam_familier_cod = perso_cod
				and perso_actif != 'N'
				and perso_type_perso = 3
		) and levt_perso_cod1 != levt_attaquant
		union
		select dmsg_cod,
		to_char(msg_date2,'DD/MM/YYYY hh24:mi:ss') as evt_date,
		to_char(msg_date2,'Dy, DD Mon YYYY hh24:mi:ss +0200') as pubdate,
		'Message de la part de '||(select perso_nom from perso,messages_exp where emsg_msg_cod = msg_cod and emsg_perso_cod = perso_cod),
		msg_titre,
		dmsg_perso_cod as levt_perso_cod1,
		0,
		0,
		msg_date2 as tri_date
		from messages,messages_dest
		where dmsg_perso_cod in
		(
		select perso_cod
		from perso,perso_compte
		where pcompt_compt_cod = " . $compt_cod . "
		and pcompt_perso_cod = perso_cod
		and perso_actif != 'N'
		UNION
		select perso_cod
		from perso,perso_compte,perso_familier
		where pcompt_compt_cod = " . $compt_cod . "
		and pcompt_perso_cod = pfam_perso_cod
		and pfam_familier_cod = perso_cod
		and perso_actif != 'N'
		and perso_type_perso = 3
		)
		and dmsg_msg_cod = msg_cod
		order by tri_date desc
		limit 50";

			/*$req = 'select levt_cod,to_char(levt_date,'DD/MM/YYYY hh24:mi:ss') as evt_date,tevt_libelle,levt_texte,levt_perso_cod1,levt_attaquant,levt_cible
from ligne_evt,type_evt
where levt_perso_cod1 in
(
select perso_cod
		from perso,perso_compte
		where pcompt_compt_cod = ' . $compt_cod . '
		and pcompt_perso_cod = perso_cod
		and perso_actif != \'N\'
		UNION
		select perso_cod
		from perso,perso_compte,perso_familier
		where pcompt_compt_cod = ' . $compt_cod . '
		and pcompt_perso_cod = pfam_perso_cod
		and pfam_familier_cod = perso_cod
		and perso_actif != \'N\'
		and perso_type_perso = 3
		)
		and levt_tevt_cod = tevt_cod
order by levt_cod desc
limit 50 ';*/


	$db->query($req);
	while($db->next_record())
	{
		$req = 'select perso_nom,dlt_passee(perso_cod) as dlt_passee from perso where perso_cod = ' . $db->f('levt_perso_cod1');
		$db_detail->query($req);
		$db_detail->next_record();
		$sortie .= '<item>
			<title>' . $db->f('tevt_libelle') . ' (' . $db_detail->f('perso_nom');
		if ($db_detail->f("dlt_passee") == 1)
			$sortie .= ' - DLT Passée ! ';
		$sortie .= ') </title>
			<link>http://www.jdr-delain.net/?evt=' . $db->f('levt_cod') . '</link>
			<guid>http://www.jdr-delain.net/?evt=' . $db->f('levt_cod') . '</guid>
			<author>no-replay@jdr-delain.net</author>
			<pubDate>' . $db->f('pubdate') . '</pubDate>
			<description>';
		$texte_evt = $db->f('levt_texte');
        $texte_evt = str_replace(chr(127), ';', $texte_evt); // Restoring lost semicolons
		/*$req_nom_evt = "select perso1.perso_nom as nom1 ";
		if ($db->f("levt_attaquant") != '')
		{
			$req_nom_evt = $req_nom_evt . ",attaquant.perso_nom as nom2";
		}
		if ($db->f("levt_cible") != '')
		{
			$req_nom_evt = $req_nom_evt . ",cible.perso_nom as nom3 ";
		}
		$req_nom_evt = $req_nom_evt . " from perso perso1";
		if ($db->f("levt_attaquant") != '')
		{
			$req_nom_evt = $req_nom_evt . ",perso attaquant";
		}
		if ($db->f("levt_cible") != '')
		{
			$req_nom_evt = $req_nom_evt . ",perso cible";
		}
		$req_nom_evt = $req_nom_evt . " where perso1.perso_cod = " . $db->f("levt_perso_cod1") . " ";
		if ($db->f("levt_attaquant") != '')
		{
			$req_nom_evt = $req_nom_evt . " and attaquant.perso_cod = " . $db->f("levt_attaquant") . " ";
		}
		if ($db->f("levt_cible") != '')
		{
			$req_nom_evt = $req_nom_evt . " and cible.perso_cod = " . $db->f("levt_cible") . " ";
		}
		$db_detail->query($req_nom_evt);
		$db_detail->next_record();
		$texte_evt = str_replace('[perso_cod1]',$db_detail->f("nom1"),$texte_evt);
		if ($db->f("levt_attaquant") != '')
		{
			$texte_evt = str_replace('[attaquant]',$db_detail->f("nom2"),$texte_evt);
		}
		if ($db->f("levt_cible") != '')
		{
			$texte_evt = str_replace('[cible]',$db_detail->f("nom3"),$texte_evt);
		}*/



		$sortie .= $db->f('evt_date') . ' - ' . $texte_evt . '</description></item>';


	}
	$sortie .= '</channel></rss>';
	echo $sortie;
}
die('');