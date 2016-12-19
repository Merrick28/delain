<?php 
// dépenses
$dep_heb = 1865.76;
$dep_paye = 1865.76;
$dep_dns = 0;
$dep_asso = 111.62;
$dep_pub = 00;
$f_bq = 125.40;

$total_dep = $dep_dns + $dep_heb + $rem_hebergeur + $dep_asso + $f_bq + $dep_pub;

// recettes
$reliquat = 487.32;
$rec_allopass = 0;
$rec_pub = 925.28;
$rec_paypal = 378.37;
$rec_dons_ch = 215;
$rec_dons_vir = 390;
$cot_asso = 75;

$pub_attente = 359.72;
$paypal_attente = 0;

$total_rec = $rec_allopass + $rec_pub + $rec_paypal + $rec_dons_ch + $rec_dons_vir + $reliquat + $cot_asso;

$treso = $total_rec - $dep_paye - $dep_asso - $dep_dns - $f_bq - $dep_pub;

$date_maj = '27/12/2007';
$heure_maj = '10:00';



?>

<html>
	<head>
		<title>Page principale de connexion</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
	</head>
	<body background="images/fond5.gif">
	
	<table background="images/fondparchemin.gif" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
		
		<tr>
			<td background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
		</tr>
		
              <tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td class="titre">
			<p class="titre">Soutenir le jeu
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>
		<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td>
			<p>L'aventure Delain a bien démarré, et est en pleine expansion. Toutefois, tout ceci n'est pas gratuit. <br>
			Voici pour info les finances du jeu :<br /><br />
			<table>
				
				<tr>
					<td class="soustitre2" colspan="4"><p style="text-align:center;"><b>Trésorerie pour l'année 2007</b></td>
				</tr>
				<tr>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>Dépenses</b></td>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>Recettes</b></td>
				</tr>
				<tr>
					<td class="soustitre2"><p>Hébergement et nom de domaine</td>
					<td><p style="text-align:right;"><?php  echo $dep_heb ?> </td>
					<td class="soustitre2"><p>Publicité (1)</td>
					<td><p style="text-align:right;"><?php  echo $rec_pub ?> </td>
				</tr>
				<tr>
					<td class="soustitre2"><p><i>(dont facturé à ce jour)</td>
					<td><p style="text-align:right;"><i><?php  echo $dep_paye ?> </i></td>
					<td  class="soustitre2"><p>Allopass (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_allopass ?> </td>
				</tr>
				<tr>
					<td class="soustitre2"><p>Dépense de fonctionnement de l'association</td>
					<td><p style="text-align:right;"><?php  echo $dep_asso ?> </td>
					<td class="soustitre2"><p>Dons Paypal (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_paypal ?> </td>
				</tr>
				<tr>
					<td class="soustitre2">Frais bancaires</td>
					<td><p style="text-align:right;"><?php  echo $f_bq ?> </td>
					<td class="soustitre2"><p>Dons directs (chèques)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_ch ?> </td>
				</tr>
				<tr>
					<td class="soustitre2">Dépenses publicitaires</td>
					<td><p style="text-align:right;"><?php  echo $dep_pub ?> </td>
					<td class="soustitre2"><p>Dons directs (virements)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_vir ?> </td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Cotisations association</td>
					<td><p style="text-align:right;"><?php  echo $cot_asso ?> </td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Reliquat trésorerie 2005</td>
					<td><p style="text-align:right;"><?php  echo $reliquat ?> </td>
				</tr>
				<tr>
					<td class="soustitre2"><p><b>Total dépenses : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_dep ?> </b></td>
					<td class="soustitre2"><p><b>Total recettes : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_rec ?> </b></td>
				</tr>
				<tr>
					<td colspan="3" class="soustitre2"><p>Trésorerie à ce jour : </td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $treso ?> </b></td>
				</tr>
				<tr>
					<td colspan="4"><p style="font-size:7pt;text-align:center;">Dernière mise à jour le <?php  echo $date_maj ?> - <?php  echo $heure_maj ?></td>
				</tr>
				<tr>
					<td colspan="4"><p style="text-align:center;"><a href="compte_2004.php">Détail des comptes antérieurs</a></td>
				</tr>
			</table>
			<p style="font-size:7pt;">(1) Les gains publicitaires sont payés plusieurs mois après les clicks. Ceux affichés ici ne sont que ceux rééllement encaissés.<br>Pour info, le montant en attente de paiement par les régies de publicité est de <?php  echo $pub_attente; ?> .<br />
			(2) Déductions faites des commissions liées aux modes de paiement (paypal, allopass)<br />
			Les gains paypal ne sont affichés qu'une fois réellement encaissés. Pour info, montant paypal en attente : <?php echo $paypal_attente;?> .<br>
			

			<hr>
			<p>C'est pourquoi je vous propose aujourd'hui de soutenir le jeu, en envoyant des dons.<br /><br />
			Je tiens à insister sur le fait que ces dons serviront exclusivement à financer le jeu : nom de domaine, hébergement.<br /><br />
			Pour cela, il existe quatre possibilités :<br /><br />
			<b><u>1 - Le don en ligne :</u></b><br />
			Via PayPal, vous pouvez donner ce que vous voulez par carte bleue, via un formulaire sécurisé. Pour cela, cliquez sur le bouton ci dessous (et merci de me laisser un petit message ou de m'envoyer un mail) :
			<p style="text-align:center;">	
			
			
			<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="merrick@jdr-delain.net">
<input type="hidden" name="item_name" value="Les souterrains de Delain">
<input type="hidden" name="currency_code" value="EUR">
<input type="image" src="images/don.gif" border="0" name="submit" alt="Effectuez vos paiements via PayPal : une solution rapide, gratuite et sécurisée !">
</form>
</p>
			<p><b><u>2 - L'envoi d'un chèque :</u></b><br />
			Vous pouvez faire un chèque à l'ordre de <b>association "les Amis de Delain"</b><br>
			Adresse :<br>
			chez S.Dewitte<br>
			32 rue Léo Ferré<br>
			31150 GRATENTOUR<br /><br />
			<p><b><u>3 - Allopass : </u></b><br />
			Vous <b>ne pouvez plus </b>utiliser la plateforme Allopass pour faire des dons au moyen d'un numéro surtaxé. En effet, Allopass m'a informé par mail que le don par Allopass était interdit, seuls sont autorisés les ventes de services et de contenu. Je me vois dans l'obligation de retirer ce moyen.
			
			<p><b><u>4 - le virement :</u></b><br />
			Vous pouvez faire un virement directement sur le compte des souterrains de Delain. Dans ce cas, merci de m'envoyer un mail pour me prévenir. Voici les coordonnées du compte :
			<table>
			
			<tr>
			<td class="soustitre2"><p>Code banque</td>
			<td class="soustitre2"><p>Code guichet</td>
			<td class="soustitre2"><p>Numéro de compte</td>
			<td class="soustitre2"><p>Clé RIB</td>
			<td class="soustitre2"><p>Domiciliation</td>
			</tr>
			
			<tr>
			<td class="soustitre2"><p>30002</td>
			<td class="soustitre2"><p>04042</td>
			<td class="soustitre2"><p>0000070432K</td>
			<td class="soustitre2"><p>18</td>
			<td class="soustitre2"><p>CREDIT LYONNAIS COTE PAVEE</td>
			</tr>
			
			</table>
			
			<p>Ou en identifiant international :
			
			<table>
			
			<tr>
			<td class="soustitre2"><p>IBAN</td>
			<td class="soustitre2"><p>BIC</td>
			</tr>
			
			<tr>
			<td class="soustitre2"><p>FR85 3000 2040 4200 0007 0432 K18</td>
			<td class="soustitre2"><p>CRLYFRPP</td>
			</tr>
			
			</table>
			
			
			<br /><br />
			
			
			<p><b><u>5 - les goodies :</u></b><br />
			<p>Pour l'instant, pas grand chose à vous proposer, à part le <b>téléchargement de logos</b> pour votre téléphone mobile et <a href="http://www.comboutique.com/delain" target="_blank">l'achat de T-shirts</a>... Cliquez sur une image ci dessous pour en télécharger le logo (service fourni par mobideal.com)<br>
			<center><table><tr><td>
<!-- Script Logo Perso : Drasilic -->
<script language="JavaScript" src="http://www.mobideal.com/script_logo_perso.php?id=11287&fenetre=CC6600&police=000000&mini=1"></script>
<!-- Script Logo Perso : Drasilic NB -->
<script language="JavaScript" src="http://www.mobideal.com/script_logo_perso.php?id=11288&fenetre=CC6600&police=000000&mini=1"></script>
</td></tr></table></center>
						Je tiens à préciser que les dons ne seront pas affichés individuellement sur le site, mais il y aura un récapitulatif. Faire un don n'apporte rien sinon la satisfaction d'aider le jeu. Aucun bonus de quelque nature que ce soit ne sera accordé aux joueurs dont le propriétaire a fait des dons.
			Vous pouvez donner ce que vous voulez, la somme qui vous semble la plus juste.<br />
			Pour finir, et pour couper court aux éventuelles questions, ces dons ne serviront en aucun cas à m'enrichir personnellement, mais bien à faire fonctionner le jeu, dans le seul but de vous amener le plus d'amusement possible.

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
