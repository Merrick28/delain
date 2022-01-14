<?php
$verif_connexion::verif_appel();

echo '<p>Les médaillons permettent, une fois apportés sur un escalier, l’ouverture de tous les escaliers menant au -5. Ces médaillons se trouvent dans les antres du -4 (Serpent, Loup et Scorpion).</p><table>
		<tr>
		<td class="titre"><strong>Médaillon</strong></td>
        <td class="titre"><strong>Localisation</strong></td>
        <td class="titre"><strong>Redistribuer ?</strong></td></tr>';
$req  = 'select obj_cod, obj_nom, trouve_objet(obj_cod) as emplacement from objets where obj_gobj_cod in (86, 87, 88)';
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
    echo '<tr><td style="padding:2px; width:30%"><p>' . $result['obj_nom'] . '</p></td><td style="padding:2px; width:30%"><p>' . $result['emplacement'] . '</p></td>';
    echo '<td style="padding:2px; width:30%"><form name="medaillon_redistribution" method="POST" action="#" onsubmit="return confirm(\'Êtes-vous sûr de vouloir redistribuer ce médaillon ?\');">
    		<input type="hidden" name="methode" value="medaillon_redistribution" />
    		<input type="hidden" name="obj_cod" value="' . $result['obj_cod'] . '" />
    		<input type="submit" value="Redistribuer ce médaillon" class="test" />
		</form></td></tr>';
}
echo '</table></div>';