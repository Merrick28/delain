<?php 
//
// ATTENTION ! Il semble que cette page ne soit pas utilisée ??!!
// Voir plutôt potions_composition.php
//
// Enchaînement : 
// "Début" -> Affichage des flacons disponibles (flacons vides ou potions en cours de composition).
// -> on peut ajouter un composant (=> "compo")
// -> on peut finaliser une potion (=> "fabrication")
// -> on peut voir le détail d’une potion en cours de fabrication
//
// "compo" => ajout d’un composant à une potion. Géré par select potions.compo_potions('. $perso_cod .','. $fiole .','. $composant .')
//
// "fabrication" => 
//
// "détail" => affiche les composants déjà placés dans la potion
//
if(isset($_POST['ajout'])) 
{
	$methode = 'compo';
}
elseif(isset($_POST['fabrication']))
{
	$methode = 'fabrication';
}
if (!isset($methode))
	$methode = "debut";
switch($methode)
{
	case "debut":
		$contenu_page .= '<br>
			<form name="potions" method="post" action="'. $PHP_SELF .'">
				<input type="hidden" name="methode" value="compo">
				<input type="hidden" name="t" value="'. $t .'">';
		$req = 'select obj_nom,obj_gobj_cod from objets,perso_objets,objet_generique
			where obj_gobj_cod = gobj_cod
				and perobj_perso_cod = ' . $perso_cod . '
				and perobj_obj_cod = obj_cod
				and gobj_tobj_cod = 22
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
				$choix_flacon .= "<input type='radio' name='flacon' value='$obj_cod' id='$obj_cod'> <label for='$obj_cod'><a href='$PHP_SELF?&t=$t&methode=detail&fiole=$obj_cod'>" . $db->f('obj_nom') . '</a> (' . $db->f('nombre') . ' composants)</label><br />';
			}			
		}
		$texte_bouton_ajout = '<input type="submit" name="ajout" value="Ajouter un composant (0PA)" class="test">';
		$texte_bouton_final = '<input type="submit" name="fabrication" value="Finaliser la potion (8PA)"  class="test">';
		
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

		$contenu_page .= '</form>';
	break;

	case "compo":
		$fiole= $_POST['flacon'];
		$composant= $_POST['composant'];
		if (!isset($fiole))
		{
			$contenu_page .= 'Vous n’avez sélectionné aucune préparation ou fiole vide !';
			$erreur = 1;
		}
		if (!isset($composant))
		{
			$contenu_page .= 'Vous n’avez sélectionné aucun composant !';
			$erreur = 1;
		}
		if ($erreur != 1)
		{
			$req = 'select potions.compo_potions('. $perso_cod .','. $fiole .','. $composant .') as resultat';
			$db->query($req);
			$db->next_record();
			$contenu_page .= $db->f('resultat');				
		}
		else
		{
			$contenu_page .= '<br>Le traitement de votre requête n’est pas possible !';
		}
	break;

	case "detail":
		$contenu_page .= '<b>Détail de la potion sélectionnée : </b><br><br><table>';
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
			$contenu_page .= '<tr><td><br />Vous ne possédez pas cette potion, vous ne pouvez donc pas en voir le détail !<br /><br /></td></tr>';
		}
		else
		{
			$contenu_page .= '<tr><td class="soustitre2">Composants</td><td class="soustitre2"> Quantités</td></tr>';
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
		/*On va chercher  si une potion correspond à la composition de la fiole en question*/
		$query = 'select * from formule f,formule_produit,objet_generique where frm_cod = frmpr_frm_cod and frmpr_gobj_cod = gobj_cod and ';
		$compteur = 1;
		$req = 'select flaccomp_obj_cod, flaccomp_comp_cod, sum(flaccomp_number) as nombre, gobj_nom from potions.flacon_composants, objet_generique, perso_objets
			where flaccomp_obj_cod = '. $fiole .'
				and flaccomp_comp_cod = gobj_cod
				and perobj_perso_cod = '. $perso_cod .'
				and perobj_obj_cod = flaccomp_obj_cod
			group by flaccomp_comp_cod,flaccomp_obj_cod,gobj_nom
			order by gobj_nom';
		$db->query($req);
		$res_parm = pg_exec($req);
		$nombre_composants = pg_numrows($res_parm);
		if($db->nf() == 0)
		{
			$contenu_page .= '<br />Vous ne possédez pas cette potion, vous ne pouvez donc pas la finaliser !<br /><br />';
		}
		else
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
		$db->query($query);
		if($db->nf() == 0)
		{
			$contenu_page .= '<br />Cette formule ne peut rien produire du tout !<br /><br />';
		}
		else	// on a trouvé une formule correspondant à la potion
		{
			$db->next_record();
			$nom = $db->f('gobj_nom');
			$g_objet = $db->f('frmpr_gobj_cod');
			$description = $db->f('gobj_description');
			$contenu_page .= '<br>En assemblant les différents éléments, vous parvenez à fabriquer une nouvelle potion ! Il semblerait que ce soit une ' . $nom . '<br />
				Prenez garde tout de même. Parfois, l’assemblage de composants peut donner des résultats que vous n’êtes pas tout à fait capable de comprendre et d’analyser. Une erreur est vite arrivée.';
			$req = 'update objets set obj_nom = e\''. pg_escape_string($nom) .'\',obj_gobj_cod = '. $g_objet .',obj_description = e\''. pg_escape_string($description) .'\' where obj_cod = '. $fiole;
			$db->query($req);
			$db->next_record();
		}
	break;
}
?>
