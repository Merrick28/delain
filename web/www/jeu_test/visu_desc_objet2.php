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
$objet = $_GET['objet'];
if (!preg_match('/^[0-9]*$/i', $objet))
{
	echo "<p>Anomalie sur numéro objet !";
	exit();
}
$autorise = 0;
$req = "select perobj_cod from perso_objets,objets
	where perobj_perso_cod = $perso_cod
	and perobj_obj_cod = obj_cod
	and perobj_identifie = 'O' 
	and obj_gobj_cod = $objet ";
$db->query($req);
if ($db->nf() != 0)
	$autorise = 1;
// on regarde si l'objet est dans une échoppe sur laquelle on est

if ($db->is_lieu($perso_cod))
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$lieu_cod = $tab_lieu['lieu_cod'];
	$req = "select mstock_obj_cod from stock_magasin,objets
		where mstock_lieu_cod = $lieu_cod
		and mstock_obj_cod = obj_cod
		and obj_gobj_cod = $objet";
	$db->query($req);
	if ($db->nf() != 0)
		$autorise = 1;
		
  $req = "select mgstock_cod from  	stock_magasin_generique
		where mgstock_lieu_cod = $lieu_cod
		and mgstock_gobj_cod = $objet";
	$db->query($req);
	if ($db->nf() != 0)
		$autorise = 1;
}
if ($autorise == 1)
{
	$req = "select gobj_nom, gobj_tobj_cod, tobj_libelle, gobj_poids, gobj_pa_normal, gobj_pa_eclair, gobj_distance, gobj_deposable, gobj_comp_cod,
				gobj_description, gobj_seuil_dex, gobj_seuil_force, gobj_niveau_min 
			from objet_generique,type_objet 
			where gobj_cod = $objet 
				and gobj_tobj_cod = tobj_cod 
				and (gobj_visible is null or gobj_visible != 'N') ";
	$db->query($req);
	if ($db->nf() != 0)
	{
		$db->next_record();
		$t_etat = 0;
		$comp = $db->f("gobj_comp_cod");
		$desc = $db->f("gobj_description");
		$distance = $db->f("gobj_distance");
		$type_objet = $db->f("gobj_tobj_cod");
		$seuil_force = $db->f("gobj_seuil_force");
		$seuil_dex =  $db->f("gobj_seuil_dex");
		$niveau_min =  $db->f("gobj_niveau_min");
		echo "<p class=\"titre\">" . $db->f("gobj_nom") . "</p>";
		echo "<center><table>";
		
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p>Type d’objet</p></td>";
		echo "<td><p>" . $db->f("tobj_libelle");
		if ($db->f("gobj_deposable") == 'N')
		{
			echo " <b>non déposable !</b>";
		}
		echo "</p></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p>Poids</p></td>";
		echo "<td><p>" . $db->f("gobj_poids") . "</p></td>";
		echo "</tr>";
		
		if ($type_objet == 1)
		{
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque normale</p></td>";
			echo "<td><p>" . $db->f("gobj_pa_normal") . "</p></td>";
			echo "</tr>";	
			
			if ($distance == 'N')
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>Coût en PA pour une attaque foudroyante</p></td>";
				echo "<td><p>" . $db->f("gobj_pa_eclair") . "</p></td>";
				echo "</tr>";	
			}
			
			$req = "select obcar_des_degats,obcar_val_des_degats,obcar_bonus_degats ";
			$req = $req . "from objets_caracs,objet_generique ";
			$req = $req . "where gobj_cod = $objet ";
			//$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and gobj_obcar_cod = obcar_cod ";
			$db->query($req);
			$db->next_record();
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Dégâts</p></td>";
			echo "<td><p>" . $db->f("obcar_des_degats") . "D" . $db->f("obcar_val_des_degats") . "+" . $db->f("obcar_bonus_degats") . "</p></td>";
			echo "</tr>";	
			
			
			$req = "select comp_libelle from competences where comp_cod = $comp ";
			$db->query($req);
			$db->next_record();
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Compétence utilisée</p></td>";
			echo "<td><p>" . $db->f("comp_libelle") . "</p></td>";
			echo "</tr>";
			if ($seuil_force > 0)
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>Seuil de force</p></td>";
				echo "<td><p>" . $seuil_force . "</p></td>";
				echo "</tr>";
			}
			if ($seuil_dex > 0)
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>Seuil de dextérité</p></td>";
				echo "<td><p>" . $seuil_dex . "</p></td>";
				echo "</tr>";
			}
			if ($niveau_min > 0)
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>Niveau minimum pour équiper</p></td>";
				echo "<td><p>" . $niveau_min . "</p></td>";
				echo "</tr>";
			}
		}
		if ($type_objet == 2)
		{
			$req = "select obcar_armure ";
			$req = $req . "from objets_caracs,objet_generique,objets ";
			$req = $req . "where gobj_cod = $objet ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and gobj_obcar_cod = obcar_cod ";
			$db->query($req);
			$db->next_record();
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Armure</p></td>";
			echo "<td><p>" . $db->f("obcar_armure") . "</p></td>";
			echo "</tr>";	
		}
		if ($type_objet == 1 and $distance == 'O')
		{
			$req = "select obcar_chute ";
			$req = $req . "from objets_caracs,objet_generique,objets ";
			$req = $req . "where gobj_cod = $objet ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and gobj_obcar_cod = obcar_cod ";
			$db->query($req);
			$db->next_record();
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Chute</p></td>";
			echo "<td><p>" . $db->f("obcar_chute") . "</p></td>";
			echo "</tr>";	
		}		
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p>Description</p></td>";
		echo "<td><p>" . $desc . "</p></td>";
		echo "</tr>";	
		
		if (isset($bon))
		{
			$req = "select obon_libelle from bonus_objets where obon_cod = $bon ";
			$db->query($req);
			$db->next_record();
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Bonus</p></td>";
			echo "<td><p>" . $db->f("obon_libelle") . "</p></td>";
			echo "</tr>";	
		}
		
		echo "</table></center>";
	}
	else
	{
		echo "<p>Aucun objet trouvé !";
	}
}
else
{
	echo "Vous n'avez pas accès au détail de cet objet !";
}
$retour = "inventaire.php";
if ($origine == 'e')
{
	$retour = "lieu.php?methode=acheter";
}
if ($origine == 'a')
{
	$retour = "admin_echoppe_tarif.php";
}
echo "<p style=\"text-align:center;\"><a href=\"$retour\">Retour !</a>";
	
	
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');

