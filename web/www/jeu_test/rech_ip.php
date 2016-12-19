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
		echo "<form name=\"rech\" method=\"post\" action=\"rech_ip.php\">";
		echo "<input type=\"hidden\" name=\"methode\" value=\"valide\">";
		echo "<p>Entrez l'IP à rechercher : <input type=\"text\" name=\"ip\">";
		echo "<p><center><input type=\"submit\" class=\"test\" value=\"Rechercher !\">";
		echo "</form>";
		break;
	case "valide":
		$req = "select distinct compt_cod,compt_nom,compt_mail,to_char(compt_dcreat,'DD/MM/YYYY hh24:mi:ss') as creation,to_char(compt_der_connex,'DD/MM/YYYY hh24:mi:ss') as connex,compt_ip,compt_commentaire, to_char(ip.timestamp,'DD/MM/YYYY hh24:mi:ss') as timestamp from compte
		inner join (
		  select icompt_compt_cod, max (icompt_compt_date) as timestamp from compte_ip
		  where icompt_compt_ip = '$ip'
		  group by icompt_compt_cod
		) ip ON ip.icompt_compt_cod = compte.compt_cod
		 where compt_actif = 'O' order by compt_nom";

		$db->query($req);
		echo "<p>Recherche sur l'adresse IP $ip";
		echo "<table>";
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p><b>Numéro</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Nom</b> (cliquez sur le nom pour détails)</td>";
		echo "<td class=\"soustitre2\"><p><b>Mail</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Date création</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Dernière connexion</b></td>";
		echo "<td class=\"soustitre2\"><p><b>IP</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Dernière utilisation<br />de l'IP recherchée</b></td>";
		echo "<td class=\"soustitre2\"><p><b>Commentaire</b></td>";
		echo "</tr>";
		while ($db->next_record())
		{
			echo "<tr>";	
			echo "<td>" . $db->f("compt_cod") . "</td>";
			echo "<td class=\"soustitre2\"><b><a href=\"detail_compte.php?compte=" . $db->f("compt_cod") . "\">" . $db->f("compt_nom") . "</a></b></td>";
			echo "<td>" . $db->f("compt_mail") . "</td>";
			echo "<td class=\"soustitre2\">" . $db->f("creation") . "</td>";
			echo "<td>" . $db->f("connex") . "</td>";
			echo "<td class=\"soustitre2\">" . $db->f("compt_ip") . "</td>";
			echo "<td>" . $db->f("timestamp") . "</td>";
			echo "<td class=\"soustitre2\">" . $db->f("compt_commentaire") . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		break;	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
