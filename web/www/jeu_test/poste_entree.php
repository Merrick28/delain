<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";

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
	if ($tab_lieu['type_lieu'] != 15 and $tab_lieu['type_lieu'] != 23)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur poste d'entrée !!!");
	}
}
if ($erreur == 0)
{
	?>
	<p><strong>Un jeune capitaine idéaliste vient vers vous : </strong><br><br>
	<p><em>Bienvenue à toi, Noble aventurier venu te consacrer à la lutte contre le Démon Malkiar et ses sombres engeances. Si seulement les Royaumes comptaientplus d'âmes courageuses comme la tienne, nous serions déjà libérés de cette sombre menace ! <br><br>
	Un grand et fameux destin t'attend, et tu auras l'occasion de prouver ta valeur à moult reprises. <br><br>
	Laisse-moi te mettre en garde contre deux dangers particuliers. <br><br>
	Tout d'abord, les <strong>portails démoniaques</strong>. Ces portails restent un temps à un endroit, puis se déplacent ailleurs. C'est par eux que se déversent les créatures de Malkiar. Plus tu t'en approches, plus le danger est grand, car un nombre inconnu de ces créatures peuvent y apparaître soudainement, à tout moment.<br> <br>
	En second lieu, il te faudra te méfier de certains aventuriers eux-mêmes. C'est hélas un témoignage de la puissance du Démon. Certains héros ont succombé à sa corruption. Les uns sont tombé entièrement sous sa coupe, et ne sont pas différents des « monstres ». Les autres sont devenus des criminels, qui n'hésitent pas à assassiner sans vergogne leurs compagnons. Leur <strong>karma</strong> te permet souvent de les repérer. <br><br>
	L'union fait la force, même pour une âme forte comme la tienne. Peut-être devrais-tu jeter un oeil à la <a href="guilde.php">liste des Guildes</a>. Elles regroupent des aventuriers partageant les mêmes idéaux, et l'une d'entre elles te conviendra sans doute. Même si tu ne rejoins pas leurs rangs, tu peux toujours leur demander conseils et assistance. <br><br>
	
	Mais je ne te retiens pas plus longtemps. Va vers ton glorieux destin !</em>
	<hr>
	<a href="action.php?methode=achat_objet&objet=1">Prendre un fut de bière pour l'amener vers l'auberge ?</a><br>
<?php 
}
if ($db->is_milice($perso_cod) == 1)
{
	echo "<p><a href=\"milice_tel.php\">Se téléporter vers un autre lieu ? </a>";
}
			//On insère toute la partie qui concerne les quêtes des postes de garde
			include "quete.php";
				echo $sortie_quete;

?>
