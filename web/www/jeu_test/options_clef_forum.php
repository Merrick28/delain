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

$methode = (isset($_GET['methode'])) ? $_GET['methode'] : '';
$texte_resultat = '';

switch ($methode)
{
	case 'clef':
		// Génération de la clé
		$chaine = md5($compt_cod . mt_rand() . time());

		$requete = "update compte set compt_clef_forum = '$chaine', 
			compt_validite_clef_forum = now() + '6 hours'::interval,
			compt_nombre_clef_forum = compt_nombre_clef_forum + 1
			where compt_cod = $compt_cod";
		$db->query($requete);

		$texte_resultat = "<p>Une nouvelle clef a été générée pour vous permettre de créer un compte sur le forum : « $chaine ».</p>";
	break;

	default:
	break;
}

$contenu_page = '<p class="titre">Clef d’activation de compte sur le forum</p>';
$contenu_page .= '<p>Cette page vous permet d’obtenir une clef, nécessaire à l’inscription sur le forum du jeu.</p>';
$contenu_page .= '<p>Une clef n’est valable que pour les 6 prochaines heures, et pour une seule inscription au forum. 
	Cela n’interdit néanmoins pas de créer autant de compte que désiré sur le forum (par exemple, un compte par personnage, pour des questions de RP).
	Il suffit, pour cela, de demander une nouvelle clef pour chaque compte à créer.</p>';

$contenu_page .= "<br /><br /><p><a href='?methode=clef'>Générer une nouvelle clef ?</a></p>$texte_resultat";

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
