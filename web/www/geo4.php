<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include 'jeu/tableau.php';
Titre('Où êtes vous ?')?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">
Vous pouvez vous inscrire pour nous signaler où vous vous trouvez :
<form name="mappemonde" method="post" action="geo5.php">
<table border="0"><tr><td><table>
<tr><td class="soustitre2"><p>Pseudo : </td><td><input type="text" name="nom"></td></tr>
<tr><td class="soustitre2"><p>Mot de passe : </td><td><input type="password" name="pass1"></td></tr></table></td></tr>
</table>
<center><input type="submit" class="test" value="Valider !"></center>
</form>
</p>
<p class="texteNorm">
Vous pouvez aussi  <a href="http://mappemonde.net/bdd/Delain/monde.html" rel="external">cliquer ici</a> pour voir où se trouvent les joueurs de Delain !


</p>
</div></div>
<?php Bordure_Tab()?>
</div>
</body>
</html>