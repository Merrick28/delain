<?php
$verif_connexion = new verif_connexion();
$verif_connexion::verif_appel();
$pdo = new bddpdo();


echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Pochettes surprises</div><br />';
$methode = $_REQUEST['methode'];
switch ($methode)
{
    case 'pochette_suppression':    // Suppression des pochettes existantes
        $req_sup = 'select f_del_objet(obj_cod) as nombre from objets where obj_gobj_cod = 642';
        $stmt    = $pdo->query($req_sup);
        $all     = $stmt->fetchAll();

        echo '<p>Suppression des pochettes effectuée. ' . count($all) . ' pochettes supprimées.</p>';

        $log = date("d/m/y - H:i") . "\tCompte $compt_cod a supprimé toutes les pochettes surprises.\n";
        writelog($log, 'animation_pochettes');
        break;
    case 'pochette_distribution':    // Réinitialisation des compteurs et distribution de nouvelles pochettes
        $req    = 'select cree_pochette_surprise() as resultat';
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        echo '<p>Compteurs réinitialisés, pochettes créées. ' . $result['resultat'] . '</p>';
        $req  =
            "INSERT INTO historique_animations(anim_date, anim_texte, anim_type) values (now()::date, :texte, 'pochettes')";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":texte" => $_POST['texte']), $stmt);
        $log =
            date("d/m/y - H:i") . "\tCompte $compt_cod a distribué des pochettes surprises à tous les aventuriers à l’occasion de $texte.\n";
        writelog($log, 'animation_pochettes');
        break;
}

echo '<table><tr><td class="titre"><strong>Fonctionnement générique</strong></td>
			<td class="titre"><strong>Historique des distributions</strong></td>
			<td class="titre"><strong>Actions</strong></td></tr>
		<tr><td style="padding:2px; width:30%"><p>Les pochettes surprises sont des documents donnés à chaque personnage. Les personnages peuvent les apporter dans un bâtiment administratif, où ils leurs seront échangés contre un cadeau, parmi :</p>
		<ul><li>- Trois runes</li><li>- Une rune et deux composants de forgeamagie</li><li>- Une rune et un œuf de basilic</li><li>- Une rune et deux parchemins</li><li>- Une rune et deux potions</li><li>- Une rune et de 5000 à 9000 brouzoufs</li></ul>
		<p>Suite à une distribution, un aventurier ne peut ouvrir qu’une seule pochette. Typiquement, les pochettes sont distribuées lors des fêtes de fin d’année (fêtes de Léno dans le jeu).</p></td>';
echo '<td style="padding:2px; width:30%"><p>(les distributions sont enregistrées depuis début 2012)</p><ul>';

$req =
    'SELECT to_char(anim_date,\'DD/MM/YYYY\') as date, anim_texte, (now()::date - anim_date) as duree FROM historique_animations WHERE anim_type=\'pochettes\' ORDER BY anim_date';
$stmt    = $pdo->query($req);
$derniere_distrib = -1;
while ($result = $stmt->fetch())
{
    echo '<li>' . $result['date'] . ' : ' . $result['anim_texte'] . '</li>';
    $derniere_distrib = $result['duree'];
}
echo '</ul></td>';

echo '<td style="padding:2px; width:30%">';
if ($derniere_distrib < 0)
    echo '<p>Aucune distribution n’a encore été enregistrée</p>';
else
    echo '<p>La dernière distribution a eu lieu il y a ' . $derniere_distrib . ' jours</p>';
?>

<p>
<form name="pochette_suppression" method="POST" action="#"
      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer les pochettes existantes ?');">
    <input type="hidden" name="methode" value="pochette_suppression"/>
    <input type="submit" value="Supprimer les pochettes existantes" class="test"/>
</form></p>
<p>
<form name="pochette_distribution" method="POST" action="#"
      onsubmit="return confirm('Êtes-vous sûr de vouloir distribuer de nouvelles pochettes ?');">
    <input type="hidden" name="methode" value="pochette_distribution"/>
    <br/><br/><strong>Nouvelle distribution de pochettes</strong><br/>
    Nom de l’occasion : <input type="text" name="texte" value="Léno..."/><br/>
    <input type="submit" value="Distribuer les nouvelles pochettes" class="test"/>
</form></p>
</td></tr></table>
</div>
