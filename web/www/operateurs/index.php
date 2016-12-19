<html>
<head>
<title>Les souterrains de Delain : partie opérateurs</title>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
</head>
<body background="../images/fond5.gif">
<?php 
//[REMOTE_USER]
//_SERVER["REMOTE_USER"]
$user = $_SERVER["REMOTE_USER"];
include "../jeu/tab_haut.php";
echo ("<p>Bienvenue, $user");
?>
<p><a href="gestion_faq.php">Gestion de la faq</a></p>
<p><a href="gestion_regles.php">Gestion des règles</a></p>
<p><a href="gestion_idee.php">Gestion des idées</a></p>
<p><a href="upload_doc.php">uploader un document</a></p>
<?php 
include "../jeu/tab_bas.php";
?>
</body>
</html>

