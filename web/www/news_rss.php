<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head><title>Les news du site</title>
<meta name="keywords" content="jeu,rôle,delain,ligne,gratuit,multi-joueur">
<meta name="description" content="Les souterrains de Delain, jeu de rôle gratuit en ligne">
<html>  
<?php 
if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		if ($_SERVER['HTTP_X_FORWARDED_HOST'] != 'www.jdr-delain.net')
		{
			die("L'accès du jeu se fait uniquement par l'adresse <a href=\"http://www.jdr-delain.net\">www.jdr-delain.net</a>");
		}
	}
if (!@include "../includes/img_pack.php")
     include "includes/img_pack.php";
include "includes/classes.php";
?>
<head>
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
</head>
<body>

<?php include 'jeu/tableau.php';
Titre('Les souterrains en XML')?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">
Afin de suivre plus facilement les nouvelles des souterrains, voici deux flux RSS mis à votre disposition :<br>
<a href="http://www.jdr-delain.net/rss.php"><img src="images/rss.gif" border="0"> Les news de la page d'accueil</a><br>
<a href="http://www.jdr-delain.net/forum/rss.php"><img src="images/rss.gif" border="0"> Les forums</a>.<br>
Vous pouvez utiliser ces liens avec tout aggrégateur de flux (par exemple <a href="http://www.feedreader.com" target="_blank">Feedreader</a>).


</p>
</div></div>
<?php 
Bordure_Tab();
?>

</body>
</html>