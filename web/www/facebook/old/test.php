<?php 
/* Déclaration des sessions */
session_name('facebookoauth');
session_start();
/* Informations sur l application */
$client_id             = '170554013031708';
$client_secret         = '85e3fed40daf9dcea8817d326042f143';
$callbackURL           = 'https://apps.facebook.com/souterrains_delain/';
$extendedPermissions   = 'publish_stream';
/* Class xHTTP */

require_once 'class.xhttp.php';

/* Demande des droits à Facebook */

if(!isset($_GET['code']))
{
	$url = 'https://graph.facebook.com/oauth/authorize?
	' . xhttp::toQueryString(array(
	'client_id'    => $client_id,
	'redirect_uri' => $callbackURL,
	'scope'        => $extendedPermissions,
	));
	/* Redirection avec code FBML */
	echo '<fb:redirect url="'.$url.'" />';
	die('');
}
/* Utilisateur autorisé */
echo "test";
if(isset($_GET['code']))
{
	$data = array();
	$data['get'] = array(
	'client_id'     => $client_id,
	'client_secret' => $client_secret,
	'code'            => $_GET['code'],
	'redirect_uri'  => $callbackURL,
	);
	/* Demande d un accès oAuth */
	$response = xhttp::fetch('https://graph.facebook.com/oauth/access_token', $data);

	/* oAuth : succès */
	if($response['successful'])
	{
		$data = xhttp::toQueryArray($response['body']);
		$_SESSION['access_token'] = $data['access_token'];
		$_SESSION['loggedin']     = true;
	}
	/* Erreur lors de la demande */
	else

	{
		print_r($response['body']);
	}
}
/* Utilisateur loggué */

if($_SESSION['loggedin'])
{
	$data = array();
	$data['get'] = array(
	'access_token'  => $_SESSION['access_token'],
	'fields' => 'id,name,accounts'
	);
	/* Récupération des informations de l utilisateur */
	$response = xhttp::fetch('https://graph.facebook.com/me', $data);

	/* Récupération OK */
	if($response['successful'])
	{
		$_SESSION['user'] = json_decode($response['body'], true);
		$_SESSION['user']['access_token'] = $_SESSION['access_token'];
		/* ICI LE CORPS DE L APPLICATION FACEBOOK */
		echo 'Bonjour '.$_SESSION['user']['name'].' !';
	}
	else

	{
		header('content-type: text/plain');
		print_r($response['body']);
	}
}
echo "test";
?>
