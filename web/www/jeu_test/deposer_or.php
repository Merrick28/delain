<?php
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
?>
    <form name="deposer_or" method="post" action="valide_deposer_or.php">
        <?php
        echo "<p>Vous avez " . $perso->perso_po . " brouzoufs.</p>";
        ?>
        <p>Je veux déposer <input type="text" name="quantite"> brouzoufs !</p>
        <input type="submit" class="test centrer" value="Valider">
    </form>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";