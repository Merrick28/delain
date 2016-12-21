<?php 
if(!DEFINED("APPEL"))
    die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';

$resultat = '';

if (!isset($fac_cod))
{
	$fac_cod = -1;
	$fac_nom = '';
	echo '<div class="barrTitle">Les lieux servant de base aux factions</div><br />';
}
else
{
	$req = "SELECT fac_nom FROM factions where fac_cod = $fac_cod";
	$db->query($req);
	$db->next_record();
	$fac_nom = $db->f('fac_nom');
	echo "<div class='barrTitle'>Les lieux servant de base à la faction « $fac_nom »</div><br />";
}

switch ($methode)
{
	case 'debut': break;

	case 'lieu_ajout':
		if (isset($_POST['tlfac_tlieu_cod']) && $fac_cod >= 0)
		{
			$tlfac_tlieu_cod = $_POST['tlfac_tlieu_cod'];
			$tlfac_dieu_cod = $_POST['tlfac_dieu_cod'];
			$tlfac_etage_min = $_POST['tlfac_etage_min'];
			$tlfac_etage_max = $_POST['tlfac_etage_max'];
			$tlfac_levo_niveau = $_POST['tlfac_levo_niveau'];
			
			if ($tlfac_etage_min == '') $tlfac_etage_min = 0;
			if ($tlfac_etage_max == '') $tlfac_etage_max = 'NULL';
			if ($tlfac_levo_niveau == '') $tlfac_levo_niveau = 0;

			$req = "INSERT INTO faction_lieu_type (tlfac_fac_cod, tlfac_tlieu_cod, tlfac_dieu_cod, tlfac_etage_min, tlfac_etage_max, tlfac_levo_niveau)
				VALUES ($fac_cod, $tlfac_tlieu_cod, $tlfac_dieu_cod, $tlfac_etage_min, $tlfac_etage_max, $tlfac_levo_niveau)";
			$db->query($req);

			$resultat = "Lieu $tlfac_tlieu_cod ajouté à la faction « $fac_nom » !";
		}
		else
			$resultat = "Erreur de paramètres";
	break;

	case 'lieu_supprime':
		if (isset($_POST['tlfac_tlieu_cod']) && $fac_cod >= 0)
		{
			$tlfac_tlieu_cod = $_POST['tlfac_tlieu_cod'];
			$tlfac_etage_min = $_POST['tlfac_etage_min'];
			$tlfac_levo_niveau = $_POST['tlfac_levo_niveau'];

			$req = "DELETE FROM faction_lieu_type
				WHERE tlfac_fac_cod = $fac_cod
					AND tlfac_tlieu_cod = $tlfac_tlieu_cod
					AND tlfac_levo_niveau = $tlfac_levo_niveau
					AND tlfac_etage_min = $tlfac_etage_min";
			$db->query($req);

			$resultat = "Lieu $tlfac_tlieu_cod supprimé à la faction « $fac_nom » !";
		}
		else
			$resultat = "Erreur de paramètres";
	break;
}

ecrireResultatEtLoguer($resultat, $req);

echo '<div style="padding:10px;"><p>Sélectionnez la faction sur laquelle vous souhaitez travailler.</p>
	<form method="GET" action="#"><select name="fac_cod">';

$req = 'SELECT fac_cod, fac_nom FROM factions WHERE fac_active=\'O\' ORDER BY fac_nom';
echo $html->select_from_query($req, 'fac_cod', 'fac_nom', $fac_cod);

echo '</select><input type="submit" class="test" value="Modifier" /><input type="hidden" value="' . $onglet . '" name="onglet" />
	</form></div><hr />';

if ($fac_cod > -1)
{
	$req = "SELECT tlieu_libelle, coalesce(dieu_nom, 'Aucun') as dieu_nom, tlfac_tlieu_cod, tlfac_levo_niveau, tlfac_etage_min,
			coalesce(emin.etage_libelle, 'Aucune restriction') as tlfac_etage_min_nom,
			coalesce(emax.etage_libelle, 'Aucune restriction') as tlfac_etage_max_nom
		FROM faction_lieu_type
		INNER JOIN lieu_type ON tlieu_cod = tlfac_tlieu_cod
		LEFT OUTER JOIN dieu ON dieu_cod = tlfac_dieu_cod
		LEFT OUTER JOIN etage emin ON emin.etage_numero = tlfac_etage_min
		LEFT OUTER JOIN etage emax ON emax.etage_numero = tlfac_etage_max
		WHERE tlfac_fac_cod = $fac_cod";

	// Tableau des missions
	echo "<div style='padding:10px;'><div>Voici la liste des lieux dans lesquels on peut trouver des missions pour la faction « $fac_nom ».</div>
		<div>Les bornes « Étage supérieur » et « Étage inférieur » représentent les limites entre lesquelles on trouve des missions dans ce lieu.<br />Par exemple, si on assigne respectivement « -1 » et « -4 », la faction n’utilisera ce lieu que dans les étages -1, -2, -3, -4 et -5 (et leurs antres).<br />
		<b>Attention ! Si les bornes sont renseignées à l’envers </b>(-5 puis -1...), <b>aucune mission ne sera jamais délivrée dans ce lieu pour cette faction !</b></div>
		<div>Le « niveau du lieu » est un concept encore peu utilisé dans le jeu, et potentiellement amené à se développer. Il permet un fonctionnement par héritage des lieux : quand un type de lieu gagne un niveau, il a plus de fonctionnalités.<br />
		Ceci est utilisé dans deux cas : la Cathédrale, une évolution de niveau 2 du temple, et la Dalle Morbeline améliorée (créée pour la faction), de niveau 2 aussi. Donc dans l’immense majorité des cas, on laissera 0 dans cette colonne.</div>";
	echo '<table>
		<tr>
			<th class="titre">Lieu</th>
			<th class="titre">Niveau du lieu</th>
			<th class="titre">Dieu concerné</th>
			<th class="titre">Étage supérieur</th>
			<th class="titre">Étage inférieur</th>
			<th class="titre">Actions</th>
		</tr>';

	$db->query($req);

	while($db->next_record())
	{
		// Récupération des données
		$tlieu_libelle = $db->f('tlieu_libelle');
		$dieu_nom = $db->f('dieu_nom');
		$tlfac_levo_niveau = $db->f('tlfac_levo_niveau');
		$tlfac_etage_min = $db->f('tlfac_etage_min');
		$tlfac_etage_min_nom = $db->f('tlfac_etage_min_nom');
    	$tlfac_etage_max_nom = $db->f('tlfac_etage_max_nom');
    	$tlfac_tlieu_cod = $db->f('tlfac_tlieu_cod');

		echo "<form action='#' method='POST' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer ce lieu d’ancrage pour cette faction ?\");'><tr>
			<td class='soustitre2'>$tlieu_libelle</td>
			<td class='soustitre2'>$tlfac_levo_niveau</td>
			<td class='soustitre2'>$dieu_nom</td>
			<td class='soustitre2'>$tlfac_etage_min_nom</td>
			<td class='soustitre2'>$tlfac_etage_max_nom</td>
			<td class='soustitre2'><input type='hidden' value='$tlfac_tlieu_cod' name='tlfac_tlieu_cod' />
				<input type='hidden' value='$fac_cod' name='fac_cod' />
				<input type='hidden' value='$tlfac_etage_min' name='tlfac_etage_min' />
				<input type='hidden' value='$tlfac_levo_niveau' name='tlfac_levo_niveau' />
				<input type='hidden' value='lieu_supprime' name='methode' />
				<input type='submit' class='test' value='Supprimer'/></td></tr></form>";
	}

	// <select> des lieux
	$req = 'SELECT tlieu_cod, tlieu_libelle from lieu_type order by tlieu_libelle';
	$select_tlieu = '<select name="tlfac_tlieu_cod">';
	$select_tlieu .= $html->select_from_query($req, 'tlieu_cod', 'tlieu_libelle');
	$select_tlieu .= '</select>';

	// <select> des dieux
	$req = 'SELECT dieu_cod, dieu_nom from dieu order by dieu_nom';
	$select_dieu = '<select name="tlfac_dieu_cod"><option value="-1">Aucun dieu</option>';
	$select_dieu .= $html->select_from_query($req, 'dieu_cod', 'dieu_nom');
	$select_dieu .= '</select>';

	// <select> des étages
	$req = 'SELECT etage_numero, etage_libelle from etage where etage_reference = etage_numero and etage_numero <> -100 order by etage_numero desc';
	$select_etage_min = '<select name="tlfac_etage_min"><option value="">Aucune restriction</option>';
	$select_etage_min .= $html->select_from_query($req, 'etage_numero', 'etage_libelle');
	$select_etage_min .= '</select>';

	$select_etage_max = '<select name="tlfac_etage_max"><option value="">Aucune restriction</option>';
	$select_etage_max .= $html->select_from_query($req, 'etage_numero', 'etage_libelle');
	$select_etage_max .= '</select>';

	echo "<form action='#' method='POST'><tr>
		<td class='soustitre2'>$select_tlieu</td>
		<td class='soustitre2'><input type='text' cols='2' value='0' /></td>
		<td class='soustitre2'>$select_dieu</td>
		<td class='soustitre2'>$select_etage_min</td>
		<td class='soustitre2'>$select_etage_max</td>
		<td class='soustitre2'><input type='hidden' value='lieu_ajout' name='methode' />
			<input type='hidden' value='$fac_cod' name='fac_cod' />
			<input type='submit' class='test' value='Ajouter' /></td></tr></form>";
	echo '</table></div>';
}