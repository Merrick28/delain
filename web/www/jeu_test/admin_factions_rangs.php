<?php 
if(!defined("APPEL"))
    die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<script type="text/javascript">
    	function changeMethode(id, valeur)
		{
			document.getElementById("methode" + id).value = valeur;
		}
	</script>';

$resultat = '';

if (!isset($fac_cod))
{
	$fac_cod = -1;
	$fac_nom = '';
	echo '<div class="barrTitle">Les rangs définis pour les factions</div><br />';
}
else
{
	$req = "SELECT fac_nom FROM factions where fac_cod = $fac_cod";
	$stmt = $pdo->query($req);
	$result = $stmt->fetch();
	$fac_nom = $result['fac_nom'];
	echo "<div class='barrTitle'>Les rangs définis pour la faction « $fac_nom »</div><br />";
}

switch ($methode)
{
	case 'debut': break;

	case 'rang_ajout':
		if (isset($_POST['rfac_seuil']) && $fac_cod >= 0)
		{
			$rfac_seuil = $_POST['rfac_seuil'];
    		$rfac_nom = pg_escape_string(str_replace('\'', '’', $_POST['rfac_nom']));
        	$rfac_description = pg_escape_string(nl2br(str_replace('\'', '’', $_POST['rfac_description'])));
        	$rfac_intro = pg_escape_string(nl2br(str_replace('\'', '’', $_POST['rfac_intro'])));

			$req = "INSERT INTO faction_rangs (rfac_fac_cod, rfac_seuil, rfac_nom, rfac_description, rfac_intro)
				VALUES ($fac_cod, $rfac_seuil, '$rfac_nom', '$rfac_description', '$rfac_intro')";
			$stmt = $pdo->query($req);

			$resultat = "Rang $rfac_nom ajouté pour la faction « $fac_nom » !";
		}
		else
			$resultat = "Erreur de paramètres";
	break;

	case 'rang_modif':
		if (isset($_POST['rfac_seuil_prec']) && $fac_cod >= 0)
		{
			$rfac_seuil_prec = $_POST['rfac_seuil_prec'];
			$rfac_seuil = $_POST['rfac_seuil'];
			$rfac_nom = pg_escape_string(str_replace('\'', '’', $_POST['rfac_nom']));
            $rfac_description = pg_escape_string(nl2br(str_replace('\'', '’', $_POST['rfac_description'])));
        	$rfac_intro = pg_escape_string(nl2br(str_replace('\'', '’', $_POST['rfac_intro'])));

			$req = "UPDATE faction_rangs
    			SET rfac_seuil = $rfac_seuil, rfac_nom = '$rfac_nom',
    			    rfac_description = '$rfac_description', rfac_intro = '$rfac_intro'
				WHERE rfac_seuil = $rfac_seuil_prec AND rfac_fac_cod = $fac_cod";
			$stmt = $pdo->query($req);

			$resultat = "Rang $rfac_nom modifié pour la faction « $fac_nom » !";
		}
		else
			$resultat = "Erreur de paramètres";
	break;

	case 'rang_supprime':
		if (isset($_POST['rfac_seuil_prec']) && $fac_cod >= 0)
		{
			$rfac_seuil = $_POST['rfac_seuil_prec'];
			$req = "DELETE FROM faction_rangs WHERE rfac_fac_cod = $fac_cod AND rfac_seuil = $rfac_seuil";
			$stmt = $pdo->query($req);

			$resultat = "Rang seuillé à $rfac_seuil supprimé pour la faction « $fac_nom » !";
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
	$req = "SELECT rfac_seuil, rfac_nom, rfac_description, rfac_intro
		FROM faction_rangs
		WHERE rfac_fac_cod = $fac_cod
		ORDER BY rfac_seuil";

	// Tableau des rangs
	echo "<div style='padding:10px;'><div>Voici la liste des rangs que les aventuriers peuvent atteindre dans la faction « $fac_nom ».
		<br />Pour information concernant les seuils, il est prévu qu’une mission rapporte entre 1 et 20 points.
		Un échec peut en supprimer autant voire plus.<br />
        La description est un texte explicatif du rang, affiché au joueur sur sa page de perso.<br />
        L’introduction est le texte qui s’affiche au joueur lorsqu’il est sur la page de la faction (du genre « Ah, coursier ! Vous tombez bien, j’avais un truc à vous demander »)</div><hr />";
	echo '<table>
		<tr>
			<th class="titre">Rang</th>
    		<th class="titre">Seuil de points</th>
    		<th class="titre">Description (courte)</th>
    		<th class="titre">Introduction</th>
			<th class="titre">Actions</th>
		</tr>';

	$stmt = $pdo->query($req);
	$i = 1;

	while($result = $stmt->fetch())
	{
		// Récupération des données
		$rfac_seuil = $result['rfac_seuil'];
		$rfac_nom = $result['rfac_nom'];
		$rfac_description = $result['rfac_description'];
		$rfac_intro = $result['rfac_intro'];

		echo "<form action='#' method='POST' onsubmit='if (this.methode == \"rang_supprime\") return confirm(\"Êtes-vous sûr de vouloir supprimer ce rang ?\");'><tr>
			<td class='soustitre2'>$i. <input type='text' value='$rfac_nom' name='rfac_nom' size='20' /></td>
			<td class='soustitre2'><input type='text' value='$rfac_seuil' name='rfac_seuil' size='7' /></td>
            <td class='soustitre2'><textarea cols='40' rows='3' name='rfac_description'>$rfac_description</textarea></td>
        	<td class='soustitre2'><textarea cols='40' rows='3' name='rfac_intro'>$rfac_intro</textarea></td>
			<td class='soustitre2'><input type='hidden' value='$rfac_seuil' name='rfac_seuil_prec' />
				<input type='hidden' value='$fac_cod' name='fac_cod' />
				<input type='hidden' value='rang_modif' name='methode' id='methode$rfac_seuil' />
				<input type='submit' class='test' value='Modifier' onclick='changeMethode($rfac_seuil, \"rang_modif\");'/>
				<input type='submit' class='test' value='Supprimer' onclick='changeMethode($rfac_seuil, \"rang_supprime\");'/>
			</td></tr></form>";
		$i++;
	}

	echo "<form action='#' method='POST'><tr>
		<td class='soustitre2'><input type='text' value='' name='rfac_nom' size='20' /></td>
    	<td class='soustitre2'><input type='text' value='' name='rfac_seuil' size='7' /></td>
    	<td class='soustitre2'><textarea cols='40' rows='3' name='rfac_description'></textarea></td>
    	<td class='soustitre2'><textarea cols='40' rows='3' name='rfac_intro'></textarea></td>
		<td class='soustitre2'><input type='hidden' value='rang_ajout' name='methode' />
			<input type='hidden' value='$fac_cod' name='fac_cod' />
			<input type='submit' class='test' value='Ajouter' /></td></tr></form>";
	echo '</table></div>';
}
