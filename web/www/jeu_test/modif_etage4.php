<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$erreur = 0;
$req = "select dcompt_modif_carte from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	$droit['carte'] = 'N';
}
else
{
	$db->next_record();
	$droit['carte'] = $db->f("dcompt_modif_carte");
}
if ($droit['carte'] != 'O')
{
	die("<p>Erreur ! Vous n'avez pas accès à cette page !");
}
$db2 = new base_delain;
$db3 = new base_delain;
if (!isset($methode))
{
	$methode = 'debut';
}
include "tab_haut.php";
if ($erreur == 0)
{

// POSITION DU JOUEUR
$req_matos = "select pos_x,pos_y,pos_etage,pos_cod "
	."from perso_position,positions "
	."where ppos_perso_cod = $perso_cod"
	."and ppos_pos_cod = pos_cod ";
$db->query($req_matos);
$db->next_record();
$perso_pos_x = $db->f("pos_x");
$perso_pos_y = $db->f("pos_y");
$perso_pos_etage = $db->f("pos_etage");
$perso_pos_cod = $db->f("pos_cod");

if(isset($_POST['methode'])){
	switch ($methode) {
		case "aj_composant":
			$list = split(";", $_POST['pos_codes']);
			for($i=0;$i<count($list);$i++)
			{
				$temp_pos_cod = $list[$i];
				if($temp_pos_cod){
					$req = "insert into ingredient_position (ingrpos_pos_cod,ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea,ingrpos_qte)
						values ($temp_pos_cod, $liste, ".$_POST['quantite'].", ".$_POST['pourcentage'].",0)";
					$db->query($req);
				}
			}
		break;
		case "suppr":
			$list = split(";", $_POST['pos_codes']);
			for($i=0;$i<count($list);$i++)
			{
				$temp_pos_cod = $list[$i];
				if($temp_pos_cod){
					$req = "delete from ingredient_position where ingrpos_pos_cod = ".$temp_pos_cod;
					$db->query($req);
				}
			}
		break;

	}
}


?>
<p> Création / Modification des champs de composants </p>
<hr>
Choix de l'étage:
<form method="post">
	Etage : <select name="pos_etage">
<?php 
	$req = "select etage_numero,etage_libelle,etage_reference from etage order by etage_reference desc, etage_numero asc ";
	$db->query($req);
	while($db->next_record())
	{
		$sel = "";
		if($pos_etage == $db->f("etage_numero")){
			$sel = "selected";
		}
        $reference = ($db->f("etage_numero") == $db->f("etage_reference"));
		echo "<option value=\"" , $db->f("etage_numero") , "\" $sel>" , ($reference?'':' |-- '),$db->f("etage_libelle") , "</option>";
	}
?>
	</select><br>
	<input type="submit" value="Valider">
</form>
<hr>

<hr>
<?php 
$sel_etage = $perso_pos_etage;
if(isset($_POST['pos_etage'])){
   $sel_etage = $_POST['pos_etage'];
}

$req_compo = "select ingrpos_gobj_cod, count(*) as nb
	from ingredient_position
	inner join positions on pos_cod = ingrpos_pos_cod
	where  pos_etage = $sel_etage
	group by ingrpos_gobj_cod";
$db->query($req_compo);
$tab_compo = array();
while ($db->next_record())
	$tab_compo[$db->f('ingrpos_gobj_cod')] = $db->f('nb');

?>
Ajouter des composants dans le sol.

Cette opération ne rajoute pas directement les composants, mais détermine un champ de composants, c'est à dire la valeur max de composants que la case va pouvoir contenir, ainsi que le pourcentage de repousse.
<br><br>Table de description des composants présents :<br>
	<table border="1">
	<tr>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#FFFF99"></div></td><td>Pomme (<?php echo  $tab_compo[562]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#FFFF00"></div></td><td>Mandragore (<?php echo  $tab_compo[563]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#FFCCCC"></div></td><td>Absinthe (<?php echo  $tab_compo[647]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#9933CC"></div></td><td>Ache Des Marais (<?php echo  $tab_compo[648]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#FF33CC"></div></td><td>Aconit (<?php echo  $tab_compo[649]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#66FFCC"></div></td><td>Bardane (<?php echo  $tab_compo[651]; ?>)</td>
	</tr>
	<tr>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#9966FF"></div></td><td>Belladone (<?php echo  $tab_compo[652]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#660000"></div></td><td>Chene Rouvre (<?php echo  $tab_compo[653]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#FF0000"></div></td><td>Alkegenge (<?php echo  $tab_compo[650]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#66FF00"></div></td><td>Digitale (<?php echo  $tab_compo[654]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#006600"></div></td><td>Pavot (<?php echo  $tab_compo[660]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#CCFFFF"></div></td><td>Serpolet (<?php echo  $tab_compo[661]; ?>)</td>
	</tr>
	<tr>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#0000FF"></div></td><td>Menthe (<?php echo  $tab_compo[657]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#000066"></div></td><td>Millepertuis (<?php echo  $tab_compo[658]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#CCFF66"></div></td><td>Noyer (<?php echo  $tab_compo[659]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#003333"></div></td><td>Gentiane (<?php echo  $tab_compo[655]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#C0C0C0"></div></td><td>Jusquiame (<?php echo  $tab_compo[656]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px">
			<table  border="0">
				<tr><td width="10" height="10" style="background:#66FFFF"></td><td width="10" height="10"  style="background:#663399"></td></tr>
				<tr><td width="10" height="10"  style="background:#663399"></td><td width="10" height="10"  style="background:#66FFFF"></td></tr>
			</table>
		</td><td>Plusieurs composants</td>
	</tr>
	<tr>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#E1E6FA"></div></td><td>Herbe de lune (<?php echo  $tab_compo[722]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#ABC8E2"></div></td><td>Léonine sucrée (<?php echo  $tab_compo[721]; ?>)</td>
		<td width="20" height="20"><div style="width:25px;height:25px;background:#B78178"></div></td><td>Pissenlit de vin (<?php echo  $tab_compo[720]; ?>)</td>
	</tr>
	</table>
<br>
<form name="action_mur" method="post">
	<select name="methode">
		<option value="aj_composant" <?php if($_POST['methode'] == "aj_composant") echo "selected";?>>Ajouter un champ de composants dans le sol</option>
		<option value="suppr" <?php if($_POST['methode'] == "suppr") echo "selected";?>>Supprimer le composant présent</option>
	</select>
	Liste des ingrédients : <select name="liste">
	<?php 
		$req = "select gobj_nom,gobj_cod from objet_generique where gobj_tobj_cod = 22 order by gobj_nom asc ";
		$db->query($req);
		while($db->next_record())
		{
			$sel = "";
			if($liste == $db->f("gobj_cod")){
				$sel = "selected";
			}
			echo "<option value=\"" , $db->f("gobj_cod") , "\" $sel>" , $db->f("gobj_nom") , "</option>";
		}
	?>
	</select><br>
	Quantité :<input type="text" name="quantite" value="10">
	Pourcentage de repousse :<input type="text" name="pourcentage" value="10">
	Rayon du pinceau :<input type="text" name="largeurPinceau" value="0">
	<input type="hidden" name="pos_cod" value="">
	<input type="hidden" name="pos_etage" value="<?php echo $sel_etage?>">
	<input type="hidden" name="pos_codes" value=";">
	Blocs modifiés:<input type="text" name="numpos" value="0" readonly>
	<input type="submit" value="Modifier !">
</form>
<script>
<!--
var coord = new Array();
function valActionMur(pcod)
{
	var rayon = parseInt(document.action_mur.largeurPinceau.value);
	var x = parseInt(coord[pcod][0]);
	var y = parseInt(coord[pcod][1]);

	for ( var i = x - rayon ; i < x + rayon + 1 ; i++)
	{
		for ( var j = y - rayon ; j < y + rayon + 1 ; j++)
		{
			if (Math.sqrt((x - i) * (x - i) + (y - j) * (y - j)) <= rayon)
			{
				var pcod_temp = trouvePCod(i, j);
				if (pcod_temp > 0)
				{
					var existeDeja = document.action_mur.pos_codes.value.indexOf(";" + pcod_temp + ";") >= 0;
					if (existeDeja)
					{
						changeBlockColor("pos_" + pcod_temp,"#FFFFFF");
						document.action_mur.pos_codes.value = document.action_mur.pos_codes.value.replace(";" + pcod_temp + ";", ";");
						document.action_mur.numpos.value--;
					}
					else
					{
						changeBlockColor("pos_" + pcod_temp,"#0000FF");
						document.action_mur.pos_codes.value += pcod_temp + ";";
						document.action_mur.numpos.value++;
					}
				}
			}
		}
	}
}
function trouvePCod(x, y)
{
	var resultat = -1;
	for (var pos in coord)
	{
		if (coord[pos][0] == x && coord[pos][1] == y)
	 		return pos;
	}
	return resultat;
}
function changeBlockColor(nr,color)
{
	if (document.layers)
	{
		document.layers[nr].background = color;
	}
	else if (document.all)
	{
		document.all[nr].style.background = color;
	}
	else if (document.getElementById)
	{
		document.getElementById(nr).style.background = color;
	}
}
-->
</script>


<b>Sélectionner une case sur la carte pour valider</b>
<br>
<br><b>ATTENTION :</b> l'action de suppression supprime tous les types de composants présent sur une case
<br> Il est possible de positionner des composants sous un mur ... Si mur non creusable ... c'est idiot ... si creusable, les composants seront récupérables après creusage.
<br> Il faut éviter de mettre trop de types de composants sur une même case, 4 semble être un maximum (lisibilité et pour favoriser la découverte)





	<table border="1">
	<tr>
<?php 

/* Tableau des couleurs */
$couleurs = array(
'562' => '#FFFF99',
'563' => '#FFFF00',
'647' => '#FFCCCC',
'648' => '#9933CC',
'649' => '#FF33CC',
'650' => '#FF0000',
'651' => '#66FFCC',
'652' => '#9966FF',
'653' => '#660000',
'654' => '#66FF00',
'655' => '#003333',
'656' => '#C0C0C0',
'657' => '#0000FF',
'658' => '#000066',
'659' => '#CCFF66',
'660' => '#006600',
'661' => '#CCFFFF',
'722' => '#E1E6FA',
'721' => '#ABC8E2',
'720' => '#B78178',
);

$req_position = "select pos_cod, pos_x, pos_y, coalesce(ingrpos_gobj_cod, -1) as ingrpos_gobj_cod, gobj_nom, ingrpos_max, ingrpos_chance_crea, coalesce(mur_pos_cod, -1) as mur_pos_cod, mur_creusable
	from positions
	left outer join ingredient_position on ingrpos_pos_cod = pos_cod
	left outer join objet_generique on gobj_cod = ingrpos_gobj_cod
	left outer join murs on mur_pos_cod = pos_cod
	where  pos_etage = $sel_etage
	order by pos_y desc, pos_x asc";
$db->query($req_position);
$db->next_record();
$caseEnCours = -1;
$continuer = true;
$p_y = $db->f("pos_y");
$js_coord = '';

while ($continuer)
{
	if($db->f("pos_y") != $p_y)
	{
?>
	</tr><tr>
<?php 
	}
	$p_y = $db->f("pos_y");
	$position = $db->f("pos_cod");
	$caseEnCours = $position;
	$nbCouleurs = ($db->f("ingrpos_gobj_cod") > 0) ? 1 : 0;
	
	if ($db->f("mur_pos_cod") == -1)
		$js_coord .= "coord[$position] = [" . $db->f("pos_x") . ", " . $db->f("pos_y") . "];\n";

	if ($nbCouleurs == 0 || $db->f("mur_pos_cod") > 0)
	{
		if($db->f("mur_pos_cod") > 0)
		{
			if($db->f("mur_creusable") == 'O')
			{
				$color = "#696969";
			}
			else
			{
				$color = "#000000";
			}
		}
		else
			$color = "#FFFFFF";
		
	?>
		<td width="20" height="20"><div id="pos_<?php echo  $position;?>" style="width:25px;height:25px;background:<?php echo $color?>;" onClick="valActionMur(<?php echo  $db->f("pos_cod");?>);"></div>
		</td>
	<?php 
		$db->next_record();
	}
	else
	{
		$ingredientsArray = array();
		$texte = $db->f('gobj_nom') . ' (' . $db->f('ingrpos_max') . ', ' . $db->f('ingrpos_chance_crea') . "\n";
		$ingredientsArray[] = $db->f("ingrpos_gobj_cod");
		while ($db->next_record() && $db->f('pos_cod') == $caseEnCours)
		{
			$ingredientsArray[] = $db->f("ingrpos_gobj_cod");
			$texte .= $db->f('gobj_nom') . ' (' . $db->f('ingrpos_max') . ', ' . $db->f('ingrpos_chance_crea') . "\n";
		}
		$nbCouleurs = sizeof($ingredientsArray);
		$taille = (25 / $nbCouleurs);
		$color = "#00FF00";
		
		?>
		<td width="20" height="20"><div id="pos_<?php echo  $position;?>" style="width:25px;height:25px" onClick="valActionMur(<?php echo  $position;?>);" title="<?php echo  $texte;?>">
		<table  cellspacing="0">
		<?php  /*On initialise le tableau secondaire qu'on va remplir */
		for ($cote = 0 ; $cote < $nbCouleurs ; $cote++)
		{
			?>
			<tr>
			<?php 
			for ($j = $cote ; $j < ($nbCouleurs + $cote) ; $j++)
			{
			?>
			<td width="<?php echo $taille; ?>" height="<?php echo $taille; ?>"  id="x<?php echo  $j;?><?php echo  $cote;?>pos_<?php echo  $position;?>" style="background:<?php echo $couleurs[$ingredientsArray[$j%$nbCouleurs]]; ?>;"></td>
			<?php 
			}
			?>
			</tr>
			<?php 
		}
		?>
		</table></div></td>
		<?php 
	}
	$continuer = ($db->f("pos_cod")) && ($db->f("pos_cod") > 0);
}
?>
	</tr>
	</table>
<?php 
echo "<script type='text/javascript'>$js_coord</script>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
