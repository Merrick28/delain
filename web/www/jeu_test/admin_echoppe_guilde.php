<?php
include "blocks/_header_page_jeu.php";
ob_start();
if (!isset($methode)) {
    $methode = "debut";
}
include "blocks/_test_admin_echoppe.php";
if ($erreur == 0) {
    switch ($methode) {
        case "debut":
            ?>
            <p>Les montants ci dessous sont des modificateurs à l'achat pour les guildes. Un modificateur négatif
                signifie
                une
                remise, un positif un surplus.<br>
                Les modificateurs doivent être compris entre -20 et 20.
            <form name="guilde" method="post" action="admin_echoppe_guilde.php">
                <div class="centrer">
                    <table>
                        <input type="hidden" name="methode" value="suite">
                        <?php
                        $req =
                            "select guilde_cod,guilde_nom,guilde_modif,lower(guilde_nom) as minuscule from guilde order by minuscule";
                        $db->query($req);
                        while ($db->next_record()) {
                            echo "<tr>";
                            echo "<td class=\"soustitre2\">", $db->f("guilde_nom"), "</td>";
                            echo "<td><input type=\"text\" name=\"modif[", $db->f("guilde_cod"), "]\" value=\"", $db->f("guilde_modif"), "\"></td>";
                            echo "</tr>";
                        }
                        ?>


                    </table>
                </div>
                <input type="submit" class="test" value="Valider !"></div>
            </form>
            <?php
            break;
        case "suite":
            foreach ($modif as $key => $val) {
                $erreur = 0;
                $req = "select guilde_nom from guilde where guilde_cod = $key ";
                $db->query($req);
                $db->next_record();
                $nom_guilde = $db->f("guilde_nom");
                if (($val < -20) || ($val > 20)) {
                    $erreur = 1;
                    echo "<p>Anomalie sur la guilde <strong>", $nom_guilde, "</strong>, le modificateur doit être compris entre -20 et +20 !</p>";
                }
                if ($erreur == 0) {
                    $req = "update guilde set guilde_modif = $val where guilde_cod = $key ";
                    $db->query($req);
                    echo "La guilde <strong>", $nom_guilde, "</strong> a été modifiée ! <br>";
                }
            }
            ?>
            <p style="text-align:center"><a href="admin_echoppe.php">Retour ! </a>
            <?php
            break;


    }

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
