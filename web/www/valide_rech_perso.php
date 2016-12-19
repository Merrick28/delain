<?php 
require "includes/classes.php";
page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
$db = new base_delain;
?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<body background="images/fond5.gif">
<?php 
include "jeu_test/tab_haut.php";
$nom = pg_escape_string($_POST['nom']);
$req = "select f_cherche_perso('$nom') as resultat ";
$db->query($req);
$db->next_record();
if ($db->f("resultat") == -1)
{
	echo "<p>Aucun perso trouvé pour ce nom !";
}
else
{
	echo "<p>Perso trouvé : numéro " . $db->f("resultat");
}
?>
<p style="text-align:right"><a href="javascript:window.close();">Fermer cette fenêtre</a>
<?php 
include "jeu_test/tab_bas.php";
?>
