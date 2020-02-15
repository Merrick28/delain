<script type="text/javascript">
    var affichePersosCoterie = true;
</script>
<?php
require_once "fonctions.php";
$pdo = new bddpdo;
$compte = new compte;
$compte->charge($compt_cod);
$perso = new perso;
$perso->charge($perso_cod);
$marquerQuatriemes        = $compte->is_admin_monstre();
$param                    = new parametres();
$req_malus_desorientation =
    " select valeur_bonus(:perso, 'DES') as desorientation ";

$stmt                     = $pdo->prepare($req_malus_desorientation);
$stmt = $pdo->execute(array(":perso" => $perso_cod),$stmt);

$result                   = $stmt->fetch();
if ($result['desorientation'] == 0)
{
    $desorientation = false;
} else
{
    $desorientation = true;
}
$combat_groupe = $param->getparm(56);
$req           = "select perso_pa from perso where perso_cod = $perso_cod ";
$stmt          = $pdo->query($req);
$result        = $stmt->fetch();
$pa            = $result['perso_pa'];
$pa_n          = $perso->get_pa_attaque();

// On recherche les autres joueurs en vue
$req_vue_joueur = "select perso_crapaud, trajectoire_vue($pos_cod,pos_cod) as traj, pcompt_compt_cod, perso_tangible, perso_nom, pos_x, pos_y,
				pos_etage, race_nom, distance(pos_cod,$pos_cod) as distance, perso_sex, perso_cod, perso_pv, perso_pv_max, perso_description,
				perso_desc_long, pos_cod, f_vue_renommee(perso_cod) as renommee, get_karma(perso_kharma) as karma,
				is_surcharge(perso_cod,$perso_cod) as surcharge, perso_pnj, perso_mortel, pgroupe_groupe_cod, l1.lock_nb_tours as lock1, l2.lock_nb_tours as lock2,
                case when triplette.triplette_perso_cod IS NOT NULL THEN 1 ELSE 0 END as triplette
		FROM perso
		INNER JOIN perso_position ON ppos_perso_cod = perso_cod
		INNER JOIN positions ON pos_cod = ppos_pos_cod
		INNER JOIN race ON perso_race_cod = race_cod
		LEFT JOIN perso_compte ON perso_cod = pcompt_perso_cod
		LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1
		LEFT OUTER JOIN lock_combat l1 ON l1.lock_cible = perso_cod AND l1.lock_attaquant = $perso_cod
		LEFT OUTER JOIN lock_combat l2 ON l2.lock_cible = $perso_cod AND l2.lock_attaquant = perso_cod
        LEFT OUTER JOIN (
                        select perso_cod triplette_perso_cod from compte join perso_compte on pcompt_compt_cod=compt_cod join perso on perso_cod=pcompt_perso_cod where compt_cod=$compt_cod and perso_actif='O'
                     ) as triplette on triplette_perso_cod = perso_cod
		WHERE pos_etage = $etage
			and perso_type_perso = 1
			and pos_x between ($x-$distance_vue) and ($x+$distance_vue)
			and pos_y between ($y-$distance_vue) and ($y+$distance_vue)
			and perso_actif = 'O'
			and perso_cod != $perso_cod
		order by distance,pos_x,pos_y,perso_nom ";

$stmt             = $pdo->query($req_vue_joueur);
$nb_joueur_en_vue = $stmt->rowCount();

?>

<table width="100%" cellspacing="2" cellapdding="2" id="tablePersos">

    <tr>
        <td colspan="9" class="soustitre">
            <div class="soustitre">Aventuriers</div>
        </td>
    </tr>
    <tr style="height: 30px;">
        <td class="soustitre2" colspan="8"><strong>Filtres:&nbsp;&nbsp;</strong>
            <input id="tablePersos-col" type="hidden" value="2">
            <input id="tablePersos-filtre-perso" type="text" size="20" onkeyup="filtre_table_search('tablePersos');">
            &nbsp;&nbsp;<strong>Limiter aux:&nbsp;&nbsp;</strong>
            &nbsp;&nbsp;<input name="tablePersos-filtre-type" value="1" type="radio"
                               onChange="filtre_table_search('tablePersos');">&nbsp;<em>Aventuriers <span
                        id="ft-aventurier"></span></em>
            &nbsp;&nbsp;<input name="tablePersos-filtre-type" value="0" type="radio"
                               onChange="filtre_table_search('tablePersos');">&nbsp;<em>Partisans <span
                        id="ft-partisan"></span></em>
            &nbsp;&nbsp;<input name="tablePersos-filtre-type" value="-1" type="radio" checked
                               onChange="filtre_table_search('tablePersos');">&nbsp;<em>Sans limites</span></em>
        </td>
    </tr>
    <?php if ($marquerQuatriemes)
    {
        echo '<tr><td colspan="9" class="soustitre2"><em>Une astérisque * à côté du O de 4ème perso signifie que toute mort sera définitive pour ce personnage</td></tr>';
    } ?>
    <tr>
        <td class="soustitre2" width="50"><strong>Dist.</strong></td>
        <?php if ($marquerQuatriemes)
        {
            echo '<td class="soustitre2"><strong>4ème</strong></td>';
        } ?>
        <td class="soustitre2"><strong>Nom</strong></td>
        <td class="soustitre2"><strong>Guilde</strong></td>
        <td class="soustitre2"><strong>Race</strong></td>
        <td class="soustitre2"><strong>Renommée</strong></td>
        <td class="soustitre2"><strong>Karma</strong></td>
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
        $i   = 0;
        $row = 3;
        while ($result = $stmt->fetch())
        {
            if ($result['traj'] == 1)
            {
                //Repérage des 4èmes persos
                $quatrieme = ($result['perso_pnj'] == 2) ? 'O' : 'N';
                $mortel    = ($result['perso_mortel'] == 'O') ? ' *' : '';

                $lock_combat  =
                    ($result['lock1'] > 0 || $result['lock2'] > 0) ? '<img src="http://www.jdr-delain.net/images/attaquer.gif" title="Vous êtes en combat avec cet aventurier." /> ' : '';
                $meme_coterie =
                    ($coterie > 0 && $result['pgroupe_groupe_cod'] == $coterie) ? '<img src="http://www.jdr-delain.net/images/guilde.gif" title="Cet aventurier appartient à la même coterie que vous." /> ' : '';

                $is_tangible = $result['perso_tangible'];

                $pv               = $result['perso_pv'];
                $num_perso        = $result['perso_cod'];
                $pv_max           = $result['perso_pv_max'];
                $niveau_blessures = niveau_blessures($pv, $pv_max);


                $aff_tangible = $palbable[$is_tangible];
                $req_guilde   =
                    "select guilde_nom,guilde_cod from guilde,guilde_perso where pguilde_perso_cod = $num_perso and pguilde_valide = 'O' and pguilde_guilde_cod = guilde_cod ";

                $stmt2     = $pdo->query($req_guilde);
                $nb_guilde = $stmt2->rowCount();
                if ($nb_guilde != 0)
                {
                    $result2     = $stmt2->fetch();
                    $nom_guilde  = $result2['guilde_nom'];
                    $code_guilde = $result2['guilde_cod'];
                } else
                {
                    $nom_guilde  = '';
                    $code_guilde = '0';
                }
                $nom  = str_replace("\\", " ", $result['perso_nom']);
                $nom  = str_replace("'", "’", $nom);
                $nom  = str_replace("/", " ", $nom);
                $desc = nl2br(htmlspecialchars(str_replace('\'', '’', $result['perso_description'])));
                $desc = str_replace("\r", "", $desc);
                $desc = str_replace("%", "pourcent", $desc);

                if ($result['perso_crapaud'] == 1)
                {
                    $crapaud = '';
                    if ($result['distance'] == 0)
                    {
                        $crapaud .= '<a href="action.php?methode=embr&cible=' . $result['perso_cod'] . '">L’embrasser ? (2 PA)</a>';
                    }
                    $nom = 'Aventurier transformé en crapaud !';
                } else
                {
                    $crapaud = '';
                }

                if ($result['perso_desc_long'] != NULL or $result['perso_desc_long'] != "")
                {
                    $nom .= '<strong> *</strong>';
                }
                if ($result['distance'] <= $portee)
                {
                    if ($result['pcompt_compt_cod'] != $compt_cod)
                    {
                        $attaquable = 1;
                    } else
                    {
                        $attaquable = 0;
                    }
                } else
                {
                    $attaquable = 0;
                }
                if ($perso->is_refuge())
                {
                    $attaquable = 0;
                }
                $perso2 = new perso;
                $perso2->charge($num_perso);
                if ($perso2->is_refuge())
                {
                    $attaquable = 0;
                }
                if ($pa < $pa_n)
                {
                    $attaquable = 0;
                }
                /*Rajout des persos non attaquable des comptes sittés*/
                $req_joueur_attaquable = "select 1 where $num_perso not in";
                $req_joueur_attaquable =
                    $req_joueur_attaquable . "((select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now())";
                $req_joueur_attaquable = $req_joueur_attaquable . "	union";
                $req_joueur_attaquable =
                    $req_joueur_attaquable . "(select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now())";
                $req_joueur_attaquable = $req_joueur_attaquable . "	union";
                $req_joueur_attaquable =
                    $req_joueur_attaquable . "(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod)";
                $req_joueur_attaquable = $req_joueur_attaquable . "	union";
                $req_joueur_attaquable =
                    $req_joueur_attaquable . "(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = $compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod))";
                $stmt2                 = $pdo->query($req_joueur_attaquable);
                if ($stmt2->rowCount() == 0)
                {
                    $attaquable = 0;
                }
                /*Fin rajout*/
                if ($is_tangible == 'N')
                {
                    $attaquable = 0;
                }
                $req   = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau ";
                $req   = $req . "where dper_perso_cod = $num_perso ";
                $req   = $req . "and dper_dieu_cod = dieu_cod ";
                $req   = $req . "and dper_niveau = dniv_niveau ";
                $req   = $req . "and dniv_dieu_cod = dieu_cod ";
                $req   = $req . "and dniv_niveau >= 1 ";
                $stmt2 = $pdo->query($req);
                if ($stmt2->rowCount() != 0)
                {
                    $result2  = $stmt2->fetch();
                    $religion = " </strong>(" . $result2['dniv_libelle'] . " de " . $result2['dieu_nom'] . ")<strong> ";
                } else
                {
                    $religion = '';
                }
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
                    'onMouseOver="changeStyles(\'cell' . $result['pos_cod'] . '\',\'lperso' . $result['perso_cod'] . '\',\'vu\',\'surligne\');" onMouseOut="changeStyles(\'cell' . $result['pos_cod'] . '\',\'lperso' . $result['perso_cod'] . '\',\'pasvu\',\'' . $style . '\');"';
                $cdata    = "";
                $cdata    .= "data-partisans='" . (($coterie > 0 && $result['pgroupe_groupe_cod'] == $coterie) || ($result['triplette'] == 1) ? "O" : "N") . "' ";
                $cdata    .= "data-type='1' ";
                echo '<tr id="row-' . $row . '" ' . $cdata . '>
				<td ' . $ch_style . '><div style="text-align:center;">' . $result['distance'] . '</div></td>';
                if ($marquerQuatriemes)
                {
                    echo '<td ' . $ch_style . '><div style="text-align:center;">' . $quatrieme . $mortel . '</div></td>';
                }
                echo '<td ' . $ch_style . 'id="lperso' . $result['perso_cod'] . '" class="' . $style . '">' . $lock_combat . $meme_coterie . '<strong><a href="visu_desc_perso.php?visu=' . $result['perso_cod'] . '">' . $nom . '</a>' . $religion . $niveau_blessures . '</strong>' . $aff_tangible . $crapaud . '<br><span style="font-size:8pt">' . $desc . '</span>
				</td>
				<td ' . $ch_style . '><a href="visu_guilde.php?num_guilde=' . $code_guilde . '">' . $nom_guilde . '</a></td>
				<td ' . $ch_style . '>' . $result['race_nom'] . '&nbsp;(' . $result['perso_sex'] . ')</td>
				<td ' . $ch_style . '>' . $result['renommee'] . '</td>
				<td ' . $ch_style . '>' . $result['karma'] . '</td>
				<td ' . $ch_style . ' nowrap><div style="text-align:center;">' . $result['pos_x'] . '</div></td>
				<td ' . $ch_style . ' nowrap><div style="text-align:center;">' . $result['pos_y'] . '</div></td>
				<td ' . $ch_style . '><td>';
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
        <td colspan="8" class="soustitre2">Aucun joueur en vue</td>
        <?php
    }
    ?>
</table>
