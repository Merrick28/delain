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
$chemin = "http://www.jdr-delain.net/avatars/";
$req_visu = "select perso_cod,perso_nom,race_nom,perso_sex,perso_description,perso_nb_mort,perso_nb_joueur_tue,perso_nb_monstre_tue,get_karma(perso_kharma) as karma,get_renommee(perso_renommee) as renommee,race_cod,perso_description,perso_avatar ";
$req_visu = $req_visu . "from perso,race ";
$req_visu = $req_visu . "where perso_cod = $perso_cod ";
$req_visu = $req_visu . "and perso_race_cod = race_cod ";
$db->query($req_visu);
$db->next_record();
echo("<center><table>");

if ($db->f("perso_avatar") == '')
{
	$avatar = "../images/" . $db->f("race_cod") . "_" . $db->f("perso_sex") . ".gif";
}
else
{
	$avatar = $chemin . $db->f("perso_avatar");
}


echo("<tr>");
printf("<td colspan=\"3\" class=\"titre\"><p class=\"titre\">Fiche de %s</p></td>",$db->f("perso_nom"));
echo("</tr>");

if ($db->f("perso_description") != '')
{
	echo("<tr>");
	printf("<td colspan=\"3\" class=\"soustitre2\"><p>%s</td></tr>",str_replace(chr(127),";",$db->f("perso_description")));
}

echo("<tr>");
echo("<td rowspan=\"9\"><img src=\"$avatar\"></td>");
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Race :</td>");
printf("<td><p>%s</td>",$db->f("race_nom"));
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Sexe :</td>");
printf("<td><p>%s</td>",$db->f("perso_sex"));
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Karma :</td>");
printf("<td><p>%s</td>",$db->f("karma"));
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Renommée :</td>");
printf("<td><p>%s</td>",$db->f("renommee"));
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Nombre de décès :</td>");
printf("<td><p>%s</td>",$db->f("perso_nb_mort"));
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Nombre de joueurs tués :</td>");
printf("<td><p>%s</td>",$db->f("perso_nb_joueur_tue"));
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Nombre de monstres tués :</td>");
printf("<td><p>%s</td>",$db->f("perso_nb_monstre_tue"));
echo("</tr>");

$db_guilde = new base_delain;
$req_guilde = "select guilde_nom,rguilde_libelle_rang from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod and pguilde_valide = 'O' and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod and rguilde_rang_cod = pguilde_rang_cod ";
$db_guilde->query($req_guilde);
$nb_guilde = $db_guilde->nf();

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Guilde :</td>");
echo("<td>");
if ($nb_guilde == 0)
{
	echo("<p>Pas de guilde");
}
else
{
	$db_guilde->next_record();
	printf("<p>%s (%s)",$db_guilde->f("guilde_nom"),$db_guilde->f("rguilde_libelle_rang"));
}
echo("</td>");
echo("</tr>");

echo("</table></center>");
$req = "select ptitre_titre,to_char(ptitre_date,'DD/MM/YYYY') as titre_date from perso_titre ";
$req = $req . "where ptitre_perso_cod = $perso_cod ";
$req = $req . "order by ptitre_cod desc ";
$db->query($req);
if ($db->nf() != 0)
{
	echo "<hr><center><table>";
	echo "<tr><td colspan=\"2\" class=\"titre\">Titres obtenus</td></tr>";
	echo "<tr><td class=\"soustitre2\">Titre</td><td class=\"soustitre2\">Obtenu le</td></tr>";
	while($db->next_record())
	{
		echo "<tr><td><strong>" , $db->f("ptitre_titre") , "</strong></td><td>" , $db->f("titre_date") , "</td></tr>";	
	}
	echo "</table></center><hr>";
}
echo("<p><a href=\"change_desc_perso.php\">Changer la description du personnage</a>");
echo("<p><a href=\"change_avatar_perso.php\">Changer l'avatar du personnage</a>");
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');