<<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
$param = new parametres();

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
/*********************/
/* VOL : 84-85-86    */
/*********************/
// contenu de la page
$contenu_page = "";
$db = new base_delain;
$req_comp = "select pcomp_pcomp_cod,pcomp_modificateur from perso_competences ";
$req_comp = $req_comp . "where pcomp_perso_cod = $perso_cod ";
$req_comp = $req_comp . "and pcomp_modificateur != 0 ";
$req_comp = $req_comp . "and pcomp_pcomp_cod IN (84,85,86)";
$db->query($req_comp);
if($db->next_record()){
	$num_comp = $db->f("pcomp_pcomp_cod");
	$valeur_comp = $db->f("pcomp_modificateur");	
	$req_vue = "select ppos_pos_cod "
			."from perso_position where ppos_perso_cod = $perso_cod";
	$db->query($req_vue);
	$db->next_record();
	$position = $db->f("ppos_pos_cod");

		
	// TRAITEMENT DE FORMULAIRE
	if(isset($_POST['methode']))
	{
		switch ($methode) {
			case "voler":
				if($cible_cod == -1){
				?><p><strong>Vous devez choisir une cible !</strong></p><?php 
				} else {
					$req_vol = "select vol($perso_cod,$cible_cod) as resultat";
					$db->query($req_vol);
					$db->next_record();
				?>
				<p>Vol !</p>
				<p><?php echo $db->f("resultat"); ?></p>
				<hr>
				<?php 
				}
				break;
			case "voler_objet":
				if($cible_cod == -1){
				?><p><strong>Vous devez choisir une cible !</strong></p><?php 
				} else {
					$req_vol = "select vol_objet($perso_cod,$cible_cod) as resultat";
					$db->query($req_vol);
					$db->next_record();
				?>
				<p>Vol !</p>
				<p><?php echo $db->f("resultat"); ?></p>
				<hr>
				<?php 
				}
				break;
		}
	}
	?>

<p>Vous disposez de la Compétence Vol</p>
Sélectionner une cible à détrousser:
<form method="post" name="form_vol" action="comp_vol.php">
<input type="hidden" name="methode" value="voler">
<select name="cible_cod">
<option value="-1">&lt;Choisir une victime&gt;</option>
<?php 
$req_cibles = "select perso_nom,race_nom,perso_cod,perso_type_perso "
. "from perso,perso_position,race "
. "where ppos_pos_cod = $position "
. "and ppos_perso_cod = perso_cod "
. "and perso_cod != $perso_cod "
. "and perso_actif = 'O' "
. "and perso_tangible = 'O' "
. "and perso_race_cod = race_cod "
. "and not exists "
. "(select 1 from lieu,lieu_position "
. "where lpos_pos_cod = ppos_pos_cod "
. "and lpos_lieu_cod = lieu_cod "
. "and lieu_refuge = 'O') "
. "and not exists "
. "(select 1 from perso_familier "
. "where pfam_perso_cod = $perso_cod "
. "and pfam_familier_cod = perso_cod) ";
$db->query($req_cibles);
while($db->next_record()){
?>
<option value="<?php echo $db->f("perso_cod") ?>"><?php echo $db->f("perso_nom") ?> (<?php echo $db->f("race_nom") ?>)</option>
<?php 
}
?>
</select>
<input type="button" value="Voler ! (<?php  echo $param->getparm(95)?>PA)" onClick="document.form_vol.submit();">
<?php if($num_comp == 86){?>
<input type="button" value="Voler un objet ! (<?php  echo $param->getparm(95)?>PA)" onClick="document.form_vol.methode.value='voler_objet';document.form_vol.submit();">
<?php  } ?>
</form>
<br>
<br>
	<?php 
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
