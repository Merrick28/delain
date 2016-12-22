<?php 
if(!defined("APPEL"))
	die("Erreur d’appel de page !");

$mode_sortie = 'echo';
if ($contenu_page != '')
	$mode_sortie = 'variable';

// Factions présentes
$req_factions = "SELECT fac_cod, fac_nom FROM v_factions_lieux
	WHERE lieu_cod = $lieu_cod";
$db->query($req_factions);

if ($db->nf() > 0)
{
	// On a des factions dans ce lieu !
	if ($db->nf() == 1)
		$contenu_page .= '<hr />Une personne semble recruter en ce lieu ! Elle vous aborde :<br />';
	else
		$contenu_page .= '<hr />Différentes factions semblent recruter en ce lieu ! Leurs représentants vous abordent :<br /><br />';
	
	while($db->next_record())
	{
		$faction = $db->f('fac_nom');
		$faction_cod = $db->f('fac_cod');
		
		$contenu_page .= "<p>« Rejoignez-nous ! <a href='factions.php?faction=$faction_cod'>Travaillez pour $faction</a>, et notre grandeur sera la vôtre ! »</p>";
	}
}

if ($mode_sortie == 'echo')
{
	echo $contenu_page;
	$contenu_page = '';
}