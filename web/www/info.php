<?php 
if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] = 'on')
	echo "HTTPS";
else
	echo "PAS HTTPS";
echo "<hr>";
define('NOGOOGLE',1);
phpinfo();
?>
