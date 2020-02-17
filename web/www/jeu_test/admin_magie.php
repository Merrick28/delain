<?php
include "blocks/_header_page_jeu.php";

ob_start();

$erreur = 0;

$droit_modif = 'dcompt_enchantements';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0) {
    $methode          = get_request_var('methode', 'debut');

    $resultat = '';

    switch ($methode) {
        case 'debut':
            break;
        case 'modif':
            if (isset($_POST['sort_cod'])) {
                $sort_cod = $_POST['sort_cod'];
                $sort_nom = pg_escape_string(str_replace('\'', '’', $_POST['sort_nom']));
                $sort_comp_cod = $_POST['sort_comp_cod'];
                $sort_niveau = $_POST['sort_niveau'];
                $sort_cout = $_POST['sort_cout'];
                $sort_distance = $_POST['sort_distance'];
                $sort_soi_meme = (isset($_POST['sort_soi_meme'])) ? 'O' : 'N';
                $sort_monstre = (isset($_POST['sort_monstre'])) ? 'O' : 'N';
                $sort_joueur = (isset($_POST['sort_joueur'])) ? 'O' : 'N';
                $sort_case = (isset($_POST['sort_case'])) ? 'O' : 'N';
                $sort_aggressif = (isset($_POST['sort_aggressif'])) ? 'O' : 'N';
                $sort_soutien = (isset($_POST['sort_soutien'])) ? 'O' : 'N';
                $sort_bloquable = (isset($_POST['sort_bloquable'])) ? 'O' : 'N';
                $sort_temps_recharge = $_POST['sort_temps_recharge'];
                $sort_description = pg_escape_string(str_replace('\'', '’', $_POST['sort_description']));

                $req = "UPDATE sorts SET
						sort_nom = '$sort_nom',
						sort_comp_cod = $sort_comp_cod,
						sort_niveau = $sort_niveau,
						sort_cout = $sort_cout,
						sort_distance = $sort_distance,
						sort_soi_meme = '$sort_soi_meme',
						sort_monstre = '$sort_monstre',
						sort_joueur = '$sort_joueur',
						sort_case = '$sort_case',
						sort_aggressif = '$sort_aggressif',
						sort_soutien = '$sort_soutien',
						sort_bloquable = '$sort_bloquable',
						sort_temps_recharge = $sort_temps_recharge,
						sort_description = '$sort_description'
					WHERE sort_cod = $sort_cod";
                $stmt = $pdo->query($req);
                $resultat = "<p>Sort $sort_nom ($sort_cod) mis à jour !</p><p>Requête : <pre>$req</pre></p>";
            } else
                $resultat = "<p>Erreur de paramètres</p>";
            break;
    }
    if ($resultat)
        echo "<div class='bordiv'>$resultat</div>";

    $req_comp = 'SELECT comp_cod, comp_libelle FROM competences WHERE comp_cod IN (50, 51)';
    $req_comp_complete = 'SELECT comp_cod, comp_libelle FROM competences WHERE comp_typc_cod = 5';


    function ecrire_checkbox($label, $id_unique, $name, $valeur)
    {
        $checked = ($valeur == 'O') ? 'checked="checked"' : '';
        return "<label for='$id_unique'>$label&nbsp;</label><input type='checkbox' $checked name='$name' id='$id_unique' />";
    }

    $req_runiques = 'SELECT
			sort_cod, sort_combinaison, sort_nom, sort_cout, sort_distance, sort_fonction,
			sort_comp_cod, sort_description, sort_aggressif, sort_niveau, sort_soi_meme,
			sort_monstre, sort_joueur, sort_soutien, sort_bloquable, sort_case, sort_temps_recharge
		FROM sorts
		INNER JOIN competences ON comp_cod = sort_comp_cod
		WHERE sort_combinaison NOT LIKE \'%9%\'
		ORDER BY sort_niveau, sort_nom';

    $req_divins = 'SELECT
			sort_cod, sort_nom, sort_cout, sort_distance, sort_description, 
			sort_aggressif, sort_niveau, sort_soi_meme, sort_monstre, sort_joueur, 
			sort_comp_cod, sort_soutien, sort_bloquable, sort_case, sort_temps_recharge,
			array_to_string(array_agg(dieu_nom), \', \') as dieux_nom, sort_fonction
		FROM sorts
		LEFT OUTER JOIN dieu_sorts ON dsort_sort_cod = sort_cod
		LEFT OUTER JOIN dieu ON dieu_cod = dsort_dieu_cod
		WHERE dieu_nom IS NOT NULL
		GROUP BY sort_cod, sort_nom, sort_cout, sort_distance, sort_description, 
			sort_aggressif, sort_niveau, sort_soi_meme, sort_monstre, sort_joueur, 
			sort_soutien, sort_bloquable, sort_case, sort_temps_recharge, sort_fonction
		ORDER BY sort_niveau, sort_nom';

    $req_autres = 'SELECT
			sort_cod, sort_nom, sort_cout, sort_distance, sort_comp_cod, 
			sort_description, sort_aggressif, sort_niveau, sort_soi_meme,
			sort_monstre, sort_joueur, sort_soutien, sort_bloquable, sort_case, 
			sort_temps_recharge, sort_fonction
		FROM sorts
		LEFT OUTER JOIN dieu_sorts ON dsort_sort_cod = sort_cod
		INNER JOIN competences ON comp_cod = sort_comp_cod
		WHERE dsort_sort_cod IS NULL AND sort_combinaison LIKE \'%9%\'
		ORDER BY sort_niveau, sort_nom';

    echo '<p>Accès direct : <a href="#runiques">Sorts runiques</a> - <a href="#divins">Sorts divins</a> - <a href="#autres">Autres sorts</a></p>';

    // Tableau des sorts runiques
    echo '<h1 id="runiques">Sorts accessibles par runes</h1><table>
		<tr>
			<th class="titre">Sort / combinaison</th>
			<th class="titre">Compétence / Paramètres</th>
			<th class="titre">Niveau / Coût</th>
			<th class="titre">Cible</th>
			<th class="titre">Caractéristiques</th>
			<th class="titre">Description</th>
			<th class="titre">Action</th>
		</tr>';

    $stmt = $pdo->query($req_runiques);

    while ($result = $stmt->fetch()) {
        // Récupération des données
        $sort_cod = $result['sort_cod'];
        $sort_nom = $result['sort_nom'];
        $sort_combinaison = $result['sort_combinaison'];
        $sort_comp_cod = $result['sort_comp_cod'];
        $sort_niveau = $result['sort_niveau'];
        $sort_cout = $result['sort_cout'];
        $sort_distance = $result['sort_distance'];
        $sort_soi_meme = $result['sort_soi_meme'];
        $sort_monstre = $result['sort_monstre'];
        $sort_joueur = $result['sort_joueur'];
        $sort_case = $result['sort_case'];
        $sort_aggressif = $result['sort_aggressif'];
        $sort_soutien = $result['sort_soutien'];
        $sort_bloquable = $result['sort_bloquable'];
        $sort_temps_recharge = $result['sort_temps_recharge'];
        $sort_description = $result['sort_description'];

        echo "<form action='#' method='POST'><tr>
			<td class='soustitre2'><input type='text' value='$sort_nom' name='sort_nom' size='20' />
				<br />$sort_combinaison</td>
			<td class='soustitre2'><select name='sort_comp_cod'>" . $html->select_from_query($req_comp, 'comp_cod', 'comp_libelle', $sort_comp_cod) . "</select>
				<br />Distance <input type='text' value='$sort_distance' name='sort_distance' size='2' />
				<br />Délai <input type='text' name='sort_temps_recharge' value='$sort_temps_recharge' size='5' /> minutes</td>
			<td class='soustitre2'>Niveau <input type='text' value='$sort_niveau' name='sort_niveau' size='2' />
				<br />Coût (PA)<input type='text' value='$sort_cout' name='sort_cout' size='2' /></td>
			<td class='soustitre2'>" . ecrire_checkbox('Soi-même', 'sort_soi_meme_' . $sort_cod, 'sort_soi_meme', $sort_soi_meme) . "
				<br />" . ecrire_checkbox('Monstre', 'sort_monstre_' . $sort_cod, 'sort_monstre', $sort_monstre) . "
				<br />" . ecrire_checkbox('Joueur', 'sort_joueur_' . $sort_cod, 'sort_joueur', $sort_joueur) . "
				<br />" . ecrire_checkbox('Case', 'sort_case_' . $sort_cod, 'sort_case', $sort_case) . "</td>
			<td class='soustitre2'>" . ecrire_checkbox('Aggressif', 'sort_aggressif_' . $sort_cod, 'sort_aggressif', $sort_aggressif) . "
				<br />" . ecrire_checkbox('Soutien', 'sort_soutien_' . $sort_cod, 'sort_soutien', $sort_soutien) . "
				<br />" . ecrire_checkbox('Bloquable', 'sort_bloquable_' . $sort_cod, 'sort_bloquable', $sort_bloquable) . "</td>
			<td class='soustitre2'><textarea cols='40' rows='3' name='sort_description'>$sort_description</textarea></td>
			<td class='soustitre2'><input type='hidden' value='$sort_cod' name='sort_cod' />
				<input type='hidden' value='modif' name='methode' />
				<input type='submit' class='test' value='Modifier' />
			</td>
		</tr></form>";
    }

    // Tableau des sorts divins
    echo '</table><h1 id="divins">Sorts Divins</h1><table>
		<tr>
			<th class="titre">Sort / dieux</th>
			<th class="titre">Paramètres</th>
			<th class="titre">Niveau / Coût</th>
			<th class="titre">Cible</th>
			<th class="titre">Caractéristiques</th>
			<th class="titre">Description</th>
			<th class="titre">Action</th>
		</tr>';

    $stmt = $pdo->query($req_divins);

    while ($result = $stmt->fetch()) {
        // Récupération des données
        $sort_cod = $result['sort_cod'];
        $sort_nom = $result['sort_nom'];
        $dieux_nom = $result['dieux_nom'];
        $sort_niveau = $result['sort_niveau'];
        $sort_comp_cod = $result['sort_comp_cod'];
        $sort_cout = $result['sort_cout'];
        $sort_distance = $result['sort_distance'];
        $sort_soi_meme = $result['sort_soi_meme'];
        $sort_monstre = $result['sort_monstre'];
        $sort_joueur = $result['sort_joueur'];
        $sort_case = $result['sort_case'];
        $sort_aggressif = $result['sort_aggressif'];
        $sort_soutien = $result['sort_soutien'];
        $sort_bloquable = $result['sort_bloquable'];
        $sort_temps_recharge = $result['sort_temps_recharge'];
        $sort_description = $result['sort_description'];

        echo "<tr><form action='#' method='POST'>
			<td class='soustitre2'><input type='text' value='$sort_nom' name='sort_nom' size='20' />
				<br /><small>$dieux_nom</small></td>
			<td class='soustitre2'>Distance <input type='text' value='$sort_distance' name='sort_distance' size='2' />
				<br />Délai <input type='text' name='sort_temps_recharge' value='$sort_temps_recharge' size='5' /> minutes</td>
			<td class='soustitre2'>Niveau <input type='text' value='$sort_niveau' name='sort_niveau' size='2' />
				<br />Coût (PA)<input type='text' value='$sort_cout' name='sort_cout' size='2' /></td>
			<td class='soustitre2'>" . ecrire_checkbox('Soi-même', 'sort_soi_meme_' . $sort_cod, 'sort_soi_meme', $sort_soi_meme) . "
				<br />" . ecrire_checkbox('Monstre', 'sort_monstre_' . $sort_cod, 'sort_monstre', $sort_monstre) . "
				<br />" . ecrire_checkbox('Joueur', 'sort_joueur_' . $sort_cod, 'sort_joueur', $sort_joueur) . "
				<br />" . ecrire_checkbox('Case', 'sort_case_' . $sort_cod, 'sort_case', $sort_case) . "</td>
			<td class='soustitre2'>" . ecrire_checkbox('Aggressif', 'sort_aggressif_' . $sort_cod, 'sort_aggressif', $sort_aggressif) . "
				<br />" . ecrire_checkbox('Soutien', 'sort_soutien_' . $sort_cod, 'sort_soutien', $sort_soutien) . "
				<br />" . ecrire_checkbox('Bloquable', 'sort_bloquable_' . $sort_cod, 'sort_bloquable', $sort_bloquable) . "</td>
			<td class='soustitre2'><textarea cols='40' rows='3' name='sort_description'>$sort_description</textarea></td>
			<td class='soustitre2'><input type='hidden' value='$sort_cod' name='sort_cod' />
				<input type='hidden' value='$sort_comp_cod' name='sort_comp_cod' />
				<input type='hidden' value='modif' name='methode' />
				<input type='submit' class='test' value='Modifier' />
			</td></form>
		</tr>";
    }

    // Tableau des autres sorts
    echo '</table><h1 id="autres">Autres sorts (tests et/ou monstres)</h1><table>
		<tr>
			<th class="titre">Sort</th>
			<th class="titre">Compétence / Paramètres</th>
			<th class="titre">Niveau / Coût</th>
			<th class="titre">Cible</th>
			<th class="titre">Caractéristiques</th>
			<th class="titre">Description</th>
			<th class="titre">Action</th>
		</tr>';

    $stmt = $pdo->query($req_autres);

    while ($result = $stmt->fetch()) {
        // Récupération des données
        $sort_cod = $result['sort_cod'];
        $sort_nom = $result['sort_nom'];
        $sort_comp_cod = $result['sort_comp_cod'];
        $sort_niveau = $result['sort_niveau'];
        $sort_cout = $result['sort_cout'];
        $sort_distance = $result['sort_distance'];
        $sort_soi_meme = $result['sort_soi_meme'];
        $sort_monstre = $result['sort_monstre'];
        $sort_joueur = $result['sort_joueur'];
        $sort_case = $result['sort_case'];
        $sort_aggressif = $result['sort_aggressif'];
        $sort_soutien = $result['sort_soutien'];
        $sort_bloquable = $result['sort_bloquable'];
        $sort_temps_recharge = $result['sort_temps_recharge'];
        $sort_description = $result['sort_description'];

        echo "<tr><form action='#' method='POST'>
			<td class='soustitre2'><input type='text' value='$sort_nom' name='sort_nom' size='20' /></td>
			<td class='soustitre2'><select name='sort_comp_cod'>" . $html->select_from_query($req_comp_complete, 'comp_cod', 'comp_libelle', $sort_comp_cod) . "</select>
				<br />Distance <input type='text' value='$sort_distance' name='sort_distance' size='2' />
				<br />Délai <input type='text' name='sort_temps_recharge' value='$sort_temps_recharge' size='5' /> minutes</td>
			<td class='soustitre2'>Niveau <input type='text' value='$sort_niveau' name='sort_niveau' size='2' />
				<br />Coût (PA)<input type='text' value='$sort_cout' name='sort_cout' size='2' /></td>
			<td class='soustitre2'>" . ecrire_checkbox('Soi-même', 'sort_soi_meme_' . $sort_cod, 'sort_soi_meme', $sort_soi_meme) . "
				<br />" . ecrire_checkbox('Monstre', 'sort_monstre_' . $sort_cod, 'sort_monstre', $sort_monstre) . "
				<br />" . ecrire_checkbox('Joueur', 'sort_joueur_' . $sort_cod, 'sort_joueur', $sort_joueur) . "
				<br />" . ecrire_checkbox('Case', 'sort_case_' . $sort_cod, 'sort_case', $sort_case) . "</td>
			<td class='soustitre2'>" . ecrire_checkbox('Aggressif', 'sort_aggressif_' . $sort_cod, 'sort_aggressif', $sort_aggressif) . "
				<br />" . ecrire_checkbox('Soutien', 'sort_soutien_' . $sort_cod, 'sort_soutien', $sort_soutien) . "
				<br />" . ecrire_checkbox('Bloquable', 'sort_bloquable_' . $sort_cod, 'sort_bloquable', $sort_bloquable) . "</td>
			<td class='soustitre2'><textarea cols='40' rows='3' name='sort_description'>$sort_description</textarea></td>
			<td class='soustitre2'><input type='hidden' value='$sort_cod' name='sort_cod' />
				<input type='hidden' value='modif' name='methode' />
				<input type='submit' class='test' value='Modifier' />
			</td></form>
		</tr>";
    }
    echo '</table>';
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";