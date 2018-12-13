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
	// initialisation de la méthode
	if(!isset($methode2))
		$methode2 = 'debut';
	switch($methode2)
	{
		case "debut":
			?>
			<table>
			<?php 
			$req = 'select gobj_cod,gobj_nom,gobj_description,gobj_tobj_cod,tobj_libelle from objet_generique,type_objet
											where gobj_tobj_cod = tobj_cod
											and gobj_cod in (select oenc_gobj_cod from enc_objets)
											and not exists (select 1 from formule_produit where frmpr_gobj_cod = gobj_cod ) 
											order by tobj_libelle,gobj_nom';
			$db->query($req);
			echo '<br><hr><td class="titre">Liste des composants d\'enchantement sans formule de création</td><br><br><table>
						<td><strong>Nom du composant</strong></td><td><strong>Type d\'objet</strong></td>';
			while ($db->next_record())
				{
						echo '<tr><td class="soustitre2"><br><a href="' . $PHP_SELF . '?methode2=ajout&pot=' . $db->f('gobj_cod') . '">' . $db->f('gobj_nom') . '</a></td>
						<td class="soustitre2">'.$db->f('tobj_libelle').'</td></tr>';

				}
			?>
			</table>
			<hr><a href="<?php echo $PHP_SELF;?>?methode2=ajout">Ou ajouter une nouvelle formule permettant d'obtenir un composant d'enchantement</a>
			<?php 
			$req = 'select 	frmpr_frm_cod,frmpr_gobj_cod,frmpr_num,frm_cod,frm_type,frm_nom,frm_comp_cod,frm_temps_travail from formule_produit,formule where frm_type = 3 and frm_cod = frmpr_frm_cod order by frm_nom ';
			$db->query($req);
			echo '<br><table><td class="titre">Liste des Composants déjà reliés à une pierre précieuse :</td><tr><br><br>
						<td><strong>Nom du composant</strong></td><td><strong>Objet nécessaire et quantités </strong></td><td><strong>Energie nécessaire</strong></td><td><strong>Compétence nécessaire</strong></td>';
			while ($db->next_record())
				{
						$cod_enchantement = $db->f("frm_cod");
						$comp = $db->f('frm_comp_cod');
						echo '<tr><td class="soustitre2"><br><a href="' . $PHP_SELF . '?methode2=modif&pot=' . $cod_enchantement . '">' . $db->f('frm_nom') . '</a></td>';
						if ($db->nf() != 0)
						{
								$req_composant = "select 	frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom from formule_composant,objet_generique	
														where frmco_frm_cod = $cod_enchantement 
														and frmco_gobj_cod = gobj_cod";
								$db2->query($req_composant);
								echo "<td>";
								while ($db2->next_record())
								{
										echo $db2->f('gobj_nom') ." \t". $db2->f('frmco_num') ."<br>";
								}

								echo "</td><td class=\"soustitre2\">" . $db->f('frm_temps_travail') . "</td>";
								$req_comp = "select comp_libelle from competences	
														where comp_cod = ". $comp;
								$db2->query($req_comp);
								$db2->next_record();
								echo "	<td class=\"soustitre2\">" . $db2->f('comp_libelle') . "</td></tr>";
						}
				}
			?>
			</table>

			<?php 
			break;
		case "ajout":
					$req = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where gobj_cod in (select oenc_gobj_cod from enc_objets)';
					if ($pot != null)
					{						
						$req .=	'and gobj_cod = '. $pot; 
					}
					$req .=	'order by gobj_nom';
					$db->query($req);
					$db->next_record();
			?>
			<table>
			<form name="ajout" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode2" value="ajout2">
				<tr>
					<td class="soustitre2">Nom / Description de la formule du composant (conserver le nom du composant dedans)</td>
					<td><textarea cols="50" rows="10"  name="nom"><?php echo $db->f("gobj_nom")?></textarea></td>
				</tr>
				<tr>
					<td class="soustitre2">Energie nécessaire <i>(Le coût en énergie sera celui qui fera diminuer la jauge d'énergie)</i></td>
					<td><input type="text" name="temps" value="40"></td>
				</tr>
				<tr>
					<td class="soustitre2">Cout en brouzoufs <i>non utilisé</i></td>
					<td><input type="text" name="pot_cout" value="0"></td>
				</tr>
				<tr>
					<td class="soustitre2">Résultat <i>(Non utilisé pour l'instant)</i></td>
					<td><input type="text" name="resultat" value="0"></td>
				</tr>
				<tr>
					<td class="soustitre2">Compétence</i></td>
					<td>
						<select name="competence">
							<option value="88">Forgeamage Niveau 1</option>';
							<option value="102">Forgeamage Niveau 2</option>';
							<option value="103">Forgeamage Niveau 3</option>';							
						</select>
							<i> <br>Cela correspond au niveau de forgeamage nécessaire. 
								<br>Mais on peut imaginer plusieurs formules pour un même composant, avec des compétences différentes / <br><strong> Pas sûr que cela marche pour l'instant !</strong></i>
							
					</td>
				</tr>
				<tr>
					<td class="soustitre2">Composant d'enchantement concerné</i></td>
					<td>
					<select name="composant">
					<?php  
					$req = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where gobj_cod in (select oenc_gobj_cod from enc_objets)';
					if ($pot != null)
					{						
						$req .=	'and gobj_cod = '. $pot; 
					}
					$req .=	'order by gobj_nom';
					$db->query($req);
					while($db->next_record())
					{
						echo '<option value="'. $db->f("gobj_cod") .'"> '. $db->f("gobj_nom") .'</option>';
					}
					echo '</select><br>';?>
					</td>
				</tr>
				<tr>
					<td class="soustitre2">Nombre de composants produits <i>(Non utilisé pour l'instant)</i></td>
					<td><input type="text" name="nombre" value="1"></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="test" value="Valider"></td>
				</tr>
			</form>
			</table>
			<?php 
			break;
		case "ajout2":
				$req_form_cod = "select nextval('seq_frm_cod') as numero";						
				$db->query($req_form_cod );
				$db->next_record();
				$num_form = $db->f("numero");
				$req = 'insert into formule
								(frm_cod,frm_type,frm_nom,frm_temps_travail,frm_cout,frm_resultat,frm_comp_cod)
								values('. $num_form .',3,e\'' . pg_escape_string($_POST['nom']) . '\','. $_POST['temps'] .','. $_POST['pot_cout'] .','. $_POST['resultat'] .','. $_POST['competence'] .')';
				$db->query($req);
				$req = 'insert into formule_produit
								(frmpr_frm_cod,frmpr_gobj_cod,frmpr_num)
								values('. $num_form .','. $_POST['composant'] .','. $_POST['nombre'] .')';
				$db->query($req);				
				echo "<p>La formule de base du composant d'enchantement a bien été insérée !<br>
				Pensez à inclure la pierre précieuse nécessaire pour ce composant. Autrement, il ne pourra jamais être produit<br>";
				?><a href="<?php echo $PHP_SELF;?>?methode2=serie_obj&pot=<?php echo $num_form;?>">Modifier la pierre précieuse associée à ce composant</a><br>
			<strong>Règle pour un composant d'enchantement :</strong>
			<br>Un composant est produit à partir d'une seule pierre précieuse. Pas d'autre règle pour les objets. L'énergie nécessaire est déterminée à partir de la formule de base (écran précédent)
			<br><hr>
			<?php 
			if(!isset($action))
				$action = '';
			if($action == 'ajout')
			{
				$req = " insert into formule_composant (frmco_frm_cod,frmco_gobj_cod,frmco_num) values ($pot,$gobj,$nombre)";
				$db->query($req);
			}
			if($action == 'suppr')
			{
				$req = " delete from formule_composant where frmco_frm_cod = $pot and frmco_gobj_cod = $comp_pot";
				$db->query($req);
			}
			$req = 'select frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom
				from formule_composant,objet_generique
				where frmco_frm_cod = ' . $num_form . '
				and frmco_gobj_cod = gobj_cod ';
			$db->query($req);
			while($db->next_record())
			{
				echo '<br>' . $db->f('gobj_nom') . ' (' . $db->f('frmco_num') . ') - <a href="' . $PHP_SELF . '?methode2=serie_obj&action=suppr&comp_pot=' . $db->f('frmco_gobj_cod') . '&pot=' . $pot . '">Supprimer ?</a>';
			}
			?>
			<br>Ajouter un objet :
			<form name="ajout" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode2" value="serie_obj">
			<input type="hidden" name="action" value="ajout">
			<input type="hidden" name="pot" value="<?php echo $num_form;?>">
			<table>
				<tr>
					<td>Composant</td>
					<td>Nombre de composants</td>
				</tr>
				<tr>
					<td><select name="gobj">
			<?php 
			$req = "select gobj_cod,gobj_nom from objet_generique where gobj_cod in (338,339,340,341,353,354,357,358,361,438) order by gobj_nom ";
			$db->query($req);
			while($db->next_record())
				echo '<option value="' . $db->f("gobj_cod") . '">' . $db->f("gobj_nom") . '</option>';
			?>
		</select></td>
			<td><input type="text" name="nombre" value="1"></td>
		</table>
				 <input type="submit" value="Ajouter"></form>
				<?php 
			break;
		case "modif":
			$req = 'select * from formule,formule_produit where frm_cod = ' . $pot .' and frm_cod = frmpr_frm_cod';
			$db->query($req);
			$db->next_record();
			$cod_pot = $db->f("frmpr_gobj_cod");
			?>
			<a href="<?php echo $PHP_SELF;?>?methode2=serie_obj&pot=<?php echo $pot;?>">Modifier la liste d'objets</a><br>
			<table>
			<form name="ajout" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode2" value="modif2">
			<input type="hidden" name="pot" value="<?php echo $pot;?>">
			<input type="hidden" name="nom" value="<?php echo $db->f("frm_nom");?>">

				<tr>
					<td class="soustitre2">Nom / Description de la formule du composant d'enchantement (conserver le nom du composant dedans)</td>
					<td><textarea cols="50" rows="10"  name="nom"><?php echo $db->f("frm_nom");?></textarea></td>
				</tr>
				<tr>
					<td class="soustitre2">Energie nécessaire <i></i></td>
					<td><input type="text" name="temps" value="<?php echo $db->f("frm_temps_travail");?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Cout en brouzoufs <i>(Non utilisé pour l'instant)</i></td>
					<td><input type="text" name="pot_cout" value="<?php echo $db->f("frm_cout");?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Résultat <i>(Non utilisé pour l'instant)</i></td>
					<td><input type="text" name="resultat" value="<?php echo $db->f("frm_resultat");?>"></td>
				</tr>
				<tr>
					<td class="soustitre2">Compétence</i></td>
					<td>
						<select name="competence">
							<?php  $s = $db->f("frm_comp_cod");
							$s1 = '';
							$s2 = '';
							$s3 = '';
							if ($s == '88')
							{
								$s1 = 'selected';
							}
							else if ($s == '102')
							{
								$s2 = 'selected';
							}
							else if ($s == '103')
							{
								$s3 = 'selected';
							}
							?>
							<option value="88" <?php echo  $s1 ?> >Forgeamage Niveau 1</option>';
							<option value="102" <?php echo  $s2 ?> >Forgeamage Niveau 2</option>';
							<option value="103" <?php echo  $s3 ?> >Forgeamage Niveau 3</option>';							
						</select>
					</td>
				</tr>
				<tr>
					<td class="soustitre2">Composant d'enchantement concerné</i></td>
					<td>
					<select name="potion">
					<?php  
					$req_pot = 'select gobj_cod,gobj_nom,gobj_description from objet_generique 
											where gobj_cod in (select oenc_gobj_cod from enc_objets)
											order by gobj_nom';
					$db2->query($req_pot);
					while($db2->next_record())
					{
						$sel = '';
						$potion = $db2->f("gobj_cod");
						if ($potion == $cod_pot)
						{
							$sel = "selected";
						}
						echo '<option value="'. $db2->f("gobj_cod") .'" '. $sel .'> '. $db2->f("gobj_nom") .'</option>';
					}
					echo '</select><br>';?>
					</td>
				</tr>
				<tr>
					<td class="soustitre2">Nombre de composants produits <i>(Non utilisé pour l'instant)</i></td>
					<td><input type="text" name="nombre" value="<?php echo $db->f("frmpr_num");?>"></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="test" value="Valider"></td>
				</tr>
		
		
			</form>
			</table>
			<?php 
			break;	
		case "modif2":
				$req = 'update formule
								set frm_nom = e\'' . pg_escape_string($_POST['nom']) . '\',
								frm_temps_travail = '. $_POST['temps'] .',
								frm_cout ='. $_POST['pot_cout'] .',
								frm_resultat = '. $_POST['resultat'] .',
								frm_comp_cod = '. $_POST['competence'] .'
								where frm_cod = ' . $pot;		
				$db->query($req);
				$req = 'update formule_produit
									set frmpr_gobj_cod = '. $_POST['potion'] .',
									frmpr_num = '. $_POST['nombre'] .'
									where frmpr_frm_cod = ' . $pot;	
				$db->query($req);				
							if ($_POST['competence'] == '88')
							{
								$comp = 1;
							}
							else if ($_POST['competence'] == '102')
							{
								$comp = 2;
							}
							else if ($_POST['competence'] == '103')
							{
								$comp = 3;
							}
			
				echo "<p>La formule de base du composant d'enchantement a bien été modifiée !<br>
							Vous pouvez aussi modifier la pierre précieuse associée.<br>";
				?><a href="<?php echo $PHP_SELF;?>?methode2=serie_obj&pot=<?php echo $pot;?>">Modifier la pierre précieuse associée</a><br>
				<?php 
			break;
		case "serie_obj":
			?>
			<strong>Règle pour un composant d'enchantement :</strong>
			<br>Un composant est produit à partir d'une seule pierre précieuse. Pas d'autre règle pour les objets. L'énergie nécessaire est déterminée à partir de la formule de base (écran précédent)
			<br><hr>
			<?php 
			if(!isset($action))
				$action = '';
			if($action == 'ajout')
			{
				$req = " insert into formule_composant (frmco_frm_cod,frmco_gobj_cod,frmco_num) values ($pot,$gobj,$nombre)";
				$db->query($req);
			}
			if($action == 'suppr')
			{
				$req = " delete from formule_composant where frmco_frm_cod = $pot and frmco_gobj_cod = $comp_pot";
				$db->query($req);
			}
			$req = 'select frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom
				from formule_composant,objet_generique
				where frmco_frm_cod = ' . $pot . '
				and frmco_gobj_cod = gobj_cod ';
			$db->query($req);
			while($db->next_record())
			{
				echo '<br>' . $db->f('gobj_nom') . ' (' . $db->f('frmco_num') . ') - <a href="' . $PHP_SELF . '?methode2=serie_obj&action=suppr&comp_pot=' . $db->f('frmco_gobj_cod') . '&pot=' . $pot . '">Supprimer ?</a>';
			}
			?>
			<br>Ajouter un objet :
			<form name="ajout" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode2" value="serie_obj">
			<input type="hidden" name="action" value="ajout">
			<input type="hidden" name="pot" value="<?php echo $pot;?>">
			<table>
				<tr>
					<td>Composant</td>
					<td>Nombre de composants</td>
				</tr>
				<tr>
					<td><select name="gobj">
			<?php 
			$req = "select gobj_cod,gobj_nom from objet_generique where gobj_cod in (338,339,340,341,353,354,357,358,361,438) order by gobj_nom ";
			$db->query($req);
			while($db->next_record())
				echo '<option value="' . $db->f("gobj_cod") . '">' . $db->f("gobj_nom") . '</option>';
			?>
		</select></td>
			<td><input type="text" name="nombre" value="1"></td>
		</table>
				 <input type="submit" value="Ajouter"></form>
			<?php 
			break;
	}
?>
<p style="text-align:center;"><a href="<?php $PHP_SELF?>?methode2=debut ">Retour au début</a>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
