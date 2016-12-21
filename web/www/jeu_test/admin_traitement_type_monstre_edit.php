<?php // RECUPERATION DES INFORMATIONS POUR LE LOG

if (isset($_POST['gmon_cod']) and $methode != 'create_mon')
{
	$req_mons = "select gmon_nom from monstre_generique where gmon_cod = $gmon_cod ";
	$db_mons = new base_delain;
	$db_mons->query($req_mons);
	if($db_mons->next_record()){
		$pmons_mod_nom = $db_mons->f("gmon_nom");
	} else {
		$pmons_mod_nom = $gmon_nom;
	}
}
$log = date("d/m/y - H:i")."$perso_nom (compte $compt_cod) modifie le type de monstre $pmons_mod_nom, numero: $gmon_cod\n";

function writelog($textline){
	$filename="../logs/monstre_edit.log"; // or whatever your path and filename
	if (is_writable($filename))
	{
		if (!$handle = fopen($filename, 'a'))
		{
			echo "Cannot open file ($filename)";
			exit;
		}
		if (fwrite($handle, $textline) === FALSE)
		{
			echo "Cannot write to file ($filename)";
			exit;
		}
		fclose($handle);
	}
	else
		echo "The file $filename is not writable";
}



switch ($methode) {
	case "create_mon":
		$req_cre_mon_cod =  "select nextval('seq_gmon_cod') as cod";
		$db_cre_mon = new base_delain;
		$db_cre_mon->query($req_cre_mon_cod);
		if ($gmon_duree_vie == '') $gmon_duree_vie = 0;
		$gmon_nom = pg_escape_string(htmlspecialchars(str_replace('\'', '’', $gmon_nom)));
		$gmon_description = pg_escape_string(htmlspecialchars(str_replace('\'', '’', $gmon_description)));
		if($db_cre_mon->next_record()){
			$gmon_cod = $db_cre_mon->f("cod");
			$req_cre_gmon = "insert into monstre_generique (gmon_cod,gmon_nom"
				.",gmon_for,gmon_dex,gmon_int,gmon_con"
				.",gmon_race_cod,gmon_temps_tour,gmon_des_regen,gmon_valeur_regen,gmon_vue"
				.",gmon_amelioration_vue,gmon_amelioration_regen,gmon_amelioration_degats,gmon_amelioration_armure"
				.",gmon_niveau,gmon_nb_des_degats,gmon_val_des_degats,gmon_or,gmon_arme,gmon_armure"
				.",gmon_soutien,gmon_amel_deg_dist,gmon_vampirisme,gmon_taille,gmon_description,gmon_quete,gmon_duree_vie) values ($gmon_cod, e'$gmon_nom'"
				.",$gmon_for,$gmon_dex,$gmon_int,$gmon_con"
				.",$gmon_race_cod,$gmon_temps_tour,$gmon_des_regen,$gmon_valeur_regen,$gmon_vue"
				.",$gmon_amelioration_vue,$gmon_amelioration_regen,$gmon_amelioration_degats,$gmon_amelioration_armure"
				.",$gmon_niveau,$gmon_nb_des_degats,$gmon_val_des_degats,$gmon_or,$gmon_arme,$gmon_armure"
				.",'$gmon_soutien',$gmon_amel_deg_dist,$gmon_vampirisme,$gmon_taille, e'$gmon_description', '$gmon_quete',$gmon_duree_vie)";
			$db_cre_mon->query($req_cre_gmon);
		}
		writelog($log."Nouveau type de monstre : $gmon_nom \n");
		echo "Nouveau modèle<br>";
	break;

	case "update_mon":
		$db_cre_mon = new base_delain;
		
		if ($gmon_duree_vie == '') $gmon_duree_vie = 0;
			
		$fields = array("gmon_nom",
			"gmon_for",
			"gmon_dex",
			"gmon_int",
			"gmon_con",
			"gmon_race_cod",
			"gmon_temps_tour",
			"gmon_des_regen",
			"gmon_valeur_regen",
			"gmon_vue",
			"gmon_amelioration_vue",
			"gmon_amelioration_regen",
			"gmon_amelioration_degats",
			"gmon_amelioration_armure",
			"gmon_niveau",
			"gmon_nb_des_degats",
			"gmon_val_des_degats",
			"gmon_or",
			"gmon_arme",
			"gmon_armure",
			"gmon_soutien",
			"gmon_amel_deg_dist",
			"gmon_vampirisme",
			"gmon_taille",
			"gmon_serie_arme_cod",
			"gmon_serie_armure_cod",
			/*"gmon_pv",
			"gmon_pourcentage_aleatoire",*/
			"gmon_nb_receptacle",
			"gmon_type_ia",
			"gmon_description",
			"gmon_quete",
			"gmon_duree_vie");
		// SELECT POUR LES VALEURS PRECEDENTES
		$req_sel_mon = "select gmon_cod";
		foreach ($fields as $i => $value) {
			$req_sel_mon = $req_sel_mon.",".$fields[$i];
		}
		$req_sel_mon = $req_sel_mon." from monstre_generique where gmon_cod = $gmon_cod";
		//echo $req_sel_mon;
		$db_cre_mon->query($req_sel_mon);
		$db_cre_mon->next_record();
		
		foreach ($fields as $i => $value) {
			if(isset($_POST[$fields[$i]]) and $db_cre_mon->f($fields[$i]) != null and $_POST[$fields[$i]] != $db_cre_mon->f($fields[$i])){
				$log = $log."Modification du champ ".$fields[$i]." : ".$db_cre_mon->f($fields[$i])." => ".$_POST[$fields[$i]]."\n";			
			}
		}
	
		writelog($log);
	
		if(!isset($_POST['gmon_vampirisme']) or $gmon_vampirisme == "")
			$gmon_vampirisme = "null";
		if(!isset($_POST['gmon_pv']) or $gmon_pv == "")
			$gmon_pv = "null";
		if(!isset($_POST['gmon_pourcentage_aleatoire']) or $gmon_pourcentage_aleatoire == "")
			$gmon_pourcentage_aleatoire = "null";
		$req_cre_gmon = "update monstre_generique set gmon_nom = e'" . pg_escape_string($gmon_nom) . "'"
			. ",gmon_for = $gmon_for,gmon_dex = $gmon_dex,gmon_int = $gmon_int,gmon_con = $gmon_con"
			. ",gmon_race_cod = $gmon_race_cod,gmon_temps_tour = $gmon_temps_tour,gmon_des_regen = $gmon_des_regen,gmon_valeur_regen = $gmon_valeur_regen,gmon_vue = $gmon_vue"
			. ",gmon_amelioration_vue = $gmon_amelioration_vue,gmon_amelioration_regen = $gmon_amelioration_regen,gmon_amelioration_degats = $gmon_amelioration_degats,gmon_amelioration_armure = $gmon_amelioration_armure"
			. ",gmon_niveau = $gmon_niveau,gmon_nb_des_degats = $gmon_nb_des_degats,gmon_val_des_degats = $gmon_val_des_degats,gmon_or = $gmon_or,gmon_arme = $gmon_arme,gmon_armure = $gmon_armure"
			. ",gmon_serie_arme_cod = $gmon_serie_arme_cod,gmon_serie_armure_cod = $gmon_serie_armure_cod,gmon_type_ia = $gmon_ia,gmon_pv = $gmon_pv,gmon_pourcentage_aleatoire = $gmon_pourcentage_aleatoire"
			. ",gmon_soutien = '$gmon_soutien',gmon_amel_deg_dist = $gmon_amel_deg_dist,gmon_vampirisme = $gmon_vampirisme,gmon_taille = $gmon_taille,gmon_description = e'" . pg_escape_string($gmon_description)
			. "',gmon_nb_receptacle = $gmon_nb_receptacle, gmon_quete = '$gmon_quete', gmon_duree_vie = $gmon_duree_vie where gmon_cod = $gmon_cod";
		echo $req_cre_gmon;
		$db_cre_mon->query($req_cre_gmon);
		echo "MAJ modèle<br>";
	break;

	case "delete_mon_sort":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Suppression d'un sort : $sort_cod - ".$db_upd_mon->f("sort_nom")."\n");
	
		$req_upd_mon =  "delete from sorts_monstre_generique where sgmon_gmon_cod  = $gmon_cod and sgmon_sort_cod = $sort_cod";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Suppression d'un sort";
	break;

	case "add_mon_sort":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Ajout d'un sort : $sort_cod - ".$db_upd_mon->f("sort_nom")."\n");
	
		$req_upd_mon =  "insert into sorts_monstre_generique (sgmon_gmon_cod,sgmon_sort_cod) values ($gmon_cod,$sort_cod)";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Ajout d'un sort";
	break;

	case "delete_mon_immunite":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Suppression d'une immunité : $sort_cod - ".$db_upd_mon->f("sort_nom")."\n");
	
		$req_upd_mon =  "delete from monstre_generique_immunite where immun_gmon_cod  = $gmon_cod and immun_sort_cod = $sort_cod";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Suppression d’une immunité";
	break;
	
	case "add_mon_immunite":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select sort_nom from sorts where sort_cod = $sort_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Ajout d'une immunité : $sort_cod - ".$db_upd_mon->f("sort_nom")."\n");
		$immun_rune = (isset($_POST['immun_rune'])) ? 'O' : 'N';
	
		$req_upd_mon =  "insert into monstre_generique_immunite (immun_sort_cod, immun_gmon_cod, immun_valeur, immun_runes) values ($sort_cod, $gmon_cod, $immun_valeur, '$immun_rune')";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Ajout d’une immunité";
	break;	

	case "add_mon_comp":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select typc_libelle from type_competences where typc_cod = $typc_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Ajout d'un type de competences : $typc_cod - ".$db_upd_mon->f("typc_libelle")." Valeur: $valeur\n");
	
		$req_upd_mon =  "insert into gmon_type_comp (gtypc_gmon_cod,gtypc_typc_cod,gtypc_valeur) values ($gmon_cod,$typc_cod,$valeur)";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Ajout d'une competence";
	break;

	case "mod_comp_mon":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select typc_libelle,gtypc_valeur from gmon_type_comp,type_competences where gtypc_gmon_cod = $gmon_cod and gtypc_typc_cod = $typc_cod and typc_cod = $typc_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Modification d’une compétence : $typc_cod - ".$db_upd_mon->f("typc_libelle")." Chances: ".$db_upd_mon->f("gtypc_valeur")." -> $valeur\n");
	
		$req_upd_mon =  "update gmon_type_comp set gtypc_valeur = $valeur where gtypc_gmon_cod = $gmon_cod and gtypc_typc_cod = $typc_cod";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Modification d’une compétence";
	break;

	case "supr_comp_mon":
		$req_upd_mon =  "delete from gmon_type_comp  where gtypc_gmon_cod = $gmon_cod and gtypc_typc_cod = $typc_cod";
		//echo $req_upd_mon;
		$db_upd_mon = new base_delain;
		$db_upd_mon->query($req_upd_mon);
		
		$req_upd_mon = "select typc_libelle from type_competences where typc_cod = $typc_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Supression d’un type de compétences : $typc_cod - ".$db_upd_mon->f("typc_libelle")."\n");	
		echo "Suppression d’une competence";
	break;

	case "add_mon_comp_spe":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select comp_libelle from competences where comp_cod = $typc_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Ajout d'une competence : $typc_cod - ".$db_upd_mon->f("comp_libelle"));
	
		$req_upd_mon =  "insert into monstre_generique_comp (gmoncomp_gmon_cod,gmoncomp_comp_cod,gmoncomp_valeur,gmoncomp_chance) values ($gmon_cod,$typc_cod,$valeur,$chance)";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Ajout d’une compétence";
	break;

	case "supr_comp_mon_spe":
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select comp_libelle from competences where comp_cod = $typc_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Supression d'une competence : $typc_cod - ".$db_upd_mon->f("comp_libelle"));
	
		$req_upd_mon =  "delete from monstre_generique_comp where gmoncomp_gmon_cod = $gmon_cod and gmoncomp_comp_cod = $typc_cod";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Supression d’une compétence";
	break;

	case "add_mon_drop":
		// AJOUT D'UN DROP
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select gobj_nom from objet_generique where gobj_cod = $gobj_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Ajout d’un Drop : $gobj_cod - ".$db_upd_mon->f("gobj_nom")." Chances: $valeur\n");
	
		$req_upd_mon =  "insert into objets_monstre_generique (ogmon_gmon_cod,ogmon_gobj_cod,ogmon_chance) values ($gmon_cod,$gobj_cod,$valeur)";
		//echo $req_upd_mon;
		$db_upd_mon->query($req_upd_mon);
		echo "Ajout d’un drop";
	break;

	case "mod_drop_mon":
		// MODIFIACTION DES CHANCES D'UN DROP
		$db_upd_mon = new base_delain;
		$req_upd_mon = "select gobj_nom,ogmon_chance from objets_monstre_generique,objet_generique where gobj_cod = $gobj_cod and ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = $gobj_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Modification d’un Drop : $gobj_cod - ".$db_upd_mon->f("gobj_nom")." Chances: ".$db_upd_mon->f("ogmon_chance")." -> $valeur\n");
		
		$req_upd_mon = "update objets_monstre_generique set ogmon_chance = $valeur where ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = $gobj_cod";
		//echo $req_upd_mon;	
		$db_upd_mon->query($req_upd_mon);
		echo "Modification d’un drop";
	break;

	case "supr_drop_mon":
		// SUPPRESSION D'UN DROP
		$req_upd_mon =  "delete from objets_monstre_generique where ogmon_gmon_cod = $gmon_cod and ogmon_gobj_cod = $gobj_cod";
		//echo $req_upd_mon;
		$db_upd_mon = new base_delain;
		$db_upd_mon->query($req_upd_mon);	
		
		$req_upd_mon = "select gobj_nom from objet_generique where gobj_cod = $gobj_cod";
		$db_upd_mon->query($req_upd_mon);
		$db_upd_mon->next_record();
		writelog($log."Suppression d’un Drop : $gobj_cod - ".$db_upd_mon->f("gobj_nom")."\n");
		echo "Suppression d’un drop";
	break;

	case "add_mon_fonction":
		// Modification des effets automatiques
		$db_add_fun = new base_delain;
		$fonctions_supprimees = explode(',', trim(base_delain::format($_POST['fonctions_supprimees']), ','));
		$fonctions_annulees = explode(',', trim(base_delain::format($_POST['fonctions_annulees']), ','));
		$fonctions_existantes = explode(',', trim(base_delain::format($_POST['fonctions_existantes']), ','));
		$fonctions_ajoutees = explode(',', trim(base_delain::format($_POST['fonctions_ajoutees']), ','));
	
		$message = '';
		// Ajout d’effet
		foreach ($fonctions_ajoutees as $numero)
		{
			if (!empty($numero) || $numero === 0)
			{
				if (!in_array($numero, $fonctions_annulees))
				{
					$fonc_type =         base_delain::format($_POST['declenchement_' . $numero]);
					$fonc_nom =          base_delain::format($_POST['fonction_type_' . $numero]);
					$fonc_effet =        !empty($_POST['fonc_effet' . $numero]) ? base_delain::format($_POST['fonc_effet' . $numero]) : '';
					$fonc_force =        !empty($_POST['fonc_force' . $numero]) ? base_delain::format($_POST['fonc_force' . $numero]) : '';
					$fonc_duree =        !empty($_POST['fonc_duree' . $numero]) ? base_delain::format($_POST['fonc_duree' . $numero]) : '0';
					$fonc_type_cible =   !empty($_POST['fonc_cible' . $numero]) ? base_delain::format($_POST['fonc_cible' . $numero]) : '';
					$fonc_nombre_cible = !empty($_POST['fonc_nombre' . $numero]) ? base_delain::format($_POST['fonc_nombre' . $numero]) : '0';
					$fonc_portee =       !empty($_POST['fonc_portee' . $numero]) ? base_delain::format($_POST['fonc_portee' . $numero]) : '0';
					$fonc_proba =        !empty($_POST['fonc_proba' . $numero]) ? base_delain::format($_POST['fonc_proba' . $numero]) : '0';
					$fonc_message =      !empty($_POST['fonc_message' . $numero]) ? base_delain::format($_POST['fonc_message' . $numero]) : '';
				
					$fonc_proba = str_replace(',', '.', $fonc_proba);
	
					$req = "INSERT INTO fonction_specifique (fonc_nom, fonc_gmon_cod, fonc_type, fonc_effet, fonc_force, fonc_duree, fonc_type_cible, fonc_nombre_cible, fonc_portee, fonc_proba, fonc_message)
						values ('$fonc_nom', $gmon_cod, '$fonc_type', '$fonc_effet', '$fonc_force', $fonc_duree, '$fonc_type_cible', '$fonc_nombre_cible', $fonc_portee, $fonc_proba, '$fonc_message')";
					$db_add_fun->query($req);
	
					$texteDeclenchement = '';
					switch ($fonc_type)
					{
						case 'D': $texteDeclenchement = 'le déclenchement de la DLT.'; break;
						case 'T': $texteDeclenchement = 'la mort de la cible du monstre.'; break;
						case 'M': $texteDeclenchement = 'la mort du monstre.'; break;
						case 'A': $texteDeclenchement = 'une attaque portée.'; break;
						case 'AE': $texteDeclenchement = 'une attaque esquivée.'; break;
						case 'AT': $texteDeclenchement = 'une attaque portée qui touche.'; break;
						case 'AC': $texteDeclenchement = 'une attaque subie.'; break;
						case 'ACE': $texteDeclenchement = 'une esquive.'; break;
						case 'ACT': $texteDeclenchement = 'une attaque subie qui touche.'; break;
					}
					if (!empty($message))
						$message .= "			";
					$message .= "Ajout d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";
				}
			}
		}
	
		// Modification d’effet
		foreach ($fonctions_existantes as $numero)
		{
			if (!empty($numero) || $numero === 0)
			{
				$fonc_cod = base_delain::format($_POST['fonc_id' . $numero]);
				if (!in_array($fonc_cod, $fonctions_supprimees))
				{
					$fonc_effet =        !empty($_POST['fonc_effet' . $numero]) ? base_delain::format($_POST['fonc_effet' . $numero]) : '';
					$fonc_force =        !empty($_POST['fonc_force' . $numero]) ? base_delain::format($_POST['fonc_force' . $numero]) : '';
					$fonc_duree =        !empty($_POST['fonc_duree' . $numero]) ? base_delain::format($_POST['fonc_duree' . $numero]) : '0';
					$fonc_type_cible =   !empty($_POST['fonc_cible' . $numero]) ? base_delain::format($_POST['fonc_cible' . $numero]) : '';
					$fonc_nombre_cible = !empty($_POST['fonc_nombre' . $numero]) ? base_delain::format($_POST['fonc_nombre' . $numero]) : '0';
					$fonc_portee =       !empty($_POST['fonc_portee' . $numero]) ? base_delain::format($_POST['fonc_portee' . $numero]) : '0';
					$fonc_proba =        !empty($_POST['fonc_proba' . $numero]) ? base_delain::format($_POST['fonc_proba' . $numero]) : '0';
					$fonc_message =      !empty($_POST['fonc_message' . $numero]) ? base_delain::format($_POST['fonc_message' . $numero]) : '';
				
					$fonc_proba = str_replace(',', '.', $fonc_proba);
	
					$req = "UPDATE fonction_specifique
						SET fonc_effet = '$fonc_effet',
							fonc_force = '$fonc_force',
							fonc_duree = $fonc_duree,
							fonc_type_cible = '$fonc_type_cible',
							fonc_nombre_cible = '$fonc_nombre_cible',
							fonc_portee = $fonc_portee,
							fonc_proba = $fonc_proba,
							fonc_message = '$fonc_message'
						WHERE fonc_cod = $fonc_cod
						RETURNING fonc_nom, fonc_type";
					$db_add_fun->query($req);
					$db_add_fun->next_record();
					$fonc_nom = $db_add_fun->f('fonc_nom');
					$fonc_type = $db_add_fun->f('fonc_type');
					
					$texteDeclenchement = '';
					switch ($fonc_type)
					{
						case 'D': $texteDeclenchement = 'le déclenchement de la DLT.'; break;
						case 'T': $texteDeclenchement = 'la mort de la cible du monstre.'; break;
						case 'M': $texteDeclenchement = 'la mort du monstre.'; break;
						case 'A': $texteDeclenchement = 'une attaque portée.'; break;
						case 'AE': $texteDeclenchement = 'une attaque esquivée.'; break;
						case 'AT': $texteDeclenchement = 'une attaque portée qui touche.'; break;
						case 'AC': $texteDeclenchement = 'une attaque subie.'; break;
						case 'ACE': $texteDeclenchement = 'une esquive.'; break;
						case 'ACT': $texteDeclenchement = 'une attaque subie qui touche.'; break;
					}
					if (!empty($message))
						$message .= "			";
					$message .= "Modification d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";
				}
			}
		}
	
		// Suppression d’effet
		foreach ($fonctions_supprimees as $id)
		{
			if (!empty($id) || $id === 0)
			{
				$req = "SELECT fonc_nom, fonc_type FROM fonction_specifique WHERE fonc_cod = $id";
				$db_add_fun->query($req);
				if ($db_add_fun->next_record())
				{
					$fonc_type = $db_add_fun->f('fonc_type');
					$fonc_nom = $db_add_fun->f('fonc_nom');
					$req = "DELETE FROM fonction_specifique WHERE fonc_cod = $id";
					$db_add_fun->query($req);
				}
	
				$texteDeclenchement = '';
				switch ($fonc_type)
				{
					case 'D': $texteDeclenchement = 'le déclenchement de la DLT.'; break;
					case 'T': $texteDeclenchement = 'la mort de la cible du monstre.'; break;
					case 'M': $texteDeclenchement = 'la mort du monstre.'; break;
					case 'A': $texteDeclenchement = 'une attaque portée.'; break;
					case 'AE': $texteDeclenchement = 'une attaque esquivée.'; break;
					case 'AT': $texteDeclenchement = 'une attaque portée qui touche.'; break;
					case 'AC': $texteDeclenchement = 'une attaque subie.'; break;
					case 'ACE': $texteDeclenchement = 'une esquive.'; break;
					case 'ACT': $texteDeclenchement = 'une attaque subie qui touche.'; break;
				}
				if (!empty($message))
					$message .= "			";
				$message .= "Suppression d’un effet de type '$fonc_nom' sur $texteDeclenchement\n";
			}
		}
	
		writelog($log . $message);
		echo nl2br($message);
	break;
}