<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
include_once "../includes/constantes.php";
include_once "verif_connexion.php";


// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un batiment administratif !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 9)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un batiment administratif !!!");
	}
	$lieu_cod = $tab_lieu['lieu_cod'];
}
if (!isset($methode))
{
	$methode = 'debut';
}
if ($erreur == 0)
{
	$req = "select perso_pnj from perso where perso_cod = $perso_cod";
	$db->query($req);
	$db->next_record();
	$quatrieme = $db->f("perso_pnj") == 2;

	$req = "select lpos_lieu_cod,pos_etage, pos_cod from lieu_position,perso_position,positions
		where ppos_perso_cod = $perso_cod 
			and ppos_pos_cod = lpos_pos_cod 
			and ppos_pos_cod = pos_cod";
	$db->query($req);
	$db->next_record();
	$lieu_cod = $db->f("lpos_lieu_cod");
	$etage_cod = $db->f("pos_etage");
	$pos_cod = $db->f("pos_cod");
	switch($methode)
	{
	    case "entrer_arene":
	    
	    $req = "select entrer_arene(".$perso_cod.",".$etage_num.",".$pos_cod.") as res";
		$db->query($req);
	    $db->next_record();
	    
	    $res = $db->f("res");
	    $libelle = explode(";", $res);
		echo $libelle[1];
	    
		$break = 'O';
		
		break;
		
		case "debut":
		?>
		<p><img src="../images/batadmin.gif"><b><?php  echo($tab_lieu['nom']. '</b> - '. $tab_lieu['description']  ); ?>
		<p>Bonjour,<br>
		Voici ce que vous pouvez faire ici :<br>
		<hr><br>
		Entrer dans une arène de combat : <br>
		
		<?php 
		echo("<table cellspacing=\"2\" cellpadding=\"2\">");
			echo("<tr><td class=\"soustitre2\" colspan=\"4\"><p style=\"text-align:center;\">Répartition par arène : </td></tr>");
			echo("<tr><td class=\"soustitre2\"><p>Arène</td>
			<td class=\"soustitre2\"><p>Personnages</td>
			<td class=\"soustitre2\"><p>Niveau moyen</td>
			<td class=\"soustitre2\"><p>Niveau maximum</td>
			</tr>");
			$req = "select etage_libelle, carene_level_max, ";
			$req = $req . "(select count(parene_perso_cod) from perso_arene ";
			$req = $req . " where parene_etage_numero = etage_numero) as joueur,";
			$req = $req . "(select sum(perso_niveau) from perso, perso_arene ";
			$req = $req . "where parene_etage_numero = etage_numero ";
			$req = $req . "and parene_perso_cod = perso_cod ) as jnv ";
			$req = $req . "from etage, carac_arene ";
			$req = $req . "where etage_arene = 'O' ";
			$req = $req . "and etage_numero = carene_etage_numero ";
			$req = $req . "and carene_ouverte = 'O' ";
			if ($quatrieme)
				$req = $req . "and etage_quatrieme_perso = 'O' ";
			else
				$req = $req . "and etage_quatrieme_perso = 'N' ";
			$db->query($req);
			
			while ($db->next_record())
			{
				echo "<tr><td class=\"soustitre2\"><p>" . $db->f("etage_libelle") . "</p></td>
				<td><p>" . $db->f("joueur") . "</td>
				<td><p>" . ($db->f("joueur") != 0 ?
				            round($db->f("jnv") / $db->f("joueur") , 0) :
				            0) . "</td>
				<td><p>" . ($db->f("carene_level_max") != 0 ?
				            $db->f("carene_level_max"): 'Tous niveaux') . "</td></tr>";
				            
			}

			echo("</table>");
		
		
		
		echo "<form name=\"ea\" method=\"post\" action=".$PHP_SELF.">";
		echo "<input type=\"hidden\" name=\"methode\" value=\"entrer_arene\">";
		echo "<select name=\"etage_num\">";
		$req = "select etage_numero, etage_libelle from etage
			inner join carac_arene on carene_etage_numero = etage_numero
			where etage_arene = 'O' and carene_ouverte = 'O' ";
		if ($quatrieme)
			$req = $req . "and etage_quatrieme_perso = 'O' ";
		else
			$req = $req . "and etage_quatrieme_perso = 'N' ";
		$db->query($req);
		
		while ($db->next_record()) {
			echo "<option value=".$db->f("etage_numero").">".$db->f("etage_libelle")."</option>";
		}
		echo "</select>";
		echo "<input type=\"submit\" value=\"Entrer (4 PA)\" />";
		echo "</form>";
		
		echo "<hr>";
		if ($lieu_cod == 1470)
		{
			if ($db->is_milice($perso_cod) != 0)
			{
				
				$req = "select pguilde_solde from guilde_perso where pguilde_perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				if ($db->f("pguilde_solde") > 0)
				{
					echo "Vous avez " , $db->f("pguilde_solde") , " brouzoufs de solde que vous pouvez retirer.<br>";
					echo "<a href=\"" , $PHP_SELF , "?methode=solde\">La retirer maintenant ?</a>";
				}
				else
				{
					echo "Vous n'avez pas de salaire à retirer à ce jour.";
				}
				echo "<hr>";
			}
			
			
		}
		if ($db->is_in_guilde($perso_cod))
		{
			echo "<p>Vous êtes déjà dans une guilde, il vous est impossible d'en créer une nouvelle.";
		}
		else
		{
			?>
			<a href="cree_guilde.php">Créer une guilde </a>(<?php  printf("%s",$db->getparm_n(27)); ?> PA - <?php  printf("%s",$db->getparm_n(28)); ?> brouzoufs)
			<?php 
		}
		$nb_queue_rat = $db->compte_objet($perso_cod,91);
		$nb_toile = $db->compte_objet($perso_cod,92);
		$nb_crochet = $db->compte_objet($perso_cod,94);
		$nb_patte = $db->compte_objet($perso_cod,833);
		$nb_citrouille_noire = $db->compte_objet($perso_cod,849);
		
		
	   
		
		echo "<form name=\"vente\" method=\"post\" action=\"action.php\">";
		echo "<input type=\"hidden\" name=\"methode\" value=\"vente_bat\">";
		echo "<input type=\"hidden\" name=\"objet\">";
		if ($nb_queue_rat != 0)
		{
			echo "<p>Vous avez " . $nb_queue_rat . " queues de rat dans votre inventaire. ";
			if ($nb_queue_rat >= 10)
			{
				echo "<a href=\"javascript:document.vente.objet.value=91;document.vente.submit();\">Vendre 10 queues de rat (2PA)</a>";
			}
			else
			{
				echo "Il faut au minimum 10 queues de rat pour pouvoir les vendre.";
			}
		}
		if ($nb_toile != 0)
		{
			echo "<p>Vous avez " . $nb_toile . " soies d'araignée dans votre inventaire. ";
			if ($nb_toile >= 10)
			{
				echo "<a href=\"javascript:document.vente.objet.value=92;document.vente.submit();\">Vendre 10 soies d'araignée (2PA)</a>";
			}
			else
			{
				echo "Il faut au minimum 10 soies d'araignée pour pouvoir les vendre.";
			}
		}	
		if ($nb_crochet != 0)
		{
			echo "<p>Vous avez " . $nb_crochet . " crochets de serpents dans votre inventaire. ";
			if ($nb_crochet >= 10)
			{
				echo "<a href=\"javascript:document.vente.objet.value=94;document.vente.submit();\">Vendre 10 crochets de serpents (2PA)</a>";
			}
			else
			{
				echo "Il faut au minimum 10 crochets de serpents pour pouvoir les vendre.";
			}
		}
		if ($nb_patte != 0)
		{
			echo "<p>Vous avez " . $nb_patte . " pattes de lièvre dans votre inventaire. ";
			if ($nb_patte >= 10)
			{
				echo "<a href=\"javascript:document.vente.objet.value=833;document.vente.submit();\">Vendre 10 pattes de lièvre (2PA)</a>";
			}
			else
			{
				echo "Il faut au minimum 10 pattes de lièvre pour pouvoir les vendre.";
			}
		}
		if ($nb_citrouille_noire != 0)
		{
			echo "<p>Vous avez " . $nb_citrouille_noire . " citrouilles noires dans votre inventaire. ";
			if ($nb_citrouille_noire >= 10)
			{
				echo "<a href=\"javascript:document.vente.objet.value=849;document.vente.submit();\">Vendre 10 citrouilles noires (2PA)</a>";
			}
			else
			{
				echo "Il faut au minimum 10 citrouilles noires pour les vendre au batiment administratif.";
			}
		}

		//La tournée des auberges Nouvelle version
		$req = "select count(paub_visite) as nbre_visite from perso_auberge,quete_perso
 			where paub_perso_cod = $perso_cod
 				and paub_visite = 'O'
				and pquete_perso_cod = $perso_cod 
				and pquete_termine = 'N'
				and pquete_quete_cod = '6'";
 		$db->query($req);
 		$db->next_record();
		$nbre_visite = $db->f("nbre_visite");
		if ($nbre_visite >= 8)
		{
			echo "<hr>Félicitations ! Vous avez terminé le marathon des auberges, vous êtes donc un vrai soiffard qui ferait palir un nain au comptoir !
			<br>Dorénavant, tout le monde vous reconnaitra, au moins sur ce point là !
			<br> Nous vous avons aussi offert un ustensile qui pourra vous être très utile dans vos explorations de taverne !";
			$req = "update perso set perso_px = perso_px + 10,perso_prestige = perso_prestige + 2
				where perso_cod = $perso_cod";
			$db->query($req);
			$db->next_record();
			$req = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date,ptitre_type) values ($perso_cod,'[Tournée des auberges]Membre de la confrérie des soiffards',now(),'4')";
			$db->query($req);
	 		$db->next_record();
			$req = "update quete_perso set pquete_termine = 'O',pquete_date_fin = now() where pquete_perso_cod = $perso_cod and pquete_quete_cod = '6'";
			$db->query($req);
			$db->next_record();
			$req = "select cree_objet_perso('410',$perso_cod)";
			$db->query($req);
			$db->next_record();
		}
		else
		{		
				echo '<hr><p>Désirez vous <a href="' . $PHP_SELF . '?nbre_visite='. $nbre_visite .'&methode=tournee">vous inscrire (50 brouzoufs - 1 PA)</a> pour la tournée des bars ?';
		}
		/*Intégration du positionnement des alchimistes et des enchanteurs*/
		$req = "select pos_x,pos_y,pos_etage,etage_libelle,perso_quete from perso,perso_position,positions,etage 
			where perso_cod = ppos_perso_cod 
				and ppos_pos_cod = pos_cod 
				and pos_etage = etage_numero 
				and perso_quete in ('quete_chasseur.php','enchanteur.php','quete_alchimiste.php')
				and pos_etage = $etage_cod
			order by perso_quete";
 		$db->query($req);
 		$db->next_record();
 		if ($db->nf() != 0)
 		{
 			echo '<hr><p>';
	 		while($db->next_record())
	 		{
	 			if ($db->f("perso_quete") == 'enchanteur.php')
	 			{
	 				echo 'Vous trouverez un enchanteur en position X : '.$db->f("pos_x").' / Y : '.$db->f("pos_y").' dans l\'étage '.$db->f("etage_libelle").'<br>'; 
	 			}
	 			if ($db->f("perso_quete") == 'quete_alchimiste.php')
	 			{
	 				echo 'Vous trouverez un alchimiste en position X : '.$db->f("pos_x").' / Y : '.$db->f("pos_y").' dans l\'étage '.$db->f("etage_libelle").'<br>'; 
	 			}
	 		}
	 		echo '</p>';
	 	} 			
		
		break;
		
	case "tournee":
		$erreur = 0;
		$req_pa = "select perso_pa,perso_po,perso_sex from perso where perso_cod = $perso_cod ";
		$db->query($req_pa);
		$db->next_record();
		$nb_po = $db->f("perso_po");
		$prix = 50;
		$sexe = $db->f("perso_sex");
			
		if ($db->f("perso_po") < $prix)
		{
			echo("<p>Vous savez, $nom_sexe[$sexe], nous ne vous inscrirons pas si vous n'avez pas de quoi payer la somme de 50 brouzoufs !<br />");
			$erreur = 1;
		}
		if ($db->f("perso_pa") < 1)
		{
			echo("<p>pas assez de PA....<br />");
			$erreur = 1;
		}
		if ($erreur == 0)
		{
			$req = "select pquete_cod,pquete_termine from quete_perso 
				where pquete_perso_cod = $perso_cod;
					and pquete_quete_cod = 6 ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				$req = "insert into quete_perso (pquete_perso_cod,pquete_quete_cod,pquete_date_debut);
				values ($perso_cod,6,now()); ";
				$db->query($req);
				$db->next_record();
				$req = "update perso set perso_po = perso_po - 50,perso_pa = perso_pa - 1 where perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				echo "<p>Vous êtes bien enregistré !<br>Vous allez devoir visiter au moins huit auberges différentes pour faire partie de l'élite. Activez vous pour mériter la suprême récompense.
					<br>Une fois que vous aurez réussi cette épreuve, vous pourrez revenir dans un batiment administratif pour les dernières formalités.
					<br>Pour vous aider, nous vous conseillons d'utiliser <a href=\"http://www.jdr-delain.net/forum/ftopic7599.php\">le Guide des Tavernes de Pépé Génépy</a>";
			}
			else
			{
				$db->next_record();
				$quete_termine = $db->f("pquete_termine");
				if ($quete_termine == 'O')
				{
					echo "Vous avez déjà réalisé avec succès cette quête !";
				}
				else	
				{
					echo "<p>Vous êtes déjà inscrit à cette tournée !<br>
						Vous n’avez visité que $nbre_visite auberges : c’est moins que votre contrat initial ! Poursuivez donc vos efforts !";
				}
			}
		}
		break;		
		//Fin nouvelle version		
		
		case "solde":
			$req = "select pguilde_solde from guilde_perso where pguilde_perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			$solde = $db->f("pguilde_solde");
			$req = "update perso set perso_po = perso_po + $solde where perso_cod = $perso_cod ";
			$db->query($req);
			$req = "update guilde_perso set pguilde_solde = 0 where pguilde_perso_cod = $perso_cod ";
			$db->query($req);
			echo "<p>Vous venez de retirer votre solde.";
		break;
	}
	if ($db->is_milice($perso_cod) == 1)
	{
	echo "<p><a href=\"milice_tel.php\">Se téléporter vers un autre lieu ? </a>";
	}

}

if (!isset($break)) {
	echo "</form>";
	include_once "quete.php";
}
?>
