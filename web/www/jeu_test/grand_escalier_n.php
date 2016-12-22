<?php 
if(!defined("APPEL"))
	die("Erreur d’appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

$erreur = 0;
// on regarde si le joueur est bien sur un escalier
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n’êtes pas sur un escalier !!!</p>");
	$erreur = 1;
}
$tab_lieu = $db->get_lieu($perso_cod);
if ($erreur == 0)
{
	if ($tab_lieu['type_lieu'] != 16)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n’êtes pas sur un escalier !!!</p>");
	}
	else if ($db->compte_objet($perso_cod, 86) + $db->compte_objet($perso_cod, 87) + $db->compte_objet($perso_cod, 88) > 0)
	{
		echo "<p>Vous ne pouvez pas prendre un escalier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
		$erreur = 1;
	}
}

if ($erreur == 0)
{
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	$lieu = $tab_lieu['lieu_cod'];

	echo("<p><b>$nom_lieu</b></p><p>$desc_lieu</p>");

	// Animation ponctuelle : désactivation du GE du -3 / -8 pour janvier 2013
	$jour = getdate();
	if ($lieu == 58003 && $jour['yday'] <= 45 && $jour['year'] == 2013)
	{
		echo "<hr /><p>Il paraît que ceci est censé être un grand escalier descendant. Mais ne voyez qu’un immense... truc...</p>";
		echo "<p>En vous approchant, vous constatez que le « truc » en question est en fait une montagne de muscles ! Un Ogre bloque l’accès à l’escalier.</p>";
		echo "<p>Il gueule à qui veut l’entendre :</p><br />";
		echo "<p>Ogres ont écroulé l’escalier !<br />
			Fini de voler la nourriture les aventuriers !!!</p><br />

			<p>Ogres ont conquis la forteresse !<br />
			À nous les petits grassouillets qui restent !!!</p><br />

			<p>Ogres ont conquis la Taverne !<br />
			À nous le garde-manger !!!</p><br />

			<p>Ogres ont conquis les temples, y sont plus protégés !<br />
			À nous le vin de cérémonie !!!</p><br />

			<p>Ogres ont conquis le Bâtiment administratif !<br />
			À nous les chocolats d’Hormand’ !!!</p><br />

			<p>Ogres enfin tranquilles chez eux !<br />
			C’est nous les maîtres-queue !!!</p>";
	}
	else
	{
		// on active pour le retour
		$req = "select pge_perso_cod from perso_grand_escalier where pge_perso_cod = $perso_cod ";
		$req = $req . "and pge_lieu_cod = $lieu ";
		$db->query($req);
		if ($db->nf() == 0)
		{
			echo "<p>Il vous est impossible de prendre cet escalier dans ce sens. Vous devez d'abord le prendre en remontant.</p>";
			$erreur = 1;
		}
		if ($erreur == 0)
		{
			echo("<p><a href=\"valide_grand_escalier_a.php\">Prendre cet escalier ! (" . $db->getparm_n(43) . " PA)</a></p>");
		}
	}
}
echo '<hr>Aux heures de sommeil de server master mortes pour la cause, la nation delainnienne reconnaissante : <br>
	18/09/2003<br>
	26/12/2005<br>
	27/01/2005<br>
	17/07/2005<br>
	25 et 26/11/2005<br>
	31/01/2006<br>
	Et tant d’autres dont on a perdu le compte...';
