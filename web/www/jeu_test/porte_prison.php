<?php 
if(!DEFINED("APPEL"))
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
	if ($tab_lieu['lieu_cod'] != 2139)
	{
		$erreur = 1;
		echo("<p>Erreur ! Vous n'êtes pas sur un escalier !!!");
	}
}

// on cherche le lieu cod
$req = "select lpos_lieu_cod from lieu_position where lpos_pos_cod =  ";
$req = $req . "(select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod) ";
$db->query($req);
$db->next_record();
$lieu = $db->f("lpos_lieu_cod");

// on active pour le retour
$req = "select pge_perso_cod from perso_grand_escalier where pge_perso_cod = $perso_cod ";
$req = $req . "and pge_lieu_cod = $lieu ";
$db->query($req);
if ($db->nf() == 0)
{
	$erreur = 1;
	if (!isset($methode))
	{
		$methode = "debut";
	}
	switch($methode)
	{
		case "debut":	
			echo "<p>La porte vous est fermée pour le moment. Etant donné son épaisseur, et les nombreux systèmes de verouillages que vous y voyez, inutile d'essayer de la forcer.<br>";
			echo "<p>Vous pouvez essayer <a href=\"" , $PHP_SELF , "?methode=appeler\">d'appeler le geolier</a> ou bien <a href=\"" , $PHP_SELF , "?methode=corrompre\">de corromopre celui-ci.</a>";
			break;
		case "appeler":
			?>
			<p>Vous vous apprêtez à héler le geolier au moment où celui-ci passe.<br>
			<p>Quel message souhaitez vous lui envoyer ?<br>
			<form name="appeler" method="post" action="<?php  echo $PHP_SELF; ?>">
			<input type="hidden" name="methode" value="appel2">	
			<textarea name="corps" cols="40" rows="10"></textarea><br>
			<center><input class="test" type="submit" value="Envoyer !"></center>
			</form>
			<?php 
			break;
		case "appel2":
			// numéro du message
			$req_msg_cod = "select nextval('seq_msg_cod') as numero";
			$db->query($req_msg_cod);
			$db->next_record();
			$num_mes = $db->f("numero");
			// encodage du texte
			$corps = htmlspecialchars($corps);
			$corps = nl2br($corps);
			$titre = "[Appel de prisonnier]";
			$corps = str_replace(";",chr(127),$corps);
			// message
			$req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps) ";
			$req_ins_mes = $req_ins_mes . "values ($num_mes,now(),now(),'$titre','$corps') ";
			$db->query($req_ins_mes);
			// expéditeur
			$req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
			$req_ins_exp = $req_ins_exp . "values (nextval('seq_emsg_cod'),$num_mes,$perso_cod,'N')";
			$db->query($req_ins_exp);
			// destinataires
			$req = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) ";
			$req = $req . "select $num_mes, perso_cod, 'N', 'N' ";
			$req = $req . "from perso,guilde_perso ";
			$req = $req . "where perso_actif = 'O' ";
			$req = $req . "and pguilde_perso_cod = perso_cod ";
			$req = $req . "and pguilde_guilde_cod = 49 ";
			$req = $req . "and pguilde_valide = 'O' ";
			$req = $req . "and pguilde_rang_cod = 16 ";
			$db->query($req);
			echo "<p>Votre message a bien été envoyé. Le geolier en prendra connaissance dès que possible.";
			break;
		case "corrompre":
			$req = "select perso_po from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			$or = $db->f("perso_po");
			?>
			<p>Vous vous apprêtez à donner au geolier quelques brouzoufs afin de le corrompre.<br>
			<p>Vous avez actuellement <b><?php echo $or; ?></b> brouzoufs disponibles.
			<p>Quelle quantité souhaitez vous lui donner ?<br>
			<form name="appeler" method="post" action="<?php  echo $PHP_SELF; ?>">
			<input type="hidden" name="methode" value="corrompre2">	
			<input type="text" name="or" value="0"><br>
			<center><input class="test" type="submit" value="Envoyer !"></center>
			</form>
			<?php 
			break;
		case "corrompre2":
			$req = "select corrompre($perso_cod,$or) as resultat ";
			$db->query($req);
			$db->next_record();
			echo "<p>" , $db->f("resultat");
			break;
	}
	
}
else
{
	$tab_lieu = $db->get_lieu($perso_cod);
	$tab_lieu = $db->get_lieu($perso_cod);
	$nom_lieu = $tab_lieu['nom'];
	$desc_lieu = $tab_lieu['description'];
	echo("<p><b>$nom_lieu</b><br>$desc_lieu ");
	echo("<p><a href=\"valide_porte_prison.php\">Sortir de cette prison ? (4 PA)</a></p>");
}

?>
