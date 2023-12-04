<?php
//perso2_description.php
$chemin = $type_flux . G_URL . "images/avatars/";
require_once G_CHE . "includes/fonctions.php";

$visu_perso = new perso;
if (isset($_REQUEST['visu'])) {
    $visu = $_REQUEST['visu'];
} else {
    $visu = $perso_cod;
}
$memeperso = false;
if ($visu == $perso_cod) {
    $memeperso = true;
}

if (!$visu_perso->charge($visu)) {
    die('Erreur sur le chargement de perso');
}

$visu_perso_nom = $visu_perso->perso_nom;

if ((!isset($contenu_page)) and ((basename($_SERVER['PHP_SELF'])) != 'perso2.php?m=4')) {
    $contenu_page = '';
}
if ($visu_perso->perso_avatar == '') {
    if ($visu_perso->perso_type_perso == 1) {
        $avatar = $chemin . "../" . $visu_perso->perso_race_cod . "_" . $visu_perso->perso_sex . ".png";
    } else {
        $avatar = $chemin . "../del.gif";
    }
} else {
    $avatar = $chemin . $visu_perso->perso_avatar . '?' . $visu_perso->perso_avatar_version;
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

$race = new race;
$race->charge($visu_perso->perso_race_cod);

$perobj        = new perso_objets();
$objets_portes = $perobj->getByPersoEquipe($visu_perso->perso_cod);


// détection des symbole d'équipe (chasuble) dans les objets portés : 1573 - Chasuble rouge / 1574 - Chasuble bleu
$jeu_equipe = array_values (array_filter($objets_portes, function($obj) { return in_array($obj->objet->obj_gobj_cod, [1573, 1574]); }));
$jeu_equipe = count( $jeu_equipe ) >0  ? G_IMAGES . $jeu_equipe[0]->objet_generique->gobj_image : "" ;

//  GUILDE
$pguilde     = new guilde_perso();
$guilde      = '';
$guilde_rang = '';
$isguilde    = false;

if ($isguilde = $pguilde->get_by_perso($visu_perso->perso_cod)) {
    $guilde = new guilde;
    $guilde->charge($pguilde->pguilde_guilde_cod);

    $guilde_rang = new guilde_rang();
    $guilde_rang->get_by_guilde_rang($guilde->guilde_cod, $pguilde->pguilde_rang_cod);
}
// RELIGION
$dieu        = new dieu();
$dieu_perso  = new dieu_perso();
$dieu_niveau = new dieu_niveau();

$isdieu = false;
if ($isdieu = $dieu_perso->getByPersoCod($visu_perso->perso_cod)) {
    $dieu_niveau->getByNiveauDieu($dieu_perso->dper_niveau, $dieu_perso->dper_dieu_cod);
    $dieu->charge($dieu_perso->dper_dieu_cod);
}


$perso_titre = new perso_titre;
$titres      = $perso_titre->getByPerso($visu_perso->perso_cod);

$perso_louche = new perso_louche();
$plouche      = $perso_louche->getByPerso($visu_perso->perso_cod);

$visu_monture = new perso();
$monture_avatar = "" ;
if ($visu_perso->perso_monture > 0)
{
    $visu_monture->charge($visu_perso->perso_monture);
    $monture_avatar = $chemin . $visu_monture->perso_avatar ;
}

$visu_cavalier = new perso();
$perso_cod_cavalier = $visu_perso->est_chevauche();
if ($perso_cod_cavalier)
{
    $visu_cavalier->charge($perso_cod_cavalier);
    $cavalier_avatar = $chemin . $visu_cavalier->perso_avatar ;
}


$template     = $twig->load('_perso2_description.twig');
$options_twig = array(

    'VISU_PERSO'    => $visu_perso,
    'MEMEPERSO'     => $memeperso,
    'AVATAR'        => $avatar,
    'RACE'          => $race,
    'PERSO_SEX_TXT' => $perso_sex_txt,
    'OBJETS_PORTES' => $objets_portes,
    'PGUILDE'       => $pguilde,
    'GUILDE'        => $guilde,
    'GUILDE_RANG'   => $guilde_rang,
    'ISDIEU'        => $isdieu,
    'DIEU'          => $dieu,
    'DIEU_NIVEAU'   => $dieu_niveau,
    'TITRES'        => $titres,
    'PLOUCHE'       => $plouche,
    'ISGUILDE'      => $isguilde,
    'MONTURE'       => $visu_monture,
    'MONTURE_IMG'   => $monture_avatar,
    'CAVALIER'      => $visu_cavalier,
    'CAVALIER_IMG'  => $cavalier_avatar,
    'JEU_EQUIPE'    => $jeu_equipe


);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));
