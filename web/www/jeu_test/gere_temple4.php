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
if (!isset($mag))
{
	echo "<p>Erreur sur la transmission du lieu_cod ";
	$erreur = 1;
}
if ($erreur == 0)
{
	$req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle
							from lieu,lieu_position,positions,etage,temple_fidele
							where lieu_cod = lpos_lieu_cod 
							and lieu_tlieu_cod = 17 
							and lpos_pos_cod = pos_cod 
							and pos_etage = etage_numero 
							and tfid_lieu_cod = lieu_cod 
							and tfid_perso_cod = $perso_cod 
							and lieu_cod = $mag";
	$db->query($req);
	if ($db->nf() == 0)
	{
		echo "<p>Erreur, vous n'êtes pas en le disciple responsable de ce temple !";
		$erreur = 1;
	}
	else
	{
		$db->next_record();
		$cod_lieu = $db->f("lieu_cod");
		$lieu_nom = $db->f("lieu_nom");
		$pos_x = $db->f("pos_x");
		$pos_y = $db->f("pos_y");
		$etage_libelle = $db->f("etage_libelle");
	}
}

// RECUPERATION DES INFORMATIONS POUR LE LOG
	$req = "select compt_nom from compte where compt_cod = $compt_cod";
	$db->query($req);
	$db->next_record();
	$compt_nom = $db->f("compt_nom");
	$req_pers = "select perso_nom from perso where perso_cod = $perso_cod ";
	$db_pers = new base_delain;
	$db_pers->query($req_pers);
	if($db_pers->next_record()){
		$perso_mod_nom = $db_pers->f("perso_nom");
	}   
	$log = date("d/m/y - H:i")." $perso_nom (compte $compt_cod / $compt_nom) modifie les statuts du temple $lieu_nom (code : $cod_lieu), X: $pos_x / Y: $pos_y / $etage_libelle\n";



if ($erreur == 0)
{
	if (!isset($methode))
	{
		$methode = "debut";
	}
	echo "<p class=\"titre\">Gestion de : ", $lieu_nom, " - (", $pos_x, ", ", $pos_y, ", ", $etage_libelle, ")</p>";
	switch($methode)
	{
		case "debut":
			break;	
		case "statut";
				echo "Compte tenu des abus déjà constatés, cette fonction est désormais désactivée.";
				/*echo "<p>Votre temple est un refuge. Si vous souhaitez abandonner cette fonctionnalité, ...<br>";*/
				/*echo "<a href=\"gere_echoppe4.php?mag=$mag&methode=statut2&ref=n\">Abandonner le statut de refuge pour ce temple ?</a>";*/
			break;
		case "statut2";	
			echo "Compte tenu des abus déjà constatés, cette fonction est désormais désactivée.";
			/*
			if ($ref == 'n')
			{
				$req = "update lieu set lieu_refuge = 'N',lieu_prelev = 15 where lieu_cod = $mag";
				$db->query($req);
				echo "<p>La modification a été effectuée.";
				$log = $log."Modification du statut pour passer en normal\n";
			}
			if ($ref == 'o')
			{
				$req = "update lieu set lieu_refuge = 'O',lieu_prelev = 30 where lieu_cod = $mag";
				$db->query($req);
				echo "<p>La modification a été effectuée.";
				$log = $log."Modification du statut pour passer en mode refuge\n";
			}*/
			writelog($log,'temple');
			break;
		case "nom";	
			echo "<b>Attention !</b> Toute modification est définitive. Les descriptions réalisées pour les temples ont été faites avec soin. Veillez à ne pas tout gacher.<br /><br />";
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_temple4.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"nom2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
			$req = "select lieu_nom,lieu_description from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			
			echo "<table>";
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Nom du temple (70 caracs maxi)</td>";
			echo "<td><input type=\"text\" name=\"nom\" size=\"50\" value=\"" . $db->f("lieu_nom") . "\"></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Description</td>";
			$desc = str_replace(chr(127),";",$db->f("lieu_description"));
			echo "<td><textarea name=\"desc\" rows=\"20\" cols=\"100\">" . $desc . "</textarea></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan=\"2\"><input type=\"submit\" class=\"test\" value=\"Valider les changements\"></td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "</form>";
		
			break;
		case "nom2":
			echo "<p><b>Aperçu : " . $desc;
			$desc = str_replace(";",chr(127),$desc);
			$req = "update lieu set lieu_nom = e'" . pg_escape_string($nom) . "', lieu_description = e'" . pg_escape_string($desc) . "' where lieu_cod = $mag ";
			$db->query($req);
			echo "<p>Les changements sont validés !";
			break;
		case "vente_adm":
			break;
	}
	echo "<p style=\"text-align:center;\"><a href=\"gere_temple3.php?mag=$mag\">Retour à la gestion du temple</a>";
	echo "<p style=\"text-align:center;\"><a href=\"gere_temple.php\">Retour à la liste des temples gérees</a>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');