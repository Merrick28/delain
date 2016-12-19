<?php 
ob_start();
?>
<link rel="stylesheet" type="text/css" href="<?php echo $type_flux.G_URL;?>jeu/style_vue.php" title="essai">
<?php 


$req_distance = "select distance_vue($perso_cod) as distance";
$db->query($req_distance);
$db->next_record();
$distance_vue = $db->f("distance");

// on cherche la position
$req_etage = "select pos_etage,pos_cod,pos_x,pos_y,etage_affichage from perso_position,positions,etage ";
$req_etage = $req_etage . "where ppos_perso_cod = $perso_cod ";
$req_etage = $req_etage . "and ppos_pos_cod = pos_cod ";
$req_etage = $req_etage . "and pos_etage = etage_numero ";
$db->query($req_etage);
if ($db->nf())
{
$db->next_record();
$aff_etage = $db->f("etage_affichage");
$etage_actuel = $db->f("pos_etage");
$pos_actuelle = $db->f("pos_cod");
$x_actuel = $db->f("pos_x");
$y_actuel = $db->f("pos_y");


echo '<table border="0" cellspacing="0" cellpadding="0" ID="tab_vue" bgcolor="#FFFFFF" >';
?>
<form name="destdroite" id="destdroite" method="post">
<input type="hidden" name="position" id="idposition">
<input type="hidden" name="destcadre" value="le_tout" id="iddestcadre">
<input type="hidden" name="dist">
<input type="hidden" name="action" id="idaction" value="action.php">
<input type="hidden" name="methode" value="deplacement">
<?php 
$req_x = "select distinct pos_x from positions where pos_x between ($x_actuel - $distance_vue) and ($x_actuel + $distance_vue) and pos_etage = $etage_actuel order by pos_x";
$db->query($req_x);
$db->next_record();
$ssize = ($distance_vue *2 + 2 )*30;
echo "<tr><td style=\"coord2\"><a href=\"javascript:parent.set('".$ssize.",*','".$ssize.",*');\" class=\"coord\"><img alt=\"Cliquez ici pour élargir la vue\" title=\"Cliquez ici pour élargir la vue\" src=\"../images/agrandir.gif\" border=\"0\"></a></td>";

$min_x = $db->f("pos_x");
$db->query($req_x);
while($db->next_record())
{
	echo  '<td style="coord2"><p class="coord">' . $db->f("pos_x") . '</p></td>';
}
echo '</tr>';
?>
<script language="JavaScript" type="text/JavaScript">
var tc = new Array();
<?php 
$req_y = "select distinct pos_y from positions where pos_y between ($y_actuel - $distance_vue) and ($y_actuel + $distance_vue) and pos_etage = $etage_actuel order by pos_y desc";
$db->query($req_y);
$db->next_record();
$min_y = $db->f("pos_y");
$req_map_vue = "select vue_perso5($perso_cod) as vue ";
$db->query($req_map_vue);
$db->next_record();
echo $db->f("vue");
?>
var etage;
var i;
var y_encours;
var code_image;
var comment;
var isobjet;
var texte;
var style;
var action;

action='<?php echo $action;?>';
etage='<?php echo $aff_etage;?>';
img_path='<?php  echo str_replace(chr(92),chr(92) . chr(92),G_IMAGES);?>';
y_encours = -2000;
for (i=0; i<tc.length; i++)
{
	titre = '';
	detail = 0;
	texte = '';
	isobjet = 0;
	comment = '';
	code_image = 0;
	if (y_encours != tc[i][2])
	{
		y_encours = tc[i][2];
		document.write('<tr class="vueoff" height="10"><td height="10" style="coord2"><p class="coord">' + y_encours + '</p></td>');
	}

	style = 'v' + tc[i][10];

	texte = texte + '<td class="' + style + '">';
	if (tc[i][12] != 0)
	{
		texte = texte + '<div id="1" class="decor' + tc[i][12] + '">';
	}


	if (tc[i][3] != 0)
	{
		comment = comment + tc[i][3] + ' aventurier(s), ';
		detail = 1;
		texte = texte + '<div id="1" class="joueur">';
		titre = titre + tc[i][3] + ' aventuriers, ';
	}

	if (tc[i][4] != 0)
	{
		comment = comment + tc[i][4] + ' monstre(s), ';
		detail = 1;
		texte = texte + '<div id="1" class="monstre">';
		titre = titre + tc[i][4] + ' monstres.';
	}

	if (tc[i][6] != 0)
	{
		comment = comment + tc[i][6] + ' objet(s), ';
		detail = 1;
		texte = texte + '<div id="1" class="objet">';

		isobjet = 1;
	}

	if (tc[i][7] != 0)
	{
		comment = comment + tc[i][7] + ' tas d\'or, ';
		detail = 1;
		if (isobjet == 0)
		{
			isobjet = 1;
		}
		texte = texte + '<div id="1" class="objet">';
	}

	if (tc[i][5] == 1)
	{
		comment = comment + '1 mur';
		texte = texte + '<div id="1" class="mur_' + tc[i][9] + '">';
	}
	if (tc[i][11] != 0)
	{
		comment = comment + '1 lieu, ';
		texte = texte + '<div class="lieu' + tc[i][11] + '">';
	}

	if (tc[i][8] == 0)
	{
		texte = texte + '<div id="1" class="oncase">';
	}
	texte = texte + '<div id="dep" class="main" onClick="javascript:document.forms[\'destdroite\'].position.value=' + tc[i][0] + ';document.forms[\'destdroite\'].dist.value=\'' + tc[i][8] + '\';voirList(document.forms[\'destdroite\'],document.forms[\'destdroite\'].action.value,document.forms[\'destdroite\'].destcadre.value);" title="' + titre + '">';
	if ((tc[i][13] == 0) && (tc[i][5] != 1))
	{
		texte = texte + '<div id="1" class="br">';
	}
	if (tc[i][13] == 1)
	{
		texte = texte + '<div id="cell2' + tc[i][0] + '"';
		texte = texte + '>\r\n';
	}
	if (tc[i][14] != 0)
	{
		texte = texte + '<div id="1" class="decor' + tc[i][14] + '">';
	}
	texte = texte + '<div id="cell' + tc[i][0] + '" class="pasvu" style="background:url(\'' + img_path + 'c1.gif\')" onClick="javascript:document.destdroite.position.value=\'' + tc[i][0] + '\';document.destdroite.dist.value=\'' + tc[i][8] + '\';voirList(document.destdroite,document.destdroite.action.value,document.destdroite.destcadre.value);" title="' + titre + '">\r\n';
	texte = texte + '<img src="' + img_path + 'del.gif" width="28" height="28" alt="' + comment + '">'
	if (tc[i][13] == 1)
	{
		texte = texte + '</div>';
	}
	texte = texte + '</div>';
	if (tc[i][14] != 0)
	{
		texte = texte + '</div>';
	}
	if ((tc[i][13] == 0) && (tc[i][5] != 1))
	{
		texte = texte + '</div>';
	}
	if (tc[i][8] == 0)
	{
		texte = texte + '</div>';
	}
	if (tc[i][11] != 0)
	{
		texte = texte + '</div>';
	}
	if (tc[i][5] == 1)
	{
		texte = texte + '</div>';
	}

	if (isobjet == 1)
	{
		texte = texte + '</div>';
	}

	if (tc[i][4] != 0)
	{
		texte = texte + '</div>';
	}

	if (tc[i][3] != 0)
	{
		texte = texte + '</div>';
	}
	if (tc[i][12] != 0)
	{
		texte = texte + '</div>';
	}

	texte = texte + '</td>';


	document.write(texte);
	if (i < tc.length -1)
	{
		j = i + 1;
		if (y_encours != tc[j][2])
		{
			document.write('</tr>');
		}
	}
	else
	{
		document.write('</tr>');
	}
}
</script></form></table>
<?php }?>
<?php 
$vue_gauche = ob_get_contents();
ob_end_clean();
//ob_flush();
//$t->set_var('VUE_GAUCHE',$vue_gauche);

?>
