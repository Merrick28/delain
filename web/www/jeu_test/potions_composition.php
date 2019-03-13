<?php 

// Enchaînement : 
// "Début" -> Affichage des flacons disponibles (flacons vides ou potions en cours de composition).
// -> on peut ajouter un composant (=> "compo")
// -> on peut finaliser une potion (=> "fabrication")
// -> on peut voir le détail d’une potion en cours de fabrication
//
// "compo" => ajout d’un composant à une potion. Géré par select potions.compo_potions('. $perso_cod .','. $fiole .','. $composant .')
//
// "fabrication" => vérifie si la potion créée donne quelque chose.
//
// "détail" => affiche les composants déjà placés dans la potion
//
$db2 = new base_delain;
$db3 = new base_delain;

$erreur = 0;

//Controle
$param = new parametres();
$req_comp = "select pcomp_modificateur,pcomp_pcomp_cod from perso_competences 
	where pcomp_perso_cod = $perso_cod 
		and pcomp_pcomp_cod in (97,100,101)";
$db->query($req_comp);
if($db->next_record())
{	
	$niveau = $db->f("pcomp_pcomp_cod");
	if ($niveau == 97)
	{
		$pa = $param->getparm(109);
	}
	else if ($niveau == 100)
	{
		$pa = $param->getparm(109) - 1;
	}
	else
	{
		$pa = $param->getparm(109) - 2 ;
	}
	$req = "select valeur_bonus($perso_cod, 'HOR') as nombre";
	$db->query($req);
	$db->next_record();
	$pa = $pa + $db->f('nombre');
	if ($pa < 2) $pa = 2;

	if(isset($_POST['ajout'])) 
	{
		$methode = "compo";
	}
	elseif(isset($_POST['fabrication']))
	{
		$methode = "fabrication";
	}
	if (!isset($methode))
		$methode = "debut";
	switch($methode)
	{
		case "debut":
			$contenu_page .= '<br>
				<form name="potions" method="post" action="'. $PHP_SELF .'">
					<input type="hidden" name="methode" value="compo">
					<input type="hidden" name="tpot" value="'. $tpot .'"><p>';

			$req = 'select obj_nom,obj_gobj_cod from objets,perso_objets,objet_generique
				where obj_gobj_cod = gobj_cod
					and perobj_perso_cod = ' . $perso_cod . '
					and perobj_obj_cod = obj_cod
					and (gobj_tobj_cod = 22 OR gobj_tobj_cod = 28 OR gobj_tobj_cod = 30 OR gobj_tobj_cod = 34 OR gobj_tobj_cod = 39)
				group by obj_nom,obj_gobj_cod
				order by obj_gobj_cod';
			$db->query($req);

			$is_compo = true;
			$is_flacon = true;
			$is_potion = true;
			
			$select_ajout_compo = '';
			$choix_flacon = '';

			if($db->nf() == 0)
			{
				$is_compo = false;
			}
			else
			{	
				$select_ajout_compo .= '<select name="composant">';
				while($db->next_record())
				{
					$select_ajout_compo .= '<option value="'. $db->f("obj_gobj_cod") .'"> '. $db->f("obj_nom") .'</option>';
				}
				$select_ajout_compo .= '</select>';
			}
			$req = 'select obj_cod, obj_nom, obj_gobj_cod from objets, perso_objets
				where obj_gobj_cod = 412
					and perobj_perso_cod = ' . $perso_cod . '
					and perobj_obj_cod = obj_cod
				order by obj_gobj_cod';
			$db->query($req);
			if($db->nf() == 0)
			{
				$is_flacon = false;
			}
			else
			{
				while($db->next_record())
				{
					$obj_cod = $db->f('obj_cod');
					$choix_flacon .= "<input type='radio' name='flacon' value='$obj_cod' id='$obj_cod'> <label for='$obj_cod'>" . $db->f('obj_nom') . '</label><br />';
				}	
			}
			$req = 'select obj_cod,obj_nom,obj_gobj_cod,sum(flaccomp_number) as nombre from objets,perso_objets,potions.flacon_composants
				where obj_gobj_cod = 561
					and perobj_perso_cod = ' . $perso_cod . '
					and perobj_obj_cod = obj_cod
					and flaccomp_obj_cod = perobj_obj_cod
				group by obj_cod,obj_nom,obj_gobj_cod
				order by obj_gobj_cod';
			$db->query($req);
			if($db->nf() == 0)
			{
				$is_potion = false;
			}
			else
			{
				while($db->next_record())
				{
					$obj_cod = $db->f('obj_cod');
					$choix_flacon .= "<input type='radio' name='flacon' value='$obj_cod' id='$obj_cod'> <label for='$obj_cod'><a href='$PHP_SELF?&tpot=$tpot&methode=detail&fiole=$obj_cod'>" . $db->f('obj_nom') . '</a> (' . $db->f('nombre') . ' composants)</label><br />';
				}			
			}
			$texte_bouton_ajout = '<input type="submit" name="ajout" value="Ajouter un composant (0PA)" class="test">';
			$texte_bouton_final = '<input type="submit" name="fabrication" value="Finaliser la potion ('. $pa .'PA)"  class="test">';
			
			if ($is_compo && ($is_flacon || $is_potion))
				$contenu_page .= "Vous souhaitez rajouter un peu de... $select_ajout_compo <br /><br />dans le récipient de votre choix :<br />$choix_flacon<br /><br />$texte_bouton_ajout";
			if ($is_compo && !($is_flacon || $is_potion))
				$contenu_page .= "Vous possédez bien quelques herbes... $select_ajout_compo <br /><br />mais aucun récipient dans lequel les mélanger !";
			if (!$is_compo && ($is_flacon || $is_potion))
				$contenu_page .= "Vous ne possédez aucune herbe pour agrémenter vos décoctions, qui sont les suivantes :<br />$choix_flacon";
			if (!$is_compo && !($is_flacon || $is_potion))
				$contenu_page .= "C’est la ruine ! Vous n’avez ni herbe ni récipient. La voie de l’alchimie est bien compliquée...";
			if ($is_potion)
				$contenu_page .= $texte_bouton_final;

			$contenu_page .= '</p></form>';
		break;

		case "compo":
			$fiole= $_POST['flacon'];
			$composant= $_POST['composant'];
			if (!isset($fiole))
			{
				$contenu_page .= 'Vous n’avez sélectionné aucune préparation ou fiole vide !<br />';
				$erreur = 1;
			}
			if (!isset($composant))
			{
				$contenu_page .= 'Vous n’avez sélectionné aucun composant !<br />';
				$erreur = 1;
			}
			if ($erreur != 1)
			{
				$req = 'select potions.compo_potions('. $perso_cod .','. $fiole .','. $composant .') as resultat';
				$db->query($req);
				$db->next_record();
				$result = explode(';',$db->f('resultat'));
				$contenu_page .= ''.$result[1] . '<br>';
			}
		break;

		case "detail":
			$contenu_page .= '<strong>Détail de la potion sélectionnée : </strong><br><br><table>';
			$req = 'select flaccomp_obj_cod,flaccomp_comp_cod,sum(flaccomp_number) as nombre,gobj_nom from potions.flacon_composants,objet_generique,perso_objets
				where flaccomp_obj_cod = '. $fiole .'
					and flaccomp_comp_cod = gobj_cod
					and perobj_perso_cod = '. $perso_cod .'
					and perobj_obj_cod = flaccomp_obj_cod
				group by flaccomp_comp_cod,flaccomp_obj_cod,gobj_nom
				order by gobj_nom';
			$db->query($req);
			if($db->nf() == 0)
			{
				$contenu_page .= '<tr><td>Vous ne possédez pas cette potion, vous ne pouvez donc pas en voir le détail !</td></tr>';
			}
			else
			{
				$contenu_page .= '<tr><td class="soustitre2">Composants</td><td class="soustitre2">Quantités</td></tr>';
				while($db->next_record())
				{
					$contenu_page .= '<tr>
							<td>' . $db->f('gobj_nom') . '</td>
							<td>' . $db->f('nombre') . '</td>
						</tr>';
				}
			}
			$contenu_page .= '</table>';
		break;

		case "fabrication":
			$fiole= $_POST['flacon'];
			if ($fiole == '')
			{
				$contenu_page .= 'Vous n’avez pas sélectionné de potion à finaliser !<br />';
				break;
			}	
			//Vérification des Pa et des pxs
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$db->query($req_pa);
			$db->next_record();
			if ($db->f("perso_pa") < $pa)
			{
				$contenu_page .= 'Vous n’avez pas assez de PA !<br />';
				break;
			}
			else
			{
				$req = 'update perso set perso_pa = perso_pa - '. $pa .' where perso_cod = '. $perso_cod;
				$db->query($req);
			}
			/*On va chercher  si une potion correspond à la composition de la fiole en question*/
			$query = 'select * from formule f,formule_produit,objet_generique where frm_cod = frmpr_frm_cod and frmpr_gobj_cod = gobj_cod and ';
			$compteur = 1;
			$req = 'select flaccomp_obj_cod,flaccomp_comp_cod,sum(flaccomp_number) as nombre,gobj_nom from potions.flacon_composants,objet_generique,perso_objets
				where flaccomp_obj_cod = '. $fiole .'
					and flaccomp_comp_cod = gobj_cod
					and perobj_perso_cod = '. $perso_cod .'
					and perobj_obj_cod = flaccomp_obj_cod
				group by flaccomp_comp_cod,flaccomp_obj_cod,gobj_nom
				order by gobj_nom';
			
			
			$db->query($req);

			//$res_parm = pg_exec($req);
			//$nombre_composants = pg_numrows($res_parm);
			
			$nombre_composants = $db->nf();
			
			if($nombre_composants == 0)
			{
				$contenu_page .= '<br />Vous ne possédez pas cette potion, vous ne pouvez donc pas la finaliser !<br /><br />';
			}
			else	// on a trouvé une formule correspondant à la potion
			{
				while($db->next_record())
				{
					$query .= ' exists (select 1 from formule_composant fc  
						where f.frm_cod = fc.frmco_frm_cod  
							and fc.frmco_gobj_cod = ' . $db->f('flaccomp_comp_cod') . ' 
							and fc.frmco_num = ' . $db->f('nombre') . ')';
					if($nombre_composants > $compteur)
					{
						$query .= ' and ';
						$compteur = $compteur + 1;
					}
				}
			}

			$requete_competence = "select potions.valide_potion_competence($perso_cod) as resultat";
			$db2->query($requete_competence);
			$db2->next_record();
			$result = explode(';',$db2->f('resultat'));
			$contenu_page .= '<br>'. $result[1] . '<br>';

			if ($result[0] == 1)	// en cas de réussite de la compétence
			{
				$db->query($query);
				if($db->nf() == 0)
				{
					$contenu_page .= '<br />Cette formule ne peut rien produire du tout !<br /><br />';
					$texte_evt = '[perso_cod1] a fini sa potion... Mais ce n’est que du jus de chaussette.';
					$req = "select insere_evenement($perso_cod, $perso_cod, 91, '$texte_evt', 'O', NULL)";
					$db->query($req);
					break;
				}

				//on teste dans l'autre sens, si tous les composants de la formule sont contenus dans le flacon
				$db->next_record();
				$formule = $db->f('frm_cod');
				$req_controle = 'select * from formule_composant fc 
					where fc.frmco_frm_cod = '.$formule;

				$db2->query($req_controle);

				while($db2->next_record())
				{
					$composant = $db2->f('frmco_gobj_cod');
					$nombre2 = $db2->f('frmco_num');
					$query = 'select flaccomp_obj_cod,flaccomp_comp_cod,sum(flaccomp_number) as nombre from potions.flacon_composants
						where flaccomp_obj_cod = '. $fiole .'
							and flaccomp_comp_cod = '. $composant .'
						group by flaccomp_comp_cod,flaccomp_obj_cod';
					$db3->query($query);
					$erreur_composant = ($db3->nf() == 0);	// Le composant n’entre pas dans une formule
					$erreur_nombre = true;
					if ($db3->next_record())
						$erreur_nombre = ($db3->f('nombre') != $nombre2);	// Le composant entre dans la formule, mais il n’y est pas dans la bonne quantité
					if ($erreur_composant || $erreur_nombre)
					{
						$contenu_page .= '<br />Cette formule ne peut rien produire du tout, il manque certainement des composants !<br /><br />';
						$erreur = 1;
						$texte_evt = '[perso_cod1] a fini sa potion... Mais ce n’est que du jus de chaussette.';
						$req = "select insere_evenement($perso_cod, $perso_cod, 91, '$texte_evt', 'O', NULL)";
						$db->query($req);
						break;
					}
				}

				if ($db->f('frm_comp_cod') > $niveau)	// Potion d’un niveau trop élevé...
				{
					$contenu_page .= '<br />Vous avez beau vous creuser le crâne, vous n’arrivez pas à comprendre cette formule. Elle a l’air bonne, mais elle n’est sans doute pas encore de votre niveau...<br /><br />';
					$erreur = 1;
					$texte_evt = '[perso_cod1] a fini sa potion... Mais qu’est-ce que ça peut bien être ?';
					$req = "select insere_evenement($perso_cod, $perso_cod, 91, '$texte_evt', 'O', NULL)";
					$db->query($req);
					break;
				}

				if($erreur != 1)
				{
					// On crée la potion
					$nom = $db->f('gobj_nom');
					$g_objet = $db->f('frmpr_gobj_cod');
					$description = $db->f('gobj_description');
					$req = 'update objets set obj_nom = e\''. pg_escape_string($nom) .'\',obj_gobj_cod = '. $g_objet .',obj_description = e\''. pg_escape_string($description) .'\' where obj_cod = '. $fiole;
					$db->query($req);
					$db->next_record();
					$contenu_page .= '<img src="http://www.jdr-delain.net/images/pos1.gif"><br>En assemblant les différents éléments, vous parvenez à fabriquer une nouvelle potion ! Il semblerait que ce soit une ' . $nom . '<br />
						Prenez garde tout de même. Parfois, l’assemblage de composants peut donner des résultats que vous n’êtes pas tout à fait capable de comprendre et d’analyser. Une erreur est vite arrivée.<br />';

					// On vérifie si la formule est déjà connue
					$requete_existence = "select * from perso_formule where pfrm_frm_cod = $formule and pfrm_perso_cod = $perso_cod";
					$db3->query($requete_existence);
					if($db3->nf() == 0)	// Formule inconnue, on réalise l’ajout
					{
						$req = 'update perso set perso_px = perso_px + 5, perso_renommee_artisanat = perso_renommee_artisanat + 6 where perso_cod = '. $perso_cod;
						$db->query($req);		
						$db->next_record();
						$req = 'insert into perso_formule (pfrm_frm_cod,pfrm_perso_cod) values ('. $formule .', '. $perso_cod .')';
						$db->query($req);		
						$db->next_record();
						$contenu_page .= '<br>Vous gagnez 5 px pour cette action. Vous connaissez maintenant une nouvelle formule de potion qu’il vous sera facile de reproduire.';
					}
					else	// Formule déjà connue, on ne donne pas tous les PXs...
					{
						$req = 'update perso set perso_px = perso_px + 1.5, perso_renommee_artisanat = perso_renommee_artisanat + 2 where perso_cod = '. $perso_cod;
						$db->query($req);
						$db->next_record();
						$contenu_page .= '<p>Vous connaissiez déjà cette potion.</p><p>Vous gagnez 1,5 px pour cette action.</p>';
					}
					$texte_evt = "[perso_cod1] a fini sa potion ! C’est une $nom.";
					$req = "select insere_evenement($perso_cod, $perso_cod, 91, '$texte_evt', 'O', '[gobj_cod]=$g_objet')";
					$db->query($req);
				}
			}
		break;
	}
}