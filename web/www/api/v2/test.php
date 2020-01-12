<?php
echo "toto";
$array_good = array(
    "nom"   => "fdsf",
    "force" => 12,
    "con"   => 12,
    "dex"   => 12,
    "intel" => 9,
    "voie"  => "guerrier",
    "poste" => "H",
    "race" => 1
);

$callapi = new callapi();

$montest = $callapi->call('http://172.17.0.1:9090/api/v2/compte/persos','GET','34ef18f1-b59f-4c9a-8cf5-b529207be6bf');
echo "titi";

echo "<pre>";
print_r($callapi);
echo "</pre>";