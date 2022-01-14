<?php
include "blocks/_header_page_jeu.php";
ob_start();
?>
    <form name="decalage" method="post" action="valide_decalage_dlt.php">
        <p>Décaler sa DLT de <input type="text" name="temps_dlt"> minutes
        <input type="submit" value="Valider !" class="test centrer">
    </form>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
