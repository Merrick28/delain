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
<script language="javascript" src="../scripts/cocheCase.js"></script>
<?php 
$db2 = new base_delain;
/*********************/
/* COMMANDEMENT : 80 */
/*********************/
// contenu de la page
$contenu_page = "";
$db = new base_delain;
$req_comp = "select pcomp_modificateur from perso_competences ";
$req_comp = $req_comp . "where pcomp_perso_cod = $perso_cod ";
$req_comp = $req_comp . "and pcomp_modificateur != 0 ";
$req_comp = $req_comp . "and pcomp_pcomp_cod = 80";
$db->query($req_comp);
if($db->next_record()){
	$valeur_comp = $db->f("pcomp_modificateur");
	
		$req_vue = "select distance_vue($perso_cod) as distance_vue,ppos_pos_cod,pos_etage,pos_x,pos_y "
			."from perso_position,positions where ppos_perso_cod = $perso_cod and ppos_pos_cod = pos_cod";
		$db->query($req_vue);
		$db->next_record();
		$vue = $db->f("distance_vue");
		$x = $db->f("pos_x");
		$y = $db->f("pos_y");
		$etage = $db->f("pos_etage");
//			
$req_troupe = "delete from perso_commandement where not exists(select 1 from perso where perso_actif = 'O' and perso_cod = perso_subalterne_cod)";
$db->query($req_troupe);
		
	// TRAITEMENT DE FORMULAIRE
if(isset($_POST['methode']))
{
	switch ($methode) {
		case "ajouter_subalterne":
			$req_troupe = "insert into perso_commandement (perso_subalterne_cod,perso_superieur_cod) values ($nouv_perso_cod,$perso_cod)";
			$db->query($req_troupe);
			break;
		case "changer_description":
			$erreur = 0;
			$description = htmlspecialchars($description);
			$description = str_replace(";",chr(127),$description);
			if (strlen($description)>=254)
			{
				echo "<p>Votre titre est trop long (max 200 caractères), merci de le raccourcir !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$in_val = "";
				$array = $_POST['subalterne_cod'];
				foreach ($array as $i => $value) {
   					$in_val.=$value.",";
				}
				$in_val = substr($in_val, 0, strlen($in_val)-1);
				$req_desc = "update perso set perso_description = '$description' where perso_cod in ($in_val) ";
				//echo $req_desc;
				$db->query($req_desc);
				echo("<p>La description est enregistrée !");
			}
		break;
		case "changer_statique":

				$in_val = "";
				$array = $_POST['subalterne_cod'];
				foreach ($array as $i => $value) {
   					$in_val.=$value.",";
				}
				$in_val = substr($in_val, 0, strlen($in_val)-1);
				$req_desc = "update perso set perso_sta_combat = '$perso_sta_combat',perso_sta_hors_combat = '$perso_sta_hors_combat' where perso_cod in ($in_val) ";
				//echo $req_desc;
				$db->query($req_desc);
				echo("<p>Les propriétés statiques sont enregistrée !");
			
		break;
		case "renvoyer":
			$in_val = "";
			$array = $_POST['subalterne_cod'];
			foreach ($array as $i => $value) {
   				$in_val.=$value.",";
			}
			$in_val = substr($in_val, 0, strlen($in_val)-1);
			$req_troupe = "delete from perso_commandement where perso_subalterne_cod in ($in_val)";
			$db->query($req_troupe);
		break;
		case "modifier_IA":
			$array = $_POST['subalterne_cod'];
			foreach ($array as $i => $mon_cod) {
	   			if($pia_ia_type != -1){
					$req = "update perso set perso_dirige_admin = 'N' where perso_cod = $mon_cod";
					$db->query($req);
					$req = "delete from perso_ia where pia_perso_cod = $mon_cod";
					$db->query($req);
					$req = "insert into perso_ia (pia_perso_cod,pia_ia_type) values ($mon_cod,$pia_ia_type)";
					$db->query($req);
				} else {
					$req = "update perso set perso_dirige_admin = 'N' where perso_cod = $mon_cod";
					$db->query($req);
					$req = "delete from perso_ia where pia_perso_cod = $mon_cod";
					$db->query($req);
				}
			}
		break;
		case "modifier_IA_pos":
			$erreur = 0;
			$req = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $etage ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucune position trouvée à ces coordonnées.<br>";
				$erreur = 1;
			}
			$db->next_record();
			$pos_cod = $db->f("pos_cod");
			$req = "select mur_pos_cod from murs where mur_pos_cod = $pos_cod ";
			$db->query($req);
			if ($db->nf() != 0)
			{
				echo "<p>impossible d'aller sur cette position : un mur en destination.<br>";
				$erreur = 1;
			}
			if ($erreur == 0)
				{
				$array = $_POST['subalterne_cod'];
				foreach ($array as $i => $mon_cod) {				
		   			if($pia_ia_type_pos != -1){
						$req = "update perso set perso_dirige_admin = 'N' where perso_cod = $mon_cod";
						$db->query($req);
						$req = "delete from perso_ia where pia_perso_cod = $mon_cod";
						$db->query($req);
						$req = "insert into perso_ia (pia_perso_cod,pia_ia_type,pia_parametre) values ($mon_cod,$pia_ia_type_pos,$pos_cod)";
						$db->query($req);
					} else {
						$req = "update perso set perso_dirige_admin = 'N' where perso_cod = $mon_cod";
						$db->query($req);
						$req = "delete from perso_ia where pia_perso_cod = $mon_cod";
						$db->query($req);
					}
				}
			}
		break;
		case "modifier_IA_cib":
			$erreur = 0;
			if ($erreur == 0)
				{
				$array = $_POST['subalterne_cod'];
				foreach ($array as $i => $mon_cod) {				
		   			if($pia_ia_type_cib != -1){
						$req = "update perso set perso_dirige_admin = 'N' where perso_cod = $mon_cod";
						$db->query($req);
						$req = "delete from perso_ia where pia_perso_cod = $mon_cod";
						$db->query($req);
						$req = "insert into perso_ia (pia_perso_cod,pia_ia_type,pia_parametre) values ($mon_cod,$pia_ia_type_cib,$cible_cod)";
						$db->query($req);
					} else {
						$req = "update perso set perso_dirige_admin = 'N' where perso_cod = $mon_cod";
						$db->query($req);
						$req = "delete from perso_ia where pia_perso_cod = $mon_cod";
						$db->query($req);
					}
				}
			}
		break;
		case "lancer_ia":
		$cod_monstre = $_POST['cod_monstre'];
		$req = "select ia_monstre($cod_monstre)";
		$db->query($req);
		break;
	}
}
	
?>
<p>Commandement: nombre maximal de troupes:<?php  echo $valeur_comp ?> </p>
<p><strong> Donner des ordres: </strong></p>
		<form method="post" name="troupes" action="comp_commandement.php">
		<input type="hidden" name="methode" value="">
		<table  border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr><td class="soustitre2" width="20%">Nom
		</td><td class="soustitre2" width="10%">Position
		</td><td class="soustitre2" width="10%">DLT
		</td><td class="soustitre2" width="7%">PA	
		</td><td class="soustitre2" width="15%">Etat
		</td><td class="soustitre2" width="10%">IA Actuelle
		</td><td class="soustitre2" width="10%">Statique En combat/hors Combat
		</td><td class="soustitre2" width="25%">Locks combat
		</td><td class="soustitre2" width="25%">Lancer l'IA manuellement	
		</td>
		</tr>
<?php 
	$req_troupe = "select perso_sta_combat,perso_sta_hors_combat,perso_subalterne_cod,perso_subalterne_cod,perso_nom,perso_description,perso_pv,perso_pv_max,perso_pa,perso_dirige_admin,pos_x,pos_y,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as dlt,dlt_passee(perso_cod) as dlt_passee from perso,perso_commandement,perso_position,positions".
	" where perso_superieur_cod = $perso_cod and perso_cod = perso_subalterne_cod and pos_cod = ppos_pos_cod and ppos_perso_cod = perso_cod";
	$db->query($req_troupe);
	$nb_subalternes = $db->nf();
	$db2 = new base_delain;
	$n = 0;
	while($db->next_record()){
		$n++;
		$cod_monstre = $db->f("perso_subalterne_cod");
		if(fmod($n,2) == 0){
			$cl = "class=\"soustitre2\"";	
		} else {
			$cl = "";
		}
		$req_ia = "select ia_type,ia_nom,pia_parametre from type_ia,perso_ia where pia_ia_type = ia_type and pia_perso_cod = ".$db->f("perso_subalterne_cod");
		$db2->query($req_ia);       
		if($db2->next_record()){
			$ia = $db2->f("ia_nom");
		} else {
			$ia = "Aucune";
		}
		$ia_param = $db2->f("pia_parametre");
		if(strpos($ia,'[position]')  != false && $ia_param){
			$req_pos = "select pos_x,pos_y from positions where pos_cod = ".$db2->f("pia_parametre");
			$db2->query($req_pos);  
			$db2->next_record();
			$ia .= " (".$db2->f("pos_x").",".$db2->f("pos_y").")";		
		}
		if(strpos($ia,'[cible]')  != false && $ia_param ){
			$req_pos = "select perso_nom from perso where perso_cod = ".$db2->f("pia_parametre");
			$db2->query($req_pos);  
			$db2->next_record();
			$ia .= " (".$db2->f("perso_nom").")";		
		}
		?>
		<tr><td <?php  echo $cl;?>>
		<input type="checkbox" name="subalterne_cod[]" value="<?php echo $db->f("perso_subalterne_cod");?>"> <a href="javascript:document.visu_evt2.cible.value=<?php echo $db->f("perso_subalterne_cod");?>;"> <?php  echo $db->f("perso_nom");?></a> 
		</td><td <?php  echo $cl;?>>
		(<?php  echo $db->f("pos_x");?>,<?php  echo $db->f("pos_y");?>)
		</td><td <?php  echo $cl;?>>
		<?php 
		if ($db->f("dlt_passee") == 1)
			{
				echo("<strong>");
			}
			echo $db->f("dlt");
			if ($db->f("dlt_passee") == 1)
			{
				echo("</strong>");
			}?>
		</td><td <?php  echo $cl;?>>
		<?php echo $db->f("perso_pa");?> / 12
		</td><td <?php  echo $cl;?>>
		(<?php echo $db->f("perso_pv");?> / <?php echo $db->f("perso_pv_max");?> PV)
		</td><td <?php  echo $cl;?>>
		<?php echo $ia;?></td>
		<td <?php  echo $cl;?>>
		<?php echo $db->f("perso_sta_combat");?> / <?php echo $db->f("perso_sta_hors_combat");?>
		</td>
		<td <?php  echo $cl;?>>
		<?php 
		$req = "select perso_nom from perso inner join 
							(select lock_attaquant as lock from lock_combat
								where lock_cible = $cod_monstre 
							union all select lock_cible as lock from lock_combat
								where lock_attaquant = $cod_monstre) as t2 on perso.perso_cod = t2.lock group by perso_nom";
		$db2->query($req);
		while ($db2->next_record())
		{
		echo $db2->f("perso_nom") . "<br>";	
		}
		?>
		</td>
		<td <?php  echo $cl;?>>
		<form method="post" action="comp_commandement2.php">
		<input type="hidden" name="methode" value="lancer_ia"
		<input type="hidden" name="cod_monstre" value="<?php echo $cod_monstre?>" >
		<input type="Submit" value="Lancer l'IA">
		</form>
		</td>	
		</tr>
		<tr><td <?php  echo $cl;?> colspan="5">
		<font style="font-size:7pt;"><?php echo $db->f("perso_description");?></font></td></tr>
		
	<?php }?>	
	</table>
	<a style="font-size:7pt;" href="javascript:toutCocher(document.troupes,'subalterne_cod');">cocher/décocher/inverser</a><br><br><br>
	<textarea name="description" rows="8" cols="25"></textarea><br><br>
	<input type="submit" value="Changer la description" onClick="methode.value='changer_description'"><br><br>
	Statique en combat:Oui<input type="radio" name="perso_sta_combat" value="O"> Non<input checked type="radio" name="perso_sta_combat" value="N"><br>
	Statique hors combat:Oui<input type="radio" name="perso_sta_hors_combat" value="O"> Non<input checked type="radio" name="perso_sta_hors_combat" value="N"><br>
	<input type="submit" value="Changer le statisme" onClick="methode.value='changer_statique'"><br><br>
	<input type="submit" value="Renvoyer de la troupe" onClick="methode.value='renvoyer'"><br><br>
<?php 
$array_ia = array();
$req = "select ia_type,ia_nom from type_ia order by ia_type desc ";
$db->query($req);
while($db->next_record())
{
	//echo $db->f("ia_nom");
	$array_ia[$db->f("ia_type")] = $db->f("ia_nom");
}
?>	
<!-- IA CLASSIQUE -->
	Modifier l'IA:<select name="pia_ia_type">
					<option value="-1">Aucune (IA par défaut)</option>
					<?php 
					foreach ($array_ia as $key => $nom) {
						if(strpos($nom,'[position]') == false && strpos($nom,'[cible]') == false){
							echo "<option value=\"" , $key , "\">" , $nom , "</option>";
						}
					}
						//$req = "select ia_type,ia_nom from type_ia order by ia_type desc ";
						//$db->query($req);
						//while($db->next_record())
						//{
						//	echo "<option value=\"" , $db->f("ia_type") , "\">" , $db->f("ia_nom") , "</option>\n";
						//}
					?>
					</select>
	<input type="submit" value="Modifier l'IA" onClick="methode.value='modifier_IA'"><br><br>
<!-- IA LOCALISEE-->	
	Modifier l'IA:<select name="pia_ia_type_pos">
					<option value="-1">Aucune (IA par défaut)</option>
					<?php 
					foreach ($array_ia as $key => $nom) {
						if(strpos($nom,'[position]') == true){
							echo "<option value=\"" , $key , "\">" , $nom , "</option>";
						}
					}
						//$req = "select ia_type,ia_nom from type_ia order by ia_type desc ";
						//$db->query($req);
						//while($db->next_record())
						//{
						//	echo "<option value=\"" , $db->f("ia_type") , "\">" , $db->f("ia_nom") , "</option>\n";
						//}
					?>
					</select>
	X:<input type="text" name="pos_x"> Y:<input type="text" name="pos_y">
	<input type="submit" value="Modifier l'IA (Localisée)" onClick="methode.value='modifier_IA_pos'">
	<br><br>
<!-- IA CIBLEE-->	
	Modifier l'IA:<select name="pia_ia_type_cib">
					<option value="-1">Aucune (IA par défaut)</option>
					<?php 
					foreach ($array_ia as $key => $nom) {
						if(strpos($nom,'[cible]') == true){
							echo "<option value=\"" , $key , "\">" , $nom , "</option>";
						}
					}
						//$req = "select ia_type,ia_nom from type_ia order by ia_type desc ";
						//$db->query($req);
						//while($db->next_record())
						//{
						//	echo "<option value=\"" , $db->f("ia_type") , "\">" , $db->f("ia_nom") , "</option>\n";
						//}
					?>
					</select>
	CIBLE:<?php 
	// LISTE DE TOUTES LES CIBLES POSSIBLES

		// On recherche les monstres en vue
		$req_vue_joueur = "select perso_nom,perso_cod,race_nom ";
		$req_vue_joueur = $req_vue_joueur . "from perso,positions,perso_position,race ";
		$req_vue_joueur = $req_vue_joueur . "where pos_x between ($x-$vue) and ($x+$vue) ";
		$req_vue_joueur = $req_vue_joueur . "and pos_y between ($y-$vue) and ($y+$vue) ";
		$req_vue_joueur = $req_vue_joueur . "and pos_cod = ppos_pos_cod ";
		$req_vue_joueur = $req_vue_joueur . "and pos_etage = $etage ";
		$req_vue_joueur = $req_vue_joueur . "and ppos_perso_cod = perso_cod ";
		$req_vue_joueur = $req_vue_joueur . "and perso_actif = 'O' ";
		$req_vue_joueur = $req_vue_joueur . "and perso_race_cod = race_cod ";
		$req_vue_joueur = $req_vue_joueur . "order by perso_cod desc ";
		$db->query($req_vue_joueur);
		?>
		<select name="cible_cod">
		<?php 
		while($db->next_record()){
			?>
			<option value="<?php  echo $db->f("perso_cod") ?>"> <?php echo $db->f("perso_nom") ?></option>
			<?php 
		}
		?>
		</select>

	<input type="submit" value="Modifier l'IA (Ciblée)" onClick="methode.value='modifier_IA_cib'">
	<br><br>	
	</form>
	
	<p><strong> Recrutement: </strong></p>
	<?php 
	// SI LE NOMBRE MAX N'EST PAS ATTEINT ON PEUT ENGAGER DES TROUPES

	if($nb_subalternes < $valeur_comp ){
		// On recherche les monstres en vue
		$req_vue_joueur = "select perso_nom,perso_cod,race_nom,pos_x,pos_y,perso_pa ";
		$req_vue_joueur = $req_vue_joueur . "from perso,positions,perso_position,race ";
		$req_vue_joueur = $req_vue_joueur . "where pos_x between ($x-$vue) and ($x+$vue) ";
		$req_vue_joueur = $req_vue_joueur . "and pos_y between ($y-$vue) and ($y+$vue) ";
		$req_vue_joueur = $req_vue_joueur . "and pos_cod = ppos_pos_cod ";
		$req_vue_joueur = $req_vue_joueur . "and pos_etage = $etage ";
		$req_vue_joueur = $req_vue_joueur . "and ppos_perso_cod = perso_cod ";
		$req_vue_joueur = $req_vue_joueur . "and perso_cod != $perso_cod ";
		$req_vue_joueur = $req_vue_joueur . "and perso_actif = 'O' ";
		$req_vue_joueur = $req_vue_joueur . "and perso_type_perso = 2 ";
		$req_vue_joueur = $req_vue_joueur . "and perso_race_cod = race_cod ";
		$req_vue_joueur = $req_vue_joueur . "and not exists ";
		$req_vue_joueur = $req_vue_joueur . "(select 1 from perso_commandement ";
		$req_vue_joueur = $req_vue_joueur . "where perso_subalterne_cod = perso_cod) ";
		$req_vue_joueur = $req_vue_joueur . "order by perso_cod desc ";
		$db->query($req_vue_joueur);
		?>
		
		
		Vous pouvez engager les troupes suivantes:<br>
		<form method="post" action="comp_commandement.php">
		<input type="hidden" name="methode" value="ajouter_subalterne">
		<select name="nouv_perso_cod">
		<?php 
		while($db->next_record()){
			?>
			<option value="<?php  echo $db->f("perso_cod") ?>"> <?php echo $db->f("perso_nom") ?> (<?php echo $db->f("pos_x") ?>,<?php echo $db->f("pos_y") ?>) <?php echo $db->f("perso_pa") ?>PA</option>
			<?php 
		}
		?>
		</select>
		<input type="Submit" value="Ajouter">
		</form>
		<?php 
		} else {?>
		<p>Votre troupe est au maximum de ses effectifs, vous ne pouvez plus engager personne.</p>
		<?php 
		}
	} else {
	?>
	<p>Vous ne disposez pas de cette competence !</p>
	<?php 
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
