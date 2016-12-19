<?php 
include "classes.php";
// initialisation des variables
$i = 0;
$code_retour = '';
$db = new base_delain;
$db2 = new base_delain;
// recherche du perso
$req = "select compt_cod,compt_hibernation from compte,perso_compte,perso
	where compt_nom = '$login'
	and md5(compt_password) = '$pass'
	and compt_actif = 'O'
	and pcompt_compt_cod = compt_cod
	and pcompt_perso_cod = $numperso 
	and perso_cod = $numperso
	and perso_actif != 'N'";
$db->query($req);
if($db->nf() == 0)
	echo "Infos persos non trouvées ou compte en hibernation";
else
{
	$req = "select tablename from pg_tables where tablename like 'perso_vue_pos%'";
	$db->query($req);
	while($db->next_record())
	{
		$tab_etage[$i] = $db->f('tablename');
		$i++;
	}
	foreach($tab_etage as $key => $val)
	{
		$req = "select pvue_pos_cod
			from perso_vue_pos_1,positions
			where pvue_perso_cod = $numperso 
			and pvue_pos_cod = pos_cod
			and pos_x <= 50
			and pos_x >= -50";
		/*$req = "select pvue_pos_cod,pos_x,pos_y,etage_libelle,etage_affichage,pos_type_aff,coalesce(pos_decor,0) as pos_decor,coalesce(pos_decor_dessus,0) as pos_decor_dessus
			from $val,positions,etage
			where pvue_perso_cod = $numperso
			and pvue_pos_cod = pos_cod
			and etage_numero = pos_etage
			limit 100";*/
		$db->query($req);
		while($db->next_record())
		{
			//
			// on boucle maintenant sur les positions connues de l'étage
			//
			// les murs
			$req2 = "select mur_type from murs
				where mur_pos_cod = " . $db->f("pvue_pos_cod");
			$db2->query($req);
			if($db2->nf() == 0)
				$type_mur = 0;
			else
			{
				$db2->next_record();
				$type_mur = $db2->f("mur_type");
			}
			// les lieux
			if($type_mur == 0)
			{
				$req2 = "select lieu_tlieu_cod,lieu_nom
					from lieu,lieu_position
					where lpos_pos_cod = " . $db->f("pvue_pos_cod") . "
					and lpos_lieu_cod = lieu_cod ";
				$db2->query($req);
				if($db2->nf() == 0)
				{
					$code_lieu = 0;
					$nom_lieu = '';
				}
				else
				{
					$db2->next_record();
					$code_lieu = $db2->f("lieu_tlieu_cod");
					$nom_lieu = $db2->f("lieu_nom");
				}
			}
			$code_retour .= "case[" . $db->f('pvue_pos_cod') . "]='" . $db->f('pos_x') . "," . $db->f('pos_y') . "," . $db->f('etage_affichage') . "," . $db->f("etage_libelle") . "," . $db->f("pos_type_aff") . "," . $db->f("pos_decor") . "," . $db->f("pos_decor_dessus") . "," . $type_mur . "," . $code_lieu . ";" . $nom_lieu . "'\r\n";
		}
	}
	echo $code_retour;
}
?>


