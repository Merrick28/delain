<table width="100%" cellspacing="2" cellapdding="2">
	<tr><td colspan="6" class="soustitre"><p class="soustitre">Objets</p></td></tr>
	<tr>
		<td class="soustitre2" width="50"><p><strong>Dist.</strong></p></td>
		<td class="soustitre2"><p><strong>Nom</strong></p></td>
		<td class="soustitre2"><p><strong>Type objet</strong></p></td>
		<td class="soustitre2"><p style="text-align:center;"><strong>X</strong></p></td>
		<td class="soustitre2"><p style="text-align:center;"><strong>Y</strong></p></td>
		<td></td>
	</tr>
<?php 

$req = "select perso_pa from perso where perso_cod = $perso_cod ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
$pa = $result['perso_pa'];
$req_vue_joueur = "select trajectoire_vue($pos_cod,pos_cod) as traj, obj_nom_generique, tobj_libelle, pos_x, pos_y, pos_etage,
							distance(pos_cod,$pos_cod) as distance, obj_cod as objet, obj_nom, pos_cod, COALESCE(pio_nb_tours, -1) as identifie,
							COALESCE(obon_libelle, '') as obon_libelle
						from positions
						inner join objet_position ON pobj_pos_cod = pos_cod
						inner join objets ON obj_cod = pobj_obj_cod
						left outer join bonus_objets ON obon_cod = obj_obon_cod
						inner join objet_generique ON gobj_cod = obj_gobj_cod
						inner join type_objet ON tobj_cod = gobj_tobj_cod
						left outer join perso_identifie_objet ON pio_obj_cod = obj_cod and pio_perso_cod = $perso_cod
						where pos_x between ($x-$distance_vue) and ($x+$distance_vue) 
							and pos_y between ($y-$distance_vue) and ($y+$distance_vue) 
							and pos_etage = $etage
						order by distance, tobj_libelle, pos_x, pos_y";
$stmt = $pdo->query($req_vue_joueur);
$nb_joueur_en_vue = $stmt->rowCount();
if ($nb_joueur_en_vue != 0)
{
	$i = 0;
	while($result = $stmt->fetch())
	{
		if ($result['traj'] == 1)
		{
			$v_objet = $result['objet'];
			if ($result['identifie'] > 0)
			{
				if ($result['obon_libelle'] != '')
				{
					$bonus = " (" . $result['obon_libelle'] . ")";
				}
				else
				{
					$bonus = "";
				}
				$nom_objet = $result['obj_nom'] . $bonus;
			}
			else
			{
				$nom_objet = $result['obj_nom_generique'];
			}
			$nom_objet = trim(str_replace("'","’",$nom_objet));
			$tobj_libelle = trim($result['tobj_libelle']);
			$pos_x = $result['pos_x'];
			$pos_y = $result['pos_y'];
			$distance = $result['distance'];
			// On ajuste la vue des runes à la portée de vue
			// 3->0  4->1 6->2 8->3
			if ($distance > ($distance_vue - 2) / 2 && $tobj_libelle == 'Rune')
				$nom_objet = 'Rune';
			$position = $result['pos_cod'];
			$ramassable = 0;
			if ($distance == 0)
			{
				$ramassable = 1;
			}
			if ($pa < 1)
			{
				$ramassable = 0;
			}
			
			$ch_style = 'onMouseOver="changeStyles(\'cell' . $position . '\',\'lobjet' . $v_objet . '\',\'vu\',\'surligne\');" onMouseOut="changeStyles(\'cell' . $position . '\',\'lobjet' . $v_objet . '\',\'pasvu\',\'soustitre2\');"';
			echo '<td ' . $ch_style . '><p style="text-align:center;">' . $distance . '</p></td>
				<td ' . $ch_style . 'id="lobjet' . $v_objet . '" class="soustitre2"><p>' . $nom_objet .'</p></td>
				<td ' . $ch_style . '><p>' . $tobj_libelle . '</p></td>
				<td ' . $ch_style . ' nowrap><p style="text-align:center;">' . $pos_x . '</p></td>
				<td ' . $ch_style . ' nowrap><p style="text-align:center;">' . $pos_y .'</p></td>
				<td ' . $ch_style . '>';
			if($ramassable==1)
			{
				echo '<p><a href="action.php?methode=ramasse_objet&type_objet=1&num_objet=' . $v_objet . '">Ramasser !</a>';
			}
			echo '</td></tr>';
		}
	}
}
$req_vue_joueur = "select trajectoire_vue($pos_cod,pos_cod) as traj,por_qte,por_cod,pos_x,pos_y,distance(pos_cod,$pos_cod) as distance,pos_cod ";
$req_vue_joueur = $req_vue_joueur . "from or_position,positions ";
$req_vue_joueur = $req_vue_joueur . "where pos_x between ($x-$distance_vue) and ($x+$distance_vue) ";
$req_vue_joueur = $req_vue_joueur . "and pos_y between ($y-$distance_vue) and ($y+$distance_vue) ";
$req_vue_joueur = $req_vue_joueur . "and pos_etage = $etage ";
$req_vue_joueur = $req_vue_joueur . "and pos_cod = por_pos_cod ";
$req_vue_joueur = $req_vue_joueur . "order by distance,pos_x,pos_y";
$stmt = $pdo->query($req_vue_joueur);
//$res_vue_joueur = pg_exec($dbconnect,$req_vue_joueur);
$nb_joueur_en_vue = $stmt->rowCount();
if ($nb_joueur_en_vue != 0)
{
	$i = 0;
	while($result = $stmt->fetch())
	{
		if($result['traj'] == 1)
		{
			$qte = $result['por_qte'];
			$nom = $qte . " brouzoufs.";
			$ch_style = 'onMouseOver="changeStyles(\'cell' . $result['pos_cod'] . '\',\'lvor' . $result['por_cod'] . '\',\'vu\',\'surligne\');" onMouseOut="changeStyles(\'cell' . $result['pos_cod'] . '\',\'lvor' . $result['por_cod'] . '\',\'pasvu\',\'soustitre2\');"';
			echo '<tr>
				<td ' . $ch_style . '><p style="text-align:center;">' . $result['distance'] . '</p></td>
				<td colspan="2"' . $ch_style .'id="lvor' . $result['por_cod'] . '" class="soustitre2"><p>' . $qte . ' brouzoufs</p></td>
				<td ' . $ch_style . ' nowrap><p style="text-align:center;">' . $result['pos_x'] . '</p></td>
				<td ' . $ch_style . ' nowrap><p style="text-align:center;">' . $result['pos_y'] . '</p></td>
				<td '. $ch_style . '>';
			if($result['distance']==0)
			{
				echo '<p><a href="action.php?methode=ramasse_objet&type_objet=2&num_objet=' . $result['por_cod'] . '">Ramasser !</a>';
			}
			echo'</td></tr>';
		}
	}
}
?>

