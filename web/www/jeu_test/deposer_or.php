<?php
include "blocks/_header_page_jeu.php";
ob_start();
$req_or = "select perso_po from perso where perso_cod = $perso_cod ";
$stmt   = $pdo->query($req_or);
$result = $stmt->fetch();
?>
    <form name="deposer_or" method="post" action="valide_deposer_or.php">
        <?php
        printf("<p>Vous avez %s brouzoufs.</p>", $result['perso_po']);
        ?>
        <p>Je veux déposer <input type="text" name="quantite"> brouzoufs !</p>
        <input type="submit" class="test centrer" value="Valider">
    </form>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";