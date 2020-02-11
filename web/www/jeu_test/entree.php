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
<?php
$perso = new perso;
$perso->charge($perso_cod);
$tab_lieu = $perso->get_lieu();
printf("<p><strong>%s</strong> - %s", $tab_lieu['lieu']->lieu_nom,
       $tab_lieu['lieu_type']->tlieu_libelle);
?>
</body>
</html>
