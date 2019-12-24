<?php 
$offset = 3600; // on remet à jour la feuille de style une fois par heure.
$expire = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($expire);
header("Content-type: text/css");

include "verif_connexion.php";

if (!isset($num_etage) || $num_etage === '')
{
	$req_etage = "select etage_affichage from perso_position,positions,etage ";
	$req_etage = $req_etage . "where ppos_perso_cod = $perso_cod ";
	$req_etage = $req_etage . "and ppos_pos_cod = pos_cod ";
	$req_etage = $req_etage . "and pos_etage = etage_numero ";
	$db->query($req_etage);
	$db->next_record();
	$etage = $db->f("etage_affichage");
}
else
{
	$req_etage = "select etage_affichage from etage where etage_numero = $num_etage ";
	$db->query($req_etage);
	$db->next_record();
	$etage = $db->f("etage_affichage");
}
?>

.caseVue{
	width: 28px;
	height: 28px;
	border: 0px;
	padding:0px;
	margin:0px;
}
<?php 
// Plutôt que de chercher tous les styles possibles et imaginables, on se contente de ceux de l'étage en cours
// Cela implique de bien mettre ?num_etage=.. dans l'appel (sinon, gare aux effets de cache et de changement d'étage)
// La feuille de style sera ainsi plus légère, et dédiée à un étage.

// FOND DE LA CASE
$req_styles = "select distinct pos_type_aff from positions where pos_etage = $num_etage";
$db->query($req_styles);
$arr_styles = array();
while($db->next_record())
{
	$cpt = $db->f('pos_type_aff');
	echo "td.v$cpt{
	background: url('" . G_IMAGES . "f_" , $etage, "_" , $cpt, ".png');
}";
}

// LIEUX
$req_styles = "select distinct tlieu_cod from lieu_type";
$db->query($req_styles);
while ($db->next_record())
{
	$cpt = $db->f('tlieu_cod');
	echo ".lieu$cpt{
	background:url('" . G_IMAGES . "t_" , $cpt, "_lie.png');
}";
}

// DECORS
$req_styles = "select distinct pos_decor from positions where pos_etage = $num_etage
	UNION
	select distinct pos_decor_dessus from positions where pos_etage = $num_etage";
$db->query($req_styles);
while ($db->next_record())
{
	$cpt = $db->f('pos_decor');
	echo ".decor$cpt{
	background:url('" . $type_flux.G_URL . "images/dec_" , $cpt, ".gif');
}";
}

echo ".joueur{
	background-image:url('" . G_IMAGES . "t_" , $etage, "_per.png');
}";
echo ".br{
	background-image:url('" . $type_flux.G_URL . "/images/br.png');
}";
echo ".monstre{
	background-image:url('" . G_IMAGES . "t_" , $etage, "_enn.png');
}";
echo ".objet{
	background-image:url('" . G_IMAGES . "t_" , $etage, "_obj.png');
}";

// MURS
$req_styles = "select distinct mur_type from murs
	inner join positions on pos_cod = mur_pos_cod
	where pos_etage = $num_etage";
$db->query($req_styles);
while ($db->next_record())
{
	$cpt = $db->f('mur_type');
	echo ".mur_$cpt{
	background: url('" . G_IMAGES . "t_" , $etage, "_mur_" , $cpt, ".png');
	visibility: visible;
}";
}
?>
.main{
	 cursor:hand;
} 
.invisible{
	background:url('<?php echo G_IMAGES?>del.gif');
}
.oncase{
	background:url('<?php echo G_IMAGES?>sur_case.gif');
}
.vu{
	visibility:visible;
	background:url('<?php echo G_IMAGES?>c1.gif');
	transition: opacity 500ms ease-in-out, background-color 500ms ease-in-out;
}
.pasvu{
	visibility:hidden;
	background:url('<?php echo G_IMAGES?>c1.gif');
	transition: opacity 500ms ease-in-out, background-color 200ms ease-in-out;
}

