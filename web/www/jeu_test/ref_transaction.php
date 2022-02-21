<?php
include "blocks/_header_page_jeu.php";

$pdo = new bddpdo();
ob_start();
$req_ref_tran = "delete from transaction where tran_cod = :transaction";
$stmt = $pdo->prepare($req_ref_tran);
$stmt = $pdo->execute(array(":transaction" => $_REQUEST['transaction']),$stmt);


?>
    La transaction a été annulée !<br><br><a href="transactions2.php">Retour aux transactions</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";