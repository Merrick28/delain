<?php // gestion des quêtes sur les auberges.

if(!defined("APPEL"))
	die("Erreur d’appel de page !");

if(!isset($methode2))
	$methode2 = "debut";

switch($methode2)
{
	case "debut":
		//Utilisation des points de prestige pour donner un peu de contenu.
		$req = "select perso_prestige,perso_nom,perso_sex,perso_po from perso where perso_cod = $perso_cod";
		$stmt = $pdo->query($req);
		$result = $stmt->fetch();
		$prestige = $result['perso_prestige'];
		$nom = $result['perso_nom'];
		$sexe = $result['perso_sex'];
		$brouzoufs = $result['perso_po'];
		if ($prestige >= 10 and $prestige <= 20)
		{
			srand ((double) microtime() * 10000000); // pour intialiser le random
			$input = array (
				"<br>Alors que vous rentrez dans cette auberge, une sorte d’ivrogne s’approche de vous, sentant la bière à plein nez :
				<br><em> Et dîtes, ch’vous connais vous ! vous n’seriez pas $nom ? Ou alors Graspork ?
				<br>Pfff, encore un de ces espèces de %#*£^ù qui cherche la gloire à tous les étages !
				<br>En plus, j’suis sûr que vous n’connaissez même pas les bons coins qui font la gloire !
				<br>Allez, si vous m’payez un coup à boire, j’vous dirais un secret. Il parait qu’il y a une drôle de grotte,
					mais... euh... boarf, ch’ai plus où elle est.",
				"<br>Et vous là, vous avez une tête qui m’revient.
				À moins que ce ne soit sur une affiche de la Milice que je vous ai vu...",
				"<br>Vous surprenez une conversation, plongé dans votre verre :
				<br><em>Un jour, en cherchant un morceau de ferraille pour le forgeron du coin, j’suis tombé sur une cachette !
				<br>J’me souviens plus bien où c’était mais franchement, y’avait un max à se faire !</em>",
				"Au pays des farfadets, on raconte qu’il y a plein de recoins avec des planques ! Ce serait les farfadets eux mêmes qui entreposeraient le butin de leurs larcins !",
				"",
				""
			);
			// Rajouter la position de cachettes réelles pour donner les indications
			$cachette = array_rand ($input, 1);
			$phrase = $input[$cachette];
			echo "<hr>$phrase<br><br>";
		}
		else if ($prestige > 20)
		{
		?>
			<hr><br>Un homme s’approche de vous, et vous interpelle :
			<br><em>« Il me semble vous reconnaître ! Ne seriez-vous pas <?php  echo $nom; ?> ? Je ne pense pas me tromper.
			<br>Vos exploits sont contés de-ci de-là, et nul ne les ignore maintenant.
			Je m’en vais diffuser la nouvelle de votre venue en cet endroit !
		<?php 		}
		else
		{
		?>
			<hr><br>Les rumeurs les plus folles hantent ces lieux. Prêtez donc l’oreille.
		<?php 		}
		//Quête de la tournée des auberges
		$req = "select * from
				(select pquete_termine, pquete_perso_cod 
				from quete_perso 
				where pquete_perso_cod = $perso_cod and pquete_quete_cod = 6 ) t1
			left join
				(select paub_visite, paub_lieu_cod, paub_perso_cod
				from perso_auberge
				where paub_perso_cod = $perso_cod and paub_lieu_cod = $lieu_cod) t2
			on t1.pquete_perso_cod = t2.paub_perso_cod";
		$stmt = $pdo->query($req);
		if($result = $stmt->fetch())
		{
			$quete_termine = $result['pquete_termine'];
			$aub_visite = $result['paub_visite'];
			if ($quete_termine == 'N' and ($aub_visite == null or $aub_visite == 'N'))
			{
				echo "<hr><br>". $nom_sexe[$sexe] .", je vois que vous faites partie des joyeux fêtards qui ont choisi la tournée des auberges !
				<br> C’est un grand honneur pour nous de vous voir ici.
				<br>Pour 50 brouzoufs, nous vous offrons boissons à volonté, et votre inscription dans notre registre pour votre tournée !";

				if ($brouzoufs < 50)
				{
					echo "<hr><br>Malheureusement, vos poches ont l’air d'être aussi vide que ma patience à l’égard des resquilleurs.
						Nous ne pouvons rien faire pour vous ! 50 brouzoufs, ce n’est quand même pas la mer à boire.
						mer... boire... auberge...
						<br>En plus, $nom_sexe[$sexe] n’a pas le sens de l’humour je vois ...";
				}
				else
				{
				?>
					<form name="tournee" method="post" action="<?php echo $PHP_SELF;?>">
					<input type="hidden" name="methode2" value="tournee">
					<table>
					<tr>
					<tr>
					<td class="soustitre2"><p>Allez, buvons un coup ensemble ! C'est ma tournée !</td>
					<td><input type="radio" class="vide" name="controle1" value="tournee_ok"></td>
					</tr>
					<tr>
					<td class="soustitre2"><p>Je ne suis pas sûr de vous trouver de bonne compagnie.
						Et cette inscription à visiter des auberges pour une beuverie n’était pas une bonne idée.</td>
					<td><input type="radio" class="vide" name="controle1" value="tournee_ko"></td>
					</tr>
					<tr>
					<td><input type="hidden" class="vide" name="sexe" value="<?php echo $sexe;?>"></td>
					</tr>
					<tr>
					<td><input type="hidden" class="vide" name="aub_visite" value="<?php echo $aub_visite;?>"></td>
					</tr>
					</table>
					<input type="submit" class="test" value="Valider !">
					</form>
				<?php 				}
			}
			else if ($quete_termine == 'N' and $aub_visite == 'O')
			{
				echo "<hr><br><br>". $nom_sexe[$sexe] ." est un connaisseur à ce que je vois,
					mais la tournée des auberges est un marathon qui ne permet pas de comptabiliser
					deux fois la même étape. Bon courage pour la suite.<br>";
			}
			else if ($quete_termine == 'O')
			{
				echo "<hr><br><br>Félicitations ! Peu sont ceux qui ont déjà achevé la tournée des auberges !
					Pour la peine, nous vous offrons à boire, ". $nom_sexe[$sexe] ."<br>";
			}
		}
		else
		{
			echo "<hr><br><br>Vous avez p’t’être raison de ne pas participer à la tournée des bars.
				Souvent, ça finit en orgie, et c’est pas beau à voir...<br>";
		}
	break;

	case "tournee":
		$valid_tournee = $_POST['controle1'];
		$aub_visite = $_POST['aub_visite'];
		if ($valid_tournee == 'tournee_ok')
		{
		?>
			<hr><br>
		<?php 			srand ((double) microtime() * 10000000); // pour initialiser le random
			$input = array (
				"<br>C’est à boire, boire, boire,
				<br>C’est à boire qu’il nous faut, Oh ! Oh ! Oh ! Oh !",
				"<br>Quand Madelon vient nous servir à boire
				<br>Sous la tonnelle on frôle son jupon
				<br>Et chacun lui raconte une histoire
				<br>Une histoire à sa façon
				<br>La Madelon pour nous n’est pas sévère
				<br>Quand on lui prend la taille ou le menton
				<br>Elle rit, c’est tout le mal qu’elle sait faire
				<br>Madelon, Madelon, Madelon !",
				"<br>Il est des nôtres
				<br>Il a bu son verre comme les autres
				<br>C’est un ivrogne
				<br>Il boit n’importe quoi pour vu que sa cogne.",
				"<br>J’vous interpelle encore, ivre mort au matin
				<br>Car aujourd’hui, c’est la saint Valentin
				<br>Et je me remémore, notre nuit très bien,
				<br>Comme un crabe déjà mort, tu t’ouvrais entre mes mains.
				<br>Ceci est mon vécu, ceci est ma priere,
				<br>Je te la fais, les deux genoux a terre.",
				"<br>S’il fallait te raconter ma vie
				<br>On resterait là toute la nuit
				<br>Car pour quelques sourires alors
				<br>J’en ai connu des temps morts
				<br>Et malgré tout l’amour que tu me donnes
				<br>Tu n’en feras jamais assez
				<br>Car c’est l’alcool lui qui me donne
				<br>Les plus beaux rêves que je fais
				<br>Et dans ces moments-là
				<br>La fille à qui je pense
				<br>La fille à qui je pense
				<br>Est plus belle que toi"
			);
			$phrase = array_rand ($input, 1);
			$phrase_boire = $input[$phrase];
			echo "<em>Un chant est alors entonné gaillardement :</em><br>$phrase_boire<br><br>";//Aub :". $aub_visite ."/". $lieu_cod ."/". $perso_cod ."<br>";

			$req = "update perso set perso_po  = perso_po - 50 where perso_cod = $perso_cod";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			if ($aub_visite == null)
			{
				$req = "insert into perso_auberge (paub_perso_cod, paub_lieu_cod, paub_nombre, paub_visite)
					values ($perso_cod, $lieu_cod, '1', 'O')";
			}
			else
			{
				$req = "update perso_auberge set paub_visite = 'O'
					where paub_lieu_cod = $lieu_cod
						and paub_perso_cod = $perso_cod";
			}
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
		}
		else if ($valid_tournee == 'tournee_ko')
		{
			echo "<hr><br>$nom_sexe[$sexe] n’assume donc plus ses penchants. À moins que vous ne soyez un peu radin. Et bien, tant pis pour vous !<br><br>";
		}
	break;
}
?>
