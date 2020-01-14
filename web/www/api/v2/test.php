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
echo "tata";
$montest = $callapi->call('http://172.17.0.1:9090/api/v2/perso','POST','0b248457-8942-45a2-85c1-d67b4557fa72',
                         $array_good);
echo "titi";


print_r($callapi);