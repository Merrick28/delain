<?php 
if(!defined("APPEL"))
	die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Renommées et Karma</div><br />';

$erreur = false;
$message_erreur = '';

// Pour factoriser le code, on commence par récupérer le nom des tables et colonnes pour le type de renommée sur lequel on travaille
$lesTypes = array('g', 'm', 'a', 'k');

$lesTables = array(
	'g' => 'renommee',
	'm' => 'renommee_magie',
	'a' => 'renommee_artisanat',
	'k' => 'karma',
);

$lesNoms = array(
	'g' => 'Renommée guerrière',
	'm' => 'Renommée magique',
	'a' => 'Renommée artisanale',
	'k' => 'Karma'
);

$lesColonnes = array(
	'g' => array(
		'cod' => 'renommee_cod',
		'min' => 'renommee_min',
		'max' => 'renommee_max',
		'lib' => 'renommee_libelle'
	),
	'm' => array(
		'cod' => 'grenommee_cod',
		'min' => 'grenommee_min',
		'max' => 'grenommee_max',
		'lib' => 'grenommee_libelle'
	),
	'a' => array(
		'cod' => 'renart_cod',
		'min' => 'renart_min',
		'max' => 'renart_max',
		'lib' => 'renart_libelle'
	),
	'k' => array(
		'cod' => 'karma_cod',
		'min' => 'karma_min',
		'max' => 'karma_max',
		'lib' => 'karma_libelle'
	),
);

$typeRenommee = '';

// Ce bloc peut sûrement être remplacé par une expression rationnelle ou un simple substring, pour déterminer le type...
// J’ai la flemme de le changer. Et je préfère le déterminisme ^^
switch ($methode)
{
	case 'ren_g_creation': case 'ren_g_modif': case 'ren_g_supp':
		$typeRenommee = 'g';
	break;
	case 'ren_m_creation': case 'ren_m_modif': case 'ren_m_supp':
		$typeRenommee = 'm';
	break;
	case 'ren_a_creation': case 'ren_a_modif': case 'ren_a_supp':
		$typeRenommee = 'a';
	break;
	case 'ren_k_creation': case 'ren_k_modif': case 'ren_k_supp':
		$typeRenommee = 'k';
	break;
	default: $typeRenommee = 'g'; break;
}
if ($typeRenommee != '')
{
	$table_ren = $lesTables[$typeRenommee];
	$col_cod = $lesColonnes[$typeRenommee]['cod'];
	$col_min = $lesColonnes[$typeRenommee]['min'];
	$col_max = $lesColonnes[$typeRenommee]['max'];
	$col_lib = $lesColonnes[$typeRenommee]['lib'];
	$log_renommee = $lesNoms[$typeRenommee];
}

switch ($methode)
{
	case 'ren_g_modif':    // Modifie une ligne de renommée
	case 'ren_a_modif':
	case 'ren_m_modif':
	case 'ren_k_modif':
		$erreur = !isset($renommee_cod) || !isset($renommee_min) || !isset($renommee_max) || !isset($renommee_libelle)
			|| !is_numeric($renommee_cod) || !is_numeric($renommee_min) || !is_numeric($renommee_max);
		$message_erreur = '';
		$renommee_min_orig = '';
		$renommee_max_orig = '';
		$renommee_libelle_orig = '';
		if ($erreur) $message_erreur = 'Paramètres manquants ou incorrects.';
		else
		{
			$req_verif = "select $col_min, $col_max, $col_lib
				from $table_ren where $col_cod = $renommee_cod";
			$stmt = $pdo->query($req_verif);

			$erreur = !$result = $stmt->fetch();
		}

		if ($erreur) $message_erreur = 'Ligne de renommée / karma inconnue.';
		else
		{
			$renommee_min_orig = $db->f($col_min);
			$renommee_max_orig = $db->f($col_max);
			$renommee_libelle_orig = $db->f($col_lib);
			$log .= "	Modification $log_renommee n°$renommee_cod « $renommee_libelle_orig ».\n";

			$renommee_libelle = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $renommee_libelle))));

			if ($renommee_libelle_orig != $renommee_libelle)
				$log .= "	Modification titre : « $renommee_libelle ».\n";
			if ($renommee_min_orig != $renommee_min || $renommee_max_orig != $renommee_max)
				$log .= "	Modification de l’intervalle : [$renommee_min_orig ; $renommee_max_orig] => [$renommee_min ; $renommee_max].\n";

			$req_upd = "update $table_ren set $col_min = $renommee_min, $col_max = $renommee_max, $col_lib = '$renommee_libelle'
				where $col_cod = $renommee_cod";
			$stmt = $pdo->query($req_upd);
		}
	break;

	case 'ren_g_creation':    // Créer une ligne de renommée
	case 'ren_a_creation':
	case 'ren_m_creation':
	case 'ren_k_creation':
		$erreur = !isset($renommee_min) || !isset($renommee_max) || !isset($renommee_libelle)
			|| !is_numeric($renommee_min) || !is_numeric($renommee_max);
		$message_erreur = '';
		if ($erreur) $message_erreur = 'Paramètres manquants ou incorrects.';
		else
		{
			$renommee_libelle = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $renommee_libelle))));

			$log .= "	Création de $log_renommee « $renommee_libelle » sur l’intervalle [$renommee_min ; $renommee_max].\n";

			$req_ins = "insert into $table_ren ($col_min, $col_max, $col_lib)
				values ($renommee_min, $renommee_max, '$renommee_libelle')";
			$stmt = $pdo->query($req_ins);
		}
	break;

	case 'ren_g_supp':    // Supprime une ligne de renommée
	case 'ren_a_supp':
	case 'ren_m_supp':
	case 'ren_k_supp':
		$erreur = !isset($renommee_cod) || !is_numeric($renommee_cod);
		$message_erreur = '';
		$renommee_min_orig = '';
		$renommee_max_orig = '';
		$renommee_libelle_orig = '';
		if ($erreur) $message_erreur = 'Paramètres manquants ou incorrects.';
		else
		{
			$req_verif = "select $col_min, $col_max, $col_lib
				from $table_ren where $col_cod = $renommee_cod";
			$stmt = $pdo->query($req_verif);

			$erreur = !$result = $stmt->fetch();
		}

		if ($erreur) $message_erreur = 'Ligne de renommée / karma inconnue.';
		else
		{
			$renommee_min_orig = $db->f($col_min);
			$renommee_max_orig = $db->f($col_max);
			$renommee_libelle_orig = $db->f($col_lib);
			$log .= "	Suppression de $log_renommee n°$renommee_cod « $renommee_libelle_orig » [$renommee_min_orig ; $renommee_max_orig].\n";

			$req_del = "delete from $table_ren where $col_cod = $renommee_cod";
			$stmt = $pdo->query($req_del);
		}
	break;
}
if (!$erreur && $log != '')
{
	echo "<div class='bordiv'><strong>Mise à jour de $log_renommee.</strong><br /><pre>$log</pre></div>";
	writelog($log,'params');
}
else if ($erreur && $message_erreur != '')
{
	echo "<div class='bordiv'><strong>Erreur !</strong><br /><pre>$message_erreur</pre></div>";
}

echo '<p>Liste des renommées / karma du jeu</p>';
echo '<script type="text/javascript">
	function montre_renommee(i)
	{
		document.getElementById("table_renommee_g").style.display = "none";
		document.getElementById("table_renommee_m").style.display = "none";
		document.getElementById("table_renommee_a").style.display = "none";
		document.getElementById("table_renommee_k").style.display = "none";
		document.getElementById("table_renommee_" + i).style.display = "block";

		document.getElementById("lien_g").style.fontWeight = "normal";
		document.getElementById("lien_m").style.fontWeight = "normal";
		document.getElementById("lien_a").style.fontWeight = "normal";
		document.getElementById("lien_k").style.fontWeight = "normal";
		document.getElementById("lien_" + i).style.fontWeight = "bold";
	}
	</script>';
echo '<p>Choisissez le type de renommée :
	<a href="javascript:montre_renommee(\'g\');" id="lien_g">Guerrière</a>,
	<a href="javascript:montre_renommee(\'m\');" id="lien_m">Magique</a>,
	<a href="javascript:montre_renommee(\'a\');" id="lien_a">Artisanale</a>,
	ou le <a href="javascript:montre_renommee(\'k\');" id="lien_k">Karma</a>.</p>';

foreach ($lesTypes as $i)
{
	$table_ren = $lesTables[$i];
	$col_cod = $lesColonnes[$i]['cod'];
	$col_min = $lesColonnes[$i]['min'];
	$col_max = $lesColonnes[$i]['max'];
	$col_lib = $lesColonnes[$i]['lib'];
	$log_renommee = $lesNoms[$i];
	echo "<table id='table_renommee_$i'><tr>
			<td class='titre' colspan='5'><strong>$log_renommee</strong></td></tr>";
	echo '<tr>
			<td class="titre"><strong>Titre</strong></td>
			<td class="titre"><strong>Intervalle</strong></td>
			<td class="titre" colspan="2"><strong>Action</strong></td>
			<td class="titre"><strong>Problèmes détectés</strong></td></tr>';
	echo "<tr><form method='POST' action='#'>
		<td class='titre' style='padding:2px;'><input name='renommee_libelle' type='text' size='25' /></td>
		<td class='titre' style='padding:2px;'><input name='renommee_min' type='text' size='6' /> / <input name='renommee_max' type='text' size='6' /></td>
		<td class='titre' style='padding:2px;' colspan='2'><input type='hidden' name='methode' value='ren_" . $i . "_creation' />
			<input type='submit' value='Ajouter' class='test' /></td><td class='titre'></td>
		</form></tr>";

	$req = "select $col_cod, $col_min, $col_max, $col_lib from $table_ren order by $col_min";
	$stmt = $pdo->query($req);
	$prev_max = false;
	while ($result = $stmt->fetch())
	{
		$renommee_cod = $db->f($col_cod);
		$renommee_min = $db->f($col_min);
		$renommee_max = $db->f($col_max);
		$renommee_libelle = str_replace('\'', '’', $db->f($col_lib));

		$erreur = false;
		$message_erreur = '';
		if ($prev_max !== false)
		{
			if ($prev_max > $renommee_min)
			{
				$erreur = true;
				$message_erreur = 'Cette plage recouvre la plage précédente !';
			}
			if ($prev_max < $renommee_min)
			{
				$erreur = true;
				$message_erreur = 'Cette plage est disjointe de la plage précédente !';
			}
			if ($renommee_max <= $renommee_min)
			{
				$erreur = true;
				$message_erreur = 'Les bornes de cette plage sont inversées ou égales !';
			}
		}
		$prev_max = $renommee_max;

		echo "<tr><form method='POST' action='#'>
			<td style='padding:2px;'><input name='renommee_libelle' type='text' size='25' value='$renommee_libelle' /></td>
			<td style='padding:2px;'><input name='renommee_min' type='text' size='6' value='$renommee_min' /> / 
				<input name='renommee_max' type='text' size='6' value='$renommee_max' /></td>";

		echo "<td style='padding:2px;'>
			<input type='hidden' name='methode' value='ren_" . $i . "_modif' />
			<input type='hidden' name='renommee_cod' value='$renommee_cod' />
			<input type='submit' value='Modifier' class='test' />
			</td></form>";
		echo "<td style='padding:2px;'><form method='POST' action='#' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer cette tranche de renommée ?\")'>
			<input type='hidden' name='methode' value='ren_" . $i . "_supp' />
			<input type='hidden' name='renommee_cod' value='$renommee_cod' />
			<input type='submit' value='Supprimer' class='test' />
			</form></td>";
		if ($erreur)
			echo "<td style='padding:2px; color:#660000'><p>$message_erreur</p></td>";
		else
			echo '<td></td>';
		echo "</tr>";
	}
}

if ($typeRenommee != '')
{
	echo "<script type='text/javascript'>
			montre_renommee('$typeRenommee');
		</script>";
}

echo '</table></div>';

