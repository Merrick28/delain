<?php 
include "classes.php";
include 'includes/template.inc';
$t = new template;
$t->set_file('FileRef','template/delain/index.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
//
// identification
//
ob_start();
include G_CHE . "ident.php";
$ident = ob_get_contents() . montre_formulaire_connexion($verif_auth);
ob_end_clean();
$t->set_var("IDENT",$ident);

//
//Contenu de la div de droite
//
$contenu_page = '';
$db_faq=new base_delain;
$db->query("select tfaq_cod,tfaq_libelle from faq_type order by tfaq_cod");
while($db->next_record())
{
	$contenu_page .= '<br><b>'.$db->f("tfaq_libelle").'</b><br>';
	$tfaq_cod=$db->f("tfaq_cod");
	$db_faq->query("select faq_cod,faq_question,faq_reponse from faq where faq_tfaq_cod=".$tfaq_cod." order by faq_cod");
	while($db_faq->next_record())
		$contenu_page .= '<a href="#'.$db_faq->f("faq_cod").'">'.$db_faq->f("faq_question").'</a><br>';
}
$db->query("select tfaq_cod,tfaq_libelle from faq_type order by tfaq_cod");
while($db->next_record())
{
	$tfaq_cod=$db->f("tfaq_cod");
	$contenu_page .= '<div class="titre">' . $db->f("tfaq_libelle") . '</div>';
	$db_faq->query("select faq_cod,faq_question,faq_reponse from faq where faq_tfaq_cod=".$tfaq_cod." order by faq_cod");
	while($db_faq->next_record())
		$contenu_page .= '<b><a name="'.$db_faq->f("faq_cod").'">'.$db_faq->f("faq_question").'</a></b><br>'.$db_faq->f("faq_reponse").'<br><a href="#haut">Haut de page</a><br><br>';	}
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');