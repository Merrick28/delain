<?php 

//On sélectionne les pierres précieuses qui peuvent être travaillées
	if(!isset($methode))
	{
	$methode = "debut";
	}
	switch($methode)
	{
		case "debut":
			$nombre_pa = $db->getparm_n(113);
			$req_comp = "select count(*),gobj_nom,obj_gobj_cod from objets,objet_generique,perso_objets 
				where gobj_cod in (338,339,340,341,353,354,357,358,361,438)
			 and gobj_cod = obj_gobj_cod and perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod group by obj_gobj_cod,gobj_nom";
			$db->query($req_comp);

			if($db->nf() == 0)
			{
				$contenu_page .= 'Vous ne possédez aucune pierre permettant de réaliser des composants d’enchantement<br><br>';
			}
			else
			{
				$contenu_page .= '<p align="left"><b>Vous êtes en possession de :</b>';
				$liste = '<option value="vide"><-- Sélectionner --></option>';
				while($db->next_record())
				{
					$contenu_page .= '<br>'.$db->f("gobj_nom");
					$liste .= '<option value="'. $db->f("obj_gobj_cod") .'"> '. $db->f("gobj_nom") .'</option>';
				}
				$contenu_page .= '</p><br><br><p align="center"><b>Sur quelle pierre souhaitez vous réaliser le forgeamage ?</b>
					<br>
					<form name="potions" method="post" action="'. $PHP_SELF .'">
						<input type="hidden" name="methode" value="compo">
						<input type="hidden" name="t_ench" value="'. $t_ench .'">
						<table width="70%">
						<select name="composant">
						'. $liste .'
						</select>
						<input type="submit" value="Valider ('.$nombre_pa.') PA" class="test">
						<i>Attention, pas de confirmation ensuite</i></p>
					</form>
				</table>';
			}
			$req = "select o2.gobj_nom as pierre, o1.gobj_nom as compo,
					case frm_comp_cod when 88 then 1 when 102 then 2 when 103 then 3 else NULL end as niveau,
					frm_temps_travail
				from perso_formule
				inner join formule on frm_cod = pfrm_frm_cod
				inner join formule_produit on frmpr_frm_cod = frm_cod
				inner join formule_composant on frmco_frm_cod = frm_cod
				inner join objet_generique o1 on o1.gobj_cod = frmpr_gobj_cod
				inner join objet_generique o2 on o2.gobj_cod = frmco_gobj_cod
				where pfrm_perso_cod = $perso_cod
					and frm_type = 3
				order by o2.gobj_nom, frm_temps_travail, o1.gobj_nom";
			
			$db->query($req);

			if($db->nf() > 0)
			{
				$contenu_page .= '<p><b>Formules connues :</b></p>';
				$contenu_page .= '<table><tr><th class="titre">Pierre nécessaire</th><th class="titre">Composant obtenu</th><th class="titre">Coût (énergie)</th></tr>';
				while($db->next_record())
				{
					$contenu_page .= '<tr><td class="soustitre2">' . $db->f("pierre") . '</td>
						<td class="soustitre2">' . $db->f("compo") . '</td>
						<td class="soustitre2">' . $db->f("frm_temps_travail") . '</td></tr>';
				}
				$contenu_page .= '</table>';
			}
		break;
		case "compo": // Création d'un composant d'un enchantement à partir d'une pierre précieuse
			$pierre = $_POST['composant'];
			$gain_renommee = 1;
			//Controle sur la sélection d'une pierre précieuse
			if($pierre == 'vide')
			{
				$contenu_page .= '<b>Vous n’avez sélectionné aucune pierre précieuse pour votre forgeamage !</b><br>';
				break;
			}
			
			//On vérifie qu'il possède bien la pierre en question
			$req_comp = "select count(*),gobj_nom,obj_gobj_cod from objets,objet_generique,perso_objets 
									where gobj_cod = $pierre
									 and gobj_cod = obj_gobj_cod and perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod group by obj_gobj_cod,gobj_nom";
			$db->query($req_comp);
			if($db->nf() == 0)
			{
				$contenu_page .= '<b>Vous ne possédez pas la pierre en question !</b><br>';
				break;
			}

			$req_comp = "select perso_energie,perso_pa from perso 
										where perso_cod = ". $perso_cod;
			$db->query($req_comp);
			$db->next_record();
			$perso_energie = $db->f("perso_energie");
			$pa = $db->f("perso_pa");

			//On vérifie les pa
			if($pa < $db->getparm_n(113))
			{
				$contenu_page .= '<b>Vous ne possédez pas suffisamment de PA pour réaliser cette opération !</b><br>';
				break;
			}

			//On regarde l'énergie de la case : niveau minimum nécessaire, et risque si le niveau est trop élevé. 1000 est un niveau mini
			$req_comp = "select pos_magie,pos_cod from positions,perso_position 
									where ppos_perso_cod = $perso_cod and ppos_pos_cod = pos_cod";
			$db->query($req_comp);
			$db->next_record();
			$position = $db->f("pos_cod");
			$puissance_case = $db->f("pos_magie");
			if($puissance_case < $db->getparm_n(114))
			{
				$contenu_page .= '<b>La puissance magique à cet endroit n’est pas suffisante pour réaliser un composant à cet endroit !
													<br>Rien ne se produit</b><br>';
				break;
			}
			
			//On calcule si il y a transformation
			$req_comp = "select pcomp_pcomp_cod,pcomp_modificateur from perso_competences
									where pcomp_perso_cod = $perso_cod and pcomp_pcomp_cod in (88,102,103)";
			$db->query($req_comp);
			$db->next_record();
			$forgeamage = $db->f("pcomp_pcomp_cod");
			$forgeamage_pourcent = $db->f("pcomp_modificateur");
			$req_comp = "select frm_cod, frm_type, frm_nom, frm_temps_travail, frm_comp_cod, gobj_nom, frmpr_gobj_cod,
					case when frm_temps_travail <= $perso_energie then 0
					else 1 end as dispo
				from formule, formule_composant, formule_produit, objet_generique
				where frmco_frm_cod = frm_cod and frm_type = 3 and frm_comp_cod <= $forgeamage and frmco_gobj_cod = $pierre 
					and frmpr_frm_cod = frm_cod and frmpr_gobj_cod = gobj_cod
				order by dispo, random() limit 1";
			$db->query($req_comp);
			$db->next_record();
			$energie_necessaire = $db->f("frm_temps_travail");
			$nom = $db->f("gobj_nom");
			$composant_cod = $db->f("frmpr_gobj_cod");
			$frm_cod = $db->f("frm_cod");
			$perte_pa = $db->getparm_n(113);

			//On va regarder si l'énergie du perso est compatible avec celle de la pierre			
			if ($energie_necessaire > $perso_energie)
			{
				$contenu_page .= 'Vous échouez dans la réalisation du composant. Vous n’aviez pas suffisamment d’énergie !
					<br>Vous perdez votre énergie accumulée, gachée dans cette action sans résultat<br>';
				$req = 'update perso set perso_energie = 0 where perso_cod = '. $perso_cod;
				$db->query($req);
				$db->next_record();
				break;
			}
			//On regarde si il y a concentration			
			$concentration = 0;
			$req = 'select concentration_perso_cod from concentrations where concentration_perso_cod = '. $perso_cod;
			$db->query($req);
			if($db->nf() != 0)
			{
				$contenu_page .= '<b>Vous vous êtes concentré avant cette action</b><br>';
				$concentration  = 20;
				$req = 'delete from concentrations where concentration_perso_cod = '. $perso_cod;
				$db->query($req);
			}
		
			//On teste si le perso arrive à créer l’enchantement en utilisant sa compétence en enchantement
			$px_gagne = 0;
			$de = rand(1,100);
			$limite_comp = $db->getparm_n(1);
			$chance = $forgeamage_pourcent - ($energie_necessaire / 4) + $concentration;
			$contenu_page .= 'Votre compétence initiale en forgeamage est de <b>'. $forgeamage_pourcent .'</b>
												<br>Vos chances de réussite modifiées sont de <b>'. $chance .'</b>, tenant compte de la difficulté de ce composant. Votre lancer de dé est de <b>'. $de .'</b><br>';
			if ($de <= 5)
			{
				$reussite = 1;
				$contenu_page .= 'Vous avez donc fait une réussite critique !';
				$perte_pa = $perte_pa - 2;
				$gain_renommee = 1.5;
			}
			else if ($de <= $chance)
			{
				$reussite = 1;	//Si transformation, composant d’enchantement disponible
			}
			else
			{
				$contenu_page .= '<b>Vous échouez dans la création de votre composant d’enchantement.</b>
					<br>Malheureusement, comme vous ne parvenez pas au bout de cette opération, vous ne savez pas non plus ce que vous auriez pu produire !';		
				if ($forgeamage_pourcent <= $limite_comp) //Amélioration si < 40%
				{
					$req = 'select ameliore_competence_px('.$perso_cod.','.$forgeamage.','.$forgeamage_pourcent.') as resultat';
					$db->query($req);
					$db->next_record();
					$jet_ameliore = explode(';',$db->f('resultat'), 3);
					$jet = $jet_ameliore[0];
					$ameliore =  $jet_ameliore[1];
					$nouvelle_valeur =  $jet_ameliore[2];
					$contenu_page .= '<br>Votre jet d’amélioration est de '.$jet.'<br>'; // pos 7 8 9 10
					if ($ameliore == '1')
					{
						$contenu_page .= 'Vous avez donc <b>amélioré</b> cette compétence. <br>';
						$contenu_page .= 'Sa nouvelle valeur est '.$nouvelle_valeur.'<br><br>';
						$px_gagne = $px_gagne + 1;
					}
					else
					{
						$contenu_page .= 'Vous n’avez pas amélioré cette compétence.<br><br> ';
					}
				}
				$gain_renommee = -0.2;
				// Sinon, conséquences négatives (PV, dégradation de l'objet, déflagration, Vue, ...)
				$req = 'select enchantement_rate('. $perso_cod .') as resultat';
				$db->query($req);
				$db->next_record();
				$contenu_page .= $db->f('resultat');
    			$reussite = 0;		
			}
			if ($reussite == 1)
			{
				$contenu_page .= '<b>Vous parvenez à créer un composant d’enchantement. Il s’agit d’un "'. $nom .'</b>"
					<br>Vous devez maintenant regarder si vous pouvez l’associer à d’autres composants pour réaliser un enchantement<br>';
				//On crée le composant
				$req = 'select cree_objet_perso_nombre('.$composant_cod.','.$perso_cod.',1) as resultat';
				$db->query($req);
				$db->next_record();
				$req = 'select ameliore_competence_px('.$perso_cod.','.$forgeamage.','.$forgeamage_pourcent.') as resultat';
				$db->query($req);
				$db->next_record();
				$jet_ameliore = explode(';',$db->f('resultat'),3);
				$jet = $jet_ameliore[0];
				$ameliore =  $jet_ameliore[1];
				$nouvelle_valeur =  $jet_ameliore[2];
				$contenu_page .= '<br>Votre jet d’amélioration est de '.$jet.'<br>'; // pos 7 8 9 10
				if ($ameliore == '1')
				{
					$contenu_page .= 'vous avez donc <b>amélioré</b> cette compétence. <br>';
					$contenu_page .= 'Sa nouvelle valeur est '.$nouvelle_valeur.'<br><br>';
					$px_gagne = $px_gagne + 1;
				}
				else
				{
					$contenu_page .= 'vous n’avez pas amélioré cette compétence.<br><br> ';
				}
				$px_gagne = $px_gagne + 4;
				
				// On ajoute la formule aux formules connues
				$req = "select * from perso_formule where pfrm_perso_cod = $perso_cod and pfrm_frm_cod = $frm_cod";
				$db->query($req);
				if ($db->nf() == 0)
				{
					$req = "insert into perso_formule (pfrm_perso_cod, pfrm_frm_cod) values ($perso_cod, $frm_cod)";
					$db->query($req);
				}
			}
			// Diminution de la puissance magique de la case & mise à jour de l'énergie du perso et des PA et des px

			// Temporaire : vents infinis pour le marché de Léno 2013
			if ($position != 152794)	// == -6 / -7 dans la Halle Merveilleuse
			{
				$req = 'update positions set pos_magie = pos_magie - 500 where pos_cod = '. $position;
				$db->query($req);
				$db->next_record();
				$contenu_page .= '<br><br>La puissance magique de ce lieu a fortement diminué.';
			}
			
			$req_comp = "select perso_pa from perso where perso_cod = ". $perso_cod;
			$db->query($req_comp);
			$db->next_record();
			$pa = $db->f("perso_pa");
			
			if ($perte_pa > $pa) {
				$req = 'update perso set perso_energie = perso_energie - '.$energie_necessaire.',perso_pa = 0,perso_px = perso_px + '.$px_gagne.', perso_renommee_artisanat = perso_renommee_artisanat + (' . $gain_renommee . ') where perso_cod = '. $perso_cod;
			} else {
				$req = 'update perso set perso_energie = perso_energie - '.$energie_necessaire.',perso_pa = perso_pa - '.$perte_pa.',perso_px = perso_px + '.$px_gagne.', perso_renommee_artisanat = perso_renommee_artisanat + (' . $gain_renommee . ')  where perso_cod = '. $perso_cod;
			}

			$db->query($req);
			$db->next_record();
			$contenu_page .= '<br>Vous vous sentez assez épuisé, il va vous falloir récupérer un peu d’énergie avant de pouvoir espérer créer un autre composant d’enchantement.';
			//On supprime la pierre précieuse
			$req = 'select f_del_objet_generique('.$pierre.','.$perso_cod . ') as resultat';
			$db->query($req);
			$db->next_record();
			if ($puissance_case > 5000)
			{
				$contenu_page .= '<br><br>Malheureusement, la puissance a cet endroit était trop importante. Vous en subissez quelques ratées<br>';
				$req = 'select enchantement_rate('. $perso_cod .') as resultat';
				$db->query($req);
				$db->next_record();
				$contenu_page .= $db->f('resultat');	
			}
		break;
	}
