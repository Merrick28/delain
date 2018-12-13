<?php 
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
ob_start();
$db2 = new base_delain;
// comment
		//Premier test : vérification si le joueur a bien vu la cachette
		$req = "select persocache_cache_cod
					from cachettes_perso,cachettes,perso_position
					where ppos_perso_cod = $perso_cod
					and ppos_perso_cod = persocache_perso_cod
					and persocache_cache_cod = cache_cod	
					and cache_pos_cod = ppos_pos_cod"; 
		$db->query($req);
		$db->next_record();
		if($db->nf() == 0)
		{
		echo 'Vous continuez à chercher sans grand espoir ! Vos efforts restent vains ...';
		
		}
		else 
		{
		//Deuxième test : vérification de la position		
		// On recherche la position du perso pour voir si correspondance avec une cachette
		$req_info_joueur = "select pos_cod from perso,perso_position,positions
														where perso_cod = $perso_cod
														and ppos_perso_cod = perso_cod
														and ppos_pos_cod = pos_cod";
		$db->query($req_info_joueur);
		$db->next_record();
		$position = $db->f("pos_cod");
			// On récupère les infos générique pour les afficher
					$req_cache = "select cache_nom,cache_desc,cache_image,cache_cod,cache_fonction from cachettes
																	where cache_pos_cod = $position";
					$db->query($req_cache);
					$db->next_record();
					$nom = $db->f("cache_nom");
					$desc = $db->f("cache_desc");
					$image = $db->f("cache_image");
					$cache = $db->f("cache_cod");
					$fonction = $db->f("cache_fonction");
		if($db->nf() == 0)
		{include "tab_haut.php";
		echo 'Vous cherchez à accéder à une page qui n\'existe pas !';
		include "tab_bas.php";
		}
		else
		{
			if (!isset($methode))
			{
				$methode = "debut";
			}
						//début 2nd tableau
			
			switch($methode)
			{
				case "debut":
			
						?>
					<table width="100%" cellspacing="2" cellapdding="2">
					<form name="ramasser" method="post" action="cachette.php">
					<input type="hidden" name="methode" value="suite">
			
				
			

			<?php 
				
				if ($nom != '')
					{
					echo("<tr><td colspan=\"3\" class=\"titre\"><p style=\"text-align:center;\">" . $nom . "</p></td></tr>");
					}
					?>
				<td><hr><i> Vous venez de tomber sur une cachette encore inviolée. Réjouissez vous, ou méfiez vous. 
				Certaines trouvailles ne sont pas toujours bonnes à exploiter ...</i><br>
			
			<?php 							if ($fonction != '')
					{
					$fonction = str_replace('[perso]',$perso_cod,$fonction);
					$req = "select $fonction as resultat";
					$db->query($req);
					$db->next_record();
					echo '<br>' . $db->f("resultat");
					}
				if ($desc != '')
					{
					echo("<br><p\">" . $desc . "</p></td><br>");
					}
					if ($image != '')
					{
					echo("<td><br><p\"><img src=\"../avatars/" . $image . "\"></p></td>");
					}
					?>
					<hr><tr><td colspan="3" class="soustitre"><p class="soustitre">Objets cachés</p></td></tr>
			
			
			<?php 		
					//******************************************
					//            O B J E T S                 **
					//******************************************
					// On recherche les objets dans la cachette
					$req = "select obj_nom_generique,tobj_libelle,obj_cod,obj_nom,objcache_cod_cache_cod,objcache_obj_cod 
															from objet_generique,type_objet,objets,cachettes_objets,cachettes
															where cache_pos_cod = $position
															and cache_cod = objcache_cod_cache_cod
															and obj_cod = objcache_obj_cod
															and obj_gobj_cod = gobj_cod
															and gobj_tobj_cod = tobj_cod
															order by tobj_libelle";
					$db->query($req);
				
				
					// on affiche la ligne d'en tête objets
				?> 
					<tr>
					<td class="soustitre2"><p><strong>Nom</strong></p></td>
					<td class="soustitre2"><p><strong>Type objet</strong></p></td>
					<td class="soustitre2"></td>
					</tr>
			
					<?php 
					if ($db->nf() != 0)
					{
					
						$nb_objets = 1;
						// on boucle sur les objets dans la cachette
						while ($db->next_record())
						{
							echo("<tr>");
							$objet = $db->f("obj_cod");
							echo "<td class=\"soustitre2\"><p><strong>" . $db->f("obj_nom_generique"). "</strong></p></td>";
							echo "<td><p>" . $db->f("tobj_libelle") . "</p></td>";
							echo "<td><p><input type=\"checkbox\" class=\"vide\" name=\"objet[" . $db->f("obj_cod") . "]\" value=\"0\"></p></td>";
							echo "</tr>";
						}
					}
					else
					{
						echo "Aucun objet dans la cachette !";
					}
					?>
					</table>
					<center><input type="submit" class="test" value="Récupérer les objets cochés !"></center>
					</form>
					<?php 
					break;
			
			 case "suite":
					$req = "select perso_pa from perso where perso_cod = $perso_cod ";
					$db->query($req);
					$db->next_record();
					$pa = $db->f("perso_pa");
					$erreur = 0;
					$total = 0;
					if ($objet)
					{
						foreach($objet as $key=>$val)
						{
							$total = $total + 1;
						}
					}
					if ($pa < $total)
					{
						echo "<p>Vous n'avez pas assez de PA pour récupérer tous ces objets ! Certains devront rester dans la cachette.";
						$erreur = 1;
					}
					if ($erreur == 0)
					{
						if ($objet)
						{
							foreach($objet as $key=>$val)
							{
								$req_ramasser = "select ramasse_objet_cachette($perso_cod,$key) as resultat";
								$db->query($req_ramasser);
								$db->next_record();
								echo $db->f("resultat");
							}
						}			
					}			
					break;
			}
		}
	}	
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
