<!DOCTYPE html>
<html>

<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.css" rel="stylesheet">
<head>
    <title>Fin de session</title>
</head>
<body>
<div class="bordiv">
    <?php
    include "../includes/classes.php";
    page_open(array("sess" => "My_Session"));
    $sess->delete();
    $temps = $param->getparm(12);
    echo("<p>Votre session a expiré. <br>");
    echo("Pour soulager la charge serveur, les sessions sont limitées à 15 minutes.<br>");
    echo("Pour vous reconnecter, vous pouvez cliquer <a href=\"../index.php\"><strong>ICI</strong></a>");

    ?>
</div>
</body>
</html>