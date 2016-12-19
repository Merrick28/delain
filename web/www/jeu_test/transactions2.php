<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
include "../includes/fonctions.php";
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
ob_start();
?>
<script type="text/javascript" src="../scripts/cocheCase.js"></script>
<p class="titre">Brouzoufs</p>
<a href="don_br.php">Donner des brouzoufs ?</a>



<?php 
/******************/
/* Partie vendeur */
/******************/
$req_tran_vendeur = "select tran_cod,tran_obj_cod,tran_acheteur,tran_nb_tours,tran_prix,obj_nom,obj_nom_generique,tran_identifie,perso_nom
														from transaction,perso,objet_generique,objets
														where tran_vendeur = $perso_cod
														and tran_acheteur = perso_cod
														and tran_obj_cod = obj_cod
														and obj_gobj_cod = gobj_cod ";
$db->query($req_tran_vendeur);
$nb_tran_vendeur = $db->nf();
echo("<table width=\"100%\"><tr><td class=\"titre\" colspan=\"4\"><div class=\"titre\">Vendeur</div></td></tr>");
if ($nb_tran_vendeur == 0)
{
	echo("<tr><td>Vous n'avez aucune transaction en cours en tant que vendeur.</td></tr>");
}
else
{
	echo("<form name=\"efface\" method=\"post\" action=\"ref_transaction.php\">");
	echo("<input type=\"hidden\" name=\"transaction\">");
	echo("<tr>");
	echo("<td class=\"soustitre2\">Objet</td>");
	echo("<td class=\"soustitre2\">Acheteur</td>");
	echo("<td class=\"soustitre2\">Prix proposé</td>");
	echo("<td></td>");
	echo("</tr>");
	while($db->next_record())
	{
		echo("<tr>");
		if ($db->f("tran_identifie") == 'O')
		{
			printf("<td>%s",$db->f("obj_nom"));
		}
		else
		{
			printf("<td>%s",$db->f("obj_nom_generique"));
		}
		echo("</td>");
		printf("<td>%s</td>",$db->f("perso_nom"));
		printf("<td>%s brouzoufs</td>",$db->f("tran_prix"));
		printf("<td><a href=\"javascript:document.efface.transaction.value=%s;document.efface.submit();\">Effacer la transaction !</a></td>",$db->f("tran_cod"));
		echo("</tr>");
		//echo '<tr><td colspan="4"></td><td><a style="font-size:7pt;" href="javascript:toutCocher(document.acheteur,\'tran\');">cocher/décocher/inverser</a></td></tr>';
	}
	echo("</form>");
}

	echo("<tr><td><div style=\"text-align:center;\"><a href=\"cree_transaction2.php\">Proposer un objet à la vente.</a></div></td></tr>");


/*******************/
/* Partie acheteur */
/*******************/
$req_tran_acheteur = "select tran_cod,gobj_tobj_cod,tran_obj_cod,tran_acheteur,tran_nb_tours,tran_prix,tran_identifie,perso_nom,obj_nom,obj_nom_generique,obj_etat
															from transaction,perso,objet_generique,objets
															where tran_acheteur = $perso_cod
															and tran_vendeur = perso_cod
															and tran_obj_cod = obj_cod
															and obj_gobj_cod = gobj_cod";

$db->query($req_tran_acheteur);
$nb_tran_acheteur = $db->nf();
echo("<table width=\"100%\"><tr><td  colspan=\"5\" class=\"titre\"><div class=\"titre\">Acheteur</div></td></tr>");
if ($nb_tran_acheteur == 0)
{
	echo("<tr><td colspan=\"5\">Vous n'avez aucune transaction à valider.</td></tr>");
}
else
{
	echo("<form name=\"acheteur\" method=\"post\" action=\"valide_transaction.php\">");
	echo("<input type=\"hidden\" name=\"transaction\">");
	echo "<input type=\"hidden\" name=\"type_a\">";
	echo("<tr><td>Transactions en attente :</td></tr>");
	echo("<tr>");
	echo '<td width="20"></td>';
	echo("<td class=\"soustitre2\">Vendeur :</td>");
	echo("<td class=\"soustitre2\">Objet</td>");
	echo("<td class=\"soustitre2\">Prix</td>");
	echo "<td class=\"soustitre2\">Etat</td>";
	
	echo("<td></td>");
	
	echo("</tr>");	
	$nb_ligne = $db->nf();
	while($db->next_record())
	{
		
		echo("<tr>");
		echo "<td><input type=\"checkbox\" class=\"vide\" name=\"tran[" . $db->f("tran_cod") . "]\" value=\"0\" id=\"tran[" . $db->f("tran_cod") . "]\"></td>";
		printf("<td class=\"soustitre2\">%s</td>",$db->f("perso_nom"));
		if ($db->f("tran_identifie") == 'O')
		{
			$nom_objet = $db->f("obj_nom") . "<i>(identifié)</i>";
		}
		else
		{
			$nom_objet = $db->f("obj_nom_generique") . "<b><i>(non identifié)</i></b>";
		}
		echo '<td><label for="tran[' . $db->f("tran_cod") . ']">' . $nom_objet . '</td>';	
		echo "<td>" . $db->f("tran_prix") . " brouzoufs.</td>";
		if ($db->f("gobj_tobj_cod") == 1)
		{
			echo "<td>" . get_etat($db->f("obj_etat")) . "</td>";
		}
		if ($db->f("gobj_tobj_cod") == 2)
		{
			echo "<td>" . get_etat($db->f("obj_etat")) . "</td>";
		}
		else
		{
			echo "<td></td>";
		}
		
		echo("</tr>");
	}
	?>
	<tr><td colspan="4"></td><td><a style="font-size:7pt;" href="javascript:toutCocher(document.acheteur,'tran');">cocher/décocher/inverser</a></td></tr>
	<?php 
	echo "<tr><td colspan=\"5\"><center><input type=\"button\" class=\"test\" onClick=\"javascript:document.acheteur.type_a.value='o';document.acheteur.submit();\" value=\"Accepter les transactions cochées\"></center></td></tr>";
	echo "<tr><td colspan=\"5\"><center><input type=\"button\" class=\"test\" onClick=\"javascript:document.acheteur.type_a.value='n';document.acheteur.submit();\" value=\"Refuser les transactions cochées\"></center></td></tr>";
	echo("</form>");
}

echo("</table>");
$contenu_page = ob_get_contents();
ob_end_clean();

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>

