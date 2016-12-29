<script type="text/javascript">
    var afficheFamiliers = true;
    var afficheMonstresCoterie = true;
</script>
<table width="100%" cellspacing="2" cellapdding="2" id="tableMonstres">

<?php 
$parm = new parametres();
$marquerQuatriemes = $db->is_admin_monstre($compt_cod);
$req_malus_desorientation = " select valeur_bonus($perso_cod, 'DES') as desorientation";
$db->query($req_malus_desorientation);
$db->next_record();
if ($db->f("desorientation") == 0)
	$desorientation = false;
else
	$desorientation = true;
$combat_groupe = $parm->getparm(56);
$req = "select perso_pa from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
$pa = $db->f("perso_pa");
$pa_n = $db->get_pa_attaque($perso_cod);

$req_vue_joueur = "select trajectoire_vue($pos_cod,pos_cod) as traj, perso_tangible, perso_description, perso_desc_long, perso_nom,
		pos_x, pos_y, pos_etage, race_nom, distance(pos_cod,$pos_cod) as distance, perso_cod, perso_sex, perso_pv,
		perso_pv_max, pos_cod, is_surcharge(perso_cod,$perso_cod) as surcharge, pgroupe_groupe_cod, l1.lock_nb_tours as lock1,
		l2.lock_nb_tours as lock2, coalesce(compt_monstre, 'N') as compt_monstre, coalesce(compt_cod, -1) as compt_cod, perso_dirige_admin
	FROM perso
	INNER JOIN perso_position ON ppos_perso_cod = perso_cod
	INNER JOIN positions ON pos_cod = ppos_pos_cod
	INNER JOIN race ON perso_race_cod = race_cod
	LEFT OUTER JOIN groupe_perso ON pgroupe_perso_cod = perso_cod AND pgroupe_statut = 1
	LEFT OUTER JOIN lock_combat l1 ON l1.lock_cible = perso_cod AND l1.lock_attaquant = $perso_cod
	LEFT OUTER JOIN lock_combat l2 ON l2.lock_cible = $perso_cod AND l2.lock_attaquant = perso_cod
	LEFT OUTER JOIN perso_compte ON pcompt_perso_cod = perso_cod
	LEFT OUTER JOIN compte ON compt_cod = pcompt_compt_cod 
	WHERE pos_etage = $etage
		and perso_type_perso in (2,3)
		and pos_x between ($x-$distance_vue) and ($x+$distance_vue) 
		and pos_y between ($y-$distance_vue) and ($y+$distance_vue) 
		and perso_actif = 'O' 
		and perso_cod != $perso_cod  
	ORDER BY distance, pos_x, pos_y";
//echo "<!--" . $req_vue_joueur . "-->";

$db->query($req_vue_joueur);
$nb_joueur_en_vue = $db->nf();

$nb_colonnes = ($marquerQuatriemes) ? 7 : 6;
?>
<tr><td colspan="<?php echo  $nb_colonnes; ?>" class="soustitre"><div class="soustitre">Monstres</div></td></tr>
<tr><td colspan="<?php echo  $nb_colonnes; ?>" class="soustitre2">Afficher/masquer : <span style="cursor:pointer;" onclick="afficheMasque(egal, new Array(2, 'Familier'), 'tableMonstres', afficheFamiliers); afficheFamiliers = !afficheFamiliers;">les familiers</span>, <span style="cursor:pointer;" onclick="afficheMasque(contient, new Array(1, 'guilde.gif'), 'tableMonstres', afficheMonstresCoterie); afficheMonstresCoterie = !afficheMonstresCoterie;">la coterie</span>.</td></tr>
<tr>
<td class="soustitre2" width="50"><b>Dist.</b></td>
<?php  if ($marquerQuatriemes) echo '<td class="soustitre2"><b>Contrôle</b></td>'; ?>
<td class="soustitre2"><b>Nom</b></td>
<td class="soustitre2"><b>Race</b></td>
<td class="soustitre2"><div style="text-align:center;"><b>X</b></div></td>
<td class="soustitre2"><div style="text-align:center;"><b>Y</b></div></td>
<td></td>
</tr>
<?php 
if ($nb_joueur_en_vue != 0)
{
	$i = 0;
	$lieuRefuge = $db_refuge->is_refuge($perso_cod);

	while($db->next_record())
	{
		if ($db->f("traj") == 1)
		{
    		//Repérage du type de contrôle
			$ia = ($db->f("perso_dirige_admin") == 'O') ? 'Hors IA' : 'Sous IA';
            $type_controle = ($db->f("compt_monstre") == 'O') ? ' (Compte monstre)' : ' (Compte joueur)';
            if ($db->f("compt_cod") == -1)
                $type_controle = '';

			$lock_combat = ($db->f("lock1") > 0 || $db->f("lock2") > 0) ? '<img src="http://images.jdr-delain.net/attaquer.gif" title="Vous êtes en combat avec ce monstre." /> ' : '';
			$meme_coterie = ($coterie > 0 && $db->f("pgroupe_groupe_cod") == $coterie) ? '<img src="http://images.jdr-delain.net/guilde.gif" title="Ce monstre appartient à la même coterie que vous." /> ' : '';
			$is_tangible = $db->f("perso_tangible");
			$aff_tangible = $palbable[$is_tangible];
			$niveau_blessures = '';
			$pv = $db->f("perso_pv");
			$num_perso = $db->f("perso_cod");
			$pv_max = $db->f("perso_pv_max");
			if ($pv/$pv_max < 0.75)
			{
				$niveau_blessures = ' - <b>' . $tab_blessures[0] . '</b>';
			}
			if ($pv/$pv_max < 0.5)
			{
				$niveau_blessures = ' - <b>' . $tab_blessures[1] . '</b>';
			}
			if ($pv/$pv_max < 0.25)
			{
				$niveau_blessures = ' - <b>' . $tab_blessures[2] . '</b>';
			}
			if ($pv/$pv_max < 0.15)
			{
				$niveau_blessures = ' - <b>' . $tab_blessures[3] . '</b>';
			}
			$nom = $db->f("perso_nom");
			if ($db->f("perso_desc_long") != NULL or $db->f("perso_desc_long") != "")
				$nom .= '<b> *</b>';
			if ($db->f("distance") <= $portee)
			{
				$attaquable = 1;
			}
			else
			{
				$attaquable = 0;
			}
			if ($pa < $pa_n)
			{
				$attaquable = 0;
			}
			if ($lieuRefuge || $db_refuge->is_refuge($num_perso))
			{
				$attaquable = 0;
			}
			$desc = nl2br(htmlspecialchars(str_replace('\'', '’', $db->f("perso_description"))));
			$desc = str_replace("\r","",$desc);
			$desc = str_replace("%","pourcent",$desc);
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
			$ch_style = 'onMouseOver="changeStyles(\'cell' . $db->f("pos_cod") . '\',\'lmonstre' . $db->f("perso_cod") . '\',\'vu\',\'surligne\');" onMouseOut="changeStyles(\'cell' . $db->f("pos_cod") . '\',\'lmonstre' . $db->f("perso_cod") . '\',\'pasvu\',\'' . $style . '\');"';
			echo '<tr>
				<td ' . $ch_style . '><div style="text-align:center;">' . $db->f("distance") . '</div></td>';
                
    		if ($marquerQuatriemes)
				echo '<td ' . $ch_style . '><div style="text-align:center;">' . $ia . $type_controle . '</div></td>';
            
			echo '<td ' . $ch_style . 'id="lmonstre' . $db->f("perso_cod") . '" class="' . $style .'">'.$lock_combat.$meme_coterie.'<b><a href="visu_desc_perso.php?visu=' . $db->f("perso_cod") . '">' . $nom . '</a>
					</b>' . $aff_tangible . $niveau_blessures . '<br><span style="font-size:8pt">' . $desc . '</span></td>
				<td ' . $ch_style .  '>' . $db->f("race_nom") . '</td>
				<td ' . $ch_style .  ' nowrap><div style="text-align:center;">' . $db->f("pos_x") . '</div></td>
				<td ' . $ch_style .  ' nowrap><div style="text-align:center;">' . $db->f("pos_y") . '</div></td><td>';
			if(!$desorientation)
			{
				if($attaquable == 1)
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
	<td colspan="5" class="soustitre2">Aucun monstre en vue</td>
	<?php 
}


?>
</table>
