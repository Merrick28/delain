<?php if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";
//
// on regarde si le joueur est bien sur le lieu qu'on attend
//
$erreur = 0;
$db2 = new base_delain;
$contenu_page = '';
//
// on vérifie que le type d'appel soit bien passé
// s'il n'est pas passé, on considère qu'on est sur un lieu
//
if(!isset($type_appel))
	$type_appel = 0;
//
// en fonction du type d'appel, on vérifie, soit le lieu, soit la compétence.
//
			$comp_enchantement = 0;
		$req = 'select pcomp_modificateur,pcomp_pcomp_cod from perso_competences
			where pcomp_perso_cod = ' . $perso_cod . '
			and pcomp_pcomp_cod in (88,102,103)';
		$db->query($req);
		if($db->nf() != 0)
		{
			$db->next_record();
			$comp_enchantement = $db->f('pcomp_pcomp_cod');
			$comp_enchantement_percent = $db->f('pcomp_modificateur');
		}

switch($type_appel)
{
	case 0:
		$erreur = 0;
		if(!defined("APPEL"))
			die("Erreur d'appel de page !");
		if (!$db->is_lieu($perso_cod))
		{
			$contenu_page .= "<p>Erreur ! Vous n'êtes pas sur une boutique de l'enchanteur !!!";
			$erreur = 1;
		}
		if ($erreur == 0)
		{
			$tab_lieu = $db->get_lieu($perso_cod);
			$lieu_cod = $tab_lieu['lieu_cod'];
			if ($tab_lieu['type_lieu'] != 26)
			{
				$erreur = 1;
				$contenu_page .= "<p>Erreur ! Vous n'êtes pas sur une boutique de l'enchanteur !!!";
			}
		}
		break;
	case 2: //Cette fois, on vérifie qu'un perso sur la case est un enchanteur PNJ
				$tab_quete = $db->get_perso_quete($perso_cod);
				foreach($tab_quete as $key=>$val)
				{
					if ($val == 'enchanteur.php')
					{
						$erreur = 0;
					}
				}
				if ($erreur != 0)
				{
					$contenu_page .= "Aucun enchanteur ne se trouve près de vous";
					$erreur = 1;
				}
		break;
	//
	// on rajoute un cas "default" pour les petits malins qui essaieraient de tricher
	//
	default:
		$contenu_page .= "<p>Erreur sur le type d'appel !";
		$erreur = 1;
		break;
}
//
// fin des controles principaux
//
if (!isset($methode))
	$methode = 'debut';

if ($erreur == 0)
{
	switch($methode)
	{
		case "debut":
			$contenu_page .= '<strong>Un enchanteur vous aborde :</strong><br>';
			//
			// requête pour voir si on a des objets enchantables
			//
			$req = 'select obj_cod,obj_nom
				from objets,perso_objets
				where perobj_perso_cod = ' . $perso_cod . '
				and perobj_identifie = \'O\'
				and perobj_obj_cod = obj_cod
				and obj_enchantable = 1 ';
			$db->query($req);
			if($db->nf() == 0)
				$contenu_page .= '« <em>Désolé, vous ne possédez aucun objet sur lequel je puisse lancer un enchantement.</em>»';
			else
			{
				$contenu_page .= '« <em>Vous possédez peut être un objet sur lequel je puisse lancer un enchantement, voyons voir.... <br>
				Voici les objets sur lesquels je peux intervenir : </em>»<br>';
				while($db->next_record())
					$contenu_page .= '<br><strong><a href="' . $PHP_SELF . '?methode=enc&obj=' . $db->f('obj_cod') . '&type_appel=' . $type_appel . '">' . $db->f('obj_nom') . '</a></strong>';
			}
			$contenu_page .= '<br><br>';
			if ($comp_enchantement == 0)
			{
				$req = "select pquete_param_texte from quete_perso,perso_competences
											where pquete_quete_cod = 15 
											and pquete_nombre = 1
											and pquete_perso_cod = ". $perso_cod;
				$db->query($req);
				if($db->nf() == 0)
				{
				$contenu_page .= '« <em>Mais j\'y pense, vous voulez peut-être devenir vous même un enchanteur de renom ?
														<br>Si c\'est le cas Dites le moi, et je vous proposerais une énigme à résoudre pour passer ce premier cap.</em>»
														<br><br>Hum, voilà quelque chose de tentant ! <a href="' . $PHP_SELF . '?methode=niv1&comp=88"><strong>Allez je me lance !</strong></a><br><br>';
				}
				else
				{
					$contenu_page .= '« <em>Vous voilà de nouveau ? Vous avez donc bien cogité sur mon problème ?
														<br>Quelle est la solution que vous me proposez ?</em>»<br><br>
														Quel code proposez vous ? <form method="post" action="' . $PHP_SELF . '">
														<input type="hidden" name="methode" value="code">
														<input type="text" name="code">
														<input type="submit" value="Valider" class="test">';
				}
			}
			else if ($comp_enchantement == 88)
			{
				if ($comp_enchantement_percent < 85)
				{
				$contenu_page .= '« <em>Vous revoilà déjà ?
													<br>Vous manquez de pratique pour prétendre à ce que je vous apprenne autre chose !
													Revenez donc lorsque vous serez un peu plus expérimenté. 
													<br>L\'enseignement est une chose, la pratique et l\'expérience une autre !
													</em>»
													<br><br>Un niveau minimum de <strong>85%</strong> dans votre compétence en forgeamage est nécessaire avant de pouvoir passer au niveau 2<br><br>';
				}
				else
				{
					$contenu_page .= '« <em>Ah, je vois que vous avez investi sur l\'enseignement que je vous avais donné !
														C\'est une bonne chose, et je me verrais ravi de vous en apprendre un peu plus.
													<br>Bon, malheureusement, je manque un peu de moyen en ce moment, et il faudra que vous me fournissiez quelques brouzoufs pour que puisse acheter des composants.
													<br>Donnez moi <strong>10000 brouzoufs</strong>, et je ferais de vous un enchanteur accompli !
													</em>»
													<br><br>Hum, voilà quelque chose de tentant ! <a href="' . $PHP_SELF . '?methode=niv2&comp=102"><strong>Allez je me lance !</strong></a><br><br>';
				}
			}
			else if ($comp_enchantement == 102)
			{
				if ($comp_enchantement_percent < 100)
				{
				$contenu_page .= '« <em>Vous revoilà déjà ?
													<br>Vous manquez de pratique pour prétendre à ce que je vous apprenne autre chose !
													Revenez donc lorsque vous serez un peu plus expérimenté. 
													<br>L\'enseignement est une chose, la pratique et l\'expérience une autre !
													</em>»
													<br><br>Un niveau minimum de <strong>100%</strong> dans votre compétence en forgeamage est nécessaire avant de pouvoir passer au niveau 2<br><br>';
				}
				else
				{
					$contenu_page .= '« <em>Ah, je vois que vous avez investi sur l\'enseignement que je vous avais donné !
														C\'est une bonne chose, et je me verrais ravi de vous en apprendre un peu plus.
													<br>Bon, malheureusement, je manque un peu de moyen en ce moment, et il faudra que vous me fournissiez quelques brouzoufs pour que puisse acheter des composants.
													<br>Donnez moi <strong>20000 brouzoufs</strong>, et je ferais de vous un enchanteur expérimenté !
													</em>»
													<br><br>Hum, voilà quelque chose de tentant ! <a href="' . $PHP_SELF . '?methode=niv3&comp=103"><strong>Allez je me lance !</strong></a><br><br>';					
				}

			}						
			else if ($comp_enchantement == 103)
			{
				$contenu_page .= '« <em>Cher confrère ! Nous pouvons deviser si vous le souhaitez des meilleurs endroits pour lancer nos enchantements !
													<br>Ces vents magiques sont tellement difficiles à capturer ...</em>»
													<br><br>Et l\'enchanteur se lance dans des palabres sans fin ...<br><br>';
			}
					
			break;
		case "enc":
			//
			// on regarde si l'objet est bien enchantable, et quels enchantements on peut lui associer
			//
			$req = 'select gobj_tobj_cod,gobj_distance
				from objet_generique,objets
				where obj_cod = ' . $obj . '
				and obj_gobj_cod = gobj_cod ';
			$db->query($req);
			$db->next_record();
			switch($db->f("gobj_tobj_cod"))
			{
				case 1: 	// arme
					if($db->f('gobj_distance') == 'O')	//arme distance
						$app_req = ' where tenc_arme_distance = 1 ';
					else	// arme contact
						$app_req = ' where tenc_arme_contact = 1 ';
					break;
				case 2:	// armure
					$app_req = ' where tenc_armure = 1 ';
					break;
				case 4:	// casque
					$app_req = ' where tenc_casque = 1 ';
					break;
				case 6:	//artefact
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
			$db->query($req);
			if($db->nf() == 0)
				$contenu_page .= 'Non, désolé, je ne peux rien faire avec ce que vous avez en inventaire. Il vous faut trouver d\'autres matériaux afin que je puisse enchanter cet objet.
													<br>Le forgeamage demande certes de l\'expertise, mais ausis d\'avoir les objets nécessaires pour cela.';
			else
			{
				$contenu_page .= 'Voici ce que nous pouvons tenter de faire avec ça : 
				<table>
					<tr>
						<td class="soustitre2"><strong>Nom</strong></td>
						<td class="soustitre2"><strong>Description</strong></td>
						<td class="soustitre2"><strong>Cout</strong></td>
						<td class="soustitre2"><strong>Nécessite</strong></td>
					</tr>';
				while($db->next_record())
				{
					$contenu_page .= '<tr>
						<td class="soustitre2"><a href="action.php?methode=enc&enc=' . $db->f('enc_cod') . '&obj=' . $obj . '&type_appel=' . $type_appel . '">' . $db->f('enc_nom') . '</a></td>
						<td>' . $db->f('enc_description') . '</td>
						<td class="soustitre2">' . $db->f('enc_cout') . ' brouzoufs - ' . $db->f('enc_cout_pa') .' PA</td>
						<td>';
					$req = 'select gobj_nom,oenc_nombre
						from enc_objets,objet_generique
						where oenc_enc_cod = ' . $db->f('enc_cod') . '
						and oenc_gobj_cod = gobj_cod ';
					$db2->query($req);
					while($db2->next_record())
						$contenu_page .= $db2->f('oenc_nombre') . ' ' . $db2->f('gobj_nom') . '<br>';
					$contenu_page .= '</td></tr>';
					//$contenu_page .= '<br><a href="' . $PHP_SELF . '?methode=enc2&enc=' . $db->f('enc_cod') . '&obj=' . $obj . '">' . $db->f('enc_nom') . '</a>';	
				}
				$contenu_page .= '</table>';
			}
			break;
			case "niv1": // Code aléatoire
				$req = "select pquete_param_texte from quete_perso,perso_competences
											where pquete_quete_cod = 15 
											and pquete_nombre = 1
											and pquete_perso_cod = ". $perso_cod;
				$db->query($req);
				if($db->nf() == 0)
				{
					$req = "select enchanteur(".$perso_cod.",".$comp.") as resultat";
					$db2->query($req);
					$db2->next_record();
					$contenu_page .= $db2->f('resultat');
				}
				else
				{
					$contenu_page .= '« <em>Vous voilà de nouveau ? Vous avez donc bien cogité sur mon problème ?
														<br>Quelle est la solution que vous me proposez ?</em>»<br><br>
														Quel code proposez vous ? <form method="post" action="' . $PHP_SELF . '">
														<input type="hidden" name="methode" value="code">
														<input type="text" name="code">
														<input type="submit" value="Valider" class="test">';
				}

			break;
			case "niv2": // 10000 brouzoufs et limite comp
					$req = "select enchanteur(".$perso_cod.",".$comp.") as resultat";
					$db->query($req);
					$db->next_record();
					$contenu_page .= $db->f('resultat');
			break;
			case "niv3": // 20000 brouzoufs et limite comp
					$req = "select enchanteur(".$perso_cod.",".$comp.") as resultat";
					$db->query($req);
					$db->next_record();
					$contenu_page .= $db->f('resultat');		
			break;						
			case "code":
				$code = $_POST['code'];
				$req = "select pquete_param_texte,perso_pa from quete_perso,perso
											where pquete_quete_cod = 15 
											and pquete_perso_cod = ".$perso_cod."
											and perso_cod = pquete_perso_cod
											and pquete_nombre = 1";
				$db->query($req);
				$db->next_record();
				if($db->nf() == 0)				
				{
					$contenu_page .= 'Vous n\'avez rien à faire ici !';
					break;
				}
				else if ($db->f('perso_pa') != 12)				
				{
					$contenu_page .= 'Vous n\'avez pas suffisamment de PA pour réaliser cette action !';
					break;
				}
				else if ($code == $db->f('pquete_param_texte'))
				{
					//Mise à jour de la comp enchanteur
					$req2 = "select enchanteur(".$perso_cod.",88) as resultat";
					$db2->query($req2);
					$db2->next_record();
					$contenu_page .= '« <em>'.$db2->f('resultat').'</em>»<br><br>
																		Vous bénéficiez maintenant d\'une nouvelle compétence. Bonne découverte !';
				}
				else
				{
					$contenu_page .= '« <em>Hum, je crois qu\'il y a méprise, vous n\'y êtes pas du tout !
														<br>Prenez un peu de temps pour réfléchir un peu plus ...</em>»<br><br>';
					$req2 = "update perso set perso_pa = perso_pa - 6 where perso_cod = ".$perso_cod;
					$db2->query($req2);
					$db2->next_record();
				}
			break;
	}
	
}

