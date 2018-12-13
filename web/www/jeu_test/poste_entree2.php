<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

// on regarde si le joueur est bien sur une banque
$erreur = 0;
$db = new base_delain;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un poste d'entrée !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 23 and $tab_lieu['type_lieu'] != 27)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur poste d'entrée !!!");
	}
}
if ($erreur == 0)
{
	?>
	<p><strong>Un homme au visage buriné, marqué par ce qu’il a vu et ce qu’il a vécu vient vers toi : </strong><br><br> 

<p><i>Salut aventurier !  Tu es venu ici afin de faire fortune et de gagner gloire et renommée ?  Tu es au bon endroit !  En ces lieux tu pourras combattre les monstres de malkiar et les vils serviteurs du roi Hormandre corrompu !  <br><br> 

Un grand et fameux destin t'attend, et tu auras l'occasion de prouver ta valeur à moult reprises ! <br><br> 

Laisse-moi te mettre en garde contre deux dangers particuliers. <br><br> 

Tout d'abord, les <strong>portails démoniaques</strong>. Ces portails restent un temps à un endroit, puis se déplacent ailleurs. C'est par eux que se déversent les créatures de Malkiar. Plus tu t'en approches, plus le danger est grand, car un nombre inconnu de ces créatures peuvent y apparaître soudainement, à tout moment.<br> <br> 

En second lieu, il te faudra te méfier des brigands et des racketteurs qui assaillent les vaillants aventuriers tels que toi !  

Ici la corruption est partout.  Le crime aussi.  Il te faudra particulièrement faire attention aux sbires d’Hormandre.  Miliciens verreux, marchands acariâtres ils ne servent qu’eux même et ne songent qu’à dépouiller les aventuriers pour leur roi.   

Heureusement ici tu es en sécurité.  La résistance libre est parvenue à reconquérir ce lieu.  Nous résistons tant bien que mal aux assauts des commandos d’extermination de la milice.  Heureusement nous sommes soutenus dans notre juste lutte par un roi épris de justice et de libertés : Salm’o’rv de Kranaal. Il fait tout son possible pour nous aider.  

Si tu désires te mettre toi aussi au service de la résistance je te conseille de te rendre auprès de la guilde qui a été créée à cet effet : <a href="guilde.php">liste des Guildes</a>.  

Tu pourras trouver là bas de nombreuses guildes.  Attention suit ton cœur et tu ne pourras te tromper.  La justice brille dans nos cœurs tel le scintillement de l’étoile du nord.  Tu ne pourras te tromper.  

Lorsque tu auras gagné tes premiers brouzoufs tu auras la possibilité de revenir ici afin d’acheter quelques petits paquets que nous mettons à ta disposition.  Les résistants t’achèterons ces paquets lors de rendez vous convenus dans les bars et les tavernes des souterrains.  Il te faudra bien faire attention à éviter les miliciens qui n’auront à l’esprit que de te les voler pour en tirer tout le profit !  Tu devras ruser et être prudent.   Leurs crimes et leurs vilénies sont sans limites !  

<br><br>

	<hr>
	<a href="action.php?methode=achat_objet&objet=1">Prendre un fut de bière pour l’amener vers l’auberge ?</a><br>
	<a href="action.php?methode=achat_objet&objet=2">Prendre un petit paquet brun pour l’amener vers l’auberge ? (lancez-vous dans la libre entreprise pour 2000 brouzoufs)</a><br>
<?php 
}

			//On insère toute la partie qui concerne les quêtes des postes de garde
			include "quete.php";
				echo $sortie_quete;

?>
