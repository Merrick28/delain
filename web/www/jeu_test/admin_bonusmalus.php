<?php
include "blocks/_header_page_jeu.php";

//
//Contenu de la div de droite
//

ob_start();

$erreur = 0;
$droit_modif = 'dcompt_enchantements';
include "blocks/_test_droit_modif_generique.php";

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
                $tbonus_cumulable = (isset($_POST['tbonus_cumulable'])) ? 'O' : 'N';
                $tbonus_degressivite = (int)($_POST['tbonus_degressivite']);

				$req = "UPDATE bonus_type SET
						tonbus_libelle = '$tonbus_libelle',
						tbonus_nettoyable = '$tbonus_nettoyable',
						tbonus_gentil_positif = '$tbonus_gentil_positif',
						tbonus_cumulable = '$tbonus_cumulable',
						tbonus_degressivite = '$tbonus_degressivite'
					WHERE tbonus_cod = $tbonus_cod RETURNING tbonus_libc";
				$db->query($req);
				$resultat = "<p>Bonus $tonbus_libelle ($tbonus_cod) mis à jour !</p><p>Requête : <pre>$req</pre></p>";

                if ($db->next_record())
                {
                    $tbonus_libc = $db->f('tbonus_libc');
                    if (in_array($tbonus_libc, ["CON", "INT", "DEX", "FOR"]) )
                    {
                        $perso_list = "" ;
                        $db2 = new base_delain;
                        $db->query("select distinct corig_perso_cod, perso_cod, perso_nom from carac_orig join perso on perso_cod=corig_perso_cod where corig_type_carac  ='{$tbonus_libc}' order by perso_cod");
                        while ($db->next_record())
                        {
                            $bonus_perso_cod = $db->f('corig_perso_cod');
                            $perso_list .= '#'.$db->f('perso_cod').' ('.$db->f('perso_nom').'), ';

                            // On recalcule le changement des limites pour ce perso
                            $db2->query("select f_modif_carac_perso({$bonus_perso_cod}, '{$tbonus_libc}'); ");
                        }

                        if ($perso_list != "")
                        {
                            $resultat.= "Les bonus/malus de caracs ont mis à jour sur TOUS les persos suivants: {$perso_list}";
                        }
                    }
                }

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
			tbonus_cod, tonbus_libelle, tbonus_libc, tbonus_nettoyable, tbonus_gentil_positif, tbonus_cumulable, tbonus_degressivite
		FROM bonus_type
		ORDER BY tbonus_libc';

	// Tableau des sorts runiques
	echo '<h1>Bonus et malus</h1>
        <strong><u>ATTENTION</u>:</strong>: la modification des limites sur les bonus de caractéristiques <strong>DEX, FOR, INT, et CON</strong> a un <u>impacte direct</u> sur les joueurs qui ont déjà des bonus/malus de ce type.<br>
        La dégressivité sur les autres Bonus/Malus ne sera pris en compte qu\'à partir des prochains qui seront donnés.<br>
        <br> 
        <table>
		<tr>
			<th class="titre">Code court</th>
			<th class="titre">Libellé</th>
			<th class="titre">Nettoyable ?</th>
			<th class="titre">Valeur positive<br />pour un effet<br />bénéfique ?</th>
			<th class="titre">Cumulable ?</th>
			<th class="titre">Dégressivité/Limite ?<br> (entre 0% et 100%)</th>
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
		$tbonus_cumulable = $db->f('tbonus_cumulable');
		$tbonus_degressivite = $db->f('tbonus_degressivite');

		echo "<form action='#' method='POST'><tr>
			<td class='soustitre2'>$tbonus_libc</td>
			<td class='soustitre2'><input type='text' value='$tonbus_libelle' name='tonbus_libelle' size='30' /></td>
			<td class='soustitre2'>" . ecrire_checkbox('', 'tbonus_nettoyable_' . $tbonus_cod, 'tbonus_nettoyable', $tbonus_nettoyable) . "</td>
			<td class='soustitre2'>" . ecrire_checkbox('', 'tbonus_gentil_positif_' . $tbonus_cod, 'tbonus_gentil_positif', $tbonus_gentil_positif) . "</td>
			<td class='soustitre2'>" . ecrire_checkbox('', 'tbonus_cumulable' . $tbonus_cod, 'tbonus_cumulable', $tbonus_cumulable) . "</td>	
			<td class='soustitre2'><input type='text' value='$tbonus_degressivite' name='tbonus_degressivite' size='3' /></td>					
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
