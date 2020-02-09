<?php
$sens[1] = "vente (magasin vers aventurier) ";
$sens[2] = "achat (aventurier vers magasin) ";
$req     =
    "select mtra_date,obj_nom,mtra_sens,mtra_montant,perso_nom,
                to_char(mtra_date,'DD/MM/YYYY hh24:mi:ss') as date_tran 
                from objet_generique,objets,mag_tran,perso 
                where mtra_lieu_cod = :lieu 
                and mtra_obj_cod = obj_cod 
                and obj_gobj_cod = gobj_cod 
                and mtra_perso_cod = perso_cod 
                order by mtra_date ";
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
            <td class="soustitre2"><strong>Objet</strong></td>
            <td class="soustitre2"><strong>Perso</strong></td>
            <td class="soustitre2"><strong>Sens</strong></td>
            <td class="soustitre2"><strong>Montant</strong></td>
            <td class="soustitre2"><strong>Date</strong></td>
        </tr>
    <?php
    foreach ($alltran as $result)
    {
        echo "<tr>";
        echo "<td class=\"soustitre2\">", $result['obj_nom'], "</td>";
        echo "<td>", $result['perso_nom'], "</td>";
        $idx_sens = $result['mtra_sens'];
        echo "<td class=\"soustitre2\">", $sens[$idx_sens], "</td>";
        echo "<td>", $result['mtra_montant'], "</td>";
        echo "<td class=\"soustitre2\">", $result['date_tran'], "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

