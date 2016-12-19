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
?>
<form ENCTYPE="multipart/form-data" name="test" action="valide_change_avatar_perso.php" method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="150600">
<input type="hidden" name="suppr" value="0">
<table><tr><td>
<INPUT NAME="avatar" TYPE="file">
</td></tr>
<tr><td><center><input type="submit" value="Enregistrer cet avatar" class="test"></td></tr>
<tr><td><p>(formats supportés : bmp, gif, jpg et png, avec extension en minuscule, maximum 20 Ko)</td></tr>
<tr><td><p style="text-align:center"><a href="javascript:document.test.suppr.value=1;document.test.submit();">Supprimer l'avatar actuel ?</a></p>
</table>
</form>
<?php 
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
