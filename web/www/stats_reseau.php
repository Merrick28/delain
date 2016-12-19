<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="shortcut icon" href="http://www.jdr-delain.net/drake_head_red.ico" type="image/gif">
</head>
<body>

<?php include 'jeu/tableau.php';
Titre('Statistiques réseau')?>
<div class="barrLbord"><div class="barrRbord">

<p style="text-align:center;"><b>Traffic du routeur <a href="http://www.pyrenet.fr" target="_blank">Pyrenet</a><b><hr></p>
<p class="texteNorm" style="text-align:center">Statistiques journalières : (moyennes sur 5 minutes)<br>
<img src="genere_image.php?mode=daily"><hr></p>
<p class="texteNorm" style="text-align:center">Statistiques hebdomadaires : (moyennes sur 30 minutes)<br>
<img src="genere_image.php?mode=weekly"><hr></p>
<p class="texteNorm" style="text-align:center">Statistiques mensuelles : (moyennes sur 2 heures)<br>
<img src="genere_image.php?mode=monthly"><hr></p>
<p class="texteNorm" style="text-align:center">Statistiques annuelles : (moyennes sur 1 jour)<br>
<img src="genere_image.php?mode=yearly"></p>


</div></div>
<?php 
Bordure_Tab();
?>
</body>
</html>
