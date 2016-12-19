<?php 
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set("html_errors","1");
ini_set("pgsql.ignore_notice","0");
ini_set("error_reporting","E_ALL");
error_reporting(E_ALL);
include "includes/constantes.php";
include "includes/classes.php";
include "includes/fonctions.php";
page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
echo '<!--';
foreach($auth->auth['uid'] as $key => $val)
{
echo $key . ' - ' . $val;
}
echo '-->';
$sess->register('auth');
$db2 = new base_delain;
$session = $sess->id();
$nom = $_POST['nom'];
$pass = $_POST['pass'];
$type_perso = $auth->auth["type_perso"];
$is_log = 0;
if ($auth->is_authenticated())
{
	$resultat = $auth->auth_validatelogin();
	if ($resultat['uid'] == '')
	{
		//
		// on recherche le type perso
		//
		
		?>
		<link rel="stylesheet" type="text/css" href="style.css" title="essai">
		<head>
		</head>
		<body background="images/fond5.gif">
		<table background="images/fondparchemin.gif" width = "90%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
		<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
		<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
		</tr>
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
		<p><a href="login2.php" target="_top">Retour à l'accueil !!!</a></p></td>
		<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
		</tr>
		<?php 
	}	
	else
	{
		$ip = getenv("REMOTE_ADDR");
		$db = new base_delain;
		$sess->register('compt_cod');
	 	//$_SESSION['compt_cod'] = $compt_cod;
		$sess->register(password);
		$is_admin_monstre = false;
		$is_admin = false;
		if ($type_perso == 'monstre')
		{
			$is_admin_monstre = true;
		}
		if ($type_perso == 'admin')
		{
			$is_admin = true;
		}
		$var_type_perso = $type_perso;
		$compt_cod = $resultat['uid'];
		$_SESSION['compt_cod'] = $compt_cod;
		$req = 'select compt_monstre from compte
			where compt_cod = ' . $compt_cod;
		$db->query($req);
		$db->next_record();
		if($db->f('compt_monstre') == 'O')
			$type_perso = 'monstre';
		else
			$type_perso = 'joueur';
		$req = 'select compt_admin from compte
			where compt_cod = ' . $compt_cod;
		$db->query($req);
		$db->next_record();
		if($db->f('compt_admin') == 'O')
			$type_perso = 'admin';
		$password = $pass;
		$anc_compte = $_COOKIE['nvcompte'];
		if (isset($anc_compte))
		{
			if ($anc_compte != $compt_cod)
			{
				$req = "insert into multi_trace (multi_cpt1,multi_cpt2,multi_date,multi_ip) ";
				$req = $req . "values ($anc_compte,$compt_cod,now(),'$ip') ";
				$db->query($req);
			}	
		}
		$idsess = $_COOKIE['idsess'];
		if (!isset($idsess))
		{
			$idsess = sha1(uniqid(rand(), true).$ip);
			$idsess .= md5(uniqid(rand(), true));
		}
		$req = 'insert into histo_log (hlog_id,hlog_ip,hlog_compte)
			values (\'' . $idsess . '\',\'' . $ip . '\',' . $compt_cod . ')';
		$db->query($req);
		setcookie("idsess",$idsess,time()+31536000);
		setcookie("nvcompte",$compt_cod,time()+3600);
		setcookie('cook_pass',false,time()-3600);
		setcookie(apc_fetch('nom_cook'),md5($password),time()+3600);
		setcookie(apc_fetch('nom_cook'),md5($password),time()+3600,'/jeu/');
		//$_COOKIE['nom_cook'] = md5($password);
		setcookie("is_admin_monstre",$is_admin_monstre,time()+3600);
		setcookie("is_admin",$is_admin,time()+3600);
		// on va maintenant prendre le idsess et lui enlever tous les alphas
		$idsessa = intval(preg_replace('`[^0-9]`', '', $idsess));
		$idsessa += $compt_cod;
		setcookie("ctrl_sess",$idsessa,time()+3600);
		
		echo ("<head>");
		?>
		<link rel="stylesheet" type="text/css" href="style.css" title="essai">
		<?php 
		echo ("</head>");
		echo ("<body background=\"images/fond5.gif\">");
		echo("<table background=\"images/fondparchemin.gif\" width = \"90%\" bgcolor=\"#EBE7E7\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n");
		echo("<tr>\n");
		echo("<td width=\"10\" background=\"images/coin_hg.gif\"><img src=\"images/del.gif\" height=\"8\" width=\"10\"></td>\n");
		echo("<td background=\"images/ligne_haut.gif\"><img src=\"images/del.gif\" height=\"8\" width=\"10\"></td>\n");
		echo("<td width=\"10\" background=\"images/coin_hd.gif\"><img src=\"images/del.gif\" height=\"8\" width=\"10\"></td>\n");
		echo("</tr>\n");echo("<tr>\n");
		echo("<td width=\"10\" background=\"images/ligne_gauche.gif\">&nbsp;</td>\n");
		echo("<td>\n");
		
		$req_dmaj = "update compte set compt_der_connex = now(),compt_ip = '$ip' where compt_cod = $compt_cod ";
		
		$db->query($req_dmaj);
		
		// Ici on sépare si monstre ou joueur
		
		// si monstre
		if ($type_perso == 'monstre')
		{
			//$sess->register(compt_cod);
			//$_SESSION['compt_cod'] = $compt_cod;
			$req = "select dcompt_etage,dcompt_monstre_carte,dcompt_modif_perso from compt_droit where dcompt_compt_cod = $compt_cod ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				die("Erreur sur les etages possibles !");
			}
			else
			{
				$db->next_record();
				$droit['etage'] = $db->f("dcompt_etage");
				$monstre_carte = $db->f("dcompt_monstre_carte");
				$modif_perso = $db->f("dcompt_modif_perso");
			}
			if ($droit['etage'] == 'A')
			{
				$restrict = '';
				$restrict2 = '';
			}
			else
			{
				$restrict = 'where etage_numero in (' . $droit['etage'] . ') ';
				$restrict2 = 'and pos_etage in (' . $droit['etage'] . ') ';
			}
			$is_admin_monstre = true;
			?>
			<table>
			<tr>
			<td class="titre">
			<p class="titre">Monstres "normaux" </p></td>
			</tr>
			<tr>
			<td>
			<?php 
			$db = new base_delain;
			$req = "select etage_libelle,etage_numero from etage " . $restrict . "order by etage_numero desc";
			$db->query($req);
			echo("<p>");
			while($db->next_record())
			{
				echo "<a href=\"login_monstre.php?etage=" . $db->f("etage_numero") . "&compt_cod=" . $compt_cod . "\">" . $db->f("etage_libelle") . "</a><br />";
			}
			?>
			<hr>
			<?php 
			$req = "select perso_nom,perso_cod,pos_x,pos_y,etage_libelle,count(dmsg_cod) as mess
				from perso,messages_dest,positions,perso_position,etage
				where (perso_type_perso = 2 or perso_pnj = 1)
				and perso_actif = 'O'
				and perso_cod = dmsg_perso_cod
				and dmsg_lu = 'N'
				and ppos_perso_cod = perso_cod
				and pos_etage = etage_numero
				and ppos_pos_cod = pos_cod " . $restrict2 ."	group by perso_nom,perso_cod,pos_x,pos_y,etage_libelle
				having count(dmsg_cod) >= 1";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Pas de messages en attente !";
			}
			else
			{
				echo "<p>Messages en attente : ";
				while($db->next_record())
				{
					echo "<br><a href=\"validation_login_monstre.php?numero=" . $db->f("perso_cod") . "&compt_cod=" . $compt_cod . "\">" . $db->f("perso_nom") . " - <b>(" , $db->f("mess") , " messages)</b> (" , $db->f("pos_x"), ", ", $db->f("pos_y"), ", " , $db->f("etage_libelle") , ")</a>";
					$req = "select compt_nom from perso_compte,compte
						where pcompt_perso_cod = " . $db->f("perso_cod") .
						"and pcompt_compt_cod = compt_cod
						and compt_monstre = 'O' ";
						$db2->query($req);
						if ($db2->nf() != 0)
						{
							$db2->next_record();
							echo "<b> - Attribué à " , $db2->f("compt_nom") , "</b>";
						}
				}
			}
			echo "<hr>";
			$req = "select perso_cod,perso_nom,pos_x,pos_y,etage_libelle from perso,perso_compte,positions,etage,perso_position where pcompt_compt_cod = $compt_cod
				and pcompt_perso_cod = perso_cod
				and (perso_type_perso = 2 or perso_pnj = 1)
				and perso_actif = 'O' 
				and ppos_perso_cod = perso_cod
				and ppos_pos_cod = pos_cod 
				and etage_numero = pos_etage ";
			$db->query($req);
			if($db->nf() != 0)
			{
				echo "<p class=\"titre\">Monstres rattachés</p>";
				while($db->next_record())
				{
					echo "<br><a href=\"validation_login_monstre.php?numero=" . $db->f("perso_cod") . "&compt_cod=" . $compt_cod . "\">" . $db->f("perso_nom") .  "(" , $db->f("pos_x"), ", ", $db->f("pos_y"), ", " , $db->f("etage_libelle") , ")</a>";
					$req = "select compt_nom from perso_compte,compte
						where pcompt_perso_cod = " . $db->f("perso_cod") .
						"and pcompt_compt_cod = compt_cod
						and compt_monstre = 'O' ";
						$db2->query($req);
						if ($db2->nf() != 0)
						{
							$db2->next_record();
							echo "<b> - Attribué à " , $db2->f("compt_nom") , "</b>";
						}
				}
			}
			echo "<hr>";
			if ($monstre_carte == 'O')			
			{
				echo "<p><a href=\"monstre_choix_vue_total.php?compt_cod=$compt_cod\">Accès aux cartes</a>";
			}
			if ($modif_perso == 'O')
			{
				echo "<p><a href=\"../jeu/admin_piege.php?compt_cod=$compt_cod\">Gestion des pièges</a>";
			}
			if ($modif_perso == 'O')
			{
				echo "<p><a href=\"../jeu/admin_cachette.php?compt_cod=$compt_cod\">Gestion des cachettes</a>";
			}			
			?>
			</td>
			</tr>
			</table>
			<?php 	
		}
		// Si admin 
		if ($type_perso == 'admin')
		{
		$is_admin = true;
			?>
			<style>
			#formulaire {
	padding: 5px;
	margin: 10px 0 0 10px;
	border: 1px dashed #999;
	width: 590px;
}

#formulaire fieldset {
	border: 0;
	margin: 0;
	padding: 0;
}

#formulaire fieldset label {
	display: block;
}

#formulaire legend {
	margin: 0 0 5px;
}

#formulaire p {
	display: block;
	padding: 5px 0 0;
	margin: 10px 0 0;
	width: 580px;
}

#zoneResultats {
	border: 1px solid #000;
	background-color: #fff;
	display: block;
	overflow:auto;
	margin-left: 200;
	padding: 0;
	position: absolute;
	width: 400px;
}

#zoneResultats li {
	background: #fff;
	display: block;
	margin: 0;
	padding: 0;
}

#zoneResultats li a{
	display: block;
	padding: 2px;
	text-decoration: none;
}
#zoneResultats li a:hover{
	background-color: #ffffc0;
}		

input {
	margin: 0;
}
</style>
<?php 
include 'sadmin.php';
			echo "<form name=\"login2\" method=\"post\" id=\"login2\" action=\"jouer.php\" target=\"_top\">";
			echo "<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">";
		
			echo "<p>Entrez directement le numéro de perso : <input type=\"text\" name=\"num_perso\"> <input type=\"submit\" value=\"Voir !\" class=\"test\">";
			echo '<p>Tapez un nom de perso pour trouver son numéro : 
				<input type="text" name="foo" id="foo" value="" onkeyup="loadData();document.getElementById(\'zoneResultats\').style.visibility = \'hidden\'" />
				<ul id="zoneResultats" style="visibility: hidden;"></ul>';
			echo "";
			echo "</form>";
		}
		// Si joueur 
		if ($type_perso == 'joueur')
		{		
			$req = "select news_cod from news order by news_cod desc limit 1";
			$db->query($req);
			$db->next_record();
			$news_cod = $db->f("news_cod");
			$req = "select compt_hibernation,compt_der_news,to_char(compt_dfin_hiber,'DD/MM/YYYY hh24:mi:ss') as fin, compt_acc_charte from compte ";
			$req = $req . "where compt_cod = $compt_cod ";
			$db->query($req);
			$db->next_record();
			if ($db->f("compt_hibernation") == 'O')
			{
				echo "<p>Votre compte est en hibernation jusqu'au " . $db->f("fin") . "<br>";
				echo "Vous ne pouvez pas vous connecter d'ici là.";
			}
			else
			{
				if ($db->f("compt_der_news") < $news_cod)
				{
					?>
					<p class="titre">Dernieres nouvelles : </p>
					<?php 
					$db2 = new base_delain;
					$recherche = "SELECT news_cod,news_titre,news_texte,to_char(news_date,'DD/MM/YYYY') as date_news,news_auteur,news_mail_auteur FROM news where news_cod > " . $db->f("compt_der_news") . " order by news_cod desc limit 3 ";
					$db2->query($recherche);
					while($db2->next_record())
					{
						$auteur_news = $db->f("news_auteur");
						?>
						<p class="titre"><?php echo $db2->f("news_titre");?></p>
						<p class="texteNorm" style="text-align:right;"><?php echo $db2->f("date_news");?></p>
						<p class="texteNorm">
						<?php echo $db2->f("news_texte");?>
						</p>
						<p class="texteNorm" style="text-align:right;">
						<?php 
						if ($auteur_news != "")
						{
							echo "<a href=\"mailto:" . $db2->f("news_mail") . "\">" . $db2->f("news_auteur") . "</a>";
						}
						else
						{
							echo $db2->f("news_auteur");
						}
						?>
						</p>
						<?php 
						
					}
					$req = "update compte set compt_der_news = " . $news_cod . " where compt_cod = " . $compt_cod;
					$db2->query($req);
					include 'jeu_test/tab_bas.php';
					include 'jeu_test/tab_haut.php';
				}
				// on efface l'hibernation si il en reste
				if ($db->f("compt_hibernation") == 'T')
				{
					$req = "select fin_hibernation($compt_cod) "; 
						$db->query($req);
				}
				if ($db->f("compt_acc_charte") == 'N')
				{
					echo "<p>Vous devez revalider la <a href=\"charte.php\" target=\"_blank\">charte des joueurs</a>.<br>";
					echo "Cette opération est nécessaire pour continuer.<br>";
					echo "Afin de valider la charte, cliquez <a href=\"valide_charte.php\">ici.<a/>";
				}
				else
				{
					$req_perso = "select pcompt_perso_cod,perso_nom,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss'),perso_pv,perso_pv_max,dlt_passee(perso_cod),perso_pa,perso_race_cod,perso_sex,limite_niveau(perso_cod),limite_niveau_actuel(perso_cod),perso_px,pos_x,pos_y,pos_etage,perso_niveau from perso,perso_compte,perso_position,positions ";
					$req_perso = $req_perso . "where pcompt_compt_cod = $compt_cod ";
					$req_perso = $req_perso . "and pcompt_perso_cod = perso_cod ";
					$req_perso = $req_perso . "and ppos_perso_cod = perso_cod ";
					$req_perso = $req_perso . "and ppos_pos_cod = pos_cod ";
					$req_perso = $req_perso . "order by perso_cod ";
					$db = new base_delain;
					$db->query($req_perso);
					$nb_perso = $db->nf();
					if ($nb_perso == 0)
					{
						echo("<p>Aucun joueur dirigé.<br />");
						echo("<form name=\"nouveau\" method=\"post\">");
						echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");
						
						echo("<a href=\"javascript:document.nouveau.action='cree_perso_compte.php';document.nouveau.submit();\">Créer un nouveau joueur !</a>");
						echo("</form>");
					}
					else
					{
						echo("<table background=\"images/fondparchemin.gif\" border=\"0\">");
						echo("<form name=\"login\" method=\"post\" action=\"validation_login3.php\">");
						echo("<input type=\"hidden\" name=\"perso\">");
						echo("<input type=\"hidden\" name=\"compt_cod\" value=\"$compt_cod\">");
						echo("<input type=\"hidden\" name=\"password\" value=\"$pass\">");
						include "tab_switch.php";
		
						echo("</table>");	
						echo("</form>");
					}
					$req = "select to_char(now(),'DD/MM/YYYY hh24:mi:ss') as maintenant ";
$db->query($req);
$db->next_record();
echo "<p style=\"text-align:center;\"><a href=\"http://www.jdr-delain.net/jeu/logout.php\"><b>se déconnecter</b></a></p>";
echo "<p style=\"text-align:center;\"><br /><i>Date et heure serveur : " , $db->f("maintenant") , "</i></p>";
				}
			}	
		}
			
			
			
		echo("</td>\n");
		echo("<td width=\"10\" background=\"images/ligne_droite.gif\">&nbsp;\n");
		echo("</td>\n");
		echo("</tr>\n");
	
	}
}
echo("<tr>\n");
echo("<td width=\"10\" background=\"images/coin_bg.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
echo("<td background=\"images/ligne_bas.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
echo("<td width=\"10\" background=\"images/coin_bd.gif\"><img src=\"images/del.gif\" height=\"10\" width=\"10\"></td>\n");
echo("</tr>\n");
echo("</table>\n");
?>
</body>
</html>
