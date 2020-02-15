<?php
if (!defined("APPEL"))
    die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Modification / création d’un type de mission</div><br />';

$resultat = '';
$methode  = $_REQUEST['methode'];
switch ($methode)
{
    case 'debut':
        break;

    case 'mission_modif':
        if (isset($_POST['miss_cod']))
        {
            $miss_cod     = $_POST['miss_cod'];
            $miss_nom     = $_POST['miss_nom'];
            $miss_libelle = $_POST['miss_libelle'];
            $req          = "UPDATE missions SET
					miss_nom = :miss_nom
					miss_libelle = :miss_libelle
				WHERE miss_cod = :miss_cod";
            $stmt         = $pdo->prepare($req);
            $stmt         = $pdo->execute(array(":miss_nom"     => $miss_nom,
                                                ":miss_libelle" => $miss_libelle,
                                                ":miss_cod"     => $miss_cod), $stmt);
            $resultat     = "Mission $miss_nom ($miss_cod) mise à jour !";
        } else
        {
            $resultat = "Erreur de paramètres";
        }

        break;

    case 'mission_ajout':
        if (isset($_POST['miss_nom']))
        {
            $miss_nom     = $_POST['miss_nom'];
            $miss_libelle = $_POST['miss_libelle'];

            $req      = "INSERT INTO Missions (miss_nom, miss_libelle, miss_fonction_init, miss_fonction_valide)
				VALUES (:miss_nom, :miss_libelle, '', '')
				RETURNING miss_cod";
            $stmt     = $pdo->prepare($req);
            $stmt     = $pdo->execute(array(":miss_nom"     => $miss_nom,
                                            ":miss_libelle" => $miss_libelle), $stmt);
            $result   = $stmt->fetch();
            $miss_cod = $result['miss_cod'];

            $resultat = "Mission $miss_nom ($miss_cod) créée !";
        } else
            $resultat = "Erreur de paramètres";
        break;
}

ecrireResultatEtLoguer($resultat, $req);

$req = 'SELECT miss_cod, miss_nom, miss_libelle, 
miss_fonction_init, miss_fonction_valide, coalesce(fmiss_nb, 0) as fmiss_nb
	FROM missions
	LEFT OUTER JOIN (
		select fmiss_miss_cod, count(*) as fmiss_nb
		from faction_missions
		inner join factions on fac_cod = fmiss_fac_cod
		where fac_active = \'O\'
		group by fmiss_miss_cod
	) fmiss on fmiss_miss_cod = miss_cod
	ORDER BY miss_cod';

// Tableau des missions
echo '<div style="padding:10px">Dans le texte de la mission, les éléments suivants seront remplacés par une valeur adéquate lors de la génération de la mission :<br />
 - [recompense] par le montant, en brouzoufs (unité comprise), de la récompense.<br />
 - [délai] par le nombre de jours (unité comprise) laissés pour la réalisation de la mission.<br />
 - [position] par la position ciblée<br />
 - [personnage] par le personnage ciblé (que ce soit un monstre, un familier ou un aventurier)<br />
 - [objet] par l’objet ciblé (y compris sa quantité)<br />
 </div><hr />';
echo '<table style="padding:10px">
	<tr>
		<th class="titre">Mission</th>
		<th class="titre">Texte</th>
		<th class="titre">Actions</th>
		<th class="titre">Définie pour</th>
	</tr>';

$stmt = $pdo->query($req);

while ($result = $stmt->fetch())
{
    // Récupération des données
    $miss_cod     = $result['miss_cod'];
    $miss_nom     = $result['miss_nom'];
    $miss_active  = ($result['miss_fonction_init'] != '' && $result['miss_fonction_valide'] != '');
    $txt_active   =
        ($miss_active) ? '' : '<br /><strong>Inactive ! Contactez un développeur / administrateur</strong><br /> afin que la mission soit reliée à ses fonctions de traitement.';
    $miss_libelle = $result['miss_libelle'];
    $txt_definie  = $result['fmiss_nb'] . ' factions';

    echo "<form action='#' method='POST'><tr>
		<td class='soustitre2'>$miss_cod. <input type='text' value='$miss_nom' name='miss_nom' size='30' />$txt_active</td>
		<td class='soustitre2'><textarea cols='40' rows='3' name='miss_libelle'>$miss_libelle</textarea></td>
		<td class='soustitre2'><input type='hidden' value='$miss_cod' name='miss_cod' />
			<input type='hidden' value='mission_modif' name='methode' />
			<input type='submit' class='test' value='Modifier'/></td>
		<td class='soustitre2'>$txt_definie</td></tr></form>";
}
echo "<form action='#' method='POST'><tr>
	<td class='soustitre2'><input type='text' value='' name='miss_nom' size='30' /></td>
	<td class='soustitre2'><textarea cols='40' rows='3' name='miss_libelle'></textarea></td>
	<td class='soustitre2'><input type='hidden' value='mission_ajout' name='methode' />
		<input type='submit' class='test' value='Ajouter' /></td></tr></form>";
echo '</table>';
