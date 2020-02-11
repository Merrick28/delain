<?php 
$param = new parametres();
$req_comp = "select pcomp_modificateur,pcomp_pcomp_cod from perso_competences
	where pcomp_perso_cod = $perso_cod
		and pcomp_pcomp_cod in (97,100,101)";
$stmt = $pdo->query($req_comp);
if($result = $stmt->fetch())
{
	$niveau = $result['pcomp_pcomp_cod'];
	if ($niveau == 100 or $niveau == 101)
	{
		$pa = $param->getparm(107);
	}
	else
	{
		$pa = $param->getparm(108);
	}
	if(!isset($methode))
	{
	$methode = "debut";
	}
	if($methode == 'detecter3' and $niveau != 101)
	{
			$methode = "debut";
	}
	switch($methode)
	{
		case "debut":
			$contenu_page .= '
				<p align="center"><br>Vous avez la possibilité de détecter des composants. Plusieurs méthodes se présentent à vous :<br>
				<br><strong><em>Attention, un seul composant par position vous sera présenté, mais en cherchant, vous pourrez parfois en trouver de plusieurs sortes</em></strong>
				<form method="post" action="' . $PHP_SELF. '">
				<br><p align="left" class="soustitre2"> <strong>Détection simple.</strong></p>
				Elle ne vous permettra que de tenter de regarder la présence de composants sur votre propre position<br>
				<input type="hidden" name="methode" value="detecter1">
				<input type="hidden" name="tpot" value="' . $tpot . '">
				<br><input type="submit" value="Rechercher (0 PA)"  class="test">
				</form><br>
				<form method="post" action="' . $PHP_SELF. '">
				<br><p align="left" class="soustitre2"> <strong>Recherche avancée.</strong></p>
				Cette recherche vous permettra de tenter de regarder la présence de composants à une case autour de vous.<br>
				<input type="hidden" name="methode" value="detecter2">
				<input type="hidden" name="tpot" value="' . $tpot . '">
				<br><input type="submit" value="Rechercher ('. $pa .' PA)"  class="test">
				</form><br>';
			if ($niveau == 101)
			{
				$contenu_page .= '<form method="post" action="' . $PHP_SELF. '">
					<br><p align="left" class="soustitre2"> <strong>Recherche poussée.</strong></p>
					Elle vous permettra de scruter une zone à deux positions ou moins autour de vous.<br>
					<input type="hidden" name="methode" value="detecter3">
					<input type="hidden" name="tpot" value="' . $tpot . '">
					<br><input type="submit" value="Rechercher (8PA)"  class="test">
					</form><br>
					</p>
					<br>';
			}
		break;

		case "detecter1":
			$contenu_page .= '<p>Vous observez ce qui pourrait se cacher sur votre propre position.</p>';
			$req_position = "select pos_x,pos_y,pos_etage,ingrpos_qte,ingrpos_gobj_cod,gobj_nom
				from perso_position,positions,ingredient_position,objet_generique
				where ppos_perso_cod = $perso_cod
					and ppos_pos_cod = pos_cod
					and ingrpos_pos_cod = pos_cod
					and gobj_cod = ingrpos_gobj_cod";
			$stmt = $pdo->query($req_position);
			if($stmt->rowCount() == 0)
			{
				$contenu_page .= 'Votre position ne semble pas vraiment constituer un lieu idéal pour récolter des composants.<br>';
				break;
			}

			else
			{
				while($result = $stmt->fetch())
				{
					$contenu_page .= '<br>Votre recherche pourrait s’avérer fructueuse ! Ce lieu est propice à la découverte de composants. Mais quelqu’un aura peut-être déjà fait sa propre récolte en ces lieux ...<br>';
				}
			}
			$contenu_page .= '<br><p><a href="' .$PHP_SELF . '?tpot=' . $tpot . '">Retour à la détection</a>';
		break;

		case "detecter2":
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$stmt = $pdo->query($req_pa);
			$result = $stmt->fetch();
			if ($result['perso_pa'] < $pa)
			{
				$contenu_page .= 'Vous n’avez pas assez de PA !';
				break;
			}
			else
			{
				$req_enl_pa = "update perso set perso_pa = perso_pa - $pa,
					perso_renommee_artisanat = perso_renommee_artisanat + 0.1 where perso_cod = $perso_cod";
				$stmt = $pdo->query($req_enl_pa);
			}
			$contenu_page .= '<p>Vous observez ce qui pourrait se cacher dans les alentours</p>
				<center><table background="../../images/fond5.gif" border="0" cellspacing="1" cellpadding="0">';
			// POSITION DU JOUEUR
			$req_position = "select pos_x,pos_y,pos_etage
				from perso_position,positions
				where ppos_perso_cod = $perso_cod
					and ppos_pos_cod = pos_cod ";
			$stmt = $pdo->query($req_position);
			$result = $stmt->fetch();
			$perso_pos_x = $result['pos_x'];
			$perso_pos_y = $result['pos_y'];
			$perso_pos_etage = $result['pos_etage'];
			$contenu_page .= '<br><br>Table de description des composants présents :<br>
				<table border="1">
				<tr>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#008080"></div></td><td>Pissenlit de vin</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FFFF99"></div></td><td>Pomme</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FFFF00"></div></td><td>Mandragore</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FFCCCC"></div></td><td>Absinthe</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#9933CC"></div></td><td>Ache Des Marais</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FF33CC"></div></td><td>Aconit</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#66FFCC"></div></td><td>Bardane</td>
				</tr>
				<tr>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FF8000"></div></td><td>Léonine sucrée</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#9966FF"></div></td><td>Belladone</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#660000"></div></td><td>Chene Rouvre</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FF0000"></div></td><td>Alkegenge</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#66FF00"></div></td><td>Digitale</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#006600"></div></td><td>Pavot</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#CCFFFF"></div></td><td>Serpolet</td>
				</tr>
				<tr>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#808080"></div></td><td>Herbe de Lune</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#0000FF"></div></td><td>Menthe</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#000066"></div></td><td>Millepertuis</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#CCFF66"></div></td><td>Noyer</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#003333"></div></td><td>Gentiane</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#C0C0C0"></div></td><td>Jusquiame</td>
					<td width="20" height="20"><div style="width:25px;height:25px">
						<table  border="0">
							<tr><td width="10" height="10" style="background:#66FFFF"></td><td width="10" height="10"  style="background:#663399"></td></tr>
							<tr><td width="10" height="10"  style="background:#663399"></td><td width="10" height="10"  style="background:#66FFFF"></td></tr>
						</table>
					</td><td>Plusieurs composants</td>
				</tr>
				</table><hr>';

			$contenu_page .= '<table border="1">';
			$req_position = "select pos_x,pos_y,pos_etage
				from perso_position,positions
				where ppos_perso_cod = $perso_cod
					and ppos_pos_cod = pos_cod ";
			$stmt = $pdo->query($req_position);
			$result = $stmt->fetch();
			$position_x = $result['pos_x'];
			$position_y = $result['pos_y'];
			for ($y=-2; $y<4; $y++)
			{
				$contenu_page .= '<TR>';
				for ($x=-2; $x<4; $x++)
				{
					if(($y*$y + $x*$x) < 8)
					{
						if($y == 0 && $x == 0)
							$image = "<img height=\"13\" src=\"../../images/rond.gif\">";
						else
							$image = "";

						$req_position = "select pos_cod,pos_x,pos_y from positions
							where pos_etage = $perso_pos_etage
								and pos_x = $position_x + $x
								and pos_y = $position_y - $y";
						$stmt = $pdo->query($req_position);
						$positionExistante = false;
						// on vérifie l'existence de la postion dans l'étage
						if($result = $stmt->fetch())
						{
							// nextRecord renvoie true la position existe
							$position = $result['pos_cod'];
							
							$req_ingredient = "select ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea from ingredient_position where ingrpos_pos_cod = $position";
							$stmt2 = $pdo->query($req_ingredient);
							$nbCouleurs = $stmt2->rowCount();
							$positionExistante = true;
						}
						else
						{
							// nexrecord renvoie false, on affiche un mur noir
							$nbCouleurs = 0;
						}
						/*
						#6600FF : bleu utilisé pour la sélection d'une case
						#66FF00 : vert
						#FFFF00 : jaune
						#FF00FF : rose
						#66FFFF : turquoise
						#663399 : violet
						#FFCC00 : orange
						#660000 : marron foncé
						#99FF99 : vert pale
						#C0C0C0 : gris
						#CCCC00 : caca d'oie
						#FFCCFF : rose pale
						
						/* Tableau des couleurs */
						$couleurs = array(
							'562' => '#FFFF99',
							'563' => '#FFFF00',
							'647' => '#FFCCCC',
							'648' => '#9933CC',
							'649' => '#FF33CC',
							'650' => '#FF0000',
							'651' => '#66FFCC',
							'652' => '#9966FF',
							'653' => '#660000',
							'654' => '#66FF00',
							'655' => '#003333',
							'656' => '#C0C0C0',
							'657' => '#0000FF',
							'658' => '#000066',
							'659' => '#CCFF66',
							'660' => '#006600',
							'661' => '#CCFFFF',
							'720' => '#008080',
							'721' => '#FF8000',
							'722' => '#808080'
						);

						if ($nbCouleurs == 0)
						{
							if ($positionExistante)
							{
								$color = "#FFFFFF";
								
								$req_murs = "select mur_creusable from murs where mur_pos_cod = $position";
								
								$stmt3 = $pdo->query($req_murs);
								if ($result3 = $stmt3->fetch())
								{
									if($result3['mur_creusable'] == 'O')
										$color = "#000000";

									if($result3['mur_creusable'] == 'N')
										$color = "#000000";
								}
							} else
								// cas en dehors de carte
								$color = "#000000";

							$contenu_page .= '<td width="20" height="20" ><div style="width:25px;height:25px;background:'. $color .';"> '. $image .'</div>
							</td>';
						}
						else
						{
							$ingredientsArray = array();
							$i = 0;
							while ($result2 = $stmt2->fetch())
							{
								$ingredientsArray[$i] = $result2['ingrpos_gobj_cod'];
								$i = ++$i;
							}

							$taille = (25 / $nbCouleurs);
							$color = "#00FF00";
							$contenu_page .= '<td width="20" height="20"><div style="width:25px;height:25px">'. $image .'
							<table  cellspacing="0"><tr>';

							/*On initialise le tableau secondaire qu'on va remplir */
							for ($cote = 0 ; $cote < $nbCouleurs ; $cote++)
							{
								$contenu_page .= '<tr>';
								for ($j = $cote ; $j < ($nbCouleurs + $cote) ; $j++)
								{
									$contenu_page .= '<td width="'. $taille .'" height="'. $taille .'"  id="x'. $j .''. $cote .'pos_'. $position .'" style="background:'. $couleurs[$ingredientsArray[$j%$nbCouleurs]] .';"></td>';
								}
								$contenu_page .= '</tr>';
							}
							$contenu_page .= '</tr></table></div></td>';
						}
					}
					else
					{
						$contenu_page .= '<td></td>';
					}
				}
				$contenu_page .= '</tr>';
			}
			$contenu_page .= '</tr>
			</table>';
		break;

		case "detecter3":
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$stmt = $pdo->query($req_pa);
			$result = $stmt->fetch();
			if ($result['perso_pa'] < 8)
			{
					$contenu_page .= 'Vous n’avez pas assez de PA !';
					break;
			}
			else
			{
				$req_enl_pa = "update perso set perso_pa = perso_pa - 8,
				perso_renommee_artisanat = perso_renommee_artisanat + 0.1 where perso_cod = $perso_cod";
				$stmt = $pdo->query($req_enl_pa);		
			}
			$contenu_page .= '<p>Vous observez ce qui pourrait se cacher dans les alentours</p>
				<center><table background="../../images/fond5.gif" border="0" cellspacing="1" cellpadding="0">';
			// POSITION DU JOUEUR
			$req_position = "select pos_x,pos_y,pos_etage
				from perso_position,positions
				where ppos_perso_cod = $perso_cod
					and ppos_pos_cod = pos_cod ";
			$stmt = $pdo->query($req_position);
			$result = $stmt->fetch();
			$perso_pos_x = $result['pos_x'];
			$perso_pos_y = $result['pos_y'];
			$perso_pos_etage = $result['pos_etage'];
			$contenu_page .= '<br><br>Table de description des composants présents :<br>
				<table border="1">
				<tr>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#008080"></div></td><td>Pissenlit de vin</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FFFF99"></div></td><td>Pomme</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FFFF00"></div></td><td>Mandragore</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FFCCCC"></div></td><td>Absinthe</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#9933CC"></div></td><td>Ache Des Marais</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FF33CC"></div></td><td>Aconit</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#66FFCC"></div></td><td>Bardane</td>
				</tr>
				<tr>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FF8000"></div></td><td>Léonine sucrée</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FF8000"></div></td><td>Léonine sucrée</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#9966FF"></div></td><td>Belladone</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#660000"></div></td><td>Chene Rouvre</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#FF0000"></div></td><td>Alkegenge</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#66FF00"></div></td><td>Digitale</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#006600"></div></td><td>Pavot</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#CCFFFF"></div></td><td>Serpolet</td>
				</tr>
				<tr>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#808080"></div></td><td>Herbe de Lune</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#0000FF"></div></td><td>Menthe</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#000066"></div></td><td>Millepertuis</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#CCFF66"></div></td><td>Noyer</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#003333"></div></td><td>Gentiane</td>
					<td width="20" height="20"><div style="width:25px;height:25px;background:#C0C0C0"></div></td><td>Jusquiame</td>
					<td width="20" height="20"><div style="width:25px;height:25px">
						<table  border="0">
							<tr><td width="10" height="10" style="background:#66FFFF"></td><td width="10" height="10"  style="background:#663399"></td></tr>
							<tr><td width="10" height="10"  style="background:#663399"></td><td width="10" height="10"  style="background:#66FFFF"></td></tr>
						</table>
					</td><td>Plusieurs composants</td>
				</tr>
				</table><hr>';
			$contenu_page .= '<table border="1">';
			$req_position = "select pos_x,pos_y,pos_etage
				from perso_position,positions
				where ppos_perso_cod = $perso_cod
					and ppos_pos_cod = pos_cod ";
			$stmt = $pdo->query($req_position);
			$result = $stmt->fetch();
			$position_x = $result['pos_x'];
			$position_y = $result['pos_y'];
			for ($y=-3; $y<4; $y++)
			{
				$contenu_page .= '<TR>';
				for ($x=-3; $x<4; $x++)
				{
					if(($y*$y + $x*$x) < 18)
					{
						if($y == 0 && $x == 0)
							$image = "<img height=\"13\" src=\"../../images/rond.gif\">";
						else
							$image = "";

						$req_position = "select pos_cod,pos_x,pos_y from positions
							where pos_etage = $perso_pos_etage
								and pos_x = $position_x + $x
								and pos_y = $position_y - $y";
						$stmt = $pdo->query($req_position);
						$positionExistante = false;

						// on vérifie l'existence de la postion dans l'étage
						if($result = $stmt->fetch())
						{
							$position = $result['pos_cod'];
							
							$req_ingredient = "select ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea from ingredient_position
								where ingrpos_pos_cod = $position";
							$stmt2 = $pdo->query($req_ingredient);
							$nbCouleurs = $stmt2->rowCount();
							$ingredients = $result2['ingrpos_gobj_cod'];
							$positionExistante = true;
						} else
							// nexrecord renvoie false, on affiche un mur noir
							$nbCouleurs = 0;

						/* Tableau des couleurs */
						$couleurs = array(
							'562' => '#FFFF99',
							'563' => '#FFFF00',
							'647' => '#FFCCCC',
							'648' => '#9933CC',
							'649' => '#FF33CC',
							'650' => '#FF0000',
							'651' => '#66FFCC',
							'652' => '#9966FF',
							'653' => '#660000',
							'654' => '#66FF00',
							'655' => '#003333',
							'656' => '#C0C0C0',
							'657' => '#0000FF',
							'658' => '#000066',
							'659' => '#CCFF66',
							'660' => '#006600',
							'661' => '#CCFFFF',
							'720' => '#008080',
							'721' => '#FF8000',
							'722' => '#808080'
						);

						if ($nbCouleurs == 0)
						{
							if ($positionExistante) {
								$req_murs = "select mur_creusable from murs where mur_pos_cod = $position";
								
								$stmt3 = $pdo->query($req_murs);
								$result3 = $stmt3->fetch();
								$color = "#FFFFFF";
								if($result3['mur_creusable'] == 'O'){
									$color = "#000000";
								}
								if($result3['mur_creusable'] == 'N'){
									$color = "#000000";
								}
								$contenu_page .= '<td width="20" height="20" ><div style="width:25px;height:25px;background:'. $color .';"> '. $image .'</div></td>';
							} else
								// cas en dehors de carte
								$color = "#000000";
						}
						else
						{
							$ingredientsArray = array();
							$i = 0;
							while ($result2 = $stmt2->fetch())
							{
								$ingredientsArray[$i] = $result2['ingrpos_gobj_cod'];
								$i = ++$i;
							}

							$taille = (25 / $nbCouleurs);
							$color = "#00FF00";
							$contenu_page .= '<td width="20" height="20"><div style="width:25px;height:25px">'. $image .'
								<table  cellspacing="0"><tr>';

							/*On initialise le tableau secondaire qu'on va remplir */
							for ($cote = 0 ; $cote < $nbCouleurs ; $cote++)
							{
								$contenu_page .= '<tr>';
								for ($j = $cote ; $j < ($nbCouleurs + $cote) ; $j++)
								{
									$contenu_page .= '<td width="'. $taille .'" height="'. $taille .'"  id="x'. $j .''. $cote .'pos_'. $position .'" style="background:'. $couleurs[$ingredientsArray[$j%$nbCouleurs]] .';"></td>';
								}
								$contenu_page .= '</tr>';
							}
							$contenu_page .= '</tr></table></div></td>';
						}
					}
					else
					{
						$contenu_page .= '<td></td>';
					}
				}
				$contenu_page .= '</tr>';
			}
			$contenu_page .= '</tr></table>';
		break;
	}
}
else
{
	$contenu_page .= "<p>Vous ne possédez pas la compétence nécessaire</p>";
}

