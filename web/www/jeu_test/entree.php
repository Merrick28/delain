<?php
include "verif_connexion.php";
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
    <title>Entrée</title>
</head>
<body background="../images/fond5.gif">
<?php $tab_lieu = $db->get_lieu($perso_cod);
printf("<p><strong>%s</strong> - %s", $tab_lieu['nom'], $tab_lieu['libelle']);
?>
</body>
</html>
