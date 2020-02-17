<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

$type_lieu = 6;
$nom_lieu  = 'un centre d\'entraînement';

define('APPEL', 1);
include "blocks/_test_lieu.php";

// on regarde si le joueur est bien sur un centre d'entrainement

if ($erreur == 0)
{
	echo("<p>Vous entrez dans un centre d'entrainement. Vous pourrez ici améliorer vos compétences nécessaires au combat, moyennant finances, bien sur...
	<br>Au delà d'une certaine expertise dans votre compétence, nous ne pourrons plus vous aider. Il faudra vous entrainer en conditions réelles<br /><br />");
	$req_typc = "select typc_cod,typc_libelle from type_competences where typc_cod in (2,6,7,8,19) ";

    echo "<p>Le maître des lieux est disponible pour ceux qui veulent modifier leurs compétences physiques ou magiques.<br>Nous n'offrons aucune garantie quand au résultat. Cela vous intéresse tout de même ?<br>Veuillez donc rentrer dans la <a href=\"centre_modif_carac.php\">salle spéciale...</a><br><br>";

	$stmt = $pdo->query($req_typc);
	?>
	<form name="amelioration_comp" method="post" action="amel_centre_entrainement.php">
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
											order by comp_libelle ";

		$stmt_comp = $pdo->query($req_comp);
        while ($result_comp = $stmt_comp->fetch())
		{
			echo("<tr>");
			$score = $result_comp['pcomp_modificateur'];
			printf("<td class=\"soustitre2\"><p>%s</td>",$result_comp['comp_libelle']);
			printf("<td><p style=\"text-align:right;\">%s", $score);
			echo(" %</td>");
			$prix = 4 * $score;
			if ($score <= 25)
			{
				$amel = '1D4';
				$pa = 1;
			}
			if (($score > 25) && ($score <= 50 ))
			{
				$amel = '1D3';
				$pa = 1;
			}
			if (($score > 50) && ($score <= 75 ))
			{
				$amel = '1D2';
				$pa = 2;
			}
			if (($score > 75) && ($score < 85 ))
			{
				$amel = '1';
				$pa = 3;
			}
			if ($score >= 85)
			{
				$amel = "Pas d'amélioration possible";
			}
			echo("<td><p style=\"text-align:right;\">$prix brouzoufs</td>");



			echo("<td><p>$amel</td>");
			echo("<td>");
			if ($score < 85)
			{
				printf("<p><a href=\"javascript:document.amelioration_comp.comp_cod.value=%s;document.amelioration_comp.prix.value=$prix;document.amelioration_comp.submit();\">S'entrainer ! ($pa PA)</a>",$result_comp['comp_cod']);
			}
			echo("</td>");
			echo("</tr>");
		}
	}
	echo("</table>");
	echo("</form>");
}
include "quete.php";

?>
