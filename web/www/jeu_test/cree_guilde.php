<?php
include "blocks/_header_page_jeu.php";
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
include "blocks/_footer_page_jeu.php";

