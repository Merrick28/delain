<?php 

$req_comp = "select pcomp_modificateur,pcomp_pcomp_cod from perso_competences 
										where pcomp_perso_cod = $perso_cod 
										and pcomp_pcomp_cod in (97,100,101)";
$db->query($req_comp);
if($db->next_record())
{	
	$niveau = $db->f("pcomp_pcomp_cod");
	if ($niveau == 100 or $niveau == 101)
	{
		$pa = $db->getparm_n(107);
	}
	else
	{
		$pa = $db->getparm_n(108);
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
				<br><b><i>Attention, un seul composant par position vous sera présenté, mais en cherchant, vous pourrez parfois en trouver de plusieurs sortes</i></b>
				<form method="post" action="' . $PHP_SELF. '">
				<br><p align="left" class="soustitre2"> <b>Détection simple.</b></p>
				Elle ne vous permettra que de tenter de regarder la présence de composants sur votre propre position<br>	
				<input type="hidden" name="methode" value="detecter1">
				<input type="hidden" name="t" value="' . $t . '">
				<br><input type="submit" value="Rechercher (0 PA)"  class="test">
				</form><br>
				<form method="post" action="' . $PHP_SELF. '">
				<br><p align="left" class="soustitre2"> <b>Recherche avancée.</b></p> 
				Cette recherche vous permettra de tenter de regarder la présence de composants à une case autour de vous.<br>	
				<input type="hidden" name="methode" value="detecter2">
				<input type="hidden" name="t" value="' . $t . '">
				<br><input type="submit" value="Rechercher ('. $pa .' PA)"  class="test">
				</form><br>';
				if ($niveau == 101)
				{
				$contenu_page .= '<form method="post" action="' . $PHP_SELF. '">
				<br><p align="left" class="soustitre2"> <b>Recherche poussée.</b></p>
				Elle vous permettra de scruter une zone à deux positions ou moins autour de vous.<br>	
				<input type="hidden" name="methode" value="detecter3">
				<input type="hidden" name="t" value="' . $t . '">
				<br><input type="submit" value="Rechercher (8PA)"  class="test">
				</form><br>
				</p>
				<br>';
				}
		break;
		case "detecter1":
			$contenu_page .= '
			<p>Vous observez ce qui pourrait se cacher sur votre propre position.</p>';	
			$req_position = "select pos_x,pos_y,pos_etage,ingrpos_qte,ingrpos_gobj_cod,gobj_nom
													from perso_position,positions,ingredient_position,objet_generique
													where ppos_perso_cod = $perso_cod
													and ppos_pos_cod = pos_cod 
													and ingrpos_pos_cod = pos_cod
													and gobj_cod = ingrpos_gobj_cod";
			$db->query($req_position);
			if($db->nf() == 0)
			{
				$contenu_page .= 'Votre position ne semble pas vraiment constituer un lieu idéal pour récolter des composants.<br>';
				break;
			}

			else
			{
				while($db->next_record())
				{
				$contenu_page .= '<br>Votre recherche pourrait s\'avérer fructueuse ! Ce lieu est propice à la découverte de composants. Mais quelqu\'un aura peut être déjà fait sa propre récolte en ces lieux ...<br>';
				}
			}
			$contenu_page .= '<br><p><a href="' .$PHP_SELF . '?t=' . $t . '">Retour à la détection</a>';
		break;
		
		case "detecter2":
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$db->query($req_pa);
			$db->next_record();
			if ($db->f("perso_pa") < $pa)
			{
					$contenu_page .= 'Vous n\'avez pas assez de PA !';
					break;
			}
			else
			{
					/*$req_enl_pa = "update perso set perso_pa = perso_pa - $pa where perso_cod = $perso_cod";
					$db->query($req_enl_pa);		*/
					$contenu_page .= '<p>Vous observez ce qui pourrait se cacher dans les alentours</p>
				<center><table background="../../images/fond5.gif" border="0" cellspacing="1" cellpadding="0">';
				// POSITION DU JOUEUR
				$req_position = "select pos_x,pos_y,pos_etage
													from perso_position,positions
													where ppos_perso_cod = $perso_cod
													and ppos_pos_cod = pos_cod ";
				$db->query($req_position);
				$db->next_record();
				$perso_pos_x = $db->f("pos_x");
				$perso_pos_y = $db->f("pos_y");
				$perso_pos_etage = $db->f("pos_etage");
				//echo "POSJ = $perso_pos_x ; $perso_pos_y ; $perso_pos_etage <br>";
				$composantArray = array();
				// POSITION DES COMPOSANTS
				$req_composant = "select pos_x,pos_y,pos_etage,ingrpos_qte,ingrpos_gobj_cod,gobj_nom
													from positions,ingredient_position,objet_generique
													where pos_etage = $perso_pos_etage
													and ingrpos_pos_cod = pos_cod
													and gobj_cod = ingrpos_gobj_cod";
																
				$db->query($req_composant);
				while($db->next_record())
				{
					if($db->f("pos_etage") == $perso_pos_etage)
					{
						$key = $db->f("pos_x")."X".$db->f("pos_y");
						if(isset($composantArray[$key]))
						{
							$composantArray[$key]++;	
						} 
						else 
						{
							$composantArray[$key] = 1;
						}
						$richesse[$key] = $db->f("ingrpos_qte");
						$composant[$key] = $db->f("gobj_nom");
					}
				};
				
				for ($i=-2; $i<4; $i++)
				{  
					echo "<TR>";
					for ($j=-2; $j<4; $j++)
					{
						if(($i*$i + $j*$j) < 8)
						{
							$pos_case_x = $perso_pos_x + $j;
							$pos_case_y = $perso_pos_y - $i;
							$key = $pos_case_x."X".$pos_case_y;
							if(isset($composantArray[$key]))
							{
								$texte = $composant[$key]." repéré en ";
								if ($richesse[$key] > 5)
								{
									$texte = $composant[$key]." repéré en grande quantité en ";
								}
								$contenu_page .= '<td><img height="13" src="../../images/automap_1_5.gif" title=" "' . $texte . "Position : X = " . $pos_case_x." / Y = ".$pos_case_y."\"></td>";	
							} 
							else 
							{
								if($i == 0 && $j == 0)
								{
									$contenu_page .= "<td><img height=\"13\" src=\"../../images/automap_1_6.gif\" title=\"rien $key\"></td>";
								} 
								else 
								{
									$contenu_page .= "<td><img height=\"13\" src=\"../../images/automap_0_0.gif\" title=\"rien $key\"></td>";
								}
							}
							
						} 
						else 
						{
							$contenu_page .= "<td></td>";
						}
					}
					$contenu_page .= "</TR>";
				}
				$contenu_page .= '</table></center><br>';
			}
		break;

		case "detecter3":
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$db->query($req_pa);
			$db->next_record();
			if ($db->f("perso_pa") < 8)
			{
					$contenu_page .= 'Vous n\'avez pas assez de PA !';
					break;
			}
			else
			{
					/*$req_enl_pa = "update perso set perso_pa = perso_pa - 8 where perso_cod = $perso_cod";
					$db->query($req_enl_pa);		*/
					$contenu_page .= '<p>Vous observez ce qui pourrait se cacher dans les alentours</p>
				<center><table background="../../images/fond5.gif" border="0" cellspacing="1" cellpadding="0">';
				// POSITION DU JOUEUR
				$req_position = "select pos_x,pos_y,pos_etage
													from perso_position,positions
													where ppos_perso_cod = $perso_cod
													and ppos_pos_cod = pos_cod ";
				$db->query($req_position);
				$db->next_record();
				$perso_pos_x = $db->f("pos_x");
				$perso_pos_y = $db->f("pos_y");
				$perso_pos_etage = $db->f("pos_etage");
				//echo "POSJ = $perso_pos_x ; $perso_pos_y ; $perso_pos_etage <br>";
				$composantArray = array();
				// POSITION DES COMPOSANTS
				$req_composant = "select pos_x,pos_y,pos_etage,ingrpos_qte,ingrpos_gobj_cod,gobj_nom
													from positions,ingredient_position,objet_generique
													where pos_etage = $perso_pos_etage
													and ingrpos_pos_cod = pos_cod
													and gobj_cod = ingrpos_gobj_cod";
																
				$db->query($req_composant);
				while($db->next_record())
				{
					//echo "POS=".$db->f("pos_x").";".$db->f("pos_y").";".$db->f("pos_etage")."<br>";
					if($db->f("pos_etage") == $perso_pos_etage)
					{
						$key = $db->f("pos_x")."X".$db->f("pos_y");
						if(isset($composantArray[$key]))
						{
							$composantArray[$key]++;	
						} 
						else 
						{
							$composantArray[$key] = 1;
						}
						$richesse[$key] = $db->f("ingrpos_qte");
						$composant[$key] = $db->f("gobj_nom");
					}
				};
				
				for ($i=-3; $i<4; $i++)
				{  
					echo "<TR>";
					for ($j=-3; $j<4; $j++)
					{
						if(($i*$i + $j*$j) < 18)
						{
							$pos_case_x = $perso_pos_x + $j;
							$pos_case_y = $perso_pos_y - $i;
							$key = $pos_case_x."X".$pos_case_y;
							if(isset($composantArray[$key]))
							{
								$texte = $composant[$key]."<br> repéré en ";
								if ($richesse[$key] > 5)
								{
									$texte = $composant[$key]."<br> repéré en grande quantité en ";
								}
								$contenu_page .= "<td><img height=\"13\" src=\"../../images/automap_1_5.gif\" title=\" ".$texte."Position : X = ".$pos_case_x." / Y = ".$pos_case_y."\"></td>";	
							} 
							else 
							{
								if($i == 0 && $j == 0)
								{
									$contenu_page .= "<td><img height=\"13\" src=\"../../images/automap_1_6.gif\" title=\"rien $key\"></td>";
								} 
								else 
								{
									$contenu_page .= "<td><img height=\"13\" src=\"../../images/automap_0_0.gif\" title=\"rien $key\"></td>";
								}
							}
							
						} 
						else 
						{
							$contenu_page .= "<td></td>";
						}
					}
					$contenu_page .= "</TR>";
				}
				$contenu_page .= '</table></center><br>';
			}
				break;
	}
} 
else 
{
	$contenu_page .= "<p>Vous ne possédez pas la compétence nécessaire</p>";	
}
//echo $contenu_page;
?>