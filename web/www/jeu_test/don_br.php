<?php
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
?>
    <form name="don" method="post" action="action.php">
        <input type="hidden" name="methode" value="don_br">
        <table>
            <tr>
                <td>Choisissez le perso qui doit recevoir votre don :</td>
                <td><select name="dest">
                        <?php
                        $req  = "select perso_cod,perso_nom,perso_type_perso,lower(perso_nom) as minusc from perso,perso_position ";
                        $req  = $req . "where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod) ";
                        $req  = $req . "and ppos_perso_cod = perso_cod ";
                        $req  = $req . "and perso_cod != $perso_cod and perso_actif = 'O' ";
                        $req  = $req . "order by perso_type_perso,minusc ";
                        $stmt = $pdo->query($req);
                        while ($result = $stmt->fetch())
                        {
                            echo "<option value=\"", $result['perso_cod'], "\">", $result['perso_nom'], "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Ainsi que la somme à donner (max <?php $perso->perso_po; ?> brouzoufs)
                    :
                </td>
                <td><input type="text" name="qte" value="0"></td>
            </tr>
        </table>
        <input type="submit" class="test centrer" value="Faire le don !">
    </form>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";