<?php
include "blocks/_header_page_jeu.php";
ob_start();
$req = "update guilde_perso set pguilde_message = 'O' where pguilde_perso_cod = $perso_cod ";
$db->query($req);

?>
    Les modifications sont enregistrées. Vous recevrez maintenant tous les messages de la guilde.

    <br/><a href="guilde.php" class="centrer">Retour !</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
