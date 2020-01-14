<?php
$callapi = new callapi();
$callapi->call(API_URL . '/perso/1', 'GET', '806133f2-306b-4f3c-8209-5854718c0b5e');


echo "<pre>";
print_r($callapi);
echo "</pre>";