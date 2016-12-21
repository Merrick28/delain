<?php 
if(!defined("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include_once "verif_connexion.php";

// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 22)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
	}
}
if ($db->compte_objet($perso_cod,86) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
	$erreur = 1;
}
if ($db->compte_objet($perso_cod,87) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
	$erreur = 1;
}
if ($db->compte_objet($perso_cod,88) != 0)
{
	echo "<p>Vous ne pouvez pas prendre un esaclier avec un médaillon. Merci de reposer tous les médaillons avant de continuer.";
	$erreur = 1;
}

if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$tab_lieu = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	echo("<p><b>$nom_lieu</b><br>$desc_lieu ");
	if (!isset($methode))
	{
		$methode = "debut";
	}
	switch ($methode)
	{
		case "debut":
			
			$erreur = 0;
			echo "<p>Merci de rentrer ce mot de passe pour continuer (4PA pour prendre le passage si le mot de passe est correct, 1 PA sinon).";
			$req = "select perso_pa from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			if ($db->f("perso_pa") < 4)
			{
				echo "<p>Vous n'avez pas assez de Pa pour tenter !";
				$erreur = 1;
			}
			$req = "select is_noir($perso_cod) as noir ";
			$db->query($req);
			$db->next_record();
			//if ($db->f("noir") == 1)
			//{
				$seq = $db->getparm_t(71);
				//echo "<hr><p style=\"text-align:center;\">Le mot de passe actuel est : <br>";
        echo "<hr><p style=\"text-align:center;\">Apparement quelqu'un a griffonné le code juste à coté de la porte. : <br>";
        for($cpt=1;$cpt<=6;$cpt++)
				{
					$rang = substr($seq,$cpt-1,1);
					$req_rune = "select gobj_cod,gobj_rune_position,gobj_nom from objet_generique ";
					$req_rune = $req_rune . "where gobj_tobj_cod = 5 ";
					$req_rune = $req_rune . "and gobj_frune_cod = $cpt ";
					$req_rune = $req_rune . "and gobj_rune_position = $rang ";
					$db->query($req_rune);
					$db->next_record();
					echo "<img src=\"" . G_IMAGES . "rune_" . $cpt . "_" . $db->f("gobj_rune_position") . ".gif\">";
				}
				echo "<hr>";
				
				
				
			//}
			if ($erreur == 0)
			{
				$req = "select perso_pa from perso where perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				if ($db->f("perso_pa") >= 4)
				{
				?>
				<center><table>
				<form name="magie" method="post" action="<?php echo $PHP_SELF;?>">
				<input type="hidden" name="methode" value="passe">
				<?php 
				for ($famille=1;$famille<7;$famille++)
				{	
					echo "<tr><td>";
					echo "<center><table><tr>";
					$req_rune = "select gobj_cod,gobj_rune_position,gobj_nom from objet_generique where gobj_tobj_cod = 5 ";
					$req_rune = $req_rune . "and gobj_frune_cod = $famille ";
					$req_rune = $req_rune . "order by gobj_rune_position ";
					$db->query($req_rune);
					while($db->next_record())
					{
						echo "<td><center><img src=\"" . G_IMAGES . "rune_" . $famille . "_" . $db->f("gobj_rune_position") . ".gif\"></center>";
						?>
						<br>
						<?php 
						echo "<input type=\"radio\" class=\"vide\" name=\"fam_" , $famille , "\" value=\"" , $db->f("gobj_rune_position") , "\"";
						if ($db->f("gobj_rune_position") == 1)
						{
							echo " checked";
						}
						echo "></td>";
					}
					echo "</tr></table></center>";
					echo "</td></tr>";
				}
				echo "</table></center>";
			
				?>
				<center><input type="submit" value="Valider !" class="test"></center></form>
				<?php 
			}
			else
			{
				echo "<p>Pas assez de PA pour continuer !";
			}
			}
			break;
		case "passe":
			$req = "select perso_pa from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			if ($db->f("perso_pa") >= 4)
			{
			$resultat = $fam_1 . $fam_2 . $fam_3 . $fam_4 . $fam_5 . $fam_6;
			if ($resultat == $db->getparm_t(71))
			{
				$req = "select perso_type_perso from perso where perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				if ($db->f("perso_type_perso") == 3)
				{
					echo "<p>Erreur ! Un familier ne peut pas se déplacer seul !";
					break;
				}
				$req_deplace = "select passage($perso_cod) as deplace";
				$db->query($req_deplace);
				$db->next_record();
				$result = explode("#",$db->f("deplace"));
				echo $result[0];
				echo "<br>";
				echo("<a href=\"frame_vue.php\">Retour !</a></p>");
			}
			else
			{
				echo "<p>Désolé, le mot de passe n'est pas le bon. Vous restez face au passage sans arriver à entrer.";
				$req = "update perso set perso_pa = perso_pa - 1 where perso_cod = $perso_cod ";
				$db->query($req);
			}
			}
			else
			{
				echo "<p>Pas assez de PA pour continuer !";
			}
			break;
	}
}

?>
