<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");

include_once "verif_connexion.php";

//Contenu de la div de droite
//
$db = new base_delain;
// on regarde si le joueur est bien sur un centre d'entrainement magique
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un centre d'entrainement !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 13)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un centre de maitrise magique !!!");
	}
}

if ($erreur == 0)
{
	echo("<p>Vous entrez dans un centre de maitrise magique. Vous pourrez ici améliorer votre connaissance de la magie, moyennant finances, bien sur...");
	$req_typc = "select typc_cod,typc_libelle from type_competences where typc_cod = 5 ";
	
	$db->query($req_typc);
	?>
	<form name="amelioration_comp" method="post" action="amel_centre_entrainement_magie.php">
	<input type="hidden" name="comp_cod">
	<input type="hidden" name="prix">
	<table>
	<tr>
	<td class="soustitre2"><p>Compétence</td>
	<td class="soustitre2"><p>Valeur actuelle</td>
	<td class="soustitre2"><p>Prix de l'amélioration</td>
	<td class="soustitre2"><p>Amélioration</td>
	<td class="soustitre2"></td>
	</tr>
	<?php 
	while($db->next_record())
	{
		printf("<tr><td colspan=\"5\" class=\"titre\"><p class=\"titre\">%s</td></tr>",$db->f("typc_libelle"));
		$typc_cod = $db->f("typc_cod");
		$req_comp = "select comp_cod,typc_libelle,comp_libelle,pcomp_modificateur from perso_competences,competences,type_competences
												where pcomp_perso_cod = $perso_cod
												and pcomp_pcomp_cod = comp_cod
												and comp_typc_cod = typc_cod
												and typc_cod = $typc_cod
												and comp_cod != 27
												order by comp_libelle ";
		$db_comp = new base_delain;
		$db_comp->query($req_comp);
		while($db_comp->next_record())
		{
			echo("<tr>");
			$pcCompetence = $db_comp->f("pcomp_modificateur");
			printf("<td class=\"soustitre2\"><p>%s</td>",$db_comp->f("comp_libelle"));
			printf("<td><p style=\"text-align:right;\">%s",$pcCompetence);
			echo(" %</td>");
			$prix = 15 * $pcCompetence;
			if ($pcCompetence <= 25)
			{
				$amel = '1D4';
				$pa = 1;
			}
			if (($pcCompetence > 25) && ($pcCompetence <= 50 ))
			{
				$amel = '1D3';
				$pa = 1;
			}
			if (($pcCompetence > 50) && ($pcCompetence <= 75 ))
			{
				$amel = '1D2';
				$pa = 2;
			}
			if (($pcCompetence > 75) && ($pcCompetence < 85 ))
			{
				$amel = '1';
				$pa = 3;
			}
			if ($pcCompetence >= 85)
			{
				$amel = "Pas d'amélioration possible. Votre compétence est supérieure à 85%";
			}
			echo("<td><p style=\"text-align:right;\">$prix brouzoufs</td>");


			
			echo("<td><p>$amel</td>");
			echo("<td>");
			if ($pcCompetence < 85)
			{
				printf("<p><a href=\"javascript:document.amelioration_comp.comp_cod.value=%s;document.amelioration_comp.prix.value=$prix;document.amelioration_comp.submit();\">S'entrainer ! ($pa PA)</a>",$db_comp->f("comp_cod"));
			}
			echo("</td>");
			echo("</tr>");		
		}
	}
	echo("</table>");
	echo("</form>");
	
			include_once "quete.php";
			echo $sortie_quete;	
}	
?>
