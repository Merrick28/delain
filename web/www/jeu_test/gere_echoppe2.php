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
$erreur = 0;
if (!isset($mag))
{
	echo "<p>Erreur sur la transmission du lieu_cod ";
	$erreur = 1;
}
if ($erreur == 0)
{
	$req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle,lieu_alignement ";
	$req = $req . "from lieu,lieu_position,positions,etage,magasin_gerant ";
	$req = $req . "where lieu_cod = lpos_lieu_cod ";
	$req = $req . "and lieu_tlieu_cod in (11,14,21) ";
	$req = $req . "and lpos_pos_cod = pos_cod ";
	$req = $req . "and pos_etage = etage_numero ";
	$req = $req . "and mger_lieu_cod = lieu_cod ";
	$req = $req . "and mger_perso_cod = $perso_cod ";
	$req = $req . "and lieu_cod = $mag ";
	$db->query($req);
	if ($db->nf() == 0)
	{
		echo "<p>Erreur, vous n'êtes pas en gérance de ce magasin !";
		$erreur = 1;
	}
	else
	{
		$db->next_record();
	}
}
if ($erreur == 0)
{
	if (!isset($methode))
	{
		$methode = "debut";
	}
	echo "<p class=\"titre\">Gestion de : ", $db->f("lieu_nom"), " - (", $db->f("pos_x"), ", ", $db->f("pos_y"), ", ", $db->f("etage_libelle"), ")</p>";
	switch($methode)
	{
		case "debut":
			$modif_possible = 0;
			
			$req = "select lieu_compte, lieu_marge, lieu_prelev, lieu_alignement ";
			$req = $req . "from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			echo "<center><table>";
			
			echo "<tr>";
			echo "<td colspan=\"2\" class=\"soustitre2\"><p><strong>Financier :</td>";
			echo "</tr>";
		
			echo "<td class=\"soustitre2\"><p>Etat de la caisse</td>";
			echo "<td><p>" . $db->f("lieu_compte") . " brouzoufs</td>";
			echo "</tr>";
			
			echo "<td class=\"soustitre2\"><p>Marge effectuée</td>";
			echo "<td><p>" . $db->f("lieu_marge") . " %</td>";
			echo "</tr>";
			
			echo "<td class=\"soustitre2\"><p>Prélèvements par l'administration</td>";
			echo "<td><p>" . $db->f("lieu_prelev") . " %</td>";
			echo "</tr>";
			
			echo "<td class=\"soustitre2\"><p>Protection</td>";
			if ($db->f("lieu_prelev") == 15)
			{
				$protection = "Votre magasin n'est pas un refuge";
			}
			else
			{
				$protection = "Votre magasin est un refuge";
			}
			echo "<td><p>" . $protection . "</td>";
			echo "</tr>";
			
			echo "<td class=\"soustitre2\"><p>Alignement</td>";
			echo "<td><p>" . $db->f("lieu_alignement") . "</td>";
			echo "</tr>";
			
			$tab_lieu = $db->get_lieu($perso_cod);
			if ($tab_lieu['type_lieu'] == 11)
			{
				$modif_possible = 1;
			}
			if ($tab_lieu['type_lieu'] == 9)
			{
				$modif_possible = 1;
			}
			$modif_possible = 1;
			if ($modif_possible == 1)
			{
				echo "<tr>";
				echo "<td colspan=\"2\" class=\"soustitre2\"><p style=\"text-align:center\"><strong><a href=\"gere_echoppe2.php?mag=$mag&methode=mod\">Modifier ces données</a></strong></td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan=\"2\" class=\"soustitre2\"><p style=\"text-align:center\"><strong><a href=\"gere_echoppe2.php?mag=$mag&methode=nom\">Changer le nom et la description</a></strong></td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan=\"2\" class=\"soustitre2\"><p style=\"text-align:center\"><strong><a href=\"gere_echoppe2.php?mag=$mag&methode=vente_adm\">Vendre du matériel à l'administration</a></strong></td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan=\"2\" class=\"soustitre2\"><p style=\"text-align:center\"><strong><a href=\"gere_echoppe2.php?mag=$mag&methode=achat_adm\">Acheter du matériel à l'administration</a></strong></td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan=\"2\" class=\"soustitre2\"><p style=\"text-align:center\"><strong><a href=\"gere_echoppe2.php?mag=$mag&methode=fix_prix\">Fixer les tarifs</a></strong></td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td colspan=\"2\" class=\"soustitre2\"><p style=\"text-align:center\"><strong><a href=\"gere_echoppe2.php?mag=$mag&methode=stats\">Voir les stats</a></strong></td>";
				echo "</tr>";
			}
			
			
			echo "</table></center>";
			
			echo "<center><table>";
			
			echo "<tr>";
			echo "<td colspan=\"3\" class=\"soustitre2\"><p><strong>Etat des stocks : </td>";
			echo "</tr>";
			
			$req = "select gobj_nom,tobj_libelle,count(obj_cod) as qte ";
			$req = $req . "from objets,objet_generique,stock_magasin,type_objet ";
			$req = $req . "where mstock_lieu_cod = $mag ";
			$req = $req . "and mstock_obj_cod = obj_cod ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and gobj_tobj_cod = tobj_cod ";
			$req = $req . "group by gobj_nom,tobj_libelle ";
			$req = $req . "order by tobj_libelle,gobj_nom ";
			$db->query($req);
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Quantité</strong></td>";
			while ($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("gobj_nom") . "</td>";
				echo "<td><p>" . $db->f("tobj_libelle") . "</td>";
				echo "<td><p>" . $db->f("qte") . "</td>";
				echo "</tr>";
			}
			
			echo "</table></center>";

			break;	
		case "mod":

			$req = "select lieu_compte, lieu_marge, lieu_prelev, lieu_neutre, lieu_alignement ";
			$req = $req . "from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();						
			echo "<center><table>";
			echo "<td class=\"soustitre2\"><p>Etat de la caisse</td>";
			echo "<td><p>" . $db->f("lieu_compte") . " brouzoufs</td>";
			echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=banque\">Faire un retrait ?</a><br>";
			echo "<a href=\"gere_echoppe2.php?mag=$mag&methode=depot\">Faire un depot ?</a></td>";
			echo "</tr>";

			echo "<td class=\"soustitre2\"><p>Marge effectuée</td>";
			echo "<td><p>" . $db->f("lieu_marge") . " %</td>";
			echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=marge\">Changer la marge ?</a></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Protection</td>";
			if ($db->f("lieu_prelev") == 15)
			{
				$protection = "Votre magasin n'est pas un refuge";
			}
			else
			{
				$protection = "Votre magasin est un refuge";
			}
			echo "<td><p>" . $protection . "</td>";
			echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=statut\">Changer le statut ?</a></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Alignement</td>";
			$neutre[0] = "non neutre ";
			$neutre[1] = "neutre ";
			$idx_neutre = $db->f("lieu_neutre");
			echo "<td><p>" , $db->f("lieu_alignement") , " - " , $neutre[$idx_neutre] , "</td>";
			echo "<td><p><a href=\"gere_echoppe2.php?mag=$mag&methode=align\">Changer l'alignement ?</a></td>";
			echo "</tr>";
			
			
			echo "</table></center>";
		
			break;
		case "banque":
			$req = "select lieu_compte,perso_po ";
			$req = $req . "from lieu,perso ";
			$req = $req . "where lieu_cod = $mag ";
			$req = $req . "and perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			echo "<p>Vous avez actuellement <strong>" . $db->f("perso_po") . "</strong> brouzoufs sur vous, et il y a <strong>" . $db->f("lieu_compte") . "</strong> brouzoufs dans les caisses de l'échoppe.";
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"banque2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
			echo "<p>Retirer <input type=\"text\" name=\"qte\" value=\"0\"> brouzoufs du compte de l'échoppe ?";
			echo "<center><input type=\"submit\" class=\"test\" value=\"Valider le transfert ?\">";
			echo "</form>";
			break;
		case "banque2":
			$req = "select lieu_compte,perso_po ";
			$req = $req . "from lieu,perso ";
			$req = $req . "where lieu_cod = $mag ";
			$req = $req . "and perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			$banque = $db->f("lieu_compte");
			$erreur = 0;
			if (!isset($qte))
			{
				echo "<p>Erreur ! Quantité non définie !";
				$erreur = 1;
			}
			if ($qte < 0)
			{
				echo "<p>Erreur ! Quantité négative !";
				$erreur = 1;
			}
			if ($qte > $banque)
			{
				echo "<p>Erreur ! Pas assez de brouzoufs à retirer !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				// message
				$req = "select perso_nom,nextval('seq_msg_cod') as message from perso where perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				$nom = str_replace("'","\'",$db->f("perso_nom"));
				$message = $db->f("message");
				$req = "insert into messages (msg_cod,msg_corps,msg_titre,msg_date,msg_date2) values ($message,'$nom a effectué un retrait de $qte brouzoufs','Retrait',now(),now()) ";
				$db->query($req);
				$req = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod) values ($message,$perso_cod) ";
				$db->query($req);
				$tab_lieu = $db->get_lieu($perso_cod);
				if ($tab_lieu['type_lieu'] == 11)
				{
					$req = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe = 'O' ";
				}
				if ($tab_lieu['type_lieu'] == 9)
				{
					$req = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe = 'O' ";
				}
				if ($tab_lieu['type_lieu'] == 21)
				{
					$req = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe_noir = 'O' ";
				}
				$db->query($req);
						
				
				
				$req = "update lieu set lieu_compte = lieu_compte - $qte where lieu_cod = $mag ";
				$db->query($req);
				$req = "update perso set perso_po = perso_po + $qte where perso_cod = $perso_cod ";
				$db->query($req);
				echo "<p>La transaction a été effectuée.";
				
			}
			break;
		case "depot":
			$req = "select lieu_compte,perso_po ";
			$req = $req . "from lieu,perso ";
			$req = $req . "where lieu_cod = $mag ";
			$req = $req . "and perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			echo "<p>Vous avez actuellement <strong>" . $db->f("perso_po") . "</strong> brouzoufs sur vous, et il y a <strong>" . $db->f("lieu_compte") . "</strong> brouzoufs dans les caisses de l'échoppe.";
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"depot2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
			echo "<p>Déposer <input type=\"text\" name=\"qte\" value=\"0\"> brouzoufs sur le compte de l'échoppe ?";
			echo "<center><input type=\"submit\" class=\"test\" value=\"Valider le transfert ?\">";
			echo "</form>";
			break;
		case "depot2":
			$req = "select lieu_compte,perso_po ";
			$req = $req . "from lieu,perso ";
			$req = $req . "where lieu_cod = $mag ";
			$req = $req . "and perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			$banque = $db->f("perso_po");
			$erreur = 0;
			if (!isset($qte))
			{
				echo "<p>Erreur ! Quantité non définie !";
				$erreur = 1;
			}
			if ($qte < 0)
			{
				echo "<p>Erreur ! Quantité négative !";
				$erreur = 1;
			}
			if ($qte > $banque)
			{
				echo "<p>Erreur ! Pas assez de brouzoufs à déposer !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				// message
				$req = "select perso_nom,nextval('seq_msg_cod') as message from perso where perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				$nom = str_replace("'","\'",$db->f("perso_nom"));
				$message = $db->f("message");
				$req = "insert into messages (msg_cod,msg_corps,msg_titre,msg_date,msg_date2) values ($message,'$nom a effectué un dépot de $qte brouzoufs','Dépot',now(),now()) ";
				$db->query($req);
				$req = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod) values ($message,$perso_cod) ";
				$db->query($req);
				$tab_lieu = $db->get_lieu($perso_cod);
				if ($tab_lieu['type_lieu'] == 11)
				{
					$req = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe = 'O'  and perso_cod != 605745 ";
				}
				if ($tab_lieu['type_lieu'] == 9)
				{
					$req = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe = 'O'  and perso_cod != 605745 ";
				}
				if ($tab_lieu['type_lieu'] == 21)
				{
					$req = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe_noir = 'O' and perso_cod != 605745 ";
				}
				$db->query($req);
				
				$req = "update lieu set lieu_compte = lieu_compte + $qte where lieu_cod = $mag ";
				$db->query($req);
				$req = "update perso set perso_po = perso_po - $qte where perso_cod = $perso_cod ";
				$db->query($req);
				echo "<p>La transaction a été effectuée.";
				
			}
			break;
		case "marge":
			$req = "select lieu_marge,lieu_prelev ";
			$req = $req . "from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			echo "<p>La marge actuelle est de " . $db->f("lieu_marge") . " %.";
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"marge2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
			echo "<p>Mettre la marge à  <input type=\"text\" name=\"qte\" value=\"" . $db->f("lieu_marge") . "\"> % ?";
			echo "<p><i>nb : vous ne pouvez pas descendre la marge en dessous de " . $db->f("lieu_prelev") . " %.</i>";
			
			echo "<center><input type=\"submit\" class=\"test\" value=\"Valider le changement ?\">";
			echo "</form>";
			break;	
		case "marge2":
			$req = "select lieu_prelev ";
			$req = $req . "from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			$prelev = $db->f("lieu_prelev");
			$erreur = 0;
			if (!isset($qte))
			{
				echo "<p>Erreur ! marge non définie !";
				$erreur = 1;
			}
			if ($qte < $prelev)
			{
				echo "<p>Erreur ! Marge inférieur à l'autorisé ($prelev %) !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$req = "update lieu set lieu_marge = $qte where lieu_cod = $mag ";
				$db->query($req);	
				echo "<p>La modification a été effectuée.";
			}
			break;
		case "statut";
			$req = "select lieu_prelev,lieu_marge ";
			$req = $req . "from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			if ($db->f("lieu_prelev") == 15)
			{
				echo "<p>Votre magasin n'est pas un refuge. Si vous souhaitez le transformer en refuge, les prélèvements de l'administration passeront automatiquement à 30%.<br>";
				if ($db->f("lieu_marge") < 30)
				{
					echo "Votre marge est insuffisante pour accomplir cette action.";
				}
				else
				{
					echo "<a href=\"gere_echoppe2.php?mag=$mag&methode=statut2&ref=o\">Passer cette échoppe en refuge ?</a>";
				}
			}
			else
			{
				echo "<p>Votre magasin est un refuge. Si vous souhaitez abandonner cette fonctionnalité, les prélèvements de l'administration passeront automatiquement à 15%.<br>";
				echo "<a href=\"gere_echoppe2.php?mag=$mag&methode=statut2&ref=n\">Abandonner le statut de refuge pour cette échoppe ?</a>";
			}
		
			break;
		case "statut2";	
			if ($ref == 'n')
			{
				$req = "update lieu set lieu_refuge = 'N',lieu_prelev = 15 where lieu_cod = $mag";
				$db->query($req);
				echo "<p>La modification a été effectuée.";
			}
			if ($ref == 'o')
			{
				$req = "update lieu set lieu_refuge = 'O',lieu_prelev = 30 where lieu_cod = $mag";
				$db->query($req);
				echo "<p>La modification a été effectuée.";
			}
			break;
		case "nom";	
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2.php\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"nom2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
			$req = "select lieu_nom,lieu_description from lieu ";
			$req = $req . "where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			
			echo "<table>";
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Nom du magasin (70 caracs maxi)</td>";
			echo "<td><input type=\"text\" name=\"nom\" size=\"50\" value=\"" . $db->f("lieu_nom") . "\"></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p>Description</td>";
			$desc = str_replace(chr(127),";",$db->f("lieu_description"));
			echo "<td><textarea name=\"desc\" rows=\"10\" cols=\"50\">" . $desc . "</textarea></td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan=\"2\"><input type=\"submit\" class=\"test\" value=\"Valider les changements\"></td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "</form>";
		
			break;
		case "nom2":
			echo "<p><strong>Aperçu : " . $desc;
			$desc = str_replace(";",chr(127),$desc);
			$req = "update lieu set lieu_nom = e'" . pg_escape_string($nom) . "', lieu_description = e'" . pg_escape_string($desc) . "' where lieu_cod = $mag ";
			$db->query($req);
			echo "<p>Les changements sont validés !";
			break;
		case "vente_adm":
			echo "<p class=\"titre\">Vendre du matériel à l'administration</p>";
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"vente_adm2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";
			$req = "select gobj_nom,gobj_cod,gobj_valeur,tobj_libelle,count(obj_cod) as qte ";
			$req = $req . "from objets,objet_generique,stock_magasin,type_objet ";
			$req = $req . "where mstock_lieu_cod = $mag ";
			$req = $req . "and mstock_obj_cod = obj_cod ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and gobj_tobj_cod = tobj_cod ";
			$req = $req . "group by gobj_nom,gobj_cod,gobj_valeur,tobj_libelle ";
			$req = $req . "order by tobj_libelle,gobj_nom ";
			$db->query($req);
			echo "<center><table>";
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Quantité</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Prix de vente</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Qte à vendre ?</strong></td>";
			echo "</tr>";
			while ($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("gobj_nom") . "</td>";
				echo "<td><p>" . $db->f("tobj_libelle") . "</td>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("qte") . "</td>";
				echo "<td><p>" . $db->f("gobj_valeur") . "</td>";
				echo "<td><input type=\"text\" name=\"obj[" . $db->f("gobj_cod") . "]\" value=\"0\"></td>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td colspan=\"5\"><center><input type=\"submit\" class=\"test\" value=\"Vendre !\"></center></td>";
			echo "</tr>";
			echo "</table></center>";
			echo "</form>";
			
			break;
		case "vente_adm2":
			$erreur = 0;
			foreach($obj as $key=>$val)
			{
				$req = "select gobj_nom,count(obj_cod) as nombre ";
				$req = $req . "from objets,objet_generique,stock_magasin ";
				$req = $req . "where gobj_cod = $key ";
				$req = $req . "and obj_gobj_cod = gobj_cod ";
				$req = $req . "and mstock_obj_cod = obj_cod ";
				$req = $req . "and mstock_lieu_cod = $mag ";
				$req = $req . "group by gobj_nom ";
				$db->query($req);
				$db->next_record();
				if ($val > $db->f("nombre"))
				{
					echo "<p>Erreur ! Vous essayez de vendre l'objet <strong>" . $db->f("gobj_nom") . "</strong> en trop grande quantité !";
					$erreur = 1;
				}
			}
			if ($erreur == 0)
			{
				$gagne = 0;
				foreach($obj as $key=>$val)
				{	
					for($cpt=0;$cpt<$val;$cpt++)
					{
						// on enlève du magasin
						$req = "select obj_cod ";
						$req = $req . "from objets,objet_generique,stock_magasin ";
						$req = $req . "where gobj_cod = $key ";
						$req = $req . "and obj_gobj_cod = gobj_cod ";
						$req = $req . "and mstock_obj_cod = obj_cod ";
						$req = $req . "and mstock_lieu_cod = $mag ";
						$req = $req . "limit 1";
						$db->query($req);
						$db->next_record();
						$objet = $db->f("obj_cod");
						$req  = "delete from stock_magasin where mstock_obj_cod = $objet ";
						$db->query($req);
						$req = "select f_del_objet($objet) ";
						$db->query($req);
						// on ajoute les sous
						$req = "select gobj_valeur from objet_generique where gobj_cod = $key ";
						$db->query($req);
						$db->next_record();
						$gagne = $gagne + $db->f("gobj_valeur");
					}
				
				}	
				$req = "update lieu set lieu_compte = lieu_compte + $gagne where lieu_cod = $mag ";
				$db->query($req);
				echo "<p>Transacstion effectuée pour $gagne brouzoufs. ";
			}
			break;
		case "achat_adm":
			echo "<p class=\"titre\">Achat de matériel à l'administration</p>";
			$req = "select lieu_compte from lieu where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			echo "<p>Vous diposez de <strong>" . $db->f("lieu_compte") . "</strong> pour acheter des objets à l'administration.";
			$req = "select gobj_cod,tobj_libelle,gobj_nom,gobj_valeur, ";
			$req = $req . "(select count(obj_cod) ";
			$req = $req . "from objets,stock_magasin ";
			$req = $req . "where obj_gobj_cod = gobj_cod ";
			$req = $req . "and mstock_obj_cod = obj_cod ";
			$req = $req . "and mstock_lieu_cod = $mag) as stock ";
			$req = $req . "from objet_generique,type_objet ";
			$req = $req . "where gobj_echoppe_stock = 'O' ";
			//$req = $req . "and gobj_deposable = 'O' ";
			//$req = $req . "and gobj_visible = 'O' ";
			$req = $req . "and gobj_tobj_cod = tobj_cod ";
			//$req = $req . "and tobj_cod in (1,2,4) ";
			$req = $req . "order by tobj_cod,gobj_nom ";
			$db->query($req);	
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"achat_adm2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";	
			echo "<center><table>";
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Valeur</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Stock</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Quantité</strong></td>";
			while($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("gobj_nom") . "</td>";
				echo "<td><p>" . $db->f("tobj_libelle") . "</td>";
				echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $db->f("gobj_valeur") . "</td>";
				echo "<td><p style=\"text-align:right;\">" . $db->f("stock") . "</td>";
				echo "<td class=\"soustitre2\"><p><input type=\"text\" name=\"obj[" . $db->f("gobj_cod") . "]\" value=\"0\"></td>";				
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td colspan=\"5\"><center><input type=\"submit\" value=\"Valider les achats !\" class=\"test\"></Center></td>";
			echo "</tr>";
			
			echo "</table></center>";
			
			
			echo "</form>";
			break;
		case "achat_adm2":
			$erreur = 0;
			$total = 0;
			foreach($obj as $key=>$val)
			{
				if ($val < 0)
				{
					echo "<p>Erreur ! Quantité négative !";
					$erreur = 1;
				}
				if ($val > 0)
				{
					$req = "select gobj_valeur ";
					$req = $req . "from objet_generique ";
					$req = $req . "where gobj_cod = $key ";
					$db->query($req);
					$db->next_record();
					$total = $total + ($db->f("gobj_valeur") * $val);
				}
			}
			$req = "select lieu_compte from lieu where lieu_cod = $mag ";
			$db->query($req);
			$db->next_record();
			if ($total > $db->f("lieu_compte"))
			{
				echo "<p>Vous n'avez pas assez de brouzoufs pour acheter ce matériel !";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				foreach($obj as $key=>$val)
				{
					if ($val > 0)
					{
						for($cpt=0;$cpt<$val;$cpt++)
						{
							// on crée l'objet
							$req = "select nextval('seq_obj_cod') as num_objet ";
							$db->query($req);
							$db->next_record();
							$num_obj = $db->f("num_objet");
							$req = "insert into objets (obj_cod,obj_gobj_cod) values ($num_obj,$key)";
							$db->query($req);
							$req = "insert into stock_magasin (mstock_obj_cod,mstock_lieu_cod) values ($num_obj,$mag) ";
							$db->query($req);
						}
					}
				}
				$req = "update lieu set lieu_compte = lieu_compte - $total where lieu_cod = $mag ";
				$db->query($req);
				echo "<p>Achat effectué pour un total de ", $total, " brouzoufs.";
				
			}	
			break;
		case "fix_prix":	
			echo "<p class=\"titre\">Fixer les tarifs</p>";
			$req = "select gobj_cod,tobj_libelle,gobj_nom,gobj_valeur, ";
			$req = $req . "(select count(obj_cod) ";
			$req = $req . "from objets,stock_magasin ";
			$req = $req . "where obj_gobj_cod = gobj_cod ";
			$req = $req . "and mstock_obj_cod = obj_cod ";
			$req = $req . "and mstock_lieu_cod = $mag) as stock, ";
			$req = $req . "f_prix_gobj($mag,gobj_cod) as valeur_actu ";
			$req = $req . "from objet_generique,type_objet ";
			$req = $req . "where gobj_echoppe = 'O' ";
			$req = $req . "and gobj_deposable = 'O' ";
			$req = $req . "and gobj_visible = 'O' ";
			$req = $req . "and gobj_tobj_cod = tobj_cod ";
			$req = $req . "and tobj_cod in (1,2) ";
			$req = $req . "order by tobj_cod,gobj_nom ";
			$db->query($req);	
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"achat_adm2\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";	
			echo "<center><table>";
			echo "<tr>";
			echo "<td class=\"soustitre2\"><p><strong>Nom</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Type</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Stock</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Valeur officielle</strong></td>";
			echo "<td class=\"soustitre2\"><p><strong>Valeur actuelle</strong></td>";
			echo "<td></td>";
			while($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\"><p>" . $db->f("gobj_nom") . "</td>";
				echo "<td><p>" . $db->f("tobj_libelle") . "</td>";
				echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $db->f("stock") . "</td>";
				echo "<td><p style=\"text-align:right;\">" . $db->f("gobj_valeur") . "</td>";
				echo "<td class=\"soustitre2\"><p style=\"text-align:right;\">" . $db->f("valeur_actu") . "</td>";
				echo "<td><a href=\"gere_echoppe2.php?mag=$mag&methode=fix_prix2&gobj=" . $db->f("gobj_cod") . "\">Modifier !</a></td>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td colspan=\"5\"><center><input type=\"submit\" value=\"Valider les achats !\" class=\"test\"></Center></td>";
			echo "</tr>";
			
			echo "</table></center>";
			break;
		case "fix_prix2":	
			$req = "select gobj_nom,gobj_valeur,f_prix_gobj($mag,gobj_cod) from objet_generique ";
			$req = $req . "where gobj_cod = $gobj ";
			$db->query($req);
			$db->next_record();
			$min = floor(($db->f("gobj_valeur") * 0.8));
			$max = floor(($db->f("gobj_valeur") * 1.2));
			echo "<p>Le tarif officiel est de " . $db->f("gobj_valeur") ." brouzoufs<br>";
			echo "<p>Vous pouvez fixer un nouveau tarif qui ne doit pas être inférieur ou supérieur à 20% du tarif officiel (entre $min et $max brouzoufs).";
			echo "<form name=\"echoppe\" method=\"post\" action=\"gere_echoppe2\">";
			echo "<input type=\"hidden\" name=\"methode\" value=\"fix_prix3\">";
			echo "<input type=\"hidden\" name=\"annul\" value=\"n\">";
			echo "<input type=\"hidden\" name=\"gobj\" value=\"$gobj\">";
			echo "<input type=\"hidden\" name=\"mag\" value=\"$mag\">";	
			echo "<p>Entrez le nouveau prix : <input type=\"text\" name=\"n_prix\"> brouzoufs<br>";
			echo "ou bien <a href=\"javascript:document.echoppe.annul.value='o';document.echoppe.submit();\">cliquez ici</a> pour utiliser le tarif officiel.";
			echo "<center><input type=\"submit\" value=\"Valider\" class=\"test\"></center>";
			echo "</form>";
			break;
		case "fix_prix3":	
			$erreur = 0;
			if ($annul == 'n')
			{
				$req = "select gobj_nom,gobj_valeur,f_prix_gobj($mag,gobj_cod) from objet_generique ";
				$req = $req . "where gobj_cod = $gobj ";
				$db->query($req);
				$db->next_record();
				$min = floor(($db->f("gobj_valeur") * 0.8));
				$max = floor(($db->f("gobj_valeur") * 1.2));
				if ($n_prix < $min)
				{
					echo "<p>Le tarif fixé est inférieur au tarif possible ($min brouzoufs).";
					$erreur = 1;
				}
				if ($n_prix > $max)
				{
					echo "<p>Le tarif fixé est supérieur au tarif possible ($max brouzoufs).";
					$erreur = 1;
				}
				if ($erreur == 0)
				{
					$req = "delete from magasin_tarif ";
					$req = $req . "where mtar_lieu_cod = $mag ";
					$req = $req . "and mtar_gobj_cod = $gobj ";
					$db->query($req);
					$req = "insert into magasin_tarif ";
					$req = $req . "(mtar_lieu_cod,mtar_gobj_cod,mtar_prix) ";
					$req = $req . "values ";
					$req = $req . "($mag,$gobj,$n_prix) ";
					$db->query($req);
					echo "<p>Le tarif a été changé !";
					
					
				}
			}
			else
			{
				$req = "delete from magasin_tarif ";
				$req = $req . "where mtar_lieu_cod = $mag ";
				$req = $req . "and mtar_gobj_cod = $gobj ";
				$db->query($req);
				echo "<p>Votre échoppe vendra maintenant cet objet au tarif officiel.";
			}
			break;	
		case "stats":
			$sens[1] = "vente (magasin vers aventurier) ";
			$sens[2] = "achat (aventurier vers magasin) ";
			$req = "select gobj_nom,mtra_sens,sum(mtra_montant) as somme,count(mtra_cod) as nombre ";
			$req = $req . "from objet_generique,objets,mag_tran ";
			$req = $req . "where mtra_lieu_cod = $mag ";
			$req = $req . "and mtra_obj_cod = obj_cod ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "group by gobj_nom,mtra_sens ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucune transaction enregistrée dans votre échoppe.";
			}
			else
			{
				?>
				<table>
				<tr>
				<td class="soustitre2"><strong>Nom</strong></td>
				<td class="soustitre2"><strong>Sens</strong></td>
				<td class="soustitre2"><strong>Montant global</strong></td>
				<td class="soustitre2"><strong>Nombre</strong></td>
				</tr>
				<?php 
				while ($db->next_record())
				{
					echo "<tr>";
					echo "<td class=\"soustitre2\">" , $db->f("gobj_nom") , "</td>";
					$idx_sens = $db->f("mtra_sens");
					echo "<td>" , $sens[$idx_sens] , "</td>";
					echo "<td class=\"soustitre2\">" , $db->f("somme") , "</td>";
					echo "<td>" , $db->f("nombre") , "</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<p style=\"text-align:center\"><a href=\"gere_echoppe2.php?mag=$mag&methode=stats2\">Voir le détail</a>";
			}
			
		
			break;
		case "stats2":
			$sens[1] = "vente (magasin vers aventurier) ";
			$sens[2] = "achat (aventurier vers magasin) ";
			$req = "select mtra_date,gobj_nom,mtra_sens,mtra_montant,perso_nom,to_char(mtra_date,'DD/MM/YYYY hh24:mi:ss') as date_tran ";
			$req = $req . "from objet_generique,objets,mag_tran,perso ";
			$req = $req . "where mtra_lieu_cod = $mag ";
			$req = $req . "and mtra_obj_cod = obj_cod ";
			$req = $req . "and obj_gobj_cod = gobj_cod ";
			$req = $req . "and mtra_perso_cod = perso_cod ";
			$req = $req . "order by mtra_date ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucune transaction enregistrée dans votre échoppe.";
			}
			else
			{
				?>
				<table>
				<tr>
				<td class="soustitre2"><strong>Objet</strong></td>
				<td class="soustitre2"><strong>Perso</strong></td>
				<td class="soustitre2"><strong>Sens</strong></td>
				<td class="soustitre2"><strong>Montant</strong></td>
				<td class="soustitre2"><strong>Date</strong></td>
				</tr>
				<?php 
				while ($db->next_record())
				{
					echo "<tr>";
					echo "<td class=\"soustitre2\">" , $db->f("gobj_nom") , "</td>";
					echo "<td>" , $db->f("perso_nom") , "</td>";
					$idx_sens = $db->f("mtra_sens");
					echo "<td class=\"soustitre2\">" , $sens[$idx_sens] , "</td>";
					echo "<td>" , $db->f("mtra_montant") , "</td>";
					echo "<td class=\"soustitre2\">" , $db->f("date_tran") , "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			
		
			break;
		case "align":
		$db->query("select lieu_alignement from lieu where lieu_cod = $mag ");
		$db->next_record();
			?>
			<form name="aligne" method="post" action="gere_echoppe2.php">
			<input type="hidden" name="methode" value="align2">
			<input type="hidden" name="mag" value="<?php  echo $mag; ?>">
			Alignement : <input type="text" name="valeur" value="<?php  echo $db->f("lieu_alignement"); ?>"><br>
			Cochez cette case pour faire de cette échoppe une zone neutre (les prix ne dépendent plus du karma) <input type="checkbox" name="neutre" value="1">
			<center><input type="submit" class="test" value="Valider les changements !">
			<?php 
			break;
		case "align2":
			$erreur = 0;
			if (!isset($valeur))
			{
				echo "<p>Erreur ! Valeur non fixée ! ";
				$erreur = 1;
			}
			if ($valeur == '')
			{
				echo "<p>Erreur ! Valeur non fixée ! ";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$req = "update lieu set lieu_alignement = $valeur where lieu_cod = $mag ";
				$db->query($req);
				if ($neutre == 1)
				{
					$req = "update lieu set lieu_neutre = 1 where lieu_cod = $mag ";
					$db->query($req);
				}	
				else
				{
					$req = "update lieu set lieu_neutre = 0 where lieu_cod = $mag ";
					$db->query($req);
					
				}
				echo "<p>Modifications effectuées !";
			}
			
			
			
			break;
	}
	echo "<p style=\"text-align:center;\"><a href=\"gere_echoppe2.php?mag=$mag\">Retour à la gestion de l'échoppe</a>";
	echo "<p style=\"text-align:center;\"><a href=\"gere_echoppe.php\">Retour à la liste des échoppes gérees</a>";
}
	


include "tab_bas.php";
?>
</body>
</html>
