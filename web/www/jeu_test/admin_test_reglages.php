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
?>
<SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
<?php $erreur = 0;
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
if ($droit['modif_gmon'] != 'O')
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
if ($erreur == 0)
{
	if(isset($_POST['methode'])){
    	//TRAITEMENT DU FORMULAIRE
    		switch ($methode) {
				case "update_liste_nom":
					$req = "delete from race_nom_monstre "
					."where rac_nom_race_cod = $rac_nom_race_cod and rac_nom_type = '$rac_nom_type' and rac_nom_genre = '$rac_nom_genre'";
					$db->query($req);	
					$array = explode(',',$_POST['listenoms']);
					foreach ($array as $i => $value) {
						if($value != ""){
							//$trimvalue = preg_replace('/\s+/', '', $value);
							$trimvalue = trim($value);
							$req = "insert into race_nom_monstre "
							."(rac_nom_race_cod,rac_nom_type,rac_nom_genre,rac_nom_nom,rac_nom_chance) values "
							."($rac_nom_race_cod,e'" . pg_escape_string($rac_nom_type) . "',e'" . pg_escape_string($rac_nom_genre) . "',e'" . pg_escape_string($trimvalue) ."',$rac_nom_chance)";
							$db->query($req);
						}
					}		
					echo "<p>MAJ</p>";
				break;
			}
    	    
	}
include "admin_edition_header.php";	?>

<p>Noms des monstres</p>
SELECTIONNER UNE RACE:
<form method="post">

<select name="race_cod">
<?php 
$req = "select race_cod,race_nom from race order by race_nom ";
$db->query($req);
while($db->next_record())
{
?>
<option value="<?php  echo  $db->f("race_cod"); ?>"><?php  echo  $db->f("race_nom"); ?></option>
<?php  } ?>
</select>
<input type="submit" value="voir">
</form>
<HR>
<?php  if(isset($_POST['race_cod'])){?>

<p>
quelques exemples:<br>
-Masculins<br>
<?php 
	$req = "select gmon_cod from monstre_generique where gmon_race_cod = $race_cod limit 1";
	$db->query($req);
	$db->next_record();
	$g_mon_ex = $db->f("gmon_cod");
	
for($i = 0; $i < 5; $i++){
	$req = "select choisir_monstre_nom($g_mon_ex,'M') as nom";
	$db->query($req);
	$db->next_record();
	echo  "&nbsp;&nbsp;&nbsp;".$db->f("nom")."<br>";
}
?>
-Feminins<br>
<?php 
for($i = 0; $i < 5; $i++){
	$req = "select choisir_monstre_nom($g_mon_ex,'F') as nom";
	$db->query($req);
	$db->next_record();
	echo  "&nbsp;&nbsp;&nbsp;".$db->f("nom")."<br>";
}
?>
</p>

<form method="post">
<input type="hidden" name="race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="methode" value="update_liste_nom">
<input type="hidden" name="rac_nom_race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="rac_nom_type" value="N">
<input type="hidden" name="rac_nom_genre" value="M">
<b>Noms:</b><br>
<?php 
$chance = '';
$req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
."where rac_nom_race_cod = $race_cod and rac_nom_type = 'N' "
."order by rac_nom_nom ";
$db->query($req);
?>
<textarea name="listenoms" rows="4" cols="80">
<?php  while($db->next_record())
{
	$chance = $db->f("rac_nom_chance");
	echo $db->f("rac_nom_nom").",";
}?></textarea>
Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>">   <input type="submit" value="Mettre à jour !">
</form>  	

<form method="post">
<input type="hidden" name="race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="methode" value="update_liste_nom">
<input type="hidden" name="rac_nom_race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="rac_nom_type" value="P">
<input type="hidden" name="rac_nom_genre" value="M">
<b>Prénoms masculins:</b><br>
<?php 
$chance = '';
$req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
."where rac_nom_race_cod = $race_cod and rac_nom_type = 'P' and rac_nom_genre = 'M'"
."order by rac_nom_nom ";
$db->query($req);
?>
<textarea name="listenoms" rows="4" cols="80">
<?php  while($db->next_record())
{
	$chance = $db->f("rac_nom_chance");
	echo $db->f("rac_nom_nom").",";
}?></textarea>
Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>">   <input type="submit" value="Mettre à jour !">
</form>  

<form method="post">
<input type="hidden" name="race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="methode" value="update_liste_nom">
<input type="hidden" name="rac_nom_race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="rac_nom_type" value="P">
<input type="hidden" name="rac_nom_genre" value="F">
<b>Prénoms féminins:</b><br>
<?php 
$chance = '';
$req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
."where rac_nom_race_cod = $race_cod and rac_nom_type = 'P' and rac_nom_genre = 'F'"
."order by rac_nom_nom ";
$db->query($req);
?>
<textarea name="listenoms" rows="4" cols="80">
<?php  while($db->next_record())
{
	$chance = $db->f("rac_nom_chance");
	echo $db->f("rac_nom_nom").",";
}?></textarea>
Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>">   <input type="submit" value="Mettre à jour !">
</form>      
<form method="post">
<input type="hidden" name="race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="methode" value="update_liste_nom">
<input type="hidden" name="rac_nom_race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="rac_nom_type" value="S">
<input type="hidden" name="rac_nom_genre" value="M">
<b>Surnoms masculins:</b><br>
<?php 
$chance = '';
$req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
."where rac_nom_race_cod = $race_cod and rac_nom_type = 'S' and rac_nom_genre = 'M'"
."order by rac_nom_nom ";
$db->query($req);
?>
<textarea name="listenoms" rows="4" cols="80">
<?php  while($db->next_record())
{
	$chance = $db->f("rac_nom_chance");
	echo $db->f("rac_nom_nom").",";
}?></textarea>
Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>">   <input type="submit" value="Mettre à jour !">
</form>  
<form method="post">
<input type="hidden" name="race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="methode" value="update_liste_nom">
<input type="hidden" name="rac_nom_race_cod" value="<?php  echo $race_cod;?>">
<input type="hidden" name="rac_nom_type" value="S">
<input type="hidden" name="rac_nom_genre" value="F">
<b>Surnoms féminins:</b><br>
<?php 
$chance = '';
$req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
."where rac_nom_race_cod = $race_cod and rac_nom_type = 'S' and rac_nom_genre = 'F'"
."order by rac_nom_nom ";
$db->query($req);
?>
<textarea name="listenoms" rows="4" cols="80">
<?php  while($db->next_record())
{
	$chance = $db->f("rac_nom_chance");
	echo $db->f("rac_nom_nom").",";
}?></textarea>
Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>">   <input type="submit" value="Mettre à jour !">
</form>  

	    
<?php 
	}
} ?>

<p style="text-align:center;"><a href="<?php echo$PHP_SELF ?>">Retour au début</a>

<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
