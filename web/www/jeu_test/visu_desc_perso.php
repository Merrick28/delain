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
if (!isset($visu) || $visu == '' || !preg_match('/^[0-9]*$/i', $visu))
{
	echo "<p>Anomalie sur numéro perso !";
	exit();
}
include "perso2_description.php";

$contenu_page .= '
<form name="evt" method="post" action="visu_evt_perso.php">
<input type="hidden" name="visu" value="' . $visu . '">
</form>
<form name="message" method="post" action="messagerie2.php">
<input type="hidden" name="m" value="2">
<input type="hidden" name="n_dest" value="' . $visu_perso_nom . '">

<input type="hidden" name="dmsg_cod">
</form>
<p style=text-align:center><a href="javascript:document.evt.submit();">Voir ses évènements !</a><br>
<a href="javascript:document.message.submit();">Envoyer un message !</a>
</p>';
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
