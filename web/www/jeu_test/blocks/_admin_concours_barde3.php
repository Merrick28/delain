<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 19/12/2018
 * Time: 15:54
 */
$req_concours = 'select cbar_cod, cbar_saison, cbar_date_ouverture, cbar_date_teaser, cbar_fermeture, cbar_description,
					case when CURRENT_DATE between cbar_date_teaser and cbar_date_ouverture then 1 else 0 end as introduction,
					case when CURRENT_DATE between cbar_date_ouverture and cbar_fermeture then 1 else 0 end as ouvert,
					case when CURRENT_DATE < cbar_date_teaser then 1 else 0 end as futur,
					case when CURRENT_DATE > cbar_fermeture then 1 else 0 end as ferme
				from concours_barde order by cbar_saison';
$stmt         = $pdo->query($req_concours);

echo '<table>
	<tr>
		<td class="titre">Saison</td>
		<td class="titre">Détails</td>
	</tr>
	<tr>
		<td class="soustitre2">';

$toutesPassees = true;
$cbar_cod      = $_REQUEST['cbar_cod'];
while ($result = $stmt->fetch())
{
    // Au passage, pendant le parcours, on enregistre les valeurs de celle qu’on va afficher.
    if ($cbar_cod == $result['cbar_cod'])
    {
        $cbar_saison         = $result['cbar_saison'];
        $cbar_date_ouverture = $result['cbar_date_ouverture'];
        $cbar_date_teaser    = $result['cbar_date_teaser'];
        $cbar_fermeture      = $result['cbar_fermeture'];
        $cbar_description    = $result['cbar_description'];
        $introduction        = ($result['introduction'] == 1);
        $ouvert              = ($result['ouvert'] == 1);
        $futur               = ($result['futur'] == 1);
        $ferme               = ($result['ferme'] == 1);
    }
    $texte_etat = '';
    if ($result['ferme'] != 1)
        $toutesPassees = false;

    if ($result['ouvert'] == 1)
        $texte_etat = ' (ouverte)';
    if ($result['introduction'] == 1)
        $texte_etat = ' (annoncée)';
    if ($result['futur'] == 1)
        $texte_etat = ' (future)';
    if ($result['ferme'] == 1)
        $texte_etat = ' (fermée)';

    if ($cbar_cod == $result['cbar_cod'])
        echo "<p><strong><a href='?methode=barde_visu&cbar_cod=" . $result['cbar_cod'] . "'>Saison " . $result['cbar_saison'] . "$texte_etat</a></strong></p>";
    else
        echo "<p><a href='?methode=barde_visu&cbar_cod=" . $result['cbar_cod'] . "'>Saison " . $result['cbar_saison'] . "$texte_etat</a></p>";
}
echo "</td>";

switch ($methode)
{
    case 'debut':        // Affichage initial vide
        echo '<td></td>';
        break;
    case 'barde_visu':        // Affichage des données d'une session

        // Récupération des membres du jury
        $req_jury = "select jbar_cod, jbar_perso_cod, perso_nom
						from concours_barde_jury
						left outer join perso on perso_cod = jbar_perso_cod
						where jbar_cbar_cod = $cbar_cod
						order by jbar_cod";
        $stmt     = $pdo->query($req_jury);
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
        while ($result = $stmt->fetch())
        {
            echo "<tr><td class='soustitre2'>Jury $i</td><td><input type='text' name='form_jury$i' value='" . $result['jbar_perso_cod'] . "' />
				<input type='hidden' name='form_jury_cod$i' value='" . $result['jbar_cod'] . "' />
				</td><td>(" . $result['perso_nom'] . ")</td></tr>";
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
            echo '<em>Cette instance est fermée et n’est plus modifiable</em>';
        echo '</form>';
        echo '</td>';
        break;
    case 'barde_nouvelle':    // Formulaire vierge
        ?>
        <td>
            <form name="creation" method="POST" action="#">
                <input type="hidden" name="methode" value="barde_creation"/>
                <table>
                    <tr>
                        <td colspan="3" class="titre">Nouvelle Saison</td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Saison</td>
                        <td><input type="text" name="form_saison" value=""/></td>
                        <td>L’année du concours.</td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Date d’annonce (aaaa-mm-jj)</td>
                        <td><input type="text" name="form_date_teaser" value=""/></td>
                        <td>La date à laquelle la page du concours devient accessible.</td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Date d’ouverture (aaaa-mm-jj)</td>
                        <td><input type="text" name="form_date_ouverture" value=""/></td>
                        <td>La date à laquelle on peut commencer à proposer des textes.</td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Date de fermeture (aaaa-mm-jj)</td>
                        <td><input type="text" name="form_fermeture" value=""/></td>
                        <td>La date à laquelle plus aucun texte n’est accepté.</td>
                    </tr>
                    <tr>
                        <td class="soustitre2" colspan="2"><p>Descriptif (html)</p><textarea rows="10" cols="50"
                                                                                             name="form_description"></textarea>
                        </td>
                        <td>Le texte qui apparaîtra en en-tête de la page du concours.</td>
                    </tr>
                    <?php
                    for ($i = 1; $i <= $nbJury; $i++)
                    {
                        echo "<tr><td class='soustitre2'>Jury $i</td><td><input type='text' name='form_jury$i' value='' /></td><td></td></tr>";
                    }
                    ?>
                </table>
                <input type="submit" class="test" value="Valider"/>
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

