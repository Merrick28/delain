<?php
$param = new parametres();
// on regarde si le joueur est bien sur une banque
$erreur = 0;
$perso  = new perso;
$perso  = $verif_connexion->perso;
if (!$perso->is_lieu())
{
	echo("<p>Erreur ! Vous n'êtes pas sur un passage !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $perso->get_lieu();
	if ($tab_lieu['lieu']->lieu_tlieu_cod != 10 && $tab_lieu['lieu']->lieu_tlieu_cod!= 34) // Passage et grande porte
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un passage !!!");
	}
}

if ($erreur == 0)
{

	$nom_lieu = $tab_lieu['lieu']->lieu_nom;
	$desc_lieu = $tab_lieu['lieu']->lieu_description;
	echo("<p><b>$nom_lieu</b> - $desc_lieu ");
	echo("<p>Vous voyez un passage vers un autre lieu.");
	echo("<p><a href=\"action.php?methode=passage\">Prendre ce passage ! (" . $param->getparm(13) . " PA)</a></p>");
}


