<html>
<?php 
include "connexion.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
</head>
<body background="images/fond5.gif">
<?php $test = debut_tran(0);
?>
<table background="images/fondparchemin.gif" width = "90%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
</tr>

<?php 
$requete = "select perso_cod,perso_dlt,to_char(now(),'DD/MM/YYYY hh24:mi:ss') from perso where perso_nom = '$nom' and perso_password = '$pass' and perso_actif = 'O'";
$resultat = pg_exec($dbconnect,$requete);
if (!$resultat)
{
	echo("<br>Une erreur est survenue pendant la recherche\n");
}
$num_resultat = pg_numrows($resultat);
if ($num_resultat == 0)
{
	// Identification échouée
	?>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td>
	<p>Identification échouée !!!</p></td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;
	</td>
	</tr>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td>
	<p><a href="index.php" target="_top">Retour à l'accueil !!!</a></p></td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</tr>
	<?php 
}
else
{
	// Identification réussie !!
	$tab_perso = pg_fetch_array($resultat,0);
	echo("<tr>\n");
	echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
	echo("<td>\n");
	echo("<p>Identification réussie !!!</p></td>\n");
	echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
	echo("</tr>\n");
// avant toute autre chose, on renseigne la date de dernier login !
$req_date = "select now()";
$res_date = pg_exec($dbconnect,$req_date);
if (!$res_date)
{
	echo("<p>Une erreur est survenue lors de la recherche de la date !");
}
$tab_date = pg_fetch_array($res_date,0);
$req_maj_date = "update perso set perso_der_connex = '$tab_date[0]' where perso_cod = $tab_perso[0]";
$res_maj_date = pg_exec($dbconnect,$req_maj_date);
if (!$res_maj_date)
{
	echo("<p>Une erreur est survenue pendant l'insertion de la date de connexion !");
}
$req_maj_dlt = "select calcul_dlt(1,$tab_perso[0])";
$res_maj_dlt = pg_exec($dbconnect,$req_maj_dlt);
if (!$res_maj_dlt)
{
	echo("<p>Une anomalie est survenue lors du calcul de la dlt");
}
$tab_maj_dlt = pg_fetch_array($res_maj_dlt,0);
$req_dlt = "select to_char(perso_dlt,'dd/mm/yyyy'),to_char(perso_dlt,'hh24:mi:ss'),perso_pa from perso where perso_cod = $tab_perso[0]";
$res_dlt = pg_exec($dbconnect,$req_dlt);
if (!$res_dlt)
{
	echo("<p>Une erreur est survenue pendant la recherche de la DLT");
}
$tab_dlt = pg_fetch_array($res_dlt,0);
$nv_tab_maj_dlt = explode(";",$tab_maj_dlt[0]);
if ($nv_tab_maj_dlt[0] == 0)
{	
	// on a calculé une nouvelle dlt
	echo("<tr>\n");
	echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
	echo("<td><p>Votre date limite de tour a été calculée.<br>");
	if ($nv_tab_maj_dlt[1] != 0)
	{
		echo("Votre temps de tour a été repoussé de <b>$nv_tab_maj_dlt[1] heures et $nv_tab_maj_dlt[2] minutes</b> à cause de vos blessures.<br>");
	}
	if ($nv_tab_maj_dlt[2] != 0)
	{
		echo("Vous avez régénéré <b>$nv_tab_maj_dlt[3]</b> points de vie. Vous avez maintenant <b>$nv_tab_maj_dlt[4]</b> points de vie.<br>");
	}
	echo("Votre nouvelle date limite de tour est : <b>$tab_dlt[0] $tab_dlt[1]</b></p></td>\n");
	echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
	echo("</tr>\n");
}
if ($nv_tab_maj_dlt[0] == 1)
{
	// l'ancienne dlt n'est pas passée
	echo("<tr>\n");
	echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
	echo("<td><p>Votre date limite de tour est : <b>$tab_dlt[0] $tab_dlt[1]</b></p></td>\n");
	echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
	echo("</tr>\n");
}
	// on affichage le solde des points d'action
	echo("<tr>\n");
	echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
	echo("<td><p>Il vous reste $tab_dlt[2] points d'action</p></td>\n");
	echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
	echo("</tr>\n");
		
	// recherche des evts non lus
	$req_evt = "select to_char(levt_date,'DD/MM/YYYY hh24:mi:ss'),tevt_libelle,levt_texte,tevt_cod,levt_perso_cod1,levt_attaquant,levt_cible from ligne_evt,type_evt where levt_perso_cod1 = $tab_perso[0] and levt_tevt_cod = tevt_cod and levt_lu = 'N'";
	$res_evt = pg_exec($dbconnect,$req_evt);
	if (!$res_evt)
	{
		echo("<p>Une erreur est survenue pendant la recherche des évènements !");
	}
	$nb_evt = pg_numrows($res_evt);
	if ($nb_evt != 0)
	{
		echo("<tr>\n");
		echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
		echo("<td><hr></td>\n");
		echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
		echo("</tr>\n");
		
		echo("<tr>");
		echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
		echo("<td><p>Vos derniers évènements importants :</p>\n");
		echo("<p>");
		for($cpt=0;$cpt<$nb_evt;$cpt++)
		{
			$tab_evt=pg_fetch_array($res_evt,$cpt);
			$req_nom_evt = "select perso1.perso_nom"; 
			if ($tab_evt[5] != '')
			{
				$req_nom_evt = $req_nom_evt . ",attaquant.perso_nom";
			}
			if ($tab_evt[6] != '')
			{
				$req_nom_evt = $req_nom_evt . ",cible.perso_nom ";
			}
			$req_nom_evt = $req_nom_evt . " from perso perso1";
			if ($tab_evt[5] != '')
			{
				$req_nom_evt = $req_nom_evt . ",perso attaquant";
			}
			if ($tab_evt[6] != '')
			{
				$req_nom_evt = $req_nom_evt . ",perso cible";
			}
			$req_nom_evt = $req_nom_evt . " where perso1.perso_cod = $tab_evt[4]";
			if ($tab_evt[5] != '')
			{
				$req_nom_evt = $req_nom_evt . " and attaquant.perso_cod = $tab_evt[5] ";
			}
			if ($tab_evt[6] != '')
			{
				$req_nom_evt = $req_nom_evt . " and cible.perso_cod = $tab_evt[6] ";
			}
			$res_nom_evt = pg_exec($dbconnect,$req_nom_evt);
			$tab_nom_evt = pg_fetch_array($res_nom_evt,0);
			$texte_evt = str_replace('[perso_cod1]',"<b>".$tab_nom_evt[0]."</b>",$tab_evt[2]);
			if ($tab_evt[5] != '')
			{
				$texte_evt = str_replace('[attaquant]',"<b>".$tab_nom_evt[1]."</b>",$texte_evt);
			}
			if ($tab_evt[6] != '')
			{
				$texte_evt = str_replace('[cible]',"<b>".$tab_nom_evt[2]."</b>",$texte_evt);
			}
			echo("$tab_evt[0] : $texte_evt ($tab_evt[1]).</br>");
		}
		echo("</td>");
		echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
		echo("</tr>\n");
		$req_raz_evt = "update ligne_evt set levt_lu = 'O' where levt_perso_cod1 = $tab_perso[0] and levt_lu = 'N'";
		$res_raz_evt = pg_exec($dbconnect,$req_raz_evt);
		if (!$res_raz_evt)
		{
			echo("<p>Une erreur est survenue pendant le raz des evts.");
		}
	}
	// formulaire pour passer au jeu
	

	
	echo("<tr>\n");
	echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
	echo("<td><form name=\"ok\" method=\"post\" action=\"jouer.php\">\n");
	echo("<input type=\"hidden\" name=\"nom_perso\" value=\"$nom\">\n");
	echo("<input type=\"hidden\" name=\"num_perso\" value=\"$tab_perso[0]\">\n");
	echo("<center><input type=\"submit\" value=\"Jouer !!\" class=\"test\"></form></td>\n");
	echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
	echo("</tr>\n");
	
		echo("<tr>\n");
	echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
	echo("<td>");
	echo("<p style=\"text-align:center;\"><br /><i>Date et heure serveur : $tab_perso[2]</i></p>");
	echo("</td>\n");
	echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;</td>\n");
	echo("</tr>\n");
}
echo("<tr>\n");
echo("<td width=\"10\" background=\"images/coin_bg.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
echo("<td background=\"images/ligne_bas.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
echo("<td width=\"10\" background=\"images/coin_bd.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
echo("</tr>\n");
echo("</table>\n");
$test = fin_tran(0);
$close=pg_close($dbconnect);
?>
</body>
</html>