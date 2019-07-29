<div id="liste_objets" class="liste_objets">

	<?php 		/* Les includes */
		include "verif_connexion.php";
		include "../includes/fonctions.php";
		
		
		$db_objets = new base_delain;
		
		/*Valeur de dex et de force */
		// on prend les valeurs de forece et dex du perso pour la suite
		
		$req = "select perso_pa, perso_for,perso_dex from perso where perso_cod = ".$perso_cod." ";
		$db_objets->query($req);
		$db_objets->next_record();
		$force = $db_objets->f("perso_for");
		$dex = $db_objets->f("perso_dex");
		$perso_pa = $db_objets->f("perso_pa");
		
		
		/* runes */
		
		$req_objets = "select obj_nom, 
							obj_nom_generique,
							obj_poids,
							obj_description,
							gobj_image,
							gobj_deposable,
							tobj_cod, 
							count(obj_nom_generique) as nb_type_objet,
							sum(obj_poids) as obj_poids_total ";
		$req_objets = $req_objets . "from perso_objets, objets, objet_generique, type_objet ";
		$req_objets = $req_objets . "where perobj_perso_cod = $perso_cod ";
		$req_objets = $req_objets . "and tobj_cod = 5 ";
		$req_objets = $req_objets . "and (gobj_visible is null or gobj_visible != 'N') ";
		$req_objets = $req_objets . "and perobj_obj_cod = obj_cod ";
		$req_objets = $req_objets . "and obj_gobj_cod = gobj_cod ";
		$req_objets = $req_objets . "and gobj_tobj_cod = tobj_cod ";
		$req_objets = $req_objets . "group by obj_nom, obj_nom_generique, obj_poids, obj_description, gobj_image, gobj_deposable, tobj_cod ";
		$req_objets = $req_objets . "order by  obj_nom_generique ";
		log_debug($req_objets);
		//echo $req_objets;
		$db_objets->query($req_objets);
		$nb_objets = $db_objets->nf();
		/*
		echo $nb_objets;
		echo "<br/>";
		*/
	
	
		if ($nb_objets > 0)
		{
			echo "<table  border=\"2\" cellspacing=\"0\" cellpadding=\"3\" bordercolor=\"black\" class=\"sortable\">";
				echo "<thead>";
					echo "<tr>";
						echo "<th class=\"pointer\">Nom</th>";
						echo "<th class=\"pointer\">Nombre</th>";
						echo "<th class=\"pointer\">Poids</th>";
						echo "<th class=\"sorttable_nosort\">&nbsp;</th>";
						echo "<th class=\"sorttable_nosort\">&nbsp;</th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				$i = 0;
				while($db_objets->next_record())
				{
					$Recup_image = $db_objets->f("gobj_image");
					$image = '../images/'.$Recup_image.'';
					$desc = htmlspecialchars($db_objets->f("obj_description"));
					$poids = $db_objets->f("obj_poids");
					$obj_poids_total = $db_objets->f("obj_poids_total");
					$id_type = $db_objets->f("tobj_cod");
					
					$nom_objet_generique = $db_objets->f("obj_nom_generique");
					$nom_objet = $db_objets->f("obj_nom");
					$nb_type_objet = $db_objets->f("nb_type_objet");
					
					$id_object = 0;
					if( $nb_type_objet == 1 )
					{
						$db_idObjets = new base_delain;
						// requete pour recup  l'id de l'objes..	
						$req_all_objets = "select perobj_obj_cod ";
						$req_all_objets = $req_all_objets . "from perso_objets, objets, objet_generique, type_objet ";
						$req_all_objets = $req_all_objets . "where perobj_perso_cod = ".$perso_cod." ";
						$req_all_objets = $req_all_objets . "and tobj_cod = 5 ";
						$req_all_objets = $req_all_objets . "and perobj_obj_cod = obj_cod ";
						$req_all_objets = $req_all_objets . "and obj_gobj_cod = gobj_cod ";
						$req_all_objets = $req_all_objets . "and gobj_tobj_cod = tobj_cod ";
						//$req_all_objets = $req_all_objets . "and tobj_cod = ".$id_type." ";
						$req_all_objets = $req_all_objets . "and obj_nom = '".addslashes($nom_objet)."' ";
						$req_all_objets = $req_all_objets . "and obj_nom_generique = '".addslashes($nom_objet_generique)."' ";
						//echo $req_all_objets;
						
						$db_idObjets->query($req_all_objets);
						$db_idObjets->next_record();
						$id_object = $db_idObjets->f("perobj_obj_cod");
						
					}
					
					$classLigneTab = "ligneTab-".(($i%2)+1);
					$i++;
					
					echo "<tr class=\"".$classLigneTab."\">";
						echo "<td><div class=\"nom_objet\">".$nom_objet."</div> <img class=\"img_objet\" title=\"".$nom_objet."\" src=\"".G_IMAGES.$Recup_image."\"> </td>";
						echo "<td>".$nb_type_objet."</td>";
						echo "<td>".$obj_poids_total."</td>";
						//echo "<td>".substr($desc, 0, 12)."[...]</td>";
						echo "<td class=\"val_nb\">";
							echo "<form name=\"trait_objet".$i."\" method=\"post\" action=\"#fiche_item\"> ";
								echo "<input name=\"idObjet\" type=\"hidden\" value=\"".$id_object."\" />";
								echo "<input name=\"nomObjet\" type=\"hidden\" value=\"".$nom_objet."\"/>";
								echo "<input name=\"nomObjetGenerique\" type=\"hidden\" value=\"".$nom_objet_generique."\"/>";
								echo "<input name=\"typeObjet\" type=\"hidden\" value=\"".$id_type."\"/>";
								echo "<input name=\"methode_divers\" type=\"hidden\" value=\"abandonner\"/>";
								echo "<input type=\"text\" name=\"nbObjet\" class=\"nb\" maxlength=\"2\" />";
							echo "</form>";
						echo "</td>";
						echo "<td>";
							echo "<a href=\"javascript:document.trait_objet".$i.".submit();\" ><img class=\"img_objet\" src=\"images/abandonner50.png\" width=\"30px\" height=\"30px\" title=\"Abandonner\" /></a>";
						echo "</td>"; 
						
					echo "</tr>";
				}
				echo "</tbody>";
	
				echo "<tfoot>";
				echo "</tfoot>";
			echo "</table>";
		}
		
	?>
</div>
