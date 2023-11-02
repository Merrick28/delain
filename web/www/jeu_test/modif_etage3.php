<?php
/* Création d’étages, modification des paramètres de case de l’étage */

include "blocks/_header_page_jeu.php";
include "../includes/tools.php";



//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur = 0;
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";

$methode          = get_request_var('methode', 'debut');

if (isset($_REQUEST["admin_etage"]) && $_REQUEST["admin_etage"]!=0 && $pos_etage==0) $pos_etage = $_REQUEST["admin_etage"] ;

$log = '';
$resultat = '';

if ($erreur == 0)
{

	if (!isset($_REQUEST["methode"]) && isset($pos_etage) && $pos_etage == '--')
		$methode = 'début_créer';
	if (!isset($_REQUEST["methode"]) && isset($pos_etage) && $pos_etage != '--')
		$methode = 'début_modifier';

	// Traitements
	switch ($methode)
	{
		case "creer_etage":
			$erreur = 0;
			if(!$nom)
			{
				$resultat .= "<p><strong>Le nom du nouvel étage n’est pas valide !</strong></p>";
				$erreur = 1;
			}
			if(!$description)
			{
				$resultat .= "<p><strong>La description du nouvel étage n’est pas valide !</strong></p>";
				$erreur = 1;
			}
			if(!$x_min || !$x_max || $x_min >= $x_max)
			{
				$resultat .= "<p><strong>Les limites de l’étage ne sont pas valides</strong></p>";
				$erreur = 1;
			}
			if(!$y_min || !$y_max || $y_min >= $y_max)
			{
				$resultat .= "<p><strong>Les limites de l’étage ne sont pas valides</strong></p>";
				$erreur = 1;
			}
			if ($etage_arene == 'O' && $etage_ref == '--')
			{
				$resultat .= "<p><strong>Un étage principal ne peut pas être une arène</strong></p>";
				$erreur = 1;
			}
			$etage_numero = 999;
			if ($etage_ref == '--')	// Création d’un étage principal
			{
				$req = "select min(etage_numero) - 1 as numero from etage where etage_numero <> -100";
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$etage_numero = $result['numero'];
			}
			if($erreur == 0)
			{
				$req = "select nextval('seq_etage_cod') as etage_cod";
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$etage_cod = $result['etage_cod'];
				$etage_numero = ($etage_numero = 999) ? $etage_cod : $etage_numero;
                $etage_monture = json_encode(   ["pa_action" => $etage_monture_pa_action,
                                                "ordre_echec" => $etage_monture_ordre_echec,
                                                "ordre_incident" => $etage_monture_ordre_inc,
                                                "ordre_talonner" => $etage_monture_ordre_talonner,
                                                "ordre_sauter" => $etage_monture_ordre_sauter,
                                                "ordre_talonner_echec"=>$etage_monture_ordre_talonner_echec,
                                                "ordre_sauter_echec"=>$etage_monture_ordre_sauter_echec,
                                                "ordre_talonner_incident"=>$etage_monture_ordre_talonner_inc,
                                                "ordre_sauter_incident"=>$etage_monture_ordre_sauter_inc,
                                                "ordre_talonner_bm"=>$etage_monture_ordre_talonner_bm,
                                                "ordre_sauter_bm"=>$etage_monture_ordre_sauter_bm]) ;
				$nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
				$description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));
				$etage_ref = ($etage_ref == '--') ? $etage_numero : $etage_ref;
				$etage_mort = ($etage_mort == '--') ? $etage_ref : $etage_mort;
				$etage_affichage = ($etage_affichage == '--') ? 2 : $etage_affichage;
				$etage_retour_rune_monstre = ($etage_retour_rune_monstre == '') ? 50 : $etage_retour_rune_monstre;
				$req = "insert into etage (etage_cod, etage_numero, etage_libelle, etage_reference, etage_description,
						etage_affichage, etage_arene, etage_familier_actif, etage_quatrieme_perso,etage_mort_speciale,etage_monture, etage_duree_imp_f, etage_duree_imp_p, etage_mort, etage_retour_rune_monstre,
						etage_mine, etage_mine_type, etage_mine_richesse) "
					."values ($etage_cod, $etage_numero, e'$nom', $etage_ref, e'$description',
						'$etage_affichage', '$etage_arene', '$etage_familier_actif', '$etage_quatrieme_perso', '$etage_mort_speciale', '$etage_monture','$etage_duree_imp_f', '$etage_duree_imp_p', $etage_mort, $etage_retour_rune_monstre,
						$etage_mine, $etage_mine_type, $etage_mine_richesse)";
				$stmt = $pdo->query($req);

				if ($etage_arene == 'O')
				{
					$req = "insert into carac_arene (carene_etage_numero, carene_level_max, carene_level_min, carene_ouverte)
						VALUES($etage_numero, $carene_level_max, $carene_level_min, '$carene_ouverte')";
					$stmt = $pdo->query($req);
				}

				$req = "select cree_etage($etage_numero, $x_min, $x_max, $y_min, $y_max)";
				$stmt = $pdo->query($req);
				$req ="create table perso_vue_pos_".$etage_cod." (  pvue_perso_cod INT not null, pvue_pos_cod INT not null )";
				$stmt = $pdo->query($req);
				$req = "ALTER TABLE perso_vue_pos_".$etage_cod." ADD CONSTRAINT pk_perso_vue_pos_".$etage_cod." PRIMARY KEY (pvue_perso_cod, pvue_pos_cod)";
				$stmt = $pdo->query($req);
				$req = "ALTER TABLE perso_vue_pos_".$etage_cod." ADD CONSTRAINT fk_pvue_perso_cod".$etage_cod." FOREIGN KEY (pvue_perso_cod) REFERENCES perso (perso_cod) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;";
				$stmt = $pdo->query($req);
				$req = "ALTER TABLE perso_vue_pos_".$etage_cod." OWNER TO delain;";
				$stmt = $pdo->query($req);

				$resultat .= "Étage $nom ($etage_numero) créé !\n";
				if ($etage_arene == 'O')
					$resultat .= "Arène\n";
				if ($etage_quatrieme_mortel == 'O')
					$resultat .= "Autorisé aux 4e persos mortels\n";
				else
					$resultat .= "Interdit aux 4e persos mortels\n";
				if ($etage_quatrieme_perso == 'O')
					$resultat .= "Autorisé aux 4e persos limités\n";
				else
					$resultat .= "Interdit aux 4e persos limités\n";

				if ($etage_mort_speciale == '0')
					$resultat .= "Etage avec mort au comportement Normal\n";
				else
					$resultat .= "Etage avec mort au comportement Course de monture\n";
			}
		break;

		case "modifier_etage":
			$erreur = 0;
			if($etage_numero === false)
			{
				$resultat .= "<p><strong>Le numéro de l’étage n’est pas défini !</strong></p>";
				$erreur = 1;
			}
			if(!$nom)
			{
				$resultat .= "<p><strong>Le nom de l’étage n’est pas valide !</strong></p>";
				$erreur = 1;
			}
			if(!$description)
			{
				$resultat .= "<p><strong>La description de l’étage n’est pas valide !</strong></p>";
				$erreur = 1;
			}
			if($erreur == 0)
			{
				$req = "select etage_reference from etage where etage_numero = $etage_numero";
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$etage_ref = $result['etage_reference'];

				$req_avant = "select etage_libelle, etage_description, etage_affichage, etage_arene, etage_familier_actif, etage_quatrieme_perso, etage_quatrieme_mortel, 
                        etage_mort_speciale,etage_monture,etage_duree_imp_f, etage_duree_imp_p, 
						etage_mort, etage_retour_rune_monstre, etage_mine, etage_mine_type, etage_mine_richesse,
						coalesce(carene_level_min, 0) as carene_level_min, coalesce(carene_level_max, 0) as carene_level_max, coalesce(carene_ouverte, 'O') as carene_ouverte
					from etage
					left outer join carac_arene on carene_etage_numero = etage_numero
					where etage_numero = $etage_numero";
				$stmt = $pdo->query($req_avant);
				$result = $stmt->fetch();
				$nom_avant = $result['etage_libelle'];
				$desc_avant = $result['etage_description'];
				$affichage_avant = $result['etage_affichage'];
				$arene_avant = $result['etage_arene'];
				$quatrieme_avant = $result['etage_quatrieme_perso'];
				$quatrieme_mortel_avant = $result['etage_quatrieme_mortel'];
				$mort_speciale_avant = $result['etage_mort_speciale'];
				$monture_ordre_avant = $result['etage_monture'];
				$mort_avant = $result['etage_mort'];
				$rune_avant = $result['etage_retour_rune_monstre'];
				$mine_avant = $result['etage_mine'];
				$mine_type_avant = $result['etage_mine_type'];
				$richesse_avant = $result['etage_mine_richesse'];
				$carene_level_max_avant = $result['carene_level_max'];
				$carene_level_min_avant = $result['carene_level_min'];
				$carene_ouverte_avant = $result['carene_ouverte'];
                $etage_familier_actif_avant = $result['etage_familier_actif'];
                $duree_imp_f_avant = $result['etage_duree_imp_f'];
                $duree_imp_p_avant = $result['etage_duree_imp_p'];


				$nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
				$description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));
				$etage_mort = ($etage_mort == '--') ? $etage_ref : $etage_mort;
				$etage_affichage = ($etage_affichage == '--') ? 2 : $etage_affichage;
				$etage_retour_rune_monstre = ($etage_retour_rune_monstre == '') ? 50 : $etage_retour_rune_monstre;
                $etage_monture = json_encode(
                        ["pa_action" => $etage_monture_pa_action,
                        "ordre_echec" => $etage_monture_ordre_echec,
                        "ordre_incident" => $etage_monture_ordre_inc,
                        "ordre_talonner" => $etage_monture_ordre_talonner,
                        "ordre_sauter" => $etage_monture_ordre_sauter,
                        "ordre_talonner_echec"=>$etage_monture_ordre_talonner_echec,
                        "ordre_sauter_echec"=>$etage_monture_ordre_sauter_echec,
                        "ordre_talonner_incident"=>$etage_monture_ordre_talonner_inc,
                        "ordre_sauter_incident"=>$etage_monture_ordre_sauter_inc,
                        "ordre_talonner_bm"=>$etage_monture_ordre_talonner_bm,
                        "ordre_sauter_bm"=>$etage_monture_ordre_sauter_bm]) ;

				$req = "update etage set
						etage_libelle = e'$nom',
						etage_description = e'$description',
						etage_affichage = '$etage_affichage',
						etage_arene = '$etage_arene',
						etage_familier_actif = '$etage_familier_actif',
						etage_quatrieme_perso = '$etage_quatrieme_perso',
						etage_quatrieme_mortel = '$etage_quatrieme_mortel',
						etage_mort_speciale = '$etage_mort_speciale',
						etage_monture = '$etage_monture',
						etage_duree_imp_f = '$etage_duree_imp_f',
						etage_duree_imp_p = '$etage_duree_imp_p',
						etage_reference = $etage_reference,
						etage_mort = $etage_mort,
						etage_retour_rune_monstre = $etage_retour_rune_monstre,
						etage_mine = $etage_mine,
						etage_mine_type = $etage_mine_type,
						etage_mine_richesse = $etage_mine_richesse
					where etage_numero = $etage_numero";
				$stmt = $pdo->query($req);

				$req = "delete from carac_arene where carene_etage_numero = $etage_numero";
				$stmt = $pdo->query($req);
				if ($etage_arene == 'O')
				{
					$req = "insert into carac_arene (carene_etage_numero, carene_level_max, carene_level_min, carene_ouverte)
						VALUES($etage_numero, $carene_level_max, $carene_level_min, '$carene_ouverte')";
					$stmt = $pdo->query($req);
				}

				$resultat .= "Étage $nom ($etage_numero) modifié !<br>";
				$resultat .= ($nom == $nom_avant) ? '' : "\nNom passe de $nom_avant à $nom";
				$resultat .= ($description == $desc_avant) ? '' : "\nLa description est changée.";
				$resultat .= ($etage_affichage == $affichage_avant) ? '' : "\nStyle d’affichage passe de $affichage_avant à $etage_affichage";
				$resultat .= ($etage_arene == $arene_avant) ? '' : "\nArène passe de $arene_avant à $etage_arene";
				if ($etage_arene == 'O')
				{
					$resultat .= ($etage_arene == $arene_avant) ? '' : "\nArène - Niveau Min passe de $carene_level_min_avant à $carene_level_min";
					$resultat .= ($etage_arene == $arene_avant) ? '' : "\nArène - Niveau Max passe de $carene_level_max_avant à $carene_level_max";
					$resultat .= ($etage_arene == $arene_avant) ? '' : "\nArène - Ouverte passe de $carene_ouverte_avant à $carene_ouverte";
					$resultat .= ($etage_familier_actif_avant == $etage_familier_actif) ? '' : "\nArène - Ouverte passe de $etage_familier_actif_avant à $etage_familier_actif";
				}
				$resultat .= ($etage_quatrieme_perso == $quatrieme_avant) ? '' : "\nAutorisé aux 4èmes persos limités passe de $quatrieme_avant à $etage_quatrieme_perso";
				$resultat .= ($etage_quatrieme_mortel == $quatrieme_mortel_avant) ? '' : "\nAutorisé aux 4èmes persos mortels passe de $quatrieme_mortel_avant à $etage_quatrieme_mortel";
				$resultat .= ($etage_mort_speciale == $mort_speciale_avant) ? '' : "\nLa mort spéciale passe de $mort_speciale_avant à $etage_mort_speciale";
				$resultat .= ($etage_monture == $monture_ordre_avant) ? '' : "\nLe cout en PA des ordres de monture passe de $monture_ordre_avant à $etage_monture";
				$resultat .= ($etage_duree_imp_f == $duree_imp_f_avant) ? '' : "\nLe délai d''impalbilité du perso passe de $duree_imp_f_avant à $etage_duree_imp_f";
				$resultat .= ($etage_duree_imp_p == $duree_imp_p_avant) ? '' : "\nLe délai d''impalbilité du familier passe de $duree_imp_p_avant à $etage_duree_imp_p";
				$resultat .= ($etage_mort == $mort_avant) ? '' : "\nÉtage de référence à la mort passe de $mort_avant à $etage_mort";
				$resultat .= ($etage_retour_rune_monstre == $rune_avant) ? '' : "\nTaux de runes allant aux monstres passe de $rune_avant à $etage_retour_rune_monstre";
				$resultat .= ($etage_mine == $mine_avant) ? '' : "\nTaux d’éboulements passe de $mine_avant à $etage_mine";
				$resultat .= ($etage_mine_type == $mine_type_avant) ? '' : "\nStyle d’éboulements passe de $mine_type_avant à $etage_mine_type";
				$resultat .= ($etage_mine_richesse == $richesse_avant) ? '' : "\nRichesse des éboulements passe de $richesse_avant à $etage_mine_richesse";
			}
		break;
	}

	// Récupération des images existantes
	// On y va à la bourrin : on parcourt tous les fichiers du répertoire images.
	$patron_fond = '/^f_(?P<affichage>[0-9a-zA-Z]+)_(?P<type>\d+)\.png$/';
	$patron_mur = '/^t_(?P<affichage>[0-9a-zA-Z]+)_mur_(?P<type>\d+)\.png$/';
	$chemin = '../../images/';

	$tableau_styles = array();
	$js_tab_fonds = "\nvar tab_fonds = new Array();";
	$js_tab_murs = "\nvar tab_murs = new Array();";

	$rep = opendir($chemin);
	while (false !== ($fichier = readdir($rep)))
	{
		$correspondances = array();
		if (1 === preg_match($patron_fond, $fichier, $correspondances))
		{
			if (!isset($tableau_styles[$correspondances['affichage']]))
			{
				$tableau_styles[$correspondances['affichage']] = $correspondances['affichage'];
				$js_tab_fonds .= "\ntab_fonds['" . $correspondances['affichage'] . "'] = new Array();";
				$js_tab_murs .= "\ntab_murs['" . $correspondances['affichage'] . "'] = new Array();";
			}
			$js_tab_fonds .= "\ntab_fonds['" . $correspondances['affichage'] . "'][" . $correspondances['type'] . "] = " . $correspondances['type'] . ";";
		}
		$correspondances = array();
		if (1 === preg_match($patron_mur, $fichier, $correspondances))
		{
			if (!isset($tableau_styles[$correspondances['affichage']]))
			{
				$tableau_styles[$correspondances['affichage']] = $correspondances['affichage'];
				$js_tab_fonds .= "\ntab_fonds['" . $correspondances['affichage'] . "'] = new Array();";
				$js_tab_murs .= "\ntab_murs['" . $correspondances['affichage'] . "'] = new Array();";
			}
			$js_tab_murs .= "\ntab_murs['" . $correspondances['affichage'] . "'][" . $correspondances['type'] . "] = " . $correspondances['type'] . ";";
		}
	}

	echo "<script type='text/javascript'>
		$js_tab_fonds
		$js_tab_murs
		function changeStyle(style)
		{
			var div_mur = document.getElementById('visu_murs');
			var div_fond = document.getElementById('visu_fonds');

			var chaine_contenu = '';

			for (var i in tab_fonds[style])
			{
				var nom = '" . G_IMAGES . "f_' + style + '_' + i + '.png';
				chaine_contenu += '<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img src=\"' + nom + '\"/><br />' + i + '</div>';
			}
			div_fond.innerHTML = chaine_contenu;

			var chaine_contenu = '';
			for (var i in tab_murs[style])
			{
				var nom = '" . G_IMAGES . "t_' + style + '_mur_' + i + '.png';
				chaine_contenu += '<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img src=\"' + nom + '\"/><br />' + i + '</div>';
			}
			div_mur.innerHTML = chaine_contenu;
		}
		</script>";
	?>

	<div class="barrTitle"> Création / Modification des étages </div>
	<br />
<?php 	if ($resultat != '')
	{
        $fonctions = new fonctions();
        $fonctions->ecrireResultatEtLoguerLoguer($resultat, $erreur == 0);
    }
?>
	Choix de l’étage à modifier
	<form method="post" action="modif_etage3.php">
		<select name="pos_etage"><option value='--'>Créer un nouvel étage</option>
	<?php
		if (!isset($pos_etage)) $pos_etage = '';
		echo $html->etage_select($pos_etage);
	?>
	</select><br>
	<input type="submit" value="Valider" class='test'>
	</form>
	<hr>
<?php 	if ($methode == 'début_créer')
	{
?>
	<p>Créer un nouvel étage</p>
	<div id="etage" class="tableau2">
		<form name="action_creer_etage" method="post" action="modif_etage3.php">
			<input type="hidden" name="methode" value="creer_etage">
			Nom : <input type="text" name="nom"><br>
			Description : <textarea name="description"></textarea><br>
			Rattaché à l’étage... <select name="etage_ref"><option value="--">Aucun (création d’un étage principal)</option>
	<?php
		echo $html->etage_select('', ' where etage_reference = etage_numero');
	?>
			</select><br />
			Quel style de cases pour l’étage ? <small><em>Voir l’aperçu dessous</em></small>
			<select name="etage_affichage" onChange="changeStyle(this.value)">
				<option value='--'>Choisissez un style...</option>
			<?php 				foreach ($tableau_styles as $unStyle)
					echo "<option value='$unStyle'>$unStyle</option>";
			?>
			</select> <a href='modif_etage3_fonds.php' target='_blank'>Voir tous les styles</a><br /><br />
			Étage de référence en cas de mort : <select name="etage_mort"><option value="--">Par défaut</option>
	<?php
		echo $html->etage_select('', ' where etage_reference = etage_numero');
        $sel_monture_ordre_talonner = create_selectbox("etage_monture_ordre_talonner", ["0"=>"Interdit", "1"=>"1x par DLT", "2"=>"2x par DLT", "0.5"=>"1x toutes les 2 DLT"], 0);
        $sel_monture_ordre_sauter = create_selectbox("etage_monture_ordre_sauter", ["0"=>"Interdit", "1"=>"1x par DLT", "2"=>"2x par DLT", "0.5"=>"1x toutes les 2 DLT"], 0);
        $sel_monture_ordre_talonner_echec = create_selectbox("etage_monture_ordre_talonner_echec", ["0"=>"Sans incident", "1"=>"Sur échec critique", "2"=>"Sur échec"], 0);
        $sel_monture_ordre_sauter_echec = create_selectbox("etage_monture_ordre_sauter_echec", ["0"=>"Sans incident", "1"=>"Sur échec critique", "2"=>"Sur échec"], 0);
        $sel_monture_ordre_talonner_inc = create_selectbox("etage_monture_ordre_talonner_inc", ["0"=>"Donner des ordres aléatoires", "1"=>"Désarçonner"], 0);
        $sel_monture_ordre_sauter_inc = create_selectbox("etage_monture_ordre_sauter_inc", ["0"=>"Donner des ordres aléatoires", "1"=>"Désarçonner"], 0);
        $sel_monture_ordre_talonner_bm = '<input name ="etage_monture_ordre_talonner_bm" value="0" type="text" size="4">';
        $sel_monture_ordre_sauter_bm = '<input name ="etage_monture_ordre_sauter_bm" value="0" type="text" size="4">';
        $sel_monture_ordre_echec = create_selectbox("etage_monture_ordre_echec", ["0"=>"Sans incident", "1"=>"Sur échec critique", "2"=>"Sur échec"], 0);
        $sel_monture_ordre_inc = create_selectbox("etage_monture_ordre_inc", ["0"=>"Donner des ordres aléatoires", "1"=>"Désarçonner"], 0);

    ?>
			</select><br />
			L’étage est-il une arène ? <select name="etage_arene"><option value='N'>Non</option><option value='O'>Oui</option></select> -- Si Oui :
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Niveau minimum pour y accéder ? <input type="text" name="carene_level_min" value="0" size="4"/> (0 = aucune limite)
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Niveau maximal pour y accéder ? <input type="text" name="carene_level_max" value="0" size="4"/> (0 = aucune limite)
            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Arène accessible librement ? <select name="carene_ouverte"><option value='O'>Oui</option><option value='N'>Non</option></select> (Non = pour animation uniquement)
            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Arène accessible aux familiers ? <select name="etage_familier_actif"><option value='0'>Non</option><option value='1' >Oui</option></select> <em>(si NON il sera impalpable)</em>
			<br />
			L’étage est-il ouvert aux 4e persos limités en niveau ? <select name="etage_quatrieme_perso"><option value='N'>Non</option><option value='O'>Oui</option></select><br />
			L’étage est-il ouvert aux 4e persos mortels ? <select name="etage_quatrieme_mortel"><option value='N'>Non</option><option value='O'>Oui</option></select><br /><br />
			La mort a cet étage a-t-elle un comportement spécial ? <select name="etage_mort_speciale"><option value='0'>Normal</option><option value='1'>Course de monture</option></select><br />
            -- Delai d'impalpabilité en cas de mort du perso ? <input type="text" name="etage_duree_imp_p" value="2" size="4"/><br>
            -- Delai d'impalpabilité en cas de mort du familier ? <input type="text" name="etage_duree_imp_f" value="8" size="4"/><br>
            <br>
            Comportement des montures sur cet étage ?<br>
            -- Cout (en PA) des ordres des montures sur cet étage ? <input type="text" name="etage_monture_pa_action" value="4" size="4"/><br>
            -- Incident d'ordre sur cet étage ? <?php echo $sel_monture_ordre_echec."&nbsp=>&nbsp".$sel_monture_ordre_inc; ?><br>
            -- Utilisation de l'ordre talonner ? <?php echo  $sel_monture_ordre_talonner." / ".$sel_monture_ordre_talonner_echec."&nbsp=>&nbsp".$sel_monture_ordre_talonner_inc." / difficulté:&nbsp;". $sel_monture_ordre_talonner_bm ." <i style='font-size:10px;'>(format dé rolliste)</i>"; ?><br>
            -- Utilisation de l'ordre sauter ? <?php echo  $sel_monture_ordre_sauter." / ".$sel_monture_ordre_sauter_echec."&nbsp=>&nbsp".$sel_monture_ordre_sauter_inc." / difficulté:&nbsp;". $sel_monture_ordre_sauter_bm ." <i style='font-size:10px;'>(format dé rolliste)</i>"; ?><br>
            <br />
            X min : <input type="text" name="x_min"> -
			X max : <input type="text" name="x_max"><br />
			Y min : <input type="text" name="y_min"> -
			Y max : <input type="text" name="y_max"><br /><br />
			Taux d’éboulement, de 0 (aucun) à 1000 (beaucoup) : <input type="text" name="etage_mine" value="50" /><br />
			<small><em>Un étage de dédié à la mine à un taux d’environ 300, un étage figé un taux de 5 voire 0</em></small><br />
			Type d’éboulements : <input type="text" name="etage_mine_type" value='999'><br />
			<small><em>Le code du type de mur à créer lors des éboulements (voir styles plus bas)</em></small><br />
			Richesse des éboulements, de 650 à 1000 : <input type="text" name="etage_mine_richesse" value='1000'><br />
			<small><em>La richesse détermine le type de pierre qu’on peut y trouver. En dessous de 960, on ne peut pas trouver de diamants, etc.</em></small><br /><br />
			Taux de retour des runes dans l’inventaire des monstres, de 0 à 100 : <input type="text" name="etage_retour_rune_monstre" value='50' /><br />
			<small><em>Hors échoppes, qui gardent leur part de runes. Un taux de 0 signifie que toutes les autres vont au sol. Un taux de 100, qu’elles vont toutes en inventaire.</em></small><br /><br />
			<input type="submit" class='test' value="Créer !">
		</form>
	</div>
	<hr />
	<p>Fonds définis pour ce style :</p>
	<div style='width:600px; overflow:auto' class='bordiv' id='visu_fonds'></div>
	<p>Murs définis pour ce style :</p>
	<div style='width:600px; overflow:auto' class='bordiv' id='visu_murs'></div>

<?php 	}

	if ($methode == 'début_modifier')
	{
		$req = "select etage_cod, etage_numero, etage_libelle, etage_reference, etage_description,
				etage_affichage, etage_arene, etage_quatrieme_perso, etage_quatrieme_mortel, etage_mort_speciale, etage_monture, etage_duree_imp_p, etage_duree_imp_f, etage_mort, etage_retour_rune_monstre,
				etage_mine, etage_mine_type, etage_mine_richesse,etage_familier_actif,
				coalesce(carene_level_max, 0) as carene_level_max, coalesce(carene_level_min, 0) as carene_level_min, coalesce(carene_ouverte, 'O') as carene_ouverte,
				(select count(*) from positions where pos_etage=93 and pos_entree_arene='O') as nb_entree_arene
			from etage
			left outer join carac_arene on carene_etage_numero = etage_numero
			where etage_numero = $pos_etage";
		$stmt = $pdo->query($req);
		$result = $stmt->fetch();
		$etage_numero = $result['etage_numero'];
		$etage_libelle = $result['etage_libelle'];
		$etage_description = $result['etage_description'];
		$etage_affichage = $result['etage_affichage'];
		$etage_arene = $result['etage_arene'];
		$etage_quatrieme_perso = $result['etage_quatrieme_perso'];
		$etage_quatrieme_mortel = $result['etage_quatrieme_mortel'];
		$etage_mort_speciale = $result['etage_mort_speciale'];
		$etage_monture = $result['etage_monture']=="" ? [   "pa_action"=>4,
                                                            "ordre_echec"=>0,
                                                            "ordre_incident"=>0,
                                                            "ordre_talonner"=>0,
                                                            "ordre_sauter"=>0,
                                                            "ordre_talonner_echec"=>0,
                                                            "ordre_sauter_echec"=>0,
                                                            "ordre_talonner_incident"=>0,
                                                            "ordre_sauter_incident"=>0,
                                                            "ordre_talonner_bm"=>0,
                                                            "ordre_sauter_bm"=>0
                                                        ] : (array)json_decode( $result['etage_monture'] );
        $etage_duree_imp_p = $result['etage_duree_imp_p'];
        $etage_duree_imp_f = $result['etage_duree_imp_f'];
		$etage_mort = $result['etage_mort'];
		$etage_reference = $result['etage_reference'];
		$etage_retour_rune_monstre = $result['etage_retour_rune_monstre'];
		$etage_mine = $result['etage_mine'];
		$etage_mine_type = $result['etage_mine_type'];
		$etage_mine_richesse = $result['etage_mine_richesse'];
		$carene_level_max = $result['carene_level_max'];
		$carene_level_min = $result['carene_level_min'];
		$carene_ouverte = $result['carene_ouverte'];
		$nb_entree_arene = $result['nb_entree_arene'];
		$etage_familier_actif = $result['etage_familier_actif'];

?>
	<p>Modifier l’étage <?php echo  $etage_libelle; ?></p>
	<div id="etage" class="tableau2">
		<form name="action_modifier_etage" method="post" action="modif_etage3.php">
			<input type="hidden" name="methode" value="modifier_etage" />
			<input type="hidden" name="etage_numero" value="<?php echo  $pos_etage; ?>" />
			Nom : <input type="text" name="nom" value="<?php echo  $etage_libelle; ?>" /><br>
			Description : <textarea name="description"><?php echo  $etage_description; ?></textarea><br>
			Quel style de cases pour l’étage ? <small><em>Voir l’aperçu dessous</em></small>
			<select name="etage_affichage" onChange="changeStyle(this.value)">
			<?php 				foreach ($tableau_styles as $unStyle)
					echo "<option value='$unStyle' " . (($unStyle == $etage_affichage) ? 'selected="selected"' : '') . ">$unStyle</option>";
			?>
			</select> <a href='modif_etage3_fonds.php' target='_blank'>Voir tous les styles</a><br /><br />

			Étage de référence  :
	<?php
        //echo "<select name=\"etage_reference\">";
		//echo $html->etage_select($etage_reference, ' where etage_numero='.$pos_etage.' or etage_reference = etage_numero');
        //echo "</select>";
		echo create_selectbox_from_req(
            "etage_reference",
            "select etage_numero, case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle  from etage where etage_numero=$pos_etage or etage_reference = etage_numero order by case when etage_reference>0 then -etage_reference else etage_reference end desc, etage_numero",
            $etage_reference);
	?>
			<br />
            Étage de référence en cas de mort :
    <?php
            //echo "<select name=\"etage_mort\"><option value=\"--\">Par défaut</option>";
            //echo $html->etage_select($etage_mort, ' where etage_reference = etage_numero');
            //echo " </select>";
            echo create_selectbox_from_req(
                "etage_mort",
                "select etage_numero, case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle  from etage where etage_reference = etage_numero order by case when etage_reference>0 then -etage_reference else etage_reference end desc, etage_numero",
                $etage_mort);
    ?>
           <br />
	<?php
		$sel_arene_O = ($etage_arene == 'O') ? 'selected="selected"' : '';
		$sel_arene_N = ($etage_arene == 'N') ? 'selected="selected"' : '';
		$sel_carene_O = ($carene_ouverte == 'O') ? 'selected="selected"' : '';
		$sel_carene_N = ($carene_ouverte == 'N') ? 'selected="selected"' : '';
		$sel_4_O = ($etage_quatrieme_perso == 'O') ? 'selected="selected"' : '';
		$sel_4_N = ($etage_quatrieme_perso == 'N') ? 'selected="selected"' : '';
		$sel_4M_O = ($etage_quatrieme_mortel == 'O') ? 'selected="selected"' : '';
		$sel_4M_N = ($etage_quatrieme_mortel == 'N') ? 'selected="selected"' : '';
		$sel_famactif_O = ($etage_familier_actif == '1') ? 'selected="selected"' : '';
        $sel_famactif_N = ($etage_familier_actif == '0') ? 'selected="selected"' : '';

        $sel_mort_spec0 = (1*$etage_mort_speciale == 0) ? 'selected="selected"' : '';
        $sel_mort_spec1 = (1*$etage_mort_speciale == 1) ? 'selected="selected"' : '';

        $sel_monture_ordre_echec = create_selectbox("etage_monture_ordre_echec", ["0"=>"Sans incident", "1"=>"Sur échec critique", "2"=>"Sur échec"], $etage_monture["ordre_echec"] ? $etage_monture["ordre_echec"] : 0);
        $sel_monture_ordre_inc = create_selectbox("etage_monture_ordre_inc", ["0"=>"Donner des ordres aléatoires", "1"=>"Désarçonner"], $etage_monture["ordre_incident"] ? $etage_monture["ordre_incident"] : 0);
        $sel_monture_ordre_talonner = create_selectbox("etage_monture_ordre_talonner", ["0"=>"Interdit", "1"=>"1x par DLT", "2"=>"2x par DLT", "0.5"=>"1x toutes les 2 DLT"], $etage_monture["ordre_talonner"] ? $etage_monture["ordre_talonner"] : 0);
        $sel_monture_ordre_sauter = create_selectbox("etage_monture_ordre_sauter", ["0"=>"Interdit", "1"=>"1x par DLT", "2"=>"2x par DLT", "0.5"=>"1x toutes les 2 DLT"], $etage_monture["ordre_sauter"] ? $etage_monture["ordre_sauter"] : 0);
        $sel_monture_ordre_talonner_echec = create_selectbox("etage_monture_ordre_talonner_echec", ["0"=>"Sans incident", "1"=>"Sur échec critique", "2"=>"Sur échec"], $etage_monture["ordre_talonner_echec"] ? $etage_monture["ordre_talonner_echec"] : 0);
        $sel_monture_ordre_sauter_echec = create_selectbox("etage_monture_ordre_sauter_echec", ["0"=>"Sans incident", "1"=>"Sur échec critique", "2"=>"Sur échec"], $etage_monture["ordre_sauter_echec"] ? $etage_monture["ordre_sauter_echec"] : 0);
        $sel_monture_ordre_talonner_inc = create_selectbox("etage_monture_ordre_talonner_inc", ["0"=>"Donner des ordres aléatoires", "1"=>"Désarçonner"], $etage_monture["ordre_talonner_incident"] ? $etage_monture["ordre_talonner_incident"] : 0);
        $sel_monture_ordre_sauter_inc = create_selectbox("etage_monture_ordre_sauter_inc", ["0"=>"Donner des ordres aléatoires", "1"=>"Désarçonner"], $etage_monture["ordre_sauter_incident"] ? $etage_monture["ordre_sauter_incident"] : 0);
        $sel_monture_ordre_talonner_bm = '<input name ="etage_monture_ordre_talonner_bm" value="'.($etage_monture["ordre_talonner_bm"] ? $etage_monture["ordre_talonner_bm"] : 0).'" type="text" size="4">';
        $sel_monture_ordre_sauter_bm = '<input name ="etage_monture_ordre_sauter_bm" value="'.($etage_monture["ordre_sauter_bm"] ? $etage_monture["ordre_sauter_bm"] : 0).'" type="text" size="4">';

        $arene_info = "" ;
        if ($carene_ouverte == 'O')
        {
            if ($nb_entree_arene>0)
            {
                $arene_info = "L'arène dispose de $nb_entree_arene entrée(s).<br />";
            }
            else
            {
                $arene_info = "<font color=\"#8b0000\"><strong><u>ATTENTION</u></strong>: L'arène ne dispose pas encore d'entrée, elle ne sera pas accessible par les batiments administratifs.</font><br />";
            }
        }

	?>
			L’étage est-il une arène ? <select name="etage_arene"><option value='N' <?php echo  $sel_arene_N; ?>>Non</option><option value='O' <?php echo  $sel_arene_O; ?>>Oui</option></select> -- Si Oui :
            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Niveau minimum pour y accéder ? <input type="text" name="carene_level_min" value="<?php echo  $carene_level_min; ?>" size="4"/> (0 = aucune limite)
            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Niveau maximal pour y accéder ? <input type="text" name="carene_level_max" value="<?php echo  $carene_level_max; ?>" size="4"/> (0 = aucune limite)
			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Arène accessible librement ? <select name="carene_ouverte"><option value='O' <?php echo  $sel_carene_O; ?>>Oui</option><option value='N' <?php echo  $sel_carene_N; ?>>Non</option></select> (Non = pour animation uniquement)
            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- Arène accessible aux familiers ? <select name="etage_familier_actif"><option value='0' <?php echo  $sel_4M_N; ?>>Non</option><option value='1' <?php echo  $sel_famactif_O; ?>>Oui</option></select> <em>(si NON il sera impalpable)</em>
			<br />
			<?php echo $arene_info; ?>
			L’étage est-il ouvert aux 4e persos limités en niveau ? <select name="etage_quatrieme_perso"><option value='N' <?php echo  $sel_4_N; ?>>Non</option><option value='O' <?php echo  $sel_4_O; ?>>Oui</option></select><br />
			L’étage est-il ouvert aux 4e persos mortels ? <select name="etage_quatrieme_mortel"><option value='N' <?php echo  $sel_famactif_N; ?>>Non</option><option value='O' <?php echo  $sel_4M_O; ?>>Oui</option></select><br /><br />
            La mort a cet étage a-t-elle un comportement spécial ? <select name="etage_mort_speciale"><option value='0' <?php echo  $sel_mort_spec0; ?>>Normal</option><option value='1' <?php echo  $sel_mort_spec1; ?>>Course de monture</option></select><br />
            -- Delai d'impalpabilité en cas de mort du perso ? <input type="text" name="etage_duree_imp_p" value="<?php echo  $etage_duree_imp_p; ?>" size="4"/><br>
            -- Delai d'impalpabilité en cas de mort du familier ? <input type="text" name="etage_duree_imp_f" value="<?php echo  $etage_duree_imp_f; ?>" size="4"/><br>
            <br>
            Comportement des montures sur cet étage ?<br>
            -- Cout (en PA) des ordres des montures sur cet étage ? <input type="text" name="etage_monture_pa_action" value="<?php echo  $etage_monture["pa_action"]; ?>" size="4"/><br>
            -- Incident d'ordre sur cet étage ? <?php echo $sel_monture_ordre_echec."&nbsp=>&nbsp".$sel_monture_ordre_inc; ?><br>
            -- Utilisation de l'ordre talonner ? <?php echo  $sel_monture_ordre_talonner." / ".$sel_monture_ordre_talonner_echec."&nbsp=>&nbsp".$sel_monture_ordre_talonner_inc." / difficulté:&nbsp;". $sel_monture_ordre_talonner_bm ." <i style='font-size:10px;'>(format dé rolliste)</i>"; ?><br>
            -- Utilisation de l'ordre sauter ? <?php echo  $sel_monture_ordre_sauter." / ".$sel_monture_ordre_sauter_echec."&nbsp=>&nbsp".$sel_monture_ordre_sauter_inc." / difficulté:&nbsp;". $sel_monture_ordre_sauter_bm ." <i style='font-size:10px;'>(format dé rolliste)</i>"; ?><br>
            <br />
			Taux d’éboulement, de 0 (aucun) à 1000 (beaucoup) : <input type="text" name="etage_mine" value="<?php echo  $etage_mine; ?>" /><br />
			<small><em>Un étage de dédié à la mine à un taux d’environ 300, un étage figé un taux de 5 voire 0</em></small><br />
			Type d’éboulements : <input type="text" name="etage_mine_type" value="<?php echo  $etage_mine_type; ?>" /><br />
			<small><em>Le code du type de mur à créer lors des éboulements (voir styles plus bas)</em></small><br />
			Richesse des éboulements, de 650 à 1000 : <input type="text" name="etage_mine_richesse" value="<?php echo  $etage_mine_richesse; ?>" /><br />
			<small><em>La richesse détermine le type de pierre qu’on peut y trouver. En dessous de 960, on ne peut pas trouver de diamants, etc.</em></small><br /><br />
			Taux de retour des runes dans l’inventaire des monstres, de 0 à 100 : <input type="text" name="etage_retour_rune_monstre" value="<?php echo  $etage_retour_rune_monstre; ?>" /><br />
			<small><em>Hors échoppes, qui gardent leur part de runes. Un taux de 0 signifie que toutes les autres vont au sol. Un taux de 100, qu’elles vont toutes en inventaire.</em></small><br /><br />
			<input type="submit" class='test' value="Modifier !">
		</form>
	</div>
	<hr />
	<p>Fonds définis pour ce style :</p>
	<div style='width:600px; overflow:auto' class='bordiv' id='visu_fonds'></div>
	<p>Murs définis pour ce style :</p>
	<div style='width:600px; overflow:auto' class='bordiv' id='visu_murs'></div>
	<script type='text/javascript'>changeStyle('<?php echo  $etage_affichage; ?>');</script>
<?php 	}
	echo "<center><a href=\"admin_etage.php\">Retour au menu d'aministration des étages<a></center>";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
