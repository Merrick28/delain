<?php 
$methode = $_POST['methode'];
switch($methode)
{
	case "cree":
		$chemin = $_POST['chemin'];
		setcookie(img_path,$chemin,time()+31536000,"/");
		setcookie(img_path,$chemin,time()+31536000,"/jeu/");
		$_COOKIE['img_path'] = $chemin;
		echo "<!-- cree -->";
		break;
	case "efface":
		$_COOKIE['img_path'] = '';
		setcookie(img_path,"",time()-31536000,"/");
		setcookie(img_path,"",time()-31536000,"/jeu/");
		echo "<!-- efface -->";
		break;
}
?>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
<META NAME="Pragma" CONTENT="No-Cache"> 
<META NAME="Cache-Control" CONTENT="No"> 
<META HTTP-EQUIV="expires" CONTENT="0">
</head>
<body background="<?php  echo G_IMAGES; ?>fond5.gif">
<?php 
include "jeu_test/tab_haut.php";
switch($methode)
{
	case "cree":
		echo "<p>Le pack est maintenant actif.";
		break;
	case "efface":
		echo "<p>Le pack est maintenant inactif.";
		break;
}
include "jeu_test/tab_bas.php";
?>