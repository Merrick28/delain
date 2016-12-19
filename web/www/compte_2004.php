<?php 
$dep_heb = 1483.04;
$dep_paye = 1483.04;
$dep_asso = 38.29;
// juste pour les tests 
$total_dep = $dep_dns + $dep_heb + $rem_hebergeur + $dep_asso;


$reliquat = 371.89;
$rec_allopass = 0;
$rec_pub = 1157.58;
$rec_paypal = 223.24;
$rec_dons_ch = 680.33;
$rec_dons_vir = 265.00;
$cot_asso = 94.22;

$pub_attente = 197.29;

$total_rec = $rec_allopass + $rec_pub + $rec_paypal + $rec_dons_ch + $rec_dons_vir + $reliquat + $cot_asso;

$treso = $total_rec - $dep_paye - $dep_asso;

$date_maj = '22/12/2004';
$heure_maj = '09:30';
?>
<html> 
	<head>
		<title>Compte 2004</title>
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
			<td>
			<p class="titre">Comptes 2004</p>
			<table>
				
				<tr>
					<td class="soustitre2" colspan="4"><p style="text-align:center;"><b>Tr&eacute;sorerie pour l'ann&eacute;e 2004</b></td>
				</tr>
				<tr>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>D&eacute;penses</b></td>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>Recettes</b></td>
				</tr>
				<tr>
					<td class="soustitre2"><p>H&eacute;bergement et nom de domaine</td>
					<td><p style="text-align:right;"><?php  echo $dep_heb ?> &euro;</td>
					<td class="soustitre2"><p>Publicit&eacute; (1)</td>
					<td><p style="text-align:right;"><?php  echo $rec_pub ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p><i>(dont factur&eacute; &agrave; ce jour)</td>
					<td><p style="text-align:right;"><i><?php  echo $dep_paye ?> &euro;</i></td>
					<td  class="soustitre2"><p>Allopass (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_allopass ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p>Frais de cr&eacute;ation de l'association</td>
					<td><p style="text-align:right;"><?php  echo $dep_asso ?> &euro;</td>
					<td class="soustitre2"><p>Dons Paypal (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_paypal ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Dons directs (ch&egrave;ques)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_ch ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Dons directs (virements)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_vir ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Cotisations association</td>
					<td><p style="text-align:right;"><?php  echo $cot_asso ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Reliquat tr&eacute;sorerie 2003</td>
					<td><p style="text-align:right;"><?php  echo $reliquat ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p><b>Total d&eacute;penses : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_dep ?> &euro;</b></td>
					<td class="soustitre2"><p><b>Total recettes : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_rec ?> &euro;</b></td>
				</tr>
				<tr>
					<td colspan="3" class="soustitre2"><p>Tr&eacute;sorerie restante au 31/12/2004 : </td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $treso ?> &euro;</b></td>
				</tr>
				
			</table>
			
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

<?php 
$dep_heb = 1923.17;
$dep_paye = 1923.17;
$dep_dns = 0;
$dep_asso = 0;
$f_bq = 355.01;

$total_dep = $dep_dns + $dep_heb + $rem_hebergeur + $dep_asso + $f_bq;


$reliquat = 1270.93;
$rec_allopass = 0;
$rec_pub = 573.9;
$rec_paypal = 403.27;
$rec_dons_ch = 498;
$rec_dons_vir = 295.75;
$cot_asso = 98.41;

$pub_attente = 263.77;
$paypal_attente = 0;

$total_rec = $rec_allopass + $rec_pub + $rec_paypal + $rec_dons_ch + $rec_dons_vir + $reliquat + $cot_asso;

$treso = $total_rec - $dep_paye - $dep_asso - $dep_dns - $f_bq;

?>
<table background="images/fondparchemin.gif" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
		
		<tr>
			<td background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
		</tr>
		
              <tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td class="titre">
			<p class="titre">Comptes 2005
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>
		<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td>
			
			<table>
				
				<tr>
					<td class="soustitre2" colspan="4"><p style="text-align:center;"><b>Tr&eacute;sorerie pour l'ann&eacute;e 2005</b></td>
				</tr>
				<tr>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>D&eacute;penses</b></td>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>Recettes</b></td>
				</tr>
				<tr>
					<td class="soustitre2"><p>H&eacute;bergement et nom de domaine</td>
					<td><p style="text-align:right;"><?php  echo $dep_heb ?> &euro;</td>
					<td class="soustitre2"><p>Publicit&eacute; (1)</td>
					<td><p style="text-align:right;"><?php  echo $rec_pub ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p><i>(dont factur&eacute; &agrave; ce jour)</td>
					<td><p style="text-align:right;"><i><?php  echo $dep_paye ?> &euro;</i></td>
					<td  class="soustitre2"><p>Allopass (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_allopass ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p>D&eacute;pense de fonctionnement de l'association</td>
					<td><p style="text-align:right;"><?php  echo $dep_asso ?> &euro;</td>
					<td class="soustitre2"><p>Dons Paypal (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_paypal ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2">Frais bancaires</td>
					<td><p style="text-align:right;"><?php  echo $f_bq ?> &euro;</td>
					<td class="soustitre2"><p>Dons directs (ch&egrave;ques)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_ch ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Dons directs (virements)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_vir ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Cotisations association</td>
					<td><p style="text-align:right;"><?php  echo $cot_asso ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Reliquat tr&eacute;sorerie 2004</td>
					<td><p style="text-align:right;"><?php  echo $reliquat ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p><b>Total d&eacute;penses : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_dep ?> &euro;</b></td>
					<td class="soustitre2"><p><b>Total recettes : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_rec ?> &euro;</b></td>
				</tr>
				<tr>
					<td colspan="3" class="soustitre2"><p>Tr&eacute;sorerie restante au 31/12/2005 : </td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $treso ?> &euro;</b></td>
				</tr>
			</table>
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
<?php 
// d&eacute;penses
$dep_heb = 1636.18;
$dep_paye = 1636.18;
$dep_dns = 0;
$dep_asso = 0;
$dep_pub = 60;
$f_bq = 217.63;

$total_dep = $dep_dns + $dep_heb + $rem_hebergeur + $dep_asso + $f_bq + $dep_pub;

// recettes
$reliquat = 862.08;
$rec_allopass = 0;
$rec_pub = 913.35;
$rec_paypal = 0;
$rec_dons_ch = 318;
$rec_dons_vir = 282.70;
$cot_asso = 25;

$pub_attente = 258.22;
$paypal_attente = 97.13;
 
$total_rec = $rec_allopass + $rec_pub + $rec_paypal + $rec_dons_ch + $rec_dons_vir + $reliquat + $cot_asso;

$treso = $total_rec - $dep_paye - $dep_asso - $dep_dns - $f_bq - $dep_pub;
?>
<table background="images/fondparchemin.gif" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
		
		<tr>
			<td background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
			<td background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
		</tr>
		
              <tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td class="titre">
			<p class="titre">Comptes 2006
			<td background="images/ligne_droite.gif">&nbsp;</td>
			</td>
		</tr>
		<tr>
			<td background="images/ligne_gauche.gif">&nbsp;</td>
			<td>
<table>
				
				<tr>
					<td class="soustitre2" colspan="4"><p style="text-align:center;"><b>Tr&eacute;sorerie pour l'ann&eacute;e 2006</b></td>
				</tr>
				<tr>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>D&eacute;penses</b></td>
					<td class="soustitre2" colspan="2"><p style="text-align:center;"><b>Recettes</b></td>
				</tr>
				<tr>
					<td class="soustitre2"><p>H&eacute;bergement et nom de domaine</td>
					<td><p style="text-align:right;"><?php  echo $dep_heb ?> &euro;</td>
					<td class="soustitre2"><p>Publicit&eacute; (1)</td>
					<td><p style="text-align:right;"><?php  echo $rec_pub ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p><i>(dont factur&eacute; &agrave; ce jour)</td>
					<td><p style="text-align:right;"><i><?php  echo $dep_paye ?> &euro;</i></td>
					<td  class="soustitre2"><p>Allopass (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_allopass ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p>D&eacute;pense de fonctionnement de l'association</td>
					<td><p style="text-align:right;"><?php  echo $dep_asso ?> &euro;</td>
					<td class="soustitre2"><p>Dons Paypal (2)</td>
					<td><p style="text-align:right;"><?php  echo $rec_paypal ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2">Frais bancaires</td>
					<td><p style="text-align:right;"><?php  echo $f_bq ?> &euro;</td>
					<td class="soustitre2"><p>Dons directs (ch&egrave;ques)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_ch ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2">D&eacute;penses publicitaires</td>
					<td><p style="text-align:right;"><?php  echo $dep_pub ?> &euro;</td>
					<td class="soustitre2"><p>Dons directs (virements)</td>
					<td><p style="text-align:right;"><?php  echo $rec_dons_vir ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Cotisations association</td>
					<td><p style="text-align:right;"><?php  echo $cot_asso ?> &euro;</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="soustitre2"><p>Reliquat tr&eacute;sorerie 2005</td>
					<td><p style="text-align:right;"><?php  echo $reliquat ?> &euro;</td>
				</tr>
				<tr>
					<td class="soustitre2"><p><b>Total d&eacute;penses : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_dep ?> &euro;</b></td>
					<td class="soustitre2"><p><b>Total recettes : </b></td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $total_rec ?> &euro;</b></td>
				</tr>
				<tr>
					<td colspan="3" class="soustitre2"><p>Tr&eacute;sorerie au 31/12/2006 : </td>
					<td class="soustitre2"><p style="text-align:right;"><b><?php  echo $treso ?> &euro;</b></td>
				</tr>
			</table>	
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