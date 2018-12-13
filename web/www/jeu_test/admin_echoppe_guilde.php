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
if (!isset($methode))
{
	$methode = "debut";
}
$req = "select perso_admin_echoppe from perso where perso_cod = $perso_cod ";
$db->query($req);
if ($db->nf() == 0)
{
	echo "<p>Erreur1 ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
else
{
	$db->next_record();
}
if ($db->f("perso_admin_echoppe") != 'O')
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
if ($erreur == 0)
{
	switch ($methode)
	{
		case "debut":
			?>
			<p>Les montants ci dessous sont des modificateurs à l'achat pour les guildes. Un modificateur négatif signifie une remise, un positif un surplus.<br>
			Les modificateurs doivent être compris entre -20 et 20.
			<form name="guilde" method="post" action="admin_echoppe_guilde.php">
			<center><table>
			<input type="hidden" name="methode" value="suite">
			<?php 
			$req = "select guilde_cod,guilde_nom,guilde_modif,lower(guilde_nom) as minuscule from guilde order by minuscule";
			$db->query($req);
			while ($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\">" , $db->f("guilde_nom"), "</td>";	
				echo "<td><input type=\"text\" name=\"modif[" , $db->f("guilde_cod") , "]\" value=\"", $db->f("guilde_modif") , "\"></td>";
				echo "</tr>";
			}
			?>
			
			
			</table><input type="submit" class="test" value="Valider !"></center>
			</form>
			<?php 
			break;
		case "suite":
			foreach($modif as $key=>$val)
			{
				$erreur = 0;
				$req = "select guilde_nom from guilde where guilde_cod = $key ";
				$db->query($req);
				$db->next_record();
				$nom_guilde = $db->f("guilde_nom");
				if (($val < -20) || ($val > 20))
				{
					$erreur = 1 ;
					echo "<p>Anomalie sur la guilde <strong>" , $nom_guilde , "</strong>, le modificateur doit être compris entre -20 et +20 !</p>";
				}
				if ($erreur == 0)
				{
					$req = "update guilde set guilde_modif = $val where guilde_cod = $key ";
					$db->query($req);
					echo "La guilde <strong>" , $nom_guilde , "</strong> a été modifiée ! <br>";
				}	
			}
			?>
			<p style="text-align:center"><a href="admin_echoppe.php">Retour ! </a>
			<?php 
			break;
			
			
	}
	
	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
