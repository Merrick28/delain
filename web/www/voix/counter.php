<?php 
include "../includes/classes.php";

$req = "UPDATE hits_voix SET counter=counter + 1 WHERE page = '$PHP_SELF'";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
    $pdo->query("INSERT INTO hits_voix VALUES ('$PHP_SELF', 1)");
?>