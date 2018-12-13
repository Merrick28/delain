<?php 
if(!defined("APPEL"))
    die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Modification des relations entre factions</div><br />';
echo '<div style="padding:10px"><p>La table doit se lire : la faction A considère la faction B avec un degré d’amitié de X.<p>
	<p>Où A se trouve dans la première colonne (faction sujet), et B dans la première ligne (faction objet). X est bien sûr le chiffre donné, de 1 à 10.</p>
	<p>Cette relation n’est pas nécessairement symétrique ! Certaines factions aiment pouvoir jouer les agents doubles...</p>
	<p>Une faction A qui n’en aime pas une autre refusera de donner des responsabilités à une personne qui joue sur les deux tableaux.</p>
	<p>Les cases en rouge sont les cases qui n’ont jamais été déterminées.</p><p></p><p><strong>Le note doit aller de 1 à 10</strong></p></div><hr />';

$resultat = '';

// Récupération de la matrice des relations
$req = 'SELECT f2f_sujet_cod, f2f_objet_cod, f2f_note_estime FROM faction_relation_faction order by f2f_sujet_cod, f2f_objet_cod';
$db->query($req);
$relations = array();
while ($db->next_record())
{
	$sujet = $db->f('f2f_sujet_cod');
	$objet = $db->f('f2f_objet_cod');
	$note = $db->f('f2f_note_estime');
	if (!isset($relations[$sujet]))
		$relations[$sujet] = array();
	$relations[$sujet][$objet] = $note;
}

// Récupération de liste des factions
$req = 'SELECT fac_cod, fac_nom FROM factions WHERE fac_active = \'O\' order by fac_cod';
$db->query($req);
$factions = array();
while ($db->next_record())
	$factions[$db->f('fac_cod')] = $db->f('fac_nom');

// Traitements
switch ($methode)
{
	case 'debut': break;

	case 'faction_relations_modif':
		foreach ($_POST as $nom => $valeur)
		{
			if (strpos($nom, 'relation_') === 0 && $valeur > 0 && $valeur < 11)
			{
				$champ_expl = explode('_', str_replace('relation_', '', $nom));
				$sujet = $champ_expl[0];
				$objet = $champ_expl[1];
				$modif = false;
				if (!isset($relations[$sujet]) ||
					!isset($relations[$sujet][$objet]))
				{
					$req = "INSERT INTO faction_relation_faction (f2f_sujet_cod, f2f_objet_cod, f2f_note_estime)
						VALUES ($sujet, $objet, $valeur)";
					$db->query($req);
					$modif = true;
				}
				else if ($relations[$sujet][$objet] != $valeur)
				{
					$req = "UPDATE faction_relation_faction
						SET f2f_note_estime = $valeur
						WHERE f2f_sujet_cod = $sujet
							AND f2f_objet_cod = $objet";
					$db->query($req);
					$modif = true;
				}
				if ($modif)
				{
					if (!isset($relations[$sujet]))
						$relations[$sujet] = array();
					$relations[$sujet][$objet] = $valeur;

					$sujet_nom = $factions[$sujet];
					$objet_nom = $factions[$objet];
					$resultat .= "<p>Relation entre « $sujet_nom » et « $objet_nom » passée à $valeur.</p>";
				}
			}
		}
	break;
}

ecrireResultatEtLoguer($resultat);

echo "<form action='#' method='POST'>
	<input name='methode' type='hidden' value='faction_relations_modif' />";

// Matrice des relations entre factions
$nombre = sizeof($factions);
echo '<table style="padding:10px">
	<tr>
		<th class="titre" rowspan="2">Faction sujet</th>
		<th class="titre" colspan="' . $nombre . '">Faction objet</th>
	</tr>';

echo '<tr>';
foreach ($factions as $id => $nom)
	echo "<th class='titre' title='$nom'>($id)</th>";
echo '</tr>';


foreach ($factions as $id_sujet => $nom_sujet)
{
	echo "<tr><th class='titre' style='text-align:right;'>$nom_sujet ($id_sujet)</th>";
	foreach ($factions as $id_objet => $nom_objet)
	{
		if ($id_sujet == $id_objet)
			echo "<td style='background-color:black;'></td>";
		else
		{
			$note_donnee = (isset($relations[$id_sujet]) && isset($relations[$id_sujet][$id_objet]));
			$note = ($note_donnee) ? $relations[$id_sujet][$id_objet] : 0;
			$style = ($note_donnee) ? '' : ' style="background-color:red;"';
			echo "<td$style><input type='text' value='$note' name='relation_$id_sujet" . "_$id_objet' size='2' /></td>";
		}
	}
	echo "</tr>";
}
echo '</table>';

echo "<input type='submit' class='test' value='Modifier les relations !' /></form>";