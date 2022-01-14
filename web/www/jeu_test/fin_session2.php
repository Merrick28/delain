<!DOCTYPE html>
<html>

<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="../css/delain.css" rel="stylesheet">
<head>
    <title>Fin de session</title>
</head>
<body background="../images/fond5.gif">
<div class="bordiv">
    <?php

    if (!isset($motif))
        $motif = 'Erreur technique : pour une raison indéterminée, votre session s’est arrêtée.';

    echo "<p>", $motif, "<br>";
    echo "Pour vous reconnecter, vous pouvez cliquer <a href=\"../index.php\"><strong>ICI</strong></a>" ;
    ?>
</div>
</body>
</html>