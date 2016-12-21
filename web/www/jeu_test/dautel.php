<?php if(!defined("APPEL"))
	die("Erreur d'appel de page !");
include_once "verif_connexion.php";

//
//Contenu de la div de droite
//
$contenu_page = '';
//
// on regarde si le joueur est bien sur le lieu qu'on attend
//
$erreur = 0;
if (!isset($methode))
{
	$methode = 'entree';
}
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas devant un autel de prière !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 33)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas devant un autel de prière !!!");
	}
	$req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
	$db->query($req);
	$db->next_record();
	if ($db->f("perso_type_perso") == 3)
	{
		$erreur = 1;
		echo("<p>Les familiers ne font pas bon usage d'un autel de prière.");
	}
}
//
// OK, tout est bon, on s'attaque à la suite
//
if ($erreur == 0)
{
	$req = 'select dper_dieu_cod,dniv_libelle,dieu_nom,dper_niveau,dper_points
				from dieu_perso,dieu_niveau,dieu where dper_perso_cod = ' . $perso_cod  . '
				and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau
				and dieu_cod = dper_dieu_cod ';
	$db->query($req);
	if ($db->nf() != 0)
	{
		$db->next_record();
		$niveau_actu = $db->f("dper_niveau");
		$dieu_perso = $db->f("dper_dieu_cod");
	}
	$lieu = $tab_lieu['lieu_cod'];
	$req = "select dieu_cod,dieu_nom,dieu_description,lieu_description,dieu_ceremonie,dieu_pouvoir from dieu,lieu ";
	$req = $req . "where lieu_cod = " . $tab_lieu['lieu_cod']  . " and lieu_dieu_cod = dieu_cod ";
	$db->query($req);
	$db->next_record();
	$dieu_cod = $db->f("dieu_cod");
	$dieu_nom = $db->f("dieu_nom");
	$dieu_descr = $db->f("dieu_description");
	$lieu_descr = $db->f("lieu_description");
	$dieu_ceremonie = $db->f("dieu_ceremonie");
	$dieu_pouvoir = $db->f("dieu_pouvoir");
	switch($methode)
	{
		case 'entree':
			// on cherche d'abord le dieu associé.
			echo "<p><img src=\"../images/temple.png\"><br />
			Vous êtes devant un autel dédié à <b>" , $dieu_nom, "</b><br>";
			echo "<br><br><i>" , $lieu_descr, "</i><br>";
			
			// on regarde s'il existe un lien avec le perso
			$req = 'select dper_dieu_cod,dniv_libelle,dieu_nom,dper_niveau,dper_points
				from dieu_perso,dieu_niveau,dieu where dper_perso_cod = ' . $perso_cod  . '
				and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau
				and dieu_cod = dper_dieu_cod ';
			$db->query($req);
			if ($db->nf() == 0)
			{
				// aucun rattachement
				echo "<p>Vous n'êtes fidèle d'aucun dieu ";
			}
			else
			{
				$db->next_record();
				$perso_dieu_nom = $db->f("dieu_nom");
				if ($db->f("dper_dieu_cod") == $dieu_cod)
				{
					echo "<p>Vous êtes " , $db->f("dniv_libelle") , " de ce dieu";
					$points = $db->f("dper_points");
					$niveau_actu = $db->f("dper_niveau");
					$cout_pa = $db->getparm_n(55);
					
					// Bénédiction
					if($niveau_actu >= 2)
						echo '<p><a href="' . $PH_SELF . '?methode=benediction">Demander une bénédiction (' . $db->getparm_n(110) . ' PA) </a>';
				}
				else
				{
					echo "<p>Vous êtes " , $db->f("dniv_libelle") , " de ". $perso_dieu_nom;
				}
			}
			// on regarde si on n'est pas rénégat, quand même.
			$req = "select dren_cod from dieu_renegat where dren_dieu_cod = $dieu_cod and dren_perso_cod = $perso_cod and dren_datfin > now()";
			$db->query($req);
			if ($db->nf() != 0)
			{
				// RENEGAT !!!
				echo "<p>Vous êtes <b>renégat</b> !! Inutile de s'attarder en ce lieu, $dieu_nom ne veut même pas entendre parler de vous !";
			}
			else
			{
				?>
				<p><a href="<?php echo $PHP_SELF;?>?methode=prie1">- Je voudrais me recueillir pour prier <?php echo $dieu_nom; ?></a> (<?php  echo $db->getparm_n(48); ?> PA)</p>
				<?php 
			}
			break;
			
		case 'prie1':
			$attention = 0;
			$req = "select dper_dieu_cod,dniv_libelle from dieu_perso,dieu_niveau where dper_perso_cod = $perso_cod ";
			$req = $req . "and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau ";
			$db->query($req);
			if ($db->nf() != 0)
			{
				$db->next_record();
				if ($db->f("dper_dieu_cod") != $dieu_cod)
				{
					$attention = 1;
				}
			}
			if ($attention == 0)
			{
				?>
				<p>Vous vous apprêtez à prier <b><?php echo $dieu_nom ?></b><br>
				<a href="action.php?methode=prie&dieu=<?php echo $dieu_cod; ?>">Continuer ?</a>
				<?php 
			}
			else
			{
				echo "<p>Vous êtes " , $db->f("dniv_libelle") , " d'un autre dieu. Inutile de s'attarder en ce lieu.<br>";
			}
			break;
			
		case 'benediction':
			if($niveau_actu >= 2)
			{
				//
				// on commence par regarder si le dieu en question a assez de puissance pour accorder la bénédiction
				//
				$point_ben = ($niveau_actu - 2)*($niveau_actu - 2);
				if($points_ben < 2)
					$points_ben = 2;
				if($dieu_pouvoir < $points_ben)
				{
					echo '<p>Votre Divinité n\'a pas assez de puissance pour exaucer votre souhait !';
					break;
				}
				//
				// on fait quand même un contrôle de cohérence divinité/perso
				//
				if($dieu_perso != $dieu_cod)
				{
					echo "Vous ne pouvez demander des bénédictions que sur un lieu dédié à votre divinité.";
					break;
				}
				//
				// on contrôle les PA
				//
				$req = 'select perso_pa from perso where perso_cod = ' . $perso_cod;
				$db->query($req);
				$db->next_record();
				$pa = $db->f('perso_pa');
				if($pa < $db->getparm_n(110))
				{
					echo "<p>Vous n'avez pas assez de PA pour cette action.";
					break;

				}
				//
				// à partir d'ici, tout devrait être bon.
				//
				$nb_tours = $niveau_actu * $niveau_actu;
				// on commence par enlever les points au dieu
				$req = "update dieu set dieu_pouvoir = dieu_pouvoir - " . $points_ben . " where dieu_cod = ". $dieu_cod;
				$db->query($req);
				// on enlève les PA
				$req = 'update perso set perso_pa = perso_pa - getparm_n(110) where perso_cod = ' . $perso_cod;
				$db->query($req);
				// et maintenant, on va switcher par rapport à la divinité.
				switch($dieu_cod)
				{
					case 1: // IO l'aveugle => chant du barde
						$req = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
						$db->query($req);
						$req = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -1)";
						$db->query($req);
						$req = "select ajoute_bonus($perso_cod,'DEG', $nb_tours, 2)";
						$db->query($req);
						$texte = "Io vous a entendu, vous vous sentez plus habile au combat.";
						break;
					case 2: // Balgur => soins importants
						$req = "select perso_pv,perso_pv_max from perso
							where perso_cod = " . $perso_cod;
						$db->query($req);
						$db->next_record();
						$v_pv = $db->f('perso_pv');
						$v_pv_max = $db->f('perso_pv_max');
						$pv_gagne = 0;
						for ($i=0;$i<8;$i++)
						{
							$req = "select lancer_des(1,4) as resultat";
							$db->query($req);
							$db->next_record();
							$pv_gagne += $db->f('resultat');
						}
						$tot_pv =  $v_pv + $pv_gagne;
						if($tot_pv > $v_pv_max)
							$tot_pv = $v_pv_max;
						$req = "update perso set perso_pv = " . $tot_pv . " where perso_cod = " . $perso_cod;
						$db->query($req);
						$texte = "Balgur soigne vos blessures... ";
						break;
					case 3: // Galthée => reconstruction intense
						$req = "select ajoute_bonus($perso_cod,'REG', $nb_tours, 10)";
						$db->query($req);
						$texte = "Dans sa grande bonté, Galthée aide votre corps à se reconstruire.";
						break;
					case 4: // Elian => sort de défense
						$req = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 2)";
						$db->query($req);
						$texte = "Elian rend votre corps plus résistant aux blessures.";
						break;
					case 5: // Apiera => armure dure
						$req = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 5)";
						$db->query($req);
						$texte = "Apiera rend votre corps plus résistant aux blessures.";
						break;
					case 7:	// FAlis => Fou furieux
						$req = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -3)";
						$db->query($req);
						$texte = "Falis vous entend, et répond à votre demande de bénédiction. Vous vous sentez plus rapide au combat.";
						break;
					case 8: // Ecatis => Mange ta soupe
						$req = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
						$db->query($req);
						$texte = "Ecatis vous a entendu, vous vous sentez plus habile au combat.";
						break;
					case 9: // Tonto => Danse de Saint Guy
						$req = "select ajoute_bonus($perso_cod,'ESQ', $nb_tours, 25)";
						$db->query($req);
						$req = "select ajoute_bonus($perso_cod,'DSG', $nb_tours, 20)";
						$db->query($req);
						$texte = "Tonto vous enseigne la technique de l'homme ivre.";
						break;
				}
				echo $texte;
			}
			break;
	}
}

?>
