<?php 
/* Création d’étages, modification des paramètres de case de l’étage */

include_once "verif_connexion.php";
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');


function ecrireResultatEtLoguer($texte, $loguer, $sql = '')
{
	global $db, $compt_cod;

	if ($texte)
	{
		$log_sql = false;	// Mettre à true pour le debug des requêtes

		if (!$log_sql || $sql == '')
			$sql = "\n";
		else
			$sql = "\n\t\tRequête : $sql\n";

		$req = "select compt_nom from compte where compt_cod = $compt_cod";
		$db->query($req);
		$db->next_record();
		$compt_nom = $db->f("compt_nom");

		$en_tete = date("d/m/y - H:i") . "\tCompte $compt_nom ($compt_cod)\t";
		echo "<div style='padding:10px;'>$texte<pre>$sql</pre></div><hr />";
		if ($loguer)
			writelog($en_tete . $texte . $sql,'lieux_etages');
	}
}

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$contenu = '';
$erreur = 0;
$req = "select dcompt_modif_carte from compt_droit where dcompt_compt_cod = $compt_cod ";
$db->query($req);


if ($db->nf() == 0)
{
    $droit['carte'] = 'N';
}
else
{
	$db->next_record();
	$droit['carte'] = $db->f("dcompt_modif_carte");
}
if ($droit['carte'] != 'O')
{
	die("<p>Erreur ! Vous n’avez pas accès à cette page !</p>");
}

if (!isset($methode))
{
	$methode = 'debut';
}
$log = '';
$resultat = '';

if ($erreur == 0)
{
	if (isset($pos_etage) && $pos_etage == '--')
		$methode = 'début_créer';
	if (isset($pos_etage) && $pos_etage != '--')
		$methode = 'début_modifier';

	// Traitements
	switch ($methode)
	{
		case "creer_etage":
			$erreur = 0;
			if(!$nom)
			{
				$resultat .= "<p><b>Le nom du nouvel étage n’est pas valide !</b></p>";
				$erreur = 1;
			}
			if(!$description)
			{
				$resultat .= "<p><b>La description du nouvel étage n’est pas valide !</b></p>";
				$erreur = 1;
			}
			if(!$x_min || !$x_max || $x_min >= $x_max)
			{
				$resultat .= "<p><b>Les limites de l’étage ne sont pas valides</b></p>";
				$erreur = 1;
			}
			if(!$y_min || !$y_max || $y_min >= $y_max)
			{
				$resultat .= "<p><b>Les limites de l’étage ne sont pas valides</b></p>";
				$erreur = 1;
			}
			if ($etage_arene == 'O' && $etage_ref == '--')
			{
				$resultat .= "<p><b>Un étage principal ne peut pas être une arène</b></p>";
				$erreur = 1;
			}
			$etage_numero = 999;
			if ($etage_ref == '--')	// Création d’un étage principal
			{
				$req = "select min(etage_numero) - 1 as numero from etage where etage_numero <> -100";
				$db->query($req);
				$db->next_record();
				$etage_numero = $db->f("numero");
			}
			if($erreur == 0)
			{
				$req = "select nextval('seq_etage_cod') as etage_cod";
				$db->query($req);
				$db->next_record();
				$etage_cod = $db->f("etage_cod");
				$etage_numero = ($etage_numero = 999) ? $etage_cod : $etage_numero;
				$nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
				$description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));
				$etage_ref = ($etage_ref == '--') ? $etage_numero : $etage_ref;
				$etage_mort = ($etage_mort == '--') ? $etage_ref : $etage_mort;
				$etage_affichage = ($etage_affichage == '--') ? 2 : $etage_affichage;
				$etage_retour_rune_monstre = ($etage_retour_rune_monstre == '') ? 50 : $etage_retour_rune_monstre;
				$req = "insert into etage (etage_cod, etage_numero, etage_libelle, etage_reference, etage_description,
						etage_affichage, etage_arene, etage_quatrieme_perso, etage_quatrieme_mortel, etage_mort, etage_retour_rune_monstre,
						etage_mine, etage_mine_type, etage_mine_richesse) "
					."values ($etage_cod, $etage_numero, e'$nom', $etage_ref, e'$description',
						'$etage_affichage', '$etage_arene', '$etage_quatrieme_perso', '$etage_quatrieme_mortel', $etage_mort, $etage_retour_rune_monstre,
						$etage_mine, $etage_mine_type, $etage_mine_richesse)";
				$db->query($req);

				if ($etage_arene == 'O')
				{
					$req = "insert into carac_arene (carene_etage_numero, carene_level_max, carene_ouverte)
						VALUES($etage_numero, $carene_level_max, '$carene_ouverte')";
					$db->query($req);
				}

				$req = "select cree_etage($etage_numero, $x_min, $x_max, $y_min, $y_max)";
				$db->query($req);
				$req ="create table perso_vue_pos_".$etage_cod." (  pvue_perso_cod INT not null, pvue_pos_cod INT not null )";
				$db->query($req);
				$req = "ALTER TABLE perso_vue_pos_".$etage_cod." ADD CONSTRAINT pk_perso_vue_pos_".$etage_cod." PRIMARY KEY (pvue_perso_cod, pvue_pos_cod)";
				$db->query($req);
				$req = "ALTER TABLE perso_vue_pos_".$etage_cod." ADD CONSTRAINT fk_pvue_perso_cod".$etage_cod." FOREIGN KEY (pvue_perso_cod) REFERENCES perso (perso_cod) MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;";
				$db->query($req);
				$req = "ALTER TABLE perso_vue_pos_".$etage_cod." OWNER TO delain;";
				$db->query($req);

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
			}
		break;

		case "modifier_etage":
			$erreur = 0;
			if(!$etage_numero)
			{
				$resultat .= "<p><b>Le numéro de l’étage n’est pas défini !</b></p>";
				$erreur = 1;
			}
			if(!$nom)
			{
				$resultat .= "<p><b>Le nom de l’étage n’est pas valide !</b></p>";
				$erreur = 1;
			}
			if(!$description)
			{
				$resultat .= "<p><b>La description de l’étage n’est pas valide !</b></p>";
				$erreur = 1;
			}
			if($erreur == 0)
			{
				$req = "select etage_reference from etage where etage_numero = $etage_numero";
				$db->query($req);
				$db->next_record();
				$etage_ref = $db->f("etage_reference");

				$req_avant = "select etage_libelle, etage_description, etage_affichage, etage_arene, etage_quatrieme_perso, etage_quatrieme_mortel,
						etage_mort, etage_retour_rune_monstre, etage_mine, etage_mine_type, etage_mine_richesse,
						coalesce(carene_level_max, 0) as carene_level_max, coalesce(carene_ouverte, 'O') as carene_ouverte
					from etage
					left outer join carac_arene on carene_etage_numero = etage_numero
					where etage_numero = $etage_numero";
				$db->query($req_avant);
				$db->next_record();
				$nom_avant = $db->f('etage_libelle');
				$desc_avant = $db->f('etage_description');
				$affichage_avant = $db->f('etage_affichage');
				$arene_avant = $db->f('etage_arene');
				$quatrieme_avant = $db->f('etage_quatrieme_perso');
				$quatrieme_mortel_avant = $db->f('etage_quatrieme_mortel');
				$mort_avant = $db->f('etage_mort');
				$rune_avant = $db->f('etage_retour_rune_monstre');
				$mine_avant = $db->f('etage_mine');
				$mine_type_avant = $db->f('etage_mine_type');
				$richesse_avant = $db->f('etage_mine_richesse');
				$carene_level_max_avant = $db->f('carene_level_max');
				$carene_ouverte_avant = $db->f('carene_ouverte');

				$nom = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $nom)));
				$description = pg_escape_string(str_replace("'", '’', str_replace("''", '’', $description)));
				$etage_mort = ($etage_mort == '--') ? $etage_ref : $etage_mort;
				$etage_affichage = ($etage_affichage == '--') ? 2 : $etage_affichage;
				$etage_retour_rune_monstre = ($etage_retour_rune_monstre == '') ? 50 : $etage_retour_rune_monstre;
				$req = "update etage set
						etage_libelle = e'$nom',
						etage_description = e'$description',
						etage_affichage = '$etage_affichage',
						etage_arene = '$etage_arene',
						etage_quatrieme_perso = '$etage_quatrieme_perso',
						etage_quatrieme_mortel = '$etage_quatrieme_mortel',
						etage_mort = $etage_mort,
						etage_retour_rune_monstre = $etage_retour_rune_monstre,
						etage_mine = $etage_mine,
						etage_mine_type = $etage_mine_type,
						etage_mine_richesse = $etage_mine_richesse
					where etage_numero = $etage_numero";
				$db->query($req);

				$req = "delete from carac_arene where carene_etage_numero = $etage_numero";
				$db->query($req);
				if ($etage_arene == 'O')
				{
					$req = "insert into carac_arene (carene_etage_numero, carene_level_max, carene_ouverte)
						VALUES($etage_numero, $carene_level_max, '$carene_ouverte')";
					$db->query($req);
				}

				$resultat .= "Étage $nom ($etage_numero) modifié !";
				$resultat .= ($nom == $nom_avant) ? '' : "\nNom passe de $nom_avant à $nom";
				$resultat .= ($description == $desc_avant) ? '' : "\nLa description est changée.";
				$resultat .= ($etage_affichage == $affichage_avant) ? '' : "\nStyle d’affichage passe de $affichage_avant à $etage_affichage";
				$resultat .= ($etage_arene == $arene_avant) ? '' : "\nArène passe de $arene_avant à $etage_arene";
				if ($etage_arene == 'O')
				{
					$resultat .= ($etage_arene == $arene_avant) ? '' : "\nArène - Niveau Max passe de $carene_level_max_avant à $carene_level_max";
					$resultat .= ($etage_arene == $arene_avant) ? '' : "\nArène - Ouverte passe de $carene_ouverte_avant à $carene_ouverte";
				}
				$resultat .= ($etage_quatrieme_perso == $quatrieme_avant) ? '' : "\nAutorisé aux 4èmes persos limités passe de $quatrieme_avant à $etage_quatrieme_perso";
				$resultat .= ($etage_quatrieme_mortel == $quatrieme_mortel_avant) ? '' : "\nAutorisé aux 4èmes persos mortels passe de $quatrieme_mortel_avant à $etage_quatrieme_mortel";
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
		ecrireResultatEtLoguer($resultat, $erreur == 0);
	}
?>
	Choix de l’étage à modifier
	<form method="post">
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
		<form name="action_creer_etage" method="post">
			<input type="hidden" name="methode" value="creer_etage">
			Nom : <input type="text" name="nom"><br>
			Description : <textarea name="description"></textarea><br>
			Rattaché à l’étage... <select name="etage_ref"><option value="--">Aucun (création d’un étage principal)</option>
	<?php 
		echo $html->etage_select('', ' where etage_reference = etage_numero');
	?>
			</select><br />
			Quel style de cases pour l’étage ? <small><i>Voir l’aperçu dessous</i></small>
			<select name="etage_affichage" onChange="changeStyle(this.value)">
				<option value='--'>Choisissez un style...</option>
			<?php 				foreach ($tableau_styles as $unStyle)
					echo "<option value='$unStyle'>$unStyle</option>";
			?>
			</select> <a href='modif_etage3_fonds.php' target='_blank'>Voir tous les styles</a><br /><br />
			Étage de référence en cas de mort : <select name="etage_mort"><option value="--">Par défaut</option>
	<?php 
		echo $html->etage_select('', ' where etage_reference = etage_numero');
	?>
			</select><br />
			L’étage est-il une arène ? <select name="etage_arene"><option value='N'>Non</option><option value='O'>Oui</option></select>
			-- Si oui : niveau maximal pour y accéder ? <input type="text" name="carene_level_max" value="0" size="4"/> (0 = aucune limite) -- Arène accessible librement ? <select name="carene_ouverte"><option value='O'>Oui</option><option value='N'>Non</option></select> (Non = pour animation uniquement)
			<br />
			L’étage est-il ouvert aux 4e persos limités en niveau ? <select name="etage_quatrieme_perso"><option value='N'>Non</option><option value='O'>Oui</option></select><br />
			L’étage est-il ouvert aux 4e persos mortels ? <select name="etage_quatrieme_mortel"><option value='N'>Non</option><option value='O'>Oui</option></select><br /><br />
			X min : <input type="text" name="x_min"> -
			X max : <input type="text" name="x_max"><br />
			Y min : <input type="text" name="y_min"> -
			Y max : <input type="text" name="y_max"><br /><br />
			Taux d’éboulement, de 0 (aucun) à 1000 (beaucoup) : <input type="text" name="etage_mine" value="50" /><br />
			<small><i>Un étage de dédié à la mine à un taux d’environ 300, un étage figé un taux de 5 voire 0</i></small><br />
			Type d’éboulements : <input type="text" name="etage_mine_type" value='999'><br />
			<small><i>Le code du type de mur à créer lors des éboulements (voir styles plus bas)</i></small><br />
			Richesse des éboulements, de 650 à 1000 : <input type="text" name="etage_mine_richesse" value='1000'><br />
			<small><i>La richesse détermine le type de pierre qu’on peut y trouver. En dessous de 960, on ne peut pas trouver de diamants, etc.</i></small><br /><br />
			Taux de retour des runes dans l’inventaire des monstres, de 0 à 100 : <input type="text" name="etage_retour_rune_monstre" value='50' /><br />
			<small><i>Hors échoppes, qui gardent leur part de runes. Un taux de 0 signifie que toutes les autres vont au sol. Un taux de 100, qu’elles vont toutes en inventaire.</i></small><br /><br />
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
				etage_affichage, etage_arene, etage_quatrieme_perso, etage_quatrieme_mortel, etage_mort, etage_retour_rune_monstre,
				etage_mine, etage_mine_type, etage_mine_richesse,
				coalesce(carene_level_max, 0) as carene_level_max, coalesce(carene_ouverte, 'O') as carene_ouverte,
				(select count(*) from positions where pos_etage=93 and pos_entree_arene='O') as nb_entree_arene
			from etage
			left outer join carac_arene on carene_etage_numero = etage_numero
			where etage_numero = $pos_etage";
		$db->query($req);
		$db->next_record();
		$etage_numero = $db->f('etage_numero');
		$etage_libelle = $db->f('etage_libelle');
		$etage_description = $db->f('etage_description');
		$etage_affichage = $db->f('etage_affichage');
		$etage_arene = $db->f('etage_arene');
		$etage_quatrieme_perso = $db->f('etage_quatrieme_perso');
		$etage_quatrieme_mortel = $db->f('etage_quatrieme_mortel');
		$etage_mort = $db->f('etage_mort');
		$etage_retour_rune_monstre = $db->f('etage_retour_rune_monstre');
		$etage_mine = $db->f('etage_mine');
		$etage_mine_type = $db->f('etage_mine_type');
		$etage_mine_richesse = $db->f('etage_mine_richesse');
		$carene_level_max = $db->f('carene_level_max');
		$carene_ouverte = $db->f('carene_ouverte');
		$nb_entree_arene = $db->f('nb_entree_arene');
?>
	<p>Modifier l’étage <?php echo  $etage_libelle; ?></p>
	<div id="etage" class="tableau2">
		<form name="action_modifier_etage" method="post">
			<input type="hidden" name="methode" value="modifier_etage" />
			<input type="hidden" name="etage_numero" value="<?php echo  $pos_etage; ?>" />
			Nom : <input type="text" name="nom" value="<?php echo  $etage_libelle; ?>" /><br>
			Description : <textarea name="description"><?php echo  $etage_description; ?></textarea><br>
			Quel style de cases pour l’étage ? <small><i>Voir l’aperçu dessous</i></small>
			<select name="etage_affichage" onChange="changeStyle(this.value)">
			<?php 				foreach ($tableau_styles as $unStyle)
					echo "<option value='$unStyle' " . (($unStyle == $etage_affichage) ? 'selected="selected"' : '') . ">$unStyle</option>";
			?>
			</select> <a href='modif_etage3_fonds.php' target='_blank'>Voir tous les styles</a><br /><br />
			Étage de référence en cas de mort : <select name="etage_mort"><option value="--">Par défaut</option>
	<?php 
		echo $html->etage_select($etage_mort, ' where etage_reference = etage_numero');
	?>
			</select><br />
	<?php 
		$sel_arene_O = ($etage_arene == 'O') ? 'selected="selected"' : '';
		$sel_arene_N = ($etage_arene == 'N') ? 'selected="selected"' : '';
		$sel_carene_O = ($carene_ouverte == 'O') ? 'selected="selected"' : '';
		$sel_carene_N = ($carene_ouverte == 'N') ? 'selected="selected"' : '';
		$sel_4_O = ($etage_quatrieme_perso == 'O') ? 'selected="selected"' : '';
		$sel_4_N = ($etage_quatrieme_perso == 'N') ? 'selected="selected"' : '';
		$sel_4M_O = ($etage_quatrieme_mortel == 'O') ? 'selected="selected"' : '';
		$sel_4M_N = ($etage_quatrieme_mortel == 'N') ? 'selected="selected"' : '';

        $arene_info = "" ;
        if ($carene_ouverte == 'O')
        {
            if ($nb_entree_arene>0)
            {
                $arene_info = "L'arène dispose de $nb_entree_arene entrée(s).<br />";
            }
            else
            {
                $arene_info = "<font color=\"#8b0000\"><b><u>ATTENTION</u></b>: L'arène ne dispose pas encore d'entrée, elle ne sera pas accessible par les batiments administratifs.</font><br />";
            }
        }

	?>
			L’étage est-il une arène ? <select name="etage_arene"><option value='N' <?php echo  $sel_arene_N; ?>>Non</option><option value='O' <?php echo  $sel_arene_O; ?>>Oui</option></select>
			-- Si oui : niveau maximal pour y accéder ? <input type="text" name="carene_level_max" value="<?php echo  $carene_level_max; ?>" size="4"/> (0 = aucune limite)
			-- Arène accessible librement ? <select name="carene_ouverte"><option value='O' <?php echo  $sel_carene_O; ?>>Oui</option><option value='N' <?php echo  $sel_carene_N; ?>>Non</option></select> (Non = pour animation uniquement)
			<br />
			<?php echo $arene_info; ?>
			L’étage est-il ouvert aux 4e persos limités en niveau ? <select name="etage_quatrieme_perso"><option value='N' <?php echo  $sel_4_N; ?>>Non</option><option value='O' <?php echo  $sel_4_O; ?>>Oui</option></select><br />
			L’étage est-il ouvert aux 4e persos mortels ? <select name="etage_quatrieme_mortel"><option value='N' <?php echo  $sel_4M_N; ?>>Non</option><option value='O' <?php echo  $sel_4M_O; ?>>Oui</option></select><br /><br />
			Taux d’éboulement, de 0 (aucun) à 1000 (beaucoup) : <input type="text" name="etage_mine" value="<?php echo  $etage_mine; ?>" /><br />
			<small><i>Un étage de dédié à la mine à un taux d’environ 300, un étage figé un taux de 5 voire 0</i></small><br />
			Type d’éboulements : <input type="text" name="etage_mine_type" value="<?php echo  $etage_mine_type; ?>" /><br />
			<small><i>Le code du type de mur à créer lors des éboulements (voir styles plus bas)</i></small><br />
			Richesse des éboulements, de 650 à 1000 : <input type="text" name="etage_mine_richesse" value="<?php echo  $etage_mine_richesse; ?>" /><br />
			<small><i>La richesse détermine le type de pierre qu’on peut y trouver. En dessous de 960, on ne peut pas trouver de diamants, etc.</i></small><br /><br />
			Taux de retour des runes dans l’inventaire des monstres, de 0 à 100 : <input type="text" name="etage_retour_rune_monstre" value="<?php echo  $etage_retour_rune_monstre; ?>" /><br />
			<small><i>Hors échoppes, qui gardent leur part de runes. Un taux de 0 signifie que toutes les autres vont au sol. Un taux de 100, qu’elles vont toutes en inventaire.</i></small><br /><br />
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
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
