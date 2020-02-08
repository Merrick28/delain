<?php

$myredis = new myredis();

$myredis->store("macle", "mavaleur");

echo "La valeur de cle est " . $myredis->get("macle");
echo "<br >";
$allkeys = $myredis->listallkeys();
echo "<pre>";
print_r($allkeys);