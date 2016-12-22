<?php // gestion des quêtes sur les postes de garde.

if(!defined("APPEL"))
	die("Erreur d’appel de page !");

if(!isset($methode2))
    $methode2 = "debut";
if(!isset($sortie_quete))
    $sortie_quete = "";

/* On teste si le perso est engagé dans la quête */
// Quête 17 <=> récupérer un bâton. C’est une quête d’entrée dans le jeu.

$req = "select pquete_termine from quete_perso where pquete_quete_cod = 17 and pquete_perso_cod = ".$perso_cod;
$db->query($req);
if($db->nf() == 0)
	$methode2 = "debut";
else 
{
	$db->next_record();
	$statut = $db->f("pquete_termine");
	if ($statut == 'N')
		$methode2 = "suite";
	else if ($statut == 'O')
		$methode2 = "fin";
}
switch($methode2)
{
	case "debut":
		$sortie_quete .= "<hr><br>En chemin pour découvrir les vastes contrées qui vous attendent,
			vous rencontrez un Briscard... Vieux soldat, dont les cicatrices ne laissent aucun doute
			sur sa ruse au combat, et sa capacité à tenir un assaut éprouvant.
			Celui-ci, vous voyant arriver, vous hèle au passage :
			<br><br>
			- Eh ! Toi... Oui toi, la recrue toute fraîche ! Viens voir un peu...
			<b>Dans ces Extérieurs</b>, j’aimerais que tu me trouves <b>un bâton</b>.
			Je n’ai jamais pu m’entraîner avec ce genre d’arme, et j’aimerais vérifier deux-trois choses avec.
			<br>On m’a raconté que l’on pouvait ainsi frapper à distance son adversaire, ne pas être bloqué
			par lui au corps à corps... Avec l’âge, je deviens moins souple, et maintenir une distance avec mon
			adversaire ne me déplairait pas. Qu’en dis-tu ? Si je vois que je ne suis pas fait pour cela, je te rendrai le bâton...
			<br><br>
			Tu me trouveras dans un poste de garde, à l’entrée. Si je n’y suis pas, on m’enverra un Hibou pour me
			prévenir de ton arrivée, et je rappliquerai fissa.
			<br><br>
			Mais que fais-tu donc encore ici ?!? T’es pas encore parti ? Allez dépêche-toi...";
		$req = "insert into quete_perso (pquete_quete_cod,pquete_perso_cod) values (17, $perso_cod)";
		$db->query($req);
	break;

	case "suite": /*le perso est engagé dans la quête, on teste*/
		// gobj_cod 151 <=> Bâton simple
		// gobj_cod 362 <=> Bâton lourd
		$req = "select obj_gobj_cod,perobj_obj_cod,obj_nom
			from objets,perso_objets
			where perobj_obj_cod = obj_cod
				and perobj_perso_cod = $perso_cod
				and perobj_identifie = 'O'
				and obj_gobj_cod in (151, 362) order by obj_gobj_cod";
		$db->query($req);
		if($db->nf() != 0)
		{
			$sortie_quete .= "<hr><br>Le vieux soldat saisit l’arme tendue : il fendit l’air avec le bâton,
				et réussit même à faire siffler le bois tellement il le maniait vite...
				<br><br>
				<br>- Bien... Pas mal ! Cela doit valoir le coup que je me cherche un bâton plus
				approprié que celui-ci. Il est trop simple. Merci l’ami ! Je vais de ce pas
				m’en acheter un meilleur en magasin. Maintenant, je sais que je ne ferai pas le trajet pour rien.
				<br>Tiens, voilà pour ta peine, jeune recrue !!!
				<br><br>
				Le soldat rendit le bâton, accompagné de quelques brouzoufs.
				<br><br>
				- Par contre, pour parer une attaque, ce n’est franchement pas terrible.
				Mieux vaut une épée dans ce cas-là, c’est plus efficace.<br>";
			$req = "update perso set perso_px = perso_px + 5, perso_po = perso_po + 25 where perso_cod = " . $perso_cod;
			$db->query($req);
			$req = "update quete_perso set pquete_termine = 'O' where pquete_quete_cod = 17 and pquete_perso_cod = " . $perso_cod;
			$db->query($req);
		}
		else
		{
			$sortie_quete .= "<hr><br>Le vieux soldat vous regarde d’un air intrigué.
				<br><br>- Que viens tu faire à nouveau ? Je t’ai envoyé me chercher quelque chose et tu reviens les mains vides ?
				<br>Je n’ai que faire de perdre mon temps avec des fainéants.
				<br>Oust !
				<br>";
		}
	break;

	case "fin":
	break;
}
$sortie_quete;
