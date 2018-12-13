<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");

if (!isset($cbar_cod))
	$cbar_cod = -1;


//echo '<div class="bordiv" style="padding:0">';
echo '<div class="barrTitle" onclick="permutte_cadre(this.parentNode);" id="cadre_barde">Concours de barde</div><br />';

// Nombre maximal de membres du jury
$nbJury = 10;

// Validations de formulaire

switch ($methode)
{
	case 'barde_modif':	// Modification d’un concours existant
		$form_cod = pg_escape_string($_POST['form_cod']);
		$form_saison = "cbar_saison='" . pg_escape_string($_POST['form_saison']) . "',";
		$form_date_ouverture = "cbar_date_ouverture='" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
		$form_date_teaser = "cbar_date_teaser='" . pg_escape_string($_POST['form_date_teaser']) . "'::timestamp,";
		$form_fermeture = "cbar_fermeture='" . pg_escape_string($_POST['form_fermeture']) . "'::timestamp,";
		$form_description = "cbar_description='" . pg_escape_string($_POST['form_description']) . "'";

		$db->query("UPDATE concours_barde SET $form_saison $form_date_ouverture $form_date_teaser $form_fermeture $form_description WHERE cbar_cod=$form_cod");

		// Modification des jurys
		for ($i = 1; $i <= $nbJury; $i++)
		{
			$form_jury = pg_escape_string($_POST["form_jury$i"]);
			$form_jury_cod = (isset($_POST["form_jury_cod$i"])) ? $_POST["form_jury_cod$i"] : '';
			// Cas update
			if ($form_jury != '' && $form_jury_cod != '')
				$db->query("UPDATE concours_barde_jury SET jbar_perso_cod = $form_jury WHERE jbar_cod=$form_jury_cod");
			// Cas delete
			if ($form_jury == '' && $form_jury_cod != '')
				$db->query("DELETE FROM concours_barde_jury WHERE jbar_cod=$form_jury_cod");
			// Cas insert
			if ($form_jury != '' && $form_jury_cod == '')
				$db->query("INSERT INTO concours_barde_jury(jbar_cbar_cod, jbar_perso_cod) VALUES($form_cod, $form_jury)");
		}
		echo '<p>Modification effectuée</p>';
		$methode = 'barde_visu';
	break;
	case 'barde_creation':	// Création d’un concours
		$form_saison = "'" . pg_escape_string($_POST['form_saison']) . "',";
		$form_date_ouverture = "'" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
		$form_date_teaser = "'" . pg_escape_string($_POST['form_date_teaser']) . "'::timestamp,";
		$form_fermeture = "'" . pg_escape_string($_POST['form_fermeture']) . "'::timestamp,";
		$form_description = "'" . pg_escape_string($_POST['form_description']) . "'";

		$req_nextval = "select nextval('concours_barde_cbar_cod_seq') as cbar_cod";
		$db->query($req_nextval);
		$db->next_record();
		$cbar_cod = $db->f('cbar_cod');

		$db->query("INSERT INTO concours_barde (cbar_cod, cbar_saison, cbar_date_ouverture, cbar_date_teaser, cbar_fermeture, cbar_description) VALUES ($cbar_cod, $form_saison $form_date_ouverture $form_date_teaser $form_fermeture $form_description)");

		// Modification des jurys
		for ($i = 1; $i <= 5; $i++)
		{
			$form_jury = pg_escape_string($_POST["form_jury$i"]);

			if ($form_jury != '')
				$db->query("INSERT INTO concours_barde_jury(jbar_cbar_cod, jbar_perso_cod) VALUES($cbar_cod, $form_jury)");
		}
		echo '<p>Création effectuée</p>';
		$methode = 'barde_visu';
	break;
	default:
	break;
}

$req_concours = 'select cbar_cod, cbar_saison, cbar_date_ouverture, cbar_date_teaser, cbar_fermeture, cbar_description,
					case when CURRENT_DATE between cbar_date_teaser and cbar_date_ouverture then 1 else 0 end as introduction,
					case when CURRENT_DATE between cbar_date_ouverture and cbar_fermeture then 1 else 0 end as ouvert,
					case when CURRENT_DATE < cbar_date_teaser then 1 else 0 end as futur,
					case when CURRENT_DATE > cbar_fermeture then 1 else 0 end as ferme
				from concours_barde order by cbar_saison';
$db->query($req_concours);

echo '<table>
	<tr>
		<td class="titre">Saison</td>
		<td class="titre">Détails</td>
	</tr>
	<tr>
		<td class="soustitre2">';

$toutesPassees = true;

while ($db->next_record())
{
	// Au passage, pendant le parcours, on enregistre les valeurs de celle qu’on va afficher.
	if ($cbar_cod == $db->f('cbar_cod'))
	{
		$cbar_saison = $db->f('cbar_saison');
		$cbar_date_ouverture = $db->f('cbar_date_ouverture');
		$cbar_date_teaser = $db->f('cbar_date_teaser');
		$cbar_fermeture = $db->f('cbar_fermeture');
		$cbar_description = $db->f('cbar_description');
		$introduction = ($db->f('introduction') == 1);
		$ouvert = ($db->f('ouvert') == 1);
		$futur = ($db->f('futur') == 1);
		$ferme = ($db->f('ferme') == 1);
	}
	$texte_etat = '';
	if ($db->f('ferme') != 1)
		$toutesPassees = false;

	if ($db->f('ouvert') == 1)
		$texte_etat = ' (ouverte)';
	if ($db->f('introduction') == 1)
		$texte_etat = ' (annoncée)';
	if ($db->f('futur') == 1)
		$texte_etat = ' (future)';
	if ($db->f('ferme') == 1)
		$texte_etat = ' (fermée)';

	if ($cbar_cod == $db->f('cbar_cod'))
		echo "<p><strong><a href='?methode=barde_visu&cbar_cod=" . $db->f('cbar_cod') . "'>Saison " . $db->f('cbar_saison') . "$texte_etat</a></strong></p>";
	else
		echo "<p><a href='?methode=barde_visu&cbar_cod=" . $db->f('cbar_cod') . "'>Saison " . $db->f('cbar_saison') . "$texte_etat</a></p>";
}
echo "</td>";

switch ($methode)
{
	case 'debut':		// Affichage initial vide
		echo '<td></td>';
		break;
	case 'barde_visu':		// Affichage des données d'une session

		// Récupération des membres du jury
		$req_jury = "select jbar_cod, jbar_perso_cod, perso_nom
						from concours_barde_jury
						left outer join perso on perso_cod = jbar_perso_cod
						where jbar_cbar_cod = $cbar_cod
						order by jbar_cod";
		$db->query($req_jury);
		echo '<td>';
		echo '<form name="modification" method="POST" action="#">
			<input type="hidden" name="methode" value="barde_modif" />
			<input type="hidden" name="form_cod" value="' . $cbar_cod . '" />';
		echo '<table><tr><td colspan="3" class="titre">Saison ' . $cbar_saison . '</td></tr>';
		echo '<tr><td colspan="3" class="soustitre2">';
		if ($ouvert)
			echo 'Cette session du concours de barde est <strong>ouverte</strong>';
		if ($introduction)
			echo 'Cette session du concours de barde est <strong>annoncée</strong>';
		if ($futur)
			echo 'Cette session du concours de barde est <strong>future</strong>';
		if ($ferme)
			echo 'Cette session du concours de barde est <strong>fermée</strong>';
		echo '</td></tr>';
		echo '<tr><td class="soustitre2">Saison</td><td><input type="text" name="form_saison" value="' . $cbar_saison . '" /></td><td>Dénomination de la saison (typiquement, l’année).</td></tr>';
		echo '<tr><td class="soustitre2">Date d’annonce (aaaa-mm-jj, jour inclus)</td><td><input type="text" name="form_date_teaser" value="' . $cbar_date_teaser . '" /></td><td>La date à laquelle la page du concours devient accessible.</td></tr>';
		echo '<tr><td class="soustitre2">Date d’ouverture (aaaa-mm-jj, jour inclus)</td><td><input type="text" name="form_date_ouverture" value="' . $cbar_date_ouverture . '" /></td><td>La date à laquelle on peut commencer à proposer des textes.</td></tr>';
		echo '<tr><td class="soustitre2">Date de fermeture (aaaa-mm-jj, jour exclus)</td><td><input type="text" name="form_fermeture" value="' . $cbar_fermeture . '" /></td><td>La date à laquelle plus aucun texte n’est accepté.</td></tr>';
		echo '<tr><td class="soustitre2" colspan="2"><p>Descriptif (html)</p><textarea rows="10" cols="50" name="form_description">' . $cbar_description . '</textarea></td><td>Le texte qui apparaîtra en en-tête de la page du concours.</td></tr>';
		$i = 1;
		while ($db->next_record())
		{
			echo "<tr><td class='soustitre2'>Jury $i</td><td><input type='text' name='form_jury$i' value='" . $db->f('jbar_perso_cod') . "' />
				<input type='hidden' name='form_jury_cod$i' value='" . $db->f('jbar_cod') . "' />
				</td><td>(" . $db->f('perso_nom') . ")</td></tr>";
			$i++;
		}
		// On complète à $nbJury
		while ($i <= $nbJury)
		{
			echo "<tr><td class='soustitre2'>Jury $i</td><td><input type='text' name='form_jury$i' value='' /></td></td><td></tr>";
			$i++;
		}
		echo '</table>';

		// Si le concours n'est pas fermé, on peut changer les paramètres
		if (!$ferme)
			echo '<input type="submit" value="Valider" />';
		if ($ferme)
			echo '<i>Cette instance est fermée et n’est plus modifiable</i>';
		echo '</form>';
		echo '</td>';
		break;
	case 'barde_nouvelle':	// Formulaire vierge
?>
	<td>
		<form name="creation" method="POST" action="#">
			<input type="hidden" name="methode" value="barde_creation" />
			<table>
				<tr><td colspan="3" class="titre">Nouvelle Saison</td></tr>
				<tr><td class="soustitre2">Saison</td><td><input type="text" name="form_saison" value="" /></td><td>L’année du concours.</td></tr>
				<tr><td class="soustitre2">Date d’annonce (aaaa-mm-jj)</td><td><input type="text" name="form_date_teaser" value="" /></td><td>La date à laquelle la page du concours devient accessible.</td></tr>
				<tr><td class="soustitre2">Date d’ouverture (aaaa-mm-jj)</td><td><input type="text" name="form_date_ouverture" value="" /></td><td>La date à laquelle on peut commencer à proposer des textes.</td></tr>
				<tr><td class="soustitre2">Date de fermeture (aaaa-mm-jj)</td><td><input type="text" name="form_fermeture" value="" /></td><td>La date à laquelle plus aucun texte n’est accepté.</td></tr>
				<tr><td class="soustitre2" colspan="2"><p>Descriptif (html)</p><textarea rows="10" cols="50" name="form_description"></textarea></td><td>Le texte qui apparaîtra en en-tête de la page du concours.</td></tr>
<?php 
		for ($i = 1; $i <= $nbJury; $i++)
		{
			echo "<tr><td class='soustitre2'>Jury $i</td><td><input type='text' name='form_jury$i' value='' /></td><td></td></tr>";
		}
?>
			</table>
			<input type="submit" class="test" value="Valider" />
		</form>
	</td>
<?php 
		break;
	default:
		break;
}
echo '</tr>';

// Si toutes les saisons sont passées, on laisse apparaître le bouton de création
if ($toutesPassees)
	echo '<tr>
		<td>
			<form name="nouvelleSaison" action="#" method="POST">
				<input type="hidden" name="methode" value="barde_nouvelle" />
				<input type="submit" class="test" value="Nouvelle saison" />
			</form>
		</td>
		<td></td>
	</tr>';
echo '</table>';
echo '</div>';
?>
