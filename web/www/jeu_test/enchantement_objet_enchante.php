<?php 

//Interface pour enchanter un objet, par un perso et non un PNJ

if (!isset($methode))
	$methode = 'debut';

switch($methode)
{
	case "debut":
		$contenu_page .= '<strong>Vous regardez les objet enchantables que vous possédez.</strong><br>';
		//
		// requête pour voir si on a des objets enchantables
		//
		$req = 'select obj_cod,obj_nom
			from objets,perso_objets,objet_generique
			where perobj_perso_cod = ' . $perso_cod . '
			and perobj_identifie = \'O\'
			and gobj_cod = obj_gobj_cod
			and perobj_obj_cod = obj_cod
			and obj_enchantable = 1
			and gobj_chance_enchant > 0
			and obj_gobj_cod not in (834,835,836,837,838,839,840,841,840,842,843,844,845)';
		$stmt = $pdo->query($req);
		if($stmt->rowCount() == 0)
			$contenu_page .= 'Désolé, vous ne possédez aucun objet sur lequel vous pourriez procéder au forgeamage.';
		else
		{
			$contenu_page .= 'Voici les objets sur lesquels vous pouvez intervenir : <br>';
			while($result = $stmt->fetch())
				$contenu_page .= '<br><strong><a href="' . $PHP_SELF . '?methode=enc&obj=' . $result['obj_cod'] . '&t_ench=3">' . $result['obj_nom'] . '</a></strong>';
		}
		$contenu_page .= '<br><br>';
	break;

	case "enc":
		//
		// on regarde si l'objet est bien enchantable, et quels enchantements on peut lui associer
		//
		$req = 'select gobj_tobj_cod,gobj_distance
			from objet_generique,objets
			where obj_cod = ' . $obj . '
			and obj_gobj_cod = gobj_cod ';
		$stmt = $pdo->query($req);
		$result = $stmt->fetch();
		switch($result['gobj_tobj_cod'])
		{
			case 1: 	// arme
				if($result['gobj_distance'] == 'O')	//arme distance
					$app_req = ' where tenc_arme_distance = 1 ';
				else	// arme contact
					$app_req = ' where tenc_arme_contact = 1 ';
				break;
			case 2:	    // armure
			case 40:	// gants
			case 41:	// bottes
				$app_req = ' where tenc_armure = 1 ';
				break;
			case 4:	    // casque
				$app_req = ' where tenc_casque = 1 ';
				break;
			case 6:	    //artefact
			case 7:	    //relique
			case 27:	//signes distinctifs
				$app_req = ' where tenc_artefact = 1 ';
				break;
		}
		//
		// Sur la requête suivante, si on veut afficher tous les enchantements disponibles, il faut supprimer la ligne
		// avec la fonction obj_enchantement
		//
		$req = 'select enc_cod,enc_nom,enc_description,enc_cout,enc_cout_pa
			from enc_type_objet,enchantements' . $app_req . '
			and tenc_enc_cod = enc_cod 
			and obj_enchantement(' . $perso_cod . ',enc_cod,' . $obj . ') = 1 ';
		$stmt = $pdo->query($req);
		$enchantements_dispo = '';
		$premier = true;
		if($stmt->rowCount() == 0)
			$contenu_page .= 'En analysant votre équipement, vous voyez que vous ne pouvez rien faire avec ce que vous avez en inventaire. Il vous faut <strong>trouver d’autres composants ou les créer</strong> afin de pouvoir enchanter cet objet.
				<br>Le forgeamage demande certes de l’expertise, mais aussi d’avoir les composants nécessaires pour cela.';
		else
		{
			$contenu_page .= '<p>Voici ce que vous pouvez tenter de faire avec ça : </p>
			<table>
				<tr>
					<td class="soustitre2"><strong>Nom</strong></td>
					<td class="soustitre2"><strong>Description</strong></td>
					<td class="soustitre2"><strong>Coût</strong></td>
					<td class="soustitre2"><strong>Nécessite</strong></td>
				</tr>';
			while($result = $stmt->fetch())
			{
				$energie_cout = (($result['enc_cout_pa'] * 20) / 3);
				$contenu_page .= '<tr>
					<td class="soustitre2"><a href="action.php?methode=enc&enc=' . $result['enc_cod'] . '&obj=' . $obj . '&type_appel=1&t_ench=3">' . $result['enc_nom'] . '</a></td>
					<td>' . $result['enc_description'] . '</td>
					<td class="soustitre2">' . $result['enc_cout_pa'] .' PA / '. $energie_cout .' énergie</td>
					<td>';
				$req = 'select gobj_nom, oenc_nombre
					from enc_objets, objet_generique
					where oenc_enc_cod = ' . $result['enc_cod'] . '
					and oenc_gobj_cod = gobj_cod ';
				$stmt2 = $pdo->query($req);
				while($result2 = $stmt2->fetch())
					$contenu_page .= $result2['oenc_nombre'] . ' ' . $result2['gobj_nom'] . '<br>';
				$contenu_page .= '</td></tr>';

				$enchantements_dispo .= (($premier) ? '' : ',') . $result['enc_cod'];
				$premier = false;
				
				// On vérifie si on connaissait déjà cet enchantement. Sinon, on le mémorise.
				$req = 'select * from perso_enchantement
					where perenc_enc_cod = ' . $result['enc_cod'] . '
					and perenc_perso_cod = ' . $perso_cod;
				$stmt2 = $pdo->query($req);
				if ($stmt2->rowCount() == 0)
				{
					$req = 'insert into perso_enchantement (perenc_enc_cod, perenc_perso_cod)
						values (' . $result['enc_cod'] . ', ' . $perso_cod . ')';
					$stmt2 = $pdo->query($req);
				}
			}
			$contenu_page .= '</table>';
		}

		// Liste des enchantements connus et non applicables
		$req = "select enc_cod, enc_nom, enc_description, enc_cout, enc_cout_pa
			from perso_enchantement
			inner join enchantements on enc_cod = perenc_enc_cod
			inner join enc_type_objet on tenc_enc_cod = enc_cod
			$app_req
			and perenc_perso_cod = $perso_cod ";
			if ($enchantements_dispo != '')
				$req .= " and perenc_enc_cod not in ($enchantements_dispo)";
		$stmt = $pdo->query($req);
		if($stmt->rowCount() > 0)
		{
			$contenu_page .= '<br /><p>Voici ce que vous savez que vous auriez pu faire, si vous aviez eu les composants nécessaires : </p>
			<table>
				<tr>
					<td class="soustitre2"><strong>Nom</strong></td>
					<td class="soustitre2"><strong>Description</strong></td>
					<td class="soustitre2"><strong>Coût</strong></td>
					<td class="soustitre2"><strong>Nécessite</strong></td>
				</tr>';
			while($result = $stmt->fetch())
			{
				$energie_cout = (($result['enc_cout_pa'] * 20) / 3);
				$contenu_page .= '<tr>
					<td class="soustitre2">' . $result['enc_nom'] . '</td>
					<td>' . $result['enc_description'] . '</td>
					<td class="soustitre2">' . $result['enc_cout_pa'] .' PA / '. $energie_cout .' énergie</td>
					<td>';
				$req = 'select gobj_nom,oenc_nombre
					from enc_objets,objet_generique
					where oenc_enc_cod = ' . $result['enc_cod'] . '
					and oenc_gobj_cod = gobj_cod ';
				$stmt2 = $pdo->query($req);
				while($result2 = $stmt2->fetch())
					$contenu_page .= $result2['oenc_nombre'] . ' ' . $result2['gobj_nom'] . '<br>';
				$contenu_page .= '</td></tr>';
			}
			$contenu_page .= '</table>';
		}
	break;
}