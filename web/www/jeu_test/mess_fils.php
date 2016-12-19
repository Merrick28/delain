<?php 
if(!isset($detail))
	$detail = 0;
switch($detail)
{
	case 0:
		$nb_messages_page = 20;
		if (!isset($msg_start))	// offset des messages
			$msg_start = 0;
		if ($msg_start < 0)
			$msg_start = 0;
		// requete pour messages dest
		$req_messages = "select to_char(msg1.msg_date2,'DD/MM/YYYY hh24:mi:ss') as date_mes,msg1.msg_init as msg_init,perso_nom,msg1.msg_titre as msg_titre,dmsg_lu,msg1.msg_cod as msg_cod,dmsg_cod,emsg_perso_cod,
		(select count(msg2.msg_cod) from messages msg2,messages_dest dmsg2
			where msg2.msg_init = msg1.msg_init
				and msg2.msg_cod = dmsg2.dmsg_msg_cod
			 and dmsg2.dmsg_perso_cod = " . $perso_cod  . "
			 and dmsg_archive = 'N'
			 and dmsg_efface = 0) as nb_fil
				from perso,messages msg1,messages_exp,messages_dest
				where dmsg_perso_cod = " . $perso_cod  . '
				and dmsg_archive = \'N\'
				and dmsg_msg_cod = msg1.msg_cod
				and emsg_msg_cod = msg1.msg_cod
				and emsg_perso_cod = perso_cod
				and dmsg_efface = 0
				and msg1.msg_cod = msg1.msg_init
				order by msg_cod desc
				limit ' . $nb_messages_page . '
				offset  ' . $msg_start;

		$req_total = "select count(msg_cod) as nb_msg
			from perso,messages,messages_exp,messages_dest
			where dmsg_perso_cod = " . $perso_cod  . '
			and dmsg_archive = \'N\'
			and dmsg_msg_cod = msg_cod
			and emsg_msg_cod = msg_cod
			and dmsg_efface = 0
			and msg_cod = msg_init
			and emsg_perso_cod = perso_cod ';

		// calcul pour pages
		$db->query($req_total);
		$db->next_record();
		$nb_total = $db->f('nb_msg');
		$nb_pages = ceil($nb_total/$nb_messages_page);
		$page_en_cours = ($msg_start/$nb_messages_page) + 1;
		// fin calcul pour pages


		$db->query($req_messages);
		$contenu_page .= '
		<form name="message" method="post" action="messagerie2.php">
		<input type="hidden" name="methode">
		<input type="hidden" name="m" value="' . $m . '">
		<table cellspacing="2" width="100%" style="table_layout:auto;">
		<tr>
		<td width="150"><div class="titre">Date</div></td>
		<td style="width:100px;"><div class="titre">Expéditeur</div></td>
		<td><div class="titre">Titre</div></td>
		<td width="20"></td>
		<td width="20"></td>
		<td width="20"></td>
		</tr>';
		$cpt=0;
		while($db->next_record())
		{
			$contenu_page .= '<tr>';
			$contenu_page .= '<td  class="soustitre2">' . $db->f("date_mes") . '</td>';
			$contenu_page .= '<td  style="width:100px;" nowrap class="soustitre2"><a href="visu_desc_perso.php?visu=' . $db->f("emsg_perso_cod") . '">' . $db->f("perso_nom") . '</a></td>';
			$contenu_page .= '<td><div style="white-space:nowrap;">';
			if ($db->f("dmsg_lu") == "N")
			{
				$contenu_page .= '<b>';
			}

			$contenu_page .= '<a  href="messagerie2.php?m=0&fil=' . $db->f('msg_init') . '">' . str_replace(chr(127), ';', $db->f("msg_titre")) . ' (' . $db->f('nb_fil') . ')</a>';
			if ($db->f("dmsg_lu") == "N")
			{
				$contenu_page .= '</b>';
			}
			$contenu_page .= '</div></td>';


			$contenu_page .= '</tr>';
			$cpt ++;
		}
		// listing des pages
		$contenu_page .= '<tr><td colspan="6">Page en cours : ';
		for($cpt=1;$cpt<=$nb_pages;$cpt++)
		{
			$v_debut = ($cpt-1)*$nb_messages_page;
			if ($cpt == $page_en_cours)
			{
				$contenu_page .= '<font class="soustitre2"><b>' . $page_en_cours . '</b></font> &nbsp;&nbsp;';
			}
			else
			{
				$contenu_page .= '<a href="' . $PHP_SELF . '?msg_start=' . $v_debut . '&m=' . $m . '&sort=' . $sort . '">' . $cpt . '</a> &nbsp;&nbsp;';
			}
		}


		$contenu_page .= '</td></tr>';
		// fin listing des pages



		$contenu_page .= '</table>
		</form>';
	break;
}
?>
