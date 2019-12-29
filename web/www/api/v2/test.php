<?php
$callapi = new callapi();

$test = $callapi->call('http://172.17.0.1:9090/api/v2/compte','GET','ebafb63e-483e-4a04-acce-6bcbf8f0b923');

print_r($test);