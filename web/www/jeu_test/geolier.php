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
$req = "select pguilde_rang_cod from guilde_perso where pguilde_perso_cod = $perso_cod and pguilde_rang_cod = 16 ";
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
			echo "<p><a href=\"" , $PHP_SELF , "?methode=prison\">Voir les joueurs en prison</a><br>";
			break;
		case "prison":
			$req = "select perso_cod,perso_nom,lower(perso_nom) as minusc from perso,perso_position,positions ";
			$req = $req . "where perso_actif = 'O' ";
			$req = $req . "and perso_type_perso = 1 ";
			$req = $req . "and perso_cod = ppos_perso_cod ";
			$req = $req . "and ppos_pos_cod = pos_cod ";
			$req = $req . "and pos_etage = 5 ";
			$req = $req . "order by minusc ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucun joueur en prison à ce jour.";
			}
			else
			{
				echo "<table>";	
				echo "<tr>";
				echo "<td class=\"soustitre2\"><strong>Nom</strong></td>";
				echo "<td></td>";
				echo "</tr>";
				while($db->next_record())
				{
					echo "<tr>";
					echo "<td class=\"soustitre2\"><strong>" , $db->f("perso_nom") , "</strong></td>";
					echo "<td><a href=\"" , $PHP_SELF , "?methode=ouvrir&perso=" , $db->f("perso_cod") , "\">Ouvrir la porte ?</a>";
					echo "</tr>";
				}
				echo "</table>";
			}
			break;
		case "ouvrir":
			// nom
			$req = "select ouvrir_prison($perso_cible,$perso_cod) as resultat ";
			$db->query($req);
			$db->next_record();
			echo $db->f("resultat");
			break;
		
	}
	echo "<hr><a href=\"" , $PHP_SELF , "\">Retour à la page principale du geolier.</a><br>";
	echo "<a href=\"milice.php\">Retour à la page milice</a><br>";
	
	
}


$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');