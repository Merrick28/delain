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
$param = new parametres();
//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$erreur = 0;
$req_niveau = "select floor(perso_px) as perso_px,limite_niveau($perso_cod) as limite,perso_niveau from perso where perso_cod = $perso_cod";
$db->query($req_niveau);
$db->next_record();
if ($db->f("perso_px") < $db->f("limite"))
{
	echo("<p>Vous n'avez pas assez de PX pour passer au niveau supérieur !!!");
	$erreur = 1;
}
$niveau = $db->f("perso_niveau");
$limite_pa = $param->getparm(8);


$req_pa = "select perso_race_cod,perso_for,perso_dex,perso_int,perso_con,perso_nb_amel_comp,perso_niveau_vampire,perso_vampirisme,perso_pa,perso_amelioration_vue,perso_amelioration_regen,perso_des_regen,perso_valeur_regen,";
$req_pa = $req_pa . "perso_amelioration_degats,perso_amelioration_armure,perso_temps_tour,perso_niveau,perso_nb_amel_chance_memo,perso_nb_receptacle,";
$req_pa = $req_pa . "perso_amel_deg_dex,perso_capa_repar,perso_nb_amel_repar,nb_sort_memorisable($perso_cod) as nb_sorts from perso where perso_cod = $perso_cod";
$db->query($req_pa);
$db->next_record();
$pa = $db->f("perso_pa");
$race = $db->f("perso_race_cod");

if ($erreur == 0)
{
		if ($niveau <= 3)
		{
			$limite = 2;
		}
		else
		{
			$limite = floor($niveau/2);
		}
		echo("<form name=\"niveau\" action=\"action.php\" method=\"post\">");
		echo "<input type=\"hidden\" name=\"methode\" value=\"passe_niveau\">";
		echo("<p>Choisissez l'amélioration que vous souhaitez :");
		echo "<table cellspacing=\"2\">";
		echo "<tr>";
		echo "<td class=\"soustitre2\"><p><strong>Amélioration</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>Valeur actuelle</strong></td>";
		echo "<td class=\"soustitre2\"><p><strong>Nouvelle valeur</strong></td>";
		echo "<td></td>";

		/**********/
		/* CARACS */
		/**********/
		$coeff_for = 1.5;
		if ($db->f("perso_for") > 29)
			$coeff_for = 2;
		else if ($db->f("perso_for") > 24)
			$coeff_for = 1.75;

		$coeff_dex = 1.5;
		if ($db->f("perso_dex") > 29)
			$coeff_dex = 2;
		else if ($db->f("perso_dex") > 24)
			$coeff_dex = 1.75;

		$coeff_con = 1.5;
		if ($db->f("perso_con") > 29)
			$coeff_con = 2;
		else if ($db->f("perso_con") > 24)
			$coeff_con = 1.75;

		$coeff_int = 1.5;
		if ($db->f("perso_int") > 29)
			$coeff_int = 2;
		else if ($db->f("perso_int") > 24)
			$coeff_int = 1.75;

		if ($niveau >= ($db->f("perso_for") * $coeff_for))
		{
			echo("<tr>");
			echo("<td class=\"soustitre2\"><p>Force</td>");
			$nb = $db->f("perso_for");
			echo "<td><p>", $nb , "</td>";
			$nb = $nb + 1;
			echo "<td class=\"soustitre2\"><p>", $nb , "</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"27\"></td>";
			echo "</tr>";
		}
		if ($niveau >= ($db->f("perso_dex") * $coeff_dex))
		{
			echo("<tr>");
			echo("<td class=\"soustitre2\"><p>Dextérité</td>");
			$nb = $db->f("perso_dex");
			echo "<td><p>", $nb , "</td>";
			$nb = $nb + 1;
			echo "<td class=\"soustitre2\"><p>", $nb , "</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"28\"></td>";
			echo "</tr>";
		}
		if ($niveau >= ($db->f("perso_con") * $coeff_con))
		{
			echo("<tr>");
			echo("<td class=\"soustitre2\"><p>Constitution</td>");
			$nb = $db->f("perso_con");
			echo "<td><p>", $nb , "</td>";
			$nb = $nb + 1;
			echo "<td class=\"soustitre2\"><p>", $nb , "</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"29\"></td>";
			echo "</tr>";
		}
		if ($niveau >= ($db->f("perso_int") * $coeff_int))
		{
			echo("<tr>");
			echo("<td class=\"soustitre2\"><p>Intelligence</td>");
			$nb = $db->f("perso_int");
			echo "<td><p>", $nb , "</td>";
			$nb = $nb + 1;
			echo "<td class=\"soustitre2\"><p>", $nb , "</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"30\"></td>";
			echo "</tr>";
		}

		/**************************************************/
		/*              degats corps à corps              */
		/**************************************************/
		echo("<tr>");
		echo("<td class=\"soustitre2\"><p>Dégâts au corps-à-corps </td>");
		echo "<td><p>+" . $db->f("perso_amelioration_degats") . "</td>";
		if ($db->f("perso_amelioration_degats") < $limite)
		{
			$degats = $db->f("perso_amelioration_degats") + 1;
			echo "<td class=\"soustitre2\"><p>+$degats</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"4\"></td>";
		}
		else
		{
			echo "<td class=\"soustitre2\" colspan=\"2\"><p>Maximum atteint pour ce niveau ! (limité à niveau / 2)</td>";
		}
		echo "</tr>";

		/**************************************************/
		/*              degats distance                   */
		/**************************************************/
		echo("<tr>");
		echo("<td class=\"soustitre2\"><p>Dégâts armes à distance </td>");
		echo "<td><p>+" . $db->f("perso_amel_deg_dex") . "</td>";
		if ($db->f("perso_amel_deg_dex") < $limite)
		{
			$degats = $db->f("perso_amel_deg_dex") + 1;
			echo "<td class=\"soustitre2\"><p>+$degats</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"2\"></td>";
		}
		else
		{
			echo "<td class=\"soustitre2\" colspan=\"2\"><p>Maximum atteint pour ce niveau ! (limité à niveau / 2)</td>";
		}
		echo "</tr>";

		/**************************************************/
		/*              Armure                            */
		/**************************************************/
		echo("<tr>");
		echo("<td class=\"soustitre2\"><p>Armure </td>");
		echo "<td><p>+" . $db->f("perso_amelioration_armure") . "</td>";
		if ($db->f("perso_amelioration_armure") < $limite)
		{
			$degats = $db->f("perso_amelioration_armure") + 1;
			echo "<td class=\"soustitre2\"><p>+$degats</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"5\"></td>";
		}
		else
		{
			echo "<td class=\"soustitre2\" colspan=\"2\"><p>Maximum atteint pour ce niveau ! (limité à niveau / 2)</td>";
		}
		echo "</tr>";


		/**************************************************/
		/*              Vue                               */
		/**************************************************/
		echo("<tr>");
		echo("<td class=\"soustitre2\"><p>Vue </td>");
		echo "<td><p>+" . $db->f("perso_amelioration_vue") . "</td>";
		if (($db->f("perso_amelioration_vue") < $limite) && ($db->f("perso_amelioration_vue") < 5))
		{
			$degats = $db->f("perso_amelioration_vue") + 1;
			echo "<td class=\"soustitre2\"><p>+$degats</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"6\"></td>";
		}
		else
		{
			echo "<td class=\"soustitre2\" colspan=\"2\"><p>Maximum atteint pour ce niveau ! (limité à 5)</td>";
		}
		echo "</tr>";


		/**************************************************/
		/*              régénération                      */
		/**************************************************/
		if ($db->f("perso_niveau_vampire") == 0)
		{
			$regen_valeur_des = $db->f("perso_valeur_regen");
			echo("<tr>");
			echo("<td class=\"soustitre2\"><p>Régénération </td>");
			echo "<td><p>" . $db->f("perso_des_regen") . "D" . $regen_valeur_des . "</td>";
			if ($db->f("perso_des_regen") < $limite+1)
			{
				$regen_des = $db->f("perso_des_regen") + 1;
				echo "<td class=\"soustitre2\"><p>" . $regen_des . "D" . $regen_valeur_des . "</td>";
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"3\"></td>";
			}
			else
			{
				echo "<td class=\"soustitre2\" colspan=\"2\"><p>Maximum atteint pour ce niveau ! (limité à niveau / 2)</td>";
			}
			echo "</tr>";
		}
		else
		{
			echo("<tr>");
			echo("<td class=\"soustitre2\"><p>Vampirisme </td>");
			$vamp = $db->f("perso_vampirisme") * 10;
			echo "<td><p>" . $vamp . "</td>";
			if (($vamp < $limite) && ($vamp < 10))
			{
				$degats = $vamp + 0.5;
				echo "<td class=\"soustitre2\"><p>" . $degats . "</td>";
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"9\"></td>";
			}
			else
			{
				echo "<td class=\"soustitre2\" colspan=\"2\"><p>Maximum atteint pour ce niveau ! (limité à niveau / 2)</td>";
			}
			echo "</tr>";
		}



		/**************************************************/
		/*              temps tour                        */
		/**************************************************/
		$temps_actu = $db->f("perso_temps_tour");
		if ($temps_actu > 660)
		{
			$amel_temps = 30;
		}
		if (($temps_actu >585) && ($temps_actu <= 660))
		{
			$amel_temps = 25;
		}
		if (($temps_actu >525) && ($temps_actu <= 585))
		{
			$amel_temps = 20;
		}
		if (($temps_actu >480) && ($temps_actu <= 525))
		{
			$amel_temps = 15;
		}
		if (($temps_actu >450) && ($temps_actu <= 480))
		{
			$amel_temps = 10;
		}
		if ($temps_actu <= 450)
		{
			$amel_temps = 5;
		}
		$nv_temps = $temps_actu - $amel_temps;


		echo("<tr>");
		echo("<td class=\"soustitre2\"><p>Temps de tour </td>");
		echo "<td><p>" . $db->f("perso_temps_tour") . " minutes</td>";
        if ( $nv_temps >= 360 )
        {
            echo "<td class=\"soustitre2\"><p>$nv_temps minutes</td>";
            echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"1\"></td>";
        }
        else
        {
            echo "<td class=\"soustitre2\" colspan=\"2\"><p>Minimum atteint pour le temps de tour !</td>";
        }
		echo "</tr>";


		/**************************************************/
		/*              Réparation                        */
		/**************************************************/
		//
		// OBSOLETE !!
		//
		/*if ($db->f("perso_capa_repar") <= 40)
		{
			$repar = 5;
		}
		if (($db->f("perso_capa_repar") > 40) && ($db->f("perso_capa_repar") <= 50))
		{
			$repar = 4;
		}
		if (($db->f("perso_capa_repar") > 50) && ($db->f("perso_capa_repar") <= 60))
		{
			$repar = 3;
		}
		if (($db->f("perso_capa_repar") > 60) && ($db->f("perso_capa_repar") <= 70))
		{
			$repar = 2;
		}
		if ($db->f("perso_capa_repar") > 70)
		{
			$repar = 1;
		}
		echo("<tr>");
		echo("<td class=\"soustitre2\"><p>Réparation </td>");
		echo "<td><p>" . $db->f("perso_capa_repar") . "</td>";
		if ($db->f("perso_nb_amel_repar") < $limite)
		{
			$vue = $db->f("perso_capa_repar") + $repar;
			echo "<td class=\"soustitre2\"><p>$vue</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"7\"></td>";
		}
		else
		{
			echo "<td class=\"soustitre2\" colspan=\"2\"><p>Maximum atteint pour ce niveau ! (limité à niveau / 2)</td>";
		}
		echo "</tr>";*/
		/**************************************************/
		/*              Sorts mémorisables                */
		/**************************************************/
		echo("<tr>");
		echo("<td class=\"soustitre2\"><p>Sorts mémorisables </td>");
		echo "<td><p>" . $db->f("nb_sorts") . "</td>";
		if(($race == 1) || ($race == 3))
			$temp = $db->f("nb_sorts") + 4;
		else
			$temp = $db->f("nb_sorts") + 1;
		echo "<td class=\"soustitre2\"><p>$temp</td>";
		echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"8\"></td>";
		echo "</tr>";
		/*************************/
		/* compétences de combat */
		/*************************/
		$test = floor($db->f("perso_niveau")/7);
		$nb_rec = $db->f("perso_nb_receptacle");
		$nb_memo = $db->f("perso_nb_amel_chance_memo");
		if ($test > $db->f("perso_nb_amel_comp"))
		{
			$af0 = $db->existe_competence($perso_cod,25);
			$af1 = $db->existe_competence($perso_cod,61);
			$af2 = $db->existe_competence($perso_cod,62);
			$f0 = $db->existe_competence($perso_cod,63);
			$f1 = $db->existe_competence($perso_cod,64);
			$f2 = $db->existe_competence($perso_cod,65);
			$cg0 = $db->existe_competence($perso_cod,66);
			$cg1= $db->existe_competence($perso_cod,67);
			$cg2 = $db->existe_competence($perso_cod,68);
			$bp0 = $db->existe_competence($perso_cod,72);
			$bp1 = $db->existe_competence($perso_cod,73);
			$bp2 = $db->existe_competence($perso_cod,74);
			$tp0 = $db->existe_competence($perso_cod,75);
			$tp1 = $db->existe_competence($perso_cod,76);
			$tp2 = $db->existe_competence($perso_cod,77);
			if (!$af0 && !$af1 && !$af2)
			{
				echo "<tr>" ;
				echo "<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">Attaque foudroyante</a><br /><i>Attention: Uniquement pour les armes au <strong>corps à corps</strong></i></td>" ;
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"18\"></td>";
				echo "</tr>";
			}
			if ($af0 && !$af1 && !$af2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">Attaque foudroyante lvl 2 </a><br /><i>Attention: Uniquement pour les armes au <strong>corps à corps</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"10\"></td>";
				echo "</tr>";
			}
			if ($af1)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">Attaque foudroyante lvl 3 </a><br /><i>Attention: Uniquement pour les armes au <strong>corps à corps</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"11\"></td>";
				echo "</tr>";
			}
			if (!$f0 && !$f1 && !$f2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=1\">Feinte</a></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"12\"></td>";
				echo "</tr>";
			}
			if ($f0 && !$f2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=1\">Feinte lvl 2 </a></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"13\"></td>";
				echo "</tr>";
			}
			if ($f1)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=1\">Feinte lvl 3 </a></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"14\"></td>";
				echo "</tr>";
			}
			if (!$cg0 && !$cg1 && !$cg2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=2\">Coup de grace</a></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"15\"></td>";
				echo "</tr>";
			}
			if ($cg0 && !$cg2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=2\">Coup de grace lvl 2 </a></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"16\"></td>";
				echo "</tr>";
			}
			if ($cg1)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=2\">Coup de grace lvl 3 </a></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"17\"></td>";
				echo "</tr>";
			}
			if (!$bp0 && !$bp1 && !$bp2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=4\">Bout portant</a><br /><i>Attention: Uniquement pour les armes de <strong>distance</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"21\"></td>";
				echo "</tr>";
			}
			if ($bp0 && !$bp2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=4\">Bout portant lvl 2 </a><br /><i>Attention: Uniquement pour les armes de <strong>distance</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"22\"></td>";
				echo "</tr>";
			}
			if ($bp1)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=4\">Bout portant lvl 3 </a><br /><i>Attention: Uniquement pour les armes de <strong>distance</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"23\"></td>";
				echo "</tr>";
			}
			if (!$tp0 && !$tp1 && !$tp2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=5\">Tir précis</a><br /><i>Attention: Uniquement pour les armes de <strong>distance</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"24\"></td>";
				echo "</tr>";
			}
			if ($tp0 && !$tp2)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=5\">Tir précis lvl 2 </a><br /><i>Attention: Uniquement pour les armes de <strong>distance</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"25\"></td>";
				echo "</tr>";
			}
			if ($tp1)
			{
				echo("<tr>");
				echo("<td colspan=\"3\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=5\">Tir précis lvl 3 </a><br /><i>Attention: Uniquement pour les armes de <strong>distance</strong></i></td>");
				echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"26\"></td>";
				echo "</tr>";
			}
			echo("<tr>");
			echo("<td class=\"soustitre2\"><p><a href=\"desc_comp.php?index=3\">Réceptacle magique </a></td>");
			$nb = $nb_rec;
			echo "<td><p>", $nb , "</td>";
			$nb = $nb + 1;
			echo "<td class=\"soustitre2\"><p>", $nb , "</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"19\"></td>";
			echo "</tr>";

			//
			// OBSOLETE
			//
			/*echo("<tr>");
			echo("<td class=\"soustitre2\"><p>Mémorisation magique </td>");
			$nb = $nb_memo;
			$nb1 = $nb*0.1;
			echo "<td><p>+", $nb1 , " %</td>";
			$nb = $nb + 1;
			$nb1 = $nb*0.1;
			echo "<td class=\"soustitre2\"><p>+", $nb1 , " %</td>";
			echo "<td><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"20\"></td>";
			echo "</tr>";*/
		}
		echo("</table>");
        if ($pa < $limite_pa)
        {
            echo("<p>Vous n'avez pas assez de PA !!!!");
            $erreur = 1;
        }
        if ( $erreur == 0)
        {
            echo("<center><input type=\"submit\" class=\"test\" value=\"Valider !\"></center>");
        }
		echo("</form>");
	}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
