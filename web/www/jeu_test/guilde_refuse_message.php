<?php
include "blocks/_header_page_jeu.php";
ob_start();
$req = "update guilde_perso set pguilde_message = 'N' where pguilde_perso_cod = $perso_cod ";
$db->query($req);
?>
    <p>Les modifications sont enregistrées. Vous ne recevrez plus tous les messages de la guilde.

    <p style="text-align:center;"><a href="guilde.php">Retour !</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
