<?php
$offset = 3600; // on remet à jour la feuille de style une fois par heure.
$expire = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($expire);
header("Content-type: text/css");


$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
include_once '../includes/images_delain.php';

$num_etage = get_request_var('num_etage', '');

if (!isset($num_etage) || $num_etage === '')
{
    $req_etage = "select etage_affichage, etage_numero from perso_position,positions,etage ";
    $req_etage = $req_etage . "where ppos_perso_cod = $perso_cod ";
    $req_etage = $req_etage . "and ppos_pos_cod = pos_cod ";
    $req_etage = $req_etage . "and pos_etage = etage_numero ";
    $stmt      = $pdo->query($req_etage);
    $result    = $stmt->fetch();
    $etage     = $result['etage_affichage'];
	$num_etage = $result['etage_numero'];
}
else
{
	$req_etage = "select etage_affichage from etage where etage_numero = $num_etage ";
	$stmt = $pdo->query($req_etage);
	$result = $stmt->fetch();
	$etage = $result['etage_affichage'];
}
?>

.caseVue{
	width: 28px;
	height: 28px;
	border: 0px;
	padding:0px;
	margin:0px;
}
.pinceau {
	width: 28px;
	height: 28px;
	border: lightblue 1px solid;
	margin: 0px;
	float: left;
	padding: 0px;
}
.pinceausurligne {
	background-color: lightblue;
	border: red 1px solid;
	opacity: 0.5;
}
.radarVierge {
	width: 14px; height: 14px;
	background-color: white;
	float: left;
	color: grey;
	font-size: 0.75em;
	cursor: pointer;
}
.radarSelectionne { background-color: lightblue; }
.radarSurvole { background-color: lightblue; opacity: 0.5; }
.radarDebutLigne { clear: left; }
.radarGauche { border-left: solid 1px #aaa; }
.radarHaut { border-top: solid 1px #aaa; }
.radarDroite { border-right: solid 1px #aaa; }
.radarBas { border-bottom: solid 1px #aaa; }
.caseFond { float: left; }
.etageSurligne {
	background-color: lightblue;
	border: red 1px solid;
	margin: -1px -1px -1px -1px;
	opacity: 0.5;
}
.murSimple { background: black; }
.pasMurSimple { background: lightgreen; }
.pinceauOnOffJoli { opacity: 0.60; }
.pinceauOn { background: lightgreen; text-align:center; font-size:9px; }
.pinceauOff { background: pink; text-align:center; font-size:9px; }
.pinceauOn.murSimple { background: darkgreen; }
.pinceauOff.murSimple{ background: darkred; }
.pinceauOn.pasMurSimple { background: lightgreen; }
.pinceauOff.pasMurSimple{ background: pink; }

.horseBlink {
    animation: blinkingBackGroundHorse 2s infinite;
}
@keyframes blinkingBackGroundHorse{
    0% {
        background-image: url(/images/interface/horse-w.png);
        background-repeat: no-repeat;
        background-position-x: 8px;
        background-position-y: 8px;
    }
    100% {
        background-image: url(/images/interface/horse-b.png);
        background-repeat: no-repeat;
        background-position-x: 8px;
        background-position-y: 8px;

    }
}

<?php 
if (empty($source) || ($source != 'bdd' && $source != 'fichiers'))
	$source = 'bdd';

// Plutôt que de chercher tous les styles possibles et imaginables, on se contente de ceux de l'étage en cours
// Cela implique de bien mettre ?num_etage=.. dans l'appel (sinon, gare aux effets de cache et de changement d'étage)
// La feuille de style sera ainsi plus légère, et dédiée à un étage.

// FOND DE LA CASE
if ($source == 'bdd')
{
	$req_styles = "select distinct pos_type_aff from positions where pos_etage = $num_etage";
	$stmt = $pdo->query($req_styles);
	$arr_styles = array();
	while($result = $stmt->fetch())
	{
		$cpt = $result['pos_type_aff'];
		echo ".v$cpt { background-image: url('" . G_IMAGES . "f_" , $etage, "_" , $cpt, ".png'); }\n";
	}
}
else
{
	// Images de fonds
	$tab_fonds = images_delain::Fonds($etage);
	foreach ($tab_fonds as $unFond)
	{
		$numero = $unFond[0];
		$image = $unFond[1];
		echo ".v$numero { background-image: url('" , G_IMAGES , $image, "'); }\n";
	}
}

// LIEUX
$req_styles = "select distinct tlieu_cod from lieu_type";
$stmt = $pdo->query($req_styles);
while ($result = $stmt->fetch())
{
	$cpt = $result['tlieu_cod'];
	echo ".lieu$cpt{ background-image:url('" . G_IMAGES . "t_" , $cpt, "_lie.png'); }\n";
}

// DECORS
?>
.decor0 { background: none; }
<?php if ($source == 'bdd')
{
	$req_styles = "select distinct pos_decor from positions where pos_etage = $num_etage
		UNION
		select distinct pos_decor_dessus from positions where pos_etage = $num_etage";
	$stmt = $pdo->query($req_styles);
	while ($result = $stmt->fetch())
	{
		$cpt = $result['pos_decor'];
		if ($cpt != 0)
			echo ".decor$cpt { background-image:url('" . G_IMAGES . "dec_" , $cpt, ".gif'); }\n";
	}
}
else
{
	// Images de décors
	$tab_decors = images_delain::Decors();
	foreach ($tab_decors as $unDecor)
	{
		$numero = $unDecor[0];
		$image = $unDecor[1];
		echo ".decor$numero { background-image: url('" , G_IMAGES , $image, "'); }\n";
	}
}

echo ".joueur{ background-image:url('" . G_IMAGES . "t_" , $etage, "_per.png'); }\n";
echo ".br{ background-image:url('" . $type_flux.G_URL . "/images/br.png'); }\n";
echo ".monstre{ background-image:url('" . G_IMAGES . "t_" , $etage, "_enn.png'); }\n";
echo ".objet{ background-image:url('" . G_IMAGES . "t_" , $etage, "_obj.png'); }\n";

// MURS
?>
.mur_0 { background: none; }
.mur_-1 { background-image:url('/images/remove.png'); }
<?php if ($source == 'bdd')
{
	$req_styles = "select distinct mur_type from murs
		inner join positions on pos_cod = mur_pos_cod
		where pos_etage = $num_etage";
	$stmt = $pdo->query($req_styles);
	while ($result = $stmt->fetch())
	{
		$cpt = $result['mur_type'];
		echo ".mur_$cpt{ background-image: url('" . G_IMAGES . "t_" , $etage, "_mur_" , $cpt, ".png'); visibility: visible; }\n";
	}
}
else
{
	// Images de murs
	$tab_murs = images_delain::Murs($etage);
	foreach ($tab_murs as $tab_murs)
	{
		$numero = $tab_murs[0];
		$image = $tab_murs[1];
		echo ".mur_$numero { background-image: url('" , G_IMAGES , $image, "'); }\n";
	}
}
?>
.lock{ background-image: url('<?php echo G_IMAGES?>fight.gif'); }
.main{ cursor:hand; } 
.invisible{	background:url('<?php echo G_IMAGES?>del.gif'); }
.oncase{ background:url('<?php echo G_IMAGES?>sur_case.gif'); }
.vu{
	visibility:visible;
	background-image:url('<?php echo G_IMAGES?>c1.gif');
	transition: opacity 500ms ease-in-out, background-color 500ms ease-in-out;
}
.pasvu{
	visibility:hidden;
	background-image:url('<?php echo G_IMAGES?>c1.gif');
	transition: opacity 500ms ease-in-out, background-color 200ms ease-in-out;
}
