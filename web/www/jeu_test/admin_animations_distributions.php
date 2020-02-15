<?php
if (!defined("APPEL"))
{
    die("Erreur d’appel de page !");
}
$pdo = new bddpdo();

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Distributions générales</div><br />';
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case 'distribution_creation':    // Crée une distribution d’objets
        $code_objet = $_POST['form_objet'];
        $code_etage = $_POST['etage'];
        $antres     = (isset($_POST['antres']));

        $code_race    = $_POST['code_race'];
        $code_monstre = $_POST['code_monstre'];

        $distrib_localisation  = $_POST['distrib_localisation'];
        $distrib_eparpillement = $_POST['distrib_eparpillement'];
        $distrib_quantite      = $_POST['distrib_quantite'];

        // Différents cas.
        $texte = 'Distribution générale de ';

        $nom_objet = '';
        if ($code_objet == -2)
        {
            $nom_objet = 'Brouzoufs';
        } else if ($code_objet == -1)
        {
            $nom_objet = 'PXs';
        } else
        {
            $req_objet = "select gobj_nom from objet_generique where gobj_cod = $code_objet";
            $stmt      = $pdo->prepare($req_objet);
            $stmt      = $pdo->execute(array(":code" => $code_objet), $stmt);
            $result    = $stmt->fetch();
            $nom_objet = $result['gobj_nom'];
        }
        $texte .= $nom_objet;

        // Formattage de $type_perso, sous la forme '1,2,3'
        $type_perso = '';
        $nom_type   = '';
        if (isset($_POST['distrib_type_1']))
        {
            $type_perso = '1';
            $nom_type   = ' aventurier';
        }
        if (isset($_POST['distrib_type_2']))
        {
            $type_perso .= ',2';
            $nom_type   = ', monstre';
        }
        if (isset($_POST['distrib_type_3']))
        {
            $type_perso .= ',3';
            $nom_type   = ', familier';
        }
        if ($type_perso == '')
        {
            $type_perso = '-1';
        }

        while ($type_perso[0] == ',')
        {
            $type_perso = substr($type_perso, 1);
        }

        while ($nom_type[0] == ',')
        {
            $nom_type = substr($nom_type, 1);
        }


        $where_type_perso = " perso_type_perso IN ($type_perso) ";

        // Formattage de la race
        $where_race = "";
        $nom_race   = ' de toute race ';
        if ($code_race != 'tous')
        {
            $req_race   = "select race_nom from race where race_cod = :race";
            $stmt       = $pdo->prepare($req_race);
            $stmt       = $pdo->execute(array(":race" => $code_race), $stmt);
            $result     = $stmt->fetch();
            $nom_race   = ' de race « ' . $result['race_nom'] . ' »';
            $where_race .= " AND perso_race_cod = $code_race ";
        }

        // Formattage du monstre
        $nom_monstre   = ' de tout type ';
        $where_monstre = "";
        if ($code_monstre != 'tous')
        {
            $req_monstre   = "select gmon_nom from monstre_generique where gmon_cod = :monstre";
            $stmt          = $pdo->prepare($req_monstre);
            $stmt          = $pdo->execute(array(":monstre" => $code_monstre), $stmt);
            $result        = $stmt->fetch();
            $nom_monstre   = ' de type « ' . $result['gmon_nom'] . ' »';
            $where_monstre .= " AND perso_gmon_cod = $code_monstre ";
        }

        // Filtrage étages
        $where_etage = '';
        $texte_etage = '';
        if ($code_etage == 'tous' && $antres)
        {
            $where_etage .= ' etage_reference != -100 ';
            $texte_etage .= "dans tous les étages !";
        } else if ($code_etage == 'tous' && !$antres)
        {
            $where_etage .= ' etage_reference != -100 AND etage_numero = etage_reference';
            $texte_etage .= "dans tous les étages principaux !";
        } else if ($code_etage != 'tous' && $antres)
        {
            $where_etage .= " etage_reference = $code_etage";
            $req_etage   = "select etage_libelle from etage where etage_numero = :etage";
            $stmt        = $pdo->prepare($req_etage);
            $stmt        = $pdo->execute(array(":etage" => $code_etage), $stmt);
            $result      = $stmt->fetch();
            $nom_etage   = $result['etage_libelle'];
            $texte_etage .= "dans l’étage « $nom_etage » et ses dépendances !";
        } else if ($code_etage != 'tous' && !$antres)
        {
            $where_etage .= " etage_numero = $code_etage";

            $req_etage   = "select etage_libelle from etage where etage_numero = etage";
            $stmt        = $pdo->prepare($req_etage);
            $stmt        = $pdo->execute(array(":etage" => $code_etage), $stmt);
            $result      = $stmt->fetch();
            $nom_etage   = $result['etage_libelle'];
            $texte_etage .= "dans l’étage « $nom_etage » !";
        }

        // DISTRIBUTION PROPREMENT DITE
        // Distribution en inventaire d’un objet physique
        if ($distrib_localisation == 'inv' && $code_objet >= 0)
        {
            $where = " $where_type_perso $where_monstre $where_race and $where_etage ";
            $texte .= " ($distrib_quantite pour chaque $nom_type, $nom_race, $nom_monstre $texte_etage)";

            // garder le 'admin_longue_requete', il permet à la purge SQL d’identifier
            // cette requête comme devant être tuée après 5 minutes au lieu d’une seule
            $req_distrib = "select 'admin_longue_requete', etage_libelle,
					cree_objet_perso_nombre($code_objet, perso_cod, $distrib_quantite) as distribution
				from perso
				inner join perso_position on ppos_perso_cod = perso_cod
				inner join positions on pos_cod = ppos_pos_cod
				inner join etage on etage_numero = pos_etage
				where $where";
            $stmt        = $pdo->query($req_distrib);
            echo '<p>Distribution réalisée !</p>';
            $etage_libelle = '';
            $nombre        = 0;
            while ($result = $stmt->fetch())
            {
                if ($etage_libelle != $result['etage_libelle'])
                {
                    if ($nombre > 0)
                        echo "<p><strong>Pour l’étage $etage_libelle</strong> : $nombre personnages impactés.</p>";
                    $nombre        = 0;
                    $etage_libelle = $result['etage_libelle'];
                }
                $nombre++;
            }
            if ($nombre > 0)
            {
                echo "<p><strong>Pour l’étage $etage_libelle</strong> : $nombre personnages impactés.</p>";
            }

        }

        // Distribution de brouzoufs ou PX
        if ($distrib_localisation == 'inv' && $code_objet < 0)
        {
            $champ = ($code_objet == -1) ? 'perso_px' : 'perso_po';
            $where = " $where_type_perso $where_monstre $where_race and $where_etage ";
            $texte .= " ($distrib_quantite pour chaque $nom_type, $nom_race, $nom_monstre $texte_etage)";

            $req_distrib = "update perso set $champ = $champ + $distrib_quantite
				where perso_cod in
					(select perso_cod from perso
					inner join perso_position on ppos_perso_cod = perso_cod
					inner join positions on pos_cod = ppos_pos_cod
					inner join etage on etage_numero = pos_etage
					where $where)";
            $stmt        = $pdo->query($req_distrib);
            echo '<p>Distribution réalisée !</p>';
        }

        // Distribution au sol
        if ($distrib_localisation == 'sol')
        {
            $texte .= " (à raison de 1 pour $distrib_eparpillement cases), $texte_etage";

            // garder le 'admin_longue_requête', il permet à la purge SQL d’identifier
            // cette requête comme devant être tuée après 5 minutes au lieu d’une seule
            $req_distrib = "select 'admin_longue_requête', etage_libelle,
					cree_objet_nombre($code_objet, etage_numero, nbcases::integer / $distrib_eparpillement) as distribution
				from
					(select etage_libelle, etage_numero, count(*) as nbcases from etage
					inner join positions on pos_etage = etage_numero
					left outer join murs on mur_pos_cod = pos_cod
					where mur_pos_cod is null
						and $where_etage
					group by etage_libelle, etage_numero) t";
            $stmt        = $pdo->query($req_distrib);
            echo '<p>Distribution réalisée !</p>';
            while ($result = $stmt->fetch())
            {
                $resultat = $result['distribution'];
                $etage    = $result['etage_libelle'];
                echo "<p><strong>Pour l’étage $etage</strong></p><p>$resultat</p>";
            }
        }

        $req   =
            "INSERT INTO historique_animations(anim_date, anim_texte, anim_type) values (now()::date,:texte, 'distribution')";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":texte" => $texte),$stmt);

        $log = date("d/m/y - H:i") . "\tCompte $compt_cod a généré une $texte\n";
        writelog($log, 'animation_distributions');
        break;
}
echo '<form name="distribution_creation" method="POST" action="#" onsubmit="return confirm(\'Êtes-vous sûr de vouloir lancer cette distribution ?\');">
	<input type="hidden" name="methode" value="distribution_creation" />';
echo '<p>Les distributions générales permettent de donner à tous les personnages sélectionnés, ou au sol, des objets, PXs ou brouzoufs. Les pochettes surprises sont gérées de façon annexe.</p>
	<p>Plusieurs paramètres sont applicables :</p>
	<p> - Objet : c’est ce qu’il faut distribuer.</p>
	<p> - Étage : c’est l’étage qui sera touché par la distribution. « Tous les étage » touchera l’ensemble des souterrains, à l’exception des étages reliés au Proving Ground. Ces derniers étages sont néanmoins ciblables individuellement.</p>
	<p> - Inclure les antres : indique si les étages rattachés aux étages principaux sont inclus dans la distribution (antres, mines, sous-bassements, cathédrales...)</p>
	<p> - Localisation : indique si la distribution se fait au sol ou en inventaire.</p>
	<p> - Cible : pour les distributions en inventaire, indique si on cible les aventuriers, les monstres et/ou les familiers.</p>
	<p> - Race : pour les distributions en inventaire, indique si on cible les aventuriers et/ou les monstres d’une race précise.</p>
	<p> - Monstre : pour les distributions en inventaire, indique si on cible les monstres d’un type précis.</p>
	<p> - Nombre : pour les distributions en inventaire, donne le nombre d’objets à donner à chacun.</p>
	<p> - Éparpillement : pour les distributions au sol, donne le nombre de cases de l’étage pour un objet. Une valeur de 50 signifie qu’on créera un objet pour 50 cases (hors murs). Pour un étage standard (1600 cases), une valeur de 50 ajoute donc 32 objets. </p>
	<table>
		<tr><td class="titre"><strong>Objet</strong></td>
		<td class="titre"><strong>Étage(s)</strong></td>
		<td class="titre"><strong>Cibles</strong></td>
		<td class="titre"><strong>Quantité</strong></td>
		<td class="titre"><strong>Lancer la distribution ?</strong></td></tr>';

// Objet
echo '<tr><td class="soustitre2"><select name="form_tobj_objet" onchange="filtrer_gobj(this.value, -1, \'distrib_form_objet\', tableauObjetsDistribution);">
	<option value="-2">Choisissez un type d’objet...</option>
	<option value="-1">PX ou brouzoufs...</option>';
$script_tobj = 'var tableauObjetsDistribution = new Array();';
$script_tobj .= "tableauObjetsDistribution[-1] = new Array();\n";
$script_gobj = "tableauObjetsDistribution[-1][-2] = \"Brouzoufs\";\n";
$script_gobj .= "tableauObjetsDistribution[-1][-1] = \"PXs\";\n";

$req =
    'select distinct tobj_cod, tobj_libelle from type_objet inner join objet_generique on gobj_tobj_cod = tobj_cod order by tobj_libelle';
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
    $clef        = $result['tobj_cod'];
    $valeur      = $result['tobj_libelle'];
    $script_tobj .= "tableauObjetsDistribution[$clef] = new Array();\n";
    echo "<option value='$clef'>$valeur</option>";
}
echo '</select><br /><select name="form_objet" id="distrib_form_objet">';
$req = 'select gobj_cod, gobj_tobj_cod, gobj_nom from objet_generique order by gobj_tobj_cod, gobj_nom';
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
    $clef        = $result['gobj_cod'];
    $clef_tobj   = $result['gobj_tobj_cod'];
    $valeur      = $result['gobj_nom'];
    $script_gobj .= "tableauObjetsDistribution[$clef_tobj][$clef] = \"" . str_replace('"', '', $valeur) . "\";\n";
}

echo '</select></td>';

// Étage
echo '<td class="soustitre2">
	<select name="etage">
		<option value="tous">Tous les étages !</option>
	';
echo $html->etage_select();

// Cible
?>

</select><br/>
<input type="checkbox" name="antres" id="distrib_antres" checked="checked"/><label for="distrib_antres">Inclure les
    antres reliées à l’étage ?</label></td>

<td class="soustitre2">
    Distribution... <select name="distrib_localisation">
        <option value='inv'>En inventaire</option>
        <option value='sol'>Au sol</option>
    </select><br/>
    <input type='checkbox' name="distrib_type_1" id='distrib_type_1' value='1'/><label
            for="distrib_type_1">Aventuriers</label><br/>
    <input type='checkbox' name="distrib_type_2" id='distrib_type_2' value='2'/><label
            for="distrib_type_2">Monstres</label><br/>
    <input type='checkbox' name="distrib_type_3" id='distrib_type_3' value='3'/><label
            for="distrib_type_3">Familiers</label><br/>
    <select name="code_race">
        <option value="tous">Race indifférente</option>
        <?php
        // Races

        $req = 'select race_cod, race_nom from race order by race_cod IN (1, 2, 3) desc, race_nom';
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            echo '<option value="' . $result['race_cod'] . '">' . $result['race_nom'] . '</option>';
        }
        echo '</select><br /><select name="code_monstre"><option value="tous">Monstre indifférent</option>';

        // Monstres
        $req = 'select gmon_cod, gmon_nom, gmon_niveau from monstre_generique order by gmon_nom';
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            echo '<option value="' . $result['gmon_cod'] . '">' . $result['gmon_nom'] . ' (Niv. ' . $result['gmon_niveau'] . ' )</option>';
        }
        ?>
    </select></td>
<td class="soustitre2">
    <label for="distrib_quantite">Quantité : </label><input type="text" name="distrib_quantite" id="distrib_quantite"
                                                            value="" style="width:20px;"/><br/>
    <label for="distrib_eparpillement">Éparpillement : 1 objet pour </label><input type="text"
                                                                                   name="distrib_eparpillement"
                                                                                   id="distrib_eparpillement" value=""
                                                                                   style="width:20px;"/><label
            for="distrib_eparpillement"> cases.</label>
</td>
<td class="soustitre2">
    <input type="submit" value="Lancer la distribution !" class="test"/>
</td></tr>
</table></form>
<?php echo "<script type='text/javascript'>
		$script_tobj
		$script_gobj
	</script>";

echo "<p><strong>Historique des distributions :</strong> (les distributions sont enregistrées depuis fin 2012)</p><ul>";

$req =
    'SELECT to_char(anim_date,\'DD/MM/YYYY\') as date, anim_texte, (now()::date - anim_date) as duree FROM historique_animations WHERE anim_type=\'distribution\' ORDER BY anim_date';
$stmt = $pdo->query($req);
$derniere_distrib = -1;
while ($result = $stmt->fetch())
{
    echo '<li>' . $result['date'] . ' : ' . $result['anim_texte'] . '</li>';
    $derniere_distrib = $result['duree'];
}
echo '</ul>';
echo '</div>';
