<?php 
//echo G_CHE;
include G_CHE . "includes/constantes.php";
include G_CHE . "includes/classes.php";
include "ident.php";
if(!$verif_auth)
{
	header('Location:' . $type_flux . G_URL . 'inter.php');
}
$db = new base_delain;
$req2 = "select count(sid) as nombre from sessions_active where changed >= to_char((now()-'5 minutes'::interval),'YYYYMMDDHH24MISS')";
$db->query($req2);
$db->next_record();
$nombre = $db->f("nombre");
if ($nombre > 120)
{
	?>
	<table width="800" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
	
	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td><p>Il y a <?php  echo ("$nombre"); ?> connecté(s) sur Delain en ce moment.
	</td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	<tr> 
	
	<tr>
	<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
	<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
	<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
	</tr>

	<tr>
	<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
	<td>
	<p>En raison d'une surcharge du serveur, les connexions simultanées sont limitées.<br>Merci de réessayer ultérieurement.
	<p>Merci de votre compréhension.
	</td>
	<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
	</td>
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
	?>
	
	<?php 
	//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
	if ($verif_auth)
	{
		//$sess->delete();
		$auth->logout();
		//$auth->auth_loginform();
		header('Location:' . G_URL . 'login2.php');
	}
}	
?>
