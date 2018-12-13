<script type="text/javascript">
    var affichePersosCoterie = true;
</script>
<?php $marquerQuatriemes = $db->is_admin_monstre($compt_cod);
$param = new parametres();
$req_malus_desorientation = " select valeur_bonus($perso_cod, 'DES') as desorientation";
$db->query($req_malus_desorientation);
$db->next_record();
if ($db->f("desorientation") == 0)
{
    $desorientation = false;
}
else
{
    $desorientation = true;
}
$combat_groupe = $param->getparm(56);
$req = "select perso_pa from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
$pa = $db->f("perso_pa");
$pa_n = $db->get_pa_attaque($perso_cod);

$coterie = 1*$coterie;  // Convertion en entier et mise à zero sir vide!
// On recherche les autres joueurs en vue
$req_vue_joueur = "select perso_crapaud, trajectoire_vue($pos_cod,pos_cod) as traj, perso_tangible, perso_nom, pos_x, pos_y, 
				pos_etage, race_nom, distance(pos_cod,$pos_cod) as distance, perso_sex, perso_cod, perso_pv, perso_pv_max, perso_description,
				perso_desc_long, pos_cod,
				case when pgroupe_montre_pv = 1 or triplette_perso_cod is not null then etat_perso(perso_cod) else 'masqué' end as perso_pv,
				case when pgroupe_montre_bonus = 1 or triplette_perso_cod is not null  then perso_bonus(perso_cod) else 'masqué' end as perso_bonus,
				case when pgroupe_montre_pa = 1 or triplette_perso_cod is not null then perso_pa::text else 'masqué' end as perso_pa,
                case when pgroupe_montre_dlt = 1 or triplette_perso_cod is not null  then to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') else 'masqué' end as perso_dlt,
                case when pgroupe_montre_dlt = 1 or triplette_perso_cod is not null  then dlt_passee(perso_cod)::text else 'masqué' end as perso_dlt_passee,
				is_surcharge(perso_cod,$perso_cod) as surcharge, perso_pnj, perso_mortel, pgroupe_groupe_cod, l1.lock_nb_tours as lock1, l2.lock_nb_tours as lock2
		FROM perso
		INNER JOIN perso_position ON ppos_perso_cod = perso_cod
		INNER JOIN positions ON pos_cod = ppos_pos_cod
		INNER JOIN race ON perso_race_cod = race_cod
		LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1 and pgroupe_groupe_cod=$coterie
		LEFT OUTER JOIN lock_combat l1 ON l1.lock_cible = perso_cod AND l1.lock_attaquant = $perso_cod
		LEFT OUTER JOIN lock_combat l2 ON l2.lock_cible = $perso_cod AND l2.lock_attaquant = perso_cod
		LEFT OUTER JOIN (
                        select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso on perso_cod=pcompt_perso_cod where compt_cod=$compt_cod and perso_actif='O'
                        union
                        select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso_familier on pfam_perso_cod=pcompt_perso_cod  join perso on perso_cod=pfam_familier_cod where compt_cod=$compt_cod and perso_actif='O'
                    ) as triplette on triplette_perso_cod = perso_cod
		WHERE pos_etage = $etage 
			and perso_type_perso in  (1,3)
			and pos_x between ($x-$distance_vue) and ($x+$distance_vue) 
			and pos_y between ($y-$distance_vue) and ($y+$distance_vue) 
			and perso_actif = 'O'
			--and perso_cod != $perso_cod 
			and (pgroupe_groupe_cod is not NULL or triplette_perso_cod is not NULL) 
		order by distance,pos_x,pos_y,perso_type_perso,perso_nom ";

$db->query($req_vue_joueur);
$nb_joueur_en_vue = $db->nf();

?>

<table width="100%" cellspacing="2" cellapdding="2" id="tablePersos">

    <tr>
        <td colspan="9" class="soustitre">
            <div class="soustitre">Partisans</div>
        </td>
    </tr>
    <?php if ($marquerQuatriemes)
    {
        echo '<tr><td colspan="9" class="soustitre2"><i>Une astérisque * à côté du O de 4ème perso signifie que toute mort sera définitive pour ce personnage</td></tr>';
    } ?>
    <tr>
        <td class="soustitre2" width="50"><strong>Dist.</strong></td>
        <?php if ($marquerQuatriemes)
        {
            echo '<td class="soustitre2"><strong>4ème</strong></td>';
        } ?>
        <td class="soustitre2"><strong>Nom</strong></td>
        <td class="soustitre2"><strong>DLT</strong></td>
        <td class="soustitre2"><strong>PA</strong></td>
        <td class="soustitre2"><strong>Bonus/Malus</strong></td>
        <td class="soustitre2"><strong>Santé</strong></td>
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
        // on boucle sur les joueurs "visibles"
        $i = 0;
        while ($db->next_record())
        {
            if ($db->f("traj") == 1)
            {
                //Repérage des 4èmes persos
                $quatrieme = ($db->f("perso_pnj") == 2) ? 'O' : 'N';
                $mortel = ($db->f("perso_mortel") == 'O') ? ' *' : '';

                $lock_combat = ($db->f("lock1") > 0 || $db->f("lock2") > 0) ? '<img src="http://www.jdr-delain.net/images/attaquer.gif" title="Vous êtes en combat avec cet aventurier." /> ' : '';
                $meme_coterie = ($coterie > 0 && $db->f("pgroupe_groupe_cod") == $coterie) ? '<img src="http://www.jdr-delain.net/images/guilde.gif" title="Cet aventurier appartient à la même coterie que vous." /> ' : '';

                $is_tangible = $db->f("perso_tangible");
                $aff_tangible = $palbable[$is_tangible];

                $niveau_blessures = '';
                $pv = $db->f("perso_pv");
                $num_perso = $db->f("perso_cod");

                $perso_dlt = "";
                if($db->f('perso_dlt_passee') == 1) $perso_dlt.= "<strong>";
                $perso_dlt .= $db->f('perso_dlt');
                if($db->f('perso_dlt_passee') == 1) $perso_dlt.= "</strong>";

                $perso_pa = $db->f('perso_pa');
                $perso_bonus = $db->f('perso_bonus');

                $nom = str_replace("\\", " ", $db->f("perso_nom"));
                $nom = str_replace("'", "’", $nom);
                $nom = str_replace("/", " ", $nom);
                $desc = nl2br(htmlspecialchars(str_replace('\'', '’', $db->f("perso_description"))));
                $desc = str_replace("\r", "", $desc);
                $desc = str_replace("%", "pourcent", $desc);

                if ($db->f("perso_crapaud") == 1)
                {
                    $crapaud = '';
                    if ($db->f("distance") == 0)
                    {
                        $crapaud .= '<a href="action.php?methode=embr&cible=' . $db->f("perso_cod") . '">L’embrasser ? (2 PA)</a>';
                    }
                    $nom = 'Aventurier transformé en crapaud !';
                }
                else
                {
                    $crapaud = '';
                }

                if ($db->f("perso_desc_long") != NULL or $db->f("perso_desc_long") != "")
                {
                    $nom .= '<strong> *</strong>';
                }

                $req = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau ";
                $req = $req . "where dper_perso_cod = $num_perso ";
                $req = $req . "and dper_dieu_cod = dieu_cod ";
                $req = $req . "and dper_niveau = dniv_niveau ";
                $req = $req . "and dniv_dieu_cod = dieu_cod ";
                $req = $req . "and dniv_niveau >= 1 ";
                $db2->query($req);
                if ($db2->nf() != 0)
                {
                    $db2->next_record();
                    $religion = " </strong>(" . $db2->f("dniv_libelle") . " de " . $db2->f("dieu_nom") . ")<strong> ";
                }
                else
                {
                    $religion = '';
                }
                $style = "soustitre2";
                if ($db->f("surcharge") == 1)
                {
                    $style = "surcharge1";
                }
                if ($db->f("surcharge") == 2)
                {
                    $style = "surcharge2";
                }
                if ($combat_groupe == 0)
                {
                    $style = "soustitre2";
                }
                $ch_style = 'onMouseOver="changeStyles(\'cell' . $db->f("pos_cod") . '\',\'lperso' . $db->f("perso_cod") . '\',\'vu\',\'surligne\');" onMouseOut="changeStyles(\'cell' . $db->f("pos_cod") . '\',\'lperso' . $db->f("perso_cod") . '\',\'pasvu\',\'' . $style . '\');"';
                echo '<tr>
				<td ' . $ch_style . '><div style="text-align:center;">' . $db->f("distance") . '</div></td>';
                if ($marquerQuatriemes)
                {
                    echo '<td ' . $ch_style . '><div style="text-align:center;">' . $quatrieme . $mortel . '</div></td>';
                }
                $nom_perso =  ($db->f("perso_cod") != $perso_cod) ? $nom : '<span style="color:black;">' . $nom . '</span></a>';
                echo '<td ' . $ch_style . 'id="lperso' . $db->f("perso_cod") . '" class="' . $style . '">' . $lock_combat . $meme_coterie . '<strong><a href="visu_desc_perso.php?visu=' . $db->f("perso_cod") . '">' . $nom_perso . '</a>' . $religion . $niveau_blessures . '</strong>' . $aff_tangible . $crapaud . '<br><span style="font-size:8pt">' . $desc . '</span>
				</td>
				<td ' . $ch_style . '>' . $perso_dlt . '</td>
				<td ' . $ch_style . '>' .$perso_pa . '</td>
				<td ' . $ch_style . '>' . $perso_bonus . '</td>
				<td ' . $ch_style . '>' . $pv . '</td>
				<td ' . $ch_style . ' nowrap><div style="text-align:center;">' . $db->f("pos_x") . '</div></td>
				<td ' . $ch_style . ' nowrap><div style="text-align:center;">' . $db->f("pos_y") . '</div></td>
				<td ' . $ch_style . '><td>';
                if (!$desorientation)
                {
                    if ($attaquable == 1)
                    {
                        echo '<a href="javascript:document.visu_evt2.cible.value=' . $db->f("perso_cod") . ';document.visu_evt2.action=\'action.php\';document.visu_evt2.submit();">Attaquer !</a>';
                    }
                }
                echo '</td></tr>';
            }
        }
    }
    else
    {
        ?>
        <td colspan="8" class="soustitre2">Aucun joueur en vue</td>
        <?php
    }
    ?>
</table>
