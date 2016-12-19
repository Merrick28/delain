<?php session_start();

include "twitteroauth.php";

define('CONSUMER_KEY','NVdv7I5ZIePWcCqKHdveg');
define('CONSUMER_SECRET' ,'EvdrgqEmKa4YMhhOFaTUnTkgLd8k7dFRnSroLYOSI');
define("OAUTH_CALLBACK", "http://www.jdr-delain.net/facebook/callback.php");

function tweet($message,$user_token,$user_secret)
{
    require 'inc_twitter.php';
 
    $tmhOAuth = new tmhOAuth(array(
    'consumer_key' => 'NVdv7I5ZIePWcCqKHdveg',
    'consumer_secret' => 'EvdrgqEmKa4YMhhOFaTUnTkgLd8k7dFRnSroLYOSI',
    'user_token' => $user_token,
    'user_secret' => $user_secret,
    ));
 
    $tmhOAuth->request('POST', $tmhOAuth->url('statuses/update'), array(
    'status' => utf8_encode($message)
    ));
 
    if ($tmhOAuth->response['code']  == 200) {return TRUE;}
    else {return FALSE;}
}


$isLoggedOnTwitter = false;

if (!empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret'])) { 
	// On a les tokens d'accès, l'authentification est OK.

	$access_token = $_SESSION['access_token'];

	/* On créé la connexion avec twitter en donnant les tokens d'accès en paramètres.*/ 
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	
	/* On récupère les informations sur le compte twitter du visiteur */
	$twitterInfos = $connection->get('account/verify_credentials');
	$isLoggedOnTwitter = true;
}
elseif(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] === $_REQUEST['oauth_token']) {
	// Les tokens d'accès ne sont pas encore stockés, il faut vérifier l'authentification
	
	/* On créé la connexion avec twitter en donnant les tokens d'accès en paramètres.*/ 
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	
	/* On vérifie les tokens et récupère le token d'accès */
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	
	/* On stocke en session les token d'accès et on supprime ceux qui ne sont plus utiles. */
	$_SESSION['access_token'] = $access_token;
	unset($_SESSION['oauth_token']);
	unset($_SESSION['oauth_token_secret']);
	
	if (200 == $connection->http_code) {
		$twitterInfos = $connection->get('account/verify_credentials');
		$isLoggedOnTwitter = true;
	}
	else {
		$isLoggedOnTwitter = false;
	}
	
}
else {
	$isLoggedOnTwitter = false;
}
if ($isLoggedOnTwitter) {	
	/** 
	* A vous d'afficher le formulaire d'ajout du commentaire ici
	* Récupérer le pseudo: $twitterInfos->screen_name
	* Récupérer l'avatar: $twitterInfos->profile_image_url
	*/
	echo $twitterInfos->screen_name." " . $twitterInfos->id . " , vous êtes identifié avec votre compte twitter.";
	echo "<br>" . $_SESSION['access_token']['oauth_token'];
	print_r($access_token);
	//tweet('test de message depuis les souterrains de Delain',$access_token['oauth_token'],$access_token['oauth_token_secret']);
}
else {
	echo "Ident avec twitter";
}
?>
