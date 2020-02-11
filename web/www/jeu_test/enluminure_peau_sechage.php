<?php
$is_enlumineur = $db->is_enlumineur($perso_cod);

if ($is_enlumineur) {
    if (!isset($methode)) {
        $methode = "debut";
    }
    switch ($methode) {
        case "debut":
            $req_comp = "select foi_obj_cod,to_char(foi_date_crea,'DD-MM-YYYY / hh24:mi') as date_deb,foi_gobj_cod as gobj_cod_fini,gobj_nom as gobj_nom_fini,obj_nom,foi_formule_cod,frm_nom,frm_temps_travail
							from objet_generique,formule_objet_inacheve,objets,perso_objets,formule
								where foi_gobj_cod = gobj_cod
								and perobj_perso_cod = $perso_cod
								and perobj_obj_cod = foi_obj_cod
								and obj_cod = foi_obj_cod
								and frm_cod = foi_formule_cod
								and frm_type = 4";
            $stmt = $pdo->query($req_comp);
            if ($stmt->rowCount() == 0) {
                $contenu_page .= 'Vous ne possédez aucune peau qui soit tannée et en attente de séchage<br><br>';
            } else {
                $contenu_page .= '<p align="left">Vous êtes en possession de ' . $stmt->rowCount() . ' peaux tannées, en cours de séchage';
                $contenu_page .= '<p align="left"><table>
				<tr>
						<td><strong>Parchemin en cours de réalisation</strong></td>
						<td>Date de début de séchage</td>
						<td border="1" style="border: medium solid #FFFF00"><strong>Date estimée de fin de séchage</strong></td>
						<td><em>Peau en séchage</em></td>
					</tr>';
                while ($result = $stmt->fetch()) {

                    $temps = $result['frm_temps_travail'];
                    $objet = $result['foi_obj_cod'];
                    $req_date = "select to_char(foi_date_crea + '" . $temps . " minutes'::interval,'DD-MM-YYYY / hh24:mi') as date_fin
							from formule_objet_inacheve
								where foi_obj_cod = " . $objet;
                    $stmt2 = $pdo->query($req_date);
                    $result2 = $stmt2->fetch();
                    $contenu_page .= '
					<tr>
						<td><strong>' . $result['gobj_nom_fini'] . '</strong></td>
						<td>' . $result['date_deb'] . '</td>
						<td><strong>' . $result2['date_fin'] . '</strong></td>
						<td><em>' . $result['obj_nom'] . '</em></td>
					</tr>';

                }
                $contenu_page .= '</table>';
            }
            break;
    }
} else {
    $contenu_page .= "<p>Vous ne possédez pas la compétence nécessaire</p>";
}
