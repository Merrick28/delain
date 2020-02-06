<?php
$pdo = new bddpdo;
if (!defined("APPEL"))
{
    die("Erreur d’appel de page !");
}


echo '<div class="bordiv" style="padding:0; margin-left: 205px; ">';
echo '<div class="barrTitle">Invasion de monstre</div><br />';

switch ($methode)
{
    case 'cree_invasion':    // Crée une invasion de monstre
        $code_monstre  = $_POST['code_monstre'];
        $code_etage    = $_POST['etage'];
        $adapterNiveau = (isset($_POST['adapter'])) ? 'true' : 'false';
        $antres        = (isset($_POST['antres']));
        $eparpillement = $_POST['eparpillement'];
        $where         = '';
        $req_invasion  = "select gmon_nom from monstre_generique where gmon_cod = $code_monstre";
        $stmt          = $pdo->prepare($req_invasion);
        $stmt          = $pdo->execute(array(":montre" => $code_monstre), $stmt);
        $result        = $stmt->fetch();
        $nom_monstre   = $result['gmon_nom'];
        $texte         = "Invasion de $nom_monstre, à raison d’un monstre pour $eparpillement cases, ";
        if ($code_etage == 'tous' && $antres)
        {
            $where = 'WHERE etage_reference != -100';
            $texte .= "dans tous les étages !";
        } else if ($code_etage == 'tous' && !$antres)
        {
            $where = 'WHERE etage_reference != -100 AND etage_numero = etage_reference';
            $texte .= "dans tous les étages principaux !";
        } else if ($code_etage != 'tous' && $antres)
        {
            $where        = "WHERE etage_reference = $code_etage";
            $req_invasion = "select etage_libelle from etage where etage_numero = :etage ";
            $stmt         = $pdo->prepare($req_invasion);
            $stmt         = $pdo->execute(array(":etage" => $code_etage), $stmt);
            $result       = $stmt->fetch();
            $nom_etage    = $result['etage_libelle'];
            $texte        .= "dans l’étage « $nom_etage » et ses dépendances !";
        } else if ($code_etage != 'tous' && !$antres)
        {
            $where        = "WHERE etage_numero = $code_etage";
            $req_invasion = "select etage_libelle from etage where etage_numero = :etage";
            $stmt         = $pdo->prepare($req_invasion);
            $stmt         = $pdo->execute(array(":etage" => $code_etage), $stmt);
            $result       = $stmt->fetch();
            $nom_etage    = $result['etage_libelle'];
            $texte        .= "dans l’étage « $nom_etage » !";
        } else    // Just to be on the safe side...
        {
            $where = "WHERE 1 = 0";
        }

        $req_etage    = "select etage_libelle, etage_numero from etage $where order by etage_reference, etage_numero";
        $stmt         = $pdo->query($req_etage);
        $req_invasion =
            "select 'admin_longue_requete', invasion(:code_monstre, :etage_numero, :eparpillement, :adapterNiveau) as invasion";
        $stmt2        = $pdo->query($req_invasion);
        echo '<p>Invasion réalisée !</p>';
        while ($result = $stmt->fetch())
        {
            $etage_nom    = $result['etage_libelle'];
            $etage_numero = $result['etage_numero'];

            // garder le 'admin_longue_requete', il permet à la purge SQL d’identifier
            // cette requête comme devant être tuée après 5 minutes au lieu d’une seule
            $stmt2    = $pdo->execute(array(
                                          ":code_monstre"  => $code_monstre,
                                          ":etage_numero"  => $etage_numero,
                                          ":eparpillement" => $eparpillement,
                                          ":adapterNiveau" => $adapterNiveau
                                      ), $stmt2);
            $restult2 = $stmt2->fetch();
            $resultat = $result2['invasion'];
            echo "<p><strong>Pour l’étage $etage_nom</strong></p><p>$resultat</p>";
        }

        $texte = pg_escape_string($texte);
        $req   =
            "INSERT INTO historique_animations(anim_date, anim_texte, anim_type) values (now()::date, '$texte', 'invasion')";
        $db->query($req);

        $log = date("d/m/y - H:i") . "\tCompte $compt_cod a généré une $texte\n";
        writelog($log, 'animation_invasion');
        break;
}
echo '<form name="cree_invasion" method="POST" action="#" onsubmit="return confirm(\'Êtes-vous sûr de vouloir lancer cette invasion ?\');">
		<input type="hidden" name="methode" value="cree_invasion" />';
echo '<p>Les invasions de monstre permettent de générer aléatoirement de nombreux monstres dans les souterrains.</p>
			<p>Plusieurs paramètres sont applicables :</p>
			<p> - Type de monstre : c’est bien entendu le type de monstre qui sera créé pour l’invasion.</p>
			<p> - Étage : c’est l’étage qui sera touché par l’invasion. « Tous les étages » touchera l’ensemble des souterrains, à l’exception des étages reliés au Proving Ground. Ces derniers étages sont néanmoins ciblables individuellement.</p>
			<p> - Adapter niveau : permet d’adapter les caractéristiques des monstres créés à l’étage où ils apparaissent. Nécessite de partir d’un monstre adapté aux extérieurs. Très pratique, voire fortement recommandé, si on choisit « Tous les étages ».</p>
			<p> - Inclure les antres : indique si les étages rattachés aux étages principaux sont inclus dans l’invasion (antres, mines, sous-bassements, cathédrales...)</p>
			<p> - Éparpillement : le nombre de cases de l’étage pour un monstre. Une valeur de 50 signifie qu’on créera un monstre pour 50 cases (hors murs). Pour un étage standard (1600 cases), une valeur de 50 ajoute donc 32 monstres. </p>
			<p>Les paramètres donnés par défaut (sauf pour le type de monstre...) donnent une invasion légère (par exemple utilisée par le passé lors des animations de pâques...).</p>
	<table>
		<tr><td class="titre"><strong>Type de monstre</strong></td>
		<td class="titre"><strong>Étage(s)</strong></td>
		<td class="titre"><strong>Options</strong></td>
		<td class="titre"><strong>Lancer l’invasion ?</strong></td></tr>
		<tr>
			<td class="soustitre2">
			<select name="code_monstre">';

$req = 'select gmon_cod, gmon_nom, gmon_niveau from monstre_generique order by gmon_nom';
$db->query($req);
while ($db->next_record())
{
    echo '<option value="' . $db->f('gmon_cod') . '">' . $db->f('gmon_nom') . ' (Niv. ' . $db->f('gmon_niveau') . ' )</option>';
}
echo '</select></td>';
echo '<td class="soustitre2">
		<select name="etage">
			<option value="tous">Tous les étages !</option>
	';

echo $html->etage_select();

echo '</select></td>';
echo '<td class="soustitre2">
	<input type="checkbox" name="adapter" id="adapter" checked="checked" /><label for="adapter">Adapter le niveau suivant l’étage ?</label><br />
	<input type="checkbox" name="antres" id="antres" checked="checked" /><label for="antres">Inclure les antres reliées à l’étage ?</label><br />
	<label for="eparpillement">Éparpillement : 1 monstre pour </label><input type="text" name="eparpillement" id="eparpillement" value="50" style="width:20px;" /><label for="eparpillement"> cases.</label>
	</td>';
echo '<td class="soustitre2">
	<input type="submit" value="Lancer l’invasion !" class="test" />
	</td></tr>';
echo '</table></form>';
echo "<p><strong>Historique des invasions :</strong> (les distributions sont enregistrées depuis fin 2012)</p><ul>";

$req =
    'SELECT to_char(anim_date,\'DD/MM/YYYY\') as date, anim_texte, (now()::date - anim_date) as duree FROM historique_animations WHERE anim_type=\'invasion\' ORDER BY anim_date';
$db->query($req);
$derniere_distrib = -1;
while ($db->next_record())
{
    echo '<li>' . $db->f('date') . ' : ' . $db->f('anim_texte') . '</li>';
    $derniere_distrib = $db->f('duree');
}
echo '</ul>';
echo '</div>';

