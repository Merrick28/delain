<?php
$req      =
    "select pos_cod from positions where pos_x = :x and pos_y = :y and pos_etage = :etage ";
$stmt     = $pdo->prepare($req);
$stmt     = $pdo->execute(array(":x"     => $_REQUEST['pos_x'],
                                ":y"     => $_REQUEST['pos_y'],
                                ":etage" => $_REQUEST['etage']), $stmt);
if(!$result = $stmt->fetch())
{
    echo "<p>Aucune position trouvée à ces coordonnées.<br /></p>";
    $err_depl = 1;
}

$pos_cod = $result['pos_cod'];
$req     = "select mur_pos_cod from murs where mur_pos_cod = :pos_cod ";
$stmt    = $pdo->prepare($req);
$stmt    = $pdo->execute(array(":pos_cod" => $pos_cod), $stmt);
if ($result = $stmt->fetch())
{
    echo "<p>Impossible de poser le monstre : un mur en destination.<br /></p>";
    $err_depl = 1;
}
