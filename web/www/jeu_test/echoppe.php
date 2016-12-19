<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");
if(!isset($db))
	include "verif_connexion.php";
$db2 = new base_delain;
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php 
// on regarde si le joueur est bien sur une échoppe
$erreur = 0;
if (!$db->is_lieu($perso_cod))
{
	echo("<p>Erreur ! Vous n'êtes pas sur un magasin !!!");
	$erreur = 1;
}
if ($erreur == 0)
{
	$tab_lieu = $db->get_lieu($perso_cod);
	if ($tab_lieu['type_lieu'] != 11)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un magasin !!!");
	}
}

if ($erreur == 0)
{
	echo "<p><b>" . $tab_lieu['nom'] . "<b><br>";
	$desc = str_replace(chr(127),";",$tab_lieu['description']);
	echo "<i>" . $desc . "</i>";
	$controle_gerant = '';
	$req = "select mger_perso_cod from magasin_gerant where mger_lieu_cod = ". $lieu;
	$db->query($req);
	$db->next_record();
	if ($db->f("mger_perso_cod") == $perso_cod)
	{
		$controle_gerant = 'OK';
	}
	$lieu = $tab_lieu['lieu_cod'];
	$req = "select mod_vente($perso_cod,$lieu) as modificateur ";
	$db->query($req);
	$db->next_record();
	$modif = $db->f("modificateur");
	if (!isset($methode))
	{
		$methode = 'entree';
	}
	//
	// phrase à modifier par la suite en fonction des alignements
	//
	switch ($methode)
	{
		case "entree":
			echo "<p>Bonjour aventurier.";
			?>
			<form name="echoppe" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode">
			<p>Voulez-vous :
			<ul>
			<li><a href="javascript:document.echoppe.methode.value='acheter';document.echoppe.submit()">Acheter de l'équipement ?</a>
			<li><a href="javascript:document.echoppe.methode.value='vendre';document.echoppe.submit()">Vendre de l'équipement ?</a>
			<li><a href="javascript:document.echoppe.methode.value='identifier';document.echoppe.submit()">Faire identifier de l'équipement ?</a>
			<li><a href="javascript:document.echoppe.methode.value='repare';document.echoppe.submit()">Faire réparer de l'équipement ?</a>
			</ul>
			<?php 
			if ($controle_gerant == 'OK')
			{
				?>
				<li><a href="javascript:document.echoppe.methode.value='mule';document.echoppe.submit()">Récupérer un familier mûle dans votre échoppe ?</a>  <i>(Attention, ceci est une action définitive)</i>
				<?php 
			}
			break;
		case "acheter":
			echo "<p class=\"titre\">Achat d'équipement</p>";
			$req = "select perso_po from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			echo "<p>Vous avez actuellement <b>" . $db->f("perso_po") . "</b> brouzoufs. ";
			$po = $db->f("perso_po");
			$lieu_cod = $tab_lieu['lieu_cod'];
			$req = "select 0 as type,0 as a,obj_nom,tobj_libelle,gobj_cod,f_prix_obj_perso_a($perso_cod,$lieu_cod,obj_cod) as valeur_achat,coalesce(obj_obon_cod, 0) as obj_obon_cod,count(*) as nombre,comp_libelle
				from objets,objet_generique,stock_magasin,type_objet,competences
				where mstock_lieu_cod = $lieu_cod
				and mstock_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod
				and obj_nom = gobj_nom
				and gobj_comp_cod = comp_cod
				group by obj_nom,a,tobj_libelle,gobj_cod,valeur_achat,obj_obon_cod
				union
				select 1 as type,obj_cod as a,obj_nom,tobj_libelle,gobj_cod,f_prix_obj_perso_a($perso_cod,$lieu_cod,obj_cod) as valeur_achat,coalesce(obj_obon_cod, 0) as obj_obon_cod,count(*) as nombre,comp_libelle
				from objets,objet_generique,stock_magasin,type_objet,competences
				where mstock_lieu_cod = $lieu_cod
				and mstock_obj_cod = obj_cod
				and obj_gobj_cod = gobj_cod
				and gobj_tobj_cod = tobj_cod
				and obj_nom != gobj_nom
				and gobj_comp_cod = comp_cod
				group by obj_nom,a,tobj_libelle,gobj_cod,valeur_achat,obj_obon_cod
				order by tobj_libelle,gobj_comp_cod,valeur_achat,obj_nom ";
			//die ($req);
			$db->query($req);

			if ($db->nf() == 0)
			{
				echo "<p>Désolé, mais les stocks sont vides, nous n'avons rien à vendre en ce moment.";
			}
			else
			{
				echo "<form name=\"achat\" action=\"action.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_achat\">";
				echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
				echo "<input type=\"hidden\" name=\"objet\">";
				echo "<center><table>";
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p><b>Nom</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Type</b></td>";
				echo "<td class=\"soustitre2\"><p><b><i>Compétence</i></b></td>";
				echo "<td class=\"soustitre2\"><p><b>Prix</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Quantité disponible</b></td>";
				echo "<td></td>";
				while ($db->next_record())
				{
					$req = "select obon_cod,obon_libelle,obon_prix from bonus_objets ";
					$req = $req . "where obon_cod = " . $db->f("obj_obon_cod") ;
					$db2->query($req);
					if ($db2->nf() != 0)
					{
						$db2->next_record();
						$bonus = " (" . $db2->f("obon_libelle") . ")";
						$prix_bon =  $db2->f("obon_prix");
						$url_bon = "&bon=" . $db2->f("obon_cod");
					}
					else
					{
						$bonus = "";
						$prix_bon = 0;
						$url_bon = "";
					}
					$prix = $db->f("gobj_valeur") + $prix_bon;
					echo "<tr>";
					echo "<td class=\"soustitre2\"><p><b>";
					if ($db->f("type") == 0)
					{
						$db2 = new base_delain;
						$req = "select obj_cod from objets,	stock_magasin
							where obj_gobj_cod = " . $db->f("gobj_cod") . "
							and obj_cod = mstock_obj_cod
							and mstock_lieu_cod = $lieu_cod
							limit 1";
						$db2->query($req);
						$db2->next_record();
						echo "<a href=\"visu_desc_objet3.php?objet=" . $db2->f("obj_cod") . "&origine=e" , $url_bon , "\">";
					}

					else
						echo "<a href=\"visu_desc_objet3.php?objet=" . $db->f("a") . "&origine=e" , $url_bon , "\">";
					echo $db->f("obj_nom") , $bonus;
					echo "</a>";
					echo "</b></td>";
					echo "<td class=\"soustitre2\"><p>" . $db->f("tobj_libelle") . "</td>";
					echo "<td class=\"soustitre2\"><p>" . $db->f("comp_libelle") . "</td>";
					echo "<td class=\"soustitre2\"><p>" . $db->f("valeur_achat") . " brouzoufs</td>";

					echo "<td><p>" , $db->f("nombre") , "</td>";
					echo "<td><p>";
					echo "<input type=\"text\" name=\"";
					if ($db->f("type") == 0)
						echo "gobj[" , $db->f("gobj_cod") , "-" , $db->f("obj_obon_cod") , "]\" value=\"0\">";
					else
						echo "uobj[" , $db->f("a") , "]\" value=\"0\">";
					echo "</td>";
					echo "</tr>\n";
				}
				echo "</table></center>";
				echo "<center><input type=\"submit\" class=\"test\" value=\"Acheter les quantités sélectionnées !\"></center>";
				echo "</form>";
			}
			break;
		case "vendre":
			$taux_rachat = $db->getparm_n(46);
			$lieu_cod = $tab_lieu['lieu_cod'];
			echo "<p class=\"titre\">Vente d'équipement</p>";
			$req = "select obj_cod,obj_etat,obj_nom as nom,f_prix_obj_perso_v($perso_cod,$lieu_cod,obj_cod) as valeur,tobj_libelle
								from objet_generique,objets,perso_objets,type_objet
								where perobj_perso_cod = $perso_cod
								and perobj_obj_cod = obj_cod
								and perobj_identifie = 'O'
								and perobj_equipe != 'O'
								and obj_gobj_cod = gobj_cod
								and obj_deposable = 'O'
								and gobj_tobj_cod = tobj_cod
								and tobj_cod in (1,2,4,25)
								union all
								select obj_cod,obj_etat,obj_nom as nom,f_prix_obj_perso_v($perso_cod,$lieu_cod,obj_cod) as valeur,tobj_libelle
								from objet_generique,objets,perso_objets,type_objet
								where perobj_perso_cod = $perso_cod
								and perobj_obj_cod = obj_cod
								and perobj_equipe != 'O'
								and obj_gobj_cod = gobj_cod
								and obj_deposable = 'O'
								and gobj_echoppe_vente = 'O'
								and gobj_tobj_cod = tobj_cod
								and tobj_cod = 11 ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Vous n'avez aucun équipement à  vendre pour l'instant.";
			}
			else
			{


				echo "<form name=\"vente\" action=\"action.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_vente\">";
				echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
				echo "<input type=\"hidden\" name=\"objet\">";
				echo "<center><table>";
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p><b>Nom</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Type</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Prix</b></td>";
				echo "<td></td>";
				while ($db->next_record())
				{
					$req = "select obon_cod,obon_libelle,obon_prix from bonus_objets,objets ";
					$req = $req . "where obj_cod = " . $db->f("obj_cod") . " and obj_obon_cod = obon_cod ";
					$db2->query($req);
					if ($db2->nf() != 0)
					{
						$db2->next_record();
						$bonus = " (" . $db2->f("obon_libelle") . ")";
						$prix_bon =  $db2->f("obon_prix");
						$url_bon = "&bon=" . $db2->f("obon_cod");
					}
					else
					{
						$bonus = "";
						$prix_bon = 0;
						$url_bon = "";
					}
					$prix = $db->f("valeur") + $prix_bon;
					echo "<tr>";
					echo "<td class=\"soustitre2\"><p><b>" . $db->f("nom") . "</b></td>";
					echo "<td class=\"soustitre2\"><p>" . $db->f("tobj_libelle") . "</td>";
					echo "<td class=\"soustitre2\"><p>" . $db->f("valeur") . " brouzoufs</td>";
					echo "<td><p><input type=\"checkbox\" name=\"obj[" , $db->f("obj_cod") , "]\"></td>";

				}
				echo "</table></center>";
				echo "<center><input type=\"submit\" class=\"test\" value=\"Vendre les objets sélectionnées !\"></center>";
				echo "</form>";
			}
			break;
		case "identifier":
			$lieu_cod = $tab_lieu['lieu_cod'];
			echo "<p class=\"titre\">Identification d'équipement</p>";
			$req = "select perso_po from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			echo "<p>Vous avez actuellement <b>" . $db->f("perso_po") . "</b> brouzoufs. ";
			$req = "select lieu_marge from lieu where lieu_cod = $lieu_cod ";
			$db->query($req);
			$db->next_record();
			$prix = $db->f("lieu_marge") + 100;
			$req = "select obj_cod,gobj_nom_generique,tobj_libelle ";
			$req = $req . "from objet_generique,objets,perso_objets,type_objet ";
			$req = $req . "where perobj_perso_cod = $perso_cod ";
			$req = $req . "and perobj_obj_cod = obj_cod ";
			$req = $req . "and perobj_identifie != 'O' ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and gobj_tobj_cod = tobj_cod ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Vous n'avez aucun équipement à faire identifier pour l'instant.";
			}
			else
			{
				echo "<form name=\"identifie\" action=\"action.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_identifie\">";
				echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
				echo "<input type=\"hidden\" name=\"objet\">";

				echo "<center><table>";
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p><b>Nom</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Type</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Prix</b></td>";
				echo "<td></td>";
				while ($db->next_record())
				{

					echo "<tr>";
					echo "<td class=\"soustitre2\"><p><b>" . $db->f("gobj_nom_generique") . "</b></td>";
					echo "<td class=\"soustitre2\"><p>" . $db->f("tobj_libelle") . "</td>";
					echo "<td class=\"soustitre2\"><p>" . $prix . " brouzoufs</td>";
					echo "<td><p><input type=\"checkbox\" name=\"obj[" , $db->f("obj_cod") , "]\"></td>";
				}
				echo "</table></center>";
				echo "<center><input type=\"submit\" class=\"test\" value=\"Identifier les objets sélectionnées !\"></center>";
				echo "</form>";

			}
			break;
		case "repare":
			$lieu_cod = $tab_lieu['lieu_cod'];
			echo "<p class=\"titre\">Réparation d'équipement</p>";
			$req = "select perso_po from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			echo "<p>Vous avez actuellement <b>" . $db->f("perso_po") . "</b> brouzoufs. ";
			$req = "select obj_cod,obj_etat,gobj_nom as nom,f_prix_objet($lieu_cod,obj_cod) as valeur,tobj_libelle ";
			$req = $req . "from objet_generique,objets,perso_objets,type_objet ";
			$req = $req . "where perobj_perso_cod = $perso_cod ";
			$req = $req . "and perobj_obj_cod = obj_cod ";
			$req = $req . "and perobj_identifie = 'O' ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and gobj_deposable = 'O' ";
			$req = $req . "and gobj_tobj_cod = tobj_cod ";
			$req = $req . "and tobj_cod in (1,2,4) ";
			$req = $req . "and obj_etat < 100 ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Vous n'avez aucun équipement à  réparer pour l'instant.";
			}
			else
			{
				echo "<form name=\"vente\" action=\"action.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"methode\" value=\"nv_magasin_repare\">";
				echo "<input type=\"hidden\" name=\"lieu\" value=\"$lieu\">";
				echo "<input type=\"hidden\" name=\"objet\">";
				echo "<center><table>";
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p><b>Nom</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Type</b></td>";
				echo "<td class=\"soustitre2\"><p><b>Prix</b></td>";
				echo "<td></td>";
				while ($db->next_record())
				{
					$req = "select obon_cod,obon_libelle,obon_prix from bonus_objets,objets ";
					$req = $req . "where obj_cod = " . $db->f("obj_cod") . " and obj_obon_cod = obon_cod ";
					$db2->query($req);
					if ($db2->nf() != 0)
					{
						$db2->next_record();
						$bonus = " (" . $db2->f("obon_libelle") . ")";
						$prix_bon =  $db2->f("obon_prix");
						$url_bon = "&bon=" . $db2->f("obon_cod");
					}
					else
					{
						$bonus = "";
						$prix_bon = 0;
						$url_bon = "";
					}
					$etat = $db->f("obj_etat");
					echo "<tr>";
					echo "<td class=\"soustitre2\"><p><b>" . $db->f("nom") . "</b></td>";
					echo "<td class=\"soustitre2\"><p>" . $db->f("tobj_libelle") . "</td>";
					$prix = ($db->f("valeur") + $prix_bon) * 0.2 * $modif;
					$prix = $prix * (100 - $etat);
					$prix = $prix / 100;
					echo "<td class=\"soustitre2\"><p>" . floor($prix) . " brouzoufs</td>";
					echo "<td><p><input type=\"checkbox\" name=\"obj[" , $db->f("obj_cod") , "]\"></td>";

				}
				echo "</table></center>";
				echo "<center><input type=\"submit\" class=\"test\" value=\"Réparer les objets sélectionnées !\"></center>";
				echo "</form>";
			}


			break;
		case "mule":
		/* on regarde s'il n'y a pas déjà un familier*/
				$req = "select pfam_familier_cod from perso_familier,perso
								where pfam_perso_cod = cible
								and pfam_familier_cod = ". $perso_cod ."
								and perso_actif = 'O'";
				$db2->query($req);
				if ($db2->nf() != 0)
					{
							echo "<p>Vous ne pouvez pas récupérer un familier mule ici. Vous êtes déjà en charge d'un autre familier, deux seraient trop à gérer.";
							break;
					}
				/* on créé le familier*/
					$req = "select ppos_pos_cod,perso_nom from perso_position,perso where ppos_perso_cod = perso_cod and ppos_perso_cod = ". $perso_cod;
					$db2->query($req);
					$db2->next_record();
					$position = $db2->f("ppos_pos_cod");
					$nom = $db2->f("perso_nom");
					$req = "select cree_monstre_pos(193,". $position .") as familier_num";
					$db2->query($req);
					$db2->next_record();
					$num_fam = $db2->f("familier_num");
					$req = "update perso set perso_nom = 'Familier de ". $nom ."',perso_lower_perso_nom = 'familier de ". strtolower($nom) .",perso_type_perso = 3,perso_kharma=0 where perso_cod = ". $num_fam;
				/* on le rattache au perso*/
					$req = "insert into perso_familier (pfam_perso_cod,pfam_familier_cod) values ($perso_cod, $num_fam)";
					$db2->query($req);
			break;
		default:
			echo "<p>Anomalie : aucune methode passée !";
			break;
	}
}

echo "</form>";
?>
</body>
</html>
