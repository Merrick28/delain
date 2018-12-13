<html>
	<head>
		<title>Page principale de connexion</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
	</head><br>
	<body background="images/fond5.gif">
	<br>
<?php  include "jeu_test/tab_haut.php"; ?>
<?php 
include "includes/classes.php";
$db = new base_delain;
if (!isset($methode))
{
	$methode = "debut";
}
switch($methode)
	{
		case "debut":
			?>
			Cette charte des joueurs de Delain explicite, comme la précédente, les règles de répartition de PX entre les personnages des souterrains. Cette répartition étant encore manuelle par nécessité, nous sommes obligés de la réglementer afin d'éviter les principaux abus et déséquilibres qui pourraient résulter de dons de PX exotiques. Aussi, vous êtes invités à (re)lire ce qui suit attentivement, les sanctions pour non respect de la charte pouvant aller de l'avertissement à la suppression pure et simple d'un compte.<br><br>
 
Un système de répartition automatique des PX est à l'étude mais demande un temps conséquent. Aussi une mise à jour de la présente charte est devenue indispensable malgré les changements du système à venir.<br><br>
 
Une version de cette même charte est disponible sur le <a href=http://www.jdr-delain.net/forum/viewtopic.php?t=8606&start=0&postdays=0&postorder=asc&highlight=>forum</a> avec les modifications apportées à la première charte en évidence. Toutefois, nous vous encourageons fortement à relire <strong>en entier</strong> ce document que vous allez accepter.<br><br>
			
			<center><IFRAME name="charte des joueurs" SRC="http://www.jdr-delain.net/charte.php" border=0 frameborder=0 height=350 width="80%"></IFRAME></center><br>
			<form method="post" action="valide_charte.php">
			<input type="hidden" name="methode" value="e1">
			<p>
			<hr><p>Afin de valider cette charte, rentrez votre nom de compte et password<br>
			<strong>La validation de ce formulaire entraine l'acceptation de la charte !
			<center><table>
			<tr><td>	<p style="text-align:center;"><strong>Nom du compte</strong></td>
			<td><input type="text" name="nom"></td>
			<td><p><strong>Mot de passe</strong></td>
			<td><p><input type="password" name="pass"> <em><a href="renvoi_mdp.php">Mot de passe oublié ? </a></em></td></td>
			</tr>
			</table>
			<input type="submit" class="test" value="J'accepte !">
			</form>
			<?php 
			break;
		case "e1";
			$req = "select compt_cod from compte ";
			$req = $req . "where compt_nom = '$nom' ";
			$req = $req . "and compt_password = '$pass' ";
			$req = $req . "and compt_actif = 'O' ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Erreur ! Aucun compte trouvé avec ces coordonées !";
			}
			else
			{
				$db->next_record();
				$num = $db->f("compt_cod");
				$req = "update compte set compt_acc_charte = 'O' where compt_cod = $num ";
				$db->query($req);
				echo "<p>Votre compte est validé.";
				echo "<p><a href=\"login2.php\">Retour à l'identification</a>";
			}
			break;
			
	}
	?>			
			
<?php  include "jeu_test/tab_bas.php"; ?>
	</body>
</html>