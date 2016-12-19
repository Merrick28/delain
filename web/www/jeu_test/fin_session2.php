<html>

<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php include "tab_haut.php";
if (!isset($motif))
    $motif = 'Erreur technique : pour une raison indéterminée, votre session s’est arrêtée.';

echo "<p>" , $motif , "<br>";
echo("Pour vous reconnecter, vous pouvez cliquer <a href=\"../index.php\"><b>ICI</b></a>");
include "tab_bas.php";
?>
</body>
</html>