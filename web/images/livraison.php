<?php


if(!isset($_GET['password']))
	die('Accès refusé');
if($_GET['password'] != 'TxGj4s76')
	die('Accès refusé');

$cmd = 'cd /home/delain/public_html && /usr/bin/git pull 2>&1';
exec($cmd, $output); 
$output = implode("<br />", $output) . "<br />";
echo "<h1>Sortie de commande</h1>";
echo $output;
echo "Livraison faite.";
//copy('includes/config_temp.inc','includes/config.inc');

?>
