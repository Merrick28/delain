<?php 
include_once "verif_connexion.php";
//include G_CHE . "../includes/classes_monstre.php";

include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
ob_start();

$resultat = '';
if (isset($_GET['methode']))
{
	switch ($_GET['methode'])
	{
		case 'redemption':
			$req = "update perso set perso_monstre_attaque_monstre = 0
				where perso_cod IN (select ppos_perso_cod from perso_position where ppos_pos_cod = $position)";
			$db->query($req);
			echo '<div class="bordiv">Rédemption générale sur la case !</div>';
	}
}

$db = new base_delain;
$req = "select pos_x, pos_y, etage_libelle from positions inner join etage on etage_numero = pos_etage where pos_cod = $position";
$db->query($req);
$db->next_record();
$position_texte = $db->f('pos_x') . ' / ' . $db->f('pos_y') . ' / ' . $db->f('etage_libelle');

echo "<div class=\"titre\">Détail de la position $position_texte</div>";
$req_detail = "select perso_type_perso, perso_pnj, count(*) as nb ";
$req_detail = $req_detail . "from perso ";
$req_detail = $req_detail . "inner join perso_position on ppos_perso_cod = perso_cod ";
$req_detail = $req_detail . "where perso_actif = 'O' ";
$req_detail = $req_detail . "and ppos_pos_cod = $position ";
$req_detail = $req_detail . "group by perso_type_perso, perso_pnj ";
$db->query($req_detail);
$matrice_type = array(
	1 => array(0, 0, 0),
	2 => array(0, 0, 0),
	3 => array(0, 0, 0)
);
while ($db->next_record())
{
	$matrice_type[$db->f('perso_type_perso')][$db->f('perso_pnj')] = $db->f('nb');
}
echo "<table><tr><td class='soustitre2'>Sont<br />présents :</td><td>";
echo "<table><tr><th></th><th class='soustitre2'>Normal</th><th class='soustitre2'>PNJ</th><th class='soustitre2'>4e perso</th></tr>";
echo "<tr><td class='soustitre2'>Aventurier</td><td>" . $matrice_type[1][0] . "</td><td>" . $matrice_type[1][1] . "</td><td>" . $matrice_type[1][2] . "</td></tr>";
echo "<tr><td class='soustitre2'>Monstre</td><td>" . $matrice_type[2][0] . "</td><td>" . $matrice_type[2][1] . "</td><td>" . $matrice_type[2][2] . "</td></tr>";
echo "<tr><td class='soustitre2'>Familier</td><td>" . $matrice_type[3][0] . "</td><td>" . $matrice_type[3][1] . "</td><td>" . $matrice_type[3][2] . "</td></tr></table></td>";

$req_mvm = "select max(coalesce(perso_monstre_attaque_monstre, 0)) as nb ";
$req_mvm = $req_mvm . "from perso ";
$req_mvm = $req_mvm . "inner join perso_position on ppos_perso_cod = perso_cod ";
$req_mvm = $req_mvm . "where perso_actif = 'O' ";
$req_mvm = $req_mvm . "and ppos_pos_cod = $position ";
$db->query($req_mvm);
$db->next_record();
$mvm = $db->f('nb');
$texte_mvm = '';
if ($mvm < 2)
	$texte_mvm = "Nul ou négligeable ($mvm)";
elseif ($mvm == 2)
	$texte_mvm = "Rififi à prévoir ($mvm)";
else
	$texte_mvm = "Baston générale ! ($mvm)";
echo "<td style='padding-left:10px' class='soustitre2'>Marqueurs MvM :<br />(Indique si des monstres<br />se tapent entre eux)</td><td><b>$texte_mvm</b> - 
	<a href='?methode=redemption&position=$position'><b>Rédemption générale !</b></a></td></tr></table>";

echo "<hr /><div class=\"titre\">Monstres jouables en $position_texte</div>";
$req_monstre = "select dlt_passee(perso_cod) as dlt_passee, etat_perso(perso_cod) as etat, ";
$req_monstre = $req_monstre . "perso_cod, perso_nom, perso_pa, perso_pv, perso_pv_max, ";
$req_monstre = $req_monstre . "to_char(perso_dlt,'DD/MM/YYYY HH24:mi:ss') as dlt, pos_x, pos_y, pos_etage, ";
$req_monstre = $req_monstre . "(select count(dmsg_cod) from messages_dest where dmsg_perso_cod = perso_cod and dmsg_lu = 'N') as messages, ";
$req_monstre = $req_monstre . "perso_dirige_admin, perso_pnj, coalesce(compt_nom, '') as compt_nom ";
$req_monstre = $req_monstre . "from perso ";
$req_monstre = $req_monstre . "inner join perso_position on ppos_perso_cod = perso_cod ";
$req_monstre = $req_monstre . "inner join positions on pos_cod = ppos_pos_cod ";
$req_monstre = $req_monstre . "left outer join perso_compte on pcompt_perso_cod = perso_cod ";
$req_monstre = $req_monstre . "left outer join compte on compt_cod = pcompt_compt_cod ";
$req_monstre = $req_monstre . "where (perso_type_perso = 2 or perso_pnj = 1) and perso_actif = 'O' ";
$req_monstre = $req_monstre . "and pos_cod = $position ";
$req_monstre = $req_monstre . "order by perso_nom ";
$db->query($req_monstre);
$nb_monstre = $db->nf();
if ($nb_monstre == 0)
{
	echo("<p>Aucun monstre à cet endroit !</p>");
}
else
{
	echo("<table>");
	while($db->next_record())
	{
		if ($db->f("perso_dirige_admin") == 'O')
		{
			$ia = "<b>Hors IA</b>";
		}
		if ($db->f("perso_pnj") == 1)
		{
		    $ia = "<b>PNJ</b>";
		}
		else
		{
			$ia = "IA";
		}
		echo("<tr>");
		echo "<td class=\"soustitre2\"><p><a href=\"../validation_login_monstre.php?numero=" . $db->f("perso_cod") . "&compt_cod=" . $compt_cod . "\">" . $db->f("perso_nom") . "</a></td>";
		echo "<td class=\"soustitre2\"><p>" . $ia . "</td>";
		echo "<td class=\"soustitre2\"><p>" , $db->f("perso_pa") , "</td>";
		echo "<td class=\"soustitre2\"><p>" , $db->f("perso_pv") , " PV sur " , $db->f("perso_pv_max");
		if ($db->f("etat") != "indemne")
		{
			echo " - (<b>" , $db->f("etat") , "</b>)";
		}
		echo "</td>";
		echo "<td class=\"soustitre2\"><p>";
		if ($db->f("messages") != 0)
		{
			echo "<b>";
		}
		echo $db->f("messages") . " msg non lus.";
		if ($db->f("messages") != 0)
		{
			echo "</b>";
		}
		echo "</td>";
		echo "<td class=\"soustitre2\"><p>";
		if ($db->f("dlt_passee") == 1)
		{
			echo("<b>");
		}
		echo $db->f("dlt");
		if ($db->f("dlt_passee") == 1)
		{
			echo("</b>");
		}
		echo "</td>";
		echo "<td class=\"soustitre2\"><p>X=" , $db->f("pos_x") , ", Y=" , $db->f("pos_y") , ", E=" , $db->f("pos_etage") , "</td>";
		if ($db->f('compt_nom') != '')
		{
			echo "<td class=\"soustitre2\">Joué par <b>" , $db->f("compt_nom") , "</b></td>";
		}
		else
			echo "<td></td>";
		echo("</tr>");
	}

	echo("</table>");
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");

