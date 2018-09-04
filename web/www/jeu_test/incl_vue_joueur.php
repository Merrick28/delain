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


// On recherche les autres joueurs en vue
$req_vue_joueur = "select perso_crapaud, trajectoire_vue($pos_cod,pos_cod) as traj, pcompt_compt_cod, perso_tangible, perso_nom, pos_x, pos_y, 
				pos_etage, race_nom, distance(pos_cod,$pos_cod) as distance, perso_sex, perso_cod, perso_pv, perso_pv_max, perso_description,
				perso_desc_long, pos_cod, f_vue_renommee(perso_cod) as renommee, get_karma(perso_kharma) as karma, 
				is_surcharge(perso_cod,$perso_cod) as surcharge, perso_pnj, perso_mortel, pgroupe_groupe_cod, l1.lock_nb_tours as lock1, l2.lock_nb_tours as lock2
		FROM perso
		INNER JOIN perso_position ON ppos_perso_cod = perso_cod
		INNER JOIN positions ON pos_cod = ppos_pos_cod
		INNER JOIN race ON perso_race_cod = race_cod
		LEFT JOIN perso_compte ON perso_cod = pcompt_perso_cod
		LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1
		LEFT OUTER JOIN lock_combat l1 ON l1.lock_cible = perso_cod AND l1.lock_attaquant = $perso_cod
		LEFT OUTER JOIN lock_combat l2 ON l2.lock_cible = $perso_cod AND l2.lock_attaquant = perso_cod
		WHERE pos_etage = $etage 
			and perso_type_perso = 1 
			and pos_x between ($x-$distance_vue) and ($x+$distance_vue) 
			and pos_y between ($y-$distance_vue) and ($y+$distance_vue) 
			and perso_actif = 'O'
			and perso_cod != $perso_cod 
		order by distance,pos_x,pos_y,perso_nom ";

$db->query($req_vue_joueur);
$nb_joueur_en_vue = $db->nf();

?>

<table width="100%" cellspacing="2" cellapdding="2" id="tablePersos">

    <tr>
        <td colspan="9" class="soustitre">
            <div class="soustitre">Aventuriers</div>
        </td>
    </tr>
    <tr>
        <td colspan="9" class="soustitre2">Afficher/masquer : <span style="cursor:pointer;"
                                                                    onclick="afficheMasque(contient, new Array(1, 'guilde.gif'), 'tablePersos', affichePersosCoterie); affichePersosCoterie = !affichePersosCoterie;">la coterie</span>.
        </td>
    </tr>
    <?php if ($marquerQuatriemes)
    {
        echo '<tr><td colspan="9" class="soustitre2"><i>Une astérisque * à côté du O de 4ème perso signifie que toute mort sera définitive pour ce personnage</td></tr>';
    } ?>
    <tr>
        <td class="soustitre2" width="50"><b>Dist.</b></td>
        <?php if ($marquerQuatriemes)
        {
            echo '<td class="soustitre2"><b>4ème</b></td>';
        } ?>
        <td class="soustitre2"><b>Nom</b></td>
        <td class="soustitre2"><b>Guilde</b></td>
        <td class="soustitre2"><b>Race</b></td>
        <td class="soustitre2"><b>Renommée</b></td>
        <td class="soustitre2"><b>Karma</b></td>
        <td class="soustitre2">
            <div style="text-align:center;"><b>X</b></div>
        </td>
        <td class="soustitre2">
            <div style="text-align:center;"><b>Y</b></div>
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

                $lock_combat = ($db->f("lock1") > 0 || $db->f("lock2") > 0) ? '<img src="http://images.jdr-delain.net/attaquer.gif" title="Vous êtes en combat avec cet aventurier." /> ' : '';
                $meme_coterie = ($coterie > 0 && $db->f("pgroupe_groupe_cod") == $coterie) ? '<img src="http://images.jdr-delain.net/guilde.gif" title="Cet aventurier appartient à la même coterie que vous." /> ' : '';

                $is_tangible = $db->f("perso_tangible");
                $niveau_blessures = '';
                $pv = $db->f("perso_pv");
                $num_perso = $db->f("perso_cod");
                $pv_max = $db->f("perso_pv_max");

                if ($pv / $pv_max < 0.75)
                {
                    $niveau_blessures = ' - ' . $tab_blessures[0];
                }
                if ($pv / $pv_max < 0.5)
                {
                    $niveau_blessures = ' - ' . $tab_blessures[1];
                }
                if ($pv / $pv_max < 0.25)
                {
                    $niveau_blessures = ' - ' . $tab_blessures[2];
                }
                if ($pv / $pv_max < 0.15)
                {
                    $niveau_blessures = ' - ' . $tab_blessures[3];
                }
                $aff_tangible = $palbable[$is_tangible];
                $req_guilde = "select guilde_nom,guilde_cod from guilde,guilde_perso where pguilde_perso_cod = $num_perso and pguilde_valide = 'O' and pguilde_guilde_cod = guilde_cod ";
                $db_guilde = new base_delain;
                $db2->query($req_guilde);
                $nb_guilde = $db2->nf();
                if ($nb_guilde != 0)
                {
                    $db2->next_record();
                    $nom_guilde = $db2->f("guilde_nom");
                    $code_guilde = $db2->f("guilde_cod");
                }
                else
                {
                    $nom_guilde = '';
                    $code_guilde = '0';
                }
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
                    $nom .= '<b> *</b>';
                }
                if ($db->f("distance") <= $portee)
                {
                    if ($db->f("pcompt_compt_cod") != $compt_cod)
                    {
                        $attaquable = 1;
                    }
                    else
                    {
                        $attaquable = 0;
                    }
                }
                else
                {
                    $attaquable = 0;
                }
                if ($db2->is_refuge($perso_cod))
                {
                    $attaquable = 0;
                }
                if ($db2->is_refuge($num_perso))
                {
                    $attaquable = 0;
                }
                if ($pa < $pa_n)
                {
                    $attaquable = 0;
                }
                /*Rajout des persos non attaquable des comptes sittés*/
                $req_joueur_attaquable = "select 1 where $num_perso not in";
                $req_joueur_attaquable = $req_joueur_attaquable . "((select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now())";
                $req_joueur_attaquable = $req_joueur_attaquable . "	union";
                $req_joueur_attaquable = $req_joueur_attaquable . "(select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now())";
                $req_joueur_attaquable = $req_joueur_attaquable . "	union";
                $req_joueur_attaquable = $req_joueur_attaquable . "(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod)";
                $req_joueur_attaquable = $req_joueur_attaquable . "	union";
                $req_joueur_attaquable = $req_joueur_attaquable . "(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod))";
                $db2->query($req_joueur_attaquable);
                if ($db2->nf() == 0)
                {
                    $attaquable = 0;
                }
                /*Fin rajout*/
                if ($is_tangible == 'N')
                {
                    $attaquable = 0;
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
                    $religion = " </b>(" . $db2->f("dniv_libelle") . " de " . $db2->f("dieu_nom") . ")<b> ";
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
                echo '<td ' . $ch_style . 'id="lperso' . $db->f("perso_cod") . '" class="' . $style . '">' . $lock_combat . $meme_coterie . '<b><a href="visu_desc_perso.php?visu=' . $db->f("perso_cod") . '">' . $nom . '</a>' . $religion . $niveau_blessures . '</b>' . $aff_tangible . $crapaud . '<br><span style="font-size:8pt">' . $desc . '</span>
				</td>
				<td ' . $ch_style . '><a href="visu_guilde.php?num_guilde=' . $code_guilde . '">' . $nom_guilde . '</a></td>
				<td ' . $ch_style . '>' . $db->f("race_nom") . '&nbsp;(' . $db->f("perso_sex") . ')</td>
				<td ' . $ch_style . '>' . $db->f("renommee") . '</td>
				<td ' . $ch_style . '>' . $db->f("karma") . '</td>
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
