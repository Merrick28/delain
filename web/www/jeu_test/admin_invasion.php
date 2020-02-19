<?php 
if(!defined("APPEL"))
    die("Erreur d’appel de page !");

echo '<div class="bordiv" style="padding:0; margin-left: 205px; max-height:20px; overflow:hidden;" id="cadre_invasion">';
echo '<div class="barrTitle" onclick="permutte_cadre(this.parentNode);">Invasion de monstre</div><br />';
$methode = $_REQUEST['methode'];
switch ($methode)
{
	case 'cree_invasion':    // Crée une invasion de monstre
        require "blocks/_admin_invasion.php";


        $texte = "Invasion de $nom_monstre ";
        if ($code_etage == 'tous' && $antres)
        {
            $where = 'WHERE etage_reference != -100';
            $texte .= "dans tous les étages !";
        } else if ($code_etage == 'tous' && !$antres)
        {
			$where = 'WHERE etage_reference != -100 AND etage_numero = etage_reference';
			$texte .= "dans tous les étages principaux !";
		}
		else if ($code_etage != 'tous' && $antres)
		{
			$where = "WHERE etage_reference = $code_etage";

			$req_invasion = "select etage_libelle from etage where etage_numero = $code_etage";
			$stmt = $pdo->query($req_invasion);
			$result = $stmt->fetch();
			$nom_etage = $result['etage_libelle'];
			$texte .= "dans l’étage « $nom_etage » et ses dépendances !";
		}
		else if ($code_etage != 'tous' && !$antres)
		{
			$where = "WHERE etage_numero = $code_etage";

			$req_invasion = "select etage_libelle from etage where etage_numero = $code_etage";
			$stmt = $pdo->query($req_invasion);
			$result = $stmt->fetch();
			$nom_etage = $result['etage_libelle'];
			$texte .= "dans l’étage « $nom_etage » !";
		}
		else	// Just to be on the safe side...
			$where = "WHERE 1 = 0";

		$req_invasion = "select etage_libelle, invasion($code_monstre, etage_numero, $eparpillement, $adapterNiveau) as invasion from etage $where";
		$stmt = $pdo->query($req_invasion);
		echo '<p>Invasion réalisée !</p>';
		while ($result = $stmt->fetch())
		{
			$resultat = $result['invasion'];
			$etage = $result['etage_libelle'];
			echo "<p><strong>Pour l’étage $etage</strong></p><p>$resultat</p>";
		}

		$texte = pg_escape_string($texte);
		$req = "INSERT INTO historique_animations(anim_date, anim_texte, anim_type) values (now()::date, '$texte', 'invasion')";
		$stmt = $pdo->query($req);
	break;
}
echo '<form name="cree_invasion" method="POST" action="#" onsubmit="return confirm(\'Êtes-vous sûr de vouloir lancer cette invasion ?\');">
		<input type="hidden" name="methode" value="cree_invasion" />';
echo '<p>Les invasions de monstre permettent de générer aléatoirement de nombreux monstres dans les souterrains.</p>
			<p>Plusieurs paramètres sont applicables :</p>
			<p> - Type de monstre : c’est bien entendu le type de monstre qui sera créé pour l’invasion.</p>
			<p> - Étage : c’est l’étage qui sera touché par l’invasion. « Tous les étage » touchera l’ensemble des souterrains, à l’exception des étages reliés au Proving Ground. Ces dernier étages sont néanmoins ciblables individuellement.</p>
			<p> - Adapter niveau : permet d’adapter les caractéristiques des monstres créés à l’étage où ils apparaissent. Nécessite de partir d’un monstre adapté aux extérieurs. Très pratique, voire fortement recommandé, si on choisit « Tous les étages ».</p>
			<p> - Inclure les antres : indique si les étages rattachés aux étage principaux sont inclus dans l’invasion (antres, mines, sous-bassements, cathédrales...)</p>
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

$req = 'select gmon_cod, gmon_nom, gmon_niveau from monstre_generique order by gmon_niveau, gmon_nom';
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
	echo '<option value="' . $result['gmon_cod'] . '">' . $result['gmon_nom'] . ' (Niv. ' . $result['gmon_niveau'] . ' )</option>';
}
echo '</select></td>';
echo '<td class="soustitre2">
		<select name="etage">
			<option value="tous">Tous les étages !</option>
	';

$req = "select case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle, etage_numero from etage order by etage_reference desc, etage_numero";
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
	echo '<option value="' . $result['etage_numero'] . '">' . $result['etage_libelle'] . '</option>';
}
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
require "blocks/_admin_distrib_invasion.php";
