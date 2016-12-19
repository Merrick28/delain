<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
if (!$db->is_admin($compt_cod))
{
	$contenu_page .= 'Vous n\'avez pas accès à cette page !';
}
else
{
	if(!isset($methode))
		$methode = 'global';
	switch($methode)
	{
		case "detail":
			$req = 'select compt_nom,hlog_compte,to_char(hlog_date,\'YYYY/MM/DD hh24:mi:ss\') as hlog_date,hlog_ip
				from histo_log,compte
				where hlog_id = \'' . $id . '\'
				and compt_cod = hlog_compte order by hlog_date desc';
			$db->query($req);
			if($db->nf() == 0)
				$contenu_page .= '<p>Aucune connexion enregistrée.';
			else
			{
				$contenu_page .= '<table>
					<tr><td class="soustitre2"><b>Compte</b></td><td class="soustitre2"><b>Date</b></td><td class="soustitre2"><b>IP</b></td>';
				while($db->next_record())
					$contenu_page .= '<tr><td class="soustitre2"><a href="detail_compte.php?compte=' . $db->f('hlog_compte') . '">' . $db->f('compt_nom') . '</a></td><td>' . $db->f('hlog_date') . '</td><td class="soustitre2">' . $db->f('hlog_ip') . '</td></tr>';
				$contenu_page .= '</table>';
			}
			$contenu_page .= '<p><a href="' . $PHP_SELF . '?methode=global&id=' . $id . '">Voir le global</a>';
			break;
		case "global":
		$req = 'select compt_nom,hlog_compte,count(hlog_id) as nombre,to_char(min(hlog_date),\'YYYY/MM/DD\') as date_min,to_char(max(hlog_date),\'YYYY/MM/DD\') as date_max 
				from histo_log,compte
				where hlog_id = \'' . $id . '\'
				and compt_cod = hlog_compte 
				group by compt_nom,hlog_compte';
			$db->query($req);
			if($db->nf() == 0)
				$contenu_page .= '<p>Aucune connexion enregistrée.';
			else
			{
				$contenu_page .= '<table>
					<tr><td class="soustitre2"><b>Compte</b></td>
					
					<td class="soustitre2"><b>Nombre</b></td>
					<td class="soustitre2"><b>Date Min</b></td>
					<td class="soustitre2"><b>Date Max</b></td>';
				while($db->next_record())
					$contenu_page .= '<tr><td class="soustitre2"><a href="detail_compte.php?compte=' . $db->f('hlog_compte') . '">' . $db->f('compt_nom') . '</a></td>
					<td class="soustitre2">' . $db->f('nombre') . '</td>
					<td>' . $db->f('date_min') . '</td>
					<td>' . $db->f('date_max') . '</td></tr>';
				$contenu_page .= '</table>';
			}
			$contenu_page .= '<p><a href="' . $PHP_SELF . '?methode=detail&id=' . $id . '">Voir le détail</a>';
			break;
	
	
	
	
	}
	
	
	
}
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
