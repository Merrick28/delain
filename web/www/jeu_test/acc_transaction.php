<?php 
include "blocks/_header_page_jeu.php";

$req_acc_tran = "select accepte_transaction($transaction) as resultat";
$db = new base_delain;
$db->query($req_acc_tran);
$db->next_record();
$resultat_temp = $db->f("resultat");
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