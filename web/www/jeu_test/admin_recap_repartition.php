<?php
include "blocks/_header_page_jeu.php";

$droit_modif = 'dcompt_modif_gmon';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    include "admin_edition_header.php";

    $restreindreEtages = isset($_GET["restreindreEtages"]);
    $ignorerProving = isset($_GET["ignorerProving"]);
    $grouperAnnexes = isset($_GET["grouperAnnexes"]);
    $objetsNonDropables = isset($_GET["objetsNonDropables"]);
    $typeObjet = (isset($_GET["typeObjet"])) ? $_GET["typeObjet"] : -1;

    echo "<hr /><p>Cette page permet d’avoir un aperçu rapide des trésors générés par les monstres, étage par étage.</p>";
    echo "<p>Le chiffre indiqué dans chaque case du tableau représente <strong>la probabilité, à chaque fois qu’on tue un monstre dans l’étage, que l’objet tombe.</strong></p>";
    echo "<p>Ce chiffre regroupe donc, pour chaque étage, deux paramètres : les chances d’apparition d’un type de monstre, et les chances que ce monstre porte un type d’objet.</p><hr />";

    // Liste déroulante de choix de type d’objet
    $req = "SELECT tobj_cod, tobj_libelle FROM type_objet ORDER BY tobj_libelle";
    $stmt = $pdo->query($req);
    echo '<form action="#" method="GET">';
    echo '<select name="typeObjet">';
    echo '<option value="-1">-- Choisissez un type d’objet --</option>';
    echo '<option value="-2"' . (($typeObjet == -2) ? ' selected="selected"' : '') . '>-- Brouzoufs --</option>';
    while ($result = $stmt->fetch())
        echo '<option value="' . $result['tobj_cod'] . '"' . (($typeObjet == $result['tobj_cod']) ? ' selected="selected"' : '') . '>' . $result['tobj_libelle'] . '</option>';
    echo '</select><br />';
    $selected = ($restreindreEtages) ? ' checked="checked"' : '';
    echo '<input type="checkbox" name="restreindreEtages" id="restreindreEtages"' . $selected . ' /><label for="restreindreEtages">Restreindre la recherche aux étages principaux ?</label><br />';
    $selected = ($ignorerProving) ? ' checked="checked"' : '';
    echo '<input type="checkbox" name="ignorerProving" id="ignorerProving"' . $selected . ' /><label for="ignorerProving">Ignorer les étages reliés au Proving ?</label><br />';
    $selected = ($grouperAnnexes) ? ' checked="checked"' : '';
    echo '<input type="checkbox" name="grouperAnnexes" id="grouperAnnexes"' . $selected . ' /><label for="grouperAnnexes">Grouper les annexes et antres dans l’étage principal ?</label><br />';
    $selected = ($objetsNonDropables) ? ' checked="checked"' : '';
    echo '<input type="checkbox" name="objetsNonDropables" id="objetsNonDropables"' . $selected . ' /><label for="objetsNonDropables">Inclure les objets non dropables ?</label><br />';
    echo '<input type="submit" value="Afficher" class="test" />';
    echo '</form>';

    $requete_series = "SELECT gmon_cod as serie_gmon_cod, coalesce(arme.probaTotale, armure.probaTotale) as serie_probaTotale, coalesce(arme.seequo_gobj_cod, armure.seequo_gobj_cod) as serie_gobj_cod
		FROM monstre_generique
		LEFT OUTER JOIN (
			SELECT seequ_cod, seequo_gobj_cod, SUM((1. - seequ_proba_sans_objet / 100.) * seequo_proba / total) as probaTotale
			FROM serie_equipement
			INNER JOIN serie_equipement_objet ON seequo_seequ_cod = seequ_cod
			INNER JOIN 
			(
				SELECT seequo_seequ_cod as serie, sum(seequo_proba) as total
				FROM serie_equipement_objet
				GROUP BY seequo_seequ_cod
			) somme ON somme.serie = seequo_seequ_cod
			GROUP BY seequ_cod, seequo_gobj_cod
		) arme ON arme.seequ_cod = gmon_serie_arme_cod
		LEFT OUTER JOIN (
			SELECT seequ_cod, seequo_gobj_cod, SUM((1. - seequ_proba_sans_objet / 100.) * seequo_proba / total) as probaTotale
			FROM serie_equipement
			INNER JOIN serie_equipement_objet ON seequo_seequ_cod = seequ_cod
			INNER JOIN 
			(
				SELECT seequo_seequ_cod as serie, sum(seequo_proba) as total
				FROM serie_equipement_objet
				GROUP BY seequo_seequ_cod
			) somme ON somme.serie = seequo_seequ_cod
			GROUP BY seequ_cod, seequo_gobj_cod
		) armure ON armure.seequ_cod = gmon_serie_armure_cod
		WHERE arme.seequ_cod IS NOT NULL OR armure.seequ_cod IS NOT NULL
		UNION ALL
		SELECT gmon_cod, 1, gmon_arme
		FROM monstre_generique
		WHERE gmon_serie_arme_cod IS NULL AND gmon_arme IS NOT NULL 
		UNION ALL
		SELECT gmon_cod, 1, gmon_armure
		FROM monstre_generique
		WHERE gmon_serie_armure_cod IS NULL AND gmon_armure IS NOT NULL ";


    if ($grouperAnnexes)
        $sous_requete_repartition = "SELECT rmon_gmon_cod as mon_cod, etage_reference as mon_etage, (SUM(rmon_poids) / tot_poids::numeric * 100)::int as mon_chance " .
            "FROM repart_monstre " .
            "INNER JOIN etage ON etage_numero = rmon_etage_cod " .
            "INNER JOIN ( " .
            "SELECT sum(rmon_poids) as tot_poids, etage_reference as tot_etage " .
            "FROM repart_monstre " .
            "INNER JOIN etage ON etage_numero = rmon_etage_cod " .
            "GROUP BY etage_reference " .
            ") t ON tot_etage = etage_reference " .
            "GROUP BY rmon_gmon_cod, etage_reference, tot_poids ";
    else
        $sous_requete_repartition = "SELECT rmon_gmon_cod as mon_cod, rmon_etage_cod as mon_etage, (SUM(rmon_poids) / tot_poids::numeric * 100)::int as mon_chance " .
            "FROM repart_monstre " .
            "INNER JOIN ( " .
            "SELECT sum(rmon_poids) as tot_poids, rmon_etage_cod as tot_etage " .
            "FROM repart_monstre " .
            "GROUP BY rmon_etage_cod " .
            ") t ON tot_etage = rmon_etage_cod " .
            "GROUP BY rmon_gmon_cod, rmon_etage_cod, tot_poids ";

    $jointuresMonstresEtages = "INNER JOIN ($sous_requete_repartition) monstre ON mon_cod = gmon_cod " .
        "INNER JOIN etage ON etage_numero = mon_etage ";

    $jointuresMonstresObjets = "INNER JOIN objets_monstre_generique ON ogmon_gmon_cod = gmon_cod " .
        "INNER JOIN objet_generique ON gobj_cod = ogmon_gobj_cod ";

    $jointuresMonstresSeries = "INNER JOIN ($requete_series) serie ON serie_gmon_cod = gmon_cod " .
        "INNER JOIN objet_generique ON gobj_cod = serie_gobj_cod ";

    if ($typeObjet >= 0)
    {
        // Requête drops inventaire
        $req = "SELECT gobj_cod, gobj_nom, etage_libelle, mon_etage, ROUND(SUM(ogmon_chance * mon_chance), 1) as chance_drop " .
            "FROM monstre_generique " .
            $jointuresMonstresObjets .
            $jointuresMonstresEtages .
            "WHERE gobj_tobj_cod = $typeObjet " .
            (($objetsNonDropables) ? " " : "AND gobj_deposable = 'O' ") .
            (($restreindreEtages) ? "AND etage_numero <= 0 " : " ") .
            (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
            "GROUP BY gobj_cod, gobj_nom, mon_etage, etage_libelle, etage_reference " .
            "ORDER BY etage_reference desc, mon_etage, gobj_cod ";

        // Requête drops objets équipés
        $req_equip = "SELECT gobj_cod, gobj_nom, etage_libelle, mon_etage, ROUND(SUM(serie_probaTotale * mon_chance), 1) as chance_drop " .
            "FROM monstre_generique " .
            $jointuresMonstresSeries .
            $jointuresMonstresEtages .
            "WHERE gobj_tobj_cod = $typeObjet " .
            (($objetsNonDropables) ? " " : "AND gobj_deposable = 'O' ") .
            (($restreindreEtages) ? "AND etage_numero <= 0 " : " ") .
            (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
            "GROUP BY gobj_cod, gobj_nom, mon_etage, etage_libelle, etage_reference " .
            "ORDER BY etage_reference desc, mon_etage, gobj_cod ";

        if ($grouperAnnexes)
        {
            // Requête drops inventaire
            $req = "SELECT gobj_cod, gobj_nom, etage_libelle || ' (annexes comprises)' as etage_libelle, etage_numero as mon_etage, ROUND(SUM(ogmon_chance * mon_chance), 1) as chance_drop " .
                "FROM monstre_generique " .
                $jointuresMonstresObjets .
                $jointuresMonstresEtages .
                "WHERE gobj_tobj_cod = $typeObjet " .
                (($objetsNonDropables) ? " " : "AND gobj_deposable = 'O' ") .
                (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
                "GROUP BY gobj_cod, gobj_nom, etage_numero, etage_libelle " .
                "ORDER BY etage_numero desc, gobj_cod ";

            // Requête drops objets équipés
            $req_equip = "SELECT gobj_cod, gobj_nom, etage_libelle || ' (annexes comprises)' as etage_libelle, etage_numero as mon_etage, ROUND(SUM(serie_probaTotale * mon_chance), 1) as chance_drop " .
                "FROM monstre_generique " .
                $jointuresMonstresSeries .
                $jointuresMonstresEtages .
                "WHERE gobj_tobj_cod = $typeObjet " .
                (($objetsNonDropables) ? " " : "AND gobj_deposable = 'O' ") .
                (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
                "GROUP BY gobj_cod, gobj_nom, etage_numero, etage_libelle " .
                "ORDER BY etage_numero desc, gobj_cod ";
        }
    } else if ($typeObjet == -2)
    {
        // Requête : gains de brouzoufs en drops purs
        $req = "SELECT -2 as gobj_cod, 'Brouzoufs purs' as gobj_nom, etage_libelle, mon_etage, ROUND(SUM(gmon_or * mon_chance) / 2 :: numeric, 1) as chance_drop " .
            "FROM monstre_generique " .
            $jointuresMonstresEtages .
            (($restreindreEtages) ? "WHERE etage_numero <= 0 " : " ") .
            (($ignorerProving && $restreindreEtages) ? "AND etage_reference <> -100 " : " ") .
            (($ignorerProving && !$restreindreEtages) ? "WHERE etage_reference <> -100 " : " ") .
            "GROUP BY mon_etage, etage_libelle, etage_reference " .
            "ORDER BY etage_reference desc, mon_etage ";

        // Brouzoufs venant des drops d’objets (hors runes)
        $req2 = "SELECT -1 as gobj_cod, 'Revente objets (hors runes)' as gobj_nom, etage_libelle, mon_etage, ROUND(SUM(ogmon_chance * mon_chance) / 100 :: numeric, 1) * (case when gobj_tobj_cod = 5 then 0 else gobj_valeur / 2 end) as chance_drop " .
            "FROM monstre_generique " .
            $jointuresMonstresObjets .
            $jointuresMonstresEtages .
            "WHERE gobj_deposable = 'O' " .
            (($restreindreEtages) ? "AND etage_numero <= 0 " : " ") .
            (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
            "GROUP BY mon_etage, etage_libelle, etage_reference, gobj_tobj_cod, gobj_valeur " .
            "ORDER BY etage_reference desc, mon_etage ";

        // Requête drops objets équipés
        $req_equip = "SELECT -3, 'Revente objets équipés' as gobj_nom, etage_libelle, mon_etage, ROUND(SUM(serie_probaTotale * mon_chance) * gobj_valeur / 200, 1) as chance_drop " .
            "FROM monstre_generique " .
            $jointuresMonstresSeries .
            $jointuresMonstresEtages .
            "WHERE  gobj_deposable = 'O' " .
            (($restreindreEtages) ? "AND etage_numero <= 0 " : " ") .
            (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
            "GROUP BY mon_etage, etage_libelle, etage_reference, gobj_valeur " .
            "ORDER BY etage_reference desc, mon_etage ";

        if ($grouperAnnexes)
        {
            // Requête : gains de brouzoufs en drops purs
            $req = "SELECT -2 as gobj_cod, 'Brouzoufs purs' as gobj_nom, etage_libelle || ' (annexes comprises)' as etage_libelle, etage_numero as mon_etage, ROUND(SUM(gmon_or * mon_chance) / 2 :: numeric, 1) as chance_drop " .
                "FROM monstre_generique " .
                $jointuresMonstresEtages .
                (($ignorerProving) ? "WHERE etage_reference <> -100 " : " ") .
                "GROUP BY etage_numero, etage_libelle " .
                "ORDER BY etage_numero desc ";

            // Brouzoufs venant des drops d’objets (hors runes)
            $req2 = "SELECT -1 as gobj_cod, 'Revente objets (hors runes)' as gobj_nom, etage_libelle || ' (annexes comprises)' as etage_libelle, etage_numero as mon_etage, ROUND(SUM(ogmon_chance * mon_chance) / 100 :: numeric, 1) * (case when gobj_tobj_cod = 5 then 0 else gobj_valeur / 2 end) as chance_drop " .
                "FROM monstre_generique " .
                $jointuresMonstresObjets .
                $jointuresMonstresEtages .
                "WHERE gobj_deposable = 'O' " .
                (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
                "GROUP BY etage_numero, etage_libelle, gobj_tobj_cod, gobj_valeur " .
                "ORDER BY etage_numero desc ";

            // Requête drops objets équipés
            $req_equip = "SELECT -3, 'Revente objets équipés' as gobj_nom, etage_libelle || ' (annexes comprises)' as etage_libelle, etage_numero as mon_etage, ROUND(SUM(serie_probaTotale * mon_chance) * gobj_valeur / 200, 1) as chance_drop " .
                "FROM monstre_generique " .
                $jointuresMonstresSeries .
                $jointuresMonstresEtages .
                "WHERE  gobj_deposable = 'O' " .
                (($ignorerProving) ? "AND etage_reference <> -100 " : " ") .
                "GROUP BY etage_numero, etage_libelle, gobj_tobj_cod, gobj_valeur " .
                "ORDER BY etage_numero desc ";
        }
    } else
        $req = "";

    $lesEtages = array();
    $lesObjets = array();
    $donnees = array();
    $objetsTousEtages = array();

    $unite = ($typeObjet >= 0) ? '&nbsp;%' : '&nbsp;br';

    // Récupération des données
    $stmt = $pdo->query($req);
    while ($result = $stmt->fetch())
    {
        if (!isset($lesEtages[$result['mon_etage']]))
        {
            $lesEtages[$result['mon_etage']] = $result['etage_libelle'];
            $donnees[$result['mon_etage']] = array();
        }
        if (!isset($lesObjets[$result['gobj_cod']]))
        {
            $lesObjets[$result['gobj_cod']] = $result['gobj_nom'];
            $objetsTousEtages[$result['gobj_cod']] = 0;
        }
        $donnees[$result['mon_etage']][$result['gobj_cod']] = $result['chance_drop'] / 100;
        $objetsTousEtages[$result['gobj_cod']] += $result['chance_drop'] / 100;
    }
    if (isset($req_equip))
    {
        $stmt = $pdo->query($req_equip);
        while ($result = $stmt->fetch())
        {
            if (!isset($lesEtages[$result['mon_etage']]))
            {
                $lesEtages[$result['mon_etage']] = $result['etage_libelle'];
                $donnees[$result['mon_etage']] = array();
            }
            if (!isset($lesObjets[$result['gobj_cod']]))
            {
                $lesObjets[$result['gobj_cod']] = $result['gobj_nom'];
                $objetsTousEtages[$result['gobj_cod']] = 0;
            }
            $donnees[$result['mon_etage']][$result['gobj_cod']] = $result['chance_drop'];
            $objetsTousEtages[$result['gobj_cod']] += $result['chance_drop'];
        }
    }
    if (isset($req2))
    {
        $db->query($req2);
        while ($result = $stmt->fetch())
        {
            if (!isset($lesEtages[$result['mon_etage']]))
            {
                $lesEtages[$result['mon_etage']] = $result['etage_libelle'];
                $donnees[$result['mon_etage']] = array();
            }
            if (!isset($lesObjets[$result['gobj_cod']]))
            {
                $lesObjets[$result['gobj_cod']] = $result['gobj_nom'];
            }
            $donnees[$result['mon_etage']][$result['gobj_cod']] += $result['chance_drop'] / 100;
        }
    }


    // Affichage des données sous forme de tableau
    if ($req)
    {
        echo '<table>';
        echo '<tr style="height:150px;"><th></th>';
    }
    foreach ($lesObjets as $obj_cod => $obj_nom)
        echo "<th class='soustitre2' style='height:150px; max-width:20px; overflow:none; text-align:left; vertical-align:bottom; padding-bottom:20px;'><div style='transform: rotate(-90deg);  text-align:left; vertical-align:top; margin-top:0px;'>" . str_replace(' ', '&nbsp;', $obj_nom) . "</div></th>";

    echo "<th class='soustitre2'>Total</th>";

    foreach ($lesEtages as $etage_num => $etage_nom)
    {
        $somme_etage = 0;
        echo "<tr><td class='soustitre2' style='min-width:150px;'>$etage_nom ($etage_num)</td>";
        foreach ($lesObjets as $obj_cod => $obj_nom)
        {
            if (isset($donnees[$etage_num][$obj_cod]) && $donnees[$etage_num][$obj_cod] > 0)
            {
                echo '<td>' . $donnees[$etage_num][$obj_cod] . $unite . '</td>';
                $somme_etage += $donnees[$etage_num][$obj_cod];
            } else
                echo '<td style="background-color:black">0' . $unite . '</td>';
        }
        echo '<td class="soustitre2"><strong>' . $somme_etage . $unite . '</strong></td>';
        echo "</tr>";
    }
    if ($typeObjet >= 0)
    {
        echo "<tr><td class='soustitre2'><strong>Total</strong></td>";

        foreach ($lesObjets as $obj_cod => $obj_nom)
        {
            if (isset($objetsTousEtages[$obj_cod]) && $objetsTousEtages[$obj_cod] > 0)
                echo '<td class="soustitre2"><strong>' . $objetsTousEtages[$obj_cod] . '</strong></td>';
            else
                echo '<td class="soustitre2" style="background-color:black">0</td>';
        }
    }
    if ($req)
        echo '</tr></table>';

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
