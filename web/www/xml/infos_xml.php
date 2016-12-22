<?php 
/************************************************************/
/* infos.php                                                */
/* g�n�re un fichier xml avec des infos, r�cup�rables       */
/*  par divers outils                                       */
/************************************************************/
header ('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header ('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?><delain>';
include 'classes.php';
$db = new base_delain;
//
// recherche du compte
//
$req = 'select compt_cod,compt_hibernation from compte
	where compt_nom = \'' . $nom . '\'
	and md5(compt_password) = \'' . $pass . '\'
	and compt_actif = \'O\'';
$db->query($req);
if($db->nf() == 0)
{
	echo '<information>Compte non trouv� </information></delain>';
	die();
}
else
{
	$db->next_record();
	if ($db->f('compt_hibernation') == 1)
	{
		echo '<information>Compte en hibernation</information></delain>';
		die();
	}
	$num_compte = $db->f('compt_cod');
	//
	// recherche des persos et infos qui vont avec
	//
	$db2 = new base_delain;
	$req_perso = "select pcompt_perso_cod,perso_nom,to_char(perso_dlt,'DD/MM hh24:mi') as dlt,
		perso_pv,perso_pv_max,perso_pa,perso_race_cod,perso_sex,dlt_passee(perso_cod) as dlt_passee
		from perso,perso_compte 
		where pcompt_compt_cod = " . $num_compte . "
		and pcompt_perso_cod = perso_cod 
		and perso_actif = 'O' 
		and perso_type_perso = 1 
		order by perso_cod ";
	$db->query($req_perso);
	$texte = '';
	$nb_perso = 0;
	while($db->next_record())
	{
		$nb_perso ++;
		if($db->f('dlt_passee') == 1)
			$dltp = 'Oui';
		else
			$dltp = 'Non';
		$texte .= '<perso numero="' . $db->f('pcompt_perso_cod') . '" nom="' . $db->f('perso_nom') . '">
			<pv valeur="' . $db->f('perso_pv') . '" />
			<pvmax valeur="' . $db->f('perso_pv_max') . '" />
			<pa valeur="' . $db->f('perso_pa') . '" />
			<dlt valeur="' . $db->f('dlt') . '" />
			<dlt_passee valeur="' . $dltp . '" />';
		$db2->query("select count(dmsg_cod) as nombre from messages_dest where dmsg_perso_cod = " . $db->f('pcompt_perso_cod') . 
			"and dmsg_lu = 'N' and dmsg_archive = 'N'");
		$db2->next_record();
		$texte .= '<msg_non_lus valeur="' . $db2->f('nombre') . '" />';
		$db2->query("select count(levt_cod) as nombre from ligne_evt where levt_perso_cod1 = " . $db->f('pcompt_perso_cod') . " and levt_lu = 'N'");
		$db2->next_record();
		$texte .= '<evt_non_lus valeur="' . $db2->f('nombre') . '" />';
		$texte .= '</perso>';
	}
	
	$req_perso = "select pfam_familier_cod,perso_cod,perso_nom,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as dlt,
		perso_pv,perso_pv_max,dlt_passee(perso_cod) as dlt_passee,perso_pa
		from perso,perso_compte,perso_familier 
		where pcompt_compt_cod = " . $num_compte . "
		and pcompt_perso_cod = pfam_perso_cod 
		and pfam_familier_cod = perso_cod 
		and perso_actif = 'O' 
		and perso_type_perso = 3 
		order by pfam_perso_cod ";
	$db->query($req_perso);
	$nb_fam = 0;
	while($db->next_record())
	{
		$nb_fam ++;
		if($db->f('dlt_passee') == 1)
			$dltp = 'Oui';
		else
			$dltp = 'Non';
		$texte .= '<familier numero="' . $db->f('perso_cod') . '" nom="' . $db->f('perso_nom') . '">
			<pv valeur="' . $db->f('perso_pv') . '" />
			<pvmax valeur="' . $db->f('perso_pv_max') . '" />
			<pa valeur="' . $db->f('perso_pa') . '" />
			<dlt valeur="' . $db->f('dlt') . '" />
			<dlt_passee valeur="' . $dltp . '" />';
		$db2->query("select count(dmsg_cod) as nombre from messages_dest where dmsg_perso_cod = " . $db->f('perso_cod') . 
			"and dmsg_lu = 'N' and dmsg_archive = 'N'");
		$db2->next_record();
		$texte .= '<msg_non_lus valeur="' . $db2->f('nombre') . '" />';
		$db2->query("select count(levt_cod) as nombre from ligne_evt where levt_perso_cod1 = " . $db->f('perso_cod') . " and levt_lu = 'N'");
		$db2->next_record();
		$texte .= '<evt_non_lus valeur="' . $db2->f('nombre') . '" />';
		$texte .= '</familier>';
	}
	$texte = '<compte numero="' . $num_compte . '">
	<nb_perso valeur="' . $nb_perso . '" /><nb_fam valeur="' . $nb_fam . '" />' . $texte . '</compte>';
}
echo $texte;
echo '</delain>';