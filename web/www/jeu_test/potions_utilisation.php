<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);


// contenu de la page
$contenu_page = '';


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
			$contenu_page .= '<br /><b>Attention !</b> Votre corps contient encore des restes d’une potion bue précédemment.<br />
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
				and obj_gobj_cod not in (561,412)
			order by obj_gobj_cod";
		$db->query($req);
		if($db->nf() == 0)
			$contenu_page .= "Vous n’avez aucune potion identifiée utilisable !<br>";
		else
		{
			$contenu_page .= '<br>
				<form name="potions" method="post" action="'. $PHP_SELF .'">
					<input type="hidden" name="methode" value="potions">
					<table width="70%">
						<tr>
							<td class="soustitre">Liste des potions disponibles</td><td></td><td><input type="submit" value="Utiliser cette potion (2PA)"  class="test"></td>
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
		$potion = (isset($_POST['potion'])) ? $_POST['potion'] : $_GET['potion'];
		$contenu_page .= boire_potion($potion);
		$contenu_page .= '<br><a href="inventaire.php">Retour à l’inventaire</a>';
	break;
	
	case 'potion_inventaire1':
		$potion = (isset($_POST['potion'])) ? $_POST['potion'] : $_GET['potion'];
		$req = 'select * from potions.perso_toxic where ptox_perso_cod = ' . $perso_cod;
		$db->query($req);
		if($db->nf() != 0)
		{
			$contenu_page .= '<br /><b>Attention !</b> Votre corps contient encore des restes d’une potion bue précédemment.<br />
				Boire une autre potion maintenant vous expose à une toxicité qui pourrait avoir des effets regrettables sur votre organisme.<br />
				Souhaitez-vous néanmoins continuer ?<br /><br />
				<a href="potions_utilisation.php?methode=potions&potion=' . $potion . '">Oui, advienne que pourra !</a><br />
				<a href="inventaire.php">Non, restons prudents...</a><br />';
		}
		else
		{
			$contenu_page .= boire_potion($potion);
			$contenu_page .= '<br><a href="inventaire.php">Retour à l’inventaire</a>';
		}
	break;
}

// Effectue effectivement l’action
function boire_potion($laPotion)
{
	global $db, $perso_cod;
	$resultat = '';
	$req = 'select fpot_fonction from potions.fonction_potion where fpot_gobj_cod = ' . $laPotion;
	$db->query($req);
	if($db->nf() == 0)
		$resultat = 'Erreur sur la fonction appelée.';
	else
	{
		$db->next_record();
		$fonction = $db->f('fpot_fonction');
		$req = 'select potions.' . $fonction . '(' . $perso_cod . ') as resultat';
		$db->query($req);
		$db->next_record();
		$resultat = $db->f('resultat');
	}
	return $resultat;
}
//
//
//
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//
//
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
