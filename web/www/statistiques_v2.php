<?php 
include "classes.php";
include 'includes/template.inc';
$t = new template;
$t->set_file('FileRef','template/delain/index.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
//
// identification
//
ob_start();
include G_CHE . "ident.php";
$ident = montre_formulaire_connexion($verif_auth, ob_get_contents());
ob_end_clean();
$t->set_var("IDENT",$ident);

//
//Contenu de la div de droite
//
$contenu_page = '';
$contenu_page .= '<div class="titre">Statistiques</div>';
$req_nb_compte = "select count(compt_cod) as nb from compte where compt_actif != 'N'
    and compt_der_connex >= now() - '30 days'::INTERVAL
	and compt_monstre = 'N'
	and compt_quete = 'N'
	and compt_admin = 'N'
	and exists
	(select 1 from perso_compte,perso
	where pcompt_compt_cod = compt_cod
	and pcompt_perso_cod = perso_cod)";
$db->query($req_nb_compte);
$db->next_record();
$nb_compte = $db->f("nb");
$req_joueur = "select count(perso_cod) as nb from perso where perso_type_perso = 1 and perso_actif != 'N' and perso_pnj != 1 and perso_der_connex >= now() - '30 days'::INTERVAL";
$db->query($req_joueur);
$db->next_record();
$nb_joueur = $db->f("nb");
$moyenne = round($nb_joueur/$nb_compte,2);
$contenu_page .= ("Il y a aujourd'hui <b>$nb_joueur</b> personnages pour <b>$nb_compte</b> comptes (soit une moyenne de $moyenne personnages par joueur),");

$req_joueur = "select count(perso_cod) as nb from perso where perso_type_perso = 2 and perso_actif = 'O' ";
$db->query($req_joueur);
$db->next_record();
$nb_monstre = $db->f("nb");

$contenu_page .=(" et <b>$nb_monstre</b> monstres dans les souterrains qui n'attendent que vous !");

$contenu_page .= '<br /><i>Statistiques sur les 30 derniers jours seulement</i>';

$contenu_page .= '<div class="titre">Statistiques des personnages</div>';			
// classement par niveau
$req_niveau = "select perso_niveau,count(perso_cod) as nb from perso ";
$req_niveau = $req_niveau . "where perso_actif != 'N' and perso_type_perso = 1 and perso_pnj != 1 and perso_der_connex >= now() - '30 days'::INTERVAL";
$req_niveau = $req_niveau . "group by perso_niveau ";
$req_niveau = $req_niveau . "order by perso_niveau desc ";
$db->query($req_niveau);
$contenu_page .=("<table cellspacing=\"2\" cellpadding=\"2\">");
$contenu_page .=("<tr><td class=\"soustitre2\" colspan=\"2\"><p style=\"text-align:center;\">Répartition par niveau</td></tr>");
$contenu_page .=("<tr><td class=\"soustitre2\">Niveau :</td><td class=\"soustitre2\">Nombre de personnages :</td></tr>");
while ($db->next_record())
{
	$contenu_page .= "<tr><td class=\"soustitre2\">" . $db->f("perso_niveau") . "</td><td class=\"soustitre2\">" . $db->f("nb") . "</td></tr>";
}
$contenu_page .=("</table>");
$contenu_page .=("<hr />");

	// classement par joueur et par sexe
	$req = "select race_nom,(select count(perso_cod) from perso where perso_actif != 'N' and perso_type_perso = 1 and perso_race_cod = race_cod and perso_sex = 'M' and perso_der_connex >= now() - '30 days'::INTERVAL) as m, ";
	$req = $req . "(select count(perso_cod) from perso where perso_actif != 'N' and perso_type_perso = 1 and perso_race_cod = race_cod and perso_sex = 'F' and perso_der_connex >= now() - '30 days'::INTERVAL) as f ";
	$req = $req . "from race where race_cod in (1,2,3,33) ";
	$db->query($req);
	$contenu_page .=("<table cellspacing=\"2\" cellpadding=\"2\">");
	$contenu_page .=("<tr><td class=\"soustitre2\" colspan=\"3\"><p style=\"text-align:center;\">Répartition par race et par sexe :</td></tr>");
	$contenu_page .=("<tr><td></td><td class=\"soustitre2\">M</td><td class=\"soustitre2\">F</td></tr>");
	while ($db->next_record())
	{
		$contenu_page .= "<tr><td class=\"soustitre2\">" . $db->f("race_nom") . "</td><td class=\"soustitre2\">" . $db->f("m") . "</td><td class=\"soustitre2\">" . $db->f("f") . "</td></tr>";
			}
	$contenu_page .=("</table>");
	$contenu_page .=("<hr />");

			// classement par étage
			$contenu_page .=("<table cellspacing=\"2\" cellpadding=\"2\">");
			$contenu_page .=("<tr><td class=\"soustitre2\" colspan=\"5\"><p style=\"text-align:center;\">Répartition par étage : <br><i>Seuls les étages connus sont visibles. De nombreux antres existent et restent à la découverte des joueurs/personnages</i></td></tr>");
			$contenu_page .=("<tr><td class=\"soustitre2\">Etage</td>
			<td class=\"soustitre2\">Personnages</td>
			<td class=\"soustitre2\">Niveau moyen</td>
			<td class=\"soustitre2\">Monstres</td>
			<td class=\"soustitre2\">Familiers</td></tr>");
			$req = "select etage_libelle, ";
			$req = $req . "(select count(perso_cod) from perso,perso_position,positions ";
			$req = $req . "where pos_etage = etage_numero ";
			$req = $req . "and ppos_pos_cod = pos_cod ";
			$req = $req . "and ppos_perso_cod = perso_cod ";
			$req = $req . "and perso_type_perso = 1 and perso_der_connex >= now() - '30 days'::INTERVAL ";
			$req = $req . "and perso_actif != 'N' and perso_pnj != 1) as joueur, ";
			$req = $req . "(select sum(perso_niveau) from perso,perso_position,positions ";
			$req = $req . "where pos_etage = etage_numero ";
			$req = $req . "and ppos_pos_cod = pos_cod ";
			$req = $req . "and ppos_perso_cod = perso_cod ";
			$req = $req . "and perso_type_perso = 1 and perso_der_connex >= now() - '30 days'::INTERVAL ";
			$req = $req . "and perso_actif != 'N' and perso_pnj != 1) as jnv, ";
			$req = $req . "(select count(perso_cod) from perso,perso_position,positions ";
			$req = $req . "where pos_etage = etage_numero ";
			$req = $req . "and ppos_pos_cod = pos_cod ";
			$req = $req . "and ppos_perso_cod = perso_cod ";
			$req = $req . "and perso_type_perso = 2 ";
			$req = $req . "and perso_actif != 'N' and perso_pnj != 1) as monstre, ";
            $req = $req . "(select count(perso_cod) from perso,perso_position,positions ";
            $req = $req . "where pos_etage = etage_numero ";
            $req = $req . "and ppos_pos_cod = pos_cod ";
            $req = $req . "and ppos_perso_cod = perso_cod ";
            $req = $req . "and perso_type_perso = 3 ";
            $req = $req . "and perso_actif != 'N' and perso_pnj != 1) as familier ";
			$req = $req . "from etage ";
			$req = $req . "where etage_numero <= 0 ";
			$req = $req . "and etage_numero != -100 "; // Proving Ground
			$req = $req . "order by etage_numero desc ";
			$db->query($req);

			while ($db->next_record())
			{
				$contenu_page .= "<tr><td class=\"soustitre2\">" . $db->f("etage_libelle") . "</td>
				<td>" . $db->f("joueur") . "</td>
				<td>" . ($db->f("joueur") != 0 ?
				            round($db->f("jnv") / $db->f("joueur") , 0) :
				            0) . "</td>
				<td>" . $db->f("monstre") . "</td>
				<td>" . $db->f("familier") . "</td></tr>";
			}





			$contenu_page .=("</table>");
$contenu_page .= "<p style=\"text-align:center;\"><a href=\"rech_class.php\">Faire une recherche !</a>";
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');

