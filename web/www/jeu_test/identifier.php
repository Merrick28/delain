<?php 
include_once "verif_connexion.php";
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$limite_exp = $db->getparm_n(1);
$req_identifier = "select identifier_objet($perso_cod,$objet) as identifie";
$db->query($req_identifier);
$db->next_record();
$resultat = $db->f("identifie");
$tab_res = explode(";",$resultat);
if ($tab_res[0] == -1)
{
	echo("<p>Une erreur est survenue : $tab_res[1]");
}
else
{
	echo("<p>Vous avez utilisé la compétence $tab_res[2] ($tab_res[3] %)</p>");
	echo("<p>Votre lancer de dés est <b>$tab_res[4]</b>, ");
	if ($tab_res[5] == -1)
	{
		echo("il s'agit donc d'un échec automatique.");
		echo '<br /><a href="' . $PHP_SELF . '?objet=' . $objet . '">Réessayer ?<a/>';
	}
	if ($tab_res[5] == 0)
	{
		echo("vous avez donc échoué dans cette compétence.<br>");
		if ($tab_res[3] <= $limite_exp)
		{
			echo("Votre compétence est inférieure à $limite_exp %. Votre jet d'amélioration est de <b>$tab_res[6]</b>.<br>");
			if ($tab_res[7] == 0)
			{
				echo("Vous n'avez pas réussi à améliorer cette compétence.");
			}
			if ($tab_res[7] == 1)
			{
				echo("Vous avez réussi à améliorer cette compétence. Sa nouvelle valeur est <b>$tab_res[8]%</b>");
			}
		}
		echo '<br /><a href="' . $PHP_SELF . '?objet=' . $objet . '">Réessayer ?<a/>';
	}
	if ($tab_res[5] == 1)
	{
		echo("vous avez réussi cette compétence !</p>");
		$req_objet = "select obj_nom,obj_enchantable,obj_description from objets ";
		$req_objet = $req_objet . "where obj_cod = " . $objet;
		$db->query($req_objet);
		$db->next_record();
		//$tab_objet = pg_fetch_array($res_objet,0);
		echo "<p>L'objet identifié est : <b>" . $db->f("obj_nom") . "</b>. Vous pouvez maintenant l'utiliser.</p>";
		if($db->f('obj_enchantable') == 1)
			echo "<p>De plus, cet objet est <b>enchantable</b></p>";
		echo "<i>" . $db->f('obj_description') . "</i>";
		echo("<hr>");
		echo("<p>Vous gagnez $tab_res[7] PX.</p>");
		echo("<hr>");
		echo("<p>Votre jet d'amélioration est de $tab_res[8]. ");
		if ($tab_res[9] == 0)
		{
			echo("<p>Vous n'avez pas réussi à améliorer cette compétence.");
		}
		else
		{
			echo("<p>Vous avez réussi à améliorer cette compétence. Sa nouvelle valeur est de $tab_res[10].");
		}
		
	} 
}
?>
<center><a href="inventaire.php">Retour à l'inventaire</a></center>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
