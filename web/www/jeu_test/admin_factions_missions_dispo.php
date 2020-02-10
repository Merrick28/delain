<?php
if (!defined("APPEL"))
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
    echo '<div class="barrTitle">Les missions accessibles à une faction donnée</div><br />';
} else
{
    $req     = "SELECT fac_nom FROM factions where fac_cod = :fac_cod";
    $stmt    = $pdo->prepare($req);
    $stmt    = $pdo->execute(array(":fac_cod" => $fac_cod), $stmt);
    $result  = $stmt->fetch();
    $fac_nom = $result['fac_nom'];
    echo "<div class='barrTitle'>Les missions accessibles à la faction « $fac_nom »</div><br />";
}

switch ($methode)
{
    case 'debut':
        break;

    case 'faction_mission_ajout':
        if (isset($_POST['fmiss_miss_cod']) && $fac_cod >= 0)
        {
            $fmiss_miss_cod         = $_POST['fmiss_miss_cod'];
            $fmiss_proba            = $_POST['fmiss_proba'];
            $fmiss_coeff_difficulte = $_POST['fmiss_coeff_difficulte'];
            $fmiss_rang_min         = $_POST['fmiss_rang_min'];
            $fmiss_libelle          = $_POST['fmiss_libelle'];

            if ($fmiss_libelle != '')
            {
                $req  = "INSERT INTO faction_missions (fmiss_fac_cod, fmiss_miss_cod, fmiss_proba, fmiss_coeff_difficulte, fmiss_rang_min, fmiss_libelle)
					VALUES (:fac_cod, :fmiss_miss_cod, :fmiss_proba, :fmiss_coeff_difficulte, :fmiss_rang_min, :fmiss_libelle)";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":fac_cod"                => $fac_cod,
                                            ":fmiss_miss_cod"         => $fmiss_miss_cod,
                                            ":fmiss_proba"            => $fmiss_proba,
                                            ":fmiss_coeff_difficulte" => $fmiss_coeff_difficulte,
                                            ":fmiss_rang_min"         => $fmiss_rang_min,
                                            ":fmiss_libelle"          => $fmiss_libelle), $stmt);
            } else
            {
                $req  = "INSERT INTO faction_missions (fmiss_fac_cod, fmiss_miss_cod, fmiss_proba, fmiss_coeff_difficulte, fmiss_rang_min)
					VALUES (:fac_cod, :fmiss_miss_cod, :fmiss_proba, :fmiss_coeff_difficulte, :fmiss_rang_min)";
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":fac_cod"                => $fac_cod,
                                            ":fmiss_miss_cod"         => $fmiss_miss_cod,
                                            ":fmiss_proba"            => $fmiss_proba,
                                            ":fmiss_coeff_difficulte" => $fmiss_coeff_difficulte,
                                            ":fmiss_rang_min"         => $fmiss_rang_min), $stmt);
            }

            $resultat = "Mission $fmiss_miss_cod ajoutée pour la faction « $fac_nom » !";
        } else
            $resultat = "Erreur de paramètres";
        break;

    case 'faction_mission_modif':
        if (isset($_POST['fmiss_miss_cod']) && $fac_cod >= 0)
        {
            $fmiss_miss_cod         = $_POST['fmiss_miss_cod'];
            $fmiss_proba            = $_POST['fmiss_proba'];
            $fmiss_coeff_difficulte = $_POST['fmiss_coeff_difficulte'];
            $fmiss_rang_min         = $_POST['fmiss_rang_min'];
            $fmiss_libelle          = pg_escape_string(str_replace('\'', '’', $_POST['fmiss_libelle']));
            $req_libelle            =
                ($fmiss_libelle != '') ? "fmiss_libelle = '$fmiss_libelle'" : "fmiss_libelle = NULL";

            $req  = "UPDATE faction_missions
				SET fmiss_proba = :fmiss_proba,
					fmiss_coeff_difficulte = :fmiss_coeff_difficulte,
					fmiss_rang_min = :fmiss_rang_min,
					$req_libelle
				WHERE fmiss_miss_cod = :fmiss_miss_cod AND fmiss_fac_cod = :fac_cod";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":fac_cod"                => $fac_cod,
                                        ":fmiss_miss_cod"         => $fmiss_miss_cod,
                                        ":fmiss_proba"            => $fmiss_proba,
                                        ":fmiss_coeff_difficulte" => $fmiss_coeff_difficulte,
                                        ":fmiss_rang_min"         => $fmiss_rang_min), $stmt);

            $resultat = "Mission $fmiss_miss_cod modifiée pour la faction « $fac_nom » !";
        } else
            $resultat = "Erreur de paramètres";
        break;

    case 'faction_mission_supprime':
        if (isset($_POST['fmiss_miss_cod']) && $fac_cod >= 0)
        {
            $fmiss_miss_cod = $_POST['fmiss_miss_cod'];
            $req            =
                "DELETE FROM faction_missions WHERE fmiss_fac_cod = $fac_cod AND fmiss_miss_cod = $fmiss_miss_cod";
            $stmt           = $pdo->query($req);

            $resultat = "Mission $fmiss_miss_cod supprimée pour la faction « $fac_nom » !";
        } else
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
    $req = "SELECT fmiss_miss_cod, miss_nom, fmiss_proba, fmiss_coeff_difficulte, fmiss_rang_min, coalesce(fmiss_libelle, '') as fmiss_libelle
		FROM faction_missions
		INNER JOIN missions ON miss_cod = fmiss_miss_cod
		WHERE fmiss_fac_cod = $fac_cod
		ORDER BY fmiss_rang_min, fmiss_miss_cod";

    $req_rangs = "SELECT (rank() OVER (PARTITION BY 1 ORDER BY rfac_seuil))::text || '. ' || rfac_nom as rfac_nom,
			rank() OVER (PARTITION BY 1 ORDER BY rfac_seuil) as rfac_rang
		FROM faction_rangs WHERE rfac_fac_cod = $fac_cod ORDER BY rfac_seuil";

    $req_rang_max = "SELECT count(*) as nombre FROM faction_rangs WHERE rfac_fac_cod = $fac_cod";
    $stmt         = $pdo->query($req_rang_max);
    $result       = $stmt->fetch();
    $rang_max     = $result['nombre'];

    // Tableau des missions
    echo "<div style='padding:10px;'><div>Voici la liste des missions que les aventuriers pourront remplir au bénéfice de la faction « $fac_nom ».
		<br />* Il est nécessaire qu’au moins un type de mission soit ouvert à tout le monde, pour qu’un nouveau venu puisse faire sa première mission pour la faction.
		<br />* Le libellé de mission n’est utile que pour écraser le libellé standard (Malkiar n’aura sans doute pas la même rhétorique qu’Hormandre III).
		S’il est laissé vide, le libellé standard s’appliquera. Elle répond aux mêmes contraintes que pour la création d’une mission.
		<br />* La probabilité doit être comprise entre 0 et 1 (séparateur décimal : le point « . »).
		<br />* Le coefficient de difficulté (nombre décimal supérieur à 1) indique si ce type de mission est plus difficile (et mieux rémunuéré) dans cette faction que dans une autre.</div><hr />";
    echo '<table>
		<tr>
			<th class="titre">Type de mission</th>
			<th class="titre">Probabilité d’apparition</th>
			<th class="titre">Coeff de difficulté</th>
			<th class="titre">Rang minimal</th>
			<th class="titre">Libellé</th>
			<th class="titre">Actions</th>
		</tr>';

    $stmt = $pdo->query($req);

    while ($result = $stmt->fetch())
    {
        // Récupération des données
        $fmiss_miss_cod         = $result['fmiss_miss_cod'];
        $miss_nom               = $result['miss_nom'];
        $fmiss_proba            = $result['fmiss_proba'];
        $fmiss_coeff_difficulte = $result['fmiss_coeff_difficulte'];
        $fmiss_rang_min         = $result['fmiss_rang_min'];
        $fmiss_libelle          = $result['fmiss_libelle'];

        $attention   =
            ($fmiss_rang_min > $rang_max) ? "<strong>Attention, le rang minimal défini, $fmiss_rang_min, est<br />supérieur au rang maximal existant pour cette faction !</strong><br />" : "";
        $select_rang = "$attention<select name='fmiss_rang_min'><option value='0'>Aucune restriction</option>"
                       . $html->select_from_query($req_rangs, 'rfac_rang', 'rfac_nom', $fmiss_rang_min)
                       . "</select>";

        echo "<form action='#' method='POST' onsubmit='if (this.methode == \"faction_mission_supprime\") return confirm(\"Êtes-vous sûr de vouloir retirer cette mission ?\");'><tr>
			<td class='soustitre2'>$miss_nom</td>
			<td class='soustitre2'><input type='text' value='$fmiss_proba' name='fmiss_proba' size='3' /></td>
			<td class='soustitre2'><input type='text' value='$fmiss_coeff_difficulte' name='fmiss_coeff_difficulte' size='3' /></td>
			<td class='soustitre2'>$select_rang</td>
			<td class='soustitre2'><textarea cols='40' rows='3' name='fmiss_libelle'>$fmiss_libelle</textarea></td>
			<td class='soustitre2'><input type='hidden' value='$fmiss_miss_cod' name='fmiss_miss_cod' />
				<input type='hidden' value='$fac_cod' name='fac_cod' />
				<input type='hidden' value='faction_mission_modif' name='methode' id='methode$fmiss_miss_cod' />
				<input type='submit' class='test' value='Modifier' onclick='changeMethode($fmiss_miss_cod, \"faction_mission_modif\");'/>
				<input type='submit' class='test' value='Supprimer' onclick='changeMethode($fmiss_miss_cod, \"faction_mission_supprime\");'/>
			</td></tr></form>";
    }

    $req = 'SELECT miss_cod, miss_nom FROM missions ORDER BY miss_nom';
    echo "<form action='#' method='POST'><tr>
		<td class='soustitre2'><select name='fmiss_miss_cod'>"
         . $html->select_from_query($req, 'miss_cod', 'miss_nom')
         . "</select></td>
		<td class='soustitre2'><input type='text' value='0.5' name='fmiss_proba' size='3' /></td>
		<td class='soustitre2'><input type='text' value='1' name='fmiss_coeff_difficulte' size='3' /></td>
		<td class='soustitre2'><select name='fmiss_rang_min'><option value='0'>Aucune restriction</option>"
         . $html->select_from_query($req_rangs, 'rfac_rang', 'rfac_nom')
         . "</select></td>
		<td class='soustitre2'><textarea cols='40' rows='3' name='fmiss_libelle'></textarea></td>
		<td class='soustitre2'><input type='hidden' value='$fac_cod' name='fac_cod' />
			<input type='hidden' value='faction_mission_ajout' name='methode' />
			<input type='submit' class='test' value='Ajouter' /></td></tr></form>";
    echo '</table></div>';
}
