<?php 
$pseudo=urlencode($_POST['nom']);
$formulaire = implode("", file("http://mappemonde.net/carte/bdd/edition.php?base=delain&include=1&pseudo_cookie=" . $pseudo . "&mdp_cookie_md5=" . md5($pass1) . "&urldecode=1"));
?>
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
<form name="mappemonde" method="post" action="geo6.php">
<input type="hidden" name="pseudo" value="<?php echo $pseudo;?>">
<input type="hidden" name="md5_pass" value="<?php echo  md5($pass1);?>">
<?php  echo $formulaire; ?>
<center><input type="submit" class="test" value="Valider !"></center>
</form>
</p>
</p>
</div></div>
<?php Bordure_Tab()?>
</div>
</body>
</html>
