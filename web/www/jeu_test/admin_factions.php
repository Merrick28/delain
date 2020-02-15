<?php
include "blocks/_header_page_jeu.php";

function ecrireResultatEtLoguer($texte, $sql = '')
{
    global $compt_cod;
    $pdo = new bddpdo;

    if ($texte) {
        $log_sql = false;    // Mettre à true pour le debug des requêtes

        if (!$log_sql || $sql == '')
            $sql = "\n";
        else
            $sql = "\n\t\tRequête : $sql\n";

        $req = "select compt_nom from compte where compt_cod = $compt_cod";
        $stmt = $pdo->query($req);
        $result = $stmt->fetch();
        $compt_nom = $result['compt_nom'];

        $en_tete = date("d/m/y - H:i") . "\tCompte $compt_nom ($compt_cod)\t";
        echo "<div style='padding:10px;'>$texte<pre>$sql</pre></div><hr />";
        writelog($en_tete . $texte . $sql, 'factions');
    }
}

//
//Contenu de la div de droite
//

ob_start();

// Liste des animations possibles
$erreur = 0;

$droit_modif = 'dcompt_factions';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0) {
    define("APPEL", 1);

    $methode           = get_request_var('methode', 'debut');

    // Choix de l’onglet
    $script = '';
    $lesMethodes = array(
        'faction' => array(
            'faction_modif',
            'faction_ajout',
            'faction_supprime',
            'faction_restaure'
        ),
        'faction_mission' => array(
            'faction_mission_ajout',
            'faction_mission_modif',
            'faction_mission_supprime'
        ),
        'faction_relations' => array(
            'faction_relations_modif'
        ),
        'faction_rang' => array(
            'rang_modif',
            'rang_ajout',
            'rang_supprime'
        ),
        'faction_lieu' => array(
            'lieu_modif_mission',    // Pas utilisé, dans un premier temps
            'lieu_ajout_mission',    // Pas utilisé, dans un premier temps
            'lieu_supprime_mission',    // Pas utilisé, dans un premier temps
            'lieu_ajout',
            'lieu_supprime'
        ),
        'mission' => array(
            'mission_modif',
            'mission_ajout',
            'mission_supprime'
        )
    );

    $onglet = 'aucun';
    $onglet = (in_array($methode, $lesMethodes['faction'])) ? 'faction' : $onglet;
    $onglet = (in_array($methode, $lesMethodes['faction_mission'])) ? 'faction_mission' : $onglet;
    $onglet = (in_array($methode, $lesMethodes['faction_relations'])) ? 'faction_relations' : $onglet;
    $onglet = (in_array($methode, $lesMethodes['faction_rang'])) ? 'faction_rang' : $onglet;
    $onglet = (in_array($methode, $lesMethodes['faction_lieu'])) ? 'faction_lieu' : $onglet;
    $onglet = (in_array($methode, $lesMethodes['mission'])) ? 'mission' : $onglet;

    if ($onglet == 'aucun' && isset($_GET['onglet']))
        $onglet = $_GET['onglet'];

    $page_include = '';
    $style_mission = '';
    $style_lieu = '';
    $style_rang = '';
    $style_faction_relations = '';
    $style_faction_mission = '';
    $style_faction = '';

    switch ($onglet) {
        case 'faction':    // Gestion des factions
            $page_include = 'admin_factions_modif.php';
            $style_faction = 'style="font-weight:bold;"';
            break;

        case 'faction_mission':    // Missions disponibles pour chaque faction
            $page_include = 'admin_factions_missions_dispo.php';
            $style_faction_mission = 'style="font-weight:bold;"';
            break;

        case 'faction_relations':    // Relations entre les factions
            $page_include = 'admin_factions_relations.php';
            $style_faction_relations = 'style="font-weight:bold;"';
            break;

        case 'faction_rang':    // Paramétrage des types de missions
            $page_include = 'admin_factions_rangs.php';
            $style_rang = 'style="font-weight:bold;"';
            break;

        case 'faction_lieu':    // Paramétrage des types de missions
            $page_include = 'admin_factions_lieux.php';
            $style_lieu = 'style="font-weight:bold;"';
            break;

        case 'mission':    // Paramétrage des types de missions
            $page_include = 'admin_factions_missions.php';
            $style_mission = 'style="font-weight:bold;"';
            break;

        default:
            break;
    }

    echo "<h1><strong><big>Gestion des factions</big></strong></h1><table width='100%'><tr>
		<td width='16%'><a href='?onglet=faction' $style_faction>Factions</a></td>
		<td width='17%'><a href='?onglet=mission' $style_mission>Missions</a></td>
		<td width='17%'><a href='?onglet=faction_mission' $style_faction_mission>Missions disponibles par faction</a></td>
		<td width='17%'><a href='?onglet=faction_relations' $style_faction_relations>Relations entre factions</a></td>
		<td width='16%'><a href='?onglet=faction_rang' $style_rang>Rangs</a></td>
		<td width='17%'><a href='?onglet=faction_lieu' $style_lieu>Lieux</a></td></tr></table></div><br />";

    if ($page_include != '')
        include_once $page_include;
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
