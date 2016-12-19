<?php 
include "/home/sdewitte/public_html/includes/constantes.php";
include "/home/sdewitte/public_html/includes/classes.php";
include "/home/sdewitte/public_html/includes/fonctions.php";
?>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
</head>
<body background="images/fond5.gif">

<img src="images/title.gif"><br />
<img src="images/identification.gif"><br />
<form name="login" method="post" action="validation_login2.php">
<table background="images/fondmarchemin.gif" width="800" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">

<tr>
<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
</tr>

<tr>
<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
<td><p><b>Puisqu'il faut le rappeler : </b>Le multi-compte est interdit ! Le multi-compte est la fait d'avoir plusieurs comptes pour un seul et même joueur. Cela utilise des ressources machines, et nuit fortement à l'ambiance du jeu.<br>
Afin que tous puissent apprécier les souterrains de Delain, merci donc de ne pas utiliser cette méthode.<br>
De nombreux comptes sont ainsi fermés chaque jour, et nous continuerons aussi longtemps qu'il le faudra. En cas d'abus suspecté, un message sera adressé aux joueurs, et si nous n'avons pas de réponse, nous supprimons le compte. Si l'abus est flagrant, nous n'attendrons pas de réponse.<br>
<hr>
</td>
<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
<tr>

<?php 
$req2 = "select count(sid) as nombre from active_sessions where changed >= to_char((now()-'5 minutes'::interval),'YYYYMMDDHH24MISS')";
$db = new base_delain;
$db->query($req2);
$db->next_record();
$nombre = $db->f("nombre");
?>

 <tr>
<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
<td><p>Il y a <?php  echo ("$nombre"); ?> connecté(s) sur Delain en ce moment.
</td>
<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
<tr> 

<tr>
<td width="10" background="images/coin_bg.gif"><img src="images/del.gif" height="10" width="10"></td>
<td background="images/ligne_bas.gif"><img src="images/del.gif" height="10" width="10"></td>
<td width="10" background="images/coin_bd.gif"><img src="images/del.gif" height="10" width="10"></td>
</tr>
</table>


<?php 
page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
$auth->auth_loginform();
?>

<table background="images/fondmarchemin.gif" width="800" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
</tr>

<tr>
<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
<td>
<center><script language='JavaScript' src='http://www.clicjeux.net/banniere.php?id=49'></script></center>
</td>
<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
</td>
</tr>

<tr>
<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
<td>
<p style="text-align:center;font-size:7pt;">N'oubliez de cliquer de temps en temps sur les pubs pour faire vivre le site...<br />

<!-- début du code banner -->
<!-- <IFRAME src="http://www.misterbot.com/autopub/scriptbanner-dh3.php?idsite=11526&adult=no" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="NO" width="468" height="64"></IFRAME> -->
<!-- fin du code banner --><br /><br />

<!--Code à insérer CibleClick : MILIMEL --><a href="http://www.cibleclick.com/cibles/clicks/symp.cfm?site_id=219426917&friend_id=443102863&banniere_id=688" target="_blank"><img src=http://www.cibleclick.com/cibles/banniere/symp.cfm?site_id=219426917&friend_id=443102863&banniere_id=688  border=0 alt=></a><!--Code à insérer CibleClick : MILIMEL --><br /><br />
<!--Code à insérer CibleClick : 2xMoinsCher.com --><a href="http://www.cibleclick.com/cibles/clicks/symp.cfm?site_id=427033252&friend_id=443102863&banniere_id=3928" target="_blank"><img src=http://www.cibleclick.com/cibles/banniere/symp.cfm?site_id=427033252&friend_id=443102863&banniere_id=3928  border=0 alt=></a><!--Code à insérer CibleClick : 2xMoinsCher.com -->

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
</form>

</body>
</html>