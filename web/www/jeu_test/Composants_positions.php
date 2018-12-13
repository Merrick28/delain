<?php 
$db2 = new base_delain;
$db3 = new base_delain;
define("APPEL",1);
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';


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
	if(!isset($methode))
	{
	$methode = "debut";
	}
	switch($methode)
	{
		case "debut":
			$contenu_page .= '
			<p>Récupérer une liste de positions autour d\'une case, en excluant les murs non creusables, pour ensuite insérer des composants</p>
			<br> On devra aussi vérifier sur chaque case si il n\'y pas déjà des composants sur cette case
			<form name="position_composant" method="post" action="'. $PHP_SELF .'">
			<input type="hidden" name="methode" value="recup_positions">
			<table width="70%">
			<tr>
				<td>Etage </td>
				<td><select name="etage">';
			$req = "select etage_numero,etage_libelle,etage_reference from etage order by etage_reference desc, etage_numero asc ";
			$db->query($req);
			while($db->next_record())
			{
				$sel = "";
				if($db->f("etage_numero") == 6)
				{
					$sel = "selected";
				}
				$reference = ($db->f("etage_numero") == $db->f("etage_reference"));
                $contenu_page .= '<option value="' . $db->f("etage_numero") .'" '. $sel .'>'. ($reference?'':' |-- ').$db->f("etage_libelle") . '</option>';
			}
			$contenu_page .= '</td>
			</tr>
			<tr>
				<td>Nombre de composants max par cases</td>
				<td><input type="text" name="max_comp" value="5"></td></tr>
			<tr>
				<td>Variation possible par case du nombre de composants</td>
				<td><input type="text" name="delta" value="1"><i>Correspond à plus ou moins 1 autour du max par case</i></td></tr>
			<tr>
			<tr>
				<td>Pourcentage de repousse</td>
				<td><input type="text" name="pourcentage" value="5"><i>Faire explication</i></td></tr>
			<tr>
				<td>Variation autour de la forme </td>
				<td><input type="text" name="variation" value="8"><i>La forme varie en concept d\'étoile</i></td></td></tr>';
			$req = 'select gobj_nom,gobj_cod from objet_generique
											where gobj_tobj_cod = 22
											order by gobj_nom';
			$db->query($req);
				$contenu_page .= '<tr><td><strong>Composant concerné : </strong></td><td><select name="composant">';
				while($db->next_record())
				{
					$contenu_page .= '<option value="'. $db->f("gobj_cod") .'"> '. $db->f("gobj_nom") .'</option>';
				}
				$contenu_page .= '</select><br></td></tr>';
			$contenu_page .= '</table>
			<input type="submit" name="positionnement" value="Récupérer les valeurs" class="test">
			</form>';



			$contenu_page .= '<hr><form name="position_composant2" method="post" action="'. $PHP_SELF .'">
			<input type="hidden" name="methode" value="recup_positions2">
			<table width="70%">
			<tr>
				<td>Etage </td>
				<td><select name="etage">';
			$req = "select etage_numero,etage_libelle,etage_reference from etage order by etage_reference desc, etage_numero asc ";
			$db->query($req);
			while($db->next_record())
			{
				$sel = "";
				if($db->f("etage_numero") == 6)
				{
					$sel = "selected";
				}
				$reference = ($db->f("etage_numero") == $db->f("etage_reference"));
                $contenu_page .= '<option value="' . $db->f("etage_numero") .'" '. $sel .'>'. ($reference?'':' |-- ').$db->f("etage_libelle") . '</option>';
			}
			$contenu_page .= '</td>
												</tr>
												<tr>
													<td>Nombre de composants max par cases</td>
													<td><input type="text" name="max_comp" value="5"></td></tr>
												<tr>
													<td>Variation possible par case du nombre de composants</td>
													<td><input type="text" name="delta" value="1"><i>Correspond à plus ou moins 1 autour du max par case</i></td></tr>
												<tr>
												<tr>
													<td>Pourcentage de repousse</td>
													<td><input type="text" name="pourcentage" value="5"><i>Faire explication</i></td></tr>
												<tr>
													<td>Variation autour de la forme </td>
													<td><input type="text" name="variation" value="8"><i>La forme varie en concept d\'étoile</i></td></td></tr>';
			$req = 'select gobj_nom,gobj_cod from objet_generique
											where gobj_tobj_cod = 22
											order by gobj_nom';
			$db->query($req);
				$contenu_page .= '<td><strong>Composants concernés : </strong><td><tr>';
				$nbs = 1;
				while($db->next_record())
				{
					$s_cod = $db->f("gobj_cod");
					$contenu_page .= '<TD>
														<INPUT type="checkbox" class="vide" name="composant['. $db->f("gobj_cod") .']" value="'. $db->f("gobj_cod") .'" > '. $db->f("gobj_nom");
					/*$contenu_page .= '<input type="hidden" name="" value="'. $db->f("gobj_cod") .'">';*/
					$contenu_page .= '</TD>';

					if($nbs%4 == 0)
					{
						$contenu_page .= '</TR><TR>';
					}
					$nbs++;
  			}
			$contenu_page .= '</TR>';
			/*$contenu_page .= '<td><a style="text-align:center;" href="javascript:toutCocher2(document.position_composant2,\'composant\');">cocher<br>décocher<br>inverser</a></td>';*/
			$contenu_page .= '</table>
												<input type="submit" name="positionnement2" value="Récupérer les valeurs" class="test">
												</form>
												<hr>Effacer tous les composants d\'un étage :
												<form name="position_composant2" method="post" action="'. $PHP_SELF .'">
													<input type="hidden" name="methode" value="effacer">
													<table width="70%">
													<tr><td>Etage à effacer</td>
												<td><select name="etage">';
											$req = "select etage_numero,etage_libelle,etage_reference from etage order by etage_reference desc, etage_numero asc ";
											$db->query($req);
											while($db->next_record())
											{
												$sel = "";
												if($db->f("etage_numero") == 6)
												{
													$sel = "selected";
												}
												$reference = ($db->f("etage_numero") == $db->f("etage_reference"));
												$contenu_page .= '<option value="' . $db->f("etage_numero") .'" '. $sel .'>'. ($reference?'':' |-- ').$db->f("etage_libelle") . '</option>';
											}
											$contenu_page .= '</td></tr>
												</table>
												<input type="submit" name="effacer" value="Supprimer tous les composants" class="test">
												Attention, pas d\'alerte ensuite !</form>';
		break;

		case "recup_positions":
				$contenu_page .= '<table border="1">';
				$req_position = "select pos_cod,pos_x as x,pos_y as y,etage_libelle from positions,etage where pos_etage = ". $etage ." and pos_etage = etage_numero order by random() limit 1";
				$db->query($req_position);
				$db->next_record();
				$position_x = $db->f("x");
				$position_y = $db->f("y");
				$position = $db->f("pos_cod");
				$etage2 = $db->f("etage_libelle");
				$requete_sql .= '';
				$increment = '';
				$increment .= '<tr><td>pos cod : '. $position .' / pos X : '. $position_x .' / pos Y '. $position_y .'</td></tr>';
				for ($y=-4; $y < 6; $y++)
				{
					$contenu_page .= '<TR>';
					for ($x=-4; $x<6; $x++)
					{
						if(($y*$y + $x*$x) < $variation)
						{
							$req_position = "select pos_cod,pos_x,pos_y from positions where
													 pos_etage = $etage
													 and pos_x = $position_x + $x
													 and pos_y = $position_y - $y";
							$db->query($req_position);
							if ($db->nf() != 0)
							{
								$db->next_record();
								$position2 = $db->f("pos_cod");
								$position_x2 = $db->f("pos_x");
								$position_y2 = $db->f("pos_y");
								$increment .= '<tr><td>pos cod : '. $position2 .' / pos X : '. $position_x2 .' / pos Y '. $position_y2;
								$db2 = new base_delain;
								$requete_sql2 = '';
								$signe = rand(0,$delta);
								if (rand(1,2) == 1)
								{
									$signe = $signe * -1;
								}
								$quantite = $max_comp + $signe;
								if ($quantite < 1)
								{
									$quantite = 1;
								}
								$requete_sql2 .= 'insert into ingredient_position (ingrpos_pos_cod,ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea) values ('.$position2.','.$composant.','.$quantite.','.$pourcentage.');<br>';

								/*On regarde si il y a déjà des composants sur une position*/
								$req_ingredient = "select ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea,gobj_nom from ingredient_position,objet_generique where
																 ingrpos_pos_cod = ". $position2 ."
																 and gobj_cod = ingrpos_gobj_cod";
								$db2->query($req_ingredient);
								if ($db2->nf() != 0)
								{
									while ($db2->next_record())
										{
											$increment .= ' / présence de '. $db2->f("gobj_nom");
											if ($db2->f("ingrpos_gobj_cod") == $composant)
											{
												$requete_sql2 = '';
											}
										}
								}
								$increment .= '</td></tr>';
								$requete_sql .= $requete_sql2 ;
								$req_murs = "select mur_creusable from murs where mur_pos_cod = $position2";
								$db3 = new base_delain;
								$db3->query($req_murs);
								$db3->next_record();
								$color = "#FFFFFF";
								if($db3->f("mur_creusable") == 'O'){
									$color = "#696969";
								}
								if($db3->f("mur_creusable") == 'N'){
									$color = "#000000";
									$requete_sql2 = '';
								}
								$contenu_page .= '<td width="20" height="20" ><div id="pos_'. $db->f("pos_cod") .'" style="width:25px;height:25px;background:'. $color .';"> '. $image .'</div>
																</td>';

							}
						}
						else
							{
								$contenu_page .= '<td></td>';
							}
						}
						$contenu_page .= '</tr>';
					}
					$contenu_page .= '</tr>
														</table><strong>Rappel : dans le '.$etage2.'</strong> ';
					$contenu_page .= '<hr><table>'. $increment .'</table>'.$requete_sql;
					break;

		case "recup_positions2":
				$contenu_page .= '<table border="1">';

				$requete_sql .= '<strong>Liste des requêtes à lancer</strong><br>';
			  foreach ($composant as $i => $valeur)
			  {
					$req_position = "select pos_cod,pos_x as x,pos_y as y,etage_libelle from positions,etage where pos_etage = ". $etage ." and pos_etage = etage_numero order by random() limit 1";
					$db->query($req_position);
					$db->next_record();
					$position_x = $db->f("x");
					$position_y = $db->f("y");
					$position = $db->f("pos_cod");
					$etage2 = $db->f("etage_libelle");
			  	$composant2 = $valeur;
			  	/* $requete_sql .= '<strong>composant : '.$composant2.' / '.$composant[$i] .'</strong><br>'; */
						for ($y=-4; $y < 6; $y++)
						{
							$contenu_page .= '<TR>';
							for ($x=-4; $x<6; $x++)
							{
								if(($y*$y + $x*$x) < $variation)
								{
									$req_position = "select pos_cod,pos_x,pos_y from positions where
															 pos_etage = $etage
															 and pos_x = $position_x + $x
															 and pos_y = $position_y - $y";
									$db->query($req_position);
									if ($db->nf() != 0)
									{
										$db->next_record();
										$position2 = $db->f("pos_cod");
										$position_x2 = $db->f("pos_x");
										$position_y2 = $db->f("pos_y");
										/*$increment .= '<tr><td>pos cod : '. $position2 .' / pos X : '. $position_x2 .' / pos Y '. $position_y2;*/
										$db2 = new base_delain;
										$requete_sql2 = '';
										$signe = rand(0,$delta);
										if (rand(1,2) == 1)
										{
											$signe = $signe * -1;
										}
										$quantite = $max_comp + $signe;
										if ($quantite < 1)
										{
											$quantite = 1;
										}
										$requete_sql2 .= 'insert into ingredient_position (ingrpos_pos_cod,ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea) values ('.$position2.','.$composant2.','.$quantite.','.$pourcentage.');<br>';

										/*On regarde si il y a déjà des composants sur une position*/
										$req_ingredient = "select ingrpos_gobj_cod,ingrpos_max,ingrpos_chance_crea,gobj_nom from ingredient_position,objet_generique where
																		 ingrpos_pos_cod = ". $position2 ."
																		 and gobj_cod = ingrpos_gobj_cod";
										$db2->query($req_ingredient);
										if ($db2->nf() != 0)
										{
											while ($db2->next_record())
												{
													if ($db2->f("ingrpos_gobj_cod") == $composant2)
													{
														$requete_sql2 = '';
													}
												}
										}
										$req_murs = "select mur_creusable from murs where mur_pos_cod = ". $position2;
										$db3 = new base_delain;
										$db3->query($req_murs);
										$db3->next_record();
										$color = "#FFFFFF";
										if($db3->f("mur_creusable") == 'O'){
											$color = "#696969";
										}
										if($db3->f("mur_creusable") == 'N'){
											$color = "#000000";
											$requete_sql2 = '';
										}
										$requete_sql .= $requete_sql2 ; /*On met finalement à jour le résultat après avoir fait tous les checks, à savoir position existante, composants déjà présents et/ou murs présents*/
									}
								}

								}
								$contenu_page .= '</tr>';
							}
				}
							$contenu_page .= '</table><strong>Rappel : dans le '.$etage2.'</strong> ';
							$contenu_page .= '<hr><table>'. $increment .'</table>'.$requete_sql;
		break;

		case "effacer":
				$req_efface = "delete from ingredient_position where
															ingrpos_pos_cod in (select pos_cod from positions,etage where pos_etage = $etage and pos_etage = etage_numero)";
				$db->query($req_efface);
				$db->next_record();
				$req_position = "select etage_libelle from etage where etage_numero = ". $etage;
				$db->query($req_position);
				$db->next_record();
				$etage2 = $db->f("etage_libelle");
				$contenu_page .= 'l\'étage '. $etage2 .' a été complètement vidé de ses composants';
		break;
	}
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");