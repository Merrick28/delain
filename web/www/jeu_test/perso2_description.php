<?php
//perso2_description.php
$chemin = $type_flux . G_URL . "images/avatars/";
require_once G_CHE . "includes/fonctions.php";

$visu_perso = new perso;
if(isset($_REQUEST['visu']))
{
    $visu = $_REQUEST['visu'];
}
else
{
    $visu = $perso_cod;
}
if (!$visu_perso->charge($visu)) {
    die('Erreur sur le chargement de perso');
}

$visu_perso_nom = $visu_perso->perso_nom;

if ((!isset($contenu_page)) and ((basename($_SERVER['PHP_SELF'])) != 'perso2.php?m=4')) {
    $contenu_page = '';
}

$contenu_page .= '<div style="text-align:center;"><table id="desc_perso"><tr><td><table>';
if ($visu_perso->perso_avatar == '') {
    if ($visu_perso->perso_type_perso == 1) {
        $avatar = $chemin . "../" . $visu_perso->perso_race_cod . "_" . $visu_perso->perso_sex . ".png";
    } else {
        $avatar = $chemin . "../del.gif";
    }
} else {
    $avatar = $chemin . $visu_perso->perso_avatar . '?' . $visu_perso->perso_avatar_version;
}
/*
//
// 1er avril
//
$avatar = $chemin . $aff_avat;
//
// fin 1er avril
//
*/
$contenu_page .= '<tr><td colspan="3" class="titre"><div class="titre">Fiche de ' . $visu_perso->perso_nom . '</div></td>
</tr>';

$desc  = '';
$desc2 = '';

if ($visu_perso->perso_description != '') {
    $desc         = nl2br(htmlspecialchars(str_replace('\'', '’', $visu_perso->perso_description)));
    $contenu_page .= '<tr><td colspan="3" class="soustitre2">' . $desc . '</td></tr>';
}
if ($_REQUEST['visu'] == $perso_cod) { // perso courant
    $contenu_page .= '<tr><td colspan="3"><div style="text-align:center;"><a href="change_desc_perso.php">Changer sa description ?</a></div></td></tr>';
}
if ($visu_perso->perso_desc_long != '' and $visu_perso->perso_desc_long != null) {
    $desc  = nl2br(htmlspecialchars($visu_perso->perso_desc_long));
    $desc2 = '<tr><td colspan="3" class="soustitre2">' . $desc . '</td></tr>';
}
if ($_REQUEST['visu'] == $perso_cod) {
    $desc2 .= '<tr><td colspan="3"><div style="text-align:center;"><a href="change_desc_perso.php">Changer sa description longue ?</a></div></td></tr>';
}

$contenu_page .= '<tr>
<td rowspan="12"><img src="' . $avatar . '" alt="Avatar de ' . $visu_perso->perso_nom . '">';
if ($_REQUEST['visu'] == $perso_cod) {
    if ($visu_perso->perso_crapaud == 0) {
        $contenu_page .= '<br><div style="text-align:center;"><a href="change_avatar_perso.php">Changer son avatar ?</a></div></td></tr>';
    }
}

if ($visu_perso->perso_sex == 'F') {
    if ((int)($visu_perso->perso_gmon_cod) > 0) {
        $perso_sex_txt = "Femelle";
    } else {
        $perso_sex_txt = "Féminin";
    }
} elseif ($visu_perso->perso_sex == 'M') {
    if ((int)$visu_perso->perso_gmon_cod > 0) {
        $perso_sex_txt = "Mâle";
    } else {
        $perso_sex_txt = "Masculin";
    }
} elseif ($visu_perso->perso_sex == 'A') {
    $perso_sex_txt = "Androgyne";
} elseif ($visu_perso->perso_sex == 'H') {
    $perso_sex_txt = "Hermaphrodite";
} else {
    $perso_sex_txt = "Inconnu";
}

$race = new race();
$race->charge($visu_perso->perso_race_cod);

$contenu_page .= '</td>
</tr>

<tr>
<td class="soustitre2">Race :</td>
<td>' . $race->race_nom . '</td>
</tr>

<tr>
<td class="soustitre2">Sexe :</td>
<td>' . $perso_sex_txt . '</td>
</tr>

<tr>
<td class="soustitre2">Renommee :</td>
<td>' . $visu_perso->f_vue_renommee() . ' (' . $visu_perso->get_renommee() . ' classique - ' .
                 $visu_perso->get_renommee_magie() . ' magie - ' .
                 $visu_perso->get_renommee_artisanat() . ' artisanat)</td>
</tr>

<tr>
<td class="soustitre2">Karma :</td>
<td>' . $visu_perso->get_karma() . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre de décès :</td>
<td>' . $visu_perso->perso_nb_mort . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre d’aventuriers tués :</td>
<td>' . $visu_perso->perso_nb_joueur_tue . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre de décès en arène :</td>
<td>' . $visu_perso->perso_nb_mort_arene . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre d’aventuriers tués en arène :</td>
<td>' . $visu_perso->perso_nb_joueur_tue_arene . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre de monstres tués :</td>
<td>' . $visu_perso->perso_nb_monstre_tue . '</td>
</tr>';

$perobj        = new perso_objets();
$objets_portes = $perobj->getByPersoEquipe($visu_perso->perso_cod);

$contenu_page .= '<tr><td class="soustitre2">Équipement porté :</td>';
if (count($objets_portes) == 0) {
    $contenu_page .= '<td>Aucun objet équipé</td>';
} else {
    $contenu_page .= '<td>';
    foreach ($objets_portes as $item) {
        $contenu_page .= $item->objet->obj_nom_porte . ' (' . $item->objet_generique->tobj_libelle . ')<br>';
    }
    $contenu_page .= '</td>';
}
$contenu_page .= '</tr>';
//  GUILDE
$contenu_page .= '<tr>
<td class="soustitre2">Guilde :</td>
<td>';
$pguilde      = new guilde_perso();
if ($pguilde->get_by_perso($visu_perso->perso_cod)) {
    $guilde = new guilde;
    $guilde->charge($pguilde->pguilde_guilde_cod);

    $guilde_rang = new guilde_rang();
    $guilde_rang->get_by_guilde_rang($guilde->guilde_cod, $pguilde->pguilde_rang_cod);


    $contenu_page .= '<a href="visu_guilde.php?num_guilde=' . $guilde->guilde_cod . '">' . $guilde->guilde_nom . '</a> (' . $guilde_rang->rguilde_libelle_rang;
    if ($guilde_rang->rguilde_admin == 'O') {
        $contenu_page .= " - Administrateur";
    }
    $contenu_page .= ')';
    if ($pguilde->pguilde_meta_noir == 'O') {
        $contenu_page .= '<br>Meta guildé à <strong>envoyés de Salm\'o\'rv</strong>';
    }

    if ($pguilde->pguilde_meta_milice == 'O') {
        $contenu_page .= '<br>Meta guildé à <strong>milice</strong>';
    }

    if ($pguilde->pguilde_meta_caravane == 'O') {
        $contenu_page .= '<br>Meta guildé à <strong>Corporation marchande du R.A.D.I.S</strong>';
    }
} else {
    $contenu_page .= 'Pas de guilde';
}
$contenu_page .= '</td></tr>';
// RELIGION
$dieu        = new dieu();
$dieu_perso  = new dieu_perso();
$dieu_niveau = new dieu_niveau();

if ($dieu_perso->getByPersoCod($visu_perso->perso_cod)) {
    $dieu_niveau->getByNiveauDieu($dieu_perso->dper_niveau, $dieu_perso->dper_dieu_cod);
    $dieu->charge($dieu_perso->dper_dieu_cod);
    $contenu_page .= '<tr>
			<td></td>
			<td class="soustitre2">Religion :</td>
			<td><strong>(" . $dieu_niveau->dniv_libelle . " de " . $dieu->dieu_nom . ")</strong></td>
			</tr>';
}


$contenu_page .= '</table></div>';

$perso_titre = new perso_titre;
$titres      = $perso_titre->getByPerso($visu_perso->perso_cod);
if (count($titres) != 0) {
    $contenu_page .= '<hr><div style="text-align:center;"><table>
	<tr><td colspan="2" class="titre">Titres obtenus</td></tr>
	<tr><td class="soustitre2">Titre</td><td class="soustitre2">Obtenu le</td></tr>';
    foreach ($titres as $titre) {
        $contenu_page .= '<tr><td><strong>' . $titre->ptitre_titre . '</strong></td><td>' . format_date($titre->ptitre_date) . '</td></tr></table></div>';
    }
}

$perso_louche = new perso_louche();
if ($perso_louche->getByPerso($visu_perso->perso_cod)) {
    $contenu_page .= '<div style="text-align:center;"><em><strong>Note de jeu : </strong>Cet individu cache sous sa veste et dans son sac quelque chose.<br>
	Il transpire abondemment et regarde autour de lui pour voir s\'il est suivi.<br>
	Le moindre blason de la milice semble le mettre dans un drole d\'état. Il n\'y a pas a dire il est pas net... </em></div>';
}
