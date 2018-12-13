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
$erreur = 0;
// on cherche la guilde dans laquelle est le joueur
$req_guilde = "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod,rguilde_admin,pguilde_message from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_rang_cod = pguilde_rang_cod ";
$req_guilde = $req_guilde . "and pguilde_valide = 'O' ";
$db->query($req_guilde);
if ($db->nf() == 0)
{
	echo "<p>Erreur ! Vous n'êtes affilié à aucune guilde !";
	$erreur = 1;
}
$db->next_record();
$num_guilde = $db->f("guilde_cod");
// on regarde les détails de la révolution
if (!$db->is_revolution($num_guilde))
{
	echo "<p>Aucune révolution en cours pour votre guilde.";
	$erreur = 1;
}
$req_lanceur = "select * from v_revguilde where guilde = $num_guilde ";
$db->query($req_lanceur);
?>
<form name="revolution" method="post" action="vote_revguilde.php">
<input type="hidden" name="revguilde_cod">
<input type="hidden" name="visu">
<table>
<tr>
<td class="soustitre2"><p><strong>Lanceur</strong></td>
<td class="soustitre2"><p><strong>Cible</strong></td>
<td class="soustitre2"><p><strong>Votes pour le lanceur</strong></td>
<td class="soustitre2"><p><strong>Votes contre le lanceur</strong></td>
<td class="soustitre2"><p><strong>Date de fin</strong></td>
<td></td>
</tr>
<?php 
while ($db->next_record())
{
	$pour_oui = round((($db->f("oui")/$db->f("nb_membres")) * 100),2);
	$pour_non = round((($db->f("non")/$db->f("nb_membres")) * 100),2);
	echo "<tr>";
	echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.revolution.action='visu_desc_perso.php';document.revolution.visu.value=" . $db->f("code_lanceur") . ";document.revolution.submit()\">" . $db->f("nom_lanceur") . "</A></td>";	
	echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.revolution.action='visu_desc_perso.php';document.revolution.visu.value=" . $db->f("code_cible") . ";document.revolution.submit()\">" . $db->f("nom_cible") . "</a></td>";	
	echo "<td><p>" . $db->f("oui") . " (" . $pour_oui . "%)</td>";	
	echo "<td><p>" . $db->f("non") . " (" . $pour_non . "%)</td>";
	echo "<td><p>" . $db->f("date_fin") . "</td>";	
	echo "<td>";
	// on regarde si la personne peut voter
	$req2 = "select vrevguilde_cod from guilde_revolution_vote ";
	$req2 = $req2 . "where vrevguilde_revguilde_cod = " . $db->f("code_rev") . " ";
	$req2 = $req2 . "and vrevguilde_perso_cod = $perso_cod ";
	$db2->query($req2);
	if ($db2->nf() == 0)
	{
		echo "<p><a href=\"javascript:document.revolution.revguilde_cod.value="  . $db->f("code_rev") . ";document.revolution.submit()\">Voter !</a>";
	}
	echo "</td>";
}
?>
</table>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
