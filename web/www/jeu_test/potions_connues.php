<?php
$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();

//
// Sélection des formules de potions connues
//

$param    = new parametres();
$req_comp = "select pcomp_modificateur,pcomp_pcomp_cod from perso_competences 
	where pcomp_perso_cod = $perso_cod 
		and pcomp_pcomp_cod in (97,100,101)";
$stmt     = $pdo->query($req_comp);
if($result = $stmt->fetch())
{	
	$niveau = $result['pcomp_pcomp_cod'];
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
	$stmt = $pdo->query($req);
	$result = $stmt->fetch();
	$pa = $pa + $result['nombre'];
	if ($pa < 2) $pa = 2;

    $methode = get_request_var('methode', 'debut');
    $tpot    = $_REQUEST['tpot'];
	switch($methode)
	{
		case "debut":
			$contenu_page .= '<br>
				<form name="potions_connues" method="post" action="' . $_SERVER['PHP_SELF'] . '">
					<input type="hidden" name="methode" value="compo">
					<input type="hidden" name="tpot" value="' . $tpot . '">
					<table width="70%">
						<tr><td colspan="3"><strong>Listes des compositions déjà connues</strong></td></tr>';
			$req = 'select pfrm_frm_cod, gobj_nom, gobj_cod, frm_comp_cod from objet_generique, perso_formule, formule_produit, formule
				where pfrm_perso_cod = '. $perso_cod .'
					and gobj_cod = frmpr_gobj_cod
					and frmpr_frm_cod = pfrm_frm_cod
					and frm_cod = pfrm_frm_cod
					and (gobj_tobj_cod = 21 or gobj_tobj_cod = 39 or gobj_tobj_cod = 24 or gobj_tobj_cod = 32 or gobj_tobj_cod = 33 or gobj_tobj_cod = 35 or gobj_tobj_cod = 37)
				order by gobj_nom';
			$stmt = $pdo->query($req);
			if($stmt->rowCount() == 0)
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
				$stmt2 = $pdo->query($req);
				$is_flacon = ($stmt2->rowCount() > 0);

				$contenu_page .= '<tr><td><strong>Sélectionnez une potion connue </strong>(en italique, les composants dont vous manquez) :</td>';
				$contenu_page .= '<td colspan="2"><input type="submit" value="Composer la potion choisie ('. $pa .' PA)"  class="test"></td>';
				while($result = $stmt->fetch())
				{
					$niveau_frm = ($result['frm_comp_cod'] == 97) ? 1 : (($result['frm_comp_cod'] == 100) ? 2 : 3);
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
						where frmco_frm_cod = ' . $result['pfrm_frm_cod'] . '
						order by gobj_nom';
					$stmt2 = $pdo->query($req);
					if($stmt2->rowCount() == 0)
					{
						$composants = '<br><em>Erreur, composants inconnus ...</em><br>';
					}
					else
					{
						$nombre_suffisant = true;
						$composants ='<br />(';
						while($result2 = $stmt2->fetch())
						{
							$compo_ok = (intval($result2['frmco_num']) <= intval($result2['obj_nombre']));
							$nombre_suffisant = $nombre_suffisant && $compo_ok;
							$ital1 = ($compo_ok) ? '' : '<em>';
							$ital2 = ($compo_ok) ? '' : '</em>';
							$composants .= " $ital1". $result2['frmco_num'] . ' ' . $result2['gobj_nom'] . $ital2 . ",";
						}

						$niveau_ok = $niveau >= $result['frm_comp_cod'];

						$disabled = (!$niveau_ok || !$nombre_suffisant || !$is_flacon) ? 'disabled="disabled"' : '';

						$composants = rtrim($composants,",");
						$composants .=')';
						$contenu_page .= '<tr>
							<td>';
                        if ($niveau_ok)
                            $contenu_page .= '<strong><a href="' . $_SERVER['PHP_SELF'] . '?&tpot=' . $tpot . '&methode=description&potion=' . $result['gobj_cod'] . '"> ' . $result['gobj_nom'] . '</strong></a> ' . $composants . '</td>';
                        else
                            $contenu_page .= '<strong><em>Potion trop complexe</em></strong> ' . $composants . '</td>';
							
						$contenu_page .= '<td><input type="radio" name="potion" value="' . $result['gobj_cod'] . '" '.$disabled.' /></td><td>Niveau ' . $niveau_frm . '</td></tr>';
					}
				}
			}
			$contenu_page .= '</form></table><hr>';
		break;

		case "compo":
            $erreur = 0;

            if ($perso->perso_pa < $pa)
            {
                $contenu_page .= 'Vous n’avez pas assez de PA !<br>';
                break;
            }
            $potion = (int)$_POST['potion'];
            $req    = 'select pfrm_frm_cod,gobj_nom,gobj_cod, frm_comp_cod from objet_generique,perso_formule,formule_produit, formule
				where pfrm_perso_cod = ' . $perso_cod . '
					and gobj_cod = frmpr_gobj_cod
					and frmpr_frm_cod	= pfrm_frm_cod
					and frm_cod = pfrm_frm_cod
					and (gobj_tobj_cod = 21 or gobj_tobj_cod = 39 or gobj_tobj_cod = 24 or gobj_tobj_cod = 32 or gobj_tobj_cod = 33 or gobj_tobj_cod = 35 or gobj_tobj_cod = 37 or gobj_tobj_cod = 39)
					and gobj_cod = '. $potion .'
				order by gobj_nom';
			$stmt = $pdo->query($req);
			if(!$result = $stmt->fetch())
			{
				$contenu_page .= '<br />Vous ne connaissez pas la composition de cette potion !<br /><br />';
				$erreur = 1;
			}
			else if($result['frm_comp_cod'] > $niveau)
			{
				$contenu_page .= '<br />Vous n’avez pas le niveau d’alchimie requis pour confectionner cette potion !<br /><br />';
				$erreur = 1;
			}
			/*L'ensemble des controles est réalisé dans la fonction*/

			if ($erreur != 1)
            {
                $perso->perso_pa = $perso->perso_pa - $pa;
                $perso->stocke();

                $contenu_page .= '<img src="http://www.jdr-delain.net/images/pos1.gif"><br>Vous êtes en train de préparer la potion !<br>';
                $req          = 'select potions.compo_potion_connue(' . $perso_cod . ',' . $potion . ') as resultat';
                $stmt         = $pdo->query($req);
                $result       = $stmt->fetch();
                $result       = explode(';', $result['resultat']);
                $contenu_page .= '<br>' . $result[1] . '<br>';
            }
		break;

		case "description":
            $potion = $_REQUEST['potion'];
			$req = 'select pfrm_frm_cod,gobj_nom,gobj_description from objet_generique,perso_formule,formule_produit
				where pfrm_perso_cod = '. $perso_cod .'
					and gobj_cod = frmpr_gobj_cod
					and frmpr_frm_cod = pfrm_frm_cod
					and gobj_cod = '. $potion;
			$stmt = $pdo->query($req);
			if($stmt->rowCount() == 0)
			{
				$contenu_page .= '<br />Vous ne connaissez pas cette composition de potion !<br /><br />';
			}
			else
			{
				$result = $stmt->fetch();
				$contenu_page .= '<strong>Description : </strong><br><br>' . $result['gobj_description'] .'<br>';
			}
		break;
	}
}