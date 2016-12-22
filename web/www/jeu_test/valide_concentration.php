<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
$db->query($req_pa);
$db->next_record();
if ($db->f("perso_pa") < 4)
{
	echo("Vous n'avez pas assez de PA pour vous concentrer !");
}
else
{
	$req_count_conc = "select 1 from concentrations where concentration_perso_cod = $perso_cod";
	$db->query($req_count_conc);
	if ( $db->nf() != 0 )
    {
        echo("Vous êtes déjà en train de vous concentrer !");
    }
    else
    {
        $req_con = "insert into concentrations (concentration_cod,concentration_perso_cod,concentration_nb_tours) ";
        $req_con = $req_con . "values (nextval('seq_concentration_cod'),$perso_cod,2)";
        $db->query($req_con);

        $req_enl_pa = "update perso set perso_pa = perso_pa - 4 where perso_cod = $perso_cod";
        $db->query($req_enl_pa);

        echo("<p>Vous êtes maintenant concentré pour 2 tours.</p>");
    }
}
echo("<p><center><a href=\"index.php\">Retour</a></center></p>");
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
$t->parse('Sortie','FileRef');
$t->p('Sortie');
