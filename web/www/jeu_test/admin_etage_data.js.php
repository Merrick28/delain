<?php header("Content-type: text/javascript");

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/images_delain.php';

if (!isset($num_etage) || $num_etage === '')
{
    die ('alert("Erreur ! Aucun étage déclaré !")');
}

// Données générales de l’étage
$req_etage =
    "SELECT MIN(pos_x) as minx, MIN(pos_y) as miny, MAX(pos_x) as maxx, MAX(pos_y) as maxy from positions where pos_etage = $num_etage";
$pdo->query($req_etage);
if (!$result = $stmt->fetch())
	die ('alert("Erreur ! Étage inconnu !")');
?>
Etage.minX = <?php echo $result['minx']; ?>;
Etage.maxX = <?php echo $result['maxx']; ?>;
Etage.minY = <?php echo $result['miny']; ?>;
Etage.maxY = <?php echo $result['maxy']; ?>;
Etage.numero = <?php echo $num_etage; ?>;

<?php 
// Type d’étage
$req_style = "select etage_affichage from etage where etage_numero = $num_etage";
$etage = new etage();
$etage->charge($num_etage);
$style = $etage->etage_affichage;
?>
Etage.style = "<?php echo $style; ?>";

<?php // Détail des cases
$req_cases = "select pos_decor, pos_cod, pos_x, pos_y, pos_type_aff, coalesce(mur_type, 0) as mur_type, pos_decor_dessus, pos_passage_autorise, pos_pvp, pos_entree_arene,
        coalesce(mur_tangible, 'N') as mur_tangible, coalesce(mur_creusable, 'N') as mur_creusable
	from positions
	left outer join murs on mur_pos_cod = pos_cod
	where pos_etage = $num_etage 
	order by pos_y desc, pos_x";
$pdo->query(req_cases);
$i = 0;
while($result = $stmt->fetch())
{
	$pos_cod = $result['pos_cod'];
	$pos_x = $result['pos_x'];
	$pos_y = $result['pos_y'];
	$mur_type = $result['mur_type'];
	$pos_decor = $result['pos_decor'];
	$pos_decor_dessus = $result['pos_decor_dessus'];
	$pos_passage_autorise = ($result['pos_passage_autorise'] == 1) ? 'true' : 'false';
	$pos_pvp = ($result['pos_pvp'] == 'O') ? 'true' : 'false';
	$entree_arene = ($result['pos_entree_arene'] == 'O') ? 'true' : 'false';
	$mur_tangible = ($result['mur_tangible'] == 'O') ? 'true' : 'false';
	$mur_creusable = ($result['mur_creusable'] == 'O') ? 'true' : 'false';
	$pos_type_aff = $result['pos_type_aff'];
	echo "Etage.Cases[$i] = { id: $pos_cod, x: $pos_x, y: $pos_y, mur: $mur_type, decor: $pos_decor, decor_dessus: $pos_decor_dessus, fond: $pos_type_aff, passage: $pos_passage_autorise, pvp: $pos_pvp, entree_arene: $entree_arene, tangible: $mur_tangible, creusable: $mur_creusable };\n";
	$i++;
}

// Images de murs
$tab_murs = images_delain::Murs($style);
echo "Murs.donnees[0] = { id: 0 };\n";
$i = 1;
foreach ($tab_murs as $unMur)
{
	$numero = $unMur[0];
	echo "Murs.donnees[$i] = { id: $numero };\n";
	$i++;
}

// Images de fonds
$tab_fonds = images_delain::Fonds($style);
$i = 0;
foreach ($tab_fonds as $unFond)
{
	$numero = $unFond[0];
	echo "Fonds.donnees[$i] = { id: $numero };\n";
	$i++;
}

// Images de décors
$tab_decors = images_delain::Decors();
echo "Decors.donnees[0] = { id: 0 };\n";
$i = 1;
foreach ($tab_decors as $unDecor)
{
	$numero = $unDecor[0];
	echo "Decors.donnees[$i] = { id: $numero };\n";
	$i++;
}

?>
