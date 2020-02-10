<?php
foreach ($_REQUEST['modif'] as $key => $val)
{
    $guilde = new guilde;
    $guilde->charge($key);
    $erreur     = 0;
    $nom_guilde = $guilde->guilde_nom;
    if (($val < -20) || ($val > 20))
    {
        $erreur = 1;
        echo "<p>Anomalie sur la guilde <strong>", $nom_guilde, "</strong>, le modificateur doit être compris entre -20 et +20 !</p>";
    }
    if ($erreur == 0)
    {
        $guilde->$champ = $val;
        $guilde->stocke();
        echo "La guilde <strong>", $nom_guilde, "</strong> a été modifiée ! <br>";
    }
}
?>
<p style="text-align:center"><a href="admin_echoppe.php">Retour ! </a>