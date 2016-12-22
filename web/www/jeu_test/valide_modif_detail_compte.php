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
if ($db->is_admin($compt_cod))
{
	switch($methode)
	{
		case "comment":
			$req = "select compt_nom from compte where compt_cod = $compt_cod ";
			$db->query($req);
			$db->next_record();
			$nom = $db->f("compt_nom");

			$req = "select to_char(now(),'DD/MM/YYYY hh24:mi:ss') as maint ";
			$db->query($req);
			$db->next_record();
			$maint = $db->f("maint");


			$comment = nl2br($comment);
			$req = "update compte set compt_commentaire = '<br><b>$maint par $nom </b><br>$comment'||coalesce(compt_commentaire,' ') ";
			$req = $req . "where compt_cod = $compte ";
			if ($db->query($req))
			{
				echo "<p>Requête effectuée !";
			}
			else
			{
				echo "<p>Erreur sur la requête !";
			}
			break;




	}


}
else
{
	echo "<p>Erreur ! Vous n'êtes pas administrateur !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
