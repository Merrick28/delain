<?php 
//include "../connexion.php";
include "../verif_connexion.php";
include '../../includes/template.inc';
$param = new parametres();
$t = new template('..');
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

$contenu_page = '';

// ON VRERIFIE SI L'OBJET EST BIEN DANS L'INVENTAIRE.
$bd=new base_delain;
$req_matos = "select perobj_obj_cod from perso_objets,objets "
. "where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 327 order by perobj_obj_cod";
$bd->query($req_matos);
if(!($bd->next_record()))
{
  // PAS D'OBJET.
 	$contenu_page .= "<p>Vous avez beau chercher, il n'y a aucun cadeau dans votre sac</p>";
}
else
{
	$contenu_page .= '<p align="center"><br>
			Vous tenez dans les mains une grande boite enveloppée d’un papier argenté sur lequel de petits dragons céladon s’enroulent autour d’un bouclier carmin marqué d’une inscription. Un ruban rouge assorti aux petits boucliers ferme la boite. Vous hésitez :<br>
		<br>
		-	Allez-vous, âme noire et vile que vous êtes, le confier à un lutin noir afin d’asseoir le règne de Monsieur Jacques ?<br>
		<br>
		-	Allez-vous, gentil paladin bisounours que vous êtes, le confier à un lutin rouge afin que le Père Noël triomphe ?<br>
		<br>
		-	Allez-vous, monstre d\'égoïsme que vous êtes, <a href="../action.php?methode=ouvre_cadeau">le garder pour vous et l\'ouvrir (' . $param->getparm(98) . ' PA) ?</a>
		</p>';
}

// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");