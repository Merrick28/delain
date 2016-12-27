<?php 
$perso = $_REQUEST['perso'];
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
$req = "select compt_quete from compte where compt_cod = $compt_cod ";
$db->query($req);
$db->next_record();
if ($db->f("compt_quete") != 'O')
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
if ($erreur == 0)
{
	if (!isset($methode))
	{
		$methode = "debut";
	}
	switch($methode)
	{
		case "debut":
			?>
			<form name="login2" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode" value="choix_perso">
			<p>Entrez directement le numéro de perso sur lequel vous voulez intervenir : <input type="text" name="num_perso2">
			<input type="submit" value="Suite" class="test">
			</form>
			<input type="button" class="test" value="Rechercher un perso !" onClick="javascript:window.open('<?php echo $type_flux.G_URL;?>rech_perso.php','rech','width=500,height=300');">		
			<hr>
			<a href="<?php echo $PHP_SELF;?>?methode=appel&met_appel=debut">Lancer un appel ?</a><br>

			<?php 
			break;	
		case "choix_perso":
			$req = "select perso_tangible,perso_nom,pos_x,pos_y,etage_libelle,perso_pv,perso_pv_max,to_char(perso_dlt,'DD/MM/YYYY hh24:mi:ss') as dlt,perso_actif ";
			$req = $req . "from perso,perso_position,positions,etage ";
			$req = $req . "where perso_cod = $num_perso2 ";
			$req = $req . "and ppos_perso_cod = perso_cod ";
			$req = $req . "and ppos_pos_cod = pos_cod ";
			$req = $req . "and pos_etage = etage_numero ";
			$db->query($req);
			$db->next_record();
			$tangible = $db->f("perso_tangible");
			$err_actif = 0;
			if ($db->f("perso_actif") != 'O')
			{
				$err_actif = 1;
				switch($db->f("perso_actif"))
				{
					case "N":
						echo "Ce perso est <b>Inactif !</b>. Vous ne pouvez pas intervenir dessus !";
						break;
					case "H":
						echo "Ce perso est <b>en hibernation !</b>. Vous ne pouvez pas intervenir dessus !";
						break;
				}
			}
			else
			{
				echo "<p><b>" , $db->f("perso_nom") , "</b> se trouve en " , $db->f("pos_x") , ", " , $db->f("pos_y") , ", " , $db->f("etage_libelle") , ".<br>";
				echo "Sa dlt est à <b>" , $db->f("dlt") , ".</br>";
				echo "Il est à " , $db->f("perso_pv") , "/" , $db->f("perso_pv_max") , " PV.";
				if ($db->is_locked($num_perso2))
				{
					echo "<p><b>Ce perso est locké en combat !</b>";
				}
				echo "<p class=\"titre\">Actions possibles : </p>";
				echo "<p>";
				echo "<a href=\"" , $PHP_SELF , "?methode=depl&met_depl=debut&num_perso2=" , $num_perso2 , "\">Déplacer le perso ?</a><br>";
				echo "<a href=\"" , $PHP_SELF , "?methode=dlt&num_perso2=" , $num_perso2 , "\">Initialiser sa DLT à l'heure actuelle ?</a><br>";
				echo "<a href=\"" , $PHP_SELF , "?methode=objet&met_obj=debut&num_perso2=" , $num_perso2 , "\">Créer un nouvel objet de quête dans son inventaire ?</a><br>";
				echo "<a href=\"" , $PHP_SELF , "?methode=objet_ex&met_obj=debut&num_perso2=" , $num_perso2 , "\">Créer un objet de quête (déjà existant) dans son inventaire ?</a><br>";
				echo "<a href=\"" , $PHP_SELF , "?methode=objet_nq&met_obj=debut&num_perso2=" , $num_perso2 , "\">Créer un objet (hors quête) dans son inventaire ?</a><br>";
				if ($tangible == 'O')
				{
					echo "<a href=\"" , $PHP_SELF , "?methode=palpable&t=N&num_perso2=" , $num_perso2 , "\">Rendre ce perso impalpable ?</a><br>";
				}
				else
				{
					echo "<a href=\"" , $PHP_SELF , "?methode=palpable&t=O&num_perso2=" , $num_perso2 , "\">Rendre ce perso palpable ?</a><br>";
				}
				
				
			}
			break;
		case "depl":
			switch($met_depl)
			{
				case "debut":
					if ($db->is_locked($num_perso2))
					{
						echo "<p><b>Ce perso est locké en combat !</b> Son déplacement va rompre tous les locks de combat.";
					}
					?>
					<form name="login2" method="post" action="<?php echo $PHP_SELF;?>">
					<input type="hidden" name="methode" value="depl">
					<input type="hidden" name="met_depl" value="dest">
					<input type="hidden" name="num_perso2" value="<?php echo $num_perso2;?>">
					<p>Entrez la position à laquelle vous souhaitez déplacer ce perso :<br>
					X : <input type="text" name="pos_x" maxlength="5" size="5"> - 
					Y : <input type="text" name="pos_y" maxlength="5" size="5"> - 
					Etage : <select name="etage">
					<?php 
						$req = "select etage_numero,etage_libelle from etage order by etage_numero desc ";
						$db->query($req);
						while($db->next_record())
						{
							echo "<option value=\"" , $db->f("etage_numero") , "\">" , $db->f("etage_libelle") , "</option>";
						}
					?>
					</select><br>
					<center><input type="submit" class="test" value="Déplacer !"></form>				
					<?php 
					break;
				case "dest":
					$err_depl = 0;
					$req = "select pos_cod from positions where pos_x = $pos_x and pos_y = $pos_y and pos_etage = $etage ";
					$db->query($req);
					if ($db->nf() == 0)
					{
						echo "<p>Aucune position trouvée à ces coordonnées.<br>";
						echo "<a href=\"" , $PHP_SELF , "?methode=depl&met_depl=debut&num_perso2=" , $num_perso2 , "\">Retour au choix des coordonnées ?</a><br>";
						$err_depl = 1;
					}
					$db->next_record();
					$pos_cod = $db->f("pos_cod");
					$req = "select mur_pos_cod from murs where mur_pos_cod = $pos_cod ";
					$db->query($req);
					if ($db->nf() != 0)
					{
						echo "<p>impossible de déplacer le perso : un mur en destination.<br>";
						echo "<a href=\"" , $PHP_SELF , "?methode=depl&met_depl=debut&num_perso2=" , $num_perso2 , "\">Retour au choix des coordonnées ?</a><br>";
						$err_depl = 1;
					}
					if ($err_depl == 0)
					{
						// insertion dun évènement
						$texte_evt = "[perso_cod1] a été déplacé par un admin quête.";
						$req = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible) ";
						$req = $req  . "values(43,now(),$num_perso2,'$texte_evt','N','N') ";
						$db->query($req);
						// effacement des locks
						$req = "delete from lock_combat where lock_cible = $num_perso2 ";
						$db->query($req);
						$req = "delete from lock_combat where lock_attaquant = $num_perso2 ";
						$db->query($req);
						// déplacement
						$req = "update perso_position set ppos_pos_cod = $pos_cod where ppos_perso_cod = $num_perso2 ";
						$db->query($req);
						echo "<p>Le perso a bien été déplacé !";
					}
					break;
			}
			break;
		case "dlt":
			// insertion dun évènement
			$texte_evt = "La DLT de [perso_cod1] a été actualisée par un admin quête.";
			$req = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible) ";
			$req = $req  . "values(43,now(),$num_perso2,'$texte_evt','N','N') ";
			$db->query($req);
			$req = "update perso set perso_dlt = now() where perso_cod = $num_perso2 ";
			$db->query($req);
			echo "<p>La dlt de ce joueur est prête à être activée.";
			break;
		case "objet":
			switch($met_obj)
			{
				case "debut":
					?>
					<p><b>Attention ! </b>Cette procédure n'a pour but que de créer de nouveaux objets (première apparition dans le jeu) dans l'inventaire d'un perso.<br>
					Si vous souhaitez créer un objet déjà existant, <a href="<?php echo $PHP_SELF;?>?methode=objet_ex&met_obj=debut&num_perso=<?php echo $num_perso2;?>">merci de cliquer ici !</a>
					<form name="login2" method="post" action="<?php echo $PHP_SELF;?>">
					<input type="hidden" name="methode" value="objet">
					<input type="hidden" name="met_obj" value="etape2">
					<input type="hidden" name="num_perso" value="<?php echo $num_perso2;?>">
					<table>
					<tr>
					<td class="soustitre2"><p>Nom de l'objet (une fois identifié) :</td>
					<td><input type="text" name="nom_objet" size="50"></td>
					</tr>
					<tr>
					<td class="soustitre2"><p>Nom de l'objet (pas encore identifié) :</td>
					<td><input type="text" name="nom_objet_non_iden" size="50"></td>
					</tr>
					<tr>
					<td class="soustitre2"><p>Description :</td>
					<td><textarea name="desc" rows="10" cols="30"></textarea></td>
					</tr>
					<tr>
					<td class="soustitre2"><p>Poids de l'objet :</td>
					<td><input type="text" name="poids_objet"></td>
					</tr>
					</table>
						<center><input type="submit" class="test" value="Créer !"></center></form>			
					<?php 
					break;
				case "etape2":
					// recherche du num objet generique
					$req = "select nextval('seq_gobj_cod') as gobj";
					$db->query($req);
					$db->next_record();
					$gobj_cod = $db->f("gobj");
					$nom_objet = str_replace("'","\'",$nom_objet);
					$nom_objet_non_iden = str_replace("'","\'",$nom_objet_non_iden);
					$desc = str_replace("'","\'",$desc);
					// création dans les objets génériques
					$req = "insert into objet_generique (gobj_cod,gobj_nom,gobj_nom_generique,gobj_tobj_cod,gobj_valeur,gobj_poids,gobj_description,gobj_deposable,gobj_visible,gobj_echoppe) ";
					$req = $req . "values ($gobj_cod,'$nom_objet','$nom_objet_non_iden',11,0,$poids_objet,'$desc','O','O','N')";	
					$db->query($req);
					// insertion dun évènement
					$texte_evt = "Un admin quête a créé un objet dans l\'inventaire de [perso_cod1].";
					$req = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible) ";
					$req = $req  . "values(43,now(),$num_perso2,'$texte_evt','N','N') ";
					$db->query($req);		
					// création
					$req = "select cree_objet_perso_nombre($gobj_cod,$num_perso2,1) ";
					$db->query($req);
					echo "<p>L'objet a bien été créé !";
					break;
			}
			break;
		case "objet_ex":
			switch($met_obj)
			{
				case "debut":
					?>
					<p><b>Attention ! </b>Cette procédure n'a pour but que de créer des objets existants (pas encore créés dans le jeu) dans l'inventaire d'un perso.<br>
					Si vous souhaitez créer un nouvel objet, <a href="<?php echo $PHP_SELF;?>?methode=objet&met_obj=debut&num_perso=<?php echo $num_perso2;?>">merci de cliquer ici !</a><br>
					<form name="login2" method="post" action="<?php echo $PHP_SELF;?>">
					<input type="hidden" name="methode" value="objet_ex">
					<input type="hidden" name="met_obj" value="etape2">
					<input type="hidden" name="num_perso" value="<?php echo $num_perso2;?>">
					<p>Objet à créer : <select name="gobj">
					<?php 
						$req = "select gobj_nom,gobj_cod from objet_generique where gobj_tobj_cod = 11 order by gobj_nom ";
						$db->query($req);
						while($db->next_record())
						{
							echo "<option value=\"" , $db->f("gobj_cod") , "\">" , $db->f("gobj_nom") , "</option>";
						}
					?></select><br>
					<center><input type="submit" class="test" value="Créer !"></center></form>			
					<?php 
					break;
				case "etape2":
					// insertion dun évènement
					$texte_evt = "Un admin quête a créé un objet dans l\'inventaire de [perso_cod1].";
					$req = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible) ";
					$req = $req  . "values(43,now(),$num_perso2,'$texte_evt','N','N') ";
					$db->query($req);		
					// création
					$req = "select cree_objet_perso_nombre($gobj,$num_perso2,1) ";
					$db->query($req);
					echo "<p>L'objet a bien été créé !";
					break;
			}
			break;
		case "objet_nq":
			switch($met_obj)
			{
				case "debut":
					?>
					<p><b>Attention ! </b>Cette procédure n'a pour but que de créer des objets existants (pas encore créés dans le jeu) dans l'inventaire d'un perso.<br>
					Si vous souhaitez créer un nouvel objet, <a href="<?php echo $PHP_SELF;?>?methode=objet&met_obj=debut&num_perso=<?php echo $num_perso2;?>">merci de cliquer ici !</a><br>
					<form name="login2" method="post" action="<?php echo $PHP_SELF;?>">
					<input type="hidden" name="methode" value="objet_ex">
					<input type="hidden" name="met_obj" value="etape2">
					<input type="hidden" name="num_perso" value="<?php echo $num_perso2;?>">
					<p>Objet à créer : <select name="gobj">
					<?php 
						$req = "select gobj_nom,gobj_cod,tobj_libelle,tobj_cod from objet_generique,type_objet where gobj_tobj_cod != 11 and gobj_tobj_cod = tobj_cod order by tobj_cod,gobj_nom ";
						$db->query($req);
						while($db->next_record())
						{
							echo "<option value=\"" , $db->f("gobj_cod") , "\">" , $db->f("gobj_nom") , " - (" , $db->f("tobj_libelle") , ")</option>";
						}
					?></select><br>
					<center><input type="submit" class="test" value="Créer !"></center></form>			
					<?php 
					break;
				case "etape2":
					// insertion dun évènement
					$texte_evt = "Un admin quête a créé un objet dans l\'inventaire de [perso_cod1].";
					$req = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible) ";
					$req = $req  . "values(43,now(),$num_perso2,'$texte_evt','N','N') ";
					$db->query($req);		
					// création
					$req = "select cree_objet_perso_nombre($gobj,$num_perso2,1) ";
					$db->query($req);
					echo "<p>L'objet a bien été créé !";
					break;
			}
			break;
		case "appel":
			switch($met_appel)
			{
				case "debut":
					?>
					<form name="login2" method="post" action="<?php echo $PHP_SELF;?>">
					<input type="hidden" name="methode" value="appel">
					<input type="hidden" name="met_appel" value="dest">
					<input type="hidden" name="num_perso" value="<?php echo $num_perso2;?>">
					<table>
					<tr>
					<td class="soustitre2">
					<p>Entrez la position à partir de laquelle l'appel sera lancé :</td>
					<td>
					X : <input type="text" name="pos_x" maxlength="5" size="5"> - 
					Y : <input type="text" name="pos_y" maxlength="5" size="5"> - 
					Etage : <select name="etage">
					<?php 
						$req = "select etage_numero,etage_libelle from etage order by etage_numero desc ";
						$db->query($req);
						while($db->next_record())
						{
							echo "<option value=\"" , $db->f("etage_numero") , "\">" , $db->f("etage_libelle") , "</option>";
						}
					?>
					</select></td>
					</tr>
					<tr>
					<td class="soustitre2">
					<p>Entrez la distance :</td>
					<td>
						<select name="distance">
						<?php 
							for($i=0;$i<=5;$i++)
							{
								echo "<option value=\"" , $i, "\">" , $i , "</option>";
							}
						?>
						</select>
					</td>
					</tr>
					<tr>
					<td class="soustitre2">
					<p>Entrez le Numéro de perso lançant l'appel :</td>
					<td><input type="text" name="perso" size="50"></td>
					</tr>
					<tr>
					<td class="soustitre2">
					<p>Entrez le titre :</td>
					<td><input type="text" name="titre" size="50"></td>
					</tr>
					<tr>
					<td class="soustitre2">
					<p>Entrez le texte :</td>
					<td><textarea name="corps" cols="50" rows="10"></textarea></td>
					</tr></table>
					
					<center><input type="submit" class="test" value="Lancer l'appel !"></form>			
					<?php 
					break;
				case "dest":
					$err_depl = 0;
					$req = "select pos_cod,pos_x,pos_y,pos_etage 
											from positions 
											where pos_x = $pos_x 
											and pos_y = $pos_y 
											and pos_etage = $etage ";
					$db->query($req);
					if ($db->nf() == 0)
					{
						echo "<p>Aucune position trouvée à ces coordonnées.<br>";
						echo "<a href=\"" , $PHP_SELF , "?methode=appel&met_appel=debut\">Retour au choix des coordonnées ?</a><br>";
						$err_depl = 1;
					}
					if ($err_depl == 0)
					{
						$titre = htmlspecialchars($titre);
						$titre = str_replace(";",chr(127),$titre);
						$titre = pg_escape_string($titre);
						// numéro du message
						$req_msg_cod = "select nextval('seq_msg_cod') as numero";
						$db->query($req_msg_cod);
						$db->next_record();
						$num_mes = $db->f("numero");
						// encodage du texte
						$corps = htmlspecialchars($corps);
						$corps = nl2br($corps);
						$corps = str_replace(";",chr(127),$corps);
						$corps = pg_escape_string($corps);
						// enregistrement du message
						$req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps) ";
						$req_ins_mes = $req_ins_mes . "values ($num_mes,now(),now(),e'$titre',e'$corps') ";
						$db->query($req_ins_mes);
						// enregistrement de l'expéditeur
						$req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
						$req_ins_exp = $req_ins_exp . "values (nextval('seq_emsg_cod'),$num_mes,$perso_cible,'N')";
						$db->query($req_ins_exp);
						// enregistrement des destinataires
						// recherche de la position
						$req_pos = "select pos_etage,pos_x,pos_y 
														from positions 
														where pos_x = $pos_x 
														and pos_y = $pos_y 
														and pos_etage = $etage";
						$db->query($req_pos);
						$db->next_record();
						$pos_actuelle = $db->f("ppos_pos_cod");
						$v_x = $db->f("pos_x");
						$v_y = $db->f("pos_y");
						$etage = $db->f("pos_etage");
						// rechreche des dest
						$req_vue = "select perso_cod,perso_type_perso,perso_nom from perso, perso_position, positions
														where pos_x >= ($pos_x - $distance) and pos_x <= ($pos_x + $distance)
														and pos_y >= ($pos_y - $distance) and pos_y <= ($pos_y + $distance)
														and ppos_perso_cod = perso_cod
														and perso_actif = 'O'
														and perso_type_perso = 1
														and ppos_pos_cod = pos_cod
														and pos_etage = $etage ";
						$db->query($req_vue);
						$db2 = new base_delain;
						while($db->next_record())
						{
							$req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) ";
							$req_ins_dest = $req_ins_dest . "values (nextval('seq_dmsg_cod'),$num_mes, " . $db->f("perso_cod") . ",'N','N')";
							$db2->query($req_ins_dest);
							$liste_expedie = $liste_expedie . $db->f("perso_nom") . ",";
						}
						echo "<p>Votre message a été envoyé à toutes les personnes présentes à $volume de distance de vous.";
					}
				
				
				
					break;
		
			}
			break;
		case "palpable":
			switch ($t)
			{
				case "O":
					$texte_evt = "Un admin quête a rendu [perso_cod1] palpable.";
					$req = "update perso set perso_tangible = 'O',perso_nb_tour_intangible = 0 where perso_cod = $num_perso2 ";
					break;	
				case "N":
					$req = "update perso set perso_tangible = 'N',perso_nb_tour_intangible = 4 where perso_cod = $num_perso2 ";
					$texte_evt = "Un admin quête a rendu [perso_cod1] impalpable.";
					break;
			}
			echo "<p>Opération effectuée !";
			$db->query($req);
			$req = "insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible) ";
			$req = $req  . "values(43,now(),$num_perso2,'$texte_evt','N','N') ";
			$db->query($req);		
			break;
	}
}
echo "<p style=\"text-align:center;\"><a href=\"" , $PHP_SELF , "\">Retour au début</a>";

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
