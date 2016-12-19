<?php 
include "/home/sdewitte/public_html/includes/classes.php";
page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<body background="images/fond5.gif">
<?php 
include "jeu_test/tab_haut.php";
?>
<form name="rech" method="post" action="valide_rech_perso.php">
<p>Entrez le nom du perso à trouver : <input type="text" name="nom">
<p><center><input type="submit" value="Rechercher !" class="test"></center>
</form>
<p style="text-align:right"><a href="javascript:window.close();">Fermer cette fenêtre</a>
<?php 
include "jeu_test/tab_bas.php";
?>
