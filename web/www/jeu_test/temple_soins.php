<?php
include "blocks/_header_page_jeu.php";
ob_start();

// on regarde si le joueur est bien sur un dispensaire
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n’êtes pas sur un dispensaire !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 2)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n’êtes pas sur un dispensaire !!!");
	}
}

if ($erreur == 0)
{
	$erreur_soins = 0;
	$tab_temple = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_temple['nom'];
	$type_lieu = $tab_temple['libelle'];
	echo("<p><img src=\"../images/temple.gif\"><strong>$nom_lieu</strong> - $type_lieu");
	$pv[1] = 5;
	$pv[2] = 10;
	$pv[3] = 20;
	$cout[1] = 20;
	$cout[2] = 35;
	$cout[3] = 60;
	$req_soins = "select temple_soins($perso_cod,$pv[$soins],$cout[$soins]) as soins";
	$db->query($req_soins);
	$db->next_record();
	if ($db->f("soins") == 0)
	{
		if ($soin == "soin_male")
		{
		    echo("<p>La jeune femme voluptueuse soigne votre corps avec douceur, durant un long moment qui vous semble un morceau de paradis dans l’enfer de ces Souterrains. ");
		}
		else
		{
	        echo("<p>Le jeune homme de ses grandes mains vous soigne pourtant avec douceur, durant un long moment qui vous semble un morceau de paradis dans l’enfer de ces Souterrains. ");
		}
	}
	else
	{
		printf("<p>Une anomalie est survenue : <strong>%s</strong>",$db->f("soins"));
	}

		
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
