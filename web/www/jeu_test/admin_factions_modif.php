<?php 
if(!defined("APPEL"))
    die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Modification / suppression / création d’une faction</div><br />';
echo '<script type="text/javascript">
		function changeMethode(id, valeur)
		{
			document.getElementById("methode" + id).value = valeur;
		}
	</script>';

$resultat = '';
$methode = $_REQUEST['methode'];
switch ($methode)
{
	case 'debut': break;

	case 'faction_modif':
		if (isset($_POST['fac_cod']))
		{
			$fac_cod = $_POST['fac_cod'];
			$fac_nom = pg_escape_string(str_replace('\'', '’', $_POST['fac_nom']));
			$fac_description = pg_escape_string(str_replace('\'', '’', $_POST['fac_description']));
			$fac_introduction = pg_escape_string(str_replace('\'', '’', $_POST['fac_introduction']));

			$req = "UPDATE factions SET
					fac_nom = '$fac_nom',
					fac_description = '$fac_description',
					fac_introduction = '$fac_introduction'
				WHERE fac_cod = $fac_cod";
			$stmt = $pdo->query($req);
			$resultat = "Faction $fac_nom ($fac_cod) mise à jour !";
		}
		else
			$resultat = "Erreur de paramètres";
	break;

	case 'faction_ajout':
		if (isset($_POST['fac_nom']))
		{
			$fac_nom = pg_escape_string(str_replace('\'', '’', $_POST['fac_nom']));
			$fac_description = pg_escape_string(str_replace('\'', '’', $_POST['fac_description']));
			$fac_introduction = pg_escape_string(str_replace('\'', '’', $_POST['fac_introduction']));

			$req = "INSERT INTO factions (fac_nom, fac_description, fac_introduction)
				VALUES ('$fac_nom', '$fac_description', '$fac_introduction')
				RETURNING fac_cod";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$fac_cod = $result['fac_cod'];

			$resultat = "Faction $fac_nom ($fac_cod) créée !";
		}
		else
			$resultat = "Erreur de paramètres";
	break;

	case 'faction_supprime':
		if (isset($_POST['fac_cod']))
		{
			$fac_cod = $_POST['fac_cod'];
			$req = "SELECT fac_nom FROM factions WHERE fac_cod = $fac_cod";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$fac_nom = $result['fac_nom'];
			$req = "UPDATE factions SET fac_active = 'N' where fac_cod = $fac_cod";
			$stmt = $pdo->query($req);
			$resultat = "Faction $fac_nom ($fac_cod) désactivée !";
		}
		else
			$resultat = "Erreur de paramètres";
	break;

	case 'faction_restaure':
		if (isset($_POST['fac_cod']))
		{
			$fac_cod  = $_POST['fac_cod'];
			$req      = "SELECT fac_nom FROM factions WHERE fac_cod = $fac_cod";
			$stmt     = $pdo->query($req);
			$result   = $stmt->fetch();
            $fac_nom  = $result['fac_nom'];
            $req      = "UPDATE factions SET fac_active = 'O' where fac_cod = $fac_cod";
            $stmt     = $pdo->query($req);
            $resultat = "Faction $fac_nom ($fac_cod) restaurée !";
        } else
            $resultat = "Erreur de paramètres";
        break;
}

$fonctions = new fonctions;
$fonctions->ecrireResultatEtLoguer($resultat, $req);

$req = 'SELECT fac_cod, fac_nom, fac_description, fac_introduction, fac_active, coalesce(fmiss_nb, 0) as fmiss_nb, coalesce(rfac_nb, 0) as rfac_nb, coalesce(lfac_nb, 0) as lfac_nb
	FROM factions
	LEFT OUTER JOIN (select fmiss_fac_cod, count(*) as fmiss_nb
		from faction_missions
		inner join missions on miss_cod = fmiss_miss_cod
		where miss_fonction_init is not null and miss_fonction_valide is not null
		group by fmiss_fac_cod) m ON fmiss_fac_cod = fac_cod
	LEFT OUTER JOIN (select rfac_fac_cod, count(*) as rfac_nb
		from faction_rangs
		group by rfac_fac_cod) r ON rfac_fac_cod = fac_cod
	LEFT OUTER JOIN (select tlfac_fac_cod, count(*) as lfac_nb
		from faction_lieu_type
		group by tlfac_fac_cod) l ON tlfac_fac_cod = fac_cod
	ORDER BY fac_nom';

// Tableau des factions
echo '<div style="padding:10px;"><table>
	<tr>
		<th class="titre">Faction</th>
		<th class="titre">Description</th>
		<th class="titre">Introduction</th>
		<th class="titre">Actions</th>
	</tr>';

$stmt = $pdo->query($req);

while($result = $stmt->fetch())
{
	// Récupération des données
	$fac_cod = $result['fac_cod'];
	$fac_nom = $result['fac_nom'];
	$fac_active = ($result['fac_active'] == 'O');
	$txt_active = ($fac_active) ? '' : '<br /><em>(inactive)</em>';
	$fac_description = $result['fac_description'];
	$fac_introduction = $result['fac_introduction'];
	$fmiss_nb = $result['fmiss_nb'];
	$rfac_nb = $result['rfac_nb'];
	$lfac_nb = $result['lfac_nb'];
	
	$b1miss = ($fmiss_nb > 0) ? '' : '<strong>';
	$b2miss = ($fmiss_nb > 0) ? '' : '</strong>';
	$b1rang = ($rfac_nb > 0) ? '' : '<strong>';
	$b2rang = ($rfac_nb > 0) ? '' : '</strong>';
	$b1lieu = ($lfac_nb > 0) ? '' : '<strong>';
	$b2lieu = ($lfac_nb > 0) ? '' : '</strong>';

	echo "<form action='#' method='POST'><tr>
		<td class='soustitre2'><input type='text' value='$fac_nom' name='fac_nom' size='30' />$txt_active</td>
		<td class='soustitre2'><textarea cols='40' rows='3' name='fac_description'>$fac_description</textarea></td>
		<td class='soustitre2'><textarea cols='40' rows='3' name='fac_introduction'>$fac_introduction</textarea></td>
		<td class='soustitre2'><input type='hidden' value='$fac_cod' name='fac_cod' />
			<input type='hidden' value='faction_modif' name='methode' id='methode$fac_cod' />
			<input type='submit' class='test' value='Modifier' onclick='changeMethode($fac_cod, \"faction_modif\");'/>";
	if ($fac_active)
		echo "<input type='submit' class='test' value='Désactiver' onclick='changeMethode($fac_cod, \"faction_supprime\");'/>";
	else
		echo "<input type='submit' class='test' value='Restaurer' onclick='changeMethode($fac_cod, \"faction_restaure\");'/>";
	echo "<br />$b1miss<a href='?onglet=faction_mission&fac_cod=$fac_cod'>Missions disponibles ($fmiss_nb définies)</a>$b2miss
		<br />$b1lieu<a href='?onglet=faction_lieu&fac_cod=$fac_cod'>Lieux de présence ($lfac_nb définis)</a>$b2lieu
		<br />$b1rang<a href='?onglet=faction_rang&fac_cod=$fac_cod'>Gérer les rangs ($rfac_nb définies)</a>$b2rang";
	echo "</td></tr></form>";
}
echo "<form action='#' method='POST'><tr>
	<td class='soustitre2'><input type='text' value='' name='fac_nom' size='30' /></td>
	<td class='soustitre2'><textarea cols='40' rows='3' name='fac_description'></textarea></td>
	<td class='soustitre2'><textarea cols='40' rows='3' name='fac_introduction'></textarea></td>
	<td class='soustitre2'><input type='hidden' value='faction_ajout' name='methode' />
		<input type='submit' class='test' value='Ajouter' /></td></tr></form>";
echo '</table></div>';
