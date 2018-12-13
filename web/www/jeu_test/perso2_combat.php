<?php 
$param = new parametres();
$contenu_page .= '<div style="padding:10px; text-align:left;">';
$req = "select obj_nom,to_char(perobj_dfin,'DD/MM/YYYY à hh24:mi:ss') as dfin
	from objets,perso_objets
	where perobj_perso_cod = $perso_cod
	and perobj_obj_cod = obj_cod
	and perobj_equipe = 'O' 
	and perobj_dfin is not null";
$db->query($req);
if ($db->nf() != 0)
{
	while($db->next_record())
	{
		$contenu_page .= '<div>Vous possédez l’objet <strong>' . $db->f("obj_nom") . '</strong> jusqu’au <strong>' . $db->f("dfin") . '</strong></div>';
	}
}

if ($db->is_locked($perso_cod))
{
	$combat = "Vous êtes actuellement engagé en combat.";
}
else
{
	$combat = "Vous êtes actuellement hors combat.";
}
$contenu_page .= '<div> ' . $combat . '</div>';

include('mode_combat.php');
$contenu_page .=  "<div>$contenu_include</div>";

$contenu_page .=  ' <a href="groupe.php">Accéder au menu coterie</a><hr>';

/*
 LEGITIMES DEFENSES 
 */
$contenu_page .= '

<table width="100%" border="1"><tr><td valign="top"><table width="100%">
<tr><td class="titre">Blocages de combat</td></tr>
<tr><td><strong>En tant que cible :</strong>';
$cout_des = $param->getparm(60);
$req_at = "select lock_attaquant,lock_nb_tours,perso_nom from perso,lock_combat 
	where lock_cible = $perso_cod 
	and lock_attaquant = perso_cod 
	and perso_actif = 'O' ";
$db->query($req_at);
$nb_at = $db->nf();
if ($nb_at == 0)
{
	$contenu_page .= 'Vous n’êtes pas bloqué en tant que cible.';
}
else
{
	$contenu_page .= '
	<form name="visu_evt3" method="post" action="visu_evt_perso.php">
	<input type="hidden" name="visu">
	<input type="hidden" name="num_guilde">
	<table cellspacing="2" cellpadding="2">
	<tr>
	<td class="soustitre2"><strong>Nom</strong></td>
	<td class="soustitre2"><strong>Tours</strong></td>
	<td></td>
	</tr>';
	
	while ($db->next_record())
	{
		$contenu_page .= '<tr>
		<td class="soustitre2"><strong><a href="javascript:document.visu_evt3.visu.value=' . $db->f("lock_attaquant") . ';document.visu_evt3.submit();">' . $db->f("perso_nom") . '</a></strong></td>
		<td style="text-align:center;">' . $db->f("lock_nb_tours") . '</td>
		<td><a href="action.php?methode=desengagement&cible=' . $db->f("lock_attaquant") . '&valide=O">Se désengager ? (' . $cout_des . 'PA)</a></td>
		</tr>';
	}
	$contenu_page .= '</table></form>';
}
$contenu_page .= '<br><strong>En tant qu’attaquant :</strong>';
$req_at = "select lock_cible,lock_nb_tours,perso_nom from perso,lock_combat 
	where lock_attaquant = $perso_cod 
	and lock_cible = perso_cod 
	and perso_actif = 'O' ";
$db->query($req_at);
$nb_at = $db->nf();
if ($nb_at == 0)
{
	$contenu_page .= ' Vous n’êtes pas bloqué en tant qu’attaquant.';
}
else
{
	$contenu_page .= '
	<form name="visu_evt4" method="post" action="visu_evt_perso.php"><input type="hidden" name="visu"><input type="hidden" name="num_guilde"><table cellspacing="2" cellpadding="2">
	<tr>
	<td class="soustitre2"><strong>Nom</strong></td>
	<td class="soustitre2"><strong>Tours</strong></td>
	<td></td>
	</tr>';
	while ($db->next_record())
	{
		$contenu_page .= '<tr><td class="soustitre2"><strong><a href="javascript:document.visu_evt4.visu.value=' . $db->f("lock_cible") . ';document.visu_evt4.submit();">' . $db->f("perso_nom") . '</a></strong></td>
		<td style="text-align:center;">' . $db->f("lock_nb_tours") . '</td>
		<td><a href="action.php?methode=desengagement&cible=' . $db->f("lock_cible") . '&valide=O">Se désengager ? (' . $cout_des . ' PA)</a></td>
		</tr>';
	}
	$contenu_page .= '</table></form>';
}
/*
LEGITIMES DEFENSES 
*/

$contenu_page .= '</table></td><td valign="top">
<table width="100%">
<tr><td class="titre">Légitimes défenses</td></tr>
<tr><td><p><strong>En tant que cible :</strong>';
$req_at = "select perso_cod,perso_nom,riposte_nb_tours from perso,riposte 
	where riposte_cible = $perso_cod 
	and riposte_attaquant = perso_cod 
	and perso_actif = 'O' 
	and perso_type_perso = 1 ";
$db->query($req_at);
$nb_at = $db->nf();
if ($nb_at == 0)
{
	$contenu_page .= ' Vous n’avez aucune légitime défense.';
}
else
{
	$contenu_page .= '
	<form name="visu_evt" method="post" action="visu_desc_perso.php">
	<input type="hidden" name="visu">
	<input type="hidden" name="num_guilde">
	<table cellspacing="2" cellpadding="2">
	<tr>
	<td class="soustitre2"><strong>Nom</strong></td>
	<td class="soustitre2"><strong>Tours</strong></td>
	</tr>';
	while ($db->next_record())
	{
		$contenu_page .= '<tr>
		<td class="soustitre2"><strong><a href="javascript:document.visu_evt.visu.value=' . $db->f("perso_cod") . ';document.visu_evt.submit();">' . $db->f("perso_nom") . '</a></strong></td>
		<td><p style="text-align:center;">' . $db->f("riposte_nb_tours") . '</td>
		</tr>';
	}
	$contenu_page .= '</table></form>';
}
// 2e partie
$contenu_page .= '<br><strong>En tant qu’attaquant :</strong>';
$req_at = "select perso_cod,perso_nom,riposte_nb_tours from perso,riposte 
	where riposte_cible = perso_cod 
	and riposte_attaquant = $perso_cod 
	and perso_actif = 'O' 
	and perso_type_perso = 1 ";
$db->query($req_at);
$nb_at = $db->nf();
if ($nb_at == 0)
{
	$contenu_page .= ' Aucun perso ne peut utiliser la légitime défense contre vous.';
}
else
{
	$contenu_page .= '
	<form name="visu_evt2" method="post" action="visu_desc_perso.php"><input type="hidden" name="visu"><input type="hidden" name="num_guilde"><table cellspacing="2" cellpadding="2">
	<tr>
	<td class="soustitre2"><strong>Nom</strong></td>
	<td class="soustitre2"><strong>Tours</strong></td>
	</tr>';
	while ($db->next_record())
	{
		$contenu_page .= '<tr>
		<td class="soustitre2"><strong><a href="javascript:document.visu_evt2.visu.value=' . $db->f("perso_cod") . ';document.visu_evt2.submit();">' . $db->f("perso_nom") . '</a></strong></td>		<td><p style="text-align:center;">' . $db->f("riposte_nb_tours") . '</td>
		</tr>';
	}
	$contenu_page .= '</table></form>';
}
$contenu_page .= '</table></td></tr></table></div>';

