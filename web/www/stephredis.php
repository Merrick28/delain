<?php
$redis = new myredis();
echo "<pre>";
print_r($redis->listallkeys());
echo "</pre>";