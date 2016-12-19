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
$type2 = addslashes($type);
$question2 = addslashes($question);
$reponse2 = addslashes($reponse);
$req_ins = "insert into faq (faq_tfaq_cod,faq_question,faq_reponse) values ($type2,'$question2','$reponse2')";
$res_ins = pg_exec($dbconnect,$req_ins);
if (!$res_ins)
{
	echo("<p>Une anomalie est survenue !");
}
else
{
	echo("<p>La nouvelle question/réponse est entrée !");
}
echo("<p><a href=\"index.php\">Retour à la page d'accueil opérateurs</a>");
echo("<p><a href=\"gestion_faq.php\">Retour à la page de gestion de la faq</a>");
include "../jeu/tab_bas.php";
?>
</body>
</html>

