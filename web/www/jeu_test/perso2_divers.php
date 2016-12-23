<?php 
//CETTE PAGE REGROUPE LES DONNÉES DIVERSES
$param = new parametres();
$db2 = new base_delain;
//
// données tutorat
//
$contenu_page .= '<p class="titre">Tutorat</p><p>';
$tutorat = false;
// on regarde si la personne est 'tutorée'
$req = 'select perso_cod,perso_nom
	from perso,tutorat
	where tuto_filleul = ' . $perso_cod . '
	and tuto_tuteur = perso_cod';
$db->query($req);
if($db->nf() != 0)
{
	$db->next_record();
	$contenu_page .= 'Votre tuteur est <a href="visu_desc_perso.php?visu=' . $db->f('perso_cod') . '">' . $db->f('perso_nom') . '</a><br />';
}
$req = 'select perso_niveau,perso_tuteur 
	from perso
	where perso_cod = ' . $perso_cod;
$db->query($req);
$db->next_record();
if($db->f('perso_niveau') >= 15)
{
	if($db->f('perso_tuteur') == 'f')
	{
		$contenu_page .= 'Vous n’êtes pas inscrit(e) en tant que tuteur. <a href="tuteur.php?methode=inscr">Cliquez ici pour vous inscrire.</a><br />';
	}
	else
	{
		$contenu_page .= 'Vous êtes inscrit(e) en tant que tuteur. <a href="tuteur.php?methode=desabo">Cliquez ici pour vous désinscrire.</a><br />';
		$req = 'select perso_nom,perso_cod
			from perso,tutorat
			where tuto_tuteur = ' . $perso_cod . '
			and tuto_filleul = perso_cod';
		$db->query($req);
		if($db->nf() == 0)
		{
			$contenu_page .= 'Vous n’avez aucun filleul pour le moment.<br />';
		}
		else
		{
			$contenu_page .= 'Liste de vos filleuls :<br />';
			while($db->next_record())
			{
				$contenu_page .= '- <a href="visu_desc_perso.php?visu=' . $db->f('perso_cod') . '">' . $db->f('perso_nom') . '</a><br />';
			}
		}
	}
}
//
// Fin du tutorat
//

//
// Début utilisation PA restants
//
if (isset($ch_util))
{
	$req = "update perso set perso_utl_pa_rest = $ch_util where perso_cod = $perso_cod ";
	$db->query($req);
}

$req = "select perso_nom,perso_utl_pa_rest from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();

if ($db->f("perso_utl_pa_rest") == 1)
{
	$util = $db->f("perso_nom") . " <b>utilise</b> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 0;
}
else
{
	$util = $db->f("perso_nom") . " <b>n’utilise pas</b> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 1;
}
$contenu_page .= '<p class="titre">Utilisation des PA restants</p><p>' . $util . ' <a href="' . $PHP_SELF . '?m=6&ch_util=' . $ch_util . '">Changer ?</a></p>';

//
// Fin utilisation PA restants
//

//
// Début Dispensaires
//
$req_temple = "select lieu_nom,pos_x,pos_y,etage_libelle,ptemple_nombre 
									from perso_temple,positions,lieu_position,lieu,etage 
									where ptemple_perso_cod = $perso_cod 
									and ptemple_pos_cod = pos_cod 
									and lpos_pos_cod = pos_cod 
									and lpos_lieu_cod = lieu_cod 
									and pos_etage = etage_numero ";
$db->query($req_temple);
$nb = $db->nf();
$contenu_page .= '<p class="titre">Dispensaire choisi</p>';
if ($nb == 0)
{
	$contenu_page .= '<p>Vous n’avez pas de dispensaire spécifique pour vous ramener en cas de mort.';
}
else
{
	$db->next_record();
	
	$contenu_page .= '<table width="100%">
		<tr><td class="soustitre2"><p><b>Nom</b></td><td class="soustitre2"><p style="text-align:center;"><b>X</b></td><td class="soustitre2"><p style="text-align:center;"><b>Y</n></td><td class="soustitre2"><p style="text-align:center;"><b>Etage</b></td><td class="soustitre2"><p>Probabilité de retour</td></tr>
		<tr><td class="soustitre2"><p><b>' . $db->f("lieu_nom") . '</b></td>
		<td><p style="text-align:center;">' . $db->f("pos_x") . '</td>
		<td><p style="text-align:center;">' . $db->f("pos_y") . '</td>
		<td><p style="text-align:center;">' . $db->f("etage_libelle") . '</td>';
	$chance = $db->f("ptemple_nombre");
	$chance = 100 - ($chance * $param->getparm(32));
	$contenu_page .= '<td><p>' . $chance . ' %</td></tr>
		</table>
		<a href="action.php?methode=abtemp">Abandonner ce dispensaire ?</a> (vous n’aurez plus de dispensaire spécifique pour vous ramener en cas de mort)';
}
//
// Fin Dispensaires
//

//
// Début impalpabilité
//
$req = 'select perso_tangible,perso_nb_tour_intangible from perso where perso_cod = ' . $perso_cod;
$db->query($req);
$db->next_record();
if ($db->f("perso_tangible") == 'N')
	$contenu_page .= '<p>Vous êtes impalpable pour ' . $db->f("perso_nb_tour_intangible") . ' tours.</p>';

//
// Fin impalpabilité
//

//
// Début Grands escaliers
//
$req = "select pge_lieu_cod,pos_x,pos_y,etage_libelle,lieu_dest
	from perso_grand_escalier,positions,lieu_position,etage,lieu
	where pge_perso_cod = $perso_cod
	and pge_lieu_cod = lpos_lieu_cod
	and lpos_pos_cod = pos_cod 
	and etage_numero = pos_etage
	and pge_lieu_cod not in (2139) 
	and pos_etage > -5 
	and lpos_lieu_cod = lieu_cod ";
$db->query($req);
if ($db->nf() != 0)
{
	$contenu_page .= '<p class="titre">Grands escaliers activés</p><p class="soustitre2">Vous avez activé les grands escaliers suivants : </p>';
	while($db->next_record())
	{
		$contenu_page .= 'Position : ' . $db->f("pos_x") . ', ' . $db->f('pos_y') . ', ' . $db->f('etage_libelle') . ' (destination : ';
		$req = 'select pos_x,pos_y,etage_libelle from positions,etage
							where pos_cod = ' . $db->f('lieu_dest') . ' and pos_etage = etage_numero ';
		$db2->query($req);
		$db2->next_record();
		$contenu_page .= $db2->f("pos_x") . ', ' . $db2->f("pos_y") . ', ' . $db2->f("etage_libelle") . ')<br>';  	
	}
}
//
// Fin Grands escaliers
//

//
// Début Voie magique
//
$req = 'select count (1) as nv5 from perso, perso_nb_sorts_total, sorts where perso_cod = pnbst_perso_cod and pnbst_sort_cod = sort_cod and sort_niveau >= 5 and pnbst_nombre > 0 and perso_cod = ' . $perso_cod;
$db->query($req); $db->next_record();
$nv5 = $db->f('nv5');
$req = 'select count(1) as mem from perso_sorts, perso where psort_perso_cod = perso_cod and perso_type_perso = 1 and perso_cod = ' . $perso_cod;
$db->query($req); $db->next_record();
$mem = $db->f('mem');
if($nv5 > 0 && $mem > 5)
{
	$contenu_page .= '<p class="titre">Voie magique</p>';
	include "texte_voie_magique.php";
}

