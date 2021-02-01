<?php 
$nb_messages_page = 20;
if (!isset($msg_start))	// offset des messages
	$msg_start = 0;
if ($msg_start < 0)
	$msg_start = 0;
$archive['N'] = 'Non';
$archive['O'] = 'Oui';
$contenu_page .= '
<table cellspacing="2">
<tr>
<td class="titre"><div class="titre">Date</div></td>
<td class="titre"><div class="titre">Titre</div></td>
</tr>';
// total messages
$req_total = "select count(msg_cod) as nb_msg
	from messages,messages_exp
	where emsg_perso_cod = " . $perso_cod  . '
	and emsg_msg_cod = msg_cod ';
$stmt = $pdo->query($req_total);
$result = $stmt->fetch();
$nb_total = $result['nb_msg'];
$nb_pages = ceil($nb_total/$nb_messages_page);
$page_en_cours = ($msg_start/$nb_messages_page) + 1;
// fin total message
$req_messages = "select to_char(msg_date2,'DD/MM/YYYY hh24:mi:ss') as datemes,msg_titre,emsg_archive,msg_cod,emsg_cod
	from messages,messages_exp
	where emsg_perso_cod = " . $perso_cod  . '
	and emsg_msg_cod = msg_cod
	order by msg_cod desc
	limit ' . $nb_messages_page . '
	offset ' . $msg_start;
$stmt = $pdo->query($req_messages);
while($result = $stmt->fetch())
{
	$contenu_page .= '<tr>';
	$contenu_page .= '<td class="soustitre2">' . $result['datemes'] . '</td>';
	$contenu_page .= '<td class="soustitre2"><a href="action_message.php?mid=' . $result['msg_cod'] . '&m=' . $m . '&methode=visu_msg">' . str_replace(chr(127), ';', $result['msg_titre']) . '</a></td>';
	$type_archive = $result['emsg_archive'];
	$contenu_page .= '</tr>';
}
// listing des pages
$contenu_page .= '<tr><td colspan="6">Page en cours : ';
for($cpt=1;$cpt<=$nb_pages;$cpt++)
{
	$v_debut = ($cpt-1)*20;
	if ($cpt == $page_en_cours)
	{
		$contenu_page .= '<font class="soustitre2"><strong>' . $page_en_cours . '</strong></font> &nbsp;&nbsp;';
	}
	else
	{
        $contenu_page .= '<a href="' . $_SERVER['PHP_SELF'] . '?msg_start=' . $v_debut . '&m=' . $m . '">' . $cpt . '</a> &nbsp;&nbsp;';
    }
}


$contenu_page .= '</td></tr>';
// fin listing des pages
$contenu_page .= '</table>';

