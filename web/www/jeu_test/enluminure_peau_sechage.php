<?php
$is_enlumineur = $db->is_enlumineur($perso_cod);
$db2 = new base_delain;
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
            $db->query($req_comp);
            if ($db->nf() == 0) {
                $contenu_page .= 'Vous ne possédez aucune peau qui soit tannée et en attente de séchage<br><br>';
            } else {
                $contenu_page .= '<p align="left">Vous êtes en possession de ' . $db->nf() . ' peaux tannées, en cours de séchage';
                $contenu_page .= '<p align="left"><table>
				<tr>
						<td><b>Parchemin en cours de réalisation</b></td>
						<td>Date de début de séchage</td>
						<td border="1" style="border: medium solid #FFFF00"><b>Date estimée de fin de séchage</b></td>
						<td><i>Peau en séchage</i></td>
					</tr>';
                while ($db->next_record()) {

                    $temps = $db->f("frm_temps_travail");
                    $objet = $db->f("foi_obj_cod");
                    $req_date = "select to_char(foi_date_crea + '" . $temps . " minutes'::interval,'DD-MM-YYYY / hh24:mi') as date_fin
							from formule_objet_inacheve
								where foi_obj_cod = " . $objet;
                    $db2->query($req_date);
                    $db2->next_record();
                    $contenu_page .= '
					<tr>
						<td><b>' . $db->f("gobj_nom_fini") . '</b></td>
						<td>' . $db->f("date_deb") . '</td>
						<td><b>' . $db2->f("date_fin") . '</b></td>
						<td><i>' . $db->f("obj_nom") . '</i></td>
					</tr>';

                }
                $contenu_page .= '</table>';
            }
            break;
    }
} else {
    $contenu_page .= "<p>Vous ne possédez pas la compétence nécessaire</p>";
}
