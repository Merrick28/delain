<?php
$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

if (!isset($ccol_cod))
    $ccol_cod = -1;

echo '<div class="bordiv" style="padding:0; margin-left: 205px; max-height:20px; overflow:hidden;" id="cadre_collections">';
echo '<div class="barrTitle" onclick="permutte_cadre(this.parentNode);">Concours des collectionneurs</div><br />';

// Nombre maximal de membres du jury
$nbJury = 10;

// Validations de formulaire
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case 'collection_modif':    // Modification d’un concours existant
        $form_cod            = pg_escape_string($_POST['form_cod']);
        $form_titre          = "ccol_titre='" . pg_escape_string($_POST['form_titre']) . "',";
        $form_date_ouverture =
            "ccol_date_ouverture='" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
        $form_date_fermeture =
            "ccol_date_fermeture='" . pg_escape_string($_POST['form_date_fermeture']) . "'::timestamp,";
        $form_description    = "ccol_description='" . pg_escape_string($_POST['form_description']) . "',";
        $form_objet          = "ccol_gobj_cod='" . pg_escape_string($_POST['form_objet']) . "'";

        $pdo->query("UPDATE concours_collections SET $form_titre $form_date_ouverture $form_date_fermeture $form_description $form_objet WHERE ccol_cod=$form_cod");
        echo '<p>Modification effectuée</p>';
        $methode = 'collection_visu';
        break;
    case 'collection_creation':    // Création d’un concours
        $form_titre          = "'" . pg_escape_string($_POST['form_titre']) . "',";
        $form_date_ouverture = "'" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
        $form_objet          = "'" . pg_escape_string($_POST['form_objet']) . "',";
        $form_date_fermeture = "'" . pg_escape_string($_POST['form_date_fermeture']) . "'::timestamp,";
        $form_description    = "'" . pg_escape_string($_POST['form_description']) . "'";

        $pdo->query("INSERT INTO concours_collections (ccol_titre, ccol_date_ouverture, ccol_gobj_cod, ccol_date_fermeture, ccol_description) VALUES ($form_titre $form_date_ouverture $form_objet $form_date_fermeture $form_description)");

        echo '<p>Création effectuée</p>';
        $methode = 'debut';
        break;
    default:
        break;
}

function getSelected($liste_id, $selected_id)
{
    return ($liste_id == $selected_id) ? ' selected="selected" ' : '';
}

?>

<script type='text/javascript'>
    var tableauObjets = [];

    function viderListe(listeObjets) {
        while (listeObjets.hasChildNodes())
            listeObjets.removeChild(listeObjets.firstChild);
    }

    function ajouterElement(clef, valeur, listeObjets, selected) {
        listeObjets.options[listeObjets.options.length] = new Option();
        listeObjets.options[listeObjets.options.length - 1].text = valeur;
        listeObjets.options[listeObjets.options.length - 1].value = clef;
        listeObjets.options[listeObjets.options.length - 1].selected = selected;
    }

    function filtrer_gobj(tobj_cod, selected_gobj) {
        var listeObjets = document.getElementById('form_objet');
        viderListe(listeObjets);
        for (var gobj_cod in tableauObjets[tobj_cod])
            ajouterElement(gobj_cod, tableauObjets[tobj_cod][gobj_cod], listeObjets, (selected_gobj == gobj_cod));
    }
</script>

<?php
$req_concours = 'select ccol_cod, ccol_titre, ccol_date_ouverture, ccol_gobj_cod, ccol_date_fermeture, ccol_description,
					case when CURRENT_DATE between ccol_date_ouverture and ccol_date_fermeture then 1 else 0 end as ouvert,
					case when CURRENT_DATE < ccol_date_ouverture then 1 else 0 end as futur,
					case when CURRENT_DATE > ccol_date_fermeture then 1 else 0 end as ferme,
					gobj_tobj_cod
				from concours_collections
				left outer join objet_generique on gobj_cod = ccol_gobj_cod
				order by ccol_date_ouverture';
$stmt         = $pdo->query($req_concours);

echo '<table>
	<tr>
		<td class="titre">Titre</td>
		<td class="titre">Détails</td>
	</tr>
	<tr>
		<td class="soustitre2">';

$toutesPassees = true;

while ($result = $stmt->fetch())
{
    // Au passage, pendant le parcours, on enregistre les valeurs de celle qu’on va afficher.
    if ($ccol_cod == $result['ccol_cod'])
    {
        $ccol_titre          = $result['ccol_titre'];
        $ccol_date_ouverture = $result['ccol_date_ouverture'];
        $ccol_gobj_cod       = $result['ccol_gobj_cod'];
        $ccol_gobj_tobj_cod  = $result['gobj_tobj_cod'];
        $ccol_date_fermeture = $result['ccol_date_fermeture'];
        $ccol_description    = $result['ccol_description'];
        $ouvert              = ($result['ouvert'] == 1);
        $futur               = ($result['futur'] == 1);
        $ferme               = ($result['ferme'] == 1);
    }
    require "blocks/_admin_collection1.php";
}
echo "</td>";

switch ($methode)
{
case 'debut':        // Affichage initial vide
    echo '<td></td>';
    break;
case 'collection_visu':        // Affichage des données d’une session
    require "blocks/_admin_collection2.php";
    echo '<table><tr><th class="titre" colspan="2">Classement actuel instantané<br />(récupéré d’après les inventaires)</th></tr>';
    echo '<tr><th class="titre">Aventurier</th><th class="titre">Nombre d’objets</th></tr>';
    $req  = "select perso_nom, count(*) as nombre from objets
			inner join perso_objets on perobj_obj_cod = obj_cod
			inner join perso on perso_cod = perobj_perso_cod
			where obj_gobj_cod = $ccol_gobj_cod
			group by perso_nom
			order by nombre desc
			limit 10";
    $stmt = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $nom    = $result['perso_nom'];
        $nombre = $result['nombre'];
        echo "<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
    }
    echo '</table><hr />';
    echo '<table><tr><th class="titre" colspan="2">Classement actuel consolidé<br />(récupéré d’après la consolidation, donc vide avant le concours, et figé après la fin du concours.)</th></tr>';
    echo '<tr><th class="titre">Aventurier</th><th class="titre">Nombre d’objets</th></tr>';
    $req  = "select coalesce(perso_nom, 'Aventurier disparu') as perso_nom, ccolres_nombre from concours_collections_resultats
			left outer join perso on perso_cod = ccolres_perso_cod
			where ccolres_ccol_cod = $ccol_cod
			order by ccolres_nombre desc";
    $stmt = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        $nom    = $result['perso_nom'];
        $nombre = $result['ccolres_nombre'];
        echo "<tr><td class='soustitre2'>$nom</td><td>$nombre</td></tr>";
    }
    echo '</table>';
    echo '</td>';
    break;
case 'collection_nouvelle':    // Formulaire vierge
?>
<td>
    <form name="creation" method="POST" action="#">
        <input type="hidden" name="methode" value="collection_creation"/>
        <table>
            <tr>
                <td colspan="3" class="titre">Nouveau concours de collection</td>
            </tr>
            <tr>
                <td class="soustitre2">Titre</td>
                <td><input type="text" name="form_titre" value=""/></td>
                <td>Dénomination du concours (typiquement, « Collections de citrouilles, 2010 »).</td>
            </tr>
            <?php echo '<tr><td class="soustitre2">Objet de collection</td><td><select name="form_tobj_objet" onchange="filtrer_gobj(this.value, -1);"><option value="-1">Choisissez un type d’objet...</option>';
            $req         =
                'select distinct tobj_cod, tobj_libelle from type_objet inner join objet_generique on gobj_tobj_cod = tobj_cod order by tobj_libelle';
            $stmt        = $pdo->query($req);
            $script_tobj = '';
            while ($result = $stmt->fetch())
            {
                $clef        = $result['tobj_cod'];
                $valeur      = $result['tobj_libelle'];
                $script_tobj .= "tableauObjets[$clef] = new Array();\n";
                echo "<option value='$clef'>$valeur</option>";
            }
            require G_CHE . "web/jeu_test/blocks/_admin_collection_detail.php";
            ?>
            <tr>
                <td class="soustitre2">Date d’ouverture (aaaa-mm-jj)</td>
                <td><input type="text" name="form_date_ouverture" value=""/></td>
                <td>La date à laquelle le concours commence.</td>
            </tr>
            <tr>
                <td class="soustitre2">Date de fermeture (aaaa-mm-jj)</td>
                <td><input type="text" name="form_date_fermeture" value=""/></td>
                <td>La date à laquelle le concours est terminé.</td>
            </tr>
            <tr>
                <td class="soustitre2" colspan="2"><p>Descriptif (html)</p><textarea rows="10" cols="50"
                                                                                     name="form_description"></textarea>
                </td>
                <td>Le texte qui apparaîtra en en-tête de la page du concours.</td>
            </tr>
        </table>
        <input type="submit" class="test" value="Valider"/>
    </form>
    <?php echo "<script type='text/javascript'>
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
