<?php
// gestion des quêtes sur les bâtiments administratifs.

$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

echo "<hr><br>";
$methode2     = get_request_var('methode2', 'debut');

switch ($methode2)
{
    case "debut":
        // Quête n°11 : contrats de chasse
        $req = "select pquete_cod, pquete_nombre, pquete_date_debut, pquete_date_fin, pquete_termine, pquete_param
			from quete_perso
			where pquete_quete_cod = 11
				and pquete_perso_cod = $perso_cod
				and pquete_termine not in ('O', 'R')";
		$stmt = $pdo->query($req);
		if ($stmt->rowCount() != 0) // le perso a une mission en cours, on teste si il l’a terminée.
		{
			$result = $stmt->fetch();
			$statut_quete = $result['pquete_termine'];
			$nombre_monstre = $result['pquete_nombre'];
			$monstre_mission = $result['pquete_param'];
			$debut_mission = $result['pquete_date_debut'];
			$fin_mission = $result['pquete_date_fin'];
			$quete_cod = $result['pquete_cod'];
			$req_monstres = "select sum(ptab_total) as total_encours from perso_tableau_chasse
				where ptab_perso_cod = $perso_cod
					and ptab_gmon_cod = $monstre_mission
					and ptab_date > '$debut_mission'
					and ptab_date < '$fin_mission'";
			$stmt = $pdo->query($req_monstres);
			$total_encours = 0;
			$result = $stmt->fetch();
			$total_encours = $result['total_encours'];
			if (is_null($total_encours))
			{
				$total_encours = 0;
			}
			
			// Contrat pas terminé
			if ($total_encours < $nombre_monstre)
			{
				$req = "update quete_perso set pquete_termine = 'R'
					where pquete_cod = $quete_cod and pquete_date_fin < now()";
				$stmt = $pdo->query($req);
				if ($stmt->rowCount() == 0) // Pas d'expiration pour cette mission (ça marche ça ??)
				{
					$req = "select gmon_nom from monstre_generique
						where gmon_cod = $monstre_mission";
					$stmt = $pdo->query($req);
					$result = $stmt->fetch();
					$monstre_nom = $result['gmon_nom'];
					echo "Vous avez déjà une mission en cours. Réalisez là avant d’en chercher une autre !
						La chasse au $monstre_nom ne se fait pas à moitié.
						<br>Ce n’est pas en en tuant seulement $total_encours que vous allez nous convaincre de votre valeur.
						<br>Votre mission était d’en tuer $nombre_monstre !
						<br>À moins que vous ne soyez pas capable d’en tuer plus ?";
				}
			}
			else
			{
                echo "Félicitations ! vous avez réalisé avec valeur votre mission.
							<br>Comme convenu, nous vous offrons votre récompense.
							<br>Voici une bourse qui vous permettra d’aller loin !";


                $perso->perso_po       = $perso->perso_po + 5000;
                $perso->perso_px       = $perso->perso_px + 10;
                $perso->perso_prestige = $perso->perso_prestige + 1;
                $perso->stocke();

                $req  = "update quete_perso set pquete_termine = 'O' where pquete_cod = $quete_cod";
                $stmt = $pdo->query($req);
            }
		}
		else /*le perso n’a pas de quête de ce type engagée, on teste si on lui en donne une
		Pour l’instant, test sur son num de perso et la date. On pourrait aussi imaginer de ne faire ça que tous les 10 premiers jours
		de chaque mois*/
		{
			$req = "select donne_contrat($perso_cod) as resultat";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$donne_contrat = $result['resultat'];

			$req = "select perso_niveau, etage_reference
				from perso, perso_position, positions, etage
				where perso_cod = ppos_perso_cod
					and ppos_pos_cod = pos_cod
					and pos_etage = etage_numero
					and perso_cod = $perso_cod";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$perso_level = $result['perso_niveau'];
			$etage_reference = $result['etage_reference'];
			if(isset($_COOKIE['quete_gmon_cod']))
			{
				$req = "select gmon_cod,gmon_nom,gmon_avatar
					from monstre_generique
					where gmon_cod = " . $_COOKIE['quete_gmon_cod'];
			}
			else
			{
				$niv_min = $perso_level - 8;
				$niv_max = $perso_level + 15;
				if ($etage_reference == -9)
				{
					$niv_min = 0;
				}
				$req = "select gmon_cod, gmon_nom, gmon_avatar
					from monstre_generique
					where gmon_niveau between $niv_min and $niv_max
						and gmon_quete = 'O'
						and gmon_cod in
							(select rmon_gmon_cod from repart_monstre where rmon_etage_cod in
								(select pos_etage from perso_position, positions
								where ppos_perso_cod = $perso_cod
									and ppos_pos_cod = pos_cod))
					order by random()
					limit 1";
			}
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() == 0) /*Le perso n’a pas le niveau requis pour cet étage pour avoir une mission*/
			{
			?>
				<br>Un fonctionnaire s’approche de vous et vous interpelle
				<br><em>Allons, soyons sérieux, vous êtes un aventurier. Mesurez-vous à ce qui pourra vous valoir Gloire et Honneur !</em>
				<br>Ce faisant, le fonctionnaire vous tourne le dos, sans vous proposer de contrat.
				<br><br>Pour rappel, vous êtes niveau <?php echo $perso_level ?>. Une quête vous sera proposée uniquement en adéquation avec votre niveau.
			<?php 			}
			else if ($donne_contrat == 'O')
			{
				$result = $stmt->fetch();
				$quete_gmon_cod = $result['gmon_cod'];
				setcookie("quete_gmon_cod", $quete_gmon_cod, time() + 86400);
				$monstre = $result['gmon_cod'];
				$monstre_nom = $result['gmon_nom'];
				$avatar = $result['gmon_avatar'];
				srand ((double) microtime() * 10000000); // pour intialiser le random
				$input         = array (
					"Aventuriers, Aventurières, depuis un certain temps, nous notons la recrudescence d’agglomération de monstres dans
						certains coins de ces souterrains, et plus particulièrement de $monstre_nom !
						<br>Nous faisons donc appel à votre aide pour chasser cette vermine.",
					"Wanted $monstre_nom - Forte récompense - Aventuriers expérimentés recherchés pour chasse animée - 
						<br>Candidature immédiate - Conditions intéressantes -",
					"Chasseurs de tout poil, ils vous manque certainement un trophée dans votre besace. Nous vous offrons l’occasion d’une chasse unique.
						Venez combattre le célèbre $monstre_nom afin de gagner ainsi une forte récompense.",
					"Vous en avez rêvé ? Vous pouvez maintenant vous offrir ce que vous désirez le plus !
						<br>Pour cela, rien de plus simple. Partez en chasse du $monstre_nom. Sa férocité n’est qu’une légende,
						et sa tête vous vaudra une fortune !",
					"En mal d’aventures ? Nous vous offrons un safari tel que vous n’en avez jamais vécu, à la chasse aux $monstre_nom. Frissons garantis.",
					"Qui ne rêve pas d’accrocher au dessus de sa cheminée une tête de $monstre_nom ? Montrez-nous votre plus bel empaillage
						et vous gagnerez peut-être notre premier prix !"
				);
				$wanted        = array_rand($input, 1);
				$phrase_wanted = $input[$wanted];
				echo $phrase_wanted;
				if ($avatar != null) //Utilisation des avatars si il existe pour ce monstre.
				{
					echo "<br>Ce type de monstre est facilement reconnaissable.
						Il ressemble à cela :<br><p\"><img src=\"../avatars/" . $avatar . "\"></p>";
				}
				echo "<br>Souhaitez vous répondre à cette annonce ? <em>(Attention, ce choix vous engage pour une certaine durée)</em>";
				?>
				<form name="mission" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<input type="hidden" name="methode2" value="mission">
					<table>
						<tr>
							<td class="soustitre2"><p>Oui, cela peut être intéressant !</p></td>
							<td><input type="radio" class="vide" name="controle1" value="oui"></td>
						</tr>
						<tr>
							<td class="soustitre2"><p>Non, je préfère réfléchir encore un peu.</p></td>
							<td><input type="radio" class="vide" name="controle1" value="non"></td>
						</tr>
					</table>
					<input type="hidden" class="vide" name="monstre" value="<?php echo $monstre;?>" />
					<input type="hidden" class="vide" name="monstre_nom" value="<?php echo $monstre_nom;?>" />
					<input type="submit" class="test" value="Valider !" />
				</form>
			<?php 			}
			else //Le perso n’obtiendra pas de quête aujourd’hui
			{
                ?>
                <br>Un papier vole au vent. On peut y lire le texte d’un ancien contrat, dont la date est passée depuis fort longtemps.
                <br>Vous regardez autour de vous ... Et personne.
                <br>Ce n’est certainement pas aujourd’hui que l’on vous proposera un contrat de chasse.
                <br>Mais comme il est dit, demain est un autre jour ...
            <?php }
        }

        //Traitement des pochettes surprises (début)
        // Type de perso

        $type_perso_ok = ($perso->perso_type_perso == 1);

        // Possession de pochette surprise
        $req              = "select obj_gobj_cod, perobj_obj_cod, obj_nom
			from objets, perso_objets
			where perobj_obj_cod = obj_cod
				and perobj_perso_cod = $perso_cod
				and perobj_identifie = 'O'
				and obj_gobj_cod = 642";
        $stmt             = $pdo->query($req);
        $possede_pochette = ($stmt->rowCount() != 0);

		// Pochette déjà échangée ?
		$req = "select ppoch_perso_cod, ppoch_valeur from perso_pochette where ppoch_perso_cod = $perso_cod";
		$stmt = $pdo->query($req);
		if (!$result = $stmt->fetch())
		{
			$req = "insert into perso_pochette (ppoch_perso_cod, ppoch_valeur) values ($perso_cod, 0)";
			$stmt = $pdo->query($req);
			$deja_fait = false;
		}
		else
		{
			$deja_fait = ($result['ppoch_valeur'] > 0);
		}

		if ($type_perso_ok && $possede_pochette && !$deja_fait)
		{
		?>
			<form name="pochette" method="post">
				<input type="hidden" name="methode2" value="pochette" >
				<hr /><table><td><br><a href="javascript:document.pochette.submit();"
					onMouseOver="img.src='../avatars/roue.gif' "
					onMouseOut="img.src='../avatars/roue.png' ">
				<img name="img" align = "center" src="../avatars/roue.png"></a></td>
				<td>Vous pouvez participer au grand tirage de la loterie magique.
				<br>Ce tirage ne peut être fait qu’<strong>une seule fois</strong>, si vous possédez votre pochette surprise.
				<br><br><br><strong>Faites tourner la roue pour découvrir votre cadeau !</strong> <em>(cliquez dessus)</em></td></table>
			</form>
		<?php 		}
	break;

	case "mission":
		$mission = $_POST['controle1'];
		$monstre = $_POST['monstre'];
		$monstre_nom = $_POST['monstre_nom'];
		if ($mission == 'oui')
		{
			$random = rand (1,4);
			$temps = $random + 4; //Nombre de semaines autorisées pour la mission
			echo "Allez, partez donc en chasse au $monstre_nom ! Ramenez-nous en <strong>$random</strong> pour nous montrer votre courage.
				<br>Nous vous récompenserons alors de 5000 brouzoufs !
				<br>Vous devez impérativement tuer cette vermine de $monstre_nom vous-même, car c’est la marque des vrais chasseurs.
				Vous pourrez vous faire aider dans votre entreprise, mais seuls les morts faites de vos mains seront comptabilisées.
				<br>Nous vous laissons $temps semaines pour réaliser cette mission, autrement,
				cela signifiera que vous n’êtes pas digne des vrais chasseurs.
				<br>La mission doit se réaliser dans ce laps de temps, mais vous pourrez ensuite prendre tout le temps
				que vous souhaitez pour venir chercher votre récompense.";
			$req = "insert into quete_perso
					(pquete_quete_cod, pquete_perso_cod, pquete_nombre, pquete_date_debut, pquete_date_fin, pquete_param)
				values ('11', '$perso_cod', '$random', now(), now() + '$temps weeks'::interval, '$monstre')";
			$stmt = $pdo->query($req);
		}
		else
		{
			setcookie("quete_gmon_cod", $quete_gmon_cod, time()+86400);
			echo	"Voilà qui est navrant !
				<br>Encore un de ces aventuriers de pacotille qui préfère les p’tits boulots faciles !
				<br>Je ne vous salue point !";
		}
	break;

    case "pochette":
        // Type de perso

        $type_perso_ok = ($perso->perso_type_perso == 1);

        // Possession de pochette surprise
        $req              = "select obj_gobj_cod, perobj_obj_cod, obj_nom
			from objets, perso_objets
			where perobj_obj_cod = obj_cod
				and perobj_perso_cod = $perso_cod
				and perobj_identifie = 'O'
				and obj_gobj_cod = 642";
        $stmt             = $pdo->query($req);
        $possede_pochette = ($stmt->rowCount() != 0);
		if ($possede_pochette)
		{
			$result = $stmt->fetch();
			$pochette_cod = $result['perobj_obj_cod'];
		}

		// Pochette déjà échangée ?
		$req = "select ppoch_perso_cod, ppoch_valeur from perso_pochette where ppoch_perso_cod = $perso_cod";
		$stmt = $pdo->query($req);
		if (!$result = $stmt->fetch())
		{
			$req = "insert into perso_pochette (ppoch_perso_cod, ppoch_valeur) values ($perso_cod, 0)";
			$stmt = $pdo->query($req);
			$deja_fait = false;
		}
		else
		{
			$deja_fait = ($result['ppoch_valeur'] > 0);
		}

		if ($type_perso_ok && $possede_pochette && !$deja_fait)
		{
			//On rajoute la récompense et on annonce ce que c’est
			// 1) Dans tous les cas : une rune.
			$rune_code = 26 + rand(1, 20);
			$req = "select cree_objet_perso_nombre($rune_code, $perso_cod, 1)";
			$stmt = $pdo->query($req);
			
			// 2) Cadeau aléatoire
			$random = rand (1, 6);
			if ($random == 1) // Entre 5000 et 9000 brouzoufs
            {
                $brouzoufs = ((rand(1, 5) * 1000) + 4000);

                $perso->perso_po = $perso->perso_po + $brouzoufs;
                $perso->stocke();

                $texte = "une petite somme d’or, de <strong>$brouzoufs brouzoufs et une rune</strong>.";
            }
			else if ($random == 2) // deux potions (différentes)
			{
				$req = "select gobj_cod, lancer_des(1, 1000) as num from objet_generique
					where gobj_tobj_cod = 21 and gobj_cod not in (561, 412)
					order by num limit 2";
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$potion1 = $result['gobj_cod'];
				$result = $stmt->fetch();
				$potion2 = $result['gobj_cod'];

				$req = "select cree_objet_perso_nombre($potion1, $perso_cod, 1),
					cree_objet_perso_nombre($potion2, $perso_cod, 1)";
				$stmt = $pdo->query($req);
				$texte = "<strong>deux petites potions</strong> qui ont été rajoutées à votre inventaire ainsi qu’une rune.";
			}
			else if ($random == 3) // Deux parchemins
			{
				$req = "select cree_objet_perso_nombre(gobj_cod, $perso_cod, 1) from objet_generique
					where gobj_cod in
						(select gobj_cod from objet_generique g2 
						where g2.gobj_tobj_cod=20 and g2.gobj_valeur > 100
						order by random()
						limit 2)";
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$texte = "<strong>deux parchemins</strong> qui ont été rajoutés à votre inventaire ainsi qu’une rune.";
			}
			else if ($random == 4)  // Deux (autres) runes
			{
				$rune_code1 = 26 + rand(1, 20);
				$rune_code2 = 26 + rand(1, 20);
				$req = "select cree_objet_perso_nombre($rune_code1, $perso_cod, 1),
					cree_objet_perso_nombre($rune_code2, $perso_cod, 1)";
				$stmt = $pdo->query($req);
				$texte = "<strong>trois runes</strong>, qui ont été rajoutées à votre inventaire.";
			}
			else if ($random == 5) // Un œuf de basilic
			{
				$req = "select cree_objet_perso_nombre(269, $perso_cod, 1)";
				$stmt = $pdo->query($req);
				$texte = "<strong>un œuf de basilic</strong>, qui a été rajouté à votre inventaire et qu’il faudra faire éclore, ainsi qu’une rune.";
			}
			else if ($random == 6) // Deux composants de forgeamagie
			{
				$req = "select cree_objet_perso_nombre(gobj_cod, $perso_cod, 1) from objet_generique
					where gobj_cod in (select oenc_gobj_cod from enc_objets
					inner join objet_generique g2 on g2.gobj_cod = oenc_gobj_cod
					where g2.gobj_poids < 2
					order by random()
					limit 2)";
				$stmt = $pdo->query($req);
				$texte = "<strong>deux composants d’enchantement</strong> qui ont été rajoutés à votre inventaire, ainsi qu’une rune.";
			}
			echo "<hr><br><strong>La roue a parlé !</strong><br>
				<br>Votre pochette surprise s’est transformée. Et pour la peine, vous avez gagné $texte
				<div align='center'><br><br><br><font size='+2'><strong>À tous un joyeux Léno, et de bonnes fêtes !<strong></font><br><br></div>";

			//On supprime la pochette et on fait augmenter le compteur pour n’avoir cette récompense qu’une seule fois
			$req = "select f_del_objet($pochette_cod)";
			$stmt = $pdo->query($req);
			$req = "update perso_pochette set ppoch_valeur = 1 where ppoch_perso_cod = $perso_cod";
			$stmt = $pdo->query($req);
		}
		else
		{
			echo 'Mmmmh, il doit s’agir d’une erreur... Vous n’êtes pas censé participer à la loterie !';
		}
	break;
}
?>