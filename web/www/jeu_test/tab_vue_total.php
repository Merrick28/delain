<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
<link rel="stylesheet" type="text/css" href="style_vue_hc.php?num_etage=<?php  echo $num_etage; ?>">
<script type="text/javascript" src="../scripts/pop-in.js" ></script>
<div id='informations_case' class='bordiv' style='width:150px; padding:5px; display:none; position:absolute;'></div>

<?php 
$db2 = new base_delain;

if ($db->is_admin_monstre($compt_cod))
{
	echo("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\">");

	$req_y = "select distinct pos_y from positions where pos_etage = $num_etage order by pos_y desc";
	$db->query($req_y);
	$nb_res_y = $db->nf();

	$req = "select etage_affichage from etage where etage_numero = $num_etage ";
	$db->query($req);
	$db->next_record();
	$aff = $db->f('etage_affichage');

	$req_y = "select distinct pos_y from positions where pos_etage = $num_etage order by pos_y desc";
	$db->query($req_y);
?>
<script type="text/javascript">
	var carte = new Array();
<?php 	$i = 0;
	$depart = 0;

	while($db->next_record())
	{
		$req_map_vue = "select detail_carte_monstre($num_etage," . $db->f("pos_y") . "," . $depart . ") as vue ";
		$db2->query($req_map_vue);
		$db2->next_record();
		$tab = explode("#",$db2->f("vue"));
		echo $tab[0];
		$depart = $tab[1];
	}

?>

	var etage;
	var i;
	var y_encours;
	var code_image;
	var comment;
	var comment_light;
	var isobjet;
	var texte;
	var style;
	var action;
	var comments = new Array();
	var tr_en_cours;

<?php  echo("	img_path='" . str_replace(chr(92),chr(92) . chr(92),G_IMAGES) ."';\n"); ?>
	y_encours = -2000;
	tr_en_cours = '';
	for (i=0; i<carte.length; i++)
	{ 
		texte = '';
		comment = 'Position&nbsp;' + carte[i][0] + ' (' + carte[i][1] + ', ' + carte[i][2] + ')<br><a href=\'login_monstre_case.php?position=' + carte[i][0] + '\'>Voir en détail</a><br />';
		comment_light = 'Position&nbsp;' + carte[i][1] + ', ' + carte[i][2] + '. ';
		code_image = 0;
		if (y_encours != carte[i][2])
		{
			y_encours = carte[i][2];
			tr_en_cours = '<tr class="vueoff" height="10">\n<td height="10" class="coord">' + y_encours + '</td>\n';
		}
		
		style = 'caseVue v' + carte[i][10];
		
		texte = texte + '<td class="' + style + '">';
		
		if (carte[i][3] != 0)
		{
			comment = comment + carte[i][3] + '&nbsp;aventuriers<br>';
			comment_light = comment_light + carte[i][3] + '&nbsp;aventuriers, ';
			texte = texte + '<div class="joueur">';
		}
		
		if (carte[i][4] != 0)
		{
			comment = comment + carte[i][4] + '&nbsp;monstres<br><em>' + carte[i][14] + '</em>';
			comment_light = comment_light + carte[i][4] + '&nbsp;monstres, ' + carte[i][14];
			texte = texte + '<div class="monstre">';
			
		}
		
		if (carte[i][6] + carte[i][7] != 0)
		{
			texte = texte + '<div class="objet">';
		}
		
		if (carte[i][5] == 1)
		{
			texte = texte + '<div class="caseVue mur_' + carte[i][9] + '">';
		}
		if (carte[i][11] != 0)
		{
			texte = texte + '<div class="caseVue lieu' + carte[i][11] + '">';
		}
		if (carte[i][8] == 0)
		{
			texte = texte + '<div class="oncase caseVue">';
		}
		if (carte[i][12] != 0)
		{
			texte = texte + '<div  id="1" class="caseVue decor' + carte[i][12] + '">';
		}	
		if (carte[i][13] != 0)
		{
			texte = texte + '<div  id="1" class="caseVue decor' + carte[i][13] + '">';
		}
		comments[carte[i][0]] = comment;
		texte = texte + '<div onClick="changeInfo_tableau(document.getElementById(\'informations_case\'), comments, ' + [carte[i][0]] + ');" class="caseVue">';
		texte = texte + '<div id="cell' + carte[i][0] + '" class="pasvu caseVue" style="background:url(\'' + img_path + 'c1.gif\')" >';
		texte = texte + '<img src="' + img_path + 'del.gif" width="27" height="25" alt="' + comment_light + '" title="' + comment_light + '" />'
		texte = texte + '</div>';
		texte = texte + '</div>'; 
		if (carte[i][13] != 0)
		{
			texte = texte + '</div>';
		}
		
		if (carte[i][12] != 0)
		{
			texte = texte + '</div>';
		}
		
		if (carte[i][8] == 0)
		{
			texte = texte + '</div>';
		}
		
		if (carte[i][11] != 0)
		{
			texte = texte + '</div>';
		}
		
		if (carte[i][5] == 1)
		{
			texte = texte + '</div>';
		}
		
		if (carte[i][6] + carte[i][7] != 0)
		{
			texte = texte + '</div>';
		}
		
		if (carte[i][4] != 0)
		{
			texte = texte + '</div>';
		}
		
		if (carte[i][3] != 0)
		{
			texte = texte + '</div>';
		}

		texte = texte + '</td>\n';

		tr_en_cours += texte;
		if (i < carte.length -1)
		{
			j = i + 1;
			if (y_encours != carte[j][2])
			{
				tr_en_cours += '</tr>';
				document.write(tr_en_cours);
				tr_en_cours = '';
			}
		}
		else
		{
			tr_en_cours += '</tr>';
			document.write(tr_en_cours);
			tr_en_cours = '';
		}
	}
</script>
</table>
<?php 
}
else
{
	echo '<p>Erreur ! Vous n’avez pas les droits d’accès à cette page !</p>';
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
?>
