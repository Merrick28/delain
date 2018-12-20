<?php
include "blocks/_header_page_jeu.php";
ob_start();
$req_ref_tran = "delete from transaction where tran_cod = $transaction";
$db->query($req_ref_tran);

?>
    La transaction a été annulée !<br><br><a href="transactions2.php">Retour aux transactions</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";