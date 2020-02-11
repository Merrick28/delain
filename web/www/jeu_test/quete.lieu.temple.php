<?php // gestion des quêtes sur les temples.

if(!defined("APPEL"))
	die("Erreur d’appel de page !");

if(!isset($methode2))
	$methode2 = "debut";

//On sélectionne le Dieu qui est concerné (le dieu du temple dans lequel on est)
$req = "select lieu_dieu_cod, dieu_nom from perso_position
		inner join lieu_position on lpos_pos_cod = ppos_pos_cod
		inner join lieu on lieu_cod = lpos_lieu_cod
		inner join dieu on dieu_cod = lieu_dieu_cod
	where ppos_perso_cod = $perso_cod";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
$dieu = $result['lieu_dieu_cod'];
$dieu_nom = $result['dieu_nom'];

// Pour la quête de la guerre des dieux, on récupère le $quete_cod
$quete_cod = 0;
switch ($dieu)
{
	case 1:	$quete_cod = 10;	break;	// Io
	case 2:	$quete_cod =  7;	break;	// Balgur
	case 4:	$quete_cod =  8;	break;	// Elian
	default:	break;
}

switch($methode2)
{
	case "debut":
		//Quête de la guerre des Dieux.

		if ($quete_cod > 0)
		{
			// Test sur la foi du perso (pas les novices) et l’or pour payer
			$req = "select perso_po, dper_dieu_cod from perso, dieu_perso
				where perso_cod = $perso_cod
					and dper_perso_cod = $perso_cod
					and dper_niveau > 0";
			$stmt = $pdo->query($req);
			$brouzoufs = 0;
			$dieu_perso = -1;
			if($result = $stmt->fetch())
			{
				$brouzoufs = $result['perso_po'];
				$dieu_perso = $result['dper_dieu_cod'];
			}

			if ($brouzoufs > 1001 && $dieu_perso == $dieu)
			{
				$code_methode = "dieu$dieu" . "_quete$quete_cod";
				$url_methode_1 = $PHP_SELF . "?methode2=$code_methode";
				$url_methode_2 = $PHP_SELF . "?methode2=$code_methode" . '_2';

				// On regarde l’avancement du perso dans la quête
				$req = "select pquete_nombre from quete_perso
					where pquete_quete_cod = $quete_cod
						and pquete_perso_cod = $perso_cod";
				$stmt = $pdo->query($req);
				
				// Quête pas commencée
				if ($stmt->rowCount() == 0)
				{
				?>
					<p><br><br>Vous avez apparemment suffisamment de brouzoufs, et votre statut dans notre religion vous
						permet de réaliser <strong><a href="<?php echo  $url_methode_1; ?>">une dévotion à <?php echo  $dieu_nom; ?>.</a></strong>
						<em>(attention, cliquer sur ce lien va vous faire réaliser cette dévotion)</em>
					<br>Cela vous en coutera un millier de brouzoufs pour les offrandes. Certains affirment avoir eu
					des visions en rentrant en transe, et entendre les paroles de <?php echo  $dieu_nom; ?>.
					<br>Mais cela peut aussi comporter des risques. D’autres ont subit des séquelles...
				<?php 				}
				else
				{
					$result = $stmt->fetch();
					$quete = $result['pquete_nombre'];

					// On est dans la deuxième étape de la quête, après découverte de la première cachette
					if ($quete == 2)
					{
					?>
						<p><br><br>Vous avez apparemment suffisamment de brouzoufs, et votre statut dans votre religion vous
						permettent de réaliser <strong><a href="<?php echo  $url_methode_2; ?>">une dévotion à <?php echo  $dieu_nom; ?>.</a></strong>
						<em>(attention, cliquer sur ce lien va vous faire réaliser cette dévotion)</em>
						<br>Cela vous en coutera un millier de brouzoufs pour les offrandes. Certains affirment avoir eu
						des visions en rentrant en transe.
						<br>Mais cela peut aussi comporter des risques. D’autres ont subit des séquelles...
					<?php 					}
				}
			}
			else
			{
				echo "<br><br><p>Je suis navré, mais tout le monde ne peut pas faire de dévotion.
					Vous devez être d’un grade suffisant dans notre religion, et posséder suffisamment
					de brouzoufs pour les offrandes.";
			}
		}
		//Fin des dévotions
	break;

	/********* Résolution des différents cas des Dieux ***************/
	//Balgur
	case "dieu2_quete7": // Balgur premières dévotions
	case "dieu4_quete8": //Elian, premières dévotions
	case "dieu1_quete10": // Io, premières dévotions
		$des = rand (1,10);
		if ($des < 3)
		{
		?>
			<hr>Une douleur très violente se fait ressentir dans votre tête !
			<br>Vous n’êtes pas capable de canaliser cette puissance, et la douleur en arrive à devenir physique.
			<br>Vous vous écroulez, du sang coulant de vos narines.
		<?php 			$req = "update perso set perso_pv = max(perso_pv - 5, 1) where perso_cod = $perso_cod";
			$stmt = $pdo->query($req);
		}
		else
		{
			srand ((double) microtime() * 10000000); // pour intialiser le random
			if ($methode2 == "dieu2_quete7") // Textes Balgur
			{
				$input = array (
					"le pays des pas perdus, au sud est, dans le cul de sac mais pas tout au bout, en longeant le mur ouest",
					"le nord est, à dix positions de l’escalier sud, et huit positions de l’escalier est",
					"le sud ouest du pays des gelées, tout au sud ouest, quatres points sombres sont marqués, le plus au sud sera l’endroit recherché",
					"le colimaçon du pays des flammes et des démons qui a perdu sa forme, dans la petite salle en dessous du dispensaire",
					"l’est de la Moria, la croix du milieu en son centre le secret tu trouveras"
				);
				$cachette = array_rand ($input, 1);
				$nom_cachette = $input[$cachette];

				$texte = "<hr>Une voix résonne dans votre tête. Profonde et grave, elle ne semble pas interrompre
					les prières des moines présents
					<br>Calmement, les mots se font plus précis et clairs :
					<br><br><strong><em>Maintenant tu dois trouver ma parole ailleurs. Cherche ton chemin vers $nom_cachette.
					<br>La vérité tu trouveras, mais cachée elle sera. Barrer la route aux imprudents nous devons.</em></strong>";
			}
			else if ($methode2 == "dieu4_quete8") // Textes Elian
			{
				$input = array (
					"les premiers sous sols, au nord ouest, dans un coin intérieur de mur",
					"le pays des morts-vivants, au sud ouest, à l’horizontal de la croix sur le bord de ce territoire. Au sud est se trouvera un dispensaire",
					"le pays des flammes et des démons, au sud, avec un dispensaire et un temple, le triangle tu marqueras, en direction de l’ouest",
					"le château, au nord un dispensaire, deux pas de plus et tu y seras",
					"la deuxième salle des orks, au nord, un recoin t’attendra"
				);
				$cachette = array_rand ($input, 1);
				$nom_cachette = $input[$cachette];

				$texte = "<hr>Une voix résonne dans votre tête. Profonde et grave, elle ne semble pas
					interrompre les prières des moines présents
					<br>Calmement, les mots se font plus précis et clairs :
					<br><br><strong><em>Maintenant tu dois trouver ma parole ailleurs. Cherche ton chemin vers $nom_cachette.
					<br>Des choix pour le futur seront réalisés. La Justice guidera tes actes.</em></strong>";
			}
			else if ($methode2 == "dieu1_quete10") // Textes io
			{
				$input = array (
					"colimaçon du pays des flammes et des démons, celui qui a perdu sa forme, juste à côté du dispensaire",
					"nord du pays des gelées, afin de se trouver juste au sud du dispensaire"
				);
				$cachette = array_rand ($input, 1);
				$nom_cachette = $input[$cachette];

				$texte = "<hr>Une voix résonne dans votre tête.
					Profonde et grave, elle ne semble pas interrompre les prières des moines présents
					<br>Calmement, les mots se font plus précis et clairs :
					<br><br><strong><em>Cherche en direction du $nom_cachette.
					<br>Tu pourras trouver la lumière qui guide l’Aveugle</em></strong>";
			}

			echo $texte;
			//Mise à jour de l’étape terminée pour passer à la cachette
			$req = "insert into quete_perso values (default, $quete_cod, $perso_cod, '1')";
			$stmt = $pdo->query($req);
		}

		//Mise à jour des brouzoufs dans tous les cas
		$req = "update perso set perso_po = perso_po - 1000 where perso_cod = $perso_cod";
		$stmt = $pdo->query($req);
	break; // Fin du traitement de la première étape des dévotions

	case "dieu2_quete7_2": // Balgur deuxièmes dévotions
	case "dieu4_quete8_2": //Elian, deuxièmes dévotions
	case "dieu1_quete10_2": // Io deuxièmes dévotions
		$des = rand (1,10);
		if ($des < 3)
		{
		?>
			<hr>Une douleur très violente se fait ressentir dans votre tête !
			<br>Vous n’êtes pas capable de canaliser cette puissance, et la douleur en arrive à devenir physique.
			<br>Vous vous écroulez, du sang coulant de vos narines.
		<?php 			$req = "update perso set perso_pv = max(perso_pv - 5, 1) where perso_cod = $perso_cod";
			$stmt = $pdo->query($req);
		}
		else
		{
			if ($methode2 == "dieu2_quete7_2" || $methode2 == "dieu4_quete8_2") // Textes Balgur ou Elian
			{
				?>
				<hr>Une voix résonne dans votre tête. Profonde et grave, elle ne semble pas interrompre les prières des moines présents
				<br>Calmement, les mots se font plus précis et clairs :
				<br><br><strong><em>Tu as poursuivis mon but. Tu es un fidèle parmis les fidèles.
				<br>Mais la tache n’est pas finie. Le plus dur reste à faire. L’Aveugle tu dois convaincre.</em></strong>
				<?php 
				//Mise à jour de l’étape pour finaliser la quête
				$req = "update quete_perso set pquete_nombre = 3
					where pquete_perso_cod = $perso_cod and pquete_quete_cod = $quete_cod";
				$stmt = $pdo->query($req);
			}
			else // Textes pour Io, qui joue le rôle d’arbitre
			{
			?>
				<hr>Votre dévotion ne semble pas produire les effets escomptés. Io n’entend sans doute pas encore votre message.
				<br>Sa décision est sans doute lourde de conséquences, et la Balance est son approche, un pas d’un côté modifie forcément l’équilibre...
			<?php 				/*À ne mettre en ligne que lorsque tout sera prêt pour le final
			?>
				<hr>Une voix résonne dans votre tête. Profonde et grave, elle ne semble pas interrompre les prières des moines présents
				<br>Calmement, les mots se font plus précis et clairs :
				<br><br><strong><em>Tu as donc ouvert tes yeux sur le futur.
				<br>Tu vas devoir choisir lequel il sera.
				<br>Pour ça, en XXXXX tu guideras, et le futur tu détermineras.
				</em></strong>
			<?php
				//Mise à jour de l'étape terminée pour cloturer cette étape
				$req = "update quete_perso set pquete_nombre = 3
					where pquete_perso_cod = $perso_cod and pquete_quete_cod = $quete_cod";
				$stmt = $pdo->query($req);

				Fin mise en commentaire à supprimer*/
			}
		}
		//Mise à jour des brouzoufs
		$req = "update perso set perso_po = perso_po - 1000 where perso_cod = $perso_cod";
		$stmt = $pdo->query($req);
	break; // Fin du traitement Etape 2
}
?>