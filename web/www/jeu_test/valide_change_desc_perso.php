<?php
include "blocks/_header_page_jeu.php";
ob_start();
$erreur  = 0;
$corps   = htmlspecialchars($corps);
$corps   = str_replace(";",chr(127),$corps);
$corps   = str_replace("\\"," ",$corps);
$corps   = pg_escape_string($corps);
$methode = get_request_var('methode', 'desc');
	// 
	// phrase à modifier par la suite en fonction des alignements
	//
	switch ($methode)
	{
		case "desc":
			if (strlen($corps)>=254)
			{
				echo "<p>Votre description est trop longue (max 254 caractères), merci de la raccourcir !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$req_desc = "update perso set perso_description = e'$corps' where perso_cod = $perso_cod ";
				$stmt = $pdo->query($req_desc);
				echo("<p>La description de votre personnage est enregistrée !");
			}
			echo("<p><a href=\"change_profil_perso.php\">Retour !</a>");
		break;
		case "desc_long";
			if (strlen($corps)>=10000)
			{
				echo "<p>Votre description est trop longue (max 10000 caractères), merci de la raccourcir !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$req_desc = "update perso set perso_desc_long = e'$corps' where perso_cod = $perso_cod ";
				$stmt = $pdo->query($req_desc);
				echo("<p>La description longue de votre personnage est enregistrée !");
			}
			echo("<p><a href=\"change_profil_perso.php\">Retour !</a>");
		break;
	}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
