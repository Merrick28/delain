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
$req = "select lieu_cod,lieu_nom,pos_x,pos_y,etage_libelle
							from lieu,lieu_position,positions,etage,temple_fidele 
							where lieu_cod = lpos_lieu_cod 
							and lieu_tlieu_cod = 17 
							and lpos_pos_cod = pos_cod 
							and pos_etage = etage_numero 
							and tfid_lieu_cod = lieu_cod 
							and tfid_perso_cod = $perso_cod 
							order by pos_etage desc";
$db->query($req);
if ($db->nf() == 0)
{
	echo "<p>Vous n'avez aucun temple à votre charge !";
}
else
{
	echo "<p>Temples à votre charge :<br>";
	while ($db->next_record())
	{
		echo "<p><a href=\"gere_temple3.php?mag=" . $db->f("lieu_cod") . "\">" . $db->f("lieu_nom") . "</a> (" . $db->f("pos_x") . ", " . $db->f("pos_y") . ", " . $db->f("etage_libelle") . ")<br>";
	}	
	
}

$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
