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
$visu = $_POST['visu'];
$req = "select perso_nom from perso where perso_cod = $visu ";
$db->query($req);
$db->next_record();
$nom_cible = $db->f("perso_nom");
$contenu_page .= "<p>Etes-vous sur de vouloir déclencher une révolution contre <b>" . $nom_cible . "</b> ?<br>";
$contenu_page .= '
<form name="revolution" method="post" action="action.php">
<input type="hidden" name="methode" value="revolution">
<input type="hidden" name="cible" value="'. $visu.'">
<p style="text-align:center;"><a href="javascript:document.revolution.submit();">OUI, je veux renverser cet administrateur inique !</a><br>
<p style="text-align:center;"><a href="guilde.php">NON, il ne faut pas contrarier les admins, après ça tourne mal.</a><br>';

//
// génération du fichier final
//
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>

?>
