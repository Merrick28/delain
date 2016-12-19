<?php 
include "../connexion.php";
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
$req_ins = "update regles set regle_titre='" .addslashes($titre) . "',regle_texte='" . addslashes($texte) . "' where regle_cod = $code ";
$res_ins = pg_exec($dbconnect,$req_ins);
if (!$res_ins)
{
	echo("<p>Une anomalie est survenue !");
}
else
{
	echo("<p>Règle modifiée !");
}
echo("<p><a href=\"index.php\">Retour à la page d'accueil opérateurs</a>");
echo("<p><a href=\"gestion_regles.php\">Retour à la page de gestion des règles</a>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

