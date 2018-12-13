<?php 
if(!isset($is_log))
	$is_log = 'N';
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
// barre d'énergie
function barre_energie($perso_energie)
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
	global $is_log;
	global $type_flux;
	//include "img_pack.php";
	$db = new base_delain;
	$req = "select perso_cod,perso_nom,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as dlt,perso_energie,
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
	$desc = str_replace(chr(128),";",$db->f("perso_description"));
	$desc = str_replace(chr(127),";",$desc);
	$pa = $db->f("perso_pa");

	$db2 = new base_delain;
	if ($db->f("perso_avatar") == '')
	{
		$avatar = G_IMAGES . $db->f("perso_race_cod") . "_" . $db->f("perso_sex") . ".gif";
	}
	else
	{
		$avatar = $type_flux . G_URL . "avatars/" . $db->f("perso_avatar");
	}
	$perso_px = $db->f("perso_px");
	$limite_niveau_actuel = $db->f("limite_niveau_actuel");
	$limite_niveau = $db->f("limite_niveau");
	$energie =  $db->f("perso_energie");
	$barre_xp = barre_xp($perso_px,$limite_niveau_actuel,$limite_niveau);
	$barre_hp = barre_hp( $db->f("perso_pv"), $db->f("perso_pv_max"));
	$barre_energie = barre_energie( $db->f("perso_energie"));
	//echo $db->f("perso_nom") ;
	echo '<div id="nom_perso" class="titre">' . $db->f("perso_nom") . '</div><br />';
	echo '<div id="description" style="text-align:center;font-size:7pt;">' . $desc . '</div><br />';
	echo '<div style="white-space:nowrap;" id="dlt">';
	if ($db->f("dlt_passee") == 1)
	{
		echo '<strong>';
	}
	echo 'DLT : ' . $db->f("dlt");
	if ($db->f("dlt_passee") == 1)
	{
		echo '</strong>';
	}
	  echo '<br /><i>Puis ± ' , $db->f('prochaine_dlt') . '</i>';
    echo '</div><br />';
	echo '<div id="position">Position : X=' . $db->f("pos_x") . '; Y=' . $db->f("pos_y") . '; ' . $db->f("etage_libelle") . '</div>';
	$num_perso = $perso_cod;
	$guilde = $db2->get_nom_guilde($num_perso);
	echo '<div id="guilde">';
	if ($guilde == '')
	{
		echo 'Pas de guilde';
	}
	else
	{
		echo 'Guilde : ' . $guilde;
	}
	echo '</div>';
	echo '<div id="login" style="text-algin:center;"><a href="#" onClick="javascript:document.login.perso.value=' . $num_perso . ';document.login.submit();"><img src="' . $avatar . '" alt="Jouer ' . $db->f("perso_nom") . '"/></a></div>';
	if ($db->f('events') == 'f')
	{
		echo '<span class="bouton"><input type="button" class="bouton" onClick="javascript:window.open(\'' . $type_flux . G_URL . 'visu_derniers_evt.php?visu_perso=' . $num_perso . '&is_log=' . $is_log . '\',\'evenements\',\'scrollbars=yes,resizable=yes,width=500,height=300\');" title=\'Cliquez ici pour voir vos événements importants depuis votre dernière connexion\' value="Événements" />';
	}
	echo '
		<div class="image"><strong>Niveau ' . $db->f("perso_niveau") . '</strong></div>
		<div class="image"><img src="' . G_IMAGES . 'barrepa_' . $pa . '.gif" alt="' . $pa . 'PA"></div>
		<div class="image"><img src="' . G_IMAGES . 'coeur.gif" alt=""> <img src="' . G_IMAGES . 'hp' . $barre_hp . '.gif" title="' . $db->f("perso_pv") . 'PV sur ' . $db->f("perso_pv_max") . '" alt="' . $db->f("perso_pv") . 'PV sur ' . $db->f("perso_pv_max") . '"></div>';
	$is_enchanteur = $db->is_enchanteur($perso_cod);
	if($is_enchanteur)
	{
		echo '<div class="image"><img src="' . G_IMAGES . 'energi10.png" alt=""> <img src="' . G_IMAGES . 'nrj' . $barre_energie . '.png" title="' . $energie . ' sur 100" alt="' . $energie . ' sur 100"></div>';
	}
	echo '<div class="image"><img src="' . G_IMAGES . 'iconexp.gif" alt=""> <img src="' . G_IMAGES . 'xp' . $barre_xp . '.gif" title="' . $perso_px . ' PX, prochain niveau à ' . $limite_niveau . '" alt="' . $perso_px . ' PX sur ' . $limite_niveau . '"></div>';
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
		echo '<div id="messages">' . $nb_msg . ' messages non lus.</div>';
	}
	//
	// Transactions
	//
	$req_tran = "select * from transaction where (tran_vendeur = $num_perso or tran_acheteur = $num_perso)";
	$db2->query($req_tran);
	$nb_tran = $db2->nf();
	if ($nb_tran != 0)
	{
		echo '<div id="transactions">' . $nb_tran . ' transactions en attente.</div>';
	}
	//echo '</div>';
	//	echo '</td></tr></table>';
}
/***************************************************************/
/* Fin des fonctions                                           */
/***************************************************************/
//
/***************************************************************/
/* Début de la page                                            */
/***************************************************************/
$req = "select compt_ligne_perso, compt_quatre_perso, to_char(compt_dcreat,'YYYY-MM-DD / hh24:mi') as date_creation,to_char(now()-'24 months'::interval,'YYYY-MM-DD / hh24:mi') as maintenant from compte where compt_cod = " . $compt_cod;
$db->query($req);
$db->next_record();
$nb_perso_max = $db->f('compt_ligne_perso') * 3;
$nb_perso_ligne = 3;
if ($db->f('date_creation') < $db->f('maintenant') && $db->f('compt_quatre_perso') != 'N')
{
	$nb_perso_max = $db->f('compt_ligne_perso') * 4;
	$nb_perso_ligne = 4;

}
$taille = 100/$nb_perso_ligne;

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
echo '<span="perso" style="white-space:nowrap;">';
for ($cpt=0;$cpt<$nb_perso_max;$cpt++)
{
	if ($cpt<$nb_perso)
	{
		$db->next_record();
	}
	//tableau intérieur
	if ($cpt>=$nb_perso)
	{
		$nom = 'Pas de perso';
		$image = '';
		$barre_pa = '';
		$barre_hp = '';
		$barre_xp = '';
		$enc = '';
		echo '<div class="bordiv"  style="float:left;">Pas de personnage<br />
			<a href="' , $type_flux . G_URL , 'cree_perso_compte.php?compt_cod=' . $compt_cod . '"><img src="' . G_IMAGES . 'noperso.gif" alt="Créer un nouveau"></a></div>';

	}
	else
	{
		echo '<div class="bordiv" id="perso' . $cpt . '" style="float:left;">';
		affiche_perso($db->f('pcompt_perso_cod'));
		echo '</div>';
	}
	//echo '</div>';
	//fin tableau intérieur

	//echo '</td>';
	if (fmod(($cpt+1),$nb_perso_ligne) == 0)
	{
		echo '</br>';
	}
}
echo "</div>";
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
	echo '<tr><td colspan="3"><hr><div class="titre">Familiers : </div></td></tr>';
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
			echo '<br />';
		}
		
		//tableau intérieur
		if ($cpt>=$nb_perso)
		{
			?>
			<div id="familiers">
			<?php 
		}
		else
		{
			echo '<div class="bordiv" id="perso' . $cpt . '" style="float:left;">';
			affiche_perso($db->f('pcompt_perso_cod'));
			echo '</div>';
		}
		echo '</div>';
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
	echo '<tr><td colspan="3"><hr><div class="titre">Persos sittés : </div></td></tr>';
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
			echo '<table width="100%" height="100%" border="1">
				<tr><td height="100%" valign="center" class="soustitre2" style="text-align:center;">Pas de personnage<br></td></tr>
				<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
				<tr><td height="100%" valign="center">&nbsp;<br></td></tr>
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
