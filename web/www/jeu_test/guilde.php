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
ob_start();

// on recherche si il y a guilde
$req_guilde = "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod,rguilde_admin,pguilde_message from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_rang_cod = pguilde_rang_cod ";
$req_guilde = $req_guilde . "and pguilde_valide = 'O' ";
$db->query($req_guilde);
$nb_guilde = $db->nf();
if ($nb_guilde == 0)
{
	$req_guilde = "select guilde_cod,guilde_nom from guilde,guilde_perso ";
	$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
	$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
	$req_guilde = $req_guilde . "and pguilde_valide = 'N' ";
	$db->query($req_guilde);
	$nb_guilde = $db->nf();
	if ($nb_guilde == 0)
		{
	?>
	<p>Vous n'êtes affilié à aucune guilde !<br />
	<a href="voir_toutes_guildes.php">Rejoindre une guilde ?</a><br />
	</p>
	<?php 
	} else {
		$db->next_record();
	?>
	<p>Vous n'êtes affilié à aucune guilde !<br />
	Vous postulez actuellement à la guilde: <strong><?php  echo $db->f("guilde_nom") ?></strong><br />
	<a href="voir_toutes_guildes.php">Rejoindre une autre guilde ?</a><br />
	</p>
	<?php 	
	}
}
else
{
	$db->next_record();
	printf("<p>Vous êtes affilié à la guilde <strong>%s</strong> en tant que <strong>%s</strong>",$db->f("guilde_nom"),$db->f("rguilde_libelle_rang"));
	if ($db->f("rguilde_admin") == 'O') //admin !!!
	{
		?>
		<p><a href="admin_guilde.php">Administrer la guilde</a><br />
		<?php 
	}
	else
	{
		?>
		<form name="visu_guilde" method="post" action="visu_guilde.php">
		<?php 
		printf("<input type=\"hidden\" name=\"num_guilde\" value=\"%s\">",$db->f("guilde_cod"));
		?>
		<p><a href="javascript:document.visu_guilde.submit();">Voir les détails</a></form>
		<p><a href="quitte_guilde.php">Quitter la guilde</a></br />
		<?php 
		if ($db->f("pguilde_message") == 'O')
		{
			?>
			<p>Vous recevez actuellement tous les messages de la guilde. <a href="guilde_refuse_message.php">Ne plus les recevoir !</a><br />
			<?php 
		}
		else
		{
			?>
			<p>Vous ne recevez pas les messages de la guilde. <a href="guilde_accepte_message.php">Les recevoir de nouveau !</a><br />
			<?php 
		}
	}
	
	?>
	<p><a href="voir_toutes_guildes.php">Voir toutes les guildes</a>
	<?php 
	
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
