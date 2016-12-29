<?php 
$req_vue_joueur = "select lieu_nom,tlieu_libelle,distance(pos_cod,$pos_cod) as distance,pos_x,pos_y,pos_cod,lieu_cod,lieu_refuge ";
$req_vue_joueur = $req_vue_joueur . "from lieu,lieu_type,lieu_position,positions ";
$req_vue_joueur = $req_vue_joueur . "where pos_x between ($x-$distance_vue) and ($x+$distance_vue) ";
$req_vue_joueur = $req_vue_joueur . "and pos_y between ($y-$distance_vue) and ($y+$distance_vue) ";
$req_vue_joueur = $req_vue_joueur . "and pos_etage = $new_etage ";
$req_vue_joueur = $req_vue_joueur . "and lpos_pos_cod = pos_cod ";
$req_vue_joueur = $req_vue_joueur . "and lpos_lieu_cod = lieu_cod ";
$req_vue_joueur = $req_vue_joueur . "and lieu_tlieu_cod = tlieu_cod ";
$req_vue_joueur = $req_vue_joueur . "and tlieu_cod != 19 ";
$req_vue_joueur = $req_vue_joueur . "and not exists(select 1 from murs where mur_pos_cod = pos_cod) ";
$req_vue_joueur = $req_vue_joueur . "order by distance,pos_x,pos_y";
$db->query($req_vue_joueur);
$nb_lieux_en_vue = $db->nf();
if ($nb_lieux_en_vue != 0)
{

	?>
	<table width="100%" cellspacing="2" cellapdding="2"><tr><td colspan="5" class="soustitre"><p class="soustitre">Sites</td></tr>
	<tr><td class="soustitre2" width="50"><p><b>Dist.</b></td>
	<td class="soustitre2"><p><b>Nom</b></td>
	<td class="soustitre2"><p><b>Type</b></td>
	<td class="soustitre2"><p style="text-align:center;"><b>X</b></td>
	<td class="soustitre2"><p style="text-align:center;"><b>Y</b></td>
	</tr>
	<?php 
	$i = 0;
	while($db->next_record())
	{
		$refuge = ($db->f('lieu_refuge') == 'O') ? 'refuge' : 'non protégé';
		$nom = $db->f("lieu_nom") . " <i>($refuge)</i>";
		$type = $db->f("tlieu_libelle");
		$style = "soustitre2";

		$ch_style = 'onMouseOver="changeStyles(\'cell' . $db->f("pos_cod") . '\',\'llieu' . $db->f("lieu_cod") . '\',\'vu\',\'surligne\');" onMouseOut="changeStyles(\'cell' . $db->f("pos_cod") . '\',\'llieu' . $db->f("lieu_cod") . '\',\'pasvu\',\'' . $style . '\');"';

		echo '<tr>
			<td ' . $ch_style . '><p style="text-align:center;">' . $db->f('distance') . '</p></td>
			<td ' . $ch_style . 'id="llieu' . $db->f("lieu_cod") . '" class="soustitre2"><p>' . $nom . '</p></td>
			<td ' . $ch_style . '><p>' .$type . '</p></td>
			<td ' . $ch_style . ' nowrap><p style="text-align:center;">' . $db->f("pos_x") . '</p></td>
			<td ' . $ch_style . ' nowrap><p style="text-align:center;">' . $db->f("pos_y") . '</p></td>
			</tr>';
	}
}
?>
