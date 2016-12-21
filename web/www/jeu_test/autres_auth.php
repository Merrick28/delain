<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
if(isset($action))
{
	switch($action)
	{
		case "assoc_google":
			$req = "update compte set compt_google = '" . $id . "' where compt_cod = " . $compt_cod;
			$db->query($req);
			$contenu_page_temp .= "Association faite avec le compte google.<br />";
			break;
		case "desassoc_google":
			$req = "update compte set compt_google = '' where compt_cod = " . $compt_cod;
			$db->query($req);
			break;		
	}
}
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
/**********/
/* GOOGLE */
/**********/
$contenu_page .= $contenu_page_temp;
$contenu_page .= '<div id="google">';
if(!$google_auth)
{
	try 
	{
		$openid = new LightOpenID('www.jdr-delain.net');
		if(!$openid->mode) 
		{
		   if(isset($_GET['login'])) 
		   {
		       $openid->identity = 'https://www.google.com/accounts/o8/id';
	          $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
	          header('Location: ' . $openid->authUrl());
	      }
			$contenu_page .= 'Vous n\'êtes pas authentifié par Google.<br /><a href="' . $PHp_SELF . '?login">Login with Google</a>';
		}
		elseif($openid->mode == 'cancel') 
		{
	   	$contenu_page .= 'User has canceled authentication!';
	   }
	   else
	   {
	   	if($openid->validate())
	      {
	 			$identity = $openid->identity;
	 			
	         $attributes = $openid->getAttributes();
	         //$contenu_page .= "<pre> test";
	 			//print_r($attributes);
	 			//$contenu_page .= "</pre>";
	         $email = $attributes['contact/email'];
	         $first_name = $attributes['namePerson/first'];
	         $last_name = $attributes['namePerson/last'];
	 			$contenu_page .= "Vous êtes authentifié par Google sous le nom " . $attributes['namePerson/first'] . " " . $attributes['namePerson/last'] . "<br />";
	 			// on va meintenant voir s'il y a quelqu'un dans la base pour valider le tout
	 			$req = "select * from compte where compt_google = '" . $identity . "'";
	 			$db->query($req);
	 			if($db->nf() == 0)
 				{
	 				$contenu_page .= 'Votre compte n\'est pas associé à votre authentification Google. <a href="' . $PHP_SELF .'?action=assoc_google&id=' . $identity . '">L\'associer maintenant ?</a>';
	 			}
	 			else
	 			{
	 				$db->next_record();
	 				if($db->f('compt_cod') != $compt_cod)
	 				{
	 					$contenu_page .= 'ERREUR ! Cette id google est déjà associée à un autre compte, merci de contacter les admins pour analyse.';
	 				}
	 				else
	 				{
		 				$contenu_page .= 'Votre compte est associé à votre authentification Google. <a href="' . $PHP_SELF .'?action=desassoc_google&id=' . $identity . '">Le désassocier ?</a>';
		 			}
	 			}
			}
	      else
	      {
		      $contenu_page .= 'User ' . $openid->identity . 'has not logged in.';
	      }
		}
	}
	catch(ErrorException $e) 
	{
		$contenu_page .= $e->getMessage();
	}
}
else
{
	
	$contenu_page .= "Vous êtes authentifié par Google.<br />";
	// on va meintenant voir s'il y a quelqu'un dans la base pour valider le tout
	$req = "select * from compte where compt_google = '" . $_SESSION['google_account'] . "'";
	$db->query($req);
	if($db->nf() == 0)
	{
		$contenu_page .= 'Votre compte n\'est pas associé à votre authentification Google. <a href="' . $PHP_SELF .'?action=assoc_google&id=' . $_SESSION['google_account'] . '">L\'associer maintenant ?</a>';
	}
	else
	{
		$db->next_record();
		if($db->f('compt_cod') != $compt_cod)
		{
			$contenu_page .= 'ERREUR ! Cette id google est déjà associée à un autre compte, merci de contacter les admins pour analyse.';
		}
		else
		{
			$contenu_page .= 'Votre compte est associé à votre authentification Google. <a href="' . $PHP_SELF .'?action=desassoc_google&id=' . $_SESSION['google_account'] . '">Le désassocier ?</a><br />
			ATTENTION ! Vous êtes actuellement connecté depuis votre compte google. Cette opération va vous déloguer du jeu.';
		}
	}
}
	


$contenu_page .= '</div>';
$t->set_var('CONTENU_COLONNE_DROITE',$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
