<?php
define("APPEL", 1);
include "blocks/_header_page_jeu.php";


// scripts JS
$contenu_page .= '<script>
function toutCocher2(formulaire,nom){
    for (i=0;i<formulaire.elements.length;++ i)
    {
        if(formulaire.elements[i].name.substring(0,nom.length) == nom){
            formulaire.elements[i].checked = !formulaire.elements[i].checked;
        }
    }
}
</script>';
$methode      = get_request_var('methode', 'debut');
switch ($methode)
{
    case "debut":
        $contenu_page .= '
			<p>Récupérer une liste de positions autour d\'une case, en excluant les murs non creusables, pour ensuite insérer des composants</p>
			<br> On devra aussi vérifier sur chaque case si il n\'y pas déjà des composants sur cette case
			<form name="position_composant" method="post" action="' . $_SERVER['PHP_SELF'] . '">
			<input type="hidden" name="methode" value="recup_positions">
			<table width="70%">
			<tr>
				<td>Etage </td>
				<td><select name="etage">';
        $html->etage_select(6);

        $contenu_page .= '<option value="' . $result['etage_numero'] . '" ' . $sel . '>' . ($reference ? '' : ' |-- ') . $result['etage_libelle'] . '</option>';
        $contenu_page .= "</select>";

        $contenu_page .= '</td>
			</tr>
			<tr>
				<td>Nombre de composants max par cases</td>
				<td><input type="text" name="max_comp" value="5"></td></tr>
			<tr>
				<td>Variation possible par case du nombre de composants</td>
				<td><input type="text" name="delta" value="1"><em>Correspond à plus ou moins 1 autour du max par case</em></td></tr>
			<tr>
			<tr>
				<td>Pourcentage de repousse</td>
				<td><input type="text" name="pourcentage" value="5"><em>Faire explication</em></td></tr>
			<tr>
				<td>Variation autour de la forme </td>
				<td><input type="text" name="variation" value="8"><em>La forme varie en concept d\'étoile</em></td></td></tr>';
        $req          = 'select gobj_nom,gobj_cod from objet_generique
											where gobj_tobj_cod = 22
											order by gobj_nom';
        $stmt         = $pdo->query($req);
        $contenu_page .= '<tr><td><strong>Composant concerné : </strong></td><td><select name="composant">';
        while ($result = $stmt->fetch())
        {
            $contenu_page .= '<option value="' . $result['gobj_cod'] . '"> ' . $result['gobj_nom'] . '</option>';
        }
        $contenu_page .= '</select><br></td></tr>';
        $contenu_page .= '</table>
			<input type="submit" name="positionnement" value="Récupérer les valeurs" class="test">
			</form>';


        $contenu_page .= '<hr><form name="position_composant2" method="post" action="' . $_SERVER['PHP_SELF'] . '">
			<input type="hidden" name="methode" value="recup_positions2">
			<table width="70%">
			<tr>
				<td>Etage </td>
				<td><select name="etage">';
        $html->etage_select(6);

        $contenu_page .= '</select></td>
												</tr>
												<tr>
													<td>Nombre de composants max par cases</td>
													<td><input type="text" name="max_comp" value="5"></td></tr>
												<tr>
													<td>Variation possible par case du nombre de composants</td>
													<td><input type="text" name="delta" value="1"><em>Correspond à plus ou moins 1 autour du max par case</em></td></tr>
												<tr>
												<tr>
													<td>Pourcentage de repousse</td>
													<td><input type="text" name="pourcentage" value="5"><em>Faire explication</em></td></tr>
												<tr>
													<td>Variation autour de la forme </td>
													<td><input type="text" name="variation" value="8"><em>La forme varie en concept d\'étoile</em></td></td></tr>';
        $gobj         = new objet_generique();
        $allgobj      = $gobj->getBy_gobj_tobj_cod(22);

        $contenu_page .= '<td><strong>Composants concernés : </strong><td><tr>';
        $nbs          = 1;
        foreach ($allgobj as $result)
        {
            $s_cod        = $result['gobj_cod'];
            $contenu_page .= '<TD>
														<INPUT type="checkbox" class="vide" name="composant[' . $result['gobj_cod'] . ']" value="' . $result['gobj_cod'] . '" > ' . $result['gobj_nom'];
            /*$contenu_page .= '<input type="hidden" name="" value="'. $result['gobj_cod'] .'">';*/
            $contenu_page .= '</TD>';

            if ($nbs % 4 == 0)
            {
                $contenu_page .= '</TR><TR>';
            }
            $nbs++;
        }
        $contenu_page .= '</TR>';
        $contenu_page .= '</table>
												<input type="submit" name="positionnement2" value="Récupérer les valeurs" class="test">
												</form>
												<hr>Effacer tous les composants d\'un étage :
												<form name="position_composant2" method="post" action="' . $_SERVER['PHP_SELF'] . '">
													<input type="hidden" name="methode" value="effacer">
													<table width="70%">
													<tr><td>Etage à effacer</td>
												<td><select name="etage">';
        $html->etage_select(6);
        $contenu_page .= '</select></select></td></tr>
												</table>
												<input type="submit" name="effacer" value="Supprimer tous les composants" class="test">
												Attention, pas d\'alerte ensuite !</form>';
        break;

    case
    "recup_positions":
        $contenu_page .= '<table border="1">';
        $req_position =
            "select pos_cod,pos_x as x,pos_y as y,etage_libelle from positions,etage where pos_etage = " . $etage . " and pos_etage = etage_numero order by random() limit 1";
        $stmt         = $pdo->query($req_position);
        $result       = $stmt->fetch();
        $position_x   = $result['x'];
        $position_y   = $result['y'];
        $position     = $result['pos_cod'];
        $etage2       = $result['etage_libelle'];
        $requete_sql  .= '';
        $increment    = '';
        $increment    .= '<tr><td>pos cod : ' . $position . ' / pos X : ' . $position_x . ' / pos Y ' . $position_y . '</td></tr>';
        for ($y = -4; $y < 6; $y++)
        {
            $contenu_page .= '<TR>';
            for ($x = -4; $x < 6; $x++)
            {
                if (($y * $y + $x * $x) < $variation)
                {
                    $req_position = "select pos_cod,pos_x,pos_y from positions where
													 pos_etage = $etage
													 and pos_x = $position_x + $x
													 and pos_y = $position_y - $y";
                    $stmt         = $pdo->query($req_position);
                    if ($stmt->rowCount() != 0)
                    {
                        $result      = $stmt->fetch();
                        $position2   = $result['pos_cod'];
                        $position_x2 = $result['pos_x'];
                        $position_y2 = $result['pos_y'];
                        $increment   .= '<tr><td>pos cod : ' . $position2 . ' / pos X : ' . $position_x2 . ' / pos Y ' . $position_y2;

                        $requete_sql2 = '';
                        $signe        = rand(0, $delta);
                        if (rand(1, 2) == 1)
                        {
                            $signe = $signe * -1;
                        }
                        $quantite = $max_comp + $signe;
                        if ($quantite < 1)
                        {
                            $quantite = 1;
                        }
                        $requete_sql2 .= 'insert into ingredient_position (ingrpos_pos_cod,ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea) values (' . $position2 . ',' . $composant . ',' . $quantite . ',' . $pourcentage . ');<br>';

                        $ig = new ingredient_position();
                        /*
                        $ig->ingrpos_pos_cod = $position2;
                        $ig->ingrpos_gobj_cod = $composant;
                        $ig->ingrpos_max = $quantite;
                        $ig->ingrpos_chance_crea = $pourcentage;
                        $ig->stocke(true);*/


                        /*On regarde si il y a déjà des composants sur une position*/
                        $req_ingredient = "select ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea,gobj_nom from ingredient_position,objet_generique where
																 ingrpos_pos_cod = " . $position2 . "
																 and gobj_cod = ingrpos_gobj_cod";
                        $stmt2          = $pdo->query($req_ingredient);
                        if ($allig = $ig->getByPos($position2))
                        {
                            foreach ($allig as $result2)
                            {
                                $increment .= ' / présence de ' . $result2['gobj_nom'];
                                if ($result2['ingrpos_gobj_cod'] == $composant)
                                {
                                    $requete_sql2 = '';
                                }
                            }
                        }
                        $increment   .= '</td></tr>';
                        $requete_sql .= $requete_sql2;
                        $req_murs    = "select mur_creusable from murs where mur_pos_cod = $position2";

                        $stmt3   = $pdo->query($req_murs);
                        $result3 = $stmt3->fetch();
                        $color   = "#FFFFFF";
                        if ($result3['mur_creusable'] == 'O')
                        {
                            $color = "#696969";
                        }
                        if ($result3['mur_creusable'] == 'N')
                        {
                            $color        = "#000000";
                            $requete_sql2 = '';
                        }
                        $contenu_page .= '<td width="20" height="20" ><div id="pos_' . $result['pos_cod'] . '" style="width:25px;height:25px;background:' . $color . ';"> ' . $image . '</div>
																</td>';

                    }
                } else
                {
                    $contenu_page .= '<td></td>';
                }
            }
            $contenu_page .= '</tr>';
        }
        $contenu_page .= '</tr>
														</table><strong>Rappel : dans le ' . $etage2 . '</strong> ';
        $contenu_page .= '<hr><table>' . $increment . '</table>' . $requete_sql;
        break;

    case "recup_positions2":
        $contenu_page .= '<table border="1">';

        $requete_sql .= '<strong>Liste des requêtes à lancer</strong><br>';
        foreach ($composant as $i => $valeur)
        {
            $req_position =
                "select pos_cod,pos_x as x,pos_y as y,etage_libelle from positions,etage where pos_etage = " . $etage . " and pos_etage = etage_numero order by random() limit 1";
            $stmt         = $pdo->query($req_position);
            $result       = $stmt->fetch();
            $position_x   = $result['x'];
            $position_y   = $result['y'];
            $position     = $result['pos_cod'];
            $etage2       = $result['etage_libelle'];
            $composant2   = $valeur;
            /* $requete_sql .= '<strong>composant : '.$composant2.' / '.$composant[$i] .'</strong><br>'; */
            for ($y = -4; $y < 6; $y++)
            {
                $contenu_page .= '<TR>';
                for ($x = -4; $x < 6; $x++)
                {
                    if (($y * $y + $x * $x) < $variation)
                    {
                        $req_position = "select pos_cod,pos_x,pos_y from positions where
															 pos_etage = $etage
															 and pos_x = $position_x + $x
															 and pos_y = $position_y - $y";
                        $stmt         = $pdo->query($req_position);
                        if ($stmt->rowCount() != 0)
                        {
                            $result      = $stmt->fetch();
                            $position2   = $result['pos_cod'];
                            $position_x2 = $result['pos_x'];
                            $position_y2 = $result['pos_y'];
                            /*$increment .= '<tr><td>pos cod : '. $position2 .' / pos X : '. $position_x2 .' / pos Y '. $position_y2;*/

                            $requete_sql2 = '';
                            $signe        = rand(0, $delta);
                            if (rand(1, 2) == 1)
                            {
                                $signe = $signe * -1;
                            }
                            $quantite = $max_comp + $signe;
                            if ($quantite < 1)
                            {
                                $quantite = 1;
                            }
                            $requete_sql2 .= 'insert into ingredient_position (ingrpos_pos_cod,ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea) values (' . $position2 . ',' . $composant2 . ',' . $quantite . ',' . $pourcentage . ');<br>';

                            /*On regarde si il y a déjà des composants sur une position*/
                            $req_ingredient = "select ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea,gobj_nom from ingredient_position,objet_generique where
																		 ingrpos_pos_cod = " . $position2 . "
																		 and gobj_cod = ingrpos_gobj_cod";
                            $stmt2          = $pdo->query($req_ingredient);
                            if ($stmt2->rowCount() != 0)
                            {
                                while ($result2 = $stmt2->fetch())
                                {
                                    if ($result2['ingrpos_gobj_cod'] == $composant2)
                                    {
                                        $requete_sql2 = '';
                                    }
                                }
                            }
                            $req_murs = "select mur_creusable from murs where mur_pos_cod = " . $position2;

                            $stmt3   = $pdo->query($req_murs);
                            $result3 = $stmt3->fetch();
                            $color   = "#FFFFFF";
                            if ($result3['mur_creusable'] == 'O')
                            {
                                $color = "#696969";
                            }
                            if ($result3['mur_creusable'] == 'N')
                            {
                                $color        = "#000000";
                                $requete_sql2 = '';
                            }
                            $requete_sql .= $requete_sql2; /*On met finalement à jour le résultat après avoir fait tous les checks, à savoir position existante, composants déjà présents et/ou murs présents*/
                        }
                    }

                }
                $contenu_page .= '</tr>';
            }
        }
        $contenu_page .= '</table><strong>Rappel : dans le ' . $etage2 . '</strong> ';
        $contenu_page .= '<hr><table>' . $increment . '</table>' . $requete_sql;
        break;

    case "effacer":
        $req_efface   = "delete from ingredient_position where
															ingrpos_pos_cod in (select pos_cod from positions,etage where pos_etage = $etage and pos_etage = etage_numero)";
        $stmt         = $pdo->query($req_efface);
        $result       = $stmt->fetch();
        $req_position = "select etage_libelle from etage where etage_numero = " . $etage;
        $stmt         = $pdo->query($req_position);
        $result       = $stmt->fetch();
        $etage2       = $result['etage_libelle'];
        $contenu_page .= 'l\'étage ' . $etage2 . ' a été complètement vidé de ses composants';
        break;
}
include "blocks/_footer_page_jeu.php";