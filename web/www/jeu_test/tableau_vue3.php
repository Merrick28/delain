<?php $start = time();
include_once "verif_connexion.php";
include "../includes/constantes.php";
if(!isset($db))
    $db = new base_delain;
ob_start();

$requete = "select pos_x, pos_y, etage_libelle, coalesce (lieu_nom, '') as lieu_nom, tlieu_libelle, lieu_refuge
	FROM perso_position 
	INNER JOIN positions ON ppos_pos_cod = pos_cod 
	INNER JOIN etage ON etage_numero = pos_etage
	LEFT OUTER JOIN lieu_position on lpos_pos_cod = pos_cod
	LEFT OUTER JOIN lieu on lieu_cod = lpos_lieu_cod
	LEFT OUTER JOIN lieu_type on lieu_tlieu_cod = tlieu_cod
	where ppos_perso_cod = $perso_cod ";
$db->query($requete);
$db->next_record();

$x = $db->f("pos_x");
$y = $db->f("pos_y");
$lib_etage = $db->f("etage_libelle");
$lieu_nom = $db->f("lieu_nom");
$tlieu_libelle = $db->f("tlieu_libelle");
$lieu_refuge = ($db->f('lieu_refuge') == 'O') ? 'refuge' : 'non protégé';

//
// choix de l'affichage */
//
$tab[0] = 'Aventuriers';
$tab[1] = 'Monstres';
$tab[2] = 'Objets';
$tab[3] = 'Lieux';
$tab[4] = 'Tout voir';
$nb = count($tab);
if (!isset($tab_vue))
{
	$tab_vue = -1;
}
?>
<script language="javascript"> 
ns4 = document.layers; 
ie = document.all; 
ns6 = document.getElementById && !document.all; 

	function changeStyles (id2, id1, style2, style1) {
		if (ns4) {
			//alert ("Sorry, but NS4 does not allow font changes.");
			return false;
		}
		else if (ie) {
			obj1 = document.all[id1];
			obj2 = document.all[id2];
		}
		else if (ns6) {
			obj1 = document.getElementById(id1);
			obj2 = document.getElementById(id2);
		}
		if (!obj1) {
			return false;
		}
		if (!obj2) {
			return false;
		}
		obj1.className = style1;
		obj2.className = style2;

		return true;
	}
	
	function changeOnglet(onglet)
	{
		<?php 		for ($i = 0; $i < $nb; $i++)
			echo "		document.getElementById('onglet_vue_$i').className = 'pas_onglet';";
		?>
		document.getElementById('onglet_vue_' + onglet).className = 'onglet';
		getdata('include_tableau2.php?tab_vue=' + onglet, 'vue_liste');
	}

	function afficheMasque(fonction, parametres, nomTable, masquer)
	{
		var laTable = document.getElementById(nomTable);
		var lesTR = laTable.getElementsByTagName("tr");
		for (var i = 0; i < lesTR.length; i++)
		{
			var lesTD = lesTR[i].getElementsByTagName("td");
			if (i > 1)
			{
				if (fonction(lesTD, parametres) && masquer)
					lesTR[i].style.display = 'none';
				else
					lesTR[i].style.display = '';
			}
		}
	}

	function egal(lesTD, parametres)
	{
		return lesTD[parametres[0]].innerHTML == parametres[1];
	}

	function contient(lesTD, parametres)
	{
		return lesTD[parametres[0]].innerHTML.indexOf(parametres[1]) >= 0;
	}

</script>

<table width="100%" cellspacing="0" cellpadding="0">
<?php 
echo '<tr><td colspan="' . $nb . '"><p><b>Vous êtes en position ' . $x . ', ' . $y . ', 
	<a href="desc_etage.php"><img alt="" src="/images/iconmap.gif" style="height:12px;border:0px;" /> ' . $lib_etage . '</a>.</p></td></tr>';
if ($lieu_nom != '')
	echo "<tr><td colspan='$nb'><p>$lieu_nom ($tlieu_libelle - $lieu_refuge)</p></td></tr>";

$size = round(100/$nb);

echo "<tr>";

for($cpt = 0; $cpt < $nb; $cpt++)
{
	$id = 'onglet_vue_' . $cpt;
	if($cpt == $tab_vue)
	{
		$style = 'onglet';
		$lien = '';
		$f_lien = '';
	}
	else
	{
		$style = 'pas_onglet';
		$lien = '<a href="javascript:void(0);" onclick="changeOnglet(' . $cpt . '); ">';
		$f_lien = '</a>';
	}
	echo '<td width="' . $size . ' %" class="' . $style .'" id="' . $id . '"><p style="text-align:center">' . $lien . $tab[$cpt] . $f_lien . '</p></td>';
}
echo '</tr>';
?>

<tr>
		<td colspan="<?php echo $nb;?> " class="reste_onglet" id="vue_liste">

</td></tr></table>
<?php $vue_bas = ob_get_contents()."<script type='text/javascript'>tailleCadre();</script>";
ob_end_clean();
if(!defined('APPEL_VUE'))
	echo $vue_bas;
?>
