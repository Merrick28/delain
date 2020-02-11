<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = 1;
$nom_lieu = 'une banque';

include "blocks/_test_lieu.php";


if ($quantite <= 0)
{
    $erreur = 1;
    ?>
    <p>Vous ne pouvez pas retirer une somme inférieure ou égale à 0 !
    <?php
}

if ($erreur == 0)
{

    ?>
    <img src="../images/banque3.png" alt="Banque"><br/>
    <?php
    $req_depot = "select retrait_banque($perso_cod,$quantite) as retrait";
    $stmt = $pdo->query($req_depot);
    $result = $stmt->fetch();
    $tab_depot = $result['retrait'];
    if ($tab_depot == 0)
    {
        ?>
        <p>Vous venez de retirer <strong><?php echo $quantite; ?></strong> brouzoufs de votre compte en banque.
        <?php
    } else
    {
        ?>
        <p>Une anomalie est survenue : <strong><?php echo $tab_depot ?>
        <?php
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";