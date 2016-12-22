<?php /* Affichage de tous les styles de murs et fonds */

include_once "verif_connexion.php";
include_once '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include_once('variables_menu.php');

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
	$erreur = 1;
}
if ($erreur == 0)
{
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
		function afficherStyles()
		{
			var div_images = document.getElementById('images');
			var chaine_contenu = '';

			for (var style in tab_fonds)
			{
				chaine_contenu += '<p><b>Style ' + style + '</b></p>\\n';
				chaine_contenu += '<p>Fonds :</p>';
				chaine_contenu += '\\n	<div style=\"width:600px; overflow:auto\" class=\"bordiv\">';
				
				for (var i in tab_fonds[style])
				{
					var nom = '" . G_IMAGES . "f_' + style + '_' + i + '.png';
					chaine_contenu += '\\n		<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img src=\"' + nom + '\"/><br />' + i + '</div>';
				}

				chaine_contenu += '</div><p>Murs :</p>';
				chaine_contenu += '\\n	<div style=\"width:600px; overflow:auto\" class=\"bordiv\">';
				for (var i in tab_murs[style])
				{
					var nom = '" . G_IMAGES . "t_' + style + '_mur_' + i + '.png';
					chaine_contenu += '\\n		<div style=\"float:left; width:28px; height:38px; margin:6px;\"><img src=\"' + nom + '\"/><br />' + i + '</div>';
				}

				chaine_contenu += '</div></div><hr />';
			}
			div_images.innerHTML = chaine_contenu;
		}
		</script>";
	echo '<div class="barrTitle">Visu de tous les styles définis</div><div id="images"></div>
	<script type="text/javascript">afficherStyles();</script>';
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
