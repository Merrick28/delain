<?php
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
<form name="change_pass" method="post" action="valide_change_pass.php">
    <table>

        <tr>
            <td><p>Ancien mot de passe : </td>
            <td><input type="password" name="ancien"></td>
        </tr>

        <tr>
            <td><p>Nouveau mot de passe : </td>
            <td><input type="password" name="nouveau1"></td>
        </tr>

        <tr>
            <td><p>Nouveau mot de passe (confirmation) : </td>
            <td><input type="password" name="nouveau2"></td>
        </tr>

        <tr>
            <td colspan="2"><p style="text-aligne:center">
                    <input type="submit" class="test" value="Valider les changements !"></p>
            </td>
        </tr>
    </table>
</form>


<?php
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse('Sortie', 'FileRef');
$t->p('Sortie');
?>
