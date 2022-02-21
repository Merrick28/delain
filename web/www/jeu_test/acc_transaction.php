<?php
include "blocks/_header_page_jeu.php";

$pdo = new bddpdo();

$req_acc_tran = "select accepte_transaction(:transaction) as resultat";

$stmt = $pdo->prepare($req_acc_tran);
$stmt = $pdo->execute(array(
                          ":transaction" => $_REQUEST['transaction']
                      ), $stmt);

if (!$result = $stmt->fetch())
{
    die('Erreur sur chargement fonction accepte_transaction');
}
$resultat_temp = $result['identifie'];
$tab_res       = explode(";", $resultat_temp);
$template      = $twig->load('acc_transaction.twig');
$options_twig  = array(

    'TEST_RESULTAT' => $tab_res[0],
    'DETAIL_ERREUR' => $tab_res[1]
);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));