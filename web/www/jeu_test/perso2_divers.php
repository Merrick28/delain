<?php 
//CETTE PAGE REGROUPE LES DONNÉES DIVERSES
$param = new parametres();

//
// données tutorat
//
$contenu_page .= '<p class="titre">Tutorat</p><p>';
$tutorat = false;
// on regarde si la personne est 'tutorée'
$req = 'select perso_cod,perso_nom
	from perso,tutorat
	where tuto_filleul = ' . $perso_cod . '
	and tuto_tuteur = perso_cod';
$stmt = $pdo->query($req);
if($stmt->rowCount() != 0)
{
	$result = $stmt->fetch();
	$contenu_page .= 'Votre tuteur est <a href="visu_desc_perso.php?visu=' . $result['perso_cod'] . '">' . $result['perso_nom'] . '</a><br />';
}
$req = 'select perso_niveau,perso_tuteur 
	from perso
	where perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req);
$result = $stmt->fetch();
if($result['perso_niveau'] >= 15)
{
	if($result['perso_tuteur'] == 'f')
	{
		$contenu_page .= 'Vous n’êtes pas inscrit(e) en tant que tuteur. <a href="tuteur.php?methode=inscr">Cliquez ici pour vous inscrire.</a><br />';
	}
	else
	{
		$contenu_page .= 'Vous êtes inscrit(e) en tant que tuteur. <a href="tuteur.php?methode=desabo">Cliquez ici pour vous désinscrire.</a><br />';
		$req = 'select perso_nom,perso_cod
			from perso,tutorat
			where tuto_tuteur = ' . $perso_cod . '
			and tuto_filleul = perso_cod';
		$stmt = $pdo->query($req);
		if($stmt->rowCount() == 0)
		{
			$contenu_page .= 'Vous n’avez aucun filleul pour le moment.<br />';
		}
		else
		{
			$contenu_page .= 'Liste de vos filleuls :<br />';
			while($result = $stmt->fetch())
			{
				$contenu_page .= '- <a href="visu_desc_perso.php?visu=' . $result['perso_cod'] . '">' . $result['perso_nom'] . '</a><br />';
			}
		}
	}
}
//
// Fin du tutorat
//

//
// Début utilisation PA restants
//
if (isset($ch_util))
{
	$req = "update perso set perso_utl_pa_rest = $ch_util where perso_cod = $perso_cod ";
	$stmt = $pdo->query($req);
}

$req = "select perso_nom,perso_utl_pa_rest from perso where perso_cod = $perso_cod ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();

if ($result['perso_utl_pa_rest'] == 1)
{
	$util = $result['perso_nom'] . " <strong>utilise</strong> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 0;
}
else
{
	$util = $result['perso_nom'] . " <strong>n’utilise pas</strong> ses PA restants pour réduire le temps de tour suivant. ";
	$ch_util = 1;
}
$contenu_page .= '<p class="titre">Utilisation des PA restants</p><p>' . $util . ' <a href="' . $_SERVER['PHP_SELF'] . '?m=6&ch_util=' . $ch_util . '">Changer ?</a></p>';

//
// Fin utilisation PA restants
//

//
// Début Dispensaires
//
$req_temple = "select lieu_nom,pos_x,pos_y,etage_libelle,ptemple_nombre 
									from perso_temple,positions,lieu_position,lieu,etage 
									where ptemple_perso_cod = $perso_cod 
									and ptemple_pos_cod = pos_cod 
									and lpos_pos_cod = pos_cod 
									and lpos_lieu_cod = lieu_cod 
									and pos_etage = etage_numero ";
$stmt = $pdo->query($req_temple);
$nb = $stmt->rowCount();
$contenu_page .= '<p class="titre">Dispensaire et résurrection</p>';

// Affichage Sort de résurection
$pdo    = new bddpdo();
$req    = "select  rpos_pos_cod as resu_pos from perso_resuc where rpos_perso_cod=:perso_cod ";
$stmt   = $pdo->prepare($req);
$stmt   = $pdo->execute(array(':perso_cod' => $perso_cod), $stmt);
$result = $stmt->fetch();
(int)$result["resu_pos"];
$resu_pos = (int)$result["resu_pos"];

// Affichage glyphe de resu-------
$req    = "select  rejoint_glyphe_resurrection(:perso_cod) as glyphe_pos";
$stmt   = $pdo->prepare($req);
$stmt   = $pdo->execute(array(':perso_cod' => $perso_cod), $stmt);
$result = $stmt->fetch();
$glyphe_pos = (int)$result["glyphe_pos"];

if ($resu_pos>0)
{
    $flag_resu = true ;
    $req    = "select etage_libelle, pos_x, pos_y from etage, positions where etage_numero = pos_etage and pos_cod = :pos_cod ";
    $stmt   = $pdo->prepare($req);
    $stmt   = $pdo->execute(array(':pos_cod' => $resu_pos), $stmt);
    $result = $stmt->fetch();
    $contenu_page .= "<p>En cas de mort vous serez ramené par: <strong>Le sort de résurrection.</strong></br><br>";
    $contenu_page .= "<p>Vous êtes lié par un sort de résurrection en <strong>X={$result['pos_x']} Y={$result['pos_y']}</strong> à l’étage <strong>{$result['etage_libelle']}</strong>.</br></br>";
}

if ($glyphe_pos>0) {
    $req = "select etage_libelle, pos_x, pos_y from etage, positions where etage_numero = pos_etage and pos_cod = :pos_cod ";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(':pos_cod' => $glyphe_pos), $stmt);
    $result = $stmt->fetch();
    if ($resu_pos <= 0)
    {
        $contenu_page .= "<p>En cas de mort vous serez ramené par: <strong>Votre glyphe de résurrection.</strong></br><br>";
    }
    $contenu_page .= "<p>Vous avez un glyphe de résurrection en <strong>X={$result['pos_x']} Y={$result['pos_y']}</strong> à l’étage <strong>{$result['etage_libelle']}</strong>.</br></br>";
}

/*
select  rejoint_glyphe_resurrection(4) ;


select etage_libelle, pos_x, pos_y from etage, positions
	where etage_numero = pos_etage
and pos_cod = 4538 ;
*/

if ($nb == 0)
{
	$contenu_page .= '<p>Vous n’avez pas de dispensaire spécifique pour vous ramener en cas de mort.';
}
else
{
	$result = $stmt->fetch();
	
	$contenu_page .= '<table width="100%">
		<tr><td class="soustitre2"><p><strong>Nom</strong></td><td class="soustitre2"><p style="text-align:center;"><strong>X</strong></td><td class="soustitre2"><p style="text-align:center;"><strong>Y</n></td><td class="soustitre2"><p style="text-align:center;"><strong>Etage</strong></td><td class="soustitre2"><p>Probabilité de retour</td></tr>
		<tr><td class="soustitre2"><p><strong>' . $result['lieu_nom'] . '</strong></td>
		<td><p style="text-align:center;">' . $result['pos_x'] . '</td>
		<td><p style="text-align:center;">' . $result['pos_y'] . '</td>
		<td><p style="text-align:center;">' . $result['etage_libelle'] . '</td>';
	$chance = $result['ptemple_nombre'];
	$chance = 100 - ($chance * $param->getparm(32));
	$contenu_page .= '<td><p>' . $chance . ' %</td></tr>
		</table>
		<a href="action.php?methode=abtemp">Abandonner ce dispensaire ?</a> (vous n’aurez plus de dispensaire spécifique pour vous ramener en cas de mort)';
}
//
// Fin Dispensaires
//

//
// Début impalpabilité
//
$req = 'select perso_tangible,perso_nb_tour_intangible from perso where perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req);
$result = $stmt->fetch();
if ($result['perso_tangible'] == 'N')
	$contenu_page .= '<p>Vous êtes impalpable pour ' . $result['perso_nb_tour_intangible'] . ' tours.</p>';

//
// Fin impalpabilité
//

//
// Début Grands escaliers
//
$req = "select pge_lieu_cod,pos_x,pos_y,etage_libelle,lieu_dest
	from perso_grand_escalier,positions,lieu_position,etage,lieu
	where pge_perso_cod = $perso_cod
	and pge_lieu_cod = lpos_lieu_cod
	and lpos_pos_cod = pos_cod 
	and etage_numero = pos_etage
	and pge_lieu_cod not in (2139) 
	and pos_etage > -5 
	and lpos_lieu_cod = lieu_cod ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() != 0)
{
	$contenu_page .= '<p class="titre">Grands escaliers activés</p><p class="soustitre2">Vous avez activé les grands escaliers suivants : </p>';
	while($result = $stmt->fetch())
	{
		$contenu_page .= 'Position : ' . $result['pos_x'] . ', ' . $result['pos_y'] . ', ' . $result['etage_libelle'] . ' (destination : ';
		$req = 'select pos_x,pos_y,etage_libelle from positions,etage
							where pos_cod = ' . $result['lieu_dest'] . ' and pos_etage = etage_numero ';
		$stmt2 = $pdo->query($req);
		$result2 = $stmt2->fetch();
		$contenu_page .= $result2['pos_x'] . ', ' . $result2['pos_y'] . ', ' . $result2['etage_libelle'] . ')<br>';  	
	}
}
//
// Fin Grands escaliers
//

//
// Début Voie magique
//
$req = 'select count (1) as nv5 from perso, perso_nb_sorts_total, sorts where perso_cod = pnbst_perso_cod and pnbst_sort_cod = sort_cod and sort_niveau >= 5 and pnbst_nombre > 0 and perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req); $result = $stmt->fetch();
$nv5 = $result['nv5'];
$req = 'select count(1) as mem from perso_sorts, perso where psort_perso_cod = perso_cod and perso_type_perso = 1 and perso_cod = ' . $perso_cod;
$stmt = $pdo->query($req); $result = $stmt->fetch();
$mem = $result['mem'];
if($nv5 > 0 && $mem > 5)
{
	$contenu_page .= '<p class="titre">Voie magique</p>';
	include "texte_voie_magique.php";
}

