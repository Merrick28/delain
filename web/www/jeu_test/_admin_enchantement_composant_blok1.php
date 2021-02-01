<?php
$verif_connexion::verif_appel();
if ($action == 'ajout')
{
    $req  =
        " insert into formule_composant (frmco_frm_cod,frmco_gobj_cod,frmco_num) values (:pot,:gobj,:nombre)";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(
                              ":pot"    => $pot,
                              ":gobj"   => $gobj,
                              ":nombre" => $_POST['nombre']
                          ), $stmt);
}
if ($action == 'suppr')
{
    $req  = " delete from formule_composant where frmco_frm_cod = pot and frmco_gobj_cod = :comp_pot";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(
                              ":pot"      => $pot,
                              ":comp_pot" => $comp_pot
                          ), $stmt);
}
$req  = 'select frmco_frm_cod,frmco_gobj_cod,frmco_num,gobj_nom
				from formule_composant,objet_generique
				where frmco_frm_cod = :num_form
				and frmco_gobj_cod = gobj_cod ';
$stmt = $pdo->prepare($req);
$stmt = $pdo->execute(array(
                          ":num_form" => $num_form
                      ), $stmt);
while ($result = $stmt->fetch())
{
    echo '<br>' . $result['gobj_nom'] . ' (' . $result['frmco_num'] . ') - <a href="' . $_SERVER['PHP_SELF'] . '?methode2=serie_obj&action=suppr&comp_pot=' . $result['frmco_gobj_cod'] . '&pot=' . $pot . '">Supprimer ?</a>';
}
?>
<br>Ajouter un objet :
<form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="methode2" value="serie_obj">
    <input type="hidden" name="action" value="ajout">
    <input type="hidden" name="pot" value="<?php echo $num_form; ?>">
    <table>
        <tr>
            <td>Composant</td>
            <td>Nombre de composants</td>
        </tr>
        <tr>
            <td><select name="gobj">
                    <?php
                    $req  =
                        "select gobj_cod,gobj_nom from objet_generique 
                            where gobj_cod in (338,339,340,341,353,354,357,358,361,438) order by gobj_nom ";
                    $stmt = $pdo->query($req);
                    while ($result = $stmt->fetch())
                    {
                        echo '<option value="' . $result['gobj_cod'] . '">' . $result['gobj_nom'] . '</option>';
                    }

                    ?>
                </select></td>
            <td><input type="text" name="nombre" value="1"></td>
    </table>
    <input type="submit" value="Ajouter"></form>
