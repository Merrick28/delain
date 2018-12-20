<?php
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//

ob_start();

$erreur = 0;
$req = "select dcompt_enchantements from compt_droit where dcompt_compt_cod = $compt_cod";
$db->query($req);
if ($db->nf() == 0)
{
	echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
	$erreur = 1;
}
else
{
	$db->next_record();
	if ($db->f("dcompt_enchantements") != 'O')
	{
		echo "<p>Erreur ! Vous n’avez pas accès à cette page !</p>";
		$erreur = 1;
	}
}
if ($erreur == 0)
{
	if (!isset($_POST['methode']))
		$methode = 'debut';
	else
		$methode = $_POST['methode'];

	$resultat = '';

	switch ($methode)
	{
		case 'debut': break;
		case 'modif':
			if (isset($_POST['tbonus_cod']))
			{
				$tbonus_cod = $_POST['tbonus_cod'];
				$tonbus_libelle = pg_escape_string(str_replace('\'', '’', $_POST['tonbus_libelle']));
				$tbonus_nettoyable = (isset($_POST['tbonus_nettoyable'])) ? 'O' : 'N';
				$tbonus_gentil_positif = (isset($_POST['tbonus_gentil_positif'])) ? 't' : 'f';
				$tbonus_libc = pg_escape_string(str_replace('\'', '’', $_POST['tbonus_libc']));

				$req = "UPDATE bonus_type SET
						tonbus_libelle = '$tonbus_libelle',
						tbonus_nettoyable = '$tbonus_nettoyable',
						tbonus_gentil_positif = '$tbonus_gentil_positif'
					WHERE tbonus_cod = $tbonus_cod";
				$db->query($req);
				$resultat = "<p>Bonus $tonbus_libelle ($tbonus_cod) mis à jour !</p><p>Requête : <pre>$req</pre></p>";
			}
			else
				$resultat = "<p>Erreur de paramètres</p>";
		break;
	}
	if ($resultat)
		echo "<div class='bordiv'>$resultat</div>";

	function ecrire_checkbox($label, $id_unique, $name, $valeur)
	{
		$checked = ($valeur == 'O' || $valeur == 't') ? 'checked="checked"' : '';
		return "<label for='$id_unique'>$label&nbsp;</label><input type='checkbox' $checked name='$name' id='$id_unique' />";
	}

	$req = 'SELECT
			tbonus_cod, tonbus_libelle, tbonus_libc, tbonus_nettoyable, tbonus_gentil_positif
		FROM bonus_type
		ORDER BY tbonus_libc';

	// Tableau des sorts runiques
	echo '<h1>Bonus et malus</h1><table>
		<tr>
			<th class="titre">Code court</th>
			<th class="titre">Libellé</th>
			<th class="titre">Nettoyable ?</th>
			<th class="titre">Valeur positive<br />pour un effet<br />bénéfique ?</th>
			<th class="titre">Action</th>
		</tr>';

	$db->query($req);

	while($db->next_record())
	{
		// Récupération des données
		$tbonus_cod = $db->f('tbonus_cod');
		$tonbus_libelle = $db->f('tonbus_libelle');
		$tbonus_nettoyable = $db->f('tbonus_nettoyable');
		$tbonus_gentil_positif = $db->f('tbonus_gentil_positif');
		$tbonus_libc = $db->f('tbonus_libc');

		echo "<form action='#' method='POST'><tr>
			<td class='soustitre2'>$tbonus_libc</td>
			<td class='soustitre2'><input type='text' value='$tonbus_libelle' name='tonbus_libelle' size='30' /></td>
			<td class='soustitre2'>" . ecrire_checkbox('', 'tbonus_nettoyable_' . $tbonus_cod, 'tbonus_nettoyable', $tbonus_nettoyable) . "</td>
			<td class='soustitre2'>" . ecrire_checkbox('', 'tbonus_gentil_positif_' . $tbonus_cod, 'tbonus_gentil_positif', $tbonus_gentil_positif) . "</td>
			<td class='soustitre2'><input type='hidden' value='$tbonus_cod' name='tbonus_cod' />
				<input type='hidden' value='modif' name='methode' />
				<input type='submit' class='test' value='Modifier' />
			</td>
		</tr></form>";
	}
	echo '</table>';
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
