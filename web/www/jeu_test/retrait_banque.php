<?php
include "blocks/_header_page_jeu.php";
ob_start();

// on regarde si le joueur est bien sur une banque
$erreur = 0;
if (!is_lieu($perso_cod)) {
    echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    $erreur = 1;
}
if ($erreur == 0) {
    $tab_lieu = get_lieu($perso_cod);
    if ($tab_lieu[3] != 'Banque') {
        $erreur = 1;
        echo("<p>Erreur ! Vous n'êtes pas sur une banque !!!");
    }
}

if ($erreur == 0) {
    echo("<img src=\"../images/banque3.png\"><br />");
    // on recherche l'or en banque
    $req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
    $stmt   = $pdo->query($req_or);

    $nb_or = $stmt->rowCount();
    if ($nb_or == 0) {
        $qte_or = 0;
    } else {
        $result = $stmt->fetch();
        $tab_or = $result['pbank_or'];
        $qte_or = $tab_or[0];
    }
    echo("<p>Vous avez $qte_or brouzoufs sur votre compte.");
    ?>
    <hr/>
    <form name="depot" method="post" action="valide_retrait_banque.php">
        <p>Retirer <input type="text" name="quantite"> brouzoufs de mon compte.
        <p><input type="submit" value="Valider !" class="test centrer">
    </form>
    <?php

}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
