<?php

//
//Contenu de la div de droite
//
$param = new parametres();
$db2 = new base_delain;
$contenu_page3 = '';
$erreur = 0;
//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$tab_position = $db->get_pos($perso_cod);
$cod_etage = $tab_position['etage'];
$req_or = "select perso_po, perso_sex, perso_nb_mort, perso_nom from perso where perso_cod = $perso_cod ";
$db->query($req_or);
$db->next_record();
$pos_temple = $tab_position['pos_cod'];
$etage = abs($cod_etage) + 1;
$nom = $db->f("perso_nom");
$sexe = $db->f("perso_sex");
$or = $db->f("perso_po");
$nb_mort = $db->f("perso_nb_mort");
$prix = ($etage * $param->getparm(30)) + ($nb_mort * $param->getparm(31));
$req_comp = "select count(perso_cod) as nombre from perso,perso_position 
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)
										and perso_quete = 'quete_dispensaire.php'
										and perso_cod = ppos_perso_cod";
$db->query($req_comp);
$db->next_record();
if ($db->f("nombre") == 0)
{
    $erreur = 1;
    $contenu_page3 .= 'Vous n’avez pas accès à cette page !';
}
if (!isset($methode))
{
    $methode = 'debut';
}
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            $contenu_page3 .= 'Ses longs doigts blanchâtres rejouent inlassablement les mêmes gestes, ceux qu’il avait appris lors de ses études de médecine, répétés toute sa vie durant dans ce dispensaire pour soigner les corps meurtris. 
			<br>Mais il n’y a plus guère que le vent qui se  laisse  saisir par sa vieille carcasse éthérée. Et pourtant, il demeure fidèle à  son poste, comme en attente d’une dernière âme à sauver...
			<br>Dans un râle, il se tourne vers vous, et vous apostrophe :
			<br><i>« Souhaitez vous bénéficier du service de rapatriement d’âme ? »</i>
													<br><a href="' . $PHP_SELF . '?methode=oui"><strong>Oui</a></strong>
													<br><a href="' . $PHP_SELF . '?methode=non"><strong>Non</a></strong>';
            break;
        case "oui":
            $req = 'select * from choix_lieu_vus(' . $perso_cod . ',2)';
            $db->query($req);
            $contenu_page3 .= '<p class="titre"><i>Ainsi, vous avez besoin de mon aide :</i></p>
			<table>';
            while ($db->next_record())
            {
                $req = 'select lieu_nom,pos_x,pos_y,etage_libelle,etage_numero,pos_etage
					from lieu,lieu_position,positions,etage
					where pos_cod = ' . $db->f(0) . '
					and lpos_pos_cod = pos_cod
					and lpos_lieu_cod = lieu_cod 
					and etage_numero = pos_etage';
                $db2->query($req);
                $db2->next_record();
                if ($db2->f("pos_etage") == $cod_etage)
                {
                    $contenu_page3 .= '<tr><td class="soustitre2">' . $db2->f("lieu_nom") . '</td>
						<td>' . $db2->f("pos_x") . ', ' . $db2->f("pos_y") . ', ' . $db2->f("etage_libelle") . '</td>
						<td class="soustitre2">';
                    $contenu_page3 .= '<a href="' . $PHP_SELF . '?methode=dispensaire&pos=' . $db->f(0) . '">Faire de ce dispensaire celui qui recueillera votre âme ? (' . $prix . ' brouzoufs)</a>';
                    $contenu_page3 .= '</td></tr>';
                }
            }
            $contenu_page3 .= '</table>';
            break;
        case "non":
            $contenu_page3 .= '<i>Des âmes, je veux des âmes...</i>
				<br>Et son corps fantomatique s’éloigne doucement de vous...';
            break;
        case "dispensaire":
            $req = 'select pos_cod from positions where pos_cod in (select* from choix_lieu_vus(' . $perso_cod . ',2)) and pos_etage = ' . $cod_etage;
            $db2->query($req);
            if ($or < $prix)
            {
                $contenu_page3 .= "<p>Désolé $nom, mais il semble que vous n’ayez pas assez de brouzoufs pour vous payer ce service.</p>";
            }
            if ($db->nf() == 0)
            {
                $contenu_page3 .= 'Vous ne connaissez pas l’existence de ce dispensaire. D’ailleurs, existe-t-il ?';
            }
            else
            {
                $db2->next_record();
                $position = $db2->f("pos_cod");
                $req_or = "update perso set perso_po = perso_po - $prix where perso_cod = $perso_cod";
                $db->query($req_or);
                $req_temple1 = "delete from perso_temple where ptemple_perso_cod = $perso_cod ";
                $db->query($req_temple1);
                $req_temple2 = "insert into perso_temple(ptemple_perso_cod,ptemple_pos_cod,ptemple_nombre) values ";
                $req_temple2 = $req_temple2 . "($perso_cod,$position,0)";
                $db->query($req_temple2);
                $contenu_page3 .= '<p>D’un geste précis mille fois recommencé, l’ombre d’une écriture vive et précise note votre nom et votre race sur un grand livre prévu à cet effet. <br>Puis sans plus attendre, cherche une autre personne à questionner...';
            }
            break;
    }
}
echo $contenu_page3;
