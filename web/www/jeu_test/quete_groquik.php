<?php 

$contenu_page4 = '';
$erreur = 0	;

//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$req_comp = "select perso_cod as groquik, pos_cod, pos_etage
				from perso,perso_position,positions
				where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)
				and perso_quete = 'quete_groquik.php'
				and perso_cod = ppos_perso_cod
				and ppos_pos_cod = pos_cod";
$stmt = $pdo->query($req_comp);
if($stmt->rowCount() == 0)
{
	$erreur = 1;
	$contenu_page4 .= 'Vous n\'avez pas accès à cette page !';
}
$methode3 = get_request_var('methode3', 'debut');
if ($erreur == 0)
{
	$result = $stmt->fetch();
	$groquik = $result['groquik'];
	$position = $result['pos_cod'];
	$etage = $result['pos_etage'];

	switch($methode3)
	{
		case "debut":
			$contenu_page4 .= "Lorsque vous approchez de ce personnage aux formes généreuses, il est en train de compter des pièces dans une bourse.<br />
	Il lève les yeux vers vous et dit : <br />
	« Bonjour aventurier, vous venez me vendre des pièces en chocolat ?
	Je vous les achète un bon prix »";
            $contenu_page4 .= "<p><a href=\"$_SERVER['PHP_SELF']?methode3=vendre\">Vendre vos pièces en chocolat (300 Brouzoufs chacune) ...</a></p>";

		break;
		case "vendre":
			$req = "select perobj_obj_cod from perso_objets, objets 
					where perobj_obj_cod = obj_cod and obj_gobj_cod = 861
					and perobj_perso_cod = $perso_cod";
			$stmt = $pdo->query($req);
			$nombre_vendus = $stmt->rowCount();
			$req = "update perso_objets set perobj_perso_cod = $groquik 
					where perobj_perso_cod = $perso_cod 
					and perobj_obj_cod in ($req)";
			$stmt = $pdo->query($req);
			$req = "update perso 
					set perso_po = perso_po + 300 * $nombre_vendus 
					where perso_cod = $perso_cod";
			$stmt = $pdo->query($req);
				
			
			$req = "select perobj_obj_cod from perso_objets, objets 
					where perobj_obj_cod = obj_cod and obj_gobj_cod = 861
					and perobj_perso_cod = $groquik
					limit 30";
			$stmt = $pdo->query($req);
			$nombre_recoltes = $stmt->rowCount();
			$brouzoufs = 300 * $nombre_vendus;
			$manquants = 30 - $nombre_recoltes;

			$contenu_page4 .= "Voilà $brouzoufs Brouzoufs pour votre peine. N'hésitez pas à me ramener d'autres pièces... Il ne m'en manque plus que $manquants !<br />";

			// Compteur de pièces par joueur.

            $pdo->query("insert into log_1_avr (nom, nombre) values ($perso_cod, $nombre_vendus)");
			if ( $nombre_recoltes >= 30 )
			{
				// Retirer les pièces de l'inventaire, Créer un dragonnet.
				while ($result = $stmt->fetch())
				{
					$req = "select f_del_objet(" . $result['perobj_obj_cod'] . ")";
					$stmt2 = $pdo->query($req);
				}
                $pdo->query("
						select cree_monstre_invasion(571, $etage) as dragonnet");
				$result2 = $stmt2->fetch();
                $pdo->query("update perso_position set ppos_pos_cod = $position
							where ppos_perso_cod = " . $result2['dragonnet']);
				$contenu_page4 .= 'Un nuage de chocolat en poudre apparaît, puis retzombe lentement laissant entrevoir un dragonnet... Visiblement peu coopératif. Vous feriez mieux de ne pas vous éterniser dans les parages...<br />';
			}
		break;
	}
}

echo $contenu_page4;

