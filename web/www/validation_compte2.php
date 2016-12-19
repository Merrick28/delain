<html>
<?php 
include G_CHE . "includes/classes.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
</head>
<body background="images/fond5.gif">
<?php if (isset($nom) && $nom != ''
    && isset($validation) && $validation != '')
{
    $db = new base_delain;
    $req_validation = "select compt_cod from compte ";
    $req_validation = $req_validation . "where compt_nom = '$nom' ";
    $req_validation = $req_validation . "and compt_validation = $validation";
    $db->query($req_validation);

    $nb_trouve = $db->nf();
}
else    // Invalid input
{
    $nb_trouve = 0;
}

if ($nb_trouve != 0)
{
	$db->next_record();
	$compt_cod = $db->f("compt_cod");
	// on a trouve quelqu'un à valider !
	$update = "update compte set compt_actif = 'O',compt_dcreat = now() where compt_cod = '$compt_cod'";
	$db->query($update);
	
	// Jusqu'au 31/12/2013 inclu, on donne 100xp pour les 10 ans de Delain (Maverick)
	if ((int)date('Y') < 2014) {
	  $donxp13 = 'insert into compte_xp2013 (cxp13_compt_cod, cxp13_total) values ('.$compt_cod.', 100)';
	  $db->query($donxp13);
	}
	?>
	<table background="images/fondparchemin.gif" width = "90%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
	<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
	<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
	</tr>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td class="titre">
	<p class="titre">Validation de l'inscription</p>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</td>
	</tr>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td class="soustitre2"><p>L'inscription est validée !</p></td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</tr>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td class="soustitre2"><p><a href="index.php">Retour à la page principale</a></p></td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</tr>
	<tr>
	<td width="10" background="images/coin_bg.gif"><img src="images/del.gif" height="10" width="10"></td>
	<td background="images/ligne_bas.gif"><img src="images/del.gif" height="10" width="10"></td>
	<td width="10" background="images/coin_bd.gif"><img src="images/del.gif" height="10" width="10"></td>
	</tr>
	</table>
	<?php 

}
else
{
	// on n'a pas trouve de validation !
	?>
	<table background="images/fondparchemin.gif" width = "90%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
	<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
	<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
	</tr>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td class="titre">
	<p class="titre">Validation échouée</p>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</td>
	</tr>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td class="soustitre2"><p>La validation de l'inscription a échoué !</p></td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</tr>
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td class="soustitre2"><p><a href="validation.php">Retour à la page de validation</a></p></td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</tr>
	<tr>
	<td width="10" background="images/coin_bg.gif"><img src="images/del.gif" height="10" width="10"></td>
	<td background="images/ligne_bas.gif"><img src="images/del.gif" height="10" width="10"></td>
	<td width="10" background="images/coin_bd.gif"><img src="images/del.gif" height="10" width="10"></td>
	</tr>
	</table>
	<?php 
}
?>
</body>
</html>
