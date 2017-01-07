<?php 
trigger_error('hstart');
header('Content-type: text/html; charset=utf-8');
function register_globals($order = 'egpcs')
{
	// define a subroutine
	if(!function_exists('register_global_array'))
	{
		function register_global_array(array $superglobal)
		{
			foreach($superglobal as $varname => $value)
			{
				global $$varname;
				$$varname = $value;
			}
		}
	}
	
	$order = explode("\r\n", trim(chunk_split($order, 1)));
	foreach($order as $k)
	{
		switch(strtolower($k))
		{
			case 'e':    register_global_array($_ENV);       break;
			case 'g':    register_global_array($_GET);       break;
			case 'p':    register_global_array($_POST);      break;
			case 'c':    register_global_array($_COOKIE);    break;
			case 's':    register_global_array($_SERVER);    break;
		}
	}
}
 
/**
 * Undo register_globals
 * @author Ruquay K Calloway
 * @link hxxp://www.php.net/manual/en/security.globals.php#82213
 */
function unregister_globals() {
	if (ini_get('register_globals')) {
		$array = array('_REQUEST', '_SESSION', '_SERVER', '_ENV', '_FILES');
		foreach ($array as $value) {
			foreach ($GLOBALS[$value] as $key => $var) {
				if ($var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}
}
register_globals();
$filename = G_CHE . 'stop_jeu';
//require '/home/delain/public_html/www/includes/filtrage_ip.php';
if (file_exists($filename) && $_SERVER["REMOTE_ADDR"] != '195.37.61.152') {
	//echo "Le jeu est actuellement arrêté pour quelques minutes. <hr>";
	include G_CHE . 'stop_jeu';
	die();
}
require 'prepend.php';
// chemins du jeu
if(isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] = 'on'))
	$type_flux = 'https://';
else
	$type_flux = 'http://';

// on commence la temporisation de sortie
ob_start();
