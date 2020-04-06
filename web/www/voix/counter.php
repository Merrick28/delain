<?php
$pdo = new bddpdo();

$req  = "UPDATE hits_voix SET counter=counter + 1 WHERE page = :page";
$stmt = $pdo->prepare($req);
$stmt = $pdo->execute(array(":page" => $_SERVER['PHP_SELF']), $stmt);
if ($stmt->rowCount() == 0)
{
    $pdo->prepare("INSERT INTO hits_voix VALUES (:page, 1)");
    $stmt = $pdo->execute(array(":page" => $_SERVER['PHP_SELF']), $stmt);
}

