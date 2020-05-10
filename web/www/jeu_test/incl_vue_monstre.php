<script type="text/javascript">
    var afficheFamiliers = true;
    var afficheMonstresCoterie = true;
</script>
<table width="100%" cellspacing="2" cellapdding="2" id="tableMonstres">

    <?php
    $parm                     = new parametres();
    $compte                   = new compte;
    $compte                   = $verif_connexion->compte;
    $perso                    = new perso;
    $perso                    = $verif_connexion->perso;
    $marquerQuatriemes        = $compte->is_admin_monstre();
    $req_malus_desorientation =
        " select valeur_bonus(perso_cod, 'DES') as desorientation from perso where perso_cod = $perso_cod";
    $stmt                     = $pdo->query($req_malus_desorientation);
    $result                   = $stmt->fetch();
    if ($result['desorientation'] <= 0)
        $desorientation = false;
    else
        $desorientation = true;
    $combat_groupe = $parm->getparm(56);
    $pa            = $perso->perso_pa;
    $pa_n          = $perso->get_pa_attaque();

    $req_vue_joueur = "select trajectoire_vue($pos_cod,pos_cod) as traj, perso_tangible, perso_description, perso_desc_long, perso_nom,
		pos_x, pos_y, pos_etage, race_nom, distance(pos_cod,$pos_cod) as distance, perso_cod, perso_sex, perso_pv,
		perso_pv_max, pos_cod, is_surcharge(perso_cod,$perso_cod) as surcharge, pgroupe_groupe_cod, l1.lock_nb_tours as lock1,
		l2.lock_nb_tours as lock2, coalesce(compt_monstre, 'N') as compt_monstre, coalesce(compt_cod, -1) as compt_cod, perso_dirige_admin,
		perso_type_perso,
		case when triplette.triplette_perso_cod IS NOT NULL THEN 1 ELSE 0 END as triplette
	FROM perso
	INNER JOIN perso_position ON ppos_perso_cod = perso_cod
	INNER JOIN positions ON pos_cod = ppos_pos_cod
	INNER JOIN race ON perso_race_cod = race_cod
	LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1
	LEFT OUTER JOIN lock_combat l1 ON l1.lock_cible = perso_cod AND l1.lock_attaquant = $perso_cod
	LEFT OUTER JOIN lock_combat l2 ON l2.lock_cible = $perso_cod AND l2.lock_attaquant = perso_cod
	LEFT OUTER JOIN perso_compte ON pcompt_perso_cod = perso_cod
	LEFT OUTER JOIN compte ON compt_cod = pcompt_compt_cod 
    LEFT OUTER JOIN (
                    select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso_familier on pfam_perso_cod=pcompt_perso_cod  join perso on perso_cod=pfam_familier_cod where compt_cod=$compt_cod and perso_actif='O'
                ) as triplette on triplette_perso_cod = perso_cod
	WHERE pos_etage = $etage
		and perso_type_perso in (2,3)
		and pos_x between ($x-$distance_vue) and ($x+$distance_vue) 
		and pos_y between ($y-$distance_vue) and ($y+$distance_vue) 
		and perso_actif = 'O' 
		and perso_cod != $perso_cod  
	ORDER BY distance, pos_x, pos_y";
    //echo "<!--" . $req_vue_joueur . "-->";

    $stmt             = $pdo->query($req_vue_joueur);
    $nb_joueur_en_vue = $stmt->rowCount();

    $nb_colonnes = ($marquerQuatriemes) ? 7 : 6;

    ?>
    <tr>
        <td colspan="<?php echo $nb_colonnes; ?>" class="soustitre">
            <div class="soustitre">Monstres</div>
        </td>
    </tr>

    <tr style="height: 30px;">
        <td class="soustitre2" colspan="8"><strong>Filtres:&nbsp;&nbsp;</strong>
            <input id="tableMonstres-col" type="hidden" value="2">
            <input id="tableMonstres-filtre-perso" type="text" size="20"
                   onkeyup="filtre_table_search('tableMonstres');">
            &nbsp;&nbsp;<strong>Limiter aux:&nbsp;&nbsp;</strong>
            &nbsp;&nbsp;<input name="tableMonstres-filtre-type" value="3" type="radio"
                               onChange="filtre_table_search('tableMonstres');">&nbsp;<em>Familiers <span
                        id="ft-familier"></span></em>
            &nbsp;&nbsp;<input name="tableMonstres-filtre-type" value="2"
                               type="radio" <?php if ($tab_vue != 5) echo "checked"; ?>
                               onChange="filtre_table_search('tableMonstres');">&nbsp;<em>Monstres <span
                        id="ft-monstre"></span></em>
            &nbsp;&nbsp;<input name="tableMonstres-filtre-type" value="0" type="radio"
                               onChange="filtre_table_search('tableMonstres');">&nbsp;<em>Partisans <span
                        id="ft-partisan"></span></em>
            &nbsp;&nbsp;<input name="tableMonstres-filtre-type" value="-1"
                               type="radio" <?php if ($tab_vue == 5) echo "checked"; ?>
                               onChange="filtre_table_search('tableMonstres');">&nbsp;<em>Sans limites</span></em>
        </td>
    </tr>

    <td class="soustitre2" width="50"><strong>Dist.</strong></td>
    <?php if ($marquerQuatriemes) echo '<td class="soustitre2"><strong>Contrôle</strong></td>'; ?>
    <td class="soustitre2"><strong>Nom</strong></td>
    <td class="soustitre2"><strong>Race</strong></td>
    <td class="soustitre2">
        <div style="text-align:center;"><strong>X</strong></div>
    </td>
    <td class="soustitre2">
        <div style="text-align:center;"><strong>Y</strong></div>
    </td>
    <td></td>
    </tr>
    <?php
    if ($nb_joueur_en_vue != 0)
    {
        $i          = 0;
        $lieuRefuge = $perso->is_refuge();
        $row        = 3;

        while ($result = $stmt->fetch())
        {
            if ($result['traj'] == 1)
            {
                //Repérage du type de contrôle
                $ia            = ($result['perso_dirige_admin'] == 'O') ? 'Hors IA' : 'Sous IA';
                $type_controle = ($result['compt_monstre'] == 'O') ? ' (Compte monstre)' : ' (Compte joueur)';
                if ($result['compt_cod'] == -1)
                    $type_controle = '';

                $lock_combat      =
                    ($result['lock1'] > 0 || $result['lock2'] > 0) ? '<img src="http://www.jdr-delain.net/images/attaquer.gif" title="Vous êtes en combat avec ce monstre." /> ' : '';
                $meme_coterie     =
                    ($coterie > 0 && $result['pgroupe_groupe_cod'] == $coterie) ? '<img src="http://www.jdr-delain.net/images/guilde.gif" title="Ce monstre appartient à la même coterie que vous." /> ' : '';
                $is_tangible      = $result['perso_tangible'];
                $aff_tangible     = $palbable[$is_tangible];
                $niveau_blessures = '';
                $pv               = $result['perso_pv'];
                $num_perso        = $result['perso_cod'];
                $pv_max           = $result['perso_pv_max'];
                if ($pv / $pv_max < 0.75)
                {
                    $niveau_blessures = ' - <strong>' . $tab_blessures[0] . '</strong>';
                }
                if ($pv / $pv_max < 0.5)
                {
                    $niveau_blessures = ' - <strong>' . $tab_blessures[1] . '</strong>';
                }
                if ($pv / $pv_max < 0.25)
                {
                    $niveau_blessures = ' - <strong>' . $tab_blessures[2] . '</strong>';
                }
                if ($pv / $pv_max < 0.15)
                {
                    $niveau_blessures = ' - <strong>' . $tab_blessures[3] . '</strong>';
                }
                $nom = $result['perso_nom'];
                if ($result['perso_desc_long'] != NULL or $result['perso_desc_long'] != "")
                    $nom .= '<strong> *</strong>';
                if ($result['distance'] <= $portee)
                {
                    $attaquable = 1;
                } else
                {
                    $attaquable = 0;
                }
                if ($pa < $pa_n)
                {
                    $attaquable = 0;
                }
                $perso2 = new perso;
                $perso2->charge($num_perso);
                if ($lieuRefuge || $perso2->is_refuge())
                {
                    $attaquable = 0;
                }
                $desc  = nl2br(htmlspecialchars(str_replace('\'', '’', $result['perso_description'])));
                $desc  = str_replace("\r", "", $desc);
                $desc  = str_replace("%", "pourcent", $desc);
                $style = "soustitre2";
                if ($result['surcharge'] == 1)
                {
                    $style = "surcharge1";
                }
                if ($result['surcharge'] == 2)
                {
                    $style = "surcharge2";
                }
                if ($combat_groupe == 0)
                {
                    $style = "soustitre2";
                }
                $ch_style =
                    'onMouseOver="changeStyles(\'cell' . $result['pos_cod'] . '\',\'lmonstre' . $result['perso_cod'] . '\',\'vu\',\'surligne\');" onMouseOut="changeStyles(\'cell' . $result['pos_cod'] . '\',\'lmonstre' . $result['perso_cod'] . '\',\'pasvu\',\'' . $style . '\');"';

                $cdata = "";
                $cdata .= "data-partisans='" . (($coterie > 0 && $result['pgroupe_groupe_cod'] == $coterie) || ($result['triplette'] == 1) ? "O" : "N") . "' ";
                $cdata .= "data-type='" . ($result['perso_type_perso']) . "' ";
                echo '<tr id="row-' . $row . '" ' . $cdata . '>
				<td ' . $ch_style . '><div style="text-align:center;">' . $result['distance'] . '</div></td>';

                if ($marquerQuatriemes)
                    echo '<td ' . $ch_style . '><div style="text-align:center;">' . $ia . $type_controle . '</div></td>';

                echo '<td ' . $ch_style . 'id="lmonstre' . $result['perso_cod'] . '" class="' . $style . '">' . $lock_combat . $meme_coterie . '<strong><a href="visu_desc_perso.php?visu=' . $result['perso_cod'] . '">' . $nom . '</a>
					</strong>' . $aff_tangible . $niveau_blessures . '<br><span style="font-size:8pt">' . $desc . '</span></td>
				<td ' . $ch_style . '>' . $result['race_nom'] . '</td>
				<td ' . $ch_style . ' nowrap><div style="text-align:center;">' . $result['pos_x'] . '</div></td>
				<td ' . $ch_style . ' nowrap><div style="text-align:center;">' . $result['pos_y'] . '</div></td><td>';
                if (!$desorientation)
                {
                    if ($attaquable == 1)
                    {
                        echo '<a href="javascript:document.visu_evt2.cible.value=' . $result['perso_cod'] . ';document.visu_evt2.action=\'action.php\';document.visu_evt2.submit();">Attaquer !</a>';
                    }
                }
                echo '</td></tr>';
                $row++;
            }
        }
    } else
    {
        ?>
        <td colspan="5" class="soustitre2">Aucun monstre en vue</td>
        <?php
    }


    ?>
</table>
<script type="text/javascript">
    filtre_table_search('tableMonstres'); // on filtre sur les monstres par défaut
</script>
