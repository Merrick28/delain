<?php 
//include "../connexion.php";
include "verif_connexion.php";
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif"">
<?php include "../jeu_test/tab_haut.php";
?>
<form ENCTYPE="multipart/form-data" name="test" action="valide_upload.php" method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2560000">
<input type="hidden" name="suppr" value="0">
<table><tr><td>
<INPUT NAME="avatar" TYPE="file">
</td></tr>
<tr><td><center><input type="submit" value="Enregistrer ce codument" class="test"></td></tr>
</table>
</form>
<?php 
include "../jeu_test/tab_bas.php";
?>
</body>
</html>