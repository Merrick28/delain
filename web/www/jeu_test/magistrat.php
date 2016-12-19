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
if ($db->is_milice($perso_cod) == 0)
{
	echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
	$erreur = 1;
}
$req = "select pguilde_rang_cod from guilde_perso where pguilde_perso_cod = $perso_cod and pguilde_rang_cod = 3 ";
$db->query($req);
if ($db->nf() == 0)
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
			echo "<p><a href=\"" , $PHP_SELF , "?methode=voir\">Voir les peines en cours ?</a><br>";
			echo "<p><a href=\"" , $PHP_SELF , "?methode=voirf\">Voir les peines faites ?</a><br>";
			echo "<p><a href=\"" , $PHP_SELF , "?methode=ajout\">Ajouter une peine ?</a><br>";
			break;
		case "voir":
			echo "<p class=\"titre\">Peines existantes </p>";
			$req = "select peine_cod,acc.perso_cod as c_acc,acc.perso_nom as n_acc,mag.perso_cod as c_mag,mag.perso_nom as n_mag,peine_type,peine_duree,peine_faite,to_char(peine_date,'DD/MM/YYYY hh24:mi:ss') as date_peine ";
			$req = $req . "from peine,perso acc,perso mag ";
			$req = $req . "where peine_magistrat = mag.perso_cod ";
			$req = $req . "and peine_perso_cod = acc.perso_cod ";
			$req = $req . "and peine_faite < 2 ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucune peine non effectuée en cours.";
			}
			else
			{
				$etat[0] = "Non effectuée";
				$etat[1] = "En cours";
				$peine[0] = "Peine de mort";
				$peine[1] = "Emprisonnement limité";
				$peine[2] = "Emprisonnement à pertpétuité";
				?>
				<table>	
				<tr>
					<td class="soustitre2"><b>Dossier</b></td>
					<td class="soustitre2"><b>Accusé</b></td>
					<td class="soustitre2"><b>Peine</b></td>
					<td class="soustitre2"><b>Validée par</b></td>
					<td class="soustitre2"><b>Date de peine</b></td>
					<td class="soustitre2"><b>Etat de la peine</b></td>
					<td></td>
				</tr>
				<?php 
				while ($db->next_record())
				{
					$v_peine = $db->f("peine_type");
					$v_faite = $db->f("peine_faite");
					echo "<tr>";
					echo "<td class=\"soustitre2\">" , $db->f("peine_cod") , "</td>";
					echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=" , $db->f("c_acc") , "\"><b>" , $db->f("n_acc") , "</b></td>";
					echo "<td>$peine[$v_peine]</td>";
					echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=" , $db->f("c_mag") , "\"><b>" , $db->f("n_mag") , "</b></td>";
					echo "<td>" , $db->f("date_peine") , "</td>";
					echo "<td>$etat[$v_faite]</td>";
					echo "<td><a href=\"" , $PHP_SELF , "?methode=suppr&peine=" , $db->f("peine_cod") , "&perso=" , $db->f("c_acc") , "\">Retirer la peine ?</a></td>";
					echo "</tr>";
				}

				?>
				</table>
				<?php 
			}
			break;
		case "voirf":
			echo "<p class=\"titre\">Peines existantes </p>";
			$req = "select peine_cod,to_char(peine_dexec,'DD/MM/YYYY') as dexec,acc.perso_cod as c_acc,acc.perso_nom as n_acc,mag.perso_cod as c_mag,mag.perso_nom as n_mag,peine_type,peine_duree,peine_faite,to_char(peine_date,'DD/MM/YYYY hh24:mi:ss') as date_peine ";
			$req = $req . "from peine,perso acc,perso mag ";
			$req = $req . "where peine_magistrat = mag.perso_cod ";
			$req = $req . "and peine_perso_cod = acc.perso_cod ";
			$req = $req . "and peine_faite = 2 ";
			$db->query($req);
			if ($db->nf() == 0)
			{
				echo "<p>Aucune peine effectuée ";
			}
			else
			{
				$etat[0] = "Non effectuée";
				$etat[1] = "En cours";
				$peine[0] = "Peine de mort";
				$peine[1] = "Emprisonnement limité";
				$peine[2] = "Emprisonnement à pertpétuité";
				?>
				<table>	
				<tr>
					<td class="soustitre2"><b>Dossier</b></td>
					<td class="soustitre2"><b>Accusé</b></td>
					<td class="soustitre2"><b>Peine</b></td>
					<td class="soustitre2"><b>Validée par</b></td>
					<td class="soustitre2"><b>Date de peine</b></td>
					<td class="soustitre2"><b>Etat de la peine</b></td>
					<td class="soustitre2"><b>Date d'éxécution</b></td>
					<td></td>
				</tr>
				<?php 
				while ($db->next_record())
				{
					$v_peine = $db->f("peine_type");
					$v_faite = $db->f("peine_faite");
					echo "<tr>";
					echo "<td class=\"soustitre2\">" , $db->f("peine_cod") , "</td>";
					echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=" , $db->f("c_acc") , "\"><b>" , $db->f("n_acc") , "</b></td>";
					echo "<td>$peine[$v_peine]</td>";
					echo "<td class=\"soustitre2\"><a href=\"visu_desc_perso.php?visu=" , $db->f("c_mag") , "\"><b>" , $db->f("n_mag") , "</b></td>";
					echo "<td>" , $db->f("date_peine") , "</td>";
					echo "<td>$etat[$v_faite]</td>";
					echo "<td>" , $db->f("dexec") , "</td>";
					echo "<td><a href=\"" , $PHP_SELF , "?methode=suppr&peine=" , $db->f("peine_cod") , "&perso=" , $db->f("c_acc") , "\">Retirer la peine ?</a></td>";
					echo "</tr>";
				}

				?>
				</table>
				<?php 
			}
			break;
		case "ajout":
			?>
			<p class="titre">Ajout d'une peine</p>
			<form name="ajout" method="post" action="<?php echo $PHP_SELF;?>">
			<input type="hidden" name="methode" value="ajout2">
			<p>Etape 1 : choisissez le nom de l'heureux élu :
			<input type="text" name="nom">
			<input type="submit" class="test" value="Rechercher !">
			</form>
			<?php 
			break;
		case "ajout2":
			$erreur = 0;
			$req = "select f_cherche_perso('$nom') as resultat ";
			$db->query($req);
			$db->next_record();
			if ($db->f("resultat") == -1)
			{
				echo "<p>Erreur ! Perso non trouvé !";
				$erreur = 1;
			}
			$perso = $db->f("resultat");
			$req = "select peine_cod from peine where peine_perso_cod = $perso and peine_faite < 2";
			$db->query($req);
			if ($db->nf() != 0)
			{
				echo "<p>Erreur ! Le perso ciblé est déjà sous le coup d'une peine !<br>
					Si vous voulez rajouter une peine à ce perso, vous devez supprimer la peine existante.";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				?>
				<p class="titre">Ajout d'une peine </p>
				<form name="ajout" method="post" action="<?php echo $PHP_SELF;?>">
				<input type="hidden" name="methode" value="ajout3">
				<input type="hidden" name="perso" value="<?php echo $perso;?>">
				<p>Etape 2 : choisissez un type de peine :
				<select name="type">
					<option value="0">Peine de mort</option>
					<option value="1">Emprisonnement limité</option>
					<option value="2">Emprisonnement à perpétuité</option>
				</select>
				ainsi qu'une durée éventuelle (en heures) : 
				<input type="text" name="duree" value="0">
				<input type="submit" class="test" value="Valider !">
				</form>
				<?php 
			}
			break;
		case "ajout3":
			$erreur = 0;
			if (($type == 1) && ($duree == ""))
			{
				echo "<p>Erreur ! Si vous choisissez un emprisonnement limité, vous devez mettre une durée !";
				echo "<a href=\"" , $PHP_SELF , "?methode=ajout2&perso=$perso\">Retour</a><br>";
				$erreur = 1;
			}
			if ($erreur == 0)
			{
				$req = "insert into peine (peine_magistrat,peine_perso_cod,peine_type,peine_duree) ";
				$req = $req . "values ($perso_cod,$perso,$type,$duree) ";
				$db->query($req);
				echo "<p>La peine a bien été enregistrée.";	
				$req = "select peine_cod,peine_type,perso_nom from peine,perso where peine_perso_cod = $perso ";
				$req = $req . "and perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				$peine[0] = "Peine de mort";
				$peine[1] = "Emprisonnement limité";
				$peine[2] = "Emprisonnement à pertpétuité";
				$titre = "Condamnation.";
				$v_peine = $db->f("peine_type");
				$v_faite = $db->f("peine_faite");
				$texte = "Le joueur " . $db->f("perso_nom") .", en tant que Magistrat de la Milice d'Hormandre III, a émis une condamnation contre vous.<br />";
				$texte = $texte . "La condamnation est : <b>" . $peine[$v_peine] . "</b> et est enrgistrée sous le dossier <b>". $db->f("peine_cod") . "</b>.";
				$texte = str_replace("'","\'",$texte);
				$req_num_mes = "select nextval('seq_msg_cod') as num_mes";
				$db->query($req_num_mes);
				$db->next_record();
				$num_mes = $db->f("num_mes");
				$req_mes = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) ";
				$req_mes = $req_mes . "values ($num_mes, now(), '$titre', '$texte', now()) ";
				$db->query($req_mes);
				// on renseigne l'expéditeur
				$req = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
				$req = $req . "values ($num_mes,$perso_cod,'N') ";
				$db->query($req);
				$req_dest = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes,$perso,'N','N') ";
				$db->query($req_dest);
			}
			break;
		case "suppr":
			$req = "delete from peine where peine_cod = $peine ";
			$db->query($req);
			echo "<p>La peine a été retirée.";
			$titre = "Suppression de peine.";
			$req = "select perso_nom from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			$texte = "Le joueur " . $db->f("perso_nom") .", en tant que Magistrat de la Milice d'Hormandre III, a levé la peine qui était émise contre vous.<br />";
			$texte = str_replace("'","\'",$texte);
			$req_num_mes = "select nextval('seq_msg_cod') as num_mes";
			$db->query($req_num_mes);
			$db->next_record();
			$num_mes = $db->f("num_mes");
			$req_mes = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) ";
			$req_mes = $req_mes . "values ($num_mes, now(), '$titre', '$texte', now()) ";
			$db->query($req_mes);
			// on renseigne l'expéditeur
			$req = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
			$req = $req . "values ($num_mes,$perso_cod,'N') ";
			$db->query($req);
			$req_dest = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes,$perso,'N','N') ";
			$db->query($req_dest);
			break;
			
			
		
	}
	echo "<hr><a href=\"" , $PHP_SELF , "\">Retour à la gestion des peines</a><br>";
	echo "<a href=\"milice.php\">Retour à la page milice</a><br>";
	
	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
