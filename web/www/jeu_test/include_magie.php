<form name="valide_sort" method="post" action="action.php">
    <input type="hidden" name="methode" value="magie">
    <input type="hidden" name="sort_cod" value="<?php echo $sort_cod; ?>">
    <input type="hidden" name="objsort_cod" value="<?php echo $objsort_cod; ?>">
    <input type="hidden" name="cible" value="0">
    <input type="hidden" name="type_lance" value="<?php echo $type_lance ?>">
    <?php
    include "../includes/constantes.php";

    $req_sort = "select sort_distance from sorts where sort_cod = $sort_cod ";
    $db->query($req_sort);
    $db->next_record();
    $dist_sort = $db->f("sort_distance");

    // On cherche la coterie du perso
    $coterie_perso_lanceur = -1;
    $req_coterie = "select pgroupe_groupe_cod
	from groupe_perso
	where pgroupe_perso_cod = $perso_cod and pgroupe_statut = 1";
    $db->query($req_coterie);
    if ($db->next_record())
        $coterie_perso_lanceur = $db->f("pgroupe_groupe_cod");

    // on cherche la position du perso
    $req_pos = "select ppos_pos_cod, pos_etage, pos_x, pos_y, distance_vue($perso_cod) as distance_vue, perso_nom, perso_type_perso, perso_pv, perso_pv_max, dper_dieu_cod, perso_bonus(perso_cod), perso_pa, to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as perso_dlt, dlt_passee(perso_cod)::text as perso_dlt_passee 
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod 
	inner join positions on pos_cod = ppos_pos_cod 
	left outer join dieu_perso on dper_perso_cod = perso_cod 
	where perso_cod = $perso_cod";
    $db->query($req_pos);
    $db->next_record();
    $position = $db->f("ppos_pos_cod");
    $etage = $db->f("pos_etage");
    $x = $db->f("pos_x");
    $y = $db->f("pos_y");
    $pos_cod = $db->f("ppos_pos_cod");
    $distance_vue = $db->f("distance_vue");
    $perso_nom = $db->f("perso_nom");
    $dieu_perso = $db->f("dper_dieu_cod");
    $perso_bonus = $db->f("perso_bonus");
    $perso_pa = $db->f("perso_pa");
    $perso_dlt = $db->f("perso_dlt_passee") == 1 ? "<strong>{$db->f("perso_dlt")}</strong>" : $db->f("perso_dlt");
    $type_perso = $db->f("perso_type_perso");

    $pv = $db->f("perso_pv");
    $pv_max = $db->f("perso_pv_max");
    $niveau_blessures = '';
    if ($pv / $pv_max < 0.75) {
        $niveau_blessures = ' - ' . $tab_blessures[0];
    }
    if ($pv / $pv_max < 0.5) {
        $niveau_blessures = ' - ' . $tab_blessures[1];
    }
    if ($pv / $pv_max < 0.25) {
        $niveau_blessures = ' - ' . $tab_blessures[2];
    }
    if ($pv / $pv_max < 0.15) {
        $niveau_blessures = ' - ' . $tab_blessures[3];
    }


    if ($distance_vue > $dist_sort) {
        $distance_vue = $dist_sort;
    }

    ?>

    <script src="/scripts/filtres.js?v20190416" type="text/javascript"></script>

    <p>Choisissez la cible du sort :<br>
        <table id="choix-sort">
            <tr style="height: 30px;"><td class="soustitre2" colspan="8"><strong>Filtres:&nbsp;&nbsp;</strong>
                    <input id="choix-sort-col" type="hidden" value="1">
                    <input id="choix-sort-filtre-perso" type="text" size="40" onkeyup="filtre_table_search('choix-sort');">
                    &nbsp;&nbsp;<br><strong>Limiter aux:&nbsp;&nbsp;</strong>
                    &nbsp;&nbsp;<input name="choix-sort-filtre-type" value="3" type="radio" onChange="filtre_table_search('choix-sort');">&nbsp;<em>Familiers <span id="ft-familier"></span></em>
                    &nbsp;&nbsp;<input name="choix-sort-filtre-type" value="2" type="radio" onChange="filtre_table_search('choix-sort');">&nbsp;<em>Monstres <span id="ft-monstre"></span></em>
                    &nbsp;&nbsp;<input name="choix-sort-filtre-type" value="1" type="radio" onChange="filtre_table_search('choix-sort');">&nbsp;<em>Aventuriers <span id="ft-aventurier"></span></em>
                    &nbsp;&nbsp;<input name="choix-sort-filtre-type" value="0" type="radio" onChange="filtre_table_search('choix-sort');">&nbsp;<em>Partisans <span id="ft-partisan"></span></em>
                    &nbsp;&nbsp;<input name="choix-sort-filtre-type" value="-1" type="radio" checked onChange="filtre_table_search('choix-sort');">&nbsp;<em>Sans limites</span></em>
                </td>
            </tr>
            <tr>
                <td class="soustitre2">
    <p><strong>Nom</strong></p></td>
    <td class="soustitre2"><p><strong>Race</strong></p></td>
    <td class="soustitre2"><p><strong>X</strong></p></td>
    <td class="soustitre2"><p><strong>Y</strong></p></td>
    <td class="soustitre2"><p><strong>Distance</strong></p></td>
    <td class="soustitre2"><p><strong>Bonus/Malus</strong></p></td>
    <td class="soustitre2"><p><strong>PA</strong></p></td>
    <td class="soustitre2"><p><strong>DLT</strong></p></td>
    </tr>

    <?php

    $row = 2 ;
    $cdata = "";
    $cdata.="data-partisans='O' ";
    $cdata.="data-type='".($type_perso)."' ";
    if ($soi_meme == 'O') {
        echo "<tr id='row-{$row}' {$cdata}>
			<td class=\"soustitre2\" style=\"background-color:darkseagreen;\" colspan=\"2\"><strong>
			<a href=\"javascript:document.valide_sort.cible.value=" . $perso_cod . ";document.valide_sort.submit();\">
			" . $perso_nom . "</a></strong><em> (vous-même<strong>" . $niveau_blessures . "</strong>)</em></td>
			<td style=\"background-color:darkseagreen; text-align:center;\">" . $x . "</td>
			<td style=\"background-color:darkseagreen; text-align:center;\">" . $y . "</td>
			<td style=\"background-color:darkseagreen; text-align:center;\">0</td>
			<td style=\"background-color:darkseagreen; text-align:left;\">$perso_bonus</td>
			<td style=\"background-color:darkseagreen; text-align:left;\">$perso_pa</td>
			<td style=\"background-color:darkseagreen; text-align:left;\">$perso_dlt</td>
		</tr>";
        $row++;
    }

    $add_dieu = "";
    if ($soi_meme == 'O' and $dieu_perso != NULL and $sort_dieu == 'mg' and $sort_joueur == 'N') {
        $type_cible = $type_cible . ",1,2,3";
        $add_dieu = "and perso_cod in (select dper_perso_cod from dieu_perso 
			where dper_dieu_cod = " . $dieu_perso . "
				and dper_perso_cod != $perso_cod )";
    }

    $req_vue_joueur = "select  trajectoire_vue($pos_cod, pos_cod) as traj, perso_nom, pos_x, pos_y, pos_etage, race_nom,
		distance($position, pos_cod) as distance, pos_cod, perso_cod, perso_type_perso, perso_pv, perso_pv_max,
		case when groupe_perso.pgroupe_perso_cod IS NOT NULL THEN 1 ELSE 0 END as meme_coterie,
		case when triplette.triplette_perso_cod IS NOT NULL THEN 1 ELSE 0 END as triplette,
        case when (triplette_perso_cod IS NOT NULL OR pgroupe_montre_bonus=1) then perso_bonus(perso_cod)
            when (groupe_perso.pgroupe_perso_cod IS NOT NULL and pgroupe_montre_bonus=0) then 'masqué'
            else NULL end perso_bonus,
        case when (triplette_perso_cod IS NOT NULL OR pgroupe_montre_pa=1) then perso_pa::text
            when (groupe_perso.pgroupe_perso_cod IS NOT NULL and pgroupe_montre_pa=0) then 'masqué'
            else NULL end perso_pa,
        case when (triplette_perso_cod IS NOT NULL OR pgroupe_montre_dlt=1) then to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss')
            when (groupe_perso.pgroupe_perso_cod IS NOT NULL and pgroupe_montre_dlt=0) then 'masqué'
            else NULL end perso_dlt,
        dlt_passee(perso_cod)::text as perso_dlt_passee,
        coalesce(pgroupe_texte,'') palimpseste
	from perso
	inner join perso_position on ppos_perso_cod = perso_cod 
	inner join positions on pos_cod = ppos_pos_cod 
	inner join race on race_cod = perso_race_cod
	LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1 and pgroupe_groupe_cod=$coterie_perso_lanceur
    LEFT OUTER JOIN (
                    select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso on perso_cod=pcompt_perso_cod where compt_cod=$compt_cod and perso_actif='O'
                    union
                    select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso_familier on pfam_perso_cod=pcompt_perso_cod  join perso on perso_cod=pfam_familier_cod where compt_cod=$compt_cod and perso_actif='O'
                ) as triplette on triplette_perso_cod = perso_cod
	where pos_x between ($x-$distance_vue) and ($x+$distance_vue)
		and pos_y between ($y-$distance_vue) and ($y+$distance_vue)
		and pos_etage = $etage
		and perso_cod != $perso_cod
		and perso_actif = 'O'
		and perso_type_perso in ($type_cible) ";
    if ($aggressif == 'O') {
        $req_vue_joueur = $req_vue_joueur . "and perso_tangible = 'O'
			and not exists
				(select 1 from lieu, lieu_position
				where lpos_pos_cod = ppos_pos_cod
					and lpos_lieu_cod = lieu_cod
					and lieu_refuge = 'O')
		order by perso_type_perso desc,";
    } else {
        $req_vue_joueur = $req_vue_joueur . $add_dieu . " order by perso_type_perso asc,";
    }
    $req_vue_joueur = $req_vue_joueur . "distance, pos_x, pos_y, perso_nom ";

    $db->query($req_vue_joueur);
    while ($db->next_record()) {
        if ($db->f("traj") == 1) {
            $pv = $db->f("perso_pv");
            $pv_max = $db->f("perso_pv_max");
            $niveau_blessures = '';
            if ($pv / $pv_max < 0.75) {
                $niveau_blessures = ' - ' . $tab_blessures[0];
            }
            if ($pv / $pv_max < 0.5) {
                $niveau_blessures = ' - ' . $tab_blessures[1];
            }
            if ($pv / $pv_max < 0.25) {
                $niveau_blessures = ' - ' . $tab_blessures[2];
            }
            if ($pv / $pv_max < 0.15) {
                $niveau_blessures = ' - ' . $tab_blessures[3];
            }
            $type_perso = $db->f("perso_type_perso");

            $script_choix = "javascript:document.valide_sort.cible.value=" . $db->f("perso_cod") . ";document.valide_sort.submit();";
            if ($aggressif == 'O' && $db->f("meme_coterie") == 1)
                $script_choix = "javascript:if (confirm('Vous vous apprêtez à lancer un sort offensif sur un membre de votre coterie. Êtes-vous sûr de vouloir le faire ?')) { document.valide_sort.cible.value=" . $db->f("perso_cod") . ";document.valide_sort.submit();}";
            $perso_pa = $db->f('perso_pa');
            if (($db->f('perso_dlt_passee') == 1) && ($perso_pa != "") && ($perso_pa != "masqué")) $perso_pa = "<strong>{$perso_pa}</strong>";
            $perso_dlt = $db->f('perso_dlt');
            if (($db->f('perso_dlt_passee') == 1) && ($perso_dlt != "") && ($perso_dlt != "masqué")) $perso_dlt = "<strong>{$perso_dlt}</strong>";

            $perso_bonus = $db->f("perso_bonus"); // le reste n'a pas été approuvé => $db->f("perso_dlt_passee")==0 ? $db->f("perso_bonus") : ( $db->f("perso_bonus")=="" ? "" : "<strong>".$db->f("perso_bonus")."</strong>" ) ;
            $perso_style = $perso_bonus == NULL ? "" : ($db->f("triplette") == 1 ? "background-color:#CCC;" : "background-color:#BA9C6C;");

            $cdata = "";
            $cdata.="data-partisans='".(($db->f("meme_coterie")== 1) || ($db->f("triplette")== 1) ? "O" : "N")."' ";
            $cdata.="data-type='".($type_perso)."' ";

            $onclick = "" ;
            $iconPalimp = "" ;
            if ($db->f("palimpseste") != "")
            {
                $onclick="onclick=\"toggle_details(event, 'detail-{$row}');\"";
                $iconPalimp="<span style='float: right;'><img src='/images/guilde.gif'></span>";
            }

            echo "<tr id='row-{$row}' {$cdata} {$onclick}>
				<td class=\"soustitre2\" style=\"{$perso_style}\"><strong><a href=\"$script_choix\">" . $db->f("perso_nom") . "</a></strong> <em>(" . $perso_type_perso[$type_perso] . "<strong>" . $niveau_blessures . "</strong>)</em>{$iconPalimp}</td>
				<td style=\"{$perso_style}\">" . $db->f("race_nom") . "</td>
				<td style=\"{$perso_style} text-align:center;\">" . $db->f("pos_x") . "</td>
				<td style=\"{$perso_style} text-align:center;\">" . $db->f("pos_y") . "</td>
				<td style=\"{$perso_style} text-align:center;\">" . $db->f("distance") . "</td>
				<td style=\"{$perso_style} text-align:left;\">" . $perso_bonus . "</td>
				<td style=\"{$perso_style} text-align:left;\">" . $perso_pa . "</td>
				<td style=\"{$perso_style} text-align:left;\">" . $perso_dlt . "</td>
			</tr>";
            if ($db->f("palimpseste") != "")
            {
                echo "<tr id='detail-{$row}' style='{$perso_style} display:none'><td colspan='8' style='padding-bottom: 15px; font-size: x-small; color:white;'><em>" . $db->f("palimpseste") . "</em></td></tr>";
            }
            $row++;
        }
    } ?>
    </table>
</form>
