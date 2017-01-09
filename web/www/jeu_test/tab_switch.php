<?php 
$db = new base_delain;
/***************************************************************/
/* Fonctions pour l'affichage des barres de santé et XP        */
/***************************************************************/
// barre XP
function barre_xp($perso_px,$limite_niveau_actuel,$limite_niveau)
{
	$barre_xp = '0';
	if (($perso_px - $limite_niveau_actuel) < 0)
	{
		$barre_xp = 'negative';
		return $barre_xp;
	}
	$niveau_xp = ($perso_px - $limite_niveau_actuel);
	$div_xp = ($limite_niveau - $limite_niveau_actuel);
	$niveau_xp = (floor(($niveau_xp / $div_xp)*10))/10;

	$barre_xp = round($niveau_xp,1)*100;
	//$barre_xp =floor($niveau_xp);
	if ($barre_xp >= 100)
		$barre_xp = 100;
	return $barre_xp;
}
// barre de sante
function barre_hp($perso_pv,$perso_pv_max)
{
	$barre_hp = floor(($perso_pv/$perso_pv_max)*10)*10;
	//$barre_hp = round(($perso_pv/$perso_pv_max),1)*100;
	if($barre_hp >= 100)
	{
		$barre_hp = 100;
	}
	return $barre_hp;
}
function barre_energie($perso_energie,$barre_energie_max)
{
	$barre_energie = floor(($perso_energie/100)*10)*10;
	if($barre_energie >= 100)
	{
		$barre_energie = 100;
	}
	return $barre_energie;
}
// affichage d'un bloc perso
function affiche_perso($perso_cod)
{
	include "img_pack.php";
	global $type_flux;
	global $is_log;
	$db = new base_delain;
	$req = "select perso_cod,perso_nom,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as dlt,perso_energie
		perso_pv,perso_pv_max,dlt_passee(perso_cod) as dlt_passee,to_char(prochaine_dlt(perso_cod),'DD/MM hh24:mi') as prochaine_dlt,perso_pa,perso_race_cod,perso_sex,
		limite_niveau(perso_cod) as limite_niveau,limite_niveau_actuel(perso_cod) as limite_niveau_actuel,floor(perso_px) as perso_px,
		pos_x,pos_y,pos_etage,perso_niveau,perso_avatar,etage_libelle,perso_description,
        exists(select levt_cod from ligne_evt where levt_perso_cod1 = $perso_cod and levt_lu = 'N') as events
		from perso,perso_position,positions,etage
	where perso_cod = $perso_cod
	and ppos_perso_cod = perso_cod
	and ppos_pos_cod = pos_cod
	and perso_actif = 'O'
	and etage_numero = pos_etage ";
	$db->query($req);
	$db->next_record();
	// description
	/*$desc = str_replace("'","\'",$db->f("perso_description"));
	$desc = nl2br($desc);
	$desc = str_replace("\r","",$desc);
	$desc = str_replace("%","pourcent",$desc);
	$desc = str_replace("\n","",$desc);*/
	/*$desc = str_replace(chr(127),";",$db->f("perso_description"));*/
	$desc = nl2br(htmlspecialchars(str_replace('\'', '’', $db->f("perso_description"))));
	$pa = $db->f("perso_pa");

	$db2 = new base_delain;
	if ($db->f("perso_avatar") == '')
	{
		$avatar = G_IMAGES . $db->f("perso_race_cod") . "_" . $db->f("perso_sex") . ".png";
	}
	else
	{
		$avatar = $type_flux.G_URL . "avatars/" . $db->f("perso_avatar");
	}
	/*
	//
	// Partie permier avril
	//
	$avatar = $type_flux.G_URL . "avatars/" . $aff_avat;
	//
	// fin 1er avril
	//
	*/
	$perso_px = $db->f("perso_px");
	$limite_niveau_actuel = $db->f("limite_niveau_actuel");
	$limite_niveau = $db->f("limite_niveau");
	$barre_xp = barre_xp($perso_px,$limite_niveau_actuel,$limite_niveau);
	$barre_hp = barre_hp( $db->f("perso_pv"), $db->f("perso_pv_max"));
	$barre_energie = barre_energie( $db->f("perso_energie"), 100);
	echo '<table width="100%" border="0">
		<tr>
		<td colspan="2" class="titre" valign="top"><p class="titre">' . $db->f("perso_nom") . '</p></td></tr>
		<tr><td colspan="2" class="soustitre2"><p style="text-align:center;font-size:7pt;">' . $desc . '</td></tr>
		<tr><td class="soustitre2" colspan="2"><p>';
	if ($db->f("dlt_passee") == 1)
	{
		echo '<b>';
	}
	echo 'DLT : ' . $db->f("dlt");
	if ($db->f("dlt_passee") == 1)
	{
		echo '</b>';
	}
    echo '<br /><i>Puis ± ' , $db->f('prochaine_dlt') . '</i>';
	echo '<br></td></tr>
		<tr><td class="soustitre2" colspan="2"><p>Position : X=' . $db->f("pos_x") . '; Y=' . $db->f("pos_y") . '; ' . $db->f("etage_libelle") . '</td></tr>';
	$num_perso = $perso_cod;
	$guilde = $db2->get_nom_guilde($num_perso);
	echo '<tr><td class="soustitre2" colspan="2"><p>';
	if ($guilde == '')
	{
		echo 'Pas de guilde';
	}
	else
	{
		echo 'Guilde : ' . $guilde;
	}
	echo '</p></td></tr>
		<tr><td valign="top"><p><center><a href="#" onClick="javascript:document.login.perso.value=' . $num_perso . ';document.login.submit();"><img src="' . $avatar . '" alt="Jouer ' . $db->f("perso_nom") . '"/></a>
		<table><tr><td class="bouton" height="1" width="1"><span class="bouton">' , ($db->f('events') == 'f' ? '' : '
		<input type="button" class="bouton" onClick="javascript:window.open(\'' . $type_flux.G_URL . 'visu_derniers_evt.php?visu_perso=' . $num_perso . '&is_log=' . $is_log . '\',\'evenements\',\'scrollbars=yes,resizable=yes,width=500,height=300\');" title=\'Cliquez ici pour voir vos événements importants depuis votre dernière connexion\' value="Événements">') , '</center>
		</span></td></tr></table>
		</td>
		<td>
		<table>
		<tr><td>
		<p class="image"><b>Niveau ' . $db->f("perso_niveau") . '</b>
		</td></tr>
		<tr><td>
		<p class="image"><img src="' . G_IMAGES . 'barrepa_' . $pa . '.gif" alt="' . $pa . 'PA">
		</td></tr>
		<tr><td>
		<p class="image"><img src="' . G_IMAGES . 'coeur.gif"> <img src="' . G_IMAGES . 'hp' . $barre_hp . '.gif" title="' . $db->f("perso_pv") . 'PV sur ' . $db->f("perso_pv_max") . '">
		</td></tr>';
$is_enchanteur = $db->is_enchanteur($perso_cod);
if($is_enchanteur)
{
		echo '	<tr><td>
		<p class="image"><img src="' . G_IMAGES . 'energi10.png"> <img src="' . G_IMAGES . 'energie' . $barre_energie . '.png" title="' . $db->f("perso_energie") . 'PV sur 100">
		</td></tr>
		<tr><td>';
}	
	echo '	<tr><td>
		<p class="image"><img src="' . G_IMAGES . 'iconexp.gif"> <img src="' . G_IMAGES . 'xp' . $barre_xp . '.gif" title="' . $db->f("perso_px") . ' PX, prochain niveau à ' . $db->f("limite_niveau") . '">
		</td></tr>
		<tr><td>';
	//
	// Messages
	//
	$req_msg = "select count(*) as nombre from messages_dest where dmsg_perso_cod = $num_perso
		and dmsg_lu = 'N' and dmsg_archive = 'N' ";
	$db2->query($req_msg);
	$db2->next_record();
	$nb_msg = $db2->f("nombre");
	if ($nb_msg != 0)
	{
		echo '<p>' . $nb_msg . ' messages non lus.<br>';
	}
	//
	// Transactions
	//
	$req_tran = "select * from transaction where (tran_vendeur = $num_perso or tran_acheteur = $num_perso)";
	$db2->query($req_tran);
	$nb_tran = $db2->nf();
	if ($nb_tran != 0)
	{
		echo '<p>' . $nb_tran . ' transactions en attente.<br>';
	}
	echo '</td></tr></table>';
}
/***************************************************************/
/* Fin des fonctions                                           */
/***************************************************************/
//
/***************************************************************/
/* Début de la page                                            */
/***************************************************************/
$req = 'select compt_ligne_perso from compte where compt_cod = ' . $compt_cod;
$db->query($req);
$db->next_record();
$nb_perso_max = $db->f('compt_ligne_perso') * 3;
/*********************/
/* Persos classiques */
/*********************/
$req_perso = "select pcompt_perso_cod
	from perso,perso_compte
	where pcompt_compt_cod = $compt_cod
	and pcompt_perso_cod = perso_cod
	and perso_actif = 'O'
	and perso_type_perso = 1
	order by perso_cod ";
$db->query($req_perso);
$nb_perso = $db->nf();
$alias_perso = 0;
for ($cpt=0;$cpt<$nb_perso_max;$cpt++)
{
	if ($cpt<$nb_perso)
	{
		$db->next_record();
	}
	if (fmod($cpt,3) == 0)
	{
		echo '<tr>';
	}
	echo '<td valign="top" width="33%">';

	//tableau intérieur
	if ($cpt>=$nb_perso)
	{
		$nom = 'Pas de perso';
		$image = '';
		$barre_pa = '';
		$barre_hp = '';
		$barre_xp = '';
		$enc = '';
		echo '<table width="100%" height="100%" border="0">
			<tr><td height="100%" valign="center" class="soustitre2" style="text-align:center;"><p>Pas de personnage<br></p></td></tr>
			<tr><td height="100%" valign="center"><p>&nbsp;<br></p></td></tr>
			<tr><td height="100%" valign="center"><p>&nbsp;<br></p></td></tr>
			<tr><td><center><a href="' , $type_flux.G_URL , 'cree_perso_compte.php?compt_cod=' . $compt_cod . '"><img src="' . G_IMAGES . 'noperso.gif"></a></center></td></tr>';

	}
	else
	{
		affiche_perso($db->f('pcompt_perso_cod'));
	}
	echo '</td></tr></table>';
	//fin tableau intérieur

	echo '</td>';
	if (fmod(($cpt+1),3) == 0)
	{
		echo '</tr>';
	}
}
/*************/
/* Familiers */
/*************/
$req_perso = "select pfam_familier_cod,perso_cod
	from perso,perso_compte,perso_familier
	where pcompt_compt_cod = $compt_cod
	and pcompt_perso_cod = pfam_perso_cod
	and pfam_familier_cod = perso_cod
	and perso_actif = 'O'
	and perso_type_perso = 3
	order by pfam_perso_cod ";
$db->query($req_perso);
if ($db->nf() != 0)
{
	echo '<tr><td colspan="3"><hr><p class="titre">Familiers : </p></td></tr>';
	$nb_perso = $db->nf();
	$alias_perso = 0;
	for ($cpt=0;$cpt<$nb_perso_max;$cpt++)
	{
		if ($cpt<$nb_perso)
		{
			$db->next_record();
		}
		if (fmod($cpt,3) == 0)
		{
			echo '<tr>';
		}
		echo '<td valign="top" width="33%">';

		//tableau intérieur
		if ($cpt>=$nb_perso)
		{
			?>
			<table width="100%" height="100%" border="0">
			<?php 
		}
		else
		{
			affiche_perso($db->f('perso_cod'));
		}
		echo '</td></tr></table>';
		//fin tableau intérieur

		echo '</td>';
		if (fmod(($cpt+1),3) == 0)
		{
			echo '</tr>';
		}
	}
}
/******************************************/
/* Comptes sittés ?                       */
/******************************************/
$req_perso = "select pcompt_perso_cod
	from perso,perso_compte,compte_sitting
	where csit_compte_sitteur = $compt_cod
	and csit_compte_sitte = pcompt_compt_cod
	and csit_ddeb <= now()
	and csit_dfin >= now()
	and pcompt_perso_cod = perso_cod
	and perso_actif = 'O'
	and perso_type_perso = 1
	order by perso_cod ";
$db->query($req_perso);
if($db->nf() != 0)
{
	//
	// là on a des persos sittés, donc, on va quand même regarder ce qui se passe
	//
	echo '<tr><td colspan="3"><hr><p class="titre">Persos sittés : </p></td></tr>';
	$nb_perso_max = $db->nf();
	$nb_perso = $nb_perso_max;
	for ($cpt=0;$cpt<$nb_perso_max;$cpt++)
	{
		if ($cpt<$nb_perso)
		{
			$db->next_record();
		}
		if (fmod($cpt,3) == 0)
		{
			echo '<tr>';
		}
		echo '<td valign="top" width="33%">';

		//tableau intérieur
		if ($cpt>=$nb_perso)
		{
			$nom = 'Pas de perso';
			$image = '';
			$barre_pa = '';
			$barre_hp = '';
			$barre_xp = '';
			$enc = '';
			echo '<table width="100%" height="100%" border="0">
				<tr><td height="100%" valign="center" class="soustitre2" style="text-align:center;"><p>Pas de personnage<br></p></td></tr>
				<tr><td height="100%" valign="center"><p>&nbsp;<br></p></td></tr>
				<tr><td height="100%" valign="center"><p>&nbsp;<br></p></td></tr>
				<tr><td><center><img src="' . G_IMAGES . 'noperso.gif"></center></td></tr>';
		}
		else
		{
			affiche_perso($db->f('pcompt_perso_cod'));
		}
		echo '</td></tr></table>';
		//fin tableau intérieur

		echo '</td>';
		if (fmod(($cpt+1),3) == 0)
		{
			echo '</tr>';
		}
	}
	//
	// bon, on sait qu'on a sitté des persos, maintenant, on va quand même voir s'il y a des familiers
	//
	$req_perso = "select pfam_familier_cod,perso_cod
		from perso,perso_compte,perso_familier,compte_sitting
		where csit_compte_sitteur = $compt_cod
		and csit_compte_sitte = pcompt_compt_cod
		and csit_ddeb <= now()
		and csit_dfin >= now()
		and pcompt_perso_cod = pfam_perso_cod
		and pfam_familier_cod = perso_cod
		and perso_actif = 'O'
		and perso_type_perso = 3
		order by pfam_perso_cod ";
	$db->query($req_perso);
	if ($db->nf() != 0)
	{

		$nb_perso_max = $db->nf();
		$nb_perso = $nb_perso_max;
		$alias_perso = 0;
		for ($cpt=0;$cpt<$nb_perso_max;$cpt++)
		{
			if ($cpt<$nb_perso)
			{
				$db->next_record();
			}
			if (fmod($cpt,3) == 0)
			{
				echo '<tr>';
			}
			echo '<td valign="top" width="33%">';
			echo '<!--' . $cpt . '-' . $nb_perso_max . '-' . $nb_perso . '-->';
			//tableau intérieur
			if ($cpt>=$nb_perso)
			{
				?>
				Pas de perso
				<?php 
			}
			else
			{
				affiche_perso($db->f('perso_cod'));
			}
			echo '</td></tr></table>';
			//fin tableau intérieur

			echo '</td>';
			if (fmod(($cpt+1),3) == 0)
			{
				echo '</tr>';
			}
		}
	}
}
?>
