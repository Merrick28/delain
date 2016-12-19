<?php 
if(!DEFINED("APPEL"))
	die("Erreur d'appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px; max-height:20px; overflow:hidden;" id="cadre_pochettes">';
echo '<div class="barrTitle" onclick="permutte_cadre(this.parentNode);">Pochettes surprises</div><br />';

switch ($methode)
{
	case 'pochette_suppression':	// Suppression des pochettes existantes
		$req_sup = 'select f_del_objet(obj_cod) as nombre from objets where obj_gobj_cod = 642';
		$db->query($req_sup);
		echo '<p>Suppression des pochettes effectuée. ' . $db->nf() . ' pochettes supprimées.</p>';
	break;
	case 'pochette_distribution':	// Réinitialisation des compteurs et distribution de nouvelles pochettes
		$req = 'select cree_pochette_surprise() as resultat';
		$db->query($req);
		$db->next_record();
		echo '<p>Compteurs réinitialisés, pochettes créées. ' . $db->f('resultat') . '</p>';
		$texte = pg_escape_string($_POST['texte']);
		$req = "INSERT INTO historique_animations(anim_date, anim_texte, anim_type) values (now()::date, '$texte', 'pochettes')";
		$db->query($req);
	break;
}

echo '<table><tr><td class="titre"><b>Fonctionnement générique</b></td>
			<td class="titre"><b>Historique des distributions</b></td>
			<td class="titre"><b>Actions</b></td></tr>
		<tr><td style="padding:2px; width:30%"><p>Les pochettes surprises sont des documents donnés à chaque personnage. Les personnages peuvent les apporter dans un bâtiment administratif, où ils leurs seront échangés contre un cadeau, parmi :</p>
		<ul><li>- Trois runes</li><li>- Une rune et deux composants de forgeamagie</li><li>- Une rune et un œuf de basilic</li><li>- Une rune et deux parchemins</li><li>- Une rune et deux potions</li><li>- Une rune et de 5000 à 9000 brouzoufs</li></ul>
		<p>Suite à une distribution, un aventurier ne peut ouvrir qu’une seule pochette. Typiquement, les pochettes sont distribuées lors des fêtes de fin d’année (fêtes de Léno dans le jeu).</p></td>';
echo '<td style="padding:2px; width:30%"><p>(les distributions sont enregistrées depuis début 2012)</p><ul>';

$req = 'SELECT to_char(anim_date,\'DD/MM/YYYY\') as date, anim_texte, (now()::date - anim_date) as duree FROM historique_animations WHERE anim_type=\'pochettes\' ORDER BY anim_date';
$db->query($req);
$derniere_distrib = -1;
while ($db->next_record())
{
	echo '<li>' . $db->f('date') . ' : ' . $db->f('anim_texte') . '</li>';
	$derniere_distrib = $db->f('duree');
}
echo '</ul></td>';

echo '<td style="padding:2px; width:30%">';
if ($derniere_distrib < 0)
	echo '<p>Aucune distribution n’a encore été enregistrée</p>';
else
	echo '<p>La dernière distribution a eu lieu il y a ' . $derniere_distrib . ' jours</p>';
?>

		<p><form name="pochette_suppression" method="POST" action="#" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer les pochettes existantes ?');">
			<input type="hidden" name="methode" value="pochette_suppression" />
			<input type="submit" value="Supprimer les pochettes existantes" class="test" />
		</form></p>
		<p><form name="pochette_distribution" method="POST" action="#" onsubmit="return confirm('Êtes-vous sûr de vouloir distribuer de nouvelles pochettes ?');">
			<input type="hidden" name="methode" value="pochette_distribution" />
			<br /><br /><b>Nouvelle distribution de pochettes</b><br />
			Nom de l’occasion : <input type="text" name="texte" value="Léno..." /><br />
			<input type="submit" value="Distribuer les nouvelles pochettes" class="test" />
		</form></p>
	</td></tr></table>
</div>
