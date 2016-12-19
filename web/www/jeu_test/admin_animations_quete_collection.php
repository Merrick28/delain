<?php 
if(!DEFINED("APPEL"))
	die("Erreur d’appel de page !");

if (!isset($ccol_cod))
	$ccol_cod = -1;

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Concours des collectionneurs</div><br />';
echo '<script type="text/javascript">
	function change_division(tableau, nom)
	{
		for (var i = 0; i < tableau.length; i++)
		{
			if (tableau[i] == nom)
				document.getElementById(nom).style.display = "";
			else
				document.getElementById(tableau[i]).style.display = "none";
		}
	}
	</script>';

// Nombre maximal de membres du jury
$nbJury = 10;

// Validations de formulaire

switch ($methode)
{
	case 'collection_modif':	// Modification d’un concours existant
		$form_cod = pg_escape_string($_POST['form_cod']);
		$form_titre = "ccol_titre='" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_titre']))) . "',";
		$form_date_ouverture = "ccol_date_ouverture='" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
		$form_date_fermeture = "ccol_date_fermeture='" . pg_escape_string($_POST['form_date_fermeture']) . "'::timestamp,";
		$form_differencier_4e = "ccol_differencier_4e='" . ((isset($_POST['form_differencier_4e'])) ? 'O' : 'N') . "',";
		$form_tranche_niveau = "ccol_tranche_niveau='" . pg_escape_string($_POST['form_tranche_niveau']) . "',";
		$form_description = "ccol_description='" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_description']))) . "',";
		$form_objet = "ccol_gobj_cod='" . pg_escape_string($_POST['form_objet']) . "'";

		$db->query("UPDATE concours_collections SET $form_titre $form_date_ouverture $form_date_fermeture $form_tranche_niveau $form_differencier_4e $form_description $form_objet WHERE ccol_cod=$form_cod");
		echo '<p>Modification effectuée</p>';
		$methode = 'collection_visu';

		$log = date("d/m/y - H:i") . "\tCompte $compt_cod modifie le concours de collection $form_titre (id = $form_cod).\n";
		writelog($log);
	break;
	case 'collection_creation':	// Création d’un concours
		$form_titre = "'" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_titre']))) . "',";
		$form_date_ouverture = "'" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
		$form_objet = "'" . pg_escape_string($_POST['form_objet']) . "',";
		$form_date_fermeture = "'" . pg_escape_string($_POST['form_date_fermeture']) . "'::timestamp,";
		$form_differencier_4e = "'" . ((isset($_POST['form_differencier_4e'])) ? 'O' : 'N') . "',";
		$form_tranche_niveau = "'" . pg_escape_string($_POST['form_tranche_niveau']) . "',";
		$form_description = "'" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_description']))) . "'";

		$db->query("INSERT INTO concours_collections (ccol_titre, ccol_date_ouverture, ccol_gobj_cod, ccol_date_fermeture, ccol_differencier_4e, ccol_tranche_niveau, ccol_description) VALUES ($form_titre $form_date_ouverture $form_objet $form_date_fermeture $form_differencier_4e $form_tranche_niveau $form_description)");

		echo '<p>Création effectuée</p>';
		$methode = 'debut';

		$log = date("d/m/y - H:i") . "\tCompte $compt_cod crée le concours de collection $form_titre.\n";
		writelog($log);
	break;
	default:
	break;
}

function getSelected($liste_id, $selected_id)
{
	return ($liste_id == $selected_id) ? ' selected="selected" ' : '';
}

$req_concours = 'select ccol_cod, ccol_titre, ccol_date_ouverture, ccol_gobj_cod, ccol_date_fermeture, ccol_description,
					case when CURRENT_DATE between ccol_date_ouverture and ccol_date_fermeture then 1 else 0 end as ouvert,
					case when CURRENT_DATE < ccol_date_ouverture then 1 else 0 end as futur,
					case when CURRENT_DATE > ccol_date_fermeture then 1 else 0 end as ferme,
					gobj_tobj_cod, ccol_tranche_niveau, ccol_differencier_4e
				from concours_collections
				left outer join objet_generique on gobj_cod = ccol_gobj_cod
				order by ccol_date_ouverture';
$db->query($req_concours);

echo '<table>
	<tr>
		<td class="titre">Titre</td>
		<td class="titre">Détails</td>
	</tr>
	<tr>
		<td class="soustitre2">';

$toutesPassees = true;

while ($db->next_record())
{
	// Au passage, pendant le parcours, on enregistre les valeurs de celle qu’on va afficher.
	if ($ccol_cod == $db->f('ccol_cod'))
	{
		$ccol_titre = $db->f('ccol_titre');
		$ccol_date_ouverture = $db->f('ccol_date_ouverture');
		$ccol_gobj_cod = $db->f('ccol_gobj_cod');
		$ccol_gobj_tobj_cod = $db->f('gobj_tobj_cod');
		$ccol_date_fermeture = $db->f('ccol_date_fermeture');
		$ccol_description = $db->f('ccol_description');
		$ccol_tranche_niveau = $db->f('ccol_tranche_niveau');
		$ccol_differencier_4e = $db->f('ccol_differencier_4e');
		$ouvert = ($db->f('ouvert') == 1);
		$futur = ($db->f('futur') == 1);
		$ferme = ($db->f('ferme') == 1);
	}
	$texte_etat = '';
	if ($db->f('ferme') != 1)
		$toutesPassees = false;

	if ($db->f('ouvert') == 1)
		$texte_etat = ' (ouverte)';
	if ($db->f('futur') == 1)
		$texte_etat = ' (future)';
	if ($db->f('ferme') == 1)
		$texte_etat = ' (fermée)';

	if ($ccol_cod == $db->f('ccol_cod'))
		echo "<p><b><a href='?methode=collection_visu&ccol_cod=" . $db->f('ccol_cod') . "'>" . $db->f('ccol_titre') . "$texte_etat</a></b></p>";
	else
		echo "<p><a href='?methode=collection_visu&ccol_cod=" . $db->f('ccol_cod') . "'>" . $db->f('ccol_titre') . "$texte_etat</a></p>";
}
echo "</td>";

switch ($methode)
{
	case 'debut':		// Affichage initial vide
		echo '<td></td>';
	break;

	case 'collection_visu':		// Affichage des données d’une session
		echo '<td>';
		echo '<form name="modification" method="POST" action="#">
			<input type="hidden" name="methode" value="collection_modif" />
			<input type="hidden" name="form_cod" value="' . $ccol_cod . '" />';
		echo '<table><tr><td colspan="3" class="titre">Titre ' . $ccol_titre . '</td></tr>';
		echo '<tr><td colspan="3" class="soustitre2">';
		if ($ouvert)
			echo 'Cette session du concours de collections est <b>ouverte</b>';
		if ($futur)
			echo 'Cette session du concours de collections est <b>future</b>';
		if ($ferme)
			echo 'Cette session du concours de collections est <b>fermée</b>';
		echo '</td></tr>';
		echo '<tr><td class="soustitre2">Titre</td><td><input type="text" name="form_titre" value="' . $ccol_titre . '" /></td><td>Dénomination du concours (typiquement, « Collections de citrouilles, 2010 »).</td></tr>';
		echo '<tr><td class="soustitre2">Objet de collection</td><td><select name="form_tobj_objet" onchange="filtrer_gobj(this.value, -1, \'form_objet\', tableauObjetsCollection);"><option value="-1">Choisissez un type d’objet...</option>';
		$req = 'select distinct tobj_cod, tobj_libelle from type_objet inner join objet_generique on gobj_tobj_cod = tobj_cod order by tobj_libelle';
		$db->query($req);
		$script_tobj = 'var tableauObjetsCollection = new Array();';
		while ($db->next_record())
		{
			$clef = $db->f('tobj_cod');
			$valeur = $db->f('tobj_libelle');
			$script_tobj .= "tableauObjetsCollection[$clef] = new Array();\n";
			echo "<option value='$clef'" . getSelected($clef, $ccol_gobj_tobj_cod) . ">$valeur</option>";
		}
		echo '</select><br /><select name="form_objet" id="form_objet">';
		$req = 'select gobj_cod, gobj_tobj_cod, gobj_nom from objet_generique order by gobj_tobj_cod, gobj_nom';
		$db->query($req);
		$script_gobj = '';
		while ($db->next_record())
		{
			$clef = $db->f('gobj_cod');
			$clef_tobj = $db->f('gobj_tobj_cod');
			$valeur = $db->f('gobj_nom');
			$script_gobj .= "tableauObjetsCollection[$clef_tobj][$clef] = \"" . str_replace('"', '', $valeur) . "\";\n";
		}

		echo '</select></td><td>L’objet que les participants devront collectionner (sélectionnez d’abord un type d’objet, puis un objet).</td></tr>';
		echo '<tr><td class="soustitre2">Date d’ouverture (aaaa-mm-jj, jour inclus)</td><td><input type="text" name="form_date_ouverture" value="' . $ccol_date_ouverture . '" /></td><td>La date à laquelle le concours commence.</td></tr>';
		echo '<tr><td class="soustitre2">Date de fermeture (aaaa-mm-jj, jour exclus)</td><td><input type="text" name="form_date_fermeture" value="' . $ccol_date_fermeture . '" /></td><td>La date à laquelle le concours est terminé.</td></tr>';
		echo '<tr><td class="soustitre2"><label for="differencier4e">Différencier les 4e persos</td><td><input type="checkbox" name="form_differencier_4e" id="differencier4e" ' . (($ccol_differencier_4e == 'O') ? 'checked="checked"' : '') . ' /></td><td>Indique si un classement hors 4e persos est également créé.</td></tr>';
		echo '<tr><td class="soustitre2">Tranches de niveau</td><td><input type="text" name="form_tranche_niveau" value="' . $ccol_tranche_niveau . '" /></td><td>Indique que l’on crée des classements par tranches de niveau. 0 si non, le nombre de niveaux par tranche si oui.</td></tr>';
		
		echo '<tr><td class="soustitre2" colspan="2"><p>Descriptif (html)</p><textarea rows="10" cols="50" name="form_description">' . $ccol_description . '</textarea></td><td>Le texte qui apparaîtra en en-tête de la page du concours.</td></tr>';
		echo '</table>';
		
		echo "<script type='text/javascript'>
				$script_tobj
				$script_gobj
				filtrer_gobj($ccol_gobj_tobj_cod, $ccol_gobj_cod, 'form_objet', tableauObjetsCollection);
			</script>";

		// Si le concours n'est pas fermé, on peut changer les paramètres
		if (!$ferme)
			echo '<input type="submit" value="Valider" />';
		if ($ferme)
			echo '<i>Cette instance est fermée et n’est plus modifiable</i>';
		echo '</form>';
		echo '<hr />';
		echo '<table><tr><td>';
		
		$req = "SELECT DISTINCT case when ccolres_division like 'Tous%' then 0 else 1 end, ccolres_division FROM concours_collections_resultats WHERE ccolres_ccol_cod = $ccol_cod
			ORDER BY case when ccolres_division like 'Tous%' then 0 else 1 end, ccolres_division";
		$db->query($req);
		$nombre_divisions = $db->nf();
		$tr_divisions = '';
		$script_divisions = '<script type="text/javascript">var divisionsConsolide = new Array();
			var divisionsInstantane = new Array();';
		$script_division1 = '';
		$i = 0;
		if ($nombre_divisions > 1)
		{
			$tr_divisions = '<tr><td class="soustitre2" colspan="2">Classements : ';
			while ($db->next_record())
			{
				$code = $db->f('ccolres_division');
				$tr_divisions .= "<span onclick=\"javascript:change_division(divisions#table#, '#table#|$code')\">#g1#" . $db->f('ccolres_division') . '#g2#</span>&nbsp; &nbsp;';
				$script_divisions .= "divisionsConsolide[$i] = 'Consolide|$code';";
				$script_divisions .= "divisionsInstantane[$i] = 'Instantane|$code';";
				if ($i == 0) $script_division1 = "change_division(divisionsInstantane, 'Instantane|$code');
					change_division(divisionsConsolide, 'Consolide|$code');";
				$i++;
				$derniere_division = $code;
			}
			$tr_divisions .= '</td></tr>';
		}
		$script_divisions .= $script_division1 . '</script>';
		$style_div = ($nombre_divisions > 1) ? ' style="display:none;"' : '';
		
		$i = 1; // Compteur de divisions pour bien gérer la dernière

		// Classement instantané
		// Concours général
		$debut_table = '<table id="Instantane|#id#"' . $style_div . '>
			<tr><th class="titre" colspan="2">Classement actuel instantané<br />(récupéré d’après les inventaires)</th></tr>';
		$debut_table .= str_replace('#table#', 'Instantane', $tr_divisions);
		$debut_table .= '<tr><th class="titre">Aventurier</th><th class="titre">Nombre d’objets</th></tr>';
		$fin_table = '</table>';

		$req = "select perso_nom || '(' || perso_cod::text || ')' as perso_nom, count(*) as nombre
			from objets
			inner join perso_objets on perobj_obj_cod = obj_cod
			inner join perso on perso_cod = perobj_perso_cod
			inner join perso_position on ppos_perso_cod = perso_cod
			inner join positions on pos_cod = ppos_pos_cod
			inner join etage on etage_numero = pos_etage
			where obj_gobj_cod = $ccol_gobj_cod
				AND etage_reference <> -100
				AND perso_actif <> 'N'
			group by perso.perso_nom || '(' || perso_cod::text || ')'
			order by nombre desc
			limit 10";
		$db->query($req);
		$txt_table = str_replace('#id#', 'Tous aventuriers', $debut_table);
		$txt_table = str_replace('#g1#Tous aventuriers#g2#', '<b>Tous aventuriers</b>', $txt_table);
		$txt_table = str_replace('#g1#', '', $txt_table);
		$txt_table = str_replace('#g2#', '', $txt_table);
		echo $txt_table;
		while ($db->next_record())
		{
			$nom = $db->f('perso_nom');
			$nombre = $db->f('nombre');
			echo "\n<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
		}
		echo $fin_table;

		// Cas des concours distinguant 4e personnages
		if ($ccol_differencier_4e == 'O')
		{
			$debut_table = '<table id="Instantane|#id#"' . $style_div . '>
				<tr><th class="titre" colspan="2">Classement actuel instantané<br />(récupéré d’après les inventaires)</th></tr>';
			$debut_table .= str_replace('#table#', 'Instantane', $tr_divisions);
			$debut_table .= '<tr><th class="titre">Aventurier</th><th class="titre">Nombre d’objets</th></tr>';
			$fin_table = '</table>';

			$req = "select p.perso_nom || '(' || p.perso_cod::text || ')' as perso_nom, count(*) as nombre
				from objets
				inner join perso_objets on perobj_obj_cod = obj_cod
				inner join perso p on p.perso_cod = perobj_perso_cod
				inner join perso_position on ppos_perso_cod = perso_cod
				inner join positions on pos_cod = ppos_pos_cod
				inner join etage on etage_numero = pos_etage
				left outer join perso_familier on pfam_familier_cod = p.perso_cod
				left outer join perso maitre on maitre.perso_cod = pfam_perso_cod
				where obj_gobj_cod = $ccol_gobj_cod
					AND etage_reference <> -100
					AND p.perso_actif <> 'N'
					AND p.perso_pnj = 0
					AND (maitre.perso_pnj = 0 OR maitre.perso_pnj IS NULL)
				group by p.perso_nom || '(' || p.perso_cod::text || ')'
				order by nombre desc
				limit 10";
			$db->query($req);
			$txt_table = str_replace('#id#', 'Hors quatrièmes', $debut_table);
			$txt_table = str_replace('#g1#Hors quatrièmes#g2#', '<b>Hors quatrièmes</b>', $txt_table);
			$txt_table = str_replace('#g1#', '', $txt_table);
			$txt_table = str_replace('#g2#', '', $txt_table);
			echo $txt_table;
			while ($db->next_record())
			{
				$nom = $db->f('perso_nom');
				$nombre = $db->f('nombre');
				echo "\n<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
			}
			echo $fin_table;
		}
		
		// Cas des concours distinguant des tranches de niveau
		if ($ccol_tranche_niveau > 0)
		{
			// Détermination du niveau maximal
			$req = "select max(perso_niveau) as niveau
				from perso
				inner join perso_position on ppos_perso_cod = perso_cod
				inner join positions on pos_cod = ppos_pos_cod
				inner join etage on etage_numero = pos_etage
				where etage_reference <> -100
					AND perso_actif <> 'N'
					AND perso_type_perso = 1";
			$db->query($req);
			$db->next_record();
			$tranche_min = 1;
			$max_tranche_min = $db->f('niveau');
			
			while ($tranche_min <= $max_tranche_min)
			{
				$i++;
				if ($i == $nombre_divisions)
				{
					$where_niveau = "(maitre.perso_niveau IS NULL AND p.perso_niveau >= $tranche_min
							OR maitre.perso_niveau >= $tranche_min)";
					$texte_tranche = $derniere_division;
				}
				else
				{
					$where_niveau = "(maitre.perso_niveau IS NULL AND p.perso_niveau >= $tranche_min AND p.perso_niveau < $tranche_min + $ccol_tranche_niveau
							OR maitre.perso_niveau >= $tranche_min AND maitre.perso_niveau < $tranche_min + $ccol_tranche_niveau)";
					$texte_tranche = "Niv. $tranche_min à " . ($tranche_min + $ccol_tranche_niveau - 1);
				}
				$debut_table = '<table id="Instantane|#id#"' . $style_div . '>
					<tr><th class="titre" colspan="2">Classement actuel instantané<br />(récupéré d’après les inventaires)</th></tr>';
				$debut_table .= str_replace('#table#', 'Instantane', $tr_divisions);
				$debut_table .= '<tr><th class="titre">Aventurier</th><th class="titre">Nombre d’objets</th></tr>';
				$fin_table = '</table>';

				$req = "select p.perso_nom || '(' || p.perso_cod::text || ')' as perso_nom, count(*) as nombre
					from objets
					inner join perso_objets on perobj_obj_cod = obj_cod
					inner join perso p on p.perso_cod = perobj_perso_cod
					inner join perso_position on ppos_perso_cod = perso_cod
					inner join positions on pos_cod = ppos_pos_cod
					inner join etage on etage_numero = pos_etage
					left outer join perso_familier on pfam_familier_cod = p.perso_cod
					left outer join perso maitre on maitre.perso_cod = pfam_perso_cod
					where obj_gobj_cod = $ccol_gobj_cod
						AND etage_reference <> -100
						AND p.perso_actif <> 'N'
						AND p.perso_pnj = 0
						AND $where_niveau
					group by p.perso_nom || '(' || p.perso_cod::text || ')'
					order by nombre desc
					limit 10";
				$db->query($req);
				$txt_table = str_replace('#id#', $texte_tranche, $debut_table);
				$txt_table = str_replace('#g1#' . $texte_tranche . '#g2#', '<b>' . $texte_tranche . '</b>', $txt_table);
				$txt_table = str_replace('#g1#', '', $txt_table);
				$txt_table = str_replace('#g2#', '', $txt_table);
				echo $txt_table;
				while ($db->next_record())
				{
					$nom = $db->f('perso_nom');
					$nombre = $db->f('nombre');
					echo "\n<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
				}
				echo $fin_table;
				$tranche_min = $tranche_min + $ccol_tranche_niveau;
			}
		}
		
		echo '</td><td>';
		
		// Classement consolidé
		$debut_table = '<table id="Consolide|#id#"' . $style_div . '>
			<tr><th class="titre" colspan="2">Classement actuel consolidé<br />(récupéré d’après la consolidation, donc vide<br />
				avant le concours, et figé après la fin du concours.)
			</th></tr>';
		$debut_table .= str_replace('#table#', 'Consolide', $tr_divisions);
		$debut_table .= '<tr><th class="titre">Aventurier</th><th class="titre">Nombre d’objets</th></tr>';
		$fin_table = '</table>';

		$req = "select coalesce(perso_nom, 'Aventurier disparu') as perso_nom, ccolres_nombre, ccolres_division from concours_collections_resultats
			left outer join perso on perso_cod = ccolres_perso_cod
			where ccolres_ccol_cod = $ccol_cod
			order by case when ccolres_division like 'Tous%' then 0 else 1 end, ccolres_division, ccolres_nombre desc";
		$db2->query($req);
		$division_en_cours = -1;

		while ($db2->next_record())
		{
			if ($db2->f('ccolres_division') != $division_en_cours)
			{
				if ($division_en_cours != -1)
					echo $fin_table;
				$txt_table = str_replace('#id#', $db2->f('ccolres_division'), $debut_table);
				$txt_table = str_replace('#g1#' . $db2->f('ccolres_division') . '#g2#', '<b>' . $db2->f('ccolres_division') . '</b>', $txt_table);
				$txt_table = str_replace('#g1#', '', $txt_table);
				$txt_table = str_replace('#g2#', '', $txt_table);
				echo $txt_table;
				$division_en_cours = $db2->f('ccolres_division');
			}
			$nom = $db2->f('perso_nom');
			$nombre = $db2->f('ccolres_nombre');
			echo "\n<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
		}
		echo $fin_table;
		echo $script_divisions;
		echo '</td></tr></table>';
		echo '</td>';
	break;

	case 'collection_nouvelle':	// Formulaire vierge
?>
	<td>
		<form name="creation" method="POST" action="#">
			<input type="hidden" name="methode" value="collection_creation" />
			<table>
				<tr><td colspan="3" class="titre">Nouveau concours de collection</td></tr>
				<tr><td class="soustitre2">Titre</td><td><input type="text" name="form_titre" value="" /></td><td>Dénomination du concours (typiquement, « Collections de citrouilles, 2010 »).</td></tr>	
<?php 		echo '<tr><td class="soustitre2">Objet de collection</td><td><select name="form_tobj_objet" onchange="filtrer_gobj(this.value, -1, \'form_objet\', tableauObjetsCollection);"><option value="-1">Choisissez un type d’objet...</option>';
		$req = 'select distinct tobj_cod, tobj_libelle from type_objet inner join objet_generique on gobj_tobj_cod = tobj_cod order by tobj_libelle';
		$db->query($req);
		$script_tobj = 'var tableauObjetsCollection = new Array();';
		while ($db->next_record())
		{
			$clef = $db->f('tobj_cod');
			$valeur = $db->f('tobj_libelle');
			$script_tobj .= "tableauObjetsCollection[$clef] = new Array();\n";
			echo "<option value='$clef'>$valeur</option>";
		}
		echo '</select><br /><select name="form_objet" id="form_objet">';
		$req = 'select gobj_cod, gobj_tobj_cod, gobj_nom from objet_generique order by gobj_tobj_cod, gobj_nom';
		$db->query($req);
		$script_gobj = '';
		while ($db->next_record())
		{
			$clef = $db->f('gobj_cod');
			$clef_tobj = $db->f('gobj_tobj_cod');
			$valeur = $db->f('gobj_nom');
			$script_gobj .= "tableauObjetsCollection[$clef_tobj][$clef] = \"" . str_replace('"', '', $valeur) . "\";\n";
		}

		echo '</select></td><td>L’objet que les participants devront collectionner (sélectionnez d’abord un type d’objet, puis un objet).</td></tr>';
?>
				<tr><td class="soustitre2">Date d’ouverture (aaaa-mm-jj)</td><td><input type="text" name="form_date_ouverture" value="" /></td><td>La date à laquelle le concours commence.</td></tr>
				<tr><td class="soustitre2">Date de fermeture (aaaa-mm-jj)</td><td><input type="text" name="form_date_fermeture" value="" /></td><td>La date à laquelle le concours est terminé.</td></tr>
				<tr><td class="soustitre2"><label for="differencier4e">Différencier les 4e persos</td><td><input type="checkbox" name="form_differencier_4e" id="differencier4e" /></td><td>Indique si un classement hors 4e persos est également créé.</td></tr>
				<tr><td class="soustitre2">Tranches de niveau</td><td><input type="text" name="form_tranche_niveau" value="" /></td><td>Indique que l’on crée des classements par tranches de niveau. 0 si non, le nombre de niveaux par tranche si oui.</td></tr>
				<tr><td class="soustitre2" colspan="2"><p>Descriptif (html)</p><textarea rows="10" cols="50" name="form_description"></textarea></td><td>Le texte qui apparaîtra en en-tête de la page du concours.</td></tr>
			</table>
			<input type="submit" class="test" value="Valider" />
		</form>
<?php 		echo "<script type='text/javascript'>
				$script_tobj
				$script_gobj
			</script>
		</td>";
		break;
	default:
		break;
}
echo '</tr>';

// Bouton de création (on peut créer plusieurs instances simultanément)
echo '<tr>
	<td>
		<form name="nouvelleCollection" action="#" method="POST">
			<input type="hidden" name="methode" value="collection_nouvelle" />
			<input type="submit" class="test" value="Nouveau concours de collection !" />
		</form>
	</td>
	<td></td>
</tr>';
echo '</table>';
echo '</div>';
?>
