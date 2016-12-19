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
$bd=new base_delain;
$req_or = "select perso_po from perso where perso_cod = $perso_cod ";
$bd->query($req_or);
$bd->next_record();
?>
<form name="deposer_or" method="post" action="valide_deposer_or.php">
<?php 
printf("<p>Vous avez %s brouzoufs.</p>",$bd->f("perso_po"));
?>
<p>Je veux déposer <input type="text" name="quantite"> brouzoufs !</p>
<center><input type="submit" class="test" value="Valider"></center>
</form>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
