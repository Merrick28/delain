<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php 
include "/home/delain/public_html/www/includes/classes.php";
include "includes/incl_mail.php";
$db = new base_delain;
?>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
</head>
<body>

<?php 
include 'jeu_test/tab_haut.php';
if (!isset($methode))
{
	$methode = "debut";
}
switch($methode)
{
	case "debut":
		?>
		<p>Pour vous inscrire à l'association, il faut :<br>
		1 - remplir le formulaire ci dessous<br>
		2 - Payer sa cotisation (deuxième étape après le formulaire, vous pourrez payer soit par paypal, soit par chèque ou virement)<br>
		Votre adhésion commencera le jour de réception du paiement (ou au 01/01/2005 pour les adhésions reçues avant le 31/12/2004), et sera valable un an.<br>
		<form name="inscr" method="post" action="inscr_asso.php">
		<input type="hidden" name="methode" value="suite">
		<center><table>
		<tr>
			<td class="soustitre2">Nom : </td>
			<td><input type="text" name="nom"></td>
			<td></td>
		</tr>
		<tr>
			<td class="soustitre2">Prénom : </td>
			<td><input type="text" name="prenom"></td>
			<td></td>
		</tr>
		<tr>
			<td class="soustitre2">Adresse mail : </td>
			<td><input type="text" name="mail"></td>
			<td></td>
		</tr>
		<tr>
			<td class="soustitre2">Pseudo sur le forum : </td>
			<td><input type="text" name="pseudo"></td>
			<td>Ce pseudo est obligatoire pour vous donner accès aux sections de vote !</td>
		</tr>
		<tr>
			<td colspan="3">
			<center><input type="submit" class="test" value="Valider !"></center>
			</td>
		</tr>
		</table></center>
		<p><i>Bien entendu, les informations fournies ne seront pas transmises à des tiers !</i>
		</form>
		<?php 
		break;
	case "suite":
		$erreur = 0;
		$valide = validateEmail($mail);	
		/*if (!$valide[0])
		{
			$erreur = -1;
			echo "<p>Adresse mail non valide !</p>";
			echo "<p><a href=\"inscr_asso.php\">Retour à l'étape 1</a>";
		}*/
		if ($erreur == 0)
		{
			$req = "select nextval('seq_asso_cod') as asso ";
			$db->query($req);
			$db->next_record();
			$asso = $db->f("asso");
			
			$req = "insert into asso (asso_cod,asso_nom,asso_prenom,asso_mail,asso_pseudo) values ";
			$req = $req . "($asso,'$nom','$prenom','$mail','$pseudo') ";
			if ($db->query($req))
			{
			?>
				<p>Votre adhésion est enregistrée. Elle ne sera validée qu'après paiement de la cotisation.<br>
				Pour cela, vous pouvez soit utiliser paypal :<br>
				<center><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="merrick@jdr-delain.net">
				<input type="hidden" name="item_name" value="Adhésion à l'association.">
				<input type="hidden" name="item_number" value="<?php echo $asso;?>">
				<input type="hidden" name="amount" value="5.00">
				<input type="hidden" name="no_note" value="1">
				<input type="hidden" name="currency_code" value="EUR">
				<input type="image" src="https://www.paypal.com/fr_FR/FR/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="Effectuez vos paiements via PayPal : une solution rapide, gratuite et sécurisée !">
				</form></center>
				<br>
				Soit envoyer un chèque de 5.00 à l'ordre des "amis de Delain", en indiquant la référénce suivante : asso-<?php echo $asso;?> à l'adresse suivante : <br>
				Association les amis de Delain<br>
				Chez S.DEWITTE<br>
				8 rue d'Aldeguier - Appt 808<br>
				31500 TOULOUSE<br>
				Soit faire un virement sur le compte suivant (et un mail à merrick@jdr-delain.net en me rappellant la référence : asso-<?php echo $asso;?> afin de finir l'enregistrement)
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
				<?php 
			}
		}
		break;
}




include 'jeu_test/tab_bas.php';
?>

</body>
</html>
