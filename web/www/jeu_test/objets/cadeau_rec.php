<?php 
include "../verif_connexion.php";
include '../../includes/template.inc';
$param = new parametres();
$t = new template('..');
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

$contenu_page = '';

// ON VERIFIE SI L’OBJET EST BIEN DANS L’INVENTAIRE.
$bd=new base_delain;
$req_matos = "select perobj_obj_cod from perso_objets,objets 
	where perobj_obj_cod = obj_cod and perobj_perso_cod = $perso_cod and obj_gobj_cod = 327 order by perobj_obj_cod";
$bd->query($req_matos);
if(!($bd->next_record()))
{
  // PAS D'OBJET.
 	$contenu_page .= "<p>Vous avez beau chercher, il n’y a aucun cadeau dans votre sac</p>";
}
else
{
	$contenu_page .= '<p>Vous tenez dans les mains une grande boite enveloppée d’un papier doré sur lequel de petits dragons céladon s’enroulent autour d’un bouclier carmin marqué d’une inscription.
		Un ruban vermeil assorti aux petits boucliers ferme la boite. Ce cadeau est superbe !<br />
		Cependant, vous constatez que des épines de sapin sont enfichées à la jointure de papier ! Ces maudits sapins enragés auraient-ils trafiqué le cadeau ?<br /><br />
		<a href="../action.php?methode=ouvre_cadeau">Ouvrir le cadeau(' . $param->getparm(98) . ' PA) ?</a>
		</p>';

	// On recherche la présence d’un lutin rouge sur la case.
	$req = "select count(*) as nb from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		where perso_gmon_cod = 345
			and ppos_pos_cod = 
				(select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod)";
	$bd->query($req);
	$bd->next_record();
	$nombre = $bd->f('nb');
	if($nombre > 0)
	{
		$cout_pa = $param->getparm(99);
		$cout10 = 10 * $cout_pa;
		$contenu_page .= '<hr /><p>Tiens donc ! Voilà un lutin rouge qui a l’air bien intéressé par votre chargement !</p>
			<p>« Messire ! Mam’zelle ! Enfin, peu importe... Vous avez des cadeaux du Père Léno ! Je suis venu les racheter, il faut qu’on les nettoie pour pouvoir les refourguer l’an prochain...<br />
			200 brouzoufs par cadeaux, ça vous va ?<br />
			<a href="../action.php?methode=don_cadeau_rouge">OK, je vous en vends un.</a> (' . $cout_pa . ' PA)<br />
			<a href="../action.php?methode=don_cadeau_rougeX10">Je vous en vends jusqu’à 10 !</a> (' . $cout10 . ' PA, ou moins si vous avez moins de 10 cadeaux et / ou PA)</p>';
	}
}
// on va maintenant charger toutes les variables liées au menu
include('../variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
