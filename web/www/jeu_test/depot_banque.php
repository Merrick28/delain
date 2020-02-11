<?php
include "blocks/_header_page_jeu.php";
ob_start();

// on regarde si le joueur est bien sur une banque
$type_lieu = 1;
$nom_lieu = 'une banque';

include "blocks/_test_lieu.php";

if ($erreur == 0)
{
    echo("<img src=\"../images/banque3.png\"><br />");
    // on recherche l'or en banque
    $req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
    $stmt = $pdo->query($req_or);
    $nb_or = $stmt->rowCount();
    if ($nb_or == 0)
    {
        $qte_or = 0;
    } else
    {
        $result = $stmt->fetch();
        $qte_or = $result['pbank_or'];
    }
    echo("<p>Vous avez $qte_or brouzoufs sur votre compte.");
    ?>
    <hr/>
    <form name="depot" method="post" action="valide_depot_banque.php">
        <p>Déposer <input type="text" name="quantite"> brouzoufs sur mon compte.
        <p><input type="submit" value="Valider !" class="test centrer">
    </form>
    <?php

}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
