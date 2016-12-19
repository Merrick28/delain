<?php 
$pseudo=urlencode($_POST['nom']);
$pays_autre=urlencode($pays_autre);
$ville=urlencode($ville);
$mail=urlencode($mail);
$lien = "http://mappemonde.net/carte/bdd/bdd_carte.php?base=Delain&action=new&pseudo=$pseudo&password_md5=" . md5($pass1) . "&password2_md5=" . md5($pass2) . "&ville=$ville&pays=$pays&departement=$departement&continent=$continent&pays_autre=$pays_autre&mail=$mail&urldecode=1&redirection=http://www.jdr-delain.net/geo3.php";
?>
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="style.php">
<link rel="stylesheet" type="text/css" href="style.css">
<META HTTP-EQUIV=Refresh CONTENT="3; URL=<?php echo $lien;?>">
</head>
<body>
<?php include 'jeu/tableau.php';
Titre('Où êtes vous ?')?>
<div class="barrLbord"><div class="barrRbord">
<p class="texteNorm">


<p style="text-align:center;"><a href="<?php  echo $lien; ?>">Cliquez ici si vous n'êtes pas redirigé</a></p>
</p>
</div></div>
<?php Bordure_Tab()?>
</div>
</body>
</html>