<?php require_once "classes.php";
require_once 'includes/template.inc';
require_once "ident.php";

$t = new template;
$t->set_file('FileRef', 'template/delain/sans_menu.tpl');
// chemins
$t->set_var('URL', $type_flux.G_URL);

$contenu_page = '';
$titre_page = '';

$erreur = empty($visu_perso) || empty($compt_cod);
if (!$erreur)
{
	$visu_perso = $db->format($visu_perso);
	$req_compte = "select pcompt_compt_cod from perso_compte
		where pcompt_perso_cod = $visu_perso
			or pcompt_perso_cod = ( select pfam_perso_cod from perso_familier where pfam_familier_cod = $visu_perso)";
	$compte_trouve = $db->get_value($req_compte, 'pcompt_compt_cod');

	$erreur = ($compte_trouve === false || $compte_trouve != $compt_cod);

	if ($erreur)	// on est peut-être dans le cas d’un sitting
	{
		$req_sitting = "select csit_compte_sitteur from compte_sitting
			inner join perso_compte on pcompt_compt_cod = csit_compte_sitte
			where pcompt_perso_cod = $visu_perso
				and now() between csit_ddeb and csit_dfin";
		$compte_sitting = $db->get_value($req_sitting, 'csit_compte_sitteur');

		$erreur = ($compte_sitting === false || $compte_sitting != $compt_cod);
	}
}
if ($erreur)
{
	$titre_page = 'Erreur';
	$contenu_page = 'Erreur d’authentification !';
}

if (!$erreur && empty($visu_msg))	// Liste des messages
{
	$titre_page = 'Voir les 10 derniers messages';
	$contenu_page = '<table><tr><th class="titre">Date</th><th class="titre">Expediteur</th><th class="titre">Titre</th></tr>';

	$req_msg = "select msg_cod, msg_titre, perso_nom, perso_cod, dmsg_lu, 
			to_char(msg_date2, 'DD/MM/YYYY hh24:mi:ss') as msg_date from messages
		inner join messages_exp on emsg_msg_cod = msg_cod
		inner join messages_dest on dmsg_msg_cod = msg_cod
		inner join perso on perso_cod = emsg_perso_cod
		where dmsg_perso_cod = $visu_perso
		order by msg_date2 desc limit 10;";
	$db->query($req_msg);
	while ($db->next_record())
	{
		$msg_cod = $db->f('msg_cod');
		$expediteur = $db->f('perso_nom');
		$expediteur_cod = $db->f('perso_cod');
		$dmsg_lu = $db->f('dmsg_lu') == 'O';
		$msg_date = $db->f('msg_date');

		$btitre1 = ($dmsg_lu) ? '' : '<strong>';
		$btitre2 = ($dmsg_lu) ? '' : '</strong>';
		$msg_titre = $btitre1 . $db->f('msg_titre') . $btitre2;
		$lien_titre = "?visu_perso=$visu_perso&visu_msg=$msg_cod";

		$contenu_page .= "<tr><td>$msg_date</td><td>$expediteur</td><td><a href='$lien_titre'>$msg_titre</a></td></tr>";
	}
	$contenu_page .= "</table>";
}

if (!$erreur && !empty($visu_msg))	// Lecture d’un message
{
	$visu_msg = $db->format($visu_msg);

	$req_msg_ok = "select 1 from messages_dest where dmsg_perso_cod = $visu_perso AND dmsg_msg_cod = $visu_msg";
	if ($db->get_one_record ($req_msg_ok) === false)
	{
		$erreur = true;
		$titre_page = 'Erreur';
		$contenu_page = 'Message introuvable !';
	}
	else
	{
		$req_msg = "select msg_cod, msg_titre, e.perso_nom, e.perso_cod, msg_corps, 
				to_char(msg_date2, 'DD/MM/YYYY hh24:mi:ss') as msg_date,
				string_agg(d.perso_nom, ', ') as destinataires
			from messages
			inner join messages_exp on emsg_msg_cod = msg_cod
			inner join messages_dest on dmsg_msg_cod = msg_cod
			inner join perso e on e.perso_cod = emsg_perso_cod
			inner join perso d on d.perso_cod = dmsg_perso_cod
			where msg_cod = $visu_msg
			group by msg_cod, msg_titre, e.perso_nom, e.perso_cod, msg_corps, msg_date2";
		$db->query($req_msg);
	
		if ($db->get_one_record ($req_msg))
		{
			$msg_cod = $db->f('msg_cod');
			$expediteur = $db->f('perso_nom');
			$expediteur_cod = $db->f('perso_cod');
			$destinataires = $db->f('destinataires');
			$msg_date = $db->f('msg_date');
			$msg_titre = $db->f('msg_titre');
			$msg_corps = $db->f('msg_corps');

			$titre_page = $msg_titre;
			$contenu_page = '<table>';
			$contenu_page .= "<tr><td class='titre'>Date</td><td>$msg_date</td></tr>";
			$contenu_page .= "<tr><td class='titre'>Expéditeur</td><td>$expediteur</td></tr>";
			$contenu_page .= "<tr><td class='titre'>Destinataires</td><td>$destinataires</td></tr>";
			$contenu_page .= "<tr><td colspan='2'>$msg_corps</td></tr>";

			// On marque comme lu
			$req_lu = "update messages_dest set dmsg_lu = 'O' where dmsg_msg_cod = $visu_msg and dmsg_perso_cod = $visu_perso and dmsg_lu <> 'O'";
			$db->query($req_lu);
		}
		$contenu_page .= "</table>";
	}
	$contenu_page .= "<p><a href='?visu_perso=$visu_perso'>Retour</a></p>";
}


$t->set_var("CONTENU", $contenu_page);
$t->set_var("TITRE", $titre_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');
