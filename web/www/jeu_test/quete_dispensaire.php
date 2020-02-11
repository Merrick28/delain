<?php

//
//Contenu de la div de droite
//
$param = new parametres();

$contenu_page3 = '';
$erreur = 0;
//On vérifie qu'il s'agit bien d'un perso permettant cette quête sur cette case
$tab_position = $db->get_pos($perso_cod);
$cod_etage = $tab_position['etage'];
$req_or = "select perso_po, perso_sex, perso_nb_mort, perso_nom from perso where perso_cod = $perso_cod ";
$stmt = $pdo->query($req_or);
$result = $stmt->fetch();
$pos_temple = $tab_position['pos_cod'];
$etage = abs($cod_etage) + 1;
$nom = $result['perso_nom'];
$sexe = $result['perso_sex'];
$or = $result['perso_po'];
$nb_mort = $result['perso_nb_mort'];
$prix = ($etage * $param->getparm(30)) + ($nb_mort * $param->getparm(31));
$req_comp = "select count(perso_cod) as nombre from perso,perso_position 
										where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)
										and perso_quete = 'quete_dispensaire.php'
										and perso_cod = ppos_perso_cod";
$stmt = $pdo->query($req_comp);
$result = $stmt->fetch();
if ($result['nombre'] == 0)
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
			<br><em>« Souhaitez vous bénéficier du service de rapatriement d’âme ? »</em>
													<br><a href="' . $PHP_SELF . '?methode=oui"><strong>Oui</a></strong>
													<br><a href="' . $PHP_SELF . '?methode=non"><strong>Non</a></strong>';
            break;
        case "oui":
            $req = 'select * from choix_lieu_vus(' . $perso_cod . ',2)';
            $stmt = $pdo->query($req);
            $contenu_page3 .= '<p class="titre"><em>Ainsi, vous avez besoin de mon aide :</em></p>
			<table>';
            while ($result = $stmt->fetch())
            {
                $req = 'select lieu_nom,pos_x,pos_y,etage_libelle,etage_numero,pos_etage
					from lieu,lieu_position,positions,etage
					where pos_cod = ' . $db->f(0) . '
					and lpos_pos_cod = pos_cod
					and lpos_lieu_cod = lieu_cod 
					and etage_numero = pos_etage';
                $stmt2 = $pdo->query($req);
                $result2 = $stmt2->fetch();
                if ($result2['pos_etage'] == $cod_etage)
                {
                    $contenu_page3 .= '<tr><td class="soustitre2">' . $result2['lieu_nom'] . '</td>
						<td>' . $result2['pos_x'] . ', ' . $result2['pos_y'] . ', ' . $result2['etage_libelle'] . '</td>
						<td class="soustitre2">';
                    $contenu_page3 .= '<a href="' . $PHP_SELF . '?methode=dispensaire&pos=' . $db->f(0) . '">Faire de ce dispensaire celui qui recueillera votre âme ? (' . $prix . ' brouzoufs)</a>';
                    $contenu_page3 .= '</td></tr>';
                }
            }
            $contenu_page3 .= '</table>';
            break;
        case "non":
            $contenu_page3 .= '<em>Des âmes, je veux des âmes...</em>
				<br>Et son corps fantomatique s’éloigne doucement de vous...';
            break;
        case "dispensaire":
            $req = 'select pos_cod from positions where pos_cod in (select* from choix_lieu_vus(' . $perso_cod . ',2)) and pos_etage = ' . $cod_etage;
            $stmt2 = $pdo->query($req);
            if ($or < $prix)
            {
                $contenu_page3 .= "<p>Désolé $nom, mais il semble que vous n’ayez pas assez de brouzoufs pour vous payer ce service.</p>";
            }
            if ($stmt->rowCount() == 0)
            {
                $contenu_page3 .= 'Vous ne connaissez pas l’existence de ce dispensaire. D’ailleurs, existe-t-il ?';
            }
            else
            {
                $result2 = $stmt2->fetch();
                $position = $result2['pos_cod'];
                $req_or = "update perso set perso_po = perso_po - $prix where perso_cod = $perso_cod";
                $stmt = $pdo->query($req_or);
                $req_temple1 = "delete from perso_temple where ptemple_perso_cod = $perso_cod ";
                $stmt = $pdo->query($req_temple1);
                $req_temple2 = "insert into perso_temple(ptemple_perso_cod,ptemple_pos_cod,ptemple_nombre) values ";
                $req_temple2 = $req_temple2 . "($perso_cod,$position,0)";
                $stmt = $pdo->query($req_temple2);
                $contenu_page3 .= '<p>D’un geste précis mille fois recommencé, l’ombre d’une écriture vive et précise note votre nom et votre race sur un grand livre prévu à cet effet. <br>Puis sans plus attendre, cherche une autre personne à questionner...';
            }
            break;
    }
}
echo $contenu_page3;
