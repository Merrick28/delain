<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
    <title>Entrée</title>
</head>
<body background="../images/fond5.gif">
<?php
$perso    = new perso;
$perso    = $verif_connexion->perso;
$tab_lieu = $perso->get_lieu();
printf("<p><strong>%s</strong> - %s", $tab_lieu['lieu']->lieu_nom,
       $tab_lieu['lieu_type']->tlieu_libelle);
?>
</body>
</html>
