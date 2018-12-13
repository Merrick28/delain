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
if ($db->is_milice($perso_cod) == 0)
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
$req = "select pguilde_rang_cod from guilde_perso where pguilde_perso_cod = $perso_cod and pguilde_rang_cod = 0 ";
$db->query($req);
if ($db->nf() == 0)
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
if ($erreur == 0)
{
	if (!isset($methode))
	{
		$methode = "debut";
	}
	switch($methode)
	{
		case "debut":
			echo "<p><a href=\"" , $PHP_SELF , "?methode=solde\">Gérer les soldes ?</a><br>";
			break;
		case "solde":
			$req = "select rguilde_libelle_rang,rguilde_rang_cod,rguilde_cod,rguilde_solde from guilde_rang ";
			$req = $req . "where rguilde_guilde_cod = 49 order by rguilde_rang_cod ";
			$db->query($req);
			?>
			<table>
			<tr>
			<td class="soustitre2"><strong>Rang</strong></td>
			<td class="soustitre2"><strong>Solde</strong></td>
			<td></td>
			</tr>
			<?php 
			while ($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><strong>" , $db->f("rguilde_libelle_rang"), "</strong></td>";
				echo "<td>" , $db->f("rguilde_solde"), " brouzoufs</td>";
				echo "<td><a href=\"" , $PHP_SELF , "?methode=solde2&rang=" , $db->f("rguilde_cod") , "\">Modifier ?</a></td>";
				echo "</tr>";
			}
			?>
			</table>
			<?php 	
			break;
		case "solde2":
			$req = "select rguilde_solde,rguilde_libelle_rang from guilde_rang where rguilde_cod = $rang ";
			$db->query($req);
			$db->next_record();
			?>
			<form action="<?php echo $PHP_SELF;?>" method="post">
			<input type="hidden" name="methode" value="solde3">
			<input type="hidden" name="rang" value="<?php echo $rang;?>">
			Entrez la valeur de la solde mensuelle pour le grade <strong><?php echo $db->f("rguilde_libelle_rang");?></strong> 
			<input type="text" name="solde" value="<?php echo $db->f("rguilde_solde");?>"><br>
			<input type="submit" class="test" value="Valider !"></form>			
			<?php 
			break;
		case "solde3";
			$req = "update guilde_rang set rguilde_solde = $solde where rguilde_cod = $rang ";
			if ($db->query($req))
			{
				echo "<p>Le solde est enregistré !";
			}
			break;
		
	}
	echo "<hr><a href=\"" , $PHP_SELF , "\">Retour à la page principale du préfet.</a><br>";
	echo "<a href=\"milice.php\">Retour à la page milice</a><br>";
	
	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
