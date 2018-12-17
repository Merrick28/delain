<?php
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);
$t->set_var('URL_IMAGES', G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');
ob_start();
?>
<form name="don" method="post" action="action.php">
    <input type="hidden" name="methode" value="don_br">
    <table>
        <tr>
            <td>Choisissez le perso qui doit recevoir votre don :</td>
            <td><select name="dest">
                    <?php
                    $req = "select perso_cod,perso_nom,perso_type_perso,lower(perso_nom) as minusc from perso,perso_position ";
                    $req = $req . "where ppos_pos_cod = (select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod) ";
                    $req = $req . "and ppos_perso_cod = perso_cod ";
                    $req = $req . "and perso_cod != $perso_cod and perso_actif = 'O' ";
                    $req = $req . "order by perso_type_perso,minusc ";
                    $db->query($req);
                    while ($db->next_record())
                    {
                        echo "<option value=\"", $db->f("perso_cod"), "\">", $db->f("perso_nom"), "</option>";
                    }
                    $db->query("select perso_po from perso where perso_cod = $perso_cod");
                    $db->next_record();
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Ainsi que la somme à donner (max <?php echo $db->f("perso_po"); ?> brouzoufs) :</td>
            <td><input type="text" name="qte" value="0"></td>
        </tr>
    </table>
    <input type="submit" class="test centrer" value="Faire le don !">
</form>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
$t->parse("Sortie", "FileRef");
$t->p("Sortie");
?>
