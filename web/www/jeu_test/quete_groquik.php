<?php 
$db2 = new base_delain;
$contenu_page4 = '';
$erreur = 0	;

//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$req_comp = "select perso_cod as groquik, pos_cod, pos_etage
				from perso,perso_position,positions
				where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)
				and perso_quete = 'quete_groquik.php'
				and perso_cod = ppos_perso_cod
				and ppos_pos_cod = pos_cod";
$db->query($req_comp);
if($db->nf() == 0)
{
	$erreur = 1;
	$contenu_page4 .= 'Vous n\'avez pas accès à cette page !';
}
if (!isset($methode3))
{
	$methode3 = 'debut';
}
if ($erreur == 0)
{
	$db->next_record();
	$groquik = $db->f('groquik');
	$position = $db->f('pos_cod');
	$etage = $db->f('pos_etage');

	switch($methode3)
	{
		case "debut":
			$contenu_page4 .= "Lorsque vous approchez de ce personnage aux formes généreuses, il est en train de compter des pièces dans une bourse.<br />
	Il lève les yeux vers vous et dit : <br />
	« Bonjour aventurier, vous venez me vendre des pièces en chocolat ?
	Je vous les achète un bon prix »";
			$contenu_page4 .= "<p><a href=\"$PHP_SELF?methode3=vendre\">Vendre vos pièces en chocolat (300 Brouzoufs chacune) ...</a></p>";

		break;
		case "vendre":
			$req = "select perobj_obj_cod from perso_objets, objets 
					where perobj_obj_cod = obj_cod and obj_gobj_cod = 861
					and perobj_perso_cod = $perso_cod";
			$db->query($req);
			$nombre_vendus = $db->nf();
			$req = "update perso_objets set perobj_perso_cod = $groquik 
					where perobj_perso_cod = $perso_cod 
					and perobj_obj_cod in ($req)";
			$db->query($req);
			$req = "update perso 
					set perso_po = perso_po + 300 * $nombre_vendus 
					where perso_cod = $perso_cod";
			$db->query($req);
				
			
			$req = "select perobj_obj_cod from perso_objets, objets 
					where perobj_obj_cod = obj_cod and obj_gobj_cod = 861
					and perobj_perso_cod = $groquik
					limit 30";
			$db->query($req);
			$nombre_recoltes = $db->nf();
			$brouzoufs = 300 * $nombre_vendus;
			$manquants = 30 - $nombre_recoltes;

			$contenu_page4 .= "Voilà $brouzoufs Brouzoufs pour votre peine. N'hésitez pas à me ramener d'autres pièces... Il ne m'en manque plus que $manquants !<br />";

			// Compteur de pièces par joueur.
			$db2 = new base_delain;
			$db2->query("insert into log_1_avr (nom, nombre) values ($perso_cod, $nombre_vendus)");
			if ( $nombre_recoltes >= 30 )
			{
				// Retirer les pièces de l'inventaire, Créer un dragonnet.
				while ($db->next_record())
				{
					$req = "select f_del_objet(" . $db->f('perobj_obj_cod') . ")";
					$db2->query($req);
				}
				$db2->query("
						select cree_monstre_invasion(571, $etage) as dragonnet");
				$db2->next_record();
				$db2->query("update perso_position set ppos_pos_cod = $position
							where ppos_perso_cod = " . $db2->f('dragonnet'));
				$contenu_page4 .= 'Un nuage de chocolat en poudre apparaît, puis retzombe lentement laissant entrevoir un dragonnet... Visiblement peu coopératif. Vous feriez mieux de ne pas vous éterniser dans les parages...<br />';
			}
		break;
	}
}

echo $contenu_page4;

