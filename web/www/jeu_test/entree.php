<?php 
include "verif_connexion.php";
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php $tab_lieu = $db->get_lieu($perso_cod);
printf("<p><b>%s</b> - %s",$tab_lieu['nom'],$tab_lieu['libelle']);
?>
</body>
</html>
