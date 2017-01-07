<?php

echo "tata<br />";

$pdo = new bddpdo();
$req = "select * from tatat where toto = ?";
$stmt = $pdo->prepare($req);
$stmt = $pdo->execute(array('tata'),$stmt);
echo "ok";