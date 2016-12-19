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
?>
<p class="titre">Édition d’un objet générique</p>
<?php 
$erreur = 0;
$req = "select dcompt_objet,dcompt_modif_perso,dcompt_modif_gmon,dcompt_controle from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	$droit['modif_perso'] = 'N';
	$droit['modif_gmon'] = 'N';
	$droit['controle'] = 'N';
	$droit['objet'] = 'N';
}
else
{
	$db->next_record();
	$droit['modif_perso'] = $db->f("dcompt_modif_perso");
	$droit['modif_gmon'] = $db->f("dcompt_modif_gmon");
	$droit['controle'] = $db->f("dcompt_controle");
	$droit['objet'] = $db->f("dcompt_objet");
}
if ($droit['objet'] != 'O')
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
else
{
	
	if(!isset($methode))
		$methode = "debut";
	switch($methode)
	{
		case "debut":
			?>
			<p>Choisissez votre méthode :</p>
			<a href="<?php echo $PHP_SELF;?>?methode=cre">Création d’un nouvel objet ?</a><br>
			<a href="<?php echo $PHP_SELF;?>?methode=mod">Modification d’un objet existant</a>
			<?php 
			break;
		case "cre": // création d'un nouvel objet
			?>
			<form name="cre" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode" value="cre2">
			<center><table>
			<tr>
				<td class="soustitre2">Nom de l’objet (identifié)</td>
				<td><input type="text" name="gobj_nom"></td>
			</tr>
			<tr>
				<td class="soustitre2">Nom de l’objet (non identifié)</td>
				<td><input type="text" name="gobj_nom_generique"></td>
			</tr>
			<tr>
				<td class="soustitre2">Type d’objet</td>
				<td><select name="gobj_tobj_cod">
				<?php 
				$req = "select tobj_libelle,tobj_cod from type_objet where tobj_cod not in (3,5,9,10) order by tobj_cod ";
				$db->query($req);
				while($db->next_record())
				{
					echo '<option value="' . $db->f("tobj_cod") . '">' . $db->f('tobj_libelle') . '</option>';
				}
				?>
				</select></td>
			</tr>
			<tr>
				<td class="soustitre2">Valeur</td>
				<td><input type="text" name="gobj_valeur"></td>
			</tr>
			<tr>
				<td class="soustitre2">Dégâts (armes uniquement)</td>
				<td><input type="text" size="5" name="obcar_des_degats"> D <input type="text" size="5" name="obcar_val_des_degats"> + <input type="text" size="5" name="obcar_bonus_degats"></td>
			</tr>
			<tr>
				<td class="soustitre2">Armure (armures uniquement)</td>
				<td><input type="text" name="obcar_armure"></td>
			</tr>
			<tr>
				<td class="soustitre2">Arme à distance ? (armes uniquement)</td>
				<td><select name="gobj_distance"><option value="O">Oui</option><option value="N">Non</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Distance max (armes à distance uniquement)</td>
				<td><input type="text" name="gobj_portee"></td>
			</tr>
			<tr>
				<td class="soustitre2">Chute (armes à distance uniquement)</td>
				<td><input type="text" name="obcar_chute"></td>
			</tr>
			<tr>
				<td class="soustitre2">Compétence utilisée (armes uniquement)</td>
				<td><select name="gobj_comp_cod">
					<option value="30">Mains nues</option>
				<?php 
				$req = "select comp_libelle,comp_cod from competences where comp_typc_cod in (6,7,8) order by comp_cod ";
				$db->query($req);
				while($db->next_record())
				{
					echo '<option value="' . $db->f("comp_cod") . '">' . $db->f('comp_libelle') . '</option>';
				}
				?>
				</select></td>
			</tr>
			<tr>
				<td class="soustitre2">Poids</td>
				<td><input type="text" name="gobj_poids"></td>
			</tr>
			<tr>
				<td class="soustitre2">Coût en PA pour une attaque normale (armes uniquement)</td>
				<td><input type="text" name="gobj_pa_normal"></td>
			</tr>
			<tr>
				<td class="soustitre2">Coût en PA pour une attaque foudroyante (armes uniquement)</td>
				<td><input type="text" name="gobj_pa_eclair"></td>
			</tr>
			<tr>
				<td class="soustitre2">Description</td>
				<td><textarea name="gobj_description"></textarea></td>
			</tr>
			<tr>
				<td class="soustitre2">Objet déposable ?</td>
				<td><select name="gobj_deposable"><option value="O">Oui</option><option value="N">Non</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Usure par utilisation</td>
				<td><input type="text" name="gobj_usure"></td>
			</tr>
			<tr>
				<td class="soustitre2">Vendable dans les échoppes ?</td>
				<td><select name="gobj_echoppe"><option value="O">Oui</option><option value="N">Non</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Vampirisme (armes uniquement) en numérique (ex : 0.2 pour 20%)</td>
				<td><input type="text" name="gobj_vampire"></td>
			</tr>
			<tr>
				<td class="soustitre2">Seuil d’utilisation en force</td>
				<td><input type="text" name="gobj_seuil_force"></td>
			</tr>
			<tr>
				<td class="soustitre2">Seuil d’utilisation en dextérité</td>
				<td><input type="text" name="gobj_seuil_dex"></td>
			</tr>
			<tr>
				<td class="soustitre2">Seuil d’utilisation en niveau</td>
				<td><input type="text" name="gobj_niveau_min"></td>
			</tr>
			<tr>
				<td class="soustitre2">Nombre de mains (armes uniquement)</td>
				<td><input type="text" name="gobj_nb_mains"></td>
			</tr>
			<tr>
				<td class="soustitre2">Bonus/malus à la régénération</td>
				<td><input type="text" name="gobj_regen"></td>
			</tr>
			<tr>
				<td class="soustitre2">Aura de feu - en numérique (ex : 0.2 pour 20%)</td>
				<td><input type="text" name="gobj_aura_feu"></td>
			</tr>
			<tr>
				<td class="soustitre2">Bonus/malus à la vue</td>
				<td><input type="text" name="gobj_bonus_vue"></td>
			</tr>
			<tr>
				<td class="soustitre2">Protection contre les critiques (en %)</td>
				<td><input type="text" name="gobj_critique"></td>
			</tr>
			<tr>
				<td class="soustitre2">Bonus à l’armure (artefacts et casques)</td>
				<td><input type="text" name="gobj_bonus_armure"></td>
			</tr>
			<tr>
				<td class="soustitre2">Chance de drop à la mort (en %)</td>
				<td><input type="text" name="gobj_chance_drop"></td>
			</tr>
			<tr>
				<td class="soustitre2">Chance d’avoir un objet enchantable (en %)</td>
				<td><input type="text" name="gobj_chance_enchant"></td>
			</tr>
			<tr>
				<td class="soustitre2">Stabilité (potion uniquement)</td>
				<td><input type="text" name="gobj_stabilite"></td>
			</tr>
			<tr>
				<td colspan="2"><center><input type="submit" class="test" value="Valider !"></center></td>
			</tr>
			
			</table></center>
			</form>
			<?php 
			break;
		case "mod": // modification d'un objet existant
			?>
			Choisissez l’objet à modifier :<br>
			<form name="mod" action="<?php echo $PHP_SELF;?>" method="post">
			<input type="hidden" name="methode" value="mod2">
			<select name="gobj_cod">

		<option value="">---------------</option>';
			<?php 
		$req = 'select tobj_libelle,gobj_cod,gobj_nom from objet_generique,type_objet
												where gobj_tobj_cod not in (3,5,9,10) 
												and gobj_tobj_cod = tobj_cod 
												order by gobj_tobj_cod,gobj_nom';
		$db->query($req);
		$ch = '';
		while($db->next_record())
		{
				if ($db->f('tobj_libelle') != $type_objet)
				{
					$ch .= '</optgroup><optgroup label="Type d’objet : ' . $db->f('tobj_libelle') . '">';
					$type_objet = $db->f('tobj_libelle');
				}
				$ch .= '<option value="' . $db->f('gobj_cod') . ';">' . $db->f('gobj_nom') . '</option>';
		}
			$ch = substr($ch,11);	
			echo $ch;			
			?>
			</select><br>
			<input type="submit" value="Valider" class="test">
			</form>
			<?php 
			break;
		case "mod2":
			$db2 = new base_delain;
			$db3 = new base_delain;
			$req = "select * from objet_generique
				where gobj_cod =  $gobj_cod ";
			$db->query($req);
			$db->next_record();
			if ($db->f("gobj_obcar_cod") != '')
			{
				$req = "select * from objets_caracs where obcar_cod = " . $db->f("gobj_obcar_cod");
				$db2->query($req);
				if ($db2->nf() != 0)
				{
					$db2->next_record();
					$obcar_cod = $db2->f("obcar_cod");
				}
				else
					$obcar_cod = 0;
				
			}
			else
				$obcar_cod = 0;
			?>
			<form name="cre" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode" value="mod3">
			<input type="hidden" name="objet" value="<?php echo $gobj_cod;?>">
			<input type="hidden" name="objet_car" value="<?php echo $obcar_cod;?>">
			<center><table>
			<tr>
				<td class="soustitre2">Nom de l’objet (identifié)</td>
				<td><input type="text" name="gobj_nom" value="<?php echo $db->f("gobj_nom");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Nom de l’objet (non identifié)</td>
				<td><input type="text" name="gobj_nom_generique" value="<?php echo $db->f("gobj_nom_generique");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Type d’objet</td>
				<td><select name="gobj_tobj_cod">
				<?php 
				$req = "select tobj_libelle,tobj_cod from type_objet where tobj_cod not in (3,5,9,10) order by tobj_cod ";
				$db3->query($req);
				while($db3->next_record())
				{
					echo '<option value="' . $db3->f("tobj_cod") . '" ';
					if ($db3->f('tobj_cod') == $db->f("gobj_tobj_cod"))
						echo " selected ";
					echo '>' . $db3->f('tobj_libelle') . '</option>';
				}
				?>
				</select></td>
			</tr>
			<tr>
				<td class="soustitre2">Valeur</td>
				<td><input type="text" name="gobj_valeur" value="<?php echo $db->f("gobj_valeur");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Dégâts (armes uniquement)</td>
				<td><input type="text" size="5" name="obcar_des_degats" value="<?php echo $db2->f("obcar_des_degats");?>"> D <input type="text" size="5" name="obcar_val_des_degats" value="<?php echo $db2->f("obcar_val_des_degats");?>"> + <input type="text" size="5" name="obcar_bonus_degats" value="<?php echo $db2->f("obcar_bonus_degats");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Armure (armures uniquement)</td>
				<td><input type="text" name="obcar_armure" value="<?php echo $db2->f("obcar_armure");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Arme à distance ? (armes uniquement)</td>
				<td><select name="gobj_distance"><option value="O"
				<?php 
					if ($db->f("gobj_distance") == 'O')
						echo " selected" ;
				?>
				>Oui</option><option value="N"
				<?php 
					if ($db->f("gobj_distance") == 'N')
						echo " selected" ;
				?>
				>Non</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Distance max (armes à distance uniquement)</td>
				<td><input type="text" name="gobj_portee" value="<?php echo $db->f("gobj_portee");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Chute (armes à distance uniquement)</td>
				<td><input type="text" name="obcar_chute" value="<?php echo $db2->f("obcar_chute");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Compétence utilisée (armes uniquement)</td>
				<td><select name="gobj_comp_cod">
					<option value="30"
					<?php 
						if ($db->f("gobj_comp_cod") == 30)
						echo " selected ";
					?>
						
					>Mains nues</option>
				<?php 
				$req = "select comp_libelle,comp_cod from competences where comp_typc_cod in (6,7,8) order by comp_cod ";
				$db3->query($req);
				while($db3->next_record())
				{
					echo '<option value="' . $db3->f("comp_cod") . '" ';
					if ($db3->f('comp_cod') == $db->f("gobj_comp_cod"))
						echo " selected ";
					echo '>' . $db3->f('comp_libelle') . '</option>';
				}
				?>
				</select></td>
			</tr>
			<tr>
				<td class="soustitre2">Poids</td>
				<td><input type="text" name="gobj_poids" value="<?php echo $db->f("gobj_poids");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Coût en PA pour une attaque normale (armes uniquement)</td>
				<td><input type="text" name="gobj_pa_normal" value="<?php echo $db->f("gobj_pa_normal");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Coût en PA pour une attaque foudroyante (armes uniquement)</td>
				<td><input type="text" name="gobj_pa_eclair" value="<?php echo $db->f("gobj_pa_eclair");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Description</td>
				<td><textarea name="gobj_description"><?php echo $db->f("gobj_description");?></textarea></td>
			</tr>
			<tr>
				<td class="soustitre2">Objet déposable ?</td>
				<td><select name="gobj_deposable"><option value="O"
				<?php 
					if ($db->f("gobj_deposable") == 'O')
						echo " selected" ;
				?>
				>Oui</option><option value="N"
				<?php 
					if ($db->f("gobj_deposable") == 'N')
						echo " selected" ;
				?>
				>Non</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Usure par utilisation</td>
				<td><input type="text" name="gobj_usure" value="<?php echo $db->f("gobj_usure");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Vendable dans les échoppes ?</td>
				<td><select name="gobj_echoppe"><option value="O"
				<?php 
					if ($db->f("gobj_echoppe") == 'O')
						echo " selected" ;
				?>
				>Oui</option><option value="N"
				<?php 
					if ($db->f("gobj_echoppe") == 'N')
						echo " selected" ;
				?>
				>Non</option></select></td>
			</tr>
			<tr>
				<td class="soustitre2">Vampirisme (armes uniquement) en numérique (ex : 0.2 pour 20%)</td>
				<td><input type="text" name="gobj_vampire" value="<?php echo $db->f("gobj_vampire");?>"></td>
			</tr>
			</tr>
			<tr>
				<td class="soustitre2">Seuil d’utilisation en force</td>
				<td><input type="text" name="gobj_seuil_force" value="<?php echo $db->f("gobj_seuil_force");?>"></td>
			</tr>
			</tr>
			<tr>
				<td class="soustitre2">Seuil d’utilisation en dextérité</td>
				<td><input type="text" name="gobj_seuil_dex" value="<?php echo $db->f("gobj_seuil_dex");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Seuil d’utilisation en niveau</td>
				<td><input type="text" name="gobj_niveau_min" value="<?php echo $db->f("gobj_niveau_min");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Nombre de mains (armes uniquement)</td>
				<td><input type="text" name="gobj_nb_mains" value="<?php echo $db->f("gobj_nb_mains");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Bonus/malus à la régénération</td>
				<td><input type="text" name="gobj_regen" value="<?php echo $db->f("gobj_regen");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Aura de feu - en numérique (ex : 0.2 pour 20%)</td>
				<td><input type="text" name="gobj_aura_feu" value="<?php echo $db->f("gobj_aura_feu");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Bonus/malus à la vue</td>
				<td><input type="text" name="gobj_bonus_vue" value="<?php echo $db->f("gobj_bonus_vue");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Protection contre les critiques (en %)</td>
				<td><input type="text" name="gobj_critique" value="<?php echo $db->f("gobj_critique");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Bonus à l’armure (artefacts et casques)</td>
				<td><input type="text" name="gobj_bonus_armure" value="<?php echo $db->f("gobj_bonus_armure");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Chance de drop à la mort (en %)</td>
				<td><input type="text" name="gobj_chance_drop" value="<?php echo $db->f("gobj_chance_drop");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Chance d’avoir un objet enchantable (en %)</td>
				<td><input type="text" name="gobj_chance_enchant" value="<?php echo $db->f("gobj_chance_enchant");?>"></td>
			</tr>
			<tr>
				<td class="soustitre2">Stabilité (potions uniquement)</td>
				<td><input type="text" name="gobj_stabilite" value="<?php echo $db->f("gobj_stabilite");?>"></td>
			</tr>
			<tr>
				<td colspan="2"><center><input type="submit" class="test" value="Valider !"></center></td>
			</tr>
			
			</table></center>
			</form>
		
		
		
		
			<?php 
			break;
		case "cre2":
			// détermination du obcar_cod
			$req = 'select nextval(\'seq_obcar_cod\') as resultat ';
			$db->query($req);
			$db->next_record();
			$obcar_cod = $db->f('resultat');
			// mise à 0 des valeurs vides pour objets_caracs
			$fields = array(
				'obcar_des_degats',
				'obcar_val_des_degats',
				'obcar_bonus_degats',
				'obcar_chute',
				'obcar_armure'
				);
			foreach ($fields as $i => $value)
			{
				if($_POST[$fields[$i]] == '')
					$_POST[$fields[$i]] = 0;
			}
			// insertion dans objets_caracs
			$req = "insert into objets_caracs
				(obcar_cod,obcar_des_degats,obcar_val_des_degats,obcar_bonus_degats,obcar_chute,obcar_armure)
				values
				(" . $obcar_cod . "," . $_POST['obcar_des_degats'] . "," . $_POST['obcar_val_des_degats'] . "," . $_POST['obcar_bonus_degats'] . "," . $_POST['obcar_chute'] . "," . $_POST['obcar_armure'] . ")";
			$db->query($req);
			// mise à 0 des valeurs vides pour objets_generique
			$fields = array(
				'gobj_valeur',
				'gobj_portee',
				'gobj_poids',
				'gobj_pa_normal',
				'gobj_pa_eclair',
				'gobj_usure',
				'gobj_vampire',
				'gobj_seuil_force',
				'gobj_seuil_dex',
				'gobj_nb_mains',
				'gobj_aura_feu',
				'gobj_bonus_vue',
				'gobj_critique',
				'gobj_bonus_armure',
				'gobj_regen',
				'gobj_chance_drop',
				'gobj_chance_enchant',
				'gobj_stabilite',
				'gobj_niveau_min'
				);
			foreach ($fields as $i => $value)
			{
				if($_POST[$fields[$i]] == '')
					$_POST[$fields[$i]] = 0;
			}			
			// insertion dans objets_generique
			$req = "insert into objet_generique
				(gobj_obcar_cod,gobj_nom,gobj_nom_generique,gobj_tobj_cod,gobj_valeur,gobj_distance,gobj_portee,gobj_comp_cod,gobj_poids,
				gobj_pa_normal,gobj_pa_eclair,gobj_description,gobj_deposable,gobj_usure,gobj_echoppe,gobj_vampire,
				gobj_seuil_force,gobj_seuil_dex,gobj_nb_mains,gobj_regen,gobj_aura_feu,gobj_bonus_vue,gobj_critique,gobj_bonus_armure,
				gobj_chance_drop,gobj_chance_enchant,gobj_stabilite, gobj_niveau_min)
				values
				($obcar_cod,e'" . pg_escape_string($gobj_nom) . "',e'" . pg_escape_string($gobj_nom_generique) . "'," . $_POST['gobj_tobj_cod'] . "," . $_POST['gobj_valeur'] .
				",'$gobj_distance'," . $_POST['gobj_portee'] . "," .$_POST['gobj_comp_cod'] . "," . $_POST['gobj_poids'] . "," . $_POST['gobj_pa_normal'] . "," . 
				$_POST['gobj_pa_eclair'] . ",e'" . pg_escape_string($gobj_description) . "','$gobj_deposable'," . $_POST['gobj_usure'] . ",'$gobj_echoppe'," . 
				$_POST['gobj_vampire'] . ",	" . $_POST['gobj_seuil_force'] . "," . $_POST['gobj_seuil_dex'] . "," . $_POST['gobj_nb_mains'] . "," . $_POST['gobj_regen'] . 
				"," . $_POST['gobj_aura_feu'] . "," . $_POST['gobj_bonus_vue'] . "," . $_POST['gobj_critique'] . "," . $_POST['gobj_bonus_armure'] . "," . $_POST['gobj_chance_drop'] .
				"," . $_POST['gobj_chance_enchant'] . "," . $_POST['gobj_stabilite'] . ", " . $_POST['gobj_niveau_min'] . ") ";
			$db->query($req);
			echo "<p>L'insertion s'est bien déroulée.";
			break;
		case "mod3":
			// détermination du obcar_cod
			$obcar_cod = $_POST['objet_car'];
			// mise à 0 des valeurs vides pour objets_caracs
			$fields = array(
				'obcar_des_degats',
				'obcar_val_des_degats',
				'obcar_bonus_degats',
				'obcar_chute',
				'obcar_armure'
				);
			foreach ($fields as $i => $value)
			{
				if($_POST[$fields[$i]] == '')
					$_POST[$fields[$i]] = 0;
			}
			// update dans objets_caracs
			$req = "update objets_caracs
				set obcar_des_degats = " . $_POST['obcar_des_degats'] . ",obcar_val_des_degats = " . $_POST['obcar_val_des_degats'] . ",
				obcar_bonus_degats = " . $_POST['obcar_bonus_degats'] . ",obcar_chute = " . $_POST['obcar_chute'] . ",obcar_armure = " . $_POST['obcar_armure'] . "
				where obcar_cod = $obcar_cod";
			$db->query($req);			
			// mise à 0 des valeurs vides pour objets_generique
			$fields = array(
				'gobj_valeur',
				'gobj_portee',
				'gobj_poids',
				'gobj_pa_normal',
				'gobj_pa_eclair',
				'gobj_usure',
				'gobj_vampire',
				'gobj_seuil_force',
				'gobj_seuil_dex',
				'gobj_nb_mains',
				'gobj_aura_feu',
				'gobj_bonus_vue',
				'gobj_critique',
				'gobj_bonus_armure',
				'gobj_regen',
				'gobj_chance_drop',
				'gobj_chance_enchant',
				'gobj_stabilite',
				'gobj_niveau_min'
				);
			foreach ($fields as $i => $value)
			{
				if($_POST[$fields[$i]] == '')
					$_POST[$fields[$i]] = 0;
			}			
			// insertion dans objets_generique
			$req = "update objet_generique
				set gobj_nom = e'" . pg_escape_string($gobj_nom) . "',gobj_nom_generique = e'" . pg_escape_string($gobj_nom_generique) . "',gobj_tobj_cod = " . $_POST['gobj_tobj_cod'] . ",
				gobj_obcar_cod = $obcar_cod, gobj_valeur = " . $_POST['gobj_valeur'] . ", gobj_distance='$gobj_distance',gobj_portee = " . $_POST['gobj_portee'] . ",
				gobj_comp_cod = " . $_POST['gobj_comp_cod'] .", gobj_poids = " . $_POST['gobj_poids'] . ",
				gobj_pa_normal = " . $_POST['gobj_pa_normal'] . ",gobj_pa_eclair = " . $_POST['gobj_pa_eclair'] . ",gobj_description=e'" . pg_escape_string($gobj_description) . "',
				gobj_deposable = '$gobj_deposable',gobj_usure = " . $_POST['gobj_usure'] . ",gobj_echoppe = '$gobj_echoppe',
				gobj_vampire = " . $_POST['gobj_vampire'] . ",gobj_seuil_force = " . $_POST['gobj_seuil_force'] . ",gobj_seuil_dex = " . $_POST['gobj_seuil_dex'] . ",
				gobj_nb_mains = " . $_POST['gobj_nb_mains'] . ",gobj_regen = " . $_POST['gobj_regen'] . ",gobj_aura_feu = " . $_POST['gobj_aura_feu'] . ",
				gobj_bonus_vue = " . $_POST['gobj_bonus_vue'] . ",gobj_critique = " . $_POST['gobj_critique'] . ",gobj_bonus_armure = " . $_POST['gobj_bonus_armure'] .",
				gobj_chance_drop = " . $_POST['gobj_chance_drop'] . ", gobj_chance_enchant = " . $_POST['gobj_chance_enchant'] . ", gobj_stabilite = " . $_POST['gobj_stabilite'] . ", 
				gobj_niveau_min = " . $_POST['gobj_niveau_min'] . " where gobj_cod = $objet ";
			$db->query($req);
			echo "<p>L’insertion s’est bien déroulée.";
			//MAJ des objets individuels déjà existants. ATTENTION, certains champs ne sont bizarrement pas présents !
			$req = "update objets set obj_nom = e'" . pg_escape_string($gobj_nom) . "',obj_nom_generique = e'" . pg_escape_string($gobj_nom_generique) . "',
			obj_des_degats = " . $_POST['obcar_des_degats'] . ",obj_val_des_degats = " . $_POST['obcar_val_des_degats'] .",obj_bonus_degats = " . $_POST['obcar_bonus_degats'] . ",
			obj_valeur = " . $_POST['gobj_valeur'] . ",obj_distance='$gobj_distance',obj_portee = " . $_POST['gobj_portee'] . ",
			obj_poids = " . $_POST['gobj_poids'] . ",obj_description=e'" . pg_escape_string($gobj_description) . "',obj_deposable = '$gobj_deposable',
			obj_usure = " . $_POST['gobj_usure'] . ",obj_vampire = " . $_POST['gobj_vampire'] . ",obj_seuil_force = " . $_POST['gobj_seuil_force'] . ",
			obj_seuil_dex = " . $_POST['gobj_seuil_dex'] . ",obj_regen = " . $_POST['gobj_regen'] . ",obj_aura_feu = " . $_POST['gobj_aura_feu'] . ",
			obj_bonus_vue = " . $_POST['gobj_bonus_vue'] . ",obj_critique = " . $_POST['gobj_critique'] . ",
			obj_chance_drop = " . $_POST['gobj_chance_drop'] . ",obj_stabilite = " . $_POST['gobj_stabilite'] . ",obj_niveau_min = " . $_POST['gobj_niveau_min'] . "
			where obj_gobj_cod = $objet and obj_modifie = 0";
			$db->query($req);
			echo "<p><br>La mise à jour des anciens objets aussi";
			break;

	}
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
