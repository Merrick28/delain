<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
// contenu de la page
//
// On regarde s'il n'y a pas un probleme de toxicite quelconque
//
if (!isset($methode))
{
	$methode = "debut";
}
switch($methode)
{		
		case "debut":
				$req = 'select * from potions.perso_toxic
					where ptox_perso_cod = ' . $perso_cod;
				$db->query($req);
				if($db->nf() != 0)
				{
					$contenu_page .= '<br /><b>Attention !</b> Votre corps contient encore des restes d\'une potion bue précédemment.<br />
						Boire une autre potion maintenant vous expose à une toxicité qui pourrait avoir des effets regrettables sur votre organisme.<br />';
				}
				//
				// on cherche maintenant les potions disponibles
				//
				// PARTIE 1 - On regarde les potions buvables et identifiées
				//
				$req = "select obj_nom,obj_cod,obj_gobj_cod
					from objets,perso_objets,objet_generique
					where perobj_perso_cod = " . $perso_cod . "
					and perobj_obj_cod = obj_cod
					and obj_gobj_cod = gobj_cod
					and gobj_tobj_cod = 21
					order by obj_gobj_cod";
				$db->query($req);
				if($db->nf() == 0)
					$contenu_page .= "Vous n'avez aucune potion identifiée utilisable !";
				else
				{
					$contenu_page .= '<br>
															<form name="potions" method="post" action="'. $PHP_SELF .'">
															<input type="hidden" name="methode" value="potions">
															<table width="70%">
															<tr>
																<td class="soustitre">Liste des potions disponibles</td><td></td><td><input type="submit" value="Utiliser cette potion (3PA)"  class="test"></td>
															</tr>';
					while($db->next_record())
					{
						$contenu_page .= '<tr>	
																<td>' . $db->f('obj_nom') . '</td><td><input type="radio" name="potion" value="' . $db->f('obj_gobj_cod') . '"></td>
															</tr>';
					}
						$contenu_page .= '</table></form>';
				}
		break;
		case 'potions':
		$potion = $_POST['potion'];
			$req = 'select fpot_fonction from potions.fonction_potion where fpot_gobj_cod = ' . $potion;
			$db->query($req);
			if($db->nf() == 0)
				$contenu_page .= 'Erreur sur la fonction appelée.';
			else
			{
				$db->next_record();
				$fonction = $db->f('fpot_fonction');
				$req = 'select potions.' . $fonction . '(' . $perso_cod . ') as resultat';
				$db->query($req);
				$db->next_record();
				$contenu_page .= $db->f('resultat');
			}
			break;
}
?>
