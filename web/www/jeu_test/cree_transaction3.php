<?php 
//include "../connexion.php";
include "verif_connexion.php";
include "../includes/fonctions.php";
$param = new parametres();
if (!isset($methode))
{
	$methode = "debut";
}
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php $identifie['O'] = "(identifié)";
$identifie['N'] = "(non identifié)";
include "tab_haut.php";
/************************/
/* recherche des objets */
/************************/
switch($methode)
	{
		case "debut":
			echo "<p class=\"titre\">Choix du destinataire </p>";
			echo "<form name=\"tran\" method=\"post\" action=\"cree_transaction3.php\">";
			echo "<p>Choisissez le joueur à qui vous voulez vendre des objets : ";
			echo "<input type=\"hidden\" name=\"methode\" value=\"e1\">";
			$req_pos = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod ";
			$db->query($req_pos);
			$db->next_record();
			$pos_actuelle = $db->f("ppos_pos_cod");
			
			$req_vue = "select lower(perso_cod) as minusc,perso_cod,perso_nom from perso, perso_position where ppos_pos_cod = $pos_actuelle and ppos_perso_cod = perso_cod and perso_cod != $perso_cod  and perso_type_perso in (1,2,3) and perso_actif = 'O' order by perso_type_perso,perso_nom,minusc";
			$db->query($req_vue);
			$nb_vue = $db->nf();
			
			if ($nb_vue == 0)
			{
				?>
				<p>Aucun joueur en vue
				<?php 
			}
			else
			{
				?>
				<select name="perso">
				<?php 
				while($db->next_record())
				{
					echo "<option value=\"" . $db->f("perso_cod") . "\">" . $db->f("perso_nom") . "</option>";
				}	
				?>
				</select>
				<?php 
				echo "<p><center><input type=\"submit\" class=\"test\" value=\"Passer à la suite\"></center>";
			}			
			echo "</form>";
			break;
		case "e1";
			echo "<p class=\"titre\">Sélection des objets à vendre</p>";
			echo "<form name=\"tran\" method=\"post\" action=\"cree_transaction3.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"e3\">";
			echo "<input type=\"hidden\" name=\"perso\" value=\"$perso_cible\">";
			$req_objet = "select obj_etat,gobj_tobj_cod,obj_cod,obj_nom,obj_nom_generique,tobj_libelle,perobj_identifie
														from objet_generique,type_objet,perso_objets,objets
														where perobj_perso_cod = $perso_cod
														and perobj_equipe = 'N'
														and perobj_obj_cod = obj_cod
														and obj_gobj_cod = gobj_cod
														and gobj_tobj_cod = tobj_cod
														and obj_deposable != 'N'
														and not exists (select 1 from transaction where tran_obj_cod = obj_cod) order by gobj_tobj_cod,obj_nom";
			$db->query($req_objet);
			$nb_objets = $db->nf();
			if ($nb_objets == 0)
			{
				echo("<p>Vous n'avez aucun objet à vendre actuellement</p>");
			}
			else
			{
				$etat = '';
				echo "<p style=\"text-align:center;\">Cliquer sur les objets que vous souhaitez vendre, et indiquez son prix de vente </p>";
				echo("<center><table>");
				while($db->next_record())
				{

					if ($db->f("perobj_identifie") == 'O')
					{
						$nom_objet = $db->f("obj_nom");
					}
					else
					{
						$nom_objet = $db->f("obj_nom_generique");
					}
					$si_identifie = $db->f("perobj_identifie");
					echo "<tr>";
					echo "<td class=\"soustitre2\"><p>$nom_objet $identifie[$si_identifie]";
					if (($db->f("gobj_tobj_cod") == 1) || ($db->f("obj_gobj_cod") == 2))
					{
						echo "  - " . get_etat($db->f("obj_etat"));
					}
					echo "</td>";
					echo "<td><p><input type=\"checkbox\" class=\"vide\" name=\"obj[" . $db->f("obj_cod") . "]\" value=\"0\"></td>";
					echo "<td><p><input type=\"text\" name=\"prix[" . $db->f("obj_cod") . "]\" value=\"0\"></td>";	
					echo "</tr>";
				}
				echo "<tr><td colspan=\"2\"><center><input class=\"test\" type=\"submit\" value=\"Passer à la suite\"></center></td></tr>";
				echo "</table></center>";
			}
			break;
		case "e2";
			echo "<p class=\"titre\">Fixer les prix</p>";
			if ($obj)
			{
				echo "<p style=\"text-align:center\">Fixez un prix pour chacun des objets : ";
				echo "<form name=\"tran\" method=\"post\" action=\"cree_transaction3.php\">";
				echo "<input type=\"hidden\" name=\"perso\" value=\"$perso_cible\">";
				echo "<input type=\"hidden\" name=\"methode\" value=\"e3\">";
				echo "<center><table>";
				foreach($obj as $key=>$val)
				{
					echo "<tr>";
					$req_objet = "select obj_etat,gobj_tobj_cod,obj_cod,obj_nom,obj_nom_generique,tobj_libelle,perobj_identifie
																from objet_generique,type_objet,perso_objets,objets
																where perobj_perso_cod = $perso_cod
																and perobj_equipe = 'N'
																and perobj_obj_cod = obj_cod
																and obj_gobj_cod = gobj_cod
																and gobj_tobj_cod = tobj_cod
																and gobj_deposable != 'N'
																and obj_cod = $key
																and not exists (select 1 from transaction where tran_obj_cod = obj_cod) ";
					$db->query($req_objet);
					if ($db->nf() == 0)
					{
						echo "<td colspan=\"2\"><p>Anomalie ! Objet $key non trouvé !";
					}
					else
					{
						$db->next_record();
						
						if ($db->f("perobj_identifie") == 'O')
						{
							$nom_objet = $db->f("obj_nom");
						}
						else
						{
							$nom_objet = $db->f("obj_nom_generique");
						}
						$si_identifie = $db->f("perobj_identifie");
						echo "<td class=\"soustitre2\"><p>$nom_objet $identifie[$si_identifie]";
						if (($db->f("gobj_tobj_cod") == 1) || ($db->f("obj_gobj_cod") == 2))
						{
							echo "  - " . get_etat($db->f("obj_etat"));
						}
						echo "</td>";
						echo "<td><p><input type=\"text\" name=\"obj[" . $db->f("obj_cod") . "]\" value=\"0\"></td>";	
						echo "</tr>";
					}
				}
				echo "<tr><td colspan=\"2\"><center><input type=\"submit\" class=\"test\" value=\"Valider les transactions !\"></center></td></tr>";
				echo "</table></center>";
			}
			else
			{
				echo "<p>Aucun objet sélectionné !";
			}
			break;
		case "e3";
			if ($obj)
			{
				foreach($obj as $key=>$val)
				{
					$req_ident = "select perobj_identifie from perso_objets where perobj_obj_cod = $key ";
					$db->query($req_ident);
					$db->next_record();
					$si_identifie = $db->f("perobj_identifie");
					$erreur = 0;
					$prix_obj = $prix[$key];
					if ($prix_obj < 0)
					{
						echo "<p>Erreur ! Le prix doit être positif !";
						$erreur = 1;
					}
					if ($prix_obj == '')
					{
						echo "<p>Erreur ! Le prix doit être fixé !";
						$erreur = 1;
					}
					if ($erreur == 0)
					{	
						//Analyse des cas de triche
						$tab = $db->get_pos($perso_cod);
						$pos_perso1 = $tab['pos_cod'];
						$tab = $db->get_pos($perso_cible);
						$pos_perso2 = $tab['pos_cod'];
						$distance = $db->distance($pos_perso1,$pos_perso2);
						$is_lieu = $db->is_lieu($perso_cod);
						$tab_lieu = $db->get_lieu($perso_cod);
						$lieu_protege = $tab_lieu['lieu_refuge'];
						/*Acceptation automatique des transactions entre persos d'un même compte*/
						$req = "select perso_type_perso from perso where perso_cod = $perso_cod";
						$db->query($req);
						$db->next_record();
						if ($db->f("perso_type_perso") == 1)
						{
							$req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cod";
							$db->query($req);
							$db->next_record();
							$compt1 = $db->f("pcompt_compt_cod");
						}
						else if ($db->f("perso_type_perso") == 3)
						{
							$req = "select pcompt_compt_cod from perso_familier,perso_compte where pfam_familier_cod = $perso_cod and pfam_perso_cod = pcompt_perso_cod";
							$db->query($req);
							$db->next_record();
							$compt1 = $db->f("pcompt_compt_cod");
						}
						else
						{
							$compt1 = '';
						}
						$req = "select perso_type_perso from perso where perso_cod = $perso_cible";
						$db->query($req);
						$db->next_record();
						if ($db->f("perso_type_perso") == 1)
						{
							$req = "select pcompt_compt_cod from perso_compte where pcompt_perso_cod = $perso_cible";
							$db->query($req);
							$db->next_record();
							$compt2 = $db->f("pcompt_compt_cod");
						}
						else if ($db->f("perso_type_perso") == 3)
						{
							$req = "select pcompt_compt_cod from perso_familier,perso_compte where pfam_familier_cod = $perso_cible and pfam_perso_cod = pcompt_perso_cod";
							$db->query($req);
							$db->next_record();
							$compt2 = $db->f("pcompt_compt_cod");
						}
						else
						{
							$compt2 = '';
						}						
						if ($distance != 0)
						{
						echo "<p>Vous ne pouvez pas faire de transaction sur des positions différentes !";
						}
						else if ($is_lieu and $lieu_protege == 'O') 
						{
								echo "<p>Vous ne pouvez pas faire de transaction sur un lieu protégé !";
						}
						
						else
						{
									$req_tran_cod = "select nextval('seq_tran_cod') as numero";
									$db->query($req_tran_cod);
									$db->next_record();
									$num_tran = $db->f("numero");
									$req_ins = "insert into transaction (tran_cod,tran_obj_cod,tran_vendeur,tran_acheteur,tran_nb_tours,tran_prix,tran_identifie)
																					values ($num_tran,$key,$perso_cod,$perso_cible," . $param->getparm(7) . ",$prix_obj,'$si_identifie')";
									$db->query($req_ins);
									echo("<p>La transaction est enregistrée. L'acheteur a deux tours pour valider cette transaction, faute de quoi elle sera annulée.<br />");
									echo("Elle sera également annulée si vous abandonnez l'objet (volontairement ou non), si vous l'équipez, ou si vous vous déplacez.");
				
									$texte = "[attaquant] a proposé un objet à la vente à [cible]";
									$req_evt = "insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
																					values (nextval('seq_levt_cod'),17,now(),1,$perso_cod,'$texte','O','N',$perso_cod,$perso_cible)";
									$db->query($req_evt);
					
									$req_evt = "insert into ligne_evt (levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
																					values (nextval('seq_levt_cod'),17,now(),1,$perso_cible,'$texte','N','N',$perso_cod,$perso_cible)";
									$db->query($req_evt);
									if ($compt1 == $compt2 and $prix_obj == 0)
											{
													$req_acc_tran = "select accepte_transaction($num_tran) as resultat";
													$db->query($req_acc_tran);
													$db->next_record();
													$resultat_temp = $db->f("resultat");
													$tab_res = explode(";",$resultat_temp);
													if ($tab_res[0] == -1)
													{
														echo("<p>Une erreur est survenue : $tab_res[1]");
													}
													else
													{
														echo"<p><b>Cette transaction (et seulement celle ci) est directement validée<br /></b>";
													}
											}
						}
					}
				}
				
			}
			else
			{
				echo "<p>Anomalie sur le prix !";
			}
			break;		
	}
	?>
<br><br><a href="http://www.jdr-delain.net/jeu/transactions2.php">Retour aux transactions</a>
<?php 
include "tab_bas.php";
?>
</body>
</html>
