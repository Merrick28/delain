<?php
include "blocks/_header_page_jeu.php";
ob_start();
if ($_POST['type_a'] == 'o') {
    if ($tran) {
        foreach ($tran as $key => $val) {
            /*controle de l'acheteur*/
            $req = "select tran_acheteur from transaction where tran_cod = $key";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            $acheteur = $result['tran_acheteur'];
            if ($acheteur != $perso_cod) {
                echo "<p>Erreur ! Vous essayez de valider une transaction qui ne vous est pas destinée !";
                break;
            }
            $req_acc_tran = "select accepte_transaction($key) as resultat";
            $stmt = $pdo->query($req_acc_tran);
            $result = $stmt->fetch();
            $resultat_temp = $result['resultat'];
            $tab_res = explode(";", $resultat_temp);
            if ($tab_res[0] == -1) {
                echo("<p>Une erreur est survenue : $tab_res[1]");
            } else {
                echo("<p>La transaction a été validée. L'objet se trouve maintenant dans votre inventaire.");
            }
        }
    } else {
        echo "<p>Aucune transaction cochée !";
    }
}
if ($_POST['type_a'] == 'n') {
    if ($tran) {
        foreach ($tran as $key => $val) {
            $req_ref_tran = "delete from transaction where tran_cod = $key";
            if ($stmt = $pdo->query($req_ref_tran)) {
                echo "<p>La transaction a été annulée !";
            } else {
                echo "<p>Une erreur est survenue !";
            }
        }
    } else {
        echo "<p>Aucune transaction cochée !";
    }
}
?>
    <br><br><a href="transactions2.php">Retour aux transactions</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
