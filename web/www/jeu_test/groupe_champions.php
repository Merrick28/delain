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
// Définition de la fonction qui gère l’affichage
//
function gereAffichage($db, $champValeur, $unite, $champTitre, $champComp, $champNom)
{
	$resultat = '';

	$type = '';
	$comp = '';
	$valeur = 0;
	$nombre_affiches = 0;
	$cpt_comp = 0;
	$afermer = false;
	$nombre_podium = 3;
	while($db->next_record())
	{
		if ($type != $db->f($champTitre))
		{
			$cpt_comp = 0;
			$type = $db->f($champTitre);
			$resultat .= '<tr><td colspan="4" class="titre">' . $type . '</td></tr>';
			$afermer = false;
		}
		if ($comp != $db->f($champComp))
		{
			$valeur = 0;
			$nombre_affiches = 0;
			if ($afermer)
			{
				$resultat .= '</td>';
				if ($cpt_comp % 2 == 0)
				{
					$resultat .= '</tr>';
				}
				$afermer = false;
			}
			$comp = $db->f($champComp);
			if ($cpt_comp % 2 == 0)
			{
				$resultat .= '<tr>';
			}
			$resultat .= '<td nowrap class="soustitre2"><b>' . $comp . '</b></td>';
			$resultat .= '<td><b>' . $db->f($champValeur) . $unite . '</b> ' . $db->f($champNom) . '<br />';
			$afermer = true;
			$cpt_comp++;
			$nombre_affiches++;
			$valeur = $db->f($champValeur);
		}
		else if ($nombre_affiches < $nombre_podium || $valeur == $db->f($champValeur))
		{
			$resultat .= '<b>' . $db->f($champValeur) . $unite . '</b> ' . $db->f($champNom) . '<br />';
			$nombre_affiches++;
			$valeur = $db->f($champValeur);
		}
	}
	if ($afermer)
	{
		$resultat .= '</td>';
		if ($cpt_comp % 2 == 0)
		{
			$resultat .= '</tr>';
		}
		else
		{
			$resultat .= '<td></td><td></td></tr>';
		}
	}
	return $resultat;
}
//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_statique = '';

$req = 'select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod = ' . $perso_cod . ' and pgroupe_statut > 0 AND pgroupe_champions = 1';
$db->query($req);
if($db->nf() == 0)
    $contenu_page .= 'Vous n’appartenez à aucune coterie, ou ne participez pas aux champions de votre coterie.';
else
{
	define("APPEL", 1);
	$db->next_record();
	$groupe_cod = $db->f('pgroupe_groupe_cod');
	$nom_fichier = "./statiques/groupe_champions_$groupe_cod.php";

	if (!is_dir('statiques'))
		mkdir('statiques', 0777);
	
	if (!is_file($nom_fichier))
	{
		$verification = '<?php
			if(!defined("APPEL"))
				die("Erreur d’appel de page !");
			?>
			';
		$contenu_statique .= '<p class="titre">Les champions de la coterie !</p>';
		$contenu_statique .= '<p>Page générée ' . date('\l\e d/m/Y \à H:i') . '</p>';
		$contenu_statique .= '<center><table>';

		$req_comp = 'select typc_libelle, comp_libelle, pcomp_modificateur, perso_nom
			from perso_competences
			inner join competences on comp_cod = pcomp_pcomp_cod
			inner join type_competences on typc_cod = comp_typc_cod
			inner join perso on perso_cod = pcomp_perso_cod
			inner join groupe_perso on pgroupe_perso_cod = pcomp_perso_cod
			where pgroupe_groupe_cod = ' . $groupe_cod . '
				and pgroupe_champions = 1
				and pgroupe_statut > 0
				and comp_cod != 29
			order by typc_libelle, pcomp_pcomp_cod, pcomp_modificateur desc, perso_nom';

		$db->query($req_comp);
		$contenu_statique .= gereAffichage($db, 'pcomp_modificateur', '%', 'typc_libelle', 'comp_libelle', 'perso_nom');
		
		$req_caracs = "WITH t as (
				select perso_for, perso_int, perso_con, perso_dex, perso_nom, perso_po, coalesce(pbank_or, 0) as banque, perso_pv_max from perso
				inner join groupe_perso on pgroupe_perso_cod = perso_cod
				left outer join perso_banque on pbank_perso_cod = perso_cod
				where pgroupe_champions = 1
					and pgroupe_statut > 0
					and pgroupe_groupe_cod = $groupe_cod
			)
			select 'Caractéristiques' as titre, 'Force' as carac, perso_for as valeur, perso_nom as nom
			from
				( select * from t
				order by perso_for desc
				) a
			UNION ALL
			select 'Caractéristiques' as titre, 'Intelligence' as carac, perso_int as valeur, perso_nom as nom
			from
				( select * from t
				order by perso_int desc
				) a
			UNION ALL
			select 'Caractéristiques' as titre, 'Dextérité' as carac, perso_dex as valeur, perso_nom as nom
			from
				( select * from t
				order by perso_dex desc
				) a
			UNION ALL
			select 'Caractéristiques' as titre, 'Constitution' as carac, perso_con as valeur, perso_nom as nom
			from
				( select * from t
				order by perso_con desc
				) a
			UNION ALL
			select 'Caractéristiques' as titre, 'PV' as carac, perso_pv_max as valeur, perso_nom as nom
			from
				( select * from t
				order by perso_pv_max desc
				) a";

		$db->query($req_caracs);
		$contenu_statique .= gereAffichage($db, 'valeur', '', 'titre', 'carac', 'nom');
		
		$req_caracs = "WITH t as (
				select perso_nom, perso_po, coalesce(pbank_or, 0) as banque from perso
				inner join groupe_perso on pgroupe_perso_cod = perso_cod
				left outer join perso_banque on pbank_perso_cod = perso_cod
				where pgroupe_champions = 1
					and pgroupe_statut > 0
					and pgroupe_groupe_cod = $groupe_cod
			)
			select 'Richesses' as titre, 'Porte-monnaie' as carac, perso_po as valeur, perso_nom as nom
			from
				( select * from t
				order by perso_po desc
				) a
			UNION ALL
			select 'Richesses' as titre, 'Banque' as carac, banque as valeur, perso_nom as nom
			from
				( select * from t
				order by banque desc
				) a";

		$db->query($req_caracs);
		$contenu_statique .= gereAffichage($db, 'valeur', ' bzf', 'titre', 'carac', 'nom');
		
		$req_chasse = 'select \'Palmarès de chasse\' as libelle, race_nom, sum(ptab_total * gmon_niveau * gmon_niveau) as total, perso_nom
			from perso_tableau_chasse
			inner join monstre_generique on gmon_cod = ptab_gmon_cod
			inner join race on race_cod = gmon_race_cod
			inner join perso on perso_cod = ptab_perso_cod
			inner join groupe_perso on pgroupe_perso_cod = perso_cod
			where pgroupe_groupe_cod = ' . $groupe_cod . '
				and pgroupe_champions = 1
				and pgroupe_statut > 0
			group by race_nom, perso_nom
			order by race_nom, total desc, perso_nom';

		$db->query($req_chasse);
		$contenu_statique .= gereAffichage($db, 'total', ' points', 'libelle', 'race_nom', 'perso_nom');
		
		$req_chasse = 'select \'Palmarès Solo (classé par niveau du monstre)\' as libelle, gmon_nom, sum(ptab_solo) as total, perso_nom
			from perso_tableau_chasse
			inner join monstre_generique on gmon_cod = ptab_gmon_cod
			inner join perso on perso_cod = ptab_perso_cod
			inner join groupe_perso on pgroupe_perso_cod = perso_cod
			where pgroupe_groupe_cod = ' . $groupe_cod . '
				and pgroupe_champions = 1
				and pgroupe_statut > 0
				and ptab_solo = 1
			group by gmon_niveau, gmon_nom, perso_nom
			order by gmon_niveau desc, gmon_nom, total desc, perso_nom';

		$db->query($req_chasse);
		$contenu_statique .= gereAffichage($db, 'total', '', 'libelle', 'gmon_nom', 'perso_nom');

		$contenu_statique .= '</table>';


		file_put_contents($nom_fichier, $verification . $contenu_statique);
		$test_mod = chmod($nom_fichier, 0777);
		$req_date = "update groupe set groupe_champion_gen_date = now() where groupe_cod = $groupe_cod";
		$db->query($req_date);
	}
	else
	{
		ob_start();
		include ($nom_fichier);
		$contenu_statique .= ob_get_contents();
		ob_end_clean();
	}
	$contenu_page .= $contenu_statique;
}
$contenu_page .= '<hr /><p style="text-align:center;"><a href="groupe.php">Retour à la gestion de la coterie</a></p>';
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
