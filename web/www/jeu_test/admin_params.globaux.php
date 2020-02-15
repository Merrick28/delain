<?php 
if(!defined("APPEL"))
    die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Paramètres globaux</div><br />';

$erreur = false;
$message_erreur = '';
$methode = $_REQUEST['methode'];
switch ($methode)
{
	case 'glob_modif':    // Modifie un paramètre global
		$erreur = !isset($parm_cod) || !isset($parm_type) || !isset($parm_desc) || !isset($parm_valeur)
			|| !is_numeric($parm_cod);
		$message_erreur = '';
		$texte_orig = '';
		$valeur_orig = '';
		$type_orig = '';
		if ($erreur)
		{
			$message_erreur = 'Paramètres manquants.';
			break;
		}
		else
		{
			$parm_cod = (int) $parm_cod;

			$req_verif = "select
					lower(parm_type) as parm_type, parm_desc,
					case lower(parm_type) when 'integer' then parm_valeur::text else parm_valeur_texte end as parm_valeur
				from parametres where parm_cod = $parm_cod";
			$stmt = $pdo->query($req_verif);

			$erreur = (!$result = $stmt->fetch()) || ($parm_type != 'integer' && $parm_type != 'text')
				|| ($parm_type == 'integer' && !is_numeric($parm_valeur));
		}

		if ($erreur)
		{
			$message_erreur = 'Paramètres invalides.';
			break;
		}
		else
		{
			$texte_orig = $result['parm_desc'];
			$valeur_orig = $result['parm_valeur'];
			$type_orig = $result['parm_type'];
			$log .= "	Modification du paramètre n°$parm_cod « $texte_orig ».\n";

			if ($parm_type == 'integer')
				$parm_valeur = (int) $parm_valeur;
			else
				$parm_valeur = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $parm_valeur))));
			$parm_desc = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $parm_desc))));

			if ($texte_orig != $parm_desc)
				$log .= "	Modification description : « $parm_desc ».\n";
			if (strtolower($type_orig) != $parm_type)
				$log .= "	Modification du type : « $type_orig » => « $parm_type ».\n";
			if ($valeur_orig != $parm_valeur)
				$log .= "	Modification de la valeur : « $valeur_orig » => « $parm_valeur ».\n";

			$champ_valeur = ($parm_type == 'integer') ? 'parm_valeur' : 'parm_valeur_texte';

			// on update memcached
			$param = new parametres();


			$param->charge($parm_cod);
			$param->parm_type = $parm_type;
			$param->$champ_valeur = $parm_valeur;
			$param->parm_desc = $parm_desc;
			$param->stocke();
		}
	break;

	case 'glob_creation': // Créer un paramètre global
		$erreur = !isset($parm_type) || !isset($parm_desc) || !isset($parm_valeur);
		$message_erreur = '';
		if ($erreur)
		{
			$message_erreur = 'Paramètres manquants.';
			break;
		}
		else
		{
			$erreur = ($parm_type != 'integer' && $parm_type != 'text')
				|| ($parm_type == 'integer' && !is_numeric($parm_valeur));
		}

		if ($erreur)
		{
			$message_erreur = 'Paramètres invalides.';
			break;
		}
		else
		{
			if ($parm_type == 'integer')
				$parm_valeur = (int) $parm_valeur;
			else
				$parm_valeur = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $parm_valeur))));
			$parm_desc = pg_escape_string(nl2br(htmlspecialchars(str_replace('\'', '’', $parm_desc))));

			$log .= "	Création du paramètre « $parm_desc » :\n";
			$log .= "		Type : « $parm_type ».\n";
			$log .= "		Valeur : « $parm_valeur ».\n";

			$champ_valeur = ($parm_type == 'integer') ? 'parm_valeur' : 'parm_valeur_texte';

			$req_ins = "insert into parametres (parm_type, $champ_valeur, parm_desc)
				values ('$parm_type', '$parm_valeur', '$parm_desc')";
			$stmt = $pdo->query($req_ins);
		}
	break;
}
if (!$erreur && $log != '')
{
	echo "<div class='bordiv'><strong>Mise à jour des paramètres globaux</strong><br /><pre>$log</pre></div>";
	writelog($log,'params');
}
else if ($erreur && $message_erreur != '')
{
	echo "<div class='bordiv'><strong>Erreur !</strong><br /><pre>$message_erreur</pre></div>";
}

echo '<p>Liste des paramètres globaux du jeu</p>
	<table><tr>
		<td class="titre"><strong>Id</strong></td>
		<td class="titre"><strong>Description</strong></td>
		<td class="titre"><strong>Type</strong></td>
		<td class="titre"><strong>Valeur</strong></td>
		<td class="titre"><strong>Modifier ?</strong></td></tr>';
echo "<tr><form method='POST' action='#'>
	<td class='titre' style='padding:2px;'></td>
	<td class='titre' style='padding:2px;'><input name='parm_desc' type='text' size='50' /></td>
	<td class='titre' style='padding:2px;'><select name='parm_type'><option value='integer'>Numérique</option><option value='text'>Textuel</option></select></td>
	<td class='titre' style='padding:2px;'><input name='parm_valeur' type='text' size='10' /></td>
	<td class='titre' style='padding:2px;'><input type='hidden' name='methode' value='glob_creation' /><input type='submit' value='Ajouter' class='test' /></td>
	</form></tr>";
$req = "select parm_cod, lower(parm_type) as parm_type, parm_desc,
		case lower(parm_type) when 'integer' then parm_valeur::text else parm_valeur_texte end as parm_valeur
	from parametres order by parm_cod";
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
	$parm_cod = $result['parm_cod'];
	$parm_desc = str_replace('\'', '’', $result['parm_desc']);
	$parm_valeur = str_replace('\'', '’', $result['parm_valeur']);
	$parm_type = $result['parm_type'];

	$int_selected = ($parm_type == 'integer') ? 'selected="selected"' : '' ;
	$tex_selected = ($parm_type == 'text') ? 'selected="selected"' : '' ;

	echo "<tr><form method='POST' action='#' onsubmit='return confirm(\"Êtes-vous sûr de vouloir modifier ce paramètre ?\");'>
		<td style='padding:2px;'>$parm_cod</td>
		<td style='padding:2px;'><input name='parm_desc' type='text' size='50' value='$parm_desc' /></td>
		<td style='padding:2px;'><select name='parm_type'>
			<option value='integer' $int_selected>Numérique</option>
			<option value='text' $tex_selected>Textuel</option></select></td>
		<td style='padding:2px;'><input name='parm_valeur' type='text' size='10' value='$parm_valeur' /></td>";
	echo "<td style='padding:2px;'>
		<input type='hidden' name='methode' value='glob_modif' />
		<input type='hidden' name='parm_cod' value='$parm_cod' />
		<input type='submit' value='Modifier' class='test' />
		</td></form></tr>";
}
echo '</table></div>';
