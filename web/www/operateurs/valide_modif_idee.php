<?php 
include "../includes/classes.php";
$db = new base_delain;
?>
<html>
<head>
<title>Les souterrains de Delain : partie opérateurs</title>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
</head>
<body background="../images/fond5.gif">
<?php 
$user = $_SERVER["REMOTE_USER"];
include "../jeu/tab_haut.php";
$req_ins = "update idees set idee_new= '$nouveau',idee_domaine= e'".pg_escape_string($domaine)."',idee_nom = e'".pg_escape_string($nom)."',idee_lien='$lien',idee_priorite='$priorite',idee_etat='$etat',idee_comment=e'".pg_escape_string($commentaire)."',idee_avancement='$avancement',idee_date_maj = now() where idee_cod = $idee ";
$db->query($req_ins);
echo "<p>Idée modifiée ";
echo("<p><a href=\"index.php\">Retour à la page d'accueil opérateurs</a>");
echo("<p><a href=\"gestion_idee.php\">Retour à la page de gestion des idées</a>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

