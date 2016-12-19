<?php 
/* Création, modification des lieux */

include_once "verif_connexion.php";
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
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
	die("<p>Erreur ! Vous n’avez pas accès à cette page !</p>");
	$erreur = 1;
}
?>
<script language="javascript">
function blocking(nr)
{
	if (document.getElementById(nr).style.display == 'block')
		document.getElementById(nr).style.display = 'none';
	else
		document.getElementById(nr).style.display = 'block';
}
</script>
<?php $db2 = new base_delain;
$db3 = new base_delain;
if (!isset($methode))
{
	$methode = 'debut';
}
if (!isset($methode2))
{
	$methode2 = '';
}

if ($erreur == 0)
{
	// POSITION DU JOUEUR
	$req_position = "select pos_x,pos_y,pos_etage,pos_cod "
		."from perso_position,positions "
		."where ppos_perso_cod = $perso_cod"
		."and ppos_pos_cod = pos_cod ";
	$db->query($req_position);
	$db->next_record();
	$perso_pos_x = $db->f("pos_x");
	$perso_pos_y = $db->f("pos_y");
	$perso_pos_etage = $db->f("pos_etage");
	$perso_pos_cod = $db->f("pos_cod");
	if(isset($_POST['pos_etage']))
	{
		$perso_pos_etage = $_POST['pos_etage'];
	}
	switch ($methode)
	{
		case "supprimer_lieu":
			$req = "delete from lieu_position where lpos_lieu_cod = ".$_POST['del_lieu_cod'];
			$db->query($req);
			$req = "delete from lieu where lieu_cod = ".$_POST['del_lieu_cod'];
			$db->query($req);
			$req = "select init_automap_pos(".$del_lieu_cod.")";
			$db->query($req);
		break;
		case "creer_lieu":
			$req = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $perso_pos_etage";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucune position trouvée à ces coordonnées.</p>";
			}
			else
			{
				echo "<p>Position du lieu trouvée.</p>";
				$db->next_record();
				$lieu_pos_cod = $db->f("pos_cod");
				$lieu_dest_pos_cod = 'null';
				if($_POST['dest_pos_x'] != NULL && $_POST['dest_pos_y'] != NULL  && $_POST['dest_pos_etage'] != NULL )
				{
					$req = "select pos_cod from positions where pos_x = $dest_pos_x and pos_y = $dest_pos_y and pos_etage = $dest_pos_etage";
					$db->query($req);
					if ($db->nf() == 0)
					{
						echo "<p>Aucune position de destination trouvée à ces coordonnées.</p>";
					}
					else
					{
						echo "<p>Position de destination trouvée.</p>";
						$db->next_record();
						$lieu_dest_pos_cod = $db->f("pos_cod");
					}
				}
				else
				{
					echo "<p>Aucune position de destination sélectionnée.</p>";
				}
				$req = "select nextval('seq_lieu_cod') as lieu_cod";
				$db->query($req);
				$db->next_record();
				$lieu_cod = $db->f("lieu_cod");
				$nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
				$description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));
				$arr =  split(";", $_POST['type']);
				if ($arr[0] == 29 or $arr[0] == 30)
				{
					$cout_pa = $_POST['cout_pa'];
				}
				else
				{
					$cout_pa = 30; /*correspond au prélèvement des magasins*/
				}
				$req = "insert into lieu (lieu_cod, lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge, lieu_url,
					lieu_dest, lieu_alignement, lieu_dfin, lieu_compte, lieu_marge, lieu_prelev,
					lieu_mobile, lieu_date_bouge, lieu_date_refill, lieu_port_dfin, lieu_dieu_cod) values "
					."($lieu_cod, ".$arr[0].", e'$nom', e'$description', e'".pg_escape_string($_POST['refuge'])."', '".$arr[1]."', ".
					"$lieu_dest_pos_cod, 0, null, null, 50, $cout_pa, '".$_POST['mobile']."', now(), null, null, ".$_POST['dieu'].")";
				echo $req;
				$db->query($req);

				$req = "insert into lieu_position (lpos_pos_cod,lpos_lieu_cod) values "
					."($lieu_pos_cod,$lieu_cod)";
				echo $req;
				$db->query($req);

				$req = "select init_automap_pos(".$lieu_pos_cod.")";
				$db->query($req);

				?>
				<p>Création lieu : <br />
				Type : <?php echo  $_POST['type']?><br />
				Nom : <?php echo  $_POST['nom']?><br />
				Description : <?php echo  $_POST['description']?><br />
				X : <?php echo  $_POST['pos_x']?><br />
				Y : <?php echo  $_POST['pos_y']?><br />
				LIEU : <?php echo  $lieu_pos_cod?><br />
				DESTINATION :  <?php echo  $lieu_dest_pos_cod?><br />
				</p>
				<?php 
			}
		break;
	}
	switch ($methode2)
	{
		case "modifier":
			$req_detail = "select lieu_cod, lieu_tlieu_cod, lieu_nom, lieu_description, lieu_refuge, lieu_url,
				lieu_dest, lieu_alignement, lieu_dfin, lieu_compte, lieu_marge, lieu_prelev,
				lieu_mobile, lieu_date_bouge, lieu_date_refill, lieu_port_dfin, lieu_dieu_cod,
				pos_x, pos_y, pos_etage
				from lieu, lieu_position, positions
				where lieu_cod = $lieu
				and lpos_lieu_cod = lieu_cod
				and lpos_pos_cod = pos_cod";
			$db2->query($req_detail);
			$db2->next_record();
			$destination = $db2->f("lieu_dest");
			$refuge = $db2->f("lieu_refuge");
			$mobile = $db2->f("lieu_mobile");
			$cout_pa = $db2->f("lieu_prelev");
			?>
			<hr>
			<p>Modifier le lieu sélectionné (<?php echo  $db2->f("lieu_nom"); ?>)</p> (<a href="javascript:blocking('lieu_modif');">Afficher/Cacher</a>)
			<div id="lieu_modif" class="tableau2"  style="display:current;">
				<form  name="modif_lieu" method="post">
					<input type="hidden" name="methode2" value="modifier_lieu">
					<input type="hidden" name="lieu" value="<?php echo  $lieu ?>">
					Type :
					<select name="type">
			<?php 
			$req = "select tlieu_cod, tlieu_libelle, tlieu_url from lieu_type order by tlieu_libelle desc ";
			$db->query($req);
			while($db->next_record())
			{
				$sel = '';
				if ($db->f("tlieu_cod") == $db2->f("lieu_tlieu_cod"))
				{
					$sel = "selected";
				}
				echo "<option value=\"" , $db->f("tlieu_cod"),";",$db->f("tlieu_url"), "\" 	",$sel,">" , $db->f("tlieu_libelle") , "</option>";
			}
			?>
					</select><br>
					Nom : <input type="text" name="nom" value="<?php echo  $db2->f("lieu_nom"); ?>"><br />
					Description : <textarea name="description"><?php echo  $db2->f("lieu_description"); ?></textarea><br />
					<b>Position : </b> X : <input type="text" name="pos_x" value="<?php echo  $db2->f("pos_x"); ?>"> Y : <input type="text" name="pos_y" value="<?php echo  $db2->f("pos_y"); ?>">
					Étage :
					<select name="pos_etage">
			<?php 
				echo $html->etage_select($db2->f("pos_etage"));
			?>
					</select><br />
					Dieu (pour les temples et autels)
					<select name="dieu">
						<option value="null">Pas de dieu</option>
			<?php 
			$req = "select dieu_cod, dieu_nom from dieu order by dieu_nom desc ";
			$db->query($req);
			while($db->next_record())
			{
				$sel = "";
				if($db->f("dieu_cod") == $db2->f("lieu_dieu_cod"))
				{
					$sel = "selected";
				}
				echo "<option value=\"" , $db->f("dieu_cod") , "\" $sel>" , $db->f("dieu_nom") , "</option>";
			}
			?>
					</select><br />
			<?php 
			if ($destination != 'null' && $destination != '')
			{
				$req = "select pos_x, pos_y, pos_etage from positions where pos_cod = $destination";
				$db->query($req);
				$db->next_record();
				$dest_pos_x = $db->f("pos_x");
				$dest_pos_y = $db->f("pos_y");
				$dest_pos_etage = $db->f("pos_etage");
			}
			else
			{
				$dest_pos_x = '';
				$dest_pos_y = '';
				$dest_pos_etage = '';
			}
			?>
					<b>Destination :</b>
					X : <input type="text" name="dest_pos_x" value="<?php echo  $dest_pos_x ?>">
					Y : <input type="text" name="dest_pos_y" value="<?php echo  $dest_pos_y ?>">
					Étage :
					<select name="dest_pos_etage">
			<?php 
				echo $html->etage_select($dest_pos_etage);
			?>
					</select><br />
					Refuge:
					<select name="refuge">
						<option value="N"<?php  if ($refuge == 'N'){echo " selected";}?>>non</option>
						<option value="O"<?php  if ($refuge == 'O'){echo " selected";}?>>oui</option>
					</select>
					Mobile:
					<select name="mobile">
						<option value="N"<?php  if ($mobile == 'N'){echo " selected";}?>>non</option>
						<option value="O"<?php  if ($mobile == 'O'){echo " selected";}?>>oui</option>
					</select>
					Coût en pa (pour les passages ondulants uniquement)<input type="text" name="cout_pa" value="<?php echo  $cout_pa ?>">
					<input type="submit" value="Modifier !" class='test'>
				</form>
			</div>
			<?php 
		break;
		case "modifier_lieu":
			$req = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $perso_pos_etage";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucune position trouvée à ces coordonnées.</p>";
			}
			else
			{
				echo "<p>Position du lieu trouvée.</p>";
				$db->next_record();
				$lieu_pos_cod = $db->f("pos_cod");
				$lieu_dest_pos_cod = 'null';
				if($_POST['dest_pos_x'] != NULL && $_POST['dest_pos_y'] != NULL  && $_POST['dest_pos_etage'] != NULL )
				{
					$req = "select pos_cod from positions where pos_x = $dest_pos_x and pos_y = $dest_pos_y and pos_etage = $dest_pos_etage";
					$db->query($req);
					if ($db->nf() == 0)
					{
						echo "<p>Aucune position de destination trouvée à ces coordonnées.</p>";
					}
					else
					{
						echo "<p>Position de destination trouvée.</p>";
						$db->next_record();
						$lieu_dest_pos_cod = $db->f("pos_cod");
					}
				}
				else
				{
				echo "<p>Aucune position de destination sélectionnée.</p>";
				}
				$nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
				$description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));
				$arr =  split(";", $_POST['type']);
				if ($arr[0] == 29 or $arr[0] == 30)
				{
					$cout_pa = $_POST['cout_pa'];
				}
				else
				{
					$cout_pa = 30; /*correspond au prélèvement des magasins*/
				}
				$req = "update lieu set lieu_cod=$lieu, lieu_tlieu_cod=".$arr[0].", lieu_nom=e'$nom', lieu_description=e'$description',
					lieu_refuge=e'".pg_escape_string($_POST['refuge'])."', lieu_url='".$arr[1]."', lieu_dest=$lieu_dest_pos_cod,
					lieu_alignement=0, lieu_marge=50, lieu_prelev=$cout_pa, lieu_mobile='".$_POST['mobile']."', lieu_date_bouge=now(),
					lieu_dieu_cod=".$_POST['dieu']."
					where lieu_cod = $lieu";
				echo $req;
				$db->query($req);
				$req = "update lieu_position set lpos_pos_cod = $lieu_pos_cod where lpos_lieu_cod = $lieu";
				echo $req;
				$db->query($req);

				$req = "select init_automap_pos(".$lieu_pos_cod.")";
				$db->query($req);

				?>
				<p>Modification du lieu : <br />
					Type : <?php echo  $_POST['type']?><br />
					Nom : <?php echo  $_POST['nom']?><br />
					Description : <?php echo  $_POST['description']?><br />
					X : <?php echo  $_POST['pos_x']?><br />
					Y : <?php echo  $_POST['pos_y']?><br />
					LIEU : <?php echo  $lieu_pos_cod?><br />
					DESTINATION : <?php echo  $lieu_dest_pos_cod?><br />
				</p>
				<?php 
			}
		break;
	}
	?>
	<p> Création / Modification des lieux</p>
	<hr>
	Choix de l’étage (pour créer ou modifier un lieu) 
	<form method="post">
	Étage : <select name="pos_etage">
	<?php 
		echo $html->etage_select($pos_etage);
	?>
	</select><br>
	<input type="submit" value="Valider" class='test'>
	</form>
	<hr>
	<p>Créer un nouveau lieu</p> (<a href="javascript:blocking('creelieu');">Afficher/Cacher</a>)
	<div id="creelieu" style="display: none;">
		<form method="post">
			<input type="hidden" name="methode" value="creer_lieu">
			<input type="hidden" name="pos_etage" value="<?php echo $perso_pos_etage?>">
			Type : <select name="type">
	<?php 
	$req = "select tlieu_cod, tlieu_libelle, tlieu_url from lieu_type order by tlieu_libelle desc ";
	$db->query($req);
	while($db->next_record())
	{
		echo "<option value=\"" , $db->f("tlieu_cod"),";",$db->f("tlieu_url"), "\">" , $db->f("tlieu_libelle") , "</option>";
	}
	?>
			</select><br>
			Nom : <input type="text" name="nom"><br />
			Description : <textarea name="description"></textarea><br />
			Position X : <input type="text" name="pos_x" value="0"><br />
			Position Y : <input type="text" name="pos_y" value="0"><br />
			Dieu (pour les temples) <select name="dieu">
			<option value="null">Pas de dieu</option>
	<?php 
	$req = "select dieu_cod,dieu_nom from dieu order by dieu_nom desc ";
	$db->query($req);
	while($db->next_record())
	{
		echo "<option value=\"" , $db->f("dieu_cod"), "\">" , $db->f("dieu_nom") , "</option>";
	}
	?>
			</select><br />
			Destination :
			X <input type="text" name="dest_pos_x" value="">
			Y <input type="text" name="dest_pos_y" value="">
			Étage : <select name="dest_pos_etage">
	<?php 
		echo $html->etage_select($pos_etage);
	?>
			</select><br />
			Refuge : <select name="refuge">
			<option value="N">non</option>
			<option value="O">oui</option>
			</select>
			Mobile : <select name="mobile">
			<option value="N">non</option>
			<option value="O">oui</option>
			</select>
			Coût en pa (pour les passages ondulants uniquement)<input type="text" name="cout_pa" value="0">

			<input type="submit" class='test' value="créer !" />
		</form>
	</div>
	<hr />
	<?php 
	$sel_etage = $perso_pos_etage;
	if(isset($_POST['pos_etage']))
	{
		$sel_etage = $_POST['pos_etage'];
	}
	elseif(isset($_GET['pos_etage']))
	{
		$sel_etage = $_GET['pos_etage'];
	}
	?>
	<p>Lieux de cet étage</p>
	<form name="action_suppr_lieu" method="post">
		<input type="hidden" name="methode" value="supprimer_lieu">
		<input type="hidden" name="del_lieu_cod" value="">
	</form>
	<script language="javascript">
		function supprimerlieu(code) {
		document.action_suppr_lieu.del_lieu_cod.value = code;
		document.action_suppr_lieu.submit();
		}
	</script>
	<table>
	<?php 
	$req_murs = "select lieu_cod, tlieu_libelle, lieu_nom, p.pos_x, p.pos_y, p.pos_etage, dest.pos_x as dest_x, dest.pos_y as dest_y, coalesce(etage_libelle, '') as etage_dest ".
		"from lieu
		inner join lieu_position on lpos_lieu_cod = lieu_cod
		inner join positions p on p.pos_cod = lpos_pos_cod
		inner join lieu_type on tlieu_cod = lieu_tlieu_cod 
		left outer join positions dest on dest.pos_cod = lieu_dest
		left outer join etage on etage_numero = dest.pos_etage ".
		"where p.pos_etage = $sel_etage ".
		"order by tlieu_libelle, lieu_nom";
	$db->query($req_murs);
	while($db->next_record())
	{
		?>
		<tr><td><a href="<?php echo  $PHP_SELF ?>?pos_etage=<?php echo  $sel_etage; ?>&methode2=modifier&lieu=<?php echo  $db->f("lieu_cod")?>" onclick="window.parent.document.location=this.href;return false";blocking('lieu')>Modifier</a></td><td><?php echo  $db->f("lieu_nom")?></td><td><?php echo  $db->f("tlieu_libelle")?></td><td>(<?php echo  $db->f("pos_x")?>,<?php echo  $db->f("pos_y")?>)</td>
		<?php 
		$etage_dest = $db->f("etage_dest");
		if ($etage_dest != '')
		{
		?>
			<td>Destination : X : <?php echo  $db->f("dest_x"); ?> / Y : <?php echo  $db->f("dest_y"); ?> / Étage : <?php echo  $etage_dest; ?>
		<?php }?>
			<td><a href="javascript:supprimerlieu(<?php echo  $db->f("lieu_cod")?>);">Supprimer</a></td>
		</tr>
		<?php 
	}
	?>
	</table>
	<?php 
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
