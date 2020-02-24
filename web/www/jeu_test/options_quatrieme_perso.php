<?php
include "blocks/_header_page_jeu.php";
$compte   = $verif_connexion->compte;
$methode  = get_request_var('methode', '');
$resultat = '<div><strong>Les paramètres de votre quatrième personnage ont été mis à jour.</strong></div>';
switch ($methode)
{
	case 'modif':
        $nouveau_type = $_GET['nouveau_type'];
        if ($nouveau_type != 2)
            $nouveau_type = 1;
        $compte->compt_type_quatrieme = $nouveau_type;
        $compte->stocke();
        break;

	case 'transforme':
		$requete = "update perso set perso_pnj = 0, perso_mortel = NULL
			from perso_compte
			where perso_cod = pcompt_perso_cod
				and pcompt_compt_cod = $compt_cod
				and perso_actif = 'O'
				and perso_pnj = 2";
		$stmt = $pdo->query($requete);
	break;

	case 'limitation':
		$nouveau_type = $_GET['mortel'];
		if ($nouveau_type != 'N' && $nouveau_type != 'O')
			$nouveau_type = 'N';
			
		$requete = "update perso set perso_mortel = '$nouveau_type'
			from perso_compte
			where perso_cod = pcompt_perso_cod
				and pcompt_compt_cod = $compt_cod
				and perso_actif = 'O'
				and perso_pnj = 2";
		$stmt = $pdo->query($requete);

		if ($nouveau_type == 'N')
		{
			$requete = "update perso set perso_px = case when perso_px > 1000 then 1000 else perso_px end
				from perso_compte
				where perso_cod = pcompt_perso_cod
					and pcompt_compt_cod = $compt_cod
					and perso_actif = 'O'
					and perso_pnj = 2";
			$stmt = $pdo->query($requete);

			$requete = "update perso_competences set pcomp_modificateur = min(85, pcomp_modificateur)
				from perso
				inner join perso_compte on pcompt_perso_cod = perso_cod
				where perso_cod = pcomp_perso_cod
					and pcompt_compt_cod = $compt_cod
					and perso_actif = 'O'
					and perso_pnj = 2";
			$stmt = $pdo->query($requete);

			$requete = "select perso_niveau from perso
				inner join perso_compte on pcompt_perso_cod = perso_cod
				where pcompt_compt_cod = $compt_cod
					and perso_actif = 'O'
					and perso_pnj = 2";
			$stmt = $pdo->query($requete);
			if($result = $stmt->fetch())
			{
				if ($result['perso_niveau'] > 14)
				{
					$requete = "select supprimer_ameliorations_perso(perso_cod)
						from perso
						inner join perso_compte on pcompt_perso_cod = perso_cod
						where pcompt_compt_cod = $compt_cod
							and perso_actif = 'O'
							and perso_pnj = 2";
					$stmt = $pdo->query($requete);
				}
			}
		}
	break;

	default:
		$resultat = '';
	break;
}

$requete = "select possede_4e_perso(compt_cod) as possede, autorise_4e_perso(compt_quatre_perso, compt_dcreat) as autorise, compt_type_quatrieme
	from compte
	where compt_cod = $compt_cod";

$stmt = $pdo->query($requete);
$result = $stmt->fetch();

$quatrieme_possible = ($result['autorise'] == 't');
$quatrieme_monstre = ($result['compt_type_quatrieme'] == 2);
$quatrieme_existant = ($result['possede'] == 't');

// Analyse des personnages du compte
$requete = "select perso_type_perso, perso_pnj, count(*) as nombre
	from perso_compte
	inner join perso on perso_cod = pcompt_perso_cod
	where pcompt_compt_cod = $compt_cod
		and perso_actif = 'O'
	group by perso_type_perso, perso_pnj
	order by perso_type_perso, perso_pnj";

$stmt = $pdo->query($requete);

// Matrice perso_type_perso / perso_pnj (la valeur contenue en est le nombre)
$persos = array(
		0 => array(0 => 0, 1 => 0, 2 => 0),
		1 => array(0 => 0, 1 => 0, 2 => 0),
		2 => array(0 => 0, 1 => 0, 2 => 0)
	);
while ($result = $stmt->fetch())
{
	$ptp = $result['perso_type_perso'];
	$pnj = $result['perso_pnj'];
	$nb = $result['nombre'];
	$persos[$ptp][$pnj] = $nb;
}

if ($quatrieme_possible)
{
	//
	//Contenu de la div de droite
	//
	$nouveau_type = ($quatrieme_monstre) ? 1 : 2;
	$type_texte = ($quatrieme_monstre) ? 'monstre' : 'aventurier';
	$contenu_page = $resultat . '<p class="titre">Options du quatrième personnage</p>';
	$contenu_page .= '<p><strong>Rappel : </strong><br />
		Vous avez la possibilité de créer un quatrième personnage car vous êtes présent sur le jeu depuis plus de deux ans.<br />
		<strong>Ceci n’est pas considéré comme un droit mais comme une responsabilité.</strong>
		<br />
		Monstre ou aventurier, le quatrième personnage est présent pour vous permettre de participer à l’animation du jeu, vous en faire découvrir de
		nouveaux aspects...<br />
		Ce nouveau personnage verra ses capacités limitées (évolution, déplacements...).<br /><br />
		Si ces règles ne vous conviennent pas, ne gardez surtout pas ce personnage, car ne pas les respecter sera forcément sanctionné !</p>
		<br /><hr />';

	$contenu_page .= "<p><strong>Type de quatrième personnage :</strong></p>";
	$contenu_page .= "<p>Vous pouvez actuellement jouer un quatrième personnage de type <strong>$type_texte</strong>.</p>";
	$contenu_page .= "<p><a href='?nouveau_type=$nouveau_type&methode=modif'>Changer ?</a></p>";

	if ($quatrieme_existant)
		$contenu_page .= '<br /><p><strong>Attention</strong> : vous avez actuellement un quatrième personnage. Les modifications ne seront effectives
			que lors d’un changement de quatrième personnage (mort du monstre ou suppression de l’aventurier)</p>';

	// Le compte possède moins de 3 persos standards, et un quatrième perso : il peut transformer ce dernier en perso standard.
	if ($persos[1][0] < 3 && $persos[1][2] == 1)
	{
		$requete = "select perso_nom
			from perso_compte
			inner join perso on perso_cod = pcompt_perso_cod
			where pcompt_compt_cod = $compt_cod
				and perso_actif = 'O'
				and perso_pnj = 2";
		$stmt = $pdo->query($requete);
		$result = $stmt->fetch();
		$nom_du_perso = $result['perso_nom'];
		$contenu_page .= "<hr /><p><strong>Transformation de personnage : </strong><br />
			Vous avez un quatrième personnage, $nom_du_perso, mais moins de trois personnages principaux.<br />
			Vous pouvez transformer votre quatrième personnage en personnage standard, lui enlevant ainsi les restrictions qui sont les siennes.<br />
			Attention, la manœuvre opposée n’est pas possible ! Cette décision est donc irréversible.</p>
			<br /><br />
			<p><a href='?methode=transforme'>Oui, je souhaite transformer $nom_du_perso en personnage standard !</a></p>";
	}

	// Le compte possède un 4e perso : on peut choisir s’il est bridé ou mortel.
	if ($persos[1][2] == 1)
	{
		$requete = "select perso_nom, perso_mortel
			from perso_compte
			inner join perso on perso_cod = pcompt_perso_cod
			where pcompt_compt_cod = $compt_cod
				and perso_actif = 'O'
				and perso_pnj = 2";
		$stmt = $pdo->query($requete);
		$result = $stmt->fetch();
		$nom_du_perso = $result['perso_nom'];
		$perso_mortel = $result['perso_mortel'];
		$texte_mortel = 'indéfini';
		if ($perso_mortel == 'O') $texte_mortel = 'mortel';
		if ($perso_mortel == 'N') $texte_mortel = 'bridé';
		$contenu_page .= "<hr /><p><strong>Devenir du 4e personnage : </strong><br />
			Vous avez un quatrième personnage, $nom_du_perso.<br />
			Afin d’éviter que ce personnage ne devienne trop puissant et déséquilibre les étages supérieurs, un choix est demandé :<br />
			- soit votre personnage vivra et mourra de façon classique, mais <strong>ses PX seront limités à 1000, et ses compétences à 85</strong><br />
			- soit il ne sera pas limité, mais <strong>deviendra mortel, de sorte que sa prochaine mort sera définitive.</strong><br />
			
			Ce choix est obligatoire à partir du moment où le personnage atteint les seuils fixés : le jeu vous empêchera alors de jouer ce personnage
			dans l’attente de votre décision.<br />
			Il est réversible à tout moment via cette même page sauf, bien sûr, si votre personnage mourrait en mode « mortel ».<br /><br />
			<p><strong>Attention</strong> Si vous êtes de niveau égal ou supérieur à 15, brider signifiera perdre des améliorations de niveau. Le système ne pouvant pas deviner à votre place lesquelles perdre, elle seront toutes réinitialisées et vous devrez repasser vos niveaux.</p><br />
			Pour le moment, il est en mode « $texte_mortel ».<br />
			Vous souhaitez :<br />
			<p> - <a href='?methode=limitation&mortel=N'>brider l’évolution de $nom_du_perso.</a></p>
			<p> - <a href='?methode=limitation&mortel=O'>rendre $nom_du_perso mortel.</a></p>";
	}
}
else
{
    $contenu_page .= 'Erreur ! Cette page ne vous concerne pas.';
}
include "blocks/_footer_page_jeu.php";
