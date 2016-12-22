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
if (!$db->is_revolution($num_guilde))
{
	$req = "delete from guilde_perso where pguilde_guilde_cod = $num_guilde and pguilde_perso_cod = $vperso ";
	$res = pg_exec($dbconnect,$req);
	if (!$res)
	{
		echo("<p>Une erreur est survenue !");
	}
	else
	{
		$texte = "Votre demande d\'admission dans une guilde a été rejetée par l\'administrateur de la guilde.<br />";
		$titre = "Demande d\'admission dans une guilde.";
		$req_num_mes = "select nextval('seq_msg_cod')";
		$res_num_mes = pg_exec($dbconnect,$req_num_mes);
		$tab_num_mes = pg_fetch_array($res_num_mes,0);
		$num_mes = $tab_num_mes[0];
		$req_mes = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) ";
		$req_mes = $req_mes . "values ($num_mes, now(), '$titre', '$texte', now()) ";
		$res_mes = pg_exec($dbconnect,$req_mes);
		// on renseigne l'expéditeur
		$req2 = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
		$req2 = $req2 . "values ($num_mes,1,'N') ";
		$res2 = pg_exec($dbconnect,$req2);
		$req_dest = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes,$vperso,'N','N') ";
		$res_dest = pg_exec($dbconnect,$req_dest);
		echo("<p>Le joueur a été supprimé de votre guilde.");
		echo("<p><a href=\"admin_guilde.php\">Retourner à l'administration de la guilde</a></p>");
	}
}
else
{
		echo "<p>Vous ne pouvez pas intervenir dans la gestion de la guilde pendant une révolution !";
}
$close=pg_close($dbconnect);
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
