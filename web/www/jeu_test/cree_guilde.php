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
    <form name="cree_guilde" mEthod="post" action="valide_cree_guilde.php">
        <table>
            <tr>
                <td>
                    <p>Nom de la guilde :
                </td>
                <td>
                    <input type="text" name="nom_guilde" size="60">
                </td>
            </tr>

            <tr>
                <td>
                    <p>Description de la guilde :
                </td>
                <td>
                    <textarea name="desc" cols="60" rows="10"></textarea>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="submit" value="Créer la guilde !" class="test centrer">
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

