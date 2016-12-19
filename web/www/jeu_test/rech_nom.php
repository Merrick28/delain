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
	$methode = "entree";
}
switch($methode)
{
	case "entree":
		echo "<form name=\"rech\" method=\"post\" action=\"rech_nom.php\">";
		echo "<input type=\"hidden\" name=\"methode\" value=\"valide\">";
		echo "<p>Entrez le nom du compte ou d'un de ses persos à rechercher : <input type=\"text\" name=\"nom\">";
		echo "<p><center><input type=\"submit\" class=\"test\" value=\"Rechercher !\">";
		echo "</form>";
		break;
	case "valide":
		$req = "select compt_cod, compt_nom, compt_mail, to_char(compt_dcreat,'DD/MM/YYYY hh24:mi:ss') as creation,
			to_char(compt_der_connex,'DD/MM/YYYY hh24:mi:ss') as connex,
			compt_ip, compt_commentaire, compt_actif from compte ";
		$req = $req . "where lower(compt_nom) like '%'||lower('" . str_replace("'", "''", $nom) . "')||'%' order by compt_nom ";
		$db->query($req);
		if ($db->nf() > 50)
		{
			echo "<p>Plus de 50 réponses, merci d'affiner votre recherche.";
			echo "<br><a href=\"rech_nom.php\">Retour</a>";
		}
		else
		{
		echo "<table>";
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p><b>Numéro</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Nom</b> (cliquez sur le nom pour détails)</td>";
		echo "<td class=\"soustitre2\"><p><b>Actif</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Mail</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Date création</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Dernière connexion</b></td>";
		echo "<td class=\"soustitre2\"><p><b>IP</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Commentaire</b></td>";
		echo "</tr>";
		while ($db->next_record())
		{
			echo "<tr>";	
			echo "<td><p>" . $db->f("compt_cod") . "</td>";
			echo "<td class=\"soustitre2\"><p><b><a href=\"detail_compte.php?vcompte=" . $db->f("compt_cod") . "\">" . $db->f("compt_nom") . "</a></b></td>";
			echo "<td><p>" . $db->f("compt_actif") . "</td>";
			echo "<td><p>" . $db->f("compt_mail") . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $db->f("creation") . "</td>";
			echo "<td><p>" . $db->f("connex") . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $db->f("compt_ip") . "</td>";
			echo "<td><p>" . $db->f("compt_commentaire") . "</td>";
			echo "</tr>";
		}
		}
		$req = "select compt_cod, compt_nom, compt_mail, to_char(compt_dcreat,'DD/MM/YYYY hh24:mi:ss') as creation,
			to_char(compt_der_connex,'DD/MM/YYYY hh24:mi:ss') as connex,
			compt_ip, compt_commentaire, compt_actif from compte, perso_compte, perso ";
		$req = $req . "where lower(perso_nom) like '%'||lower('" . str_replace("'", "''", $nom) . "')||'%' and compt_cod = pcompt_compt_cod and pcompt_perso_cod = perso_cod order by compt_nom ";
		$db->query($req);
		if ($db->nf() > 50)
		{
			echo "<p>Plus de 50 réponses, merci d'affiner votre recherche.";
			echo "<br><a href=\"rech_nom.php\">Retour</a>";
		}
		else
		{
		while ($db->next_record())
		{
			echo "<tr>";	
			echo "<td><p>" . $db->f("compt_cod") . "</td>";
			echo "<td class=\"soustitre2\"><p><b><a href=\"detail_compte.php?vcompte=" . $db->f("compt_cod") . "\">" . $db->f("compt_nom") . "</a></b></td>";
			echo "<td><p>" . $db->f("compt_actif") . "</td>";
			echo "<td><p>" . $db->f("compt_mail") . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $db->f("creation") . "</td>";
			echo "<td><p>" . $db->f("connex") . "</td>";
			echo "<td class=\"soustitre2\"><p>" . $db->f("compt_ip") . "</td>";
			echo "<td><p>" . $db->f("compt_commentaire") . "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
		}
		break;	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
