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
//$mod_perso_cod = 2;


$erreur = 0;
$req = "select compt_nom from compte where compt_cod = $compt_cod";
$db->query($req);
$db->next_record();
$compt_nom = $db->f("compt_nom");
$req = "select dcompt_modif_perso, dcompt_modif_gmon, dcompt_controle, dcompt_creer_monstre from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	$droit['modif_perso'] = 'N';
	$droit['modif_gmon'] = 'N';
	$droit['controle'] = 'N';
	$droit['creer_monstre'] = 'N';
}
else
{
	$db->next_record();
	$droit['modif_perso'] = $db->f("dcompt_modif_perso");
	$droit['modif_gmon'] = $db->f("dcompt_modif_gmon");
	$droit['controle'] = $db->f("dcompt_controle");
	$droit['creer_monstre'] = $db->f("dcompt_creer_monstre");
}
if ($droit['modif_perso'] != 'O')
{
	echo "<p>Erreur ! Vous n’avez pas accès à cette page !";
	$erreur = 1;
}
if ($erreur == 0)
{
	include "admin_edition_header.php";
	if(!isset($methode))
		$methode = 'debut';
	switch($methode)
	{
		case 'debut':
			?>
			Pour éditer un objet :<br>
			- Entrez le numéro du perso qui possède l’objet :
			<form method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode" value="perso"><input type="text" name="num_perso2">
			<input type="submit" class="test" value="Valider"></form>
			- Ou entrez directement le numéro de l’objet s’il est connu :
			<form method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode" value="objet"><input type="text" name="num_objet">
			<input type="submit" class="test" value="Valider"></form>
			<?php 
			break;
		case 'perso':
			$req = 'select obj_cod,obj_nom,obj_nom_generique
				from perso_objets,objets,objet_generique
				where perobj_perso_cod = ' . $num_perso2 . '
				and perobj_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod ';
			$db->query($req);
			if($db->nf() == 0)
				echo 'Aucun objet modifiable trouvé pour ce perso !';
			else
			{
				echo 'Liste des objets modifiables : <br>';
				while($db->next_record())
					echo '<a href="' . $PHP_SELF . '?methode=objet&num_objet=' . $db->f("obj_cod") . '">' . $db->f("obj_nom") . '</a><br>';
			}
			break;
		case 'objet':
			?>
			<form method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode" value="fin">
			<input type="hidden" name="num_objet" value="<?php echo $num_objet;?>">
			<?php 
            // Caractéristiques
			$req = 'select obj_nom,obj_nom_generique,tobj_libelle,tobj_cod,gobj_pa_normal,gobj_pa_eclair,obj_nom_porte,
				obj_description,obj_valeur,obj_etat,obj_des_degats,obj_val_des_degats,obj_bonus_degats,obj_armure,
				obj_distance,obj_chute,obj_poids,obj_usure,obj_poison,obj_vampire,obj_regen,obj_aura_feu,obj_bonus_vue,obj_critique,
				obj_critique,obj_seuil_force,obj_seuil_dex,obj_chance_drop,obj_enchantable,obj_desequipable, trouve_objet(obj_cod) as obj_position,
				obj_niveau_min
				from objets,objet_generique,type_objet
				where obj_cod = ' . $num_objet . '
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod';
			$db->query($req);
			$db->next_record();
			$tobj_cod = $db->f('tobj_cod');
			$is_distance = ($db->f('obj_distance') == 'O');
			?>
			<table>
				<tr>
					<td class="soustitre2">Position : </td>
					<td><?php echo $db->f('obj_position');?></td>
				</tr>
				<tr>
					<td class="soustitre2">Nom : </td>
					<td><input type="text" size="50" name="obj_nom" value="<?php echo $db->f('obj_nom');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Nom générique <br><em>(objet non identifié, quand il est au sol.  Un @ à la fin du nom signifie qu'il ne sera plus déséquipable): </em></td>
					<td><input type="text" size="50" name="obj_nom_generique" value="<?php echo $db->f('obj_nom_generique');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Nom porté <br><em>(dans la description d’un perso): </em></td>
					<td><input type="text" size="50" name="obj_nom_porte" value="<?php echo $db->f('obj_nom_porte');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Type d’objet <br><em>(non modifiable) </em></td>
					<td><?php echo $db->f('tobj_libelle');?></td>
				</tr>
				<tr>
					<td class="soustitre2">Cout en PA pour une attaque normale <br><em>(non modifiable, armes seulement) </em></td>
					<td><?php echo $db->f('gobj_pa_normal');?></td>
				</tr>
				<tr>
					<td class="soustitre2">Coût en PA pour une attaque foudroyante <br><em>(non modifiable, armes seulement) </em></td>
					<td><?php echo $db->f('gobj_pa_eclair');?></td>
				</tr>
				<tr>
					<td class="soustitre2">Description </td>
					<td><textarea name="obj_description"><?php echo $db->f('obj_description');?></textarea></td>
				</tr>
				<tr>
					<td class="soustitre2">Valeur : <br><em>(référence de prix pour les échoppes)</em></td>
					<td><input type="text" size="50" name="obj_valeur" value="<?php echo $db->f('obj_valeur');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Poids</td>
					<td><input type="text" size="5" name="obj_poids" value="<?php echo $db->f('obj_poids');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">État de l’objet : <br><em>(en %)</em></td>
					<td><input type="text" size="50" name="obj_etat" value="<?php echo  round($db->f('obj_etat'),2);?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Dégâts : <br><em>(armes uniquement)</em></td>
					<td><input type="text" size="5" name="obj_des_degats" value="<?php echo $db->f('obj_des_degats');?>">D
						<input type="text" size="5" name="obj_val_des_degats" value="<?php echo $db->f('obj_val_des_degats');?>">+
						<input type="text" size="5" name="obj_bonus_degats" value="<?php echo $db->f('obj_bonus_degats');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Seuil en dextérité : <br><em>(0 pour pas de seuil)</em></td>
					<td><input type="text" size="5" name="obj_seuil_dex" value="<?php echo $db->f('obj_seuil_dex');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Seuil en force : <br><em>(0 pour pas de seuil)</em></td>
					<td><input type="text" size="5" name="obj_seuil_force" value="<?php echo $db->f('obj_seuil_force');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Seuil en niveau : <br><em>(0 pour pas de seuil)</em></td>
					<td><input type="text" size="5" name="obj_niveau_min" value="<?php echo $db->f('obj_niveau_min');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Armure : <br><em>(armures et casques uniquement)</em></td>
					<td><input type="text" size="5" name="obj_armure" value="<?php echo $db->f('obj_armure');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Distance max : <br><em>(armes de distance uniquement)</em></td>
					<td><input type="text" size="5" name="obj_distance" value="<?php echo $db->f('obj_distance');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Chute : <br><em>(armes de distance uniquement)</em></td>
					<td><input type="text" size="5" name="obj_chute" value="<?php echo $db->f('obj_chute');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Taux d’usure : </td>
					<td><input type="text" size="5" name="obj_usure" value="<?php echo $db->f('obj_usure');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Régénération : </td>
					<td><input type="text" size="5" name="obj_regen" value="<?php echo $db->f('obj_regen');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Vampirisme : </td>
					<td><input type="text" size="5" name="obj_vampire" value="<?php echo $db->f('obj_vampire');?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Aura de feu : </td>
					<td><input type="text" size="5" name="obj_aura_feu" value="<?php echo $db->f('obj_aura_feu');?>"></td>
				</tr>
                <tr>
                    <td class="soustitre2">Bonus/malus à la vue</td>
                    <td><input type="text" name="obj_bonus_vue" value="<?php echo $db->f('obj_bonus_vue');?>"></td>
                </tr>
                <tr>
                    <td class="soustitre2">Protection contre les critiques (en %)</td>
                    <td><input type="text" name="obj_critique" value="<?php echo $db->f('obj_critique');?>"></td>
                </tr>
				<tr>
					<td class="soustitre2">Chance de drop à la mort (en %) : </td>
					<td><input type="text" size="5" name="obj_chance_drop" value="<?php echo $db->f('obj_chance_drop');?>"></td>
				</tr>
    			<tr>
					<td class="soustitre2">Enchantable ? (1 pour oui, 0 pour non, 2 pour déjà enchanté) </td>
					<td><input type="text" size="5" name="obj_enchantable" value="<?php echo $db->f('obj_enchantable');?>"></td>
				</tr>
    			<tr>
					<td class="soustitre2">Deséquipable ? (O pour oui, N pour non) </td>
					<td><input type="text" size="5" name="obj_desequipable" value="<?php echo $db->f('obj_desequipable');?>"></td>
				</tr>
    			<tr>
					<td class="soustitre2">Ajouter l’enchantement<br/>(irréversible, sauf à changer manuellement les caractéristiques) </td>
					<td>
						<select name='cree_enchantement'>
							<option selected='selected' value='-1'>Aucun nouvel enchantement</option>
<?php 			$categorie = 0;
			$premiere_categorie = true;
			$where = ' where ';
			switch ($tobj_cod)
			{
				case 1:	// arme
					if($is_distance)	//arme distance
						$where .= 'tenc_arme_distance = 1 ';
					else	// arme contact
						$where .= 'tenc_arme_contact = 1 ';
					break;
				case 2:	// armure
					$where .= 'tenc_armure = 1 ';
					break;
				case 4:	// casque
					$where .= 'tenc_casque = 1 ';
					break;
				case 6:	//artefact
					$where .= 'tenc_artefact = 1 ';
					break;
				default:	// autres cas : objet non enchantable
					$where .= '0 = 1';
					break;
			}
			$req = "select enc_cod, enc_nom || ' (' || enc_description || ')' as nom, enc_cout from enchantements
				inner join enc_type_objet on tenc_enc_cod = enc_cod $where
				order by enc_cout, enc_description";
			$db->query($req);
			while ($db->next_record())
			{
				if ($db->f('enc_cout') != $categorie)
				{
					$categorie = $db->f('enc_cout');
					$nom = '';
					switch ($categorie)
					{
						case 1000: $nom = 'Enchantements niveau 1'; break;
						case 2000: $nom = 'Enchantements niveau 2'; break;
						case 5000: $nom = 'Enchantements niveau 3'; break;
						default: $nom = 'Autres enchantements'; break;
					}
					if (!$premiere_categorie) echo "</optgroup>";
					$premiere_categorie = false;
					echo "<optgroup label='$nom'>";
				}
				echo '<option value="' . $db->f('enc_cod') . '">' . $db->f('nom') . '</option>';
			}
			if (!$premiere_categorie) echo "</optgroup>";
?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><center><input type="submit" class="test" value="Valider !"></td>
				</tr>
			</table>
			<?php 
			break;
		case 'fin':
			$req = "select compt_nom from compte where compt_cod = $compt_cod";
			$db->query($req);
			$db->next_record();
			$compt_nom = $db->f("compt_nom");
			$modifie = 0;
			$log = date("d/m/y - H:i")." - (compte $compt_cod / $compt_nom) modifie l’objet $num_objet\n";
			$fields = array(
				'obj_nom',
				'obj_nom_generique',
				'obj_nom_porte',
				'obj_description',
				'obj_valeur',
				'obj_poids',
				'obj_etat',
				'obj_des_degats',
				'obj_val_des_degats',
				'obj_bonus_degats',
				'obj_armure',
				'obj_distance',
				'obj_chute',
				'obj_usure',
				'obj_regen',
				'obj_vampire',
				'obj_aura_feu',
				'obj_bonus_vue',
				'obj_critique',
				'obj_seuil_dex',
				'obj_seuil_force',
				'obj_chance_drop',
				'obj_enchantable',
				'obj_desequipable',
				'obj_niveau_min'
				);
			$req_sel_obj = "select obj_cod";
			foreach ($fields as $i => $value)
				$req_sel_obj .= ",".$fields[$i];
			$req_sel_obj .= ' from objets where obj_cod = ' . $num_objet;
			$db->query($req_sel_obj);
			$db->next_record();
			foreach ($fields as $i => $value)
			{
				if($_POST[$fields[$i]] != $db->f($fields[$i]))
				{
					$log .= "Modification du champ ".$fields[$i]." : ".$db->f($fields[$i])." => ".$_POST[$fields[$i]]."\n";
					$modifie = 1;
				}
			}
			if ($modifie == 1)
			{
				$fieldsNum = array(
					'obj_valeur',
					'obj_poids',
					'obj_etat',
					'obj_des_degats',
					'obj_val_des_degats',
					'obj_bonus_degats',
					'obj_armure',
					'obj_chute',
					'obj_usure',
					'obj_regen',
					'obj_vampire',
					'obj_aura_feu',
					'obj_bonus_vue',
					'obj_critique',
					'obj_seuil_dex',
					'obj_seuil_force',
					'obj_chance_drop',
					'obj_enchantable',
					'obj_niveau_min'
					);
				$fieldsText = array(
					'obj_desequipable',
					'obj_distance'
					);
				if ($_POST['obj_nom'] != $db->f('obj_nom'))
				{
					$obj_nom = $_POST['obj_nom'];
				}
				if ($_POST['obj_nom_generique'] != $db->f('obj_nom_generique'))
				{
					$obj_nom_generique = $_POST['obj_nom_generique'];
				}
				if ($_POST['obj_description'] != $db->f('obj_description'))
				{
					$obj_description = $_POST['obj_description'];
				}
				if ($_POST['obj_nom_porte'] != $db->f('obj_nom_porte'))
				{
					$obj_nom_porte = $_POST['obj_nom_porte'];
				}
				$req = "update objets set obj_nom = e'" . pg_escape_string(str_replace("'", '’', $obj_nom)) . "'
					, obj_nom_generique = e'" . pg_escape_string(str_replace("'", '’', $obj_nom_generique)) . "'
					, obj_description = e'" . pg_escape_string(str_replace("'", '’', $obj_description)) . "'
					, obj_nom_porte = e'" . pg_escape_string(str_replace("'", '’', $obj_nom_porte)) . "'";
				foreach ($fieldsNum as $i => $value)
				{
					if($_POST[$fieldsNum[$i]] != '')
						$req .= ', ' . $fieldsNum[$i] . ' = ' . $_POST[$fieldsNum[$i]];
				}
				foreach ($fieldsText as $i => $value)
				{
					if($_POST[$fieldsText[$i]] != '')
						$req .= ', ' . $fieldsText[$i] . " = '" . $_POST[$fieldsText[$i]] . "'";
				}
				$req .= ' where obj_cod = ' . $num_objet;
				$db->query($req);
			}

			$cree_enchantement = $_POST['cree_enchantement'];
			if ($cree_enchantement != '-1')
			{
				$req = "select enc_nom || ' (' || enc_description || ')' as nom from enchantements where enc_cod = $cree_enchantement";
				$db->query($req);
				$db->next_record();
				$log .= "Ajout de l’enchantement " . $db->f('nom') . "\n";
				$modifie = 1;
				$req = "select f_enchantement(-1, $num_objet, $cree_enchantement, 3) ";
				$db->query($req);
			}

			if ($modifie == 1)
			{
				$db->query('update objets set obj_modifie = 1 where obj_cod = ' . $num_objet);
				writelog($log,'objet_edit');
				echo "Modification effectuée : <br /><pre>$log</pre>";
			}
			else
			{
				echo "Aucune modification";
			}
		break;
	}
}
?>
<p style="text-align:center;"><a href="<?php echo$PHP_SELF ?>">Retour au début</a></p>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
