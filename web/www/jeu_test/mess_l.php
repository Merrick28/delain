<?php 
$a_sort[0] = 'fil';
$a_sort[1] = 'date';
$n_sort[0] = 1;
$n_sort[1] = 0;
if(!isset($sort))
	$sort = 0;
if(!isset($fil))
	$fil = 0;
$nb_messages_page = 20;
if (!isset($msg_start))	// offset des messages
	$msg_start = 0;
if ($msg_start < 0)
	$msg_start = 0;

$disparu = '<i>-- Personnage disparu --</i>';
$archive = ($m == 0) ? 'N' : 'O';
$where_fil = ($m == 0 && $fil != 0) ? " and msg_init = $fil" : '';
$orderby = ($sort == 0) ? 'msg_cod desc' : 'msg_init desc, msg_cod desc';

$req_messages = "select to_char(msg_date2,'DD/MM/YYYY hh24:mi:ss') as date_mes,
		msg_titre, dmsg_lu, msg_cod, dmsg_cod,
		coalesce(emsg_perso_cod, -1) as emsg_perso_cod, 
		coalesce(perso_nom, '$disparu') as perso_nom
	from messages_dest
	inner join messages on msg_cod = dmsg_msg_cod
	left outer join messages_exp on emsg_msg_cod = dmsg_msg_cod
	left outer join perso on perso_cod = emsg_perso_cod
	where dmsg_perso_cod = $perso_cod
		and dmsg_archive = '$archive'
		and dmsg_efface = 0
		$where_fil
	order by $orderby
	limit $nb_messages_page
	offset $msg_start";

$req_total = "select count(msg_cod) as nb_msg
	from messages_dest
	inner join messages on msg_cod = dmsg_msg_cod
	left outer join messages_exp on emsg_msg_cod = dmsg_msg_cod
	left outer join perso on perso_cod = emsg_perso_cod
	where dmsg_perso_cod = $perso_cod
		and dmsg_archive = '$archive'
		and dmsg_efface = 0";

// calcul pour pages
$db->query($req_total);
$db->next_record();
$nb_total = $db->f('nb_msg');
$nb_pages = ceil($nb_total/$nb_messages_page);
$page_en_cours = ($msg_start/$nb_messages_page) + 1;
// fin calcul pour pages


$db->query($req_messages);
$contenu_page .= '
<form name="message" method="post" action="action_message.php">
<input type="hidden" name="methode">
<input type="hidden" name="m" value="' . $m . '">
<table cellspacing="2" width="100%" style="table_layout:auto;">
<tr>
<td width="150"><p class="titre">Date</p></td>
<td style="width:100px;"><p class="titre">Expéditeur</p></td>
<td><p class="titre">Titre</p></td>
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
	$contenu_page .= '<td><span style="white-space:nowrap;">';
	if ($db->f("dmsg_lu") == "N")
	{
		$contenu_page .= '<b>';
	}

	$contenu_page .= '<a  href="action_message.php?methode=visu_msg&m=' . $m . '&mid=' . $db->f("msg_cod") . '">' . str_replace(chr(127), ';', $db->f("msg_titre") ) . '</a>';
	if ($db->f("dmsg_lu") == "N")
	{
		$contenu_page .= '</b>';
	}
	$contenu_page .= '</td>';
	$contenu_page .= '<td width="20">';
	if ($compt_cod != 'admin')
	{
		$contenu_page .= '<a href="action_message.php?m=' . $m . '&methode=efface_vue generale_msg&mid=' . $db->f("dmsg_cod") . '">Effacer</a>';
	}
	$contenu_page .= '</td>';
	$contenu_page .= '<td width="20">';
	if (($compt_cod != 'admin') && ($m == 0))
	{
		$contenu_page .= '<a href="action_message.php?m=' . $m . '&methode=archive__vue generale_msg&mid=' . $db->f("dmsg_cod") . '">Archiver</a>';
	}
	$contenu_page .= '</td>';
	$contenu_page .= '<td width="20"><input type="checkbox" class="vide" name="msg[' . $cpt . ']" value="' . $db->f("dmsg_cod") . '">';
	$contenu_page .= '</tr>';
	$cpt ++;
}
$contenu_page .= '<tr><td colspan="5"></td><td></td></tr>

<tr>
<td class="soustitre2"><a href="' . $PHP_SELF .'?sort=' . $n_sort[$sort] . '&m=' . $m . '">Trier par ' . $a_sort[$sort] . ' ?</a></td>
<td class="soustitre2" style="width:100px;"><a style="white-space:nowrap;" href="javascript:document.message.methode.value=\'select_non_lu\';document.message.submit();">Marquer comme non lus</a></td>

<td class="soustitre2"><a href="action_message.php?m=' . $m . '&methode=tout_lu">Tout marquer comme lu</a></td>
<td class="soustitre2" width="20"><a href="javascript:document.message.methode.value=\'select_efface\';document.message.submit();">Effacer la sélection</a></td>
<td class="soustitre2" width="20">';
if($m==0)
	$contenu_page .= '<a href="javascript:document.message.methode.value=\'select_archive\';document.message.submit();">Archiver la sélection</a>';
$contenu_page .= '
	</td>
	<td width="20"><a style="font-size:7pt;text-align:center;" href="javascript:toutCocher(document.message,\'msg\');">cocher<br>décocher<br>inverser</a></td>
	</tr>';
// listing des pages
$contenu_page .= '<tr><td colspan="6">Page en cours : ';
for($cpt = 1; $cpt <= $nb_pages; $cpt++)
{
	$v_debut = ($cpt - 1) * $nb_messages_page;
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
?>
