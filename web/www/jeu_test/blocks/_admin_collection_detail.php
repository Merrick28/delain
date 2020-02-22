<?php
$verif_connexion::verif_appel();
$req         = 'select gobj_cod, gobj_tobj_cod, gobj_nom from objet_generique order by gobj_tobj_cod, gobj_nom';
$stmt        = $pdo->query($req);
$script_gobj = '';
while ($result = $stmt->fetch())
{
    $clef        = $result['gobj_cod'];
    $clef_tobj   = $result['gobj_tobj_cod'];
    $valeur      = $result['gobj_nom'];
    $script_gobj .= "tableauObjets[$clef_tobj][$clef] = \"" . str_replace('"', '', $valeur) . "\";\n";
}

echo '</select></td><td>L’objet que les participants devront collectionner (sélectionnez d’abord un type d’objet, puis un objet).</td></tr>';
