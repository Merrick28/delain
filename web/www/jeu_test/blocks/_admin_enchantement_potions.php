<?php
$verif_connexion::verif_appel();
if ($pot != null)
{
    $req .= 'and gobj_cod = ' . $pot;
}
$req  .= 'order by gobj_nom';
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
    echo '<option value="' . $result['gobj_cod'] . '"> ' . $result['gobj_nom'] . '</option>';
}
echo '</select><br>'; ?>
</td>
</tr>
<tr>
    <td class="soustitre2">Nombre de composants produits <em>(Non utilisé pour l'instant)</em></td>
    <td><input type="text" name="nombre" value="1"></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" class="test" value="Valider"></td>
</tr>
</form>
</table>