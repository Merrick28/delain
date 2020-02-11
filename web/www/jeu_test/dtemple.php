<?php if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";
$param = new parametres();
//
// on regarde si le joueur est bien sur le lieu qu'on attend
//
$erreur = 0;
if (!isset($methode))
{
	$methode = 'entree';
}

$type_lieu = 17;
$nom_lieu = 'un temple';

include "blocks/_test_lieu.php";

$perso_fam = false;
$req = 'select perso_type_perso from perso where perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req);
$result = $stmt->fetch();
if ($result['perso_type_perso'] == 3)
{
    $erreur = 1;
    $perso_fam = true;
    echo("<p>Les familiers, quels qu’ils soient ne sont pas admis dans les temples.</p>");
}

//
// OK, tout est bon, on s’attaque à la suite
//
if ($erreur == 0)
{
	$req = 'select dper_dieu_cod, dniv_libelle, dieu_nom, dper_niveau, dper_points
		from dieu_perso,dieu_niveau,dieu where dper_perso_cod = ' . $perso_cod  . '
			and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau
			and dieu_cod = dper_dieu_cod ';
	$stmt = $pdo->query($req);
	if ($stmt->rowCount() != 0)
	{
		$result = $stmt->fetch();
		$niveau_actu = $result['dper_niveau'];
		$dieu_perso = $result['dper_dieu_cod'];
	}
    else
    {
    	$niveau_actu = 0;
		$dieu_perso = -1;
    }

	$lieu = $tab_lieu['lieu_cod'];
	$req = "select dieu_cod,dieu_nom,dieu_description,lieu_description,dieu_ceremonie,dieu_pouvoir from dieu,lieu ";
	$req = $req . "where lieu_cod = " . $tab_lieu['lieu_cod']  . " and lieu_dieu_cod = dieu_cod ";
	$stmt = $pdo->query($req);
	$result = $stmt->fetch();
	$dieu_cod = $result['dieu_cod'];
	$dieu_nom = $result['dieu_nom'];
	$dieu_descr = $result['dieu_description'];
	$lieu_descr = $result['lieu_description'];
	$dieu_ceremonie = $result['dieu_ceremonie'];
	$dieu_pouvoir = $result['dieu_pouvoir'];
	switch($methode)
	{
		case 'entree':

			/*	Ajout par Maverick, le 17/02/2011.
				Affichage du niveau de pouvoir (%age).
				Pts pouvoir <= 0 : Calice noir.
				%age de 1 à 10 : Calice rouge.
				%age de 11 à 20 : Calice violer.
				%age de 21 à 40 : Calice bleu foncé.
				%age de 41 à 60 : Calice bleu.
				%age de 61 à 80 : Calice bleu clair.
				%age de 81 à 90 : Calice vert.
				%age de 91 à 100 : Calice jaune.
			*/
			if ($niveau_actu >= 1 && $dieu_perso == $dieu_cod) {
				$calice = G_IMAGES.'calice_'; // URL des images (sous-domaine)
				$nomcalice = '';
				if ($dieu_pouvoir <= 0) {
					$nomcalice = 'noir.png';
					$calice .= $nomcalice;
				} else {
					$req = 'SELECT MAX(dieu_pouvoir) AS dp FROM dieu';
					$stmt = $pdo->query($req);
					$result = $stmt->fetch();
					$max_pouvoir = $result['dp'];
					$pourc = floor( $dieu_pouvoir *100 /$max_pouvoir );
					if ($pourc <= 10) {
						$nomcalice = 'rouge.png';
						$calice .= $nomcalice;
					} elseif ($pourc <= 20) {
						$nomcalice = 'violet.png';
						$calice .= $nomcalice;
					} elseif ($pourc <= 40) {
						$nomcalice = 'bleuf.png';
						$calice .= $nomcalice;
					} elseif ($pourc <= 60) {
						$nomcalice = 'bleum.png';
						$calice .= $nomcalice;
					} elseif ($pourc <= 80) {
						$nomcalice = 'bleuc.png';
						$calice .= $nomcalice;
					} elseif ($pourc <= 90) {
						$nomcalice = 'vert.png';
						$calice .= $nomcalice;
					} else {
						$nomcalice = 'jaune.png';
						$calice .= $nomcalice;
					}
				}
				echo '<div style="float:right;"><table><tr><td colspan=8><img title="Niveau de pouvoir" src="'.$calice.'" /></td></tr><tr>';
				$calices = Array('noir.png','rouge.png','violet.png',
					'bleuf.png','bleum.png','bleuc.png',
					'vert.png','jaune.png');
				for ( $i = 0; $i < sizeof($calices); ++$i)
				{
					echo '<td><img '. ($calices[$i] == $nomcalice?' border=1 ':'') .'width=20 height=25 title=' . ($calices[$i] == $nomcalice?'"Niveau de pouvoir actuel du Dieu"':'"Niveaux de pouvoir, du plus faible au plus fort"') . ' src="'.G_IMAGES.'calice_'.$calices[$i].'" /></td>';
				}
				echo '</tr></table></div>';
			}
			$texte_reliquaire = '<hr /><p><strong>Reliquaire</strong></p>';

			// on cherche d'abord le dieu associé.
			echo "<p><img src=\"../images/temple.png\"><br />
			Vous êtes sur un temple de <strong>$dieu_nom</strong><br></p>";
			echo "<br><br><em>" , $lieu_descr, "</em><br>";
			//on indique le responsable du temple
			$req = 'select tfid_perso_cod, perso_nom from perso, temple_fidele where tfid_perso_cod = perso_cod and tfid_lieu_cod = '. $lieu;
			$stmt = $pdo->query($req);
			if($result = $stmt->fetch())
            {
			?>
                <form name="message" method="post" action="messagerie2.php">
                    <input type="hidden" name="m" value="2">
                    <input type="hidden" name="n_dest" value="<?php echo $result['perso_nom']?>;">
                    <input type="hidden" name="dmsg_cod">
                </form>
                <p>Ce temple est sous la responsabilité de <strong><?php echo $result['perso_nom']?></strong> (<a href="javascript:document.message.submit();">Envoyer un message</a>)</p><br>
			<?php 
            }
			// on regarde s’il existe un lien avec le perso
			$req = 'select dper_dieu_cod,dniv_libelle,dieu_nom,dper_niveau,dper_points
				from dieu_perso,dieu_niveau,dieu where dper_perso_cod = ' . $perso_cod  . '
				and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau
				and dieu_cod = dper_dieu_cod ';
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() == 0)
			{
				// aucun rattachement
				echo "<p>Vous n’êtes fidèle d’aucun dieu</p>";
			}
			else
			{
				$result = $stmt->fetch();
				$perso_dieu_nom = $result['dieu_nom'];
				if ($result['dper_dieu_cod'] == $dieu_cod)
				{
					echo "<p>Vous êtes " . $result['dniv_libelle'] . " de ce dieu</p>";
					$points = $result['dper_points'];
					$niveau_actu = $result['dper_niveau'];
					$cout_pa = $param->getparm(55);
					switch($niveau_actu)
					{
						case 0:
							if ($points > $param->getparm(51))
							{
								$req = "select dniv_libelle from dieu_niveau ";
								$req = $req . "where dniv_dieu_cod = $dieu_cod and dniv_niveau = 1 ";
								$stmt = $pdo->query($req);
								$result = $stmt->fetch();
								echo "<p>Vous pouvez choisir de <a href=\"" , $PHP_SELF , "?methode=grade\">passer <strong>", $result['dniv_libelle'], "</strong></a> ($cout_pa PA).</p><br>";
							}
							break;
						case 1:
							if ($points > $param->getparm(52))
							{
								$req = "select dniv_libelle from dieu_niveau
									where dniv_dieu_cod = $dieu_cod and dniv_niveau = 2 ";
								$stmt = $pdo->query($req);
								$result = $stmt->fetch();
								echo "<p>Vous pouvez choisir de <a href=\"" , $PHP_SELF , "?methode=grade\">passer <strong>", $result['dniv_libelle'], "</strong></a> ($cout_pa PA).</p><br>";
							}
							break;
						case 2:
							if ($points > $param->getparm(53))
							{
								$req = "select dniv_libelle from dieu_niveau
									where dniv_dieu_cod = $dieu_cod and dniv_niveau = 3 ";
								$stmt = $pdo->query($req);
								$result = $stmt->fetch();
								echo "<p>Vous pouvez choisir de <a href=\"" , $PHP_SELF , "?methode=grade\">passer <strong>", $result['dniv_libelle'], "</strong></a> ($cout_pa PA).</p><br>";
							}
							break;
						case 3:
							if ($points > $param->getparm(54))
							{
								$req = "select dniv_libelle from dieu_niveau
									where dniv_dieu_cod = $dieu_cod and dniv_niveau = 4 ";
								$stmt = $pdo->query($req);
								$result = $stmt->fetch();
								echo "<p>Vous pouvez choisir de <a href=\"" , $PHP_SELF , "?methode=grade\">passer <strong>", $result['dniv_libelle'], "</strong></a> ($cout_pa PA).</p><br>";
							}
							break;
					}
					if($niveau_actu >= 2)
					{
						echo '<p><a href="?methode=benediction">Demander une bénédiction (' . $param->getparm(110) . ' PA) </a></p>';
						$texte_reliquaire .= '<p>Votre rang vous autorise à exécuter une cérémonie pour <a href="?methode=relique">bénir et déposer une relique (6 PA)</a></p>';
					}
				}
				else
				{
					echo "<p>Vous êtes " , $result['dniv_libelle'] , " de $perso_dieu_nom</p>";
				}
			}
			// on regarde si on n'est pas rénégat, quand même.
			$req = "select dren_cod from dieu_renegat where dren_dieu_cod = $dieu_cod and dren_perso_cod = $perso_cod and dren_datfin > now()";
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() != 0)
			{
				// RENEGAT !!!
				echo "<p>Vous êtes <strong>renégat</strong> !! Inutile de s’attarder en ce lieu, $dieu_nom ne veut même pas entendre parler de vous !</p>";
			}
			else
			{
				?>
				<form method="post" name="dieu_desc" action="<?php echo $PHP_SELF;?>">
				<input type="hidden" class="vide" name="dieu_desc" value="<?php echo $dieu_cod;?>">

				<p><a href="<?php echo $PHP_SELF;?>?methode=prie1">- Je voudrais me recueillir pour prier <?php echo $dieu_nom; ?>
				</a> (<?php  echo $param->getparm(48); ?> PA)
				<p><a href="<?php echo $PHP_SELF;?>?methode=ceremonie">- J’aimerais organiser une <?php echo $dieu_ceremonie; ?> en l’honneur de <?php echo $dieu_nom; ?>
					</a> (<?php  echo $param->getparm(49), " PA, ", $param->getparm(50) , " brouzoufs )"; ?>
				<p><a href="<?php echo $PHP_SELF;?>?methode=infos&javascript:document.dieu_desc.submit();">- Pouvez-vous m’en dire plus sur <?php echo $dieu_nom; ?> ?</a>
				<p><a href="expl_dieu.php">- Pouvez-vous m’apprendre ce que vous savez sur les dieux ?</a>
				</form>
				<?php 
				$cout_or = $param->getparm(50) * 2;
                // On regarde si le perso possède déjà un familier/esprit
                $req = "SELECT count(*) count FROM perso_familier INNER JOIN perso ON perso_cod=pfam_familier_cod WHERE pfam_perso_cod = {$perso_cod} AND perso_actif='O' ";
                $stmt = $pdo->query($req);
                $result = $stmt->fetch();
                $count_familier_actif = $result['count'] ;

				if ($count_familier_actif == 0 and $dieu_perso == $dieu_cod and $niveau_actu >= 3 and $dieu_pouvoir > 150)
				{
                    // On regarde si le perso possède déjà un familier/esprit décédé, proposer une résu
                    // le familier doit être de la même foi (même dieu) et avoir encore des point de foi (ne pas être décédé à cause sa foi)
                    $req = "SELECT count(*) count 
                            FROM perso_familier 
                            INNER JOIN perso fam on fam.perso_cod=pfam_familier_cod
                            INNER JOIN dieu_perso dieu_fam on dieu_fam.dper_perso_cod=pfam_familier_cod
                            INNER JOIN perso maitre on maitre.perso_cod=pfam_perso_cod
                            INNER JOIN dieu_perso dieu_maitre on dieu_maitre.dper_perso_cod=pfam_perso_cod
                            WHERE pfam_perso_cod = {$perso_cod} AND fam.perso_actif='N' AND fam.perso_gmon_cod=441 AND dieu_fam.dper_points>0 AND dieu_maitre.dper_dieu_cod=dieu_fam.dper_dieu_cod ";
                    $stmt = $pdo->query($req);
                    $result = $stmt->fetch();
                    $count_familier_mort = $result['count'] ;
                    if ($count_familier_mort > 0 )
                    {
                        ?>
                        <p><a href="<?php echo $PHP_SELF;?>?methode=resu">- Ressusciter un esprit de <?php echo $dieu_nom; ?></a> (<?php  echo $param->getparm(100); ?> PA, <?php  echo $cout_or;?> brouzoufs)
                        <br><em>Ceci rapellera du plan des morts le familier de <?php echo $dieu_nom; ?> qui agira sous votre contrôle comme cela l'était avant sa mort.
                        <br>N’oubliez pas que cette action <strong>éprouve fortement</strong> le pouvoir de votre Dieu</em><br>

                        <?php
                    }

					?>
					<p><a href="<?php echo $PHP_SELF;?>?methode=invoc">- Invoquer un esprit de <?php echo $dieu_nom; ?></a> (<?php  echo $param->getparm(100); ?> PA, <?php  echo $cout_or;?> brouzoufs)
						<br><em>Ceci invoque un familier de <?php echo $dieu_nom; ?> qui ne pourra agir que de manière temporaire sous votre contrôle.
						<br><strong>Il disparaîtra ensuite avec toutes ses possessions.</strong>
						<br>N’oubliez pas que cette action <strong>éprouve fortement</strong> le pouvoir de votre Dieu</em><br>

					<?php 
				}
				if ($dieu_perso == $dieu_cod and $niveau_actu >= 3)
				{
						?>
						<p><a href="<?php echo $PHP_SELF;?>?methode=tel1">Se téléporter vers un autre temple ?</a> (<?php  echo $param->getparm(100); ?> PA)
						<?php 
				}
			}
			
			// Ajout des options si c’est un lieu avancé
			if (isset($tab_lieu['evo_url']) && file_exists($tab_lieu['evo_url']) )
				include_once $tab_lieu['evo_url'];
			
			//On insère toute la partie qui concerne les quêtes des temples
			include_once "quete.php";

			// on termine par le reliquaire
			echo $texte_reliquaire;
			$sql_reliquaire = "SELECT DISTINCT obj_cod, obj_nom, obj_description
				FROM lieu cetemple
				INNER JOIN lieu toustemples ON toustemples.lieu_tlieu_cod = cetemple.lieu_tlieu_cod AND toustemples.lieu_dieu_cod = cetemple.lieu_dieu_cod
				INNER JOIN stock_magasin ON mstock_lieu_cod = toustemples.lieu_cod
				INNER JOIN objets ON obj_cod = mstock_obj_cod
				WHERE cetemple.lieu_cod = $lieu
				ORDER BY obj_nom";
			$stmt = $pdo->query($sql_reliquaire);
			if ($stmt->rowCount() != 0)
			{
				?>
				<script type='text/javascript'>
					function permutterDescription(obj_cod)
					{
						var longue = document.getElementById('plus' + obj_cod);
						var courte = document.getElementById('moins' + obj_cod);
						if (longue.style.display == 'none')
						{
							longue.style.display = 'block';
							courte.style.display = 'none';
						}
						else
						{
							courte.style.display = 'block';
							longue.style.display = 'none';
						}
					}
				</script>
				<?php 				echo '<p>Voici les objets sacrés exposés dans le reliquaire du temple :</p>';
				echo '<table><tr><th class="titre"><strong>Relique</strong></th><th class="titre">Description</th></tr>';
				while ($result = $stmt->fetch())
				{
					$description_longue = $result['obj_description'];
					$description_courte = $description_longue;
					$objet = $result['obj_cod'];
					if (strlen($description_longue) > 100)
						$description_courte = substr($description_longue, 0, 100) . "... <a href='javascript:permutterDescription($objet);'>(Voir plus)</a>";
					echo '<tr><td class="soustitre2">' . $result['obj_nom'] . '</td>';
					echo "<td class='soustitre2'><div style='display:none;' id='plus$objet'>" . nl2br($description_longue) . "<br /><a href='javascript:permutterDescription($objet);'>(Réduire)</a></div>
						<div style='display:block;' id='moins$objet'>" . nl2br($description_courte) . "</div></td></tr>";
				}
				echo '</table>';
			}
			else
			{
				echo '<p>Le reliquaire ne contient pour le moment aucun objet digne d’intérêt.</p>';
			}
		break;

		case 'relique':
			if($niveau_actu >= 2 && $dieu_perso == $dieu_cod)
			{
				echo '<p><strong>Reliquaire</strong></p><br /><br />';
				echo '<p>Choisissez l’objet que vous allez confier à votre dieu</p>';
				echo '<p><strong>Attention !</strong> Cette action est irréversible ! L’objet ne pourra pas être repris au dieu par des moyens conventionnels. Il restera en vitrine <em>ad vitam</em>.</p>';
				echo '<form method="POST"><input type="hidden" name="methode" value="valider_relique" />';
				echo '<select name="relique_cod"><option value="-1">Choisissez l’objet à confier à votre dieu</option>';
				$req_relique = "select obj_nom, min(obj_cod) as obj_cod from objets
					inner join perso_objets on perobj_obj_cod = obj_cod
					inner join objet_generique on gobj_cod = obj_gobj_cod
					where perobj_perso_cod = $perso_cod
					group by obj_nom
					order by obj_nom";
				$stmt = $pdo->query($req_relique);
				while ($result = $stmt->fetch())
				{
					$relique_cod = $result['obj_cod'];
					$relique_nom = $result['obj_nom'];
					echo "<option value='$relique_cod'>$relique_nom</option>";
				}
				echo '</select><input type="submit" class="test" value="Effectuer la cérémonie de remise (6 PA)" /></form>';
			}
			else
			{
				echo '<p>Ce dieu ne vous fait pas suffisamment confiance pour accepter un de vos objets.</p>';
			}
		break;

		case 'valider_relique':
			$erreur = 0;
			$relique_nom = "";
			if($niveau_actu < 2 || $dieu_perso != $dieu_cod)
			{
				echo "<p>$dieu_nom vous regarde... puis va s’occupe d’un prêtre plus méritant, vous laissant seul avec votre relique.</p>";
				$erreur = 1;
			}

			$req_verif = "select perso_pa from perso where perso_cod = $perso_cod";
			$stmt = $pdo->query($req_verif);
			$result = $stmt->fetch();
			if ($result['perso_pa'] < 6)
			{
				echo "<p>$dieu_nom regarde cet énergumène essoufflé, vociférer en agitant une breloque... Non, mais soyez une sérieux, une cérémonie se fait avec 6 PA, pas moins !</p>";
				$erreur = 1;
			}

			$req_verif = "select obj_nom from objets
				inner join perso_objets on perobj_obj_cod = obj_cod
				where perobj_perso_cod = $perso_cod
					and obj_cod = $relique_cod";
			$stmt = $pdo->query($req_verif);
			if ($stmt->rowCount() == 0)
			{
				echo "<p>$dieu_nom regarde cet énergumène s’agiter les mains vides... Non, mais soyez une sérieux, il ne vous manque pas la relique, là ?</p>";
				$erreur = 1;
			}
			else
			{
				$result = $stmt->fetch();
				$relique_nom = $result['obj_nom'];
			}

			if ($erreur == 0)
			{
				echo "<p>$dieu_nom accepte volontiers votre don de ce(tte) superbe $relique_nom.<br />La cérémonie et l’objet lui font gagner de la puissance, dont il vous reverse une part.</p>";

				// Équivalent d’une prière
				$req = "select prie_dieu($perso_cod, $dieu_cod) ";
				$stmt = $pdo->query($req);

				// Ajout de points supplémentaires au dieu
				$req = "update dieu set dieu_pouvoir = dieu_pouvoir + $niveau_actu where dieu_cod = $dieu_cod";
				$stmt = $pdo->query($req);

				// Mise à jour du reliquaire
				$req = "insert into stock_magasin(mstock_lieu_cod, mstock_obj_cod) values ($lieu, $relique_cod) ";
				$stmt = $pdo->query($req);
				
				// Suppression de l’objet de l’inventaire
				$req = "delete from perso_objets where perobj_obj_cod = $relique_cod ";
				$stmt = $pdo->query($req);
				
				// Suppression de 2 PA (4 déjà enlevés par la prière)
				$req = "update perso set perso_pa = perso_pa - 2 where perso_cod = $perso_cod ";
				$stmt = $pdo->query($req);
			}
			echo '<hr /><a href="lieu.php">Retour au temple</a>';
		break;

		case 'resu':
			if($dieu_perso != $dieu_cod)
			{
				echo "Vous ne pouvez lancer des invocations que dans le temple de votre divinité.";
				break;
			}
			if ($niveau_actu >= 3 and $dieu_pouvoir > 150)
			{
				echo "Votre Dieu entend votre demande.<br>";
			}
			else
			{
				echo "Vous n’êtes pas d’un niveau suffisant ou votre Dieu ne possède pas assez de pouvoir.<br>";
				break;
			}
			/* on regarde s'il n'y a pas déjà un familier*/
			$req = "select pfam_familier_cod from perso_familier,perso
				where  pfam_familier_cod = perso_cod
					and perso_actif in ('O','H')
					and pfam_perso_cod = ". $perso_cod;
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() != 0)
			{
				echo "<br><strong><p>Vous ne pouvez pas ressuciter un familier esprit ici. Vous êtes déjà en charge d’un autre familier, deux seraient trop à gérer.</p></strong><br>";
				break;
			}

            /* on regarde s'il n'y a bien un familier à ressuciter (du même dieu, on ne ressuscite pas les impies) */
            $req = "SELECT count(*) count 
                    FROM perso_familier 
                    INNER JOIN perso fam on fam.perso_cod=pfam_familier_cod
                    INNER JOIN dieu_perso dieu_fam on dieu_fam.dper_perso_cod=pfam_familier_cod
                    INNER JOIN perso maitre on maitre.perso_cod=pfam_perso_cod
                    INNER JOIN dieu_perso dieu_maitre on dieu_maitre.dper_perso_cod=pfam_perso_cod
                    WHERE pfam_perso_cod = {$perso_cod} AND fam.perso_actif='N' AND fam.perso_gmon_cod=441 AND dieu_fam.dper_points>0 AND dieu_maitre.dper_dieu_cod=dieu_fam.dper_dieu_cod ";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $count_familier_mort = $result['count'] ;
            if ($count_familier_mort == 0)
            {
                echo "<br><strong><p>la résurection n'est pas possible, l'ame de votre familier n'a pas été retrouvée.</p></strong><br>";
                break;
            }

			/* contrôle sur les pa disponible*/
			$req = "select perso_pa,perso_po from perso
				where perso_cod = ". $perso_cod;
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$pa = $result['perso_pa'];
			$or = $result['perso_po'];
			$cout_pa = $param->getparm(100);
			$cout_or = $param->getparm(50) * 2;
			if ($pa < $cout_pa)
			{
					echo "<br><strong><p>Vous n’avez pas suffisamment de PA pour cette action</strong><br><br>";
					break;
			}
			else if ($or < $cout_or)
			{
					echo "<br><strong><p>Vous n’avez pas suffisamment de brouzoufs pour cette action</strong><br><br>";
					break;
			}
			else
			{
				$req = "update perso set perso_pa = perso_pa - ". $cout_pa .",perso_po = perso_po -  ". $cout_or ."
						where perso_cod = ". $perso_cod;
				$stmt = $pdo->query($req);
			}
			/* on ressucite le familier*/
			$req = "select ressuscite_familier_divin($perso_cod) as resultat";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$resultat = explode(';', $result['resultat']);
			if ($resultat[0] == '1')
			{
				echo '<p>' . $resultat[1] . '</p>';
			}
			else
			{
				echo "<br>Vous avez retrouvé votre familier <strong>esprit de $dieu_nom</strong>.
					Sa longévité tiendra à votre foi : quand son énergie divine tombera à 0, il sera renvoyé au royaume de $dieu_nom.";
			}
			/* On diminue le compteur de la religion*/
			$req = "update dieu set dieu_pouvoir = dieu_pouvoir - 150 where dieu_cod = ". $dieu_cod;
			$stmt = $pdo->query($req);
		break;
		
		case 'invoc':
			if($dieu_perso != $dieu_cod)
			{
				echo "Vous ne pouvez lancer des invocations que dans le temple de votre divinité.";
				break;
			}
			if ($niveau_actu >= 3 and $dieu_pouvoir > 150)
			{
				echo "Votre Dieu entend votre demande.<br>";
			}
			else
			{
				echo "Vous n’êtes pas d’un niveau suffisant ou votre Dieu ne possède pas assez de pouvoir.<br>";
				break;
			}
			/* on regarde s'il n'y a pas déjà un familier*/
			$req = "select pfam_familier_cod from perso_familier,perso
				where  pfam_familier_cod = perso_cod
					and perso_actif in ('O','H')
					and pfam_perso_cod = ". $perso_cod;
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() != 0)
			{
				echo "<br><strong><p>Vous ne pouvez pas récupérer un familier esprit ici. Vous êtes déjà en charge d’un autre familier, deux seraient trop à gérer.</p></strong><br>";
				break;
			}
			/* contrôle sur les pa disponible*/
			$req = "select perso_pa,perso_po from perso
				where perso_cod = ". $perso_cod;
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$pa = $result['perso_pa'];
			$or = $result['perso_po'];
			$cout_pa = $param->getparm(100);
			$cout_or = $param->getparm(50) * 2;
			if ($pa < $cout_pa)
			{
					echo "<br><strong><p>Vous n’avez pas suffisamment de PA pour cette action</strong><br><br>";
					break;
			}
			else if ($or < $cout_or)
			{
					echo "<br><strong><p>Vous n’avez pas suffisamment de brouzoufs pour cette action</strong><br><br>";
					break;
			}
			else
			{
				$req = "update perso set perso_pa = perso_pa - ". $cout_pa .",perso_po = perso_po -  ". $cout_or ."
						where perso_cod = ". $perso_cod;
				$stmt = $pdo->query($req);
			}
			/* on créé le familier*/
			$req = "select ajoute_familier(441, $perso_cod) as resultat";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$resultat = explode(';', $result['resultat']);
			if ($resultat[0] == '1')
			{
				echo '<p>' . $resultat[1] . '</p>';
			}
			else
			{
				echo "<br>Vous êtes maintenant en possession d’un magnifique <strong>esprit de $dieu_nom</strong>.
					Sa longévité tiendra à votre foi : quand son énergie divine tombera à 0, il sera renvoyé au royaume de $dieu_nom.";
			}
			/* On diminue le compteur de la religion*/
			$req = "update dieu set dieu_pouvoir = dieu_pouvoir - 150 where dieu_cod = ". $dieu_cod;
			$stmt = $pdo->query($req);
		break;

		case 'prie1':
			$attention = 0;
			$req = "select dper_dieu_cod,dniv_libelle from dieu_perso,dieu_niveau where dper_perso_cod = $perso_cod ";
			$req = $req . "and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau ";
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() != 0)
			{
				$result = $stmt->fetch();
				if ($result['dper_dieu_cod'] != $dieu_cod)
				{
					$attention = 1;
				}
			}
			if ($attention == 0)
			{
				echo "<p>Vous vous apprêtez à prier <strong>$dieu_nom</strong><br>";
			}
			else
			{
				echo "<p>Vous êtes " , $result['dniv_libelle'] , " d’un autre dieu.<br>";
			}
			?>
			<a href="action.php?methode=prie&dieu=<?php echo $dieu_cod; ?>">Continuer ?</a>
			<?php 
		break;

		case 'ceremonie':
			$attention = 0;
			$req = "select dper_dieu_cod,dniv_libelle from dieu_perso,dieu_niveau where dper_perso_cod = $perso_cod ";
			$req = $req . "and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau ";
			$stmt = $pdo->query($req);
			if ($stmt->rowCount() != 0)
			{
				$result = $stmt->fetch();
				if ($result['dper_dieu_cod'] != $dieu_cod)
				{
					$attention = 1;
				}
			}
			if ($attention == 0)
			{
				echo "<p>Vous vous apprêtez à organiser une cémémonie pour <strong>$dieu_nom</strong><br>";
			}
			else
			{
				echo "<p>Vous êtes " , $result['dniv_libelle'] , " d’un autre dieu.<br>";
			}
			?>
			<a href="action.php?methode=ceremonie&dieu=<?php echo $dieu_cod; ?>">Continuer ?</a>
			<?php 
		break;

		case 'infos':
			$req = "select dieu_cod,dieu_nom,dieu_description from dieu
								where dieu_cod = $dieu_cod ";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$dieu_descr = $result['dieu_description'];
			echo "<br><em>" , $dieu_descr, "</em><br><br>";
			echo '<a href="' . $PHP_SELF . '?methode=entree"">Retour</a><br>';
		break;

		case 'grade':
			$req = "select dper_niveau from dieu_perso,dieu_niveau,dieu where dper_perso_cod = $perso_cod ";
			$req = $req . "and dniv_dieu_cod = dper_dieu_cod and dniv_niveau = dper_niveau ";
			$req = $req . "and dieu_cod = dper_dieu_cod ";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			$niveau = $result['dper_niveau'] + 1;
			$req = "select dniv_libelle from dieu_niveau ";
			$req = $req . "where dniv_dieu_cod = $dieu_cod and dniv_niveau = $niveau ";
			$stmt = $pdo->query($req);
			$result = $stmt->fetch();
			echo "<p>Vous vous apprêtez à devenir <strong>", $result['dniv_libelle'] ,"</strong><br>";
			switch($niveau)
			{
				case 1:
				// novice
					?>
					<p>En devenant novice, vous acceptez de rentrer dans la religion. Vous commencez à porter les signes distinctifs liés à votre religion (les autres joueurs auront donc dans la vue votre grade et votre divinité).<br>
					Vous gagnez la confiance de votre dieu, qui vous offre la possibilité de l’invoquer pour un sort mineur. Au moment ou vous voulez lancer ce sort, si votre dieu possède assez de puissance, il le lancera pour vous (pas de jet de dés et pas de gain d’expérience sur les sorts divins). <br><br>
					<?php 
				break;
				case 2:
				// initié
					?>
					<p>En devenant <?php  echo $result['dniv_libelle']; ?>, vous faites un pas de plus dans la religion. Vous acceptez votre divinité pour seule et unique source de puissance.<br>
					Vous gagnez de nouveaux sorts divins, mais en retour, il vous est impossible de lancer des sorts mémorisés. Vous pouvez toujours utiliser les runes pour lancer des sorts non divins, mais vous ne faites pas de jet de mémorisation. Votre capacité de mémorisation n’augmentera pas pour ces sorts.<br>
					À partir de ce rang, vous avez la possibilité de prier votre divinité en extérieur, et non plus seulement dans un temple.<br>
					<?php 
				break;
				case 3:
				// clerc
					?>
					<p>Le grade de <?php  echo $result['dniv_libelle']; ?> marque un tournant dans la religion. La confiance entre vous et votre dieu est maintenant totalement réciproque. C’est pour cela que votre dieu vous permet à tout moment d’avoir accès à la liste des personnes renégates.<br>
					Votre dieu attend de vous une obéissance totale et aveugle, ainsi qu’un investissment total. Il vous offre des sorts de plus en plus puissants. Toutefois, du fait de votre charge, chaque tour passé sans prier vous fait oublier de votre divinité. Cet oubli n’est pas suffisant pour vous faire descendre de grade, mais il peut sérieusement retarder votre accession au grade suivant.<br>
					<br><em>Attention ! Pas encore codé !</em>
					<?php 
				break;
				case 4:
				//prêtre
					?>
					<p>À partir de <?php  echo $result['dniv_libelle']; ?>, en cas de mort, le corps n’est plus ramené dans un dispensaire, mais dans un temple de votre dieu, le plus proche de vous.
					<br><em>Attention ! Pas encore codé !</em>
					<?php 
				break;
				case 5:
				// grand prêtre
					?>
					<p>À partir de <?php  echo $result['dniv_libelle']; ?>, votre dieu vous autorise à vous téléporter sur chaque temple qui lui est dévoué (sous réserve qu’il ait assez d’énergie bien sur). Par contre, vous n’avez plus aucun accès à la magie runique.
					<br><em>Attention ! Pas encore codé !</em>
					<?php 
				break;
			}
			// sorts gagnés
			echo "<p><strong>Sorts disponibles en étant " , $result['dniv_libelle'] , "</strong> :<br>";
			$req = "select sort_cod,sort_nom from sorts, dieu_sorts ";
			$req = $req . "where dsort_dieu_cod = $dieu_cod ";
			$req = $req . "and dsort_niveau <= $niveau ";
			$req = $req . "and dsort_sort_cod = sort_cod ";
			$stmt = $pdo->query($req);
			while($result = $stmt->fetch())
			{
				echo "<a href=\"visu_desc_sort.php?sort_cod=" ,$result['sort_cod'] , "\">" , $result['sort_nom'], "</a><br>";
			}
			echo "<br><a href=\"action.php?methode=dgrade&dieu=$dieu_cod\">Continuer ? </a>";
		break;

		case "tel1":
			// liste des temples visités
			$cout_pa = $param->getparm(100);
			$req = 'select * from choix_temple_vus(' . $perso_cod . ')';
			$stmt = $pdo->query($req);
			

			?>
			<p class="titre">Temples déjà visités :</p>
			<table>
			<?php 
			while($result = $stmt->fetch())
			{
				$req = 'select  lieu_nom,pos_x,pos_y,etage_libelle
					from lieu,lieu_position,positions,etage
					where pos_cod = ' . $db->f(0) . '
					and lpos_pos_cod = pos_cod
					and lpos_lieu_cod = lieu_cod
					and etage_numero = pos_etage ';
				$stmt2 = $pdo->query($req);
				$result2 = $stmt2->fetch();
				?>
				<tr><td class="soustitre2"><?php echo $result2['lieu_nom'];?></td>
					<td><?php echo $result2['pos_x'];?>, <?php echo $result2['pos_y'];?>, <?php echo $result2['etage_libelle'];?></td>
					<td class="soustitre2">
					<?php 
					if ($tab_lieu['pos_cod'] != $db->f(0))
					{
						?>
						<a href="action.php?methode=teld&pos=<?php echo $db->f(0);?>">Téléportation (<?php  echo $cout_pa; ?> PA)</a>
						<?php 
					}
					?>
					</td></tr>
				<?php 
			}
			?>
			</table>
			<?php 
		break;

		case 'benediction':
			if($niveau_actu >= 2)
			{
				//
				// on commence par regarder si le dieu en question a assez de puissance pour accorder la bénédiction
				//
				$points_ben = ($niveau_actu - 2)*($niveau_actu - 2);
				if($points_ben < 2)
					$points_ben = 2;
				if($dieu_pouvoir < $points_ben)
				{
					echo '<p>Votre Divinité n’a pas assez de puissance pour exaucer votre souhait !';
					break;
				}
				//
				// on fait quand même un contrôle de cohérence divinité/perso
				//
				if($dieu_perso != $dieu_cod)
				{
					echo "Vous ne pouvez demander des bénédictions que dans le temple de votre divinité.";
					break;
				}
				//
				// on contrôle les PA
				//
				$req = 'select perso_pa from perso where perso_cod = ' . $perso_cod;
				$stmt = $pdo->query($req);
				$result = $stmt->fetch();
				$pa = $result['perso_pa'];
				if($pa < $param->getparm(110))
				{
					echo "<p>Vous n’avez pas assez de PA pour cette action.";
					break;

				}
				//
				// à partir d'ici, tout devrait être bon.
				//
				$nb_tours = $niveau_actu * $niveau_actu;
				// on commence par enlever les points au dieu
				$req = "update dieu set dieu_pouvoir = dieu_pouvoir - " . $points_ben . " where dieu_cod = ". $dieu_cod;
				$stmt = $pdo->query($req);
				// on enlève les PA
				$req = 'update perso set perso_pa = perso_pa - getparm_n(110) where perso_cod = ' . $perso_cod;
				$stmt = $pdo->query($req);
				// et maintenant, on va switcher par rapport à la divinité.
				switch($dieu_cod)
				{
					case 1: // IO l'aveugle => chant du barde
						$req = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
						$stmt = $pdo->query($req);
						$req = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -1)";
						$stmt = $pdo->query($req);
						$req = "select ajoute_bonus($perso_cod,'DEG', $nb_tours, 2)";
						$stmt = $pdo->query($req);
						$texte = "Io vous a entendu, vous vous sentez plus habile au combat.";
						break;
					case 2: // Balgur => soins importants
						$req = "select perso_pv,perso_pv_max from perso
							where perso_cod = " . $perso_cod;
						$stmt = $pdo->query($req);
						$result = $stmt->fetch();
						$v_pv = $result['perso_pv'];
						$v_pv_max = $result['perso_pv_max'];
						$pv_gagne = 0;
						for ($i=0;$i<8;$i++)
						{
							$req = "select lancer_des(1,4) as resultat";
							$stmt = $pdo->query($req);
							$result = $stmt->fetch();
							$pv_gagne += $result['resultat'];
						}
						$tot_pv =  $v_pv + $pv_gagne;
						if($tot_pv > $v_pv_max)
							$tot_pv = $v_pv_max;
						$req = "update perso set perso_pv = " . $tot_pv . " where perso_cod = " . $perso_cod;
						$stmt = $pdo->query($req);
						$texte = "Balgur soigne vos blessures... ";
						break;
					case 3: // Galthée => reconstruction intense
						$req = "select ajoute_bonus($perso_cod,'REG', $nb_tours, 10)";
						$stmt = $pdo->query($req);
						$texte = "Dans sa grande bonté, Galthée aide votre corps à se reconstruire.";
						break;
					case 4: // Elian => sort de défense
						$req = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 2)";
						$stmt = $pdo->query($req);
						$texte = "Elian rend votre corps plus résistant aux blessures.";
						break;
					case 5: // Apiera => armure dure
						$req = "select ajoute_bonus($perso_cod,'ARM', $nb_tours, 5)";
						$stmt = $pdo->query($req);
						$texte = "Apiera rend votre corps plus résistant aux blessures.";
						break;
					case 7:	// FAlis => Fou furieux
						$req = "select ajoute_bonus($perso_cod,'PAA', $nb_tours, -3)";
						$stmt = $pdo->query($req);
						$texte = "Falis vous entend, et répond à votre demande de bénédiction. Vous vous sentez plus rapide au combat.";
						break;
					case 8: // Ecatis => Mange ta soupe
						$req = "select ajoute_bonus($perso_cod,'TOU', $nb_tours, 25)";
						$stmt = $pdo->query($req);
						$texte = "Ecatis vous a entendu, vous vous sentez plus habile au combat.";
						break;
					case 9: // Tonto => Danse de Saint Guy
						$req = "select ajoute_bonus($perso_cod,'ESQ', $nb_tours, 25)";
						$stmt = $pdo->query($req);
						$req = "select ajoute_bonus($perso_cod,'DSG', $nb_tours, 20)";
						$stmt = $pdo->query($req);
						$texte = "Tonto vous enseigne la technique de l'homme ivre.";
						break;
				}
				echo $texte;
			}
		break;
	}
}

?>

