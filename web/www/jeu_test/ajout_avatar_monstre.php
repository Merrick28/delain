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
if (!isset($methode))
{
	$methode = "debut";
}
include "tab_haut.php";
$req = "select dcompt_controle_admin from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	$droit['controle_admin'] = 'N';
}
else
{
	$db->next_record();
	$droit['controle_admin'] = $db->f("dcompt_controle_admin");
}
if ($droit['controle_admin'] == 'O')
{
	switch($methode)
	{
		case "debut":
			$req = "select gmon_cod,gmon_nom from monstre_generique order by gmon_nom";
			$db->query($req);
			?>
			<p>Choisissez le compte à controler :
			<form action="<?php echo $PHP_SELF;?>" metod="post">
			<input type="hidden" name="methode" value="et2">
			<select name="vmonstre">
			<?php 	
			while($db->next_record())
			{
				?>
				<option value="<?php echo $db->f("gmon_cod");?>"><?php echo $db->f("gmon_nom");?></option>
				<?php 
			}		
			?>
			</select>
			<center><input type="submit" class="test" value="Suite !"></center>
			</form>		
			<?php 
			break;
		case "et2":
			$req = "select gmon_nom, gmon_avatar from monstre_generique where gmon_cod = $vmonstre";
			$db->query($req);
			?>
			<center>
				<table>
				<tr>
					<td class="soustitre2"><b>Nom du monstre générique</b></td>
					<td class="soustitre2"><b>Avatar</b></td>
				</tr>
				<?php 
				while($db->next_record())
				{
					?>
					<tr>
					<td><?php echo $db->f("gmon_nom");?></td>
					<td class="soustitre2"><img src=http://www.jdr-delain.net/images/avatars/<?php echo $db->f("gmon_avatar");?>></td>
					</tr>
					<?php 
				}
			?>
			</table>
			</center>
			</form>
			<?php 
			break;
	}
	
	
	
	
	
	
	
	
}
else
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
