<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");

//
// Sélection des formules de potions connues
//
$db2 = new base_delain;
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
	
	if (!isset($methode))
		$methode = "debut";
	switch($methode)
	{
		case "debut":
			$contenu_page .= '<br>
				<form name="potions_connues" method="post" action="'. $PHP_SELF .'">
					<input type="hidden" name="methode" value="compo">
					<input type="hidden" name="tpot" value="'. $tpot .'">
					<table width="70%">
						<tr><td colspan="3"><strong>Listes des compositions déjà connues</strong></td></tr>';
			$req = 'select pfrm_frm_cod, gobj_nom, gobj_cod, frm_comp_cod from objet_generique, perso_formule, formule_produit, formule
				where pfrm_perso_cod = '. $perso_cod .'
					and gobj_cod = frmpr_gobj_cod
					and frmpr_frm_cod = pfrm_frm_cod
					and frm_cod = pfrm_frm_cod
					and (gobj_tobj_cod = 21 or gobj_tobj_cod = 24 or gobj_tobj_cod = 32 or gobj_tobj_cod = 33 or gobj_tobj_cod = 35 or gobj_tobj_cod = 37)
				order by gobj_nom';
			$db->query($req);
			if($db->nf() == 0)
			{
				$contenu_page .= '<tr><td colspan="3"><br />Vous ne connaissez aucune composition de potion<br /><br /></td></tr>';
			}
			else
			{
				// On commence par regarder si on a un flacon pour composer directement les potions
				$req = 'select obj_cod from objets, perso_objets
					where obj_gobj_cod = 412
						and perobj_perso_cod = ' . $perso_cod . '
						and perobj_obj_cod = obj_cod
					order by obj_gobj_cod limit 1';
				$db2->query($req);
				$is_flacon = ($db2->nf() > 0);

				$contenu_page .= '<tr><td><strong>Sélectionnez une potion connue </strong>(en italique, les composants dont vous manquez) :</td>';
				$contenu_page .= '<td colspan="2"><input type="submit" value="Composer la potion choisie ('. $pa .' PA)"  class="test"></td>';
				while($db->next_record())
				{
					$niveau_frm = ($db->f('frm_comp_cod') == 97) ? 1 : (($db->f('frm_comp_cod') == 100) ? 2 : 3);
					$req = 'select frmco_gobj_cod, frmco_num, gobj_nom, coalesce(obj_nombre, 0) as obj_nombre
						from objet_generique
						inner join formule_composant on frmco_gobj_cod = gobj_cod
						left outer join
						(
							select obj_gobj_cod, count(*) as obj_nombre
							from objets
							inner join perso_objets on perobj_obj_cod = obj_cod
							where perobj_perso_cod = ' . $perso_cod . '
							group by obj_gobj_cod
						) obj on obj_gobj_cod = gobj_cod
						where frmco_frm_cod = ' . $db->f('pfrm_frm_cod') . '
						order by gobj_nom';
					$db2->query($req);
					if($db2->nf() == 0)
					{
						$composants = '<br><em>Erreur, composants inconnus ...</em><br>';
					}
					else
					{
						$nombre_suffisant = true;
						$composants ='<br />(';
						while($db2->next_record())
						{
							$compo_ok = (intval($db2->f('frmco_num')) <= intval($db2->f('obj_nombre')));
							$nombre_suffisant = $nombre_suffisant && $compo_ok;
							$ital1 = ($compo_ok) ? '' : '<em>';
							$ital2 = ($compo_ok) ? '' : '</em>';
							$composants .= " $ital1". $db2->f('frmco_num') . ' ' . $db2->f('gobj_nom') . $ital2 . ",";
						}

						$niveau_ok = $niveau >= $db->f('frm_comp_cod');

						$disabled = (!$niveau_ok || !$nombre_suffisant || !$is_flacon) ? 'disabled="disabled"' : '';

						$composants = rtrim($composants,",");
						$composants .=')';
						$contenu_page .= '<tr>
							<td>';
						if ($niveau_ok)
							$contenu_page .= '<strong><a href="'. $PHP_SELF .'?&tpot='. $tpot .'&methode=description&potion=' . $db->f('gobj_cod') . '"> ' . $db->f('gobj_nom') .'</strong></a> '. $composants .'</td>';
						else
							$contenu_page .= '<strong><em>Potion trop complexe</em></strong> '. $composants .'</td>';
							
						$contenu_page .= '<td><input type="radio" name="potion" value="' . $db->f('gobj_cod') . '" '.$disabled.' /></td><td>Niveau ' . $niveau_frm . '</td></tr>';
					}
				}
			}
			$contenu_page .= '</form></table><hr>';
		break;

		case "compo":
            $erreur = 0;
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$db->query($req_pa);
			$db->next_record();
			if ($db->f("perso_pa") < $pa)
			{
				$contenu_page .= 'Vous n’avez pas assez de PA !<br>';
				break;
			}
			$potion = $_POST['potion'];
			$req = 'select pfrm_frm_cod,gobj_nom,gobj_cod, frm_comp_cod from objet_generique,perso_formule,formule_produit, formule
				where pfrm_perso_cod = '. $perso_cod .'
					and gobj_cod = frmpr_gobj_cod
					and frmpr_frm_cod	= pfrm_frm_cod
					and frm_cod = pfrm_frm_cod
					and (gobj_tobj_cod = 21 or gobj_tobj_cod = 24 or gobj_tobj_cod = 32 or gobj_tobj_cod = 33 or gobj_tobj_cod = 35 or gobj_tobj_cod = 37)
					and gobj_cod = '. $potion .'
				order by gobj_nom';
			$db->query($req);
			if(!$db->next_record())
			{
				$contenu_page .= '<br />Vous ne connaissez pas la composition de cette potion !<br /><br />';
				$erreur = 1;
			}
			else if($db->f('frm_comp_cod') > $niveau)
			{
				$contenu_page .= '<br />Vous n’avez pas le niveau d’alchimie requis pour confectionner cette potion !<br /><br />';
				$erreur = 1;
			}
			/*L'ensemble des controles est réalisé dans la fonction*/

			if ($erreur != 1)
			{
				$req = 'update perso set perso_pa = perso_pa - '. $pa .' where perso_cod = '. $perso_cod;
				$db->query($req);

				$contenu_page .= '<img src="http://www.jdr-delain.net/images/pos1.gif"><br>Vous êtes en train de préparer la potion !<br>';
				$req = 'select potions.compo_potion_connue('. $perso_cod .','. $potion .') as resultat';
				$db->query($req);
				$db->next_record();
				$result = explode(';',$db->f('resultat'));
				$contenu_page .= '<br>'. $result[1] . '<br>';
			}
		break;

		case "description":
			$req = 'select pfrm_frm_cod,gobj_nom,gobj_description from objet_generique,perso_formule,formule_produit
				where pfrm_perso_cod = '. $perso_cod .'
					and gobj_cod = frmpr_gobj_cod
					and frmpr_frm_cod = pfrm_frm_cod
					and gobj_cod = '. $potion;
			$db->query($req);
			if($db->nf() == 0)
			{
				$contenu_page .= '<br />Vous ne connaissez pas cette composition de potion !<br /><br />';
			}
			else
			{
				$db->next_record();
				$contenu_page .= '<strong>Description : </strong><br><br>' . $db->f('gobj_description') .'<br>';
			}
		break;
	}
}