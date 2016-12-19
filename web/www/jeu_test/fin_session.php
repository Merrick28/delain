<html>

<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php include "../includes/classes.php";
page_open(array("sess" => "My_Session"));
$sess->delete();
include "tab_haut.php";
$temps = $db->getparm_n(12);
echo("<p>Votre session a expiré. <br>");
echo("Pour soulager la charge serveur, les sessions sont limitées à 15 minutes.<br>");
echo("Pour vous reconnecter, vous pouvez cliquer <a href=\"../index.php\"><b>ICI</b></a>");
include "tab_bas.php";
?>
</body>
</html>