<?php 
require_once "includes/classes.php";
$erreur = 0;
if (!isset($perso_nom))
{
	$perso_nom = '';
	$password = '';
}
$db = new base_delain;
$req2 = "select count(sid) as nombre from sessions_active where changed >= to_char((now()-'5 minutes'::interval),'YYYYMMDDHH24MISS')";
$db->query($req2);
$db->next_record();
$nombre = $db->f("nombre");
$param = new parametres();
?>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">

<head>
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.gif" type="image/gif">

</head>
<body background="<?php  echo G_IMAGES; ?>fond5.gif">
	<center><table background="<?php  echo G_IMAGES; ?>fondparchemin.gif" width="800" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
<tr>
<td colspan="3">
<table cellpadding="0" cellspacing="0">
<tr><td><img src="<?php  echo G_IMAGES; ?>title.gif"></td></tr>
<tr><td><img src="<?php  echo G_IMAGES; ?>identification.gif"></td></tr>
</table>
</td>
</tr>
<table background="<?php  echo G_IMAGES; ?>fondparchemin.gif" width="800" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="10" background="<?php  echo G_IMAGES; ?>coin_hg.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
<td background="<?php  echo G_IMAGES; ?>ligne_haut.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
<td width="10" background="<?php  echo G_IMAGES; ?>coin_hd.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
</tr>
 
<tr>
<td width="10" background="<?php  echo G_IMAGES; ?>ligne_gauche.gif">&nbsp;</td>
<td><p>Il y a <?php  echo ("$nombre"); ?> connecté(s) sur Delain en ce moment.
</td>
<td width="10" background="<?php  echo G_IMAGES; ?>ligne_droite.gif">&nbsp;</td>
<tr> 
<tr>
<td width="10" background="<?php  echo G_IMAGES; ?>ligne_gauche.gif">&nbsp;</td>
<td><p>Aujourdhui, dans les souterrains, <?php echo $param->getparm(64);?> aventuriers, <?php echo $param->getparm(65);?> monstres et <?php echo $param->getparm(66);?> familiers sont morts au combat. Etes-vous sûr de vouloir vous connecter quand même ?
<hr>
<table><td>En attendant de vous connecter, vous pouvez aller voir la <a href="champions.php"><b>page des champions !</b></a></td>
<td>
	</td>
</table>
<hr>
<?php 
echo '<center><table><tr class="titre"><td colspan="4">Animation du moment</td></tr>
<tr>
	<td>
		<p class="titre">Collectionneurs de crânes</p>
		<table>
			<tr>
				<td class="soustitre2">Nom</td><td class="soustitre2">Nombre</td></tr>';
		$req = 'select perso_nom, count(*) as nombre from objets, perso_objets, perso
where perobj_obj_cod = obj_cod
and obj_gobj_cod = 850
and perobj_perso_cod = perso_cod
group by perso_nom
order by nombre desc
limit 10';
		$db->query($req);
		while($db->next_record())
			echo '<tr><td class="soustitre2">' . $db->f('perso_nom') . '</td><td>' . $db->f('nombre') . '</td></tr>';
		echo '</table>
	</td>
</tr>
</table>'


/*
//
// compteur halloween
//
echo '<center><table><tr class="titre"><td colspan="4">Animation du moment</td></tr>
<tr>
	<td>
		<p class="titre">Tartiflettes Killers</p>
		<table>
			<tr>
				<td class="soustitre2">Nom</td><td class="soustitre2">Nombre</td></tr>';
		$req = 'select perso_cod,perso_nom,count(cpt_tueur) as nombre
			from perso,compt_halloween
			where cpt_type_monstre = 422
			and cpt_tueur = perso_cod
			group by perso_cod,perso_nom
			order by nombre desc
			limit 5';
		$db->query($req);
		while($db->next_record())
			echo '<tr><td class="soustitre2">' . $db->f('perso_nom') . '</td><td>' . $db->f('nombre') . '</td></tr>';
		echo '</table>
	</td>

	<td>
		<p class="titre">Collectionneurs de lardons</p>
		<table>
			<tr>
				<td class="soustitre2">Nom</td><td class="soustitre2">Nombre</td></tr>';
		$req = 'select perso_nom,count(obj_cod) as nombre
from perso,perso_objets,objets
where obj_gobj_cod = 624
and perobj_obj_cod = obj_cod
and perobj_perso_cod = perso_cod
group by perso_nom
order by nombre desc
limit 5';
		$db->query($req);
		while($db->next_record())
			echo '<tr><td class="soustitre2">' . $db->f('perso_nom') . '</td><td>' . $db->f('nombre') . '</td></tr>';
		echo '</table>
	</td>

	<td>
		<p class="titre">Nettoyeurs d\'endives</p>
		<table>
			<tr>
				<td class="soustitre2">Nom</td><td class="soustitre2">Nombre</td></tr>';
		$req = 'select perso_cod,perso_nom,count(cpt_tueur) as nombre
			from perso,compt_halloween
			where cpt_type_monstre = 423
			and cpt_tueur = perso_cod
			group by perso_cod,perso_nom
			order by nombre desc
			limit 5';
		$db->query($req);
		while($db->next_record())
			echo '<tr><td class="soustitre2">' . $db->f('perso_nom') . '</td><td>' . $db->f('nombre') . '</td></tr>';
		echo '</table>
	</td>

	<td>
		<p class="titre">Entrepôts de béchamel</p>
		<table>
			<tr>
				<td class="soustitre2">Nom</td><td class="soustitre2">Nombre</td></tr>';
		$req = 'select perso_nom,count(obj_cod) as nombre
from perso,perso_objets,objets
where obj_gobj_cod = 625
and perobj_obj_cod = obj_cod
and perobj_perso_cod = perso_cod
group by perso_nom
order by nombre desc
limit 5';
		$db->query($req);
		while($db->next_record())
			echo '<tr><td class="soustitre2">' . $db->f('perso_nom') . '</td><td>' . $db->f('nombre') . '</td></tr>';
		echo '</table>
	</td>
</tr></table></center>';*/
?>
<hr><center><script language='JavaScript' src='http://www.clicjeux.net/banniere.php?id=390'></script></center><hr>
<p><b>Puisqu'il faut le rappeler :</b><br>
	Le multi comptes est <b>strictement</b> interdit ! Le multi comptes est le fait d'avoir plusieurs comptes pour le même joueur.<br>
	Cette technique pénalise le serveur et déséquilibre le jeu. Toute personne pratiquant le multi comptes pourra voir ses comptes supprimés.<br>
	De plus, le sitting de son compte n'est toléré que pour une durée de 5 jours au maximum. Tout sitting de plus de 5 jours sera considéré comme du multi comptes et sanctionné comme tel. <br>
		<div style="text-align:center;"><a href="http://www.jdr-delain.net/charte.php">La charte complète.</a><hr>
</td>
<td width="10" background="<?php  echo G_IMAGES; ?>ligne_droite.gif">&nbsp;</td>
<tr> 
<tr>
<td width="10" background="<?php  echo G_IMAGES; ?>coin_bg.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
<td background="<?php  echo G_IMAGES; ?>ligne_bas.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
<td width="10" background="<?php  echo G_IMAGES; ?>coin_bd.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
</tr>
</table>
<?php  
if ($nombre >= 120)
{
	?>
	<table background="<?php  echo G_IMAGES; ?>fondparchemin.gif" width="800" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_hg.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
	<td background="<?php  echo G_IMAGES; ?>ligne_haut.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_hd.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
	</tr>

	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_gauche.gif">&nbsp;</td>
	<td>
	<p>En raison d'une surcharge du serveur, les connexions simultanées sont limitées.<br>Merci de réessayer ultérieurement.
	<p>Merci de votre compréhension.
	</td>
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_droite.gif">&nbsp;</td>
	</td>
	</tr>
	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_bg.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
	<td background="<?php  echo G_IMAGES; ?>ligne_bas.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_bd.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
	</tr>
	</table>
	<?php 
	//page_close();
	$erreur = 1;
}
else
{
	?>


	<form name="login" method="post" action="<?php echo $type_flux .G_URL;?>validation_login2.php">
<table background="<?php  echo G_IMAGES; ?>fondparchemin.gif" width="800" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
	
	
	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_hg.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
	<td background="<?php  echo G_IMAGES; ?>ligne_haut.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_hd.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="8" width="10"></td>
	</tr>
	

	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_gauche.gif">&nbsp;</td>
	<td>
	
	
	

	</td>
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_droite.gif">&nbsp;</td>
	</tr> 
	
	
	
	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_gauche.gif">&nbsp;</td>
	<td>
	
	<center><table>
	<tr><td>	<p style="text-align:center;"><b>Nom du compte</b></td>
	<td><input type="text" name="username" value="<?php  echo ("$perso_nom") ?>"></td>
	<td><p><b>Mot de passe</b></td>
	<td><p><input type="password" name="password" value="<?php  echo ("$password") ?>"> <i><a href="renvoi_mdp.php">Mot de passe oublié ? </a></i></td></td>
	</tr>
	</table></center>
	
	
	</td>
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_droite.gif">&nbsp;</td>
	</tr>
	
	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_gauche.gif">&nbsp;</td>
	<td><center>

		<input type="submit" class="test" value="Valider !">
	
	</center></td>
	
	<td width="10" background="<?php  echo G_IMAGES; ?>ligne_droite.gif">&nbsp;</td>
	</tr>
		</form>
		
	
	

	
	<tr>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_bg.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
	<td background="<?php  echo G_IMAGES; ?>ligne_bas.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
	<td width="10" background="<?php  echo G_IMAGES; ?>coin_bd.gif"><img src="<?php  echo G_IMAGES; ?>del.gif" height="10" width="10"></td>
	</tr>
	</table></center>
	<?php 
	
	$pub = '<script type="text/javascript"><!--
google_ad_client = "pub-6632318064183878";
google_alternate_color = "666666";
google_ad_width = 468;
google_ad_height = 60;
google_ad_format = "468x60_as";
google_ad_channel ="";
google_page_url = document.location;
google_color_border = "800000";
google_color_bg = "CEB68D";
google_color_link = "800000";
google_color_url = "800000";
google_color_text = "000000";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';

	echo "<center>" , $pub , "</center>";
	page_close();
}

?>

</body>
</html>

