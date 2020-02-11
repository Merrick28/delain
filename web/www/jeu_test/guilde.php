<?php
include "blocks/_header_page_jeu.php";
ob_start();

// on recherche si il y a guilde
$req_guilde = "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod,rguilde_admin,pguilde_message from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_rang_cod = pguilde_rang_cod ";
$req_guilde = $req_guilde . "and pguilde_valide = 'O' ";
$stmt = $pdo->query($req_guilde);
$nb_guilde = $stmt->rowCount();
if ($nb_guilde == 0) {
    $req_guilde = "select guilde_cod,guilde_nom from guilde,guilde_perso ";
    $req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod ";
    $req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
    $req_guilde = $req_guilde . "and pguilde_valide = 'N' ";
    $stmt = $pdo->query($req_guilde);
    $nb_guilde = $stmt->rowCount();
    if ($nb_guilde == 0) {
        ?>
        <p>Vous n'êtes affilié à aucune guilde !<br/>
            <a href="voir_toutes_guildes.php">Rejoindre une guilde ?</a><br/>
        </p>
        <?php
    } else {
        $result = $stmt->fetch();
        ?>
        <p>Vous n'êtes affilié à aucune guilde !<br/>
            Vous postulez actuellement à la guilde: <strong><?php echo $result['guilde_nom'] ?></strong><br/>
            <a href="voir_toutes_guildes.php">Rejoindre une autre guilde ?</a><br/>
        </p>
        <?php
    }
} else {
    $result = $stmt->fetch();
    printf("<p>Vous êtes affilié à la guilde <strong>%s</strong> en tant que <strong>%s</strong>", $result['guilde_nom'], $result['rguilde_libelle_rang']);
    if ($result['rguilde_admin'] == 'O') //admin !!!
    {
        ?>
        <p><a href="admin_guilde.php">Administrer la guilde</a><br/>
        <?php
    } else {
        ?>
        <form name="visu_guilde" method="post" action="visu_guilde.php">
            <?php
            printf("<input type=\"hidden\" name=\"num_guilde\" value=\"%s\">", $result['guilde_cod']);
            ?>
        <a href="javascript:document.visu_guilde.submit();">Voir les détails</a></form>
        <br /><a href="quitte_guilde.php">Quitter la guilde</a></br />
        <?php
        if ($result['pguilde_message'] == 'O') {
            ?>
            <p>Vous recevez actuellement tous les messages de la guilde. <a href="guilde_refuse_message.php">Ne plus les
                recevoir !</a><br/>
            <?php
        } else {
            ?>
            <p>Vous ne recevez pas les messages de la guilde. <a href="guilde_accepte_message.php">Les recevoir de
                nouveau !</a><br/>
            <?php
        }
    }

    ?>
    <p><a href="voir_toutes_guildes.php">Voir toutes les guildes</a>
    <?php

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
