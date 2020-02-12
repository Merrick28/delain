<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 20/12/18
 * Time: 13:17
 */
//
// on regarde si l'objet est bien enchantable, et quels enchantements on peut lui associer
//
$req = 'select gobj_tobj_cod,gobj_distance
				from objet_generique,objets
				where obj_cod = ' . $obj . '
				and obj_gobj_cod = gobj_cod ';
$stmt = $pdo->query($req);
$result = $stmt->fetch();
switch ($result['gobj_tobj_cod'])
{
    case 1:    // arme
        if ($result['gobj_distance'] == 'O')    //arme distance
            $app_req = ' where tenc_arme_distance = 1 ';
        else    // arme contact
            $app_req = ' where tenc_arme_contact = 1 ';
        break;
    case 2:    // armure
        $app_req = ' where tenc_armure = 1 ';
        break;
    case 4:    // casque
        $app_req = ' where tenc_casque = 1 ';
        break;
    case 6:    //artefact
        $app_req = ' where tenc_artefact = 1 ';
        break;
    default: // Le reste
        $app_req = ' where false ';
}
//
// Sur la requête suivante, si on veut afficher tous les enchantements disponibles, il faut supprimer la ligne
// avec la fonction obj_enchantement
//
$req = 'select enc_cod,enc_nom,enc_description,enc_cout,enc_cout_pa
				from enc_type_objet,enchantements' . $app_req . '
				and tenc_enc_cod = enc_cod
				and obj_enchantement(' . $perso_cod . ',enc_cod,' . $obj . ') = 1 ';
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
    $contenu_page .= 'Non, désolé, je ne peux rien faire avec ce que vous avez en inventaire. Il vous faut trouver d\'autres matériaux afin que je puisse enchanter cet objet.
													<br>Le forgeamage demande certes de l\'expertise, mais ausis d\'avoir les objets nécessaires pour cela.';
else
{
    $contenu_page .= 'Voici ce que nous pouvons tenter de faire avec ça :
				<table>
					<tr>
						<td class="soustitre2"><strong>Nom</strong></td>
						<td class="soustitre2"><strong>Description</strong></td>
						<td class="soustitre2"><strong>Cout</strong></td>
						<td class="soustitre2"><strong>Nécessite</strong></td>
					</tr>';
    while ($result = $stmt->fetch())
    {
        $contenu_page .= '<tr>
						<td class="soustitre2"><a href="action.php?methode=enc&enc=' . $result['enc_cod'] . '&obj=' . $obj . '&type_appel=' . $type_appel . '">' . $result['enc_nom'] . '</a></td>
						<td>' . $result['enc_description'] . '</td>
						<td class="soustitre2">' . $result['enc_cout'] . ' brouzoufs - ' . $result['enc_cout_pa'] . ' PA</td>
						<td>';
        $req = 'select gobj_nom,oenc_nombre
						from enc_objets,objet_generique
						where oenc_enc_cod = ' . $result['enc_cod'] . '
						and oenc_gobj_cod = gobj_cod ';
        $stmt2 = $pdo->query($req);
        while ($result2 = $stmt2->fetch())
            $contenu_page .= $result2['oenc_nombre'] . ' ' . $result2['gobj_nom'] . '<br>';
        $contenu_page .= '</td></tr>';
        //$contenu_page .= '<br><a href="' . $PHP_SELF . '?methode=enc2&enc=' . $result['enc_cod'] . '&obj=' . $obj . '">' . $result['enc_nom'] . '</a>';
    }
    $contenu_page .= '</table>';
}
