<?php 
include "connexion.php";
include "includes/classes.php";
$db = new base_delain;
?>
<html>
	<head>
		<title>Page principale de connexion</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
	</head>
	<body background="images/fond5.gif">
	<table background="images/fondmarchemin.gif" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">

		<tr>
			<td background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
		</tr>

		<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td class="titre">
			<p class="titre">Statistiques
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>
		<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td>
			<?php 
			$req_nb_compte = "select count(compt_cod) as nb from compte where compt_actif != 'N'
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

			$req_joueur = "select count(perso_cod) as nb from perso where perso_type_perso = 1 and perso_actif != 'N' and perso_pnj != 1 ";
			$db->query($req_joueur);
			$db->next_record();
			$nb_joueur = $db->f("nb");

			$moyenne = round($nb_joueur/$nb_compte,2);

			echo("<p>Il y a aujourd'hui <b>$nb_joueur</b> personnages pour <b>$nb_compte</b> comptes (soit une moyenne de $moyenne personnages par joueur),");

			$req_joueur = "select count(perso_cod) as nb from perso where perso_type_perso = 2 and perso_actif = 'O' ";
			$db->query($req_joueur);
			$db->next_record();
			$nb_monstre = $db->f("nb");

			echo(" et <b>$nb_monstre</b> monstres dans les souterrains qui n'attendent que vous !");
			?>
			</td>
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>
		<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td><p><a href="http://www.jdr-delain.net/awstats/awstats.pl?config=jdr-delain" target="_blank">Statistiques du site web</a>
			</td>
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>

			<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td class="titre"><p class="titre">Statistiques des personnages :
			</td>
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>

		</tr>
			<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td>
			<?php 
			// classement par niveau
			$req_niveau = "select perso_niveau,count(perso_cod) as nb from perso ";
			$req_niveau = $req_niveau . "where perso_actif != 'N' and perso_type_perso = 1 and perso_pnj != 1";
			$req_niveau = $req_niveau . "group by perso_niveau ";
			$req_niveau = $req_niveau . "order by perso_niveau desc ";
			$db->query($req_niveau);
			echo("<table cellspacing=\"2\" cellpadding=\"2\">");
			echo("<tr><td class=\"soustitre2\" colspan=\"2\"><p style=\"text-align:center;\">Répartition par niveau</td></tr>");
			echo("<tr><td class=\"soustitre2\"><p>Niveau :</td><td class=\"soustitre2\"><p>Nombre de personnages :</td></tr>");
			while ($db->next_record())
			{
				echo "<tr><td class=\"soustitre2\"><p>" . $db->f("perso_niveau") . "</td><td class=\"soustitre2\"><p>" . $db->f("nb") . "</td></tr>";
			}
			echo("</table>");
			echo("<hr />");

			// classement par joueur et par sexe
			$req = "select race_nom,(select count(perso_cod) from perso where perso_actif != 'N' and perso_type_perso = 1 and perso_race_cod = race_cod and perso_sex = 'M') as m, ";
			$req = $req . "(select count(perso_cod) from perso where perso_actif != 'N' and perso_type_perso = 1 and perso_race_cod = race_cod and perso_sex = 'F') as f ";
			$req = $req . "from race where race_cod in (1,2,3,33) ";
			$db->query($req);
			echo("<table cellspacing=\"2\" cellpadding=\"2\">");
			echo("<tr><td class=\"soustitre2\" colspan=\"3\"><p style=\"text-align:center;\">Répartition par race et par sexe :</td></tr>");
			echo("<tr><td></td><td class=\"soustitre2\"><p>M</td><td class=\"soustitre2\"><p>F</td></tr>");
			while ($db->next_record())
			{
				echo "<tr><td class=\"soustitre2\"><p>" . $db->f("race_nom") . "</td><td class=\"soustitre2\"><p>" . $db->f("m") . "</td><td class=\"soustitre2\"><p>" . $db->f("f") . "</td></tr>";


			}
			echo("</table>");
			echo("<hr />");

			// classement par étage
			echo("<table cellspacing=\"2\" cellpadding=\"2\">");
			echo("<tr><td class=\"soustitre2\" colspan=\"5\"><p style=\"text-align:center;\">Répartition par étage : <br><i>Seuls les étages connus sont visibles. De nombreux antres existent et restent à la découverte des joueurs/personnages</i></td></tr>");
			echo("<tr><td class=\"soustitre2\"><p>Etage</td>
			<td class=\"soustitre2\"><p>Personnages</td>
			<td class=\"soustitre2\"><p>Niveau moyen</td>
			<td class=\"soustitre2\"><p>Monstres</td>
			<td class=\"soustitre2\"><p>Familiers</td></tr>");
			$req = "select etage_libelle, ";
			$req = $req . "(select count(perso_cod) from perso,perso_position,positions ";
			$req = $req . "where pos_etage = etage_numero ";
			$req = $req . "and ppos_pos_cod = pos_cod ";
			$req = $req . "and ppos_perso_cod = perso_cod ";
			$req = $req . "and perso_type_perso = 1 ";
			$req = $req . "and perso_actif != 'N' and perso_pnj != 1) as joueur, ";
			$req = $req . "(select sum(perso_niveau) from perso,perso_position,positions ";
			$req = $req . "where pos_etage = etage_numero ";
			$req = $req . "and ppos_pos_cod = pos_cod ";
			$req = $req . "and ppos_perso_cod = perso_cod ";
			$req = $req . "and perso_type_perso = 1 ";
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
				echo "<tr><td class=\"soustitre2\"><p>" . $db->f("etage_libelle") . "</p></td>
				<td><p>" . $db->f("joueur") . "</td>
				<td><p>" . ($db->f("joueur") != 0 ?
				            round($db->f("jnv") / $db->f("joueur") , 0) :
				            0) . "</td>
				<td><p>" . $db->f("monstre") . "</td>
				<td><p>" . $db->f("familier") . "</td></tr>";
			}





			echo("</table>");



			?>
			</td>
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>

		<tr>
			<td background="images/coin_bg.gif"><img src="images/del.gif" height="10" width="10"></td>
			<td background="images/ligne_bas.gif"><img src="images/del.gif" height="10" width="10"></td>
			<td background="images/coin_bd.gif"><img src="images/del.gif" height="10" width="10"></td>
		</tr>
	</table>

	</body>
</html>
