<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <form name="change_pass" method="post" action="valide_change_pass.php">
        <table>

            <tr>
                <td><p>Ancien mot de passe :</td>
                <td><input type="password" name="ancien"></td>
            </tr>

            <tr>
                <td><p>Nouveau mot de passe :</td>
                <td><input type="password" name="nouveau1"></td>
            </tr>

            <tr>
                <td><p>Nouveau mot de passe (confirmation) :</td>
                <td><input type="password" name="nouveau2"></td>
            </tr>

            <tr>
                <td colspan="2">
                        <input type="submit" class="test centrer" value="Valider les changements !">
                </td>
            </tr>
        </table>
    </form>


<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";