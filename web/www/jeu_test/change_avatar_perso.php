<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <form ENCTYPE="multipart/form-data" name="test" action="valide_change_avatar_perso.php" method="post">
        <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="150600">
        <input type="hidden" name="suppr" value="0">
        <table>
            <tr>
                <td>
                    <INPUT NAME="avatar" TYPE="file">
                </td>
            </tr>
            <tr>
                <td><input type="submit" value="Enregistrer cet avatar" class="test centrer"></td>
            </tr>
            <tr>
                <td><p>(formats supportés : bmp, gif, jpg et png, avec extension en minuscule, maximum 20 Ko)</td>
            </tr>
            <tr>
                <td><a class="centrer" href="javascript:document.test.suppr.value=1;document.test.submit();">Supprimer
                        l'avatar actuel ?</a>
        </table>
    </form>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";