<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");

include_once "verif_connexion.php";

$type_lieu = 13;
$nom_lieu = 'un centre d\'entraînement';

include "blocks/_test_lieu.php";

//Contenu de la div de droite
//


if ($erreur == 0)
{
	echo("<p>Vous entrez dans un centre de maitrise magique. Vous pourrez ici améliorer votre connaissance de la magie, moyennant finances, bien sur...");
	$req_typc = "select typc_cod,typc_libelle from type_competences where typc_cod = 5 ";

    echo "<br><br><p>La maîtresse des lieux est disponible pour ceux qui veulent modifier leurs compétences physiques ou magiques.<br>Nous n'offrons aucune garantie quand au résultat. Cela vous intéresse tout de même ?<br>Veuillez donc rentrer dans la <a href=\"centre_modif_carac.php\">salle spéciale...</a><br><br>";

$stmt = $pdo->query($req_typc);
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
	while($result = $stmt->fetch())
	{
		printf("<tr><td colspan=\"5\" class=\"titre\"><p class=\"titre\">%s</td></tr>",$result['typc_libelle']);
		$typc_cod = $result['typc_cod'];
		$req_comp = "select comp_cod,typc_libelle,comp_libelle,pcomp_modificateur from perso_competences,competences,type_competences
												where pcomp_perso_cod = $perso_cod
												and pcomp_pcomp_cod = comp_cod
												and comp_typc_cod = typc_cod
												and typc_cod = $typc_cod
												and comp_cod != 27
												order by comp_libelle ";

		$stmt_comp = $pdo->query($req_comp);
		while($result_comp = $stmt_comp->fetch()())
		{
			echo("<tr>");
			$pcCompetence = $result_comp['pcomp_modificateur'];
			printf("<td class=\"soustitre2\"><p>%s</td>",$result_comp['comp_libelle']);
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
				printf("<p><a href=\"javascript:document.amelioration_comp.comp_cod.value=%s;document.amelioration_comp.prix.value=$prix;document.amelioration_comp.submit();\">S'entrainer ! ($pa PA)</a>",$result_comp['comp_cod']);
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
