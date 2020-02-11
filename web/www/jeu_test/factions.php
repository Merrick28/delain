<?php ob_start();
include "blocks/_header_page_jeu.php";

$perso = new perso;
$perso->charge($perso_cod);
$is_lieu = $perso->is_lieu();

$erreur = !$is_lieu || !isset($faction);

$cout_amende_honorable = 10000;
$txt_amende_honorable  = "10 000";

if (!$erreur)
{
    $tab_lieu     = $perso->get_lieu();
    $lieu_cod     = $tab_lieu['lieu']->lieu_cod;
    $lieu_pos_cod = $tab_lieu['lieu_position']->lpos_pos_cod;

    // Faction demandée
    $req_factions = "SELECT v.fac_cod, v.fac_nom, f.fac_description, f.fac_introduction, v.pos_etage FROM v_factions_lieux v
		INNER JOIN factions f ON f.fac_cod = v.fac_cod
		WHERE v.lieu_cod = $lieu_cod
			AND v.fac_cod = $faction";
    $stmt         = $pdo->query($req_factions);
    if ($result = $stmt->fetch())
    {
        $faction_nom   = $result['fac_nom'];
        $faction_desc  = $result['fac_description'];
        $faction_intro = $result['fac_introduction'];
        $etage_numero  = $result['pos_etage'];
    } else
    {
        $erreur = true;
    }
}

if ($erreur)
{
    echo "<p>Anomalie, vous n’êtes pas sur un lieu où cette faction officie !</p>";
} else
{
    // Récupérer infos sur la faction et le perso.
    $req_rang_perso = "SELECT pfac_points, pfac_rang_numero, pfac_statut, 
			case when pfac_date_mission IS NULL OR pfac_date_mission + '5 days'::interval < now() then 1 else 0 end as date_mission_ok
		FROM faction_perso
		WHERE pfac_fac_cod = $faction
			AND pfac_perso_cod = $perso_cod";
    $stmt           = $pdo->query($req_rang_perso);
    if ($result = $stmt->fetch())
    {
        $pfac_points      = $result['pfac_points'];
        $date_mission_ok  = $result['date_mission_ok'] == 1;
        $pfac_rang_numero = $result['pfac_rang_numero'];
        $pfac_statut      = $result['pfac_statut'];
    } else
    {
        $pfac_points      = 0;
        $date_mission_ok  = true;
        $pfac_rang_numero = 0;
        $pfac_statut      = 0;
    }
    $pfac_nv_points = $pfac_points;

    // Afficher introduction de la faction
    echo "<div><strong>Faction « $faction_nom »</strong><br /><em>$faction_desc</em></div>";

    if (!isset($methode))
        $methode = 'Début';

    switch ($methode)
    {
        case "accepte";
            // Vérifications
            if (!isset($mission))
            {
                $erreur         = true;
                $message_erreur = "Erreur ! La mission à accepter n’est pas définie.";
            }
            if (!$date_mission_ok)
            {
                $erreur         = true;
                $message_erreur = "Erreur ! Vous ne pouvez pas accepter cette mission pour le moment.";
            }
            if (!$erreur)
            {
                $req_mission = "select miss_nom, coalesce(miss_fonction_releve, '') as fonction_releve, mpf_delai, mpf_recompense from mission_perso_faction_lieu
					INNER JOIN missions ON miss_cod = mpf_miss_cod
					INNER JOIN faction_missions ON fmiss_fac_cod = mpf_fac_cod AND fmiss_miss_cod = miss_cod
					WHERE mpf_cod = $mission AND mpf_fac_cod = $faction AND mpf_etage_numero = $etage_numero AND mpf_statut = 0 and fmiss_rang_min <= $pfac_rang_numero";
                $stmt        = $pdo->query($req_mission);
                if (!$result = $stmt->fetch())
                {
                    $erreur         = true;
                    $message_erreur =
                        "Erreur ! La mission à accepter n’est pas définie, ne correspond pas à la bonne faction, ou ne vous est pas destinée.";
                } else
                {
                    $nom              = $result['miss_nom'];
                    $delai            = $result['mpf_delai'];
                    $fonction_releve  = $result['fonction_releve'];
                    $message_reussite =
                        "Parfait ! Réalisez cette mission de $nom d’ici $delai jours, et vous aurez votre récompense.";
                }
            }
            if (!$erreur && $fonction_releve != '')
            {
                $req_releve = "select $fonction_releve($mission, $perso_cod) as resultat";
                $stmt       = $pdo->query($req_releve);
                $result     = $stmt->fetch();
                if ($result['resultat'] == 'f')
                {
                    $erreur         = true;
                    $message_erreur = "Erreur ! Vous ne pouvez pas relever cette mission.";
                }
            }
            if (!$erreur)
            {
                $req_attribue = "select mission_attribue($perso_cod, $mission)";
                $stmt         = $pdo->query($req_attribue);
                echo "<div class='bordiv' style='margin-top:10px;'>$message_reussite</div>";
            } else
            {
                echo "<div class='bordiv' style='margin-top:10px;'>$message_erreur</div>";
            }
            break;

        case 'demission':    // Message de confirmation
            $message = "Attention ! Êtes-vous sûr de vouloir rompre toute relation avec la faction $faction_nom ?<br />
				Cette action vous fera perdre votre avancement de cette faction, et risque de leur laisser un mauvais souvenir...<hr /><br />
				<a href='factions.php?methode=demission_oui&faction=$faction'>Oui ! J’envoie « $faction_nom » paître, ils ne me méritent pas.</a><br /><br />
				<a href='factions.php?faction=$faction'>Non, surtout pas ! J’ai pas fait exprès de cliquer, promis...</a><br />";
            echo "<div class='bordiv' style='margin-top:10px;'>$message</div>";
            break;

        case 'demission_oui':    // Démission validée
            $req_dem =
                "update faction_perso set pfac_statut = 1 where pfac_fac_cod = $faction and pfac_perso_cod = $perso_cod";
            $stmt    = $pdo->query($req_dem);

            $message = "Voilà, c’est fait. Vous avez démissionné de votre position chez $faction_nom.<br />
				Ils ne sont pas content, ah ça, non !<br />";
            echo "<div class='bordiv' style='margin-top:10px;'>$message</div>";
            $pfac_statut = 1;
            break;

        case 'amende_honorable':    // Démission validée
            $req_brouzoufs = "select perso_po from perso where perso_cod = $perso_cod";
            $stmt          = $pdo->query($req_brouzoufs);
            $result        = $stmt->fetch();
            if ($result['perso_po'] >= $cout_amende_honorable)
            {
                $req_brouzoufs =
                    "update perso set perso_po = perso_po - $cout_amende_honorable where perso_cod = $perso_cod";
                $stmt          = $pdo->query($req_brouzoufs);

                $req_faction =
                    "update faction_perso set pfac_points = 0, pfac_statut = 0 where pfac_fac_cod = $faction and pfac_perso_cod = $perso_cod";
                $stmt        = $pdo->query($req_faction);

                $message =
                    "Allez, c’est fait... $txt_amende_honorable grouzoufs ont été retirés de votre inventaire.<br />";
            } else
            {
                $message =
                    "Et en plus il se moque de nous... Vous n’avez pas le début de la somme demandée ! Barrez-vous ou j’appelle les gardes.<br />";
            }
            $pfac_statut = 0;
            break;

        default:
            break;
    }

    echo "<div style='margin-top:10px;'>$faction_intro</div>";

    if ($pfac_statut == 1)    // Le gars est démissionnaire
    {
        echo "<hr /><div style='margin-top:10px'><p>Qu’est-ce que vous faites encore là ? Allez, du balais ! On n’a pas de temps à consacrer aux rénégats, nous...</p>
			<p>À moins que vous ne souhaitiez faire amende honorable ? Nous pouvons vous reprendre, mais sous certaines conditions. Vous perdrez le rang que vous aviez acquis. Et pour nous assurer que vous ne plaisantez pas, il nous faudrait également $txt_amende_honorable brouzoufs. C’est le prix de la réconciliation.</p>
			<p><a href='factions.php?methode=amende_honorable&faction=$faction'>Tenez, voilà vos $txt_amende_honorable brouzoufs...</a></p>
			<p><a href='factions.php?methode=rien&faction=$faction'>Ah, laissez tomber, vous êtes trop minables pour moi !</a></p></div>";
    }

    if ($pfac_statut == 0)    // Le gars n’est pas démissionnaire
    {
        // -> Résolution des missions en cours
        $tableauMission      = $perso->missions_du_perso();
        $missionEnCours      = false;
        $missionEnRetard     = false;
        $resultat_validation = "";
        foreach ($tableauMission as $uneMission)
        {
            if ($uneMission['ÀValider'])
            {
                if ($resultat_validation == '')
                    $resultat_validation .= '<div class="bordiv">Mission nouvellement terminée :<br />';

                // Valider la mission
                $missionCode    = $uneMission['Code'];
                $req_validation = "select mission_valider($missionCode) as resultat";
                $stmt           = $pdo->query($req_validation);

                if ($result = $stmt->fetch())
                    $resultat_validation .= '<p>' . $result['resultat'] . '</p>';

                // Récupérer les nouvelles infos sur la faction et le perso.
                $req_rang_perso = "SELECT pfac_points FROM faction_perso
					WHERE pfac_fac_cod = $faction AND pfac_perso_cod = $perso_cod";
                $stmt           = $pdo->query($req_rang_perso);
                if ($result = $stmt->fetch())
                    $pfac_nv_points = $result['pfac_points'];
                else
                    $pfac_nv_points = 0;
            }
            if ($uneMission['EnCours'])
            {
                $missionEnCours = true;
            }
        }
        if ($resultat_validation != '')
            echo $resultat_validation . '</div>';

        if ($missionEnCours)
            echo "<div style='margin-top:10px;'>Hum hum... Vous avez déjà un travail à finir avant de pouvoir en réclamer un nouveau.</div>";

        // Message supplémentaire « ça fait longtemps qu’on vous avait pas vu » ?

        // Afficher texte fonction du rang
        $req_rang_perso = "SELECT rfac_nom, coalesce(rfac_intro, '') as rfac_intro, rfac_seuil, rank() over (partition by rfac_fac_cod order by rfac_seuil) as rang
			from faction_rangs 
			where rfac_fac_cod = $faction and rfac_seuil <= $pfac_points
			order by rfac_seuil desc
			limit 1";
        $stmt           = $pdo->query($req_rang_perso);
        if ($result = $stmt->fetch())
        {
            $rfac_nom   = $result['rfac_nom'];
            $rfac_intro = $result['rfac_intro'];
            $rfac_rang  = $result['rang'];
        } else
        {
            $rfac_nom   = '';
            $rfac_intro = 'Tiens ! Un nouveau venu. Venez venez, pour vous aussi nous avons des missions !';
            $rfac_rang  = 0;
        }
        // -> Promotions ? Dégradations ?
        $texte_promo = "";

        // Vérification du risque de traitrise...
        $req_affinite           = "select mission_calcule_affinite ($perso_cod, $faction) as affinite_ennemie";
        $stmt                   = $pdo->query($req_affinite);
        $result                 = $stmt->fetch();
        $affinite_ennemie       = explode(';', $result['affinite_ennemie']);
        $score_affinite_ennemie = $affinite_ennemie[0];
        $nom_affinite_ennemie   = $affinite_ennemie[1];

        // Le score d’affinité vaut (6 - f2f_note_estime) * pfac_rang_numero
        // Donc si le perso est au rang 2 dans une faction ayant une relation 3 avec $faction, le score sera 6.
        // Le rang maximal que l’on peut atteindre dans une faction est ($rg_max_faction) - aff / 2.
        // Donc dans notre exemple, on peut atteindre au mieux le rang 3.
        $req_rg_max = "select count(*) as rg_max from faction_rangs where rfac_fac_cod = $faction";
        $stmt       = $pdo->query($req_rg_max);
        $result     = $stmt->fetch();
        $rang_max   = $result['rg_max'] - $score_affinite_ennemie / 2;

        //Récupération du nom du nouveau rang (suite à validation de mission)
        $req_rang_perso = "SELECT rfac_nom, rank() over (partition by rfac_fac_cod order by rfac_seuil) as rang
			from faction_rangs 
			where rfac_fac_cod = $faction and rfac_seuil <= $pfac_nv_points
			order by rfac_seuil desc
			limit 1";
        $stmt           = $pdo->query($req_rang_perso);
        if ($result = $stmt->fetch())
        {
            $rfac_nv_nom  = $result['rfac_nom'];
            $rfac_nv_rang = $result['rang'];
        } else
        {
            $rfac_nv_nom  = '';
            $rfac_nv_rang = 0;
        }

        if ($pfac_rang_numero > $rfac_nv_rang)
        {
            // Le gars a un rang supérieur à celui auquel lui donnent droit ses points...
            $texte_promo =
                "<p style='margin-top:10px;'>C’est étonnant, $rfac_nom, je ne comprends pas comment vos états de service ont pu vous permettre d’atteindre un tel rang ! Nous sommes obligés de vous rétrograder au rang de « $rfac_nv_nom ».</p>";
            $req_promo   = "update faction_perso set pfac_rang_numero = $rfac_nv_rang
				WHERE pfac_fac_cod = $faction AND pfac_perso_cod = $perso_cod";
            $stmt        = $pdo->query($req_promo);
        }
        if ($pfac_rang_numero < $rfac_nv_rang) // le gars a pris un niveau, cool
        {
            $nouveau_rang   = $pfac_rang_numero + 1; // On y va un par un.
            $req_rang_perso = "SELECT rfac_nom
				from 
					(SELECT rfac_nom, rank() over (partition by rfac_fac_cod order by rfac_seuil) as rang
					FROM faction_rangs WHERE rfac_fac_cod = $faction) t
				where rang = $nouveau_rang";
            $stmt           = $pdo->query($req_rang_perso);
            $result         = $stmt->fetch();
            $rfac_nv_nom    = $result['rfac_nom'];

            // Autorise-t-on ce gain ?
            if ($nouveau_rang > $rang_max)
            {
                $texte_promo = "<p style='margin-top:10px;'>$rfac_nom, nos espions nous ont appris que vous travaillez régulièrement avec
					$nom_affinite_ennemie...<br />
					Si vous souhaitez bénéficier d’un avancement chez nous, il faudra abandonner ces pratiques ! Nous ne pouvons accepter
					qu’un « $rfac_nv_nom » se compromette auprès de nos ennemis.</p>";
            } else
            {
                // On donne des brouzoufs et des PXs pour cet exploit
                $brouzoufs   = 1000 * $nouveau_rang * ($nouveau_rang - $pfac_rang_numero);
                $pxs         = 20 * $nouveau_rang * ($nouveau_rang - $pfac_rang_numero);
                $texte_promo =
                    "<p style='margin-top:10px;'><strong>Félicitations $rfac_nom ! Vos états de service nous permettent de vous promouvoir au rang de « $rfac_nv_nom » !</strong><br /> Pour fêter ça, prenez ces $brouzoufs brouzoufs. Vous gagnez aussi $pxs PX.</p>";
                $req_promo   = "update faction_perso set pfac_rang_numero = $nouveau_rang
					WHERE pfac_fac_cod = $faction AND pfac_perso_cod = $perso_cod";
                $stmt        = $pdo->query($req_promo);
                $req_promo   = "update perso set perso_po = perso_po + $brouzoufs, perso_px = perso_px + $pxs
					WHERE perso_cod = $perso_cod";
                $stmt        = $pdo->query($req_promo);
            }
        }

        if ($rfac_intro == "")
            $rfac_intro =
                "<strong>$rfac_nom</strong>, content de vous revoir ! Il y a toujours du travail pour quelqu’un d’assidu.";

        if ($texte_promo != '')
            echo $texte_promo;
        else if ($score_affinite_ennemie > 0)
            echo "<p style='margin-top:10px;'>$rfac_nom, nous savons que vous travaillez pour $nom_affinite_ennemie...<br />
				Ce n’est pas problématique dans l’immédiat, mais sachez que cela pourra vous fermer des portes, un jour...</p>";

        // Afficher liste des missions
        if (!$missionEnCours && $date_mission_ok)
        {
            echo "<div style='margin-top:10px;'>$rfac_intro Alors, voici les missions que nous pouvons proposer.</div>";
            $req_missions = "SELECT miss_nom, mission_texte(mpf_cod) as libelle, mpf_delai, mpf_recompense, mpf_cod
				FROM mission_perso_faction_lieu
				INNER JOIN missions ON miss_cod = mpf_miss_cod
				INNER JOIN faction_missions ON fmiss_fac_cod = mpf_fac_cod AND fmiss_miss_cod = miss_cod
				WHERE mpf_fac_cod = $faction AND mpf_etage_numero = $etage_numero AND mpf_statut = 0 AND fmiss_rang_min <= $pfac_rang_numero 
					AND (mpf_pos_cod IS NULL OR mpf_pos_cod != $lieu_pos_cod)";  // On exclut les missions à destination du lieu lui-même...
            $stmt         = $pdo->query($req_missions);

            echo '<table><tr>
				<th class="soustitre">Type de mission</th>
				<th class="soustitre">Description</th>
				<th class="soustitre">Récompense</th>
				<th class="soustitre">Délai de réalisation</th>
				<th class="soustitre">Action</th></tr>';

            while ($result = $stmt->fetch())
            {
                $type       = $result['miss_nom'];
                $delai      = $result['mpf_delai'];
                $recompense = $result['mpf_recompense'];
                $texte      = $result['libelle'];
                $code       = $result['mpf_cod'];
                echo "<tr><td class='soustitre2'>$type</td>
					<td class='soustitre2'>$texte</td>
					<td class='soustitre2'>$recompense br</td>
					<td class='soustitre2'>$delai jours</td>
					<td class='soustitre2'><a href='?methode=accepte&mission=$code&faction=$faction'>Relever&nbsp;!</a></td></tr>";
            }
            echo '</table>';
        }
        if (!$date_mission_ok)
        {
            echo "<div style='margin: 10px'>Nous n’avons aucune mission à vous proposer. Revenez dans quelques jours !</div>";
        }

        if ($pfac_rang_numero > 0)
            echo "<hr />Souhaitez-vous <a href='factions.php?methode=demission&faction=$faction'>démissionner de $faction_nom</a> ?";
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
