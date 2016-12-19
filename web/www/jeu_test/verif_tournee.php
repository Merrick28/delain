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
switch($methode)
{
	case "debut":
		$req = "select perso_cod,perso_nom ";
		$req = $req . "from perso,quete_perso ";
		$req = $req . "where pquete_quete_cod = 6 ";
		$req = $req . "and pquete_perso_cod = perso_cod ";
		$db->query($req);
		echo "<p><b>Liste des inscrits :</b></br>";
		while ($db->next_record())
		{
			echo "<a href=\"verif_tournee.php?methode=verif&perso=", $db->f("perso_cod"), "\">", $db->f("perso_nom"), "</a></br>";
		}
		break;
	case "verif":
		$req = "select paub_nombre,lieu_nom,pos_x,pos_y,etage_libelle ";
		$req = $req . "from perso_auberge,lieu,positions,etage,lieu_position ";
		$req = $req . "where paub_perso_cod = $perso ";
		$req = $req . "and paub_lieu_cod = lieu_cod ";
		$req = $req . "and lpos_lieu_cod = lieu_cod ";
		$req = $req . "and lpos_pos_cod = pos_cod ";
		$req = $req . "and etage_numero = pos_etage ";
		$db->query($req);
		if ($db->nf() == 0)
		{
			echo "<p>Aucune boisson n'a été prise dans les auberges !";
		}
		else
		{
			while ($db->next_record())
			{
				echo "<b>", $db->f("lieu_nom"), "</b> : ", $db->f("pos_x"), ", ", $db->f("pos_y"), ", ", $db->f("etage_libelle"), "(", $db->f("paub_nombre"), " boissons prises).<br>";
			}		
		}
	
		break;
}


$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
