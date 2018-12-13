<?php $colonneMax = 2;
$colonne = 0;
$debut = true;

// Liste des factions que le perso a contactées
$req_factions = "SELECT pfac_fac_cod, pfac_points, pfac_date_mission, fac_nom
	FROM faction_perso
	INNER JOIN factions ON fac_cod = pfac_fac_cod
	WHERE pfac_perso_cod = $perso_cod
	ORDER BY pfac_points desc";
$db->query($req_factions);

if ($db->nf() > 0)
{
	$contenu_page .= '<p class="titre">Factions</p><p></p>';
	
	if (isset($methode) && $methode == 'valide_mission')
	{
		$req_mission = "select missions_verifie($perso_cod) as resultat";
		$db2->query($req_mission);
		$db2->next_record();
		if ($db2->f('resultat') != '')
		{
			$contenu_page .= "<div class='bordiv' style='margin:10px;'>Résultats de validation :<br />" . $db2->f('resultat') . "</div>";
		}
	}
	$contenu_page .= '<table><tr><td valign="top">';
	while ($db->next_record())
	{
		$contenu_page .= gereColonnes($colonne, $debut, '50%');
		$colonne = ($colonne + 1) % $colonneMax;
		$debut = false;
	
		// Faction X
		$fac_nom = $db->f('fac_nom');
		$fac_cod = $db->f('pfac_fac_cod');
		$pfac_points = $db->f('pfac_points');
	
		$contenu_page .= "<p class='soustitre'>$fac_nom</p>";
	
		// Rang dans cette faction
		$req_rang = "SELECT rfac_nom FROM faction_rangs
			WHERE rfac_fac_cod = $fac_cod
				AND rfac_seuil <= $pfac_points
			ORDER BY rfac_seuil DESC
			LIMIT 1";
		$db2->query($req_rang);
		if ($db2->next_record())
			$rang = 'Vous avez atteint le rang de « ' . $db2->f('rfac_nom') . ' »';
		else
			$rang = 'Vous n’avez pas encore été inscrits dans les registres.';
	
		// Missions pour cette faction
		$listeMissions = $db2->missions_du_perso($perso_cod, $fac_cod, TRUE, 'date');
	
		$contenu_page .= $rang . '<div style="max-height:300px; overflow:auto;"><table><tr>
			<th class="soustitre2"><strong>Date de début</strong></th>
			<th class="soustitre2"><strong>Type</strong></th>
			<th class="soustitre2"><strong>Libellé</strong></th>
			<th class="soustitre2"><strong>Statut</strong></th></tr>';
	
		$revalider = false;
		$avalider = false;
	
		foreach ($listeMissions as $uneMission)
		{
			$texte_statut = '';
			if ($uneMission['EnCours'])
			{
				$texte_statut = 'En cours...';
				$revalider = true;
			}
			if ($uneMission['Réussie'])
				$texte_statut = 'Réalisée !';
			
			if ($uneMission['Ratée'] && !$uneMission['RéussitePartielle'])
				$texte_statut = 'Ratée...';
			
			if ($uneMission['Ratée'] && $uneMission['RéussitePartielle'])
				$texte_statut = 'Partiellement ratée...';
			
			if ($uneMission['ÀValider'])
			{
				$avalider = true;
				$texte_statut .= ' À valider.';
			}
			
			if ($uneMission['Validée'])
				$texte_statut = 'Validée';
			
			if ($uneMission['Échouée'] && $uneMission['RéussitePartielle'])
				$texte_statut = 'Partiellement échouée';
			
			if ($uneMission['Échouée'] && !$uneMission['RéussitePartielle'])
				$texte_statut = 'Échouée';
	
			$contenu_page .= '<tr>';
			$contenu_page .= '<td class="soustitre2">' . $uneMission['DateDébut'] . '</td>';
			$contenu_page .= '<td class="soustitre2">' . $uneMission['Nom'] . '</td>';
			$contenu_page .= '<td class="soustitre2">' . $uneMission['Libellé'] . '</td>';
			$contenu_page .= '<td class="soustitre2">' . $texte_statut . '</td>';
			$contenu_page .= '</tr>';
		}
		$contenu_page .= '</table></div>';
	
		if ($revalider)
			$contenu_page .= "<div>Vous avez une mission en cours. Vous pouvez <a href='?m=5&methode=valide_mission'>vérifier si elle est réalisée.</a></div>";
		if ($avalider)
			$contenu_page .= "<div>Vous avez une ou plusieurs mission(s) réalisée(s) mais non validée(s). Retournez voir un représentant de la faction pour obtenir votre récompense.</div>";
	}
	
	while ($colonne != 0)
	{
		$contenu_page .= gereColonnes($colonne, $debut, '50%');
		$colonne = ($colonne + 1) % $colonneMax;
		$debut = false;
	}
	$contenu_page .= '</td></tr></table><br /><br />';
}