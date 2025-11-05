<?php
$verif_connexion::verif_appel();
echo '<td>';
echo '<form name="modification" method="POST" action="#">
			<input type="hidden" name="methode" value="collection_modif" />
			<input type="hidden" name="form_cod" value="' . $ccol_cod . '" />';
echo '<table><tr><td colspan="3" class="titre">Titre ' . $ccol_titre . '</td></tr>';
echo '<tr><td colspan="3" class="soustitre2">';
if ($ouvert)
    echo 'Cette session du concours de collections est <strong>ouverte</strong>';
if ($futur)
    echo 'Cette session du concours de collections est <strong>future</strong>';
if ($ferme)
    echo 'Cette session du concours de collections est <strong>fermée</strong>';
echo '</td></tr>';
echo '<tr><td class="soustitre2">Titre</td><td><input type="text" name="form_titre" value="' . $ccol_titre . '" /></td><td>Dénomination du concours (typiquement, « Collections de citrouilles, 2010 »).</td></tr>';
echo '<tr><td class="soustitre2">Objet de collection</td><td><select name="form_tobj_objet" onchange="filtrer_gobj(this.value, -1, \'form_objet\', tableauObjets);"><option value="-1">Choisissez un type d’objet...</option>';
$req         =
    'select distinct tobj_cod, tobj_libelle from type_objet inner join objet_generique on gobj_tobj_cod = tobj_cod order by tobj_libelle';
$stmt        = $pdo->query($req);
$script_tobj = 'tableauObjets = new Array();
                ';
while ($result = $stmt->fetch())
{
    $clef        = $result['tobj_cod'];
    $valeur      = $result['tobj_libelle'];
    $script_tobj .= "tableauObjets[$clef] = new Array();
                        ";
    echo "<option value='$clef'" . getSelected($clef, $ccol_gobj_tobj_cod) . ">$valeur</option>";
}
echo '</select><br /><select name="form_objet" id="form_objet">';
require G_CHE . "/jeu_test/blocks/_admin_collection_detail.php";
echo '<tr><td class="soustitre2">Date d’ouverture (aaaa-mm-jj, jour inclus)</td><td><input type="text" name="form_date_ouverture" value="' . $ccol_date_ouverture . '" /></td><td>La date à laquelle le concours commence.</td></tr>';
echo '<tr><td class="soustitre2">Date de fermeture (aaaa-mm-jj, jour exclus)</td><td><input type="text" name="form_date_fermeture" value="' . $ccol_date_fermeture . '" /></td><td>La date à laquelle le concours est terminé.</td></tr>';
echo '<tr><td class="soustitre2">Différencier les 4e persos</td><td><input type="checkbox" '.($ccol_differencier_4e == 'O' ? " checked " : " ").'name="form_differencier_4e" id="differencier4e"/></td><td>LIndique si un classement hors 4e persos est également créé.</td></tr>';
echo '<tr><td class="soustitre2">Tranches de niveau</td><td><input type="text" name="form_tranche_niveau" value="'.$ccol_tranche_niveau.'"/></td><td>Indique que l’on crée des classements par tranches de niveau. 0 si non, le nombre de niveaux par tranche si oui.</td></tr>';
echo '<tr><td class="soustitre2" colspan="2"><p>Descriptif (html)</p><textarea rows="10" cols="50" name="form_description">' . $ccol_description . '</textarea></td><td>Le texte qui apparaîtra en en-tête de la page du concours.</td></tr>';
echo '</table>';

echo "
        <script type='text/javascript'>
				$script_tobj
				$script_gobj;
				filtrer_gobj($ccol_gobj_tobj_cod, $ccol_gobj_cod, 'form_objet', tableauObjets);
        </script>
        ";

// Si le concours n'est pas fermé, on peut changer les paramètres
if (!$ferme)
    echo '<input type="submit" value="Valider" />';
if ($ferme)
    echo '<em>Cette instance est fermée et n’est plus modifiable</em>';
echo '</form>';
echo '<hr />';

