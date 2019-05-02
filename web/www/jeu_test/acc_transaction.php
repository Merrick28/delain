<?php 
include "blocks/_header_page_jeu.php";

$pdo = new bddpdo();

$req_acc_tran = "select accepte_transaction(:transaction) as resultat";

$stmt           = $pdo->prepare($req_acc_tran);
$stmt           = $pdo->execute(array(
    ":transaction" => $_REQUEST['transaction']
), $stmt);

if(!$result = $stmt->fetch())
{
    die('Erreur sur chargement fonction identification');
}
$resultat_temp = $result['identifie'];


$tab_res = explode(";",$resultat_temp);
if ($tab_res[0] == -1)
{
	$contenu_page  = '<p>Une erreur est survenue : ' . $tab_res[1];
}
else
{
	$contenu_page  = '<p>La transaction a été validée. L\'objet de trouve maintenant dans votre inventaire.';
}

include "blocks/_footer_page_jeu.php";