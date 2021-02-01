<?php
$verif_connexion::verif_appel();
$sens[1] = "vente (magasin vers aventurier) ";
$sens[2] = "achat (aventurier vers magasin) ";
$req     = "select obj_nom,mtra_sens,sum(mtra_montant) as somme,count(mtra_cod) as nombre 
                from objet_generique,objets,mag_tran 
                where mtra_lieu_cod = :lieu
                and mtra_obj_cod = obj_cod 
                and obj_gobj_cod = gobj_cod 
                group by obj_nom,mtra_sens ";
$stmt    = $pdo->prepare($req);
$stmt    = $pdo->execute(array(":lieu" => $_REQUEST['lieu']), $stmt);
$alltran = $stmt->fetchAll();
if (count($alltran) == 0)
{
    echo "<p>Aucune transaction enregistrée dans votre échoppe.";
} else
{
    ?>
    <table>
        <tr>
            <td class="soustitre2"><strong>Nom</strong></td>
            <td class="soustitre2"><strong>Sens</strong></td>
            <td class="soustitre2"><strong>Montant global</strong></td>
            <td class="soustitre2"><strong>Nombre</strong></td>
        </tr>
    <?php
    foreach ($alltran as $result)
    {
        echo "<tr>";
        echo "<td class=\"soustitre2\">", $result['obj_nom'], "</td>";
        $idx_sens = $result['mtra_sens'];
        echo "<td>", $sens[$idx_sens], "</td>";
        echo "<td class=\"soustitre2\">", $result['somme'], "</td>";
        echo "<td>", $result['nombre'], "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p style=\"text-align:center\"><a href=\"admin_echoppe_stats.php?lieu=$lieu&methode=stats2\">Voir le détail</a>";
}


