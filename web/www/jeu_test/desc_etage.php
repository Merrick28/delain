<?php
include "blocks/_header_page_jeu.php";

define("MARCHE_LENO", 93);


$req = "select etage_libelle, etage_description, etage_numero from etage,positions,perso_position where pos_cod = ppos_pos_cod and ppos_perso_cod = $perso_cod and pos_etage = etage_numero ";
$stmt = $pdo->query($req);
$result = $stmt->fetch();
$etage_numero = $result['etage_numero'];
$contenu_page .= "<p>Vous êtes dans le lieu : <strong>" . $result['etage_libelle'] . "</strong><br>";
$contenu_page .= "<p><em>" . $result['etage_description'] . "</em>";

$contenu_page .= "<p style=\"text-align:center;\"><a href=\"frame_vue.php\">Retour à la vue !</a></p>";

$droit_modif = 'dcompt_modif_perso';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";


if ($etage_numero == MARCHE_LENO && $erreur == 0)
{
    $contenu_page .= '<hr /><br /><div class="titre">Concours d’alchimie</div><p><em>Attention ! Les résultats visibles ci-dessous ne sont valables que pour ces deux dernières semaines !</em></p>';

    $perso = (isset($_GET['perso'])) ? $_GET['perso'] : 0;

    // Première partie de la page : liste des personnages ayant pratiqué
    $req = 'select distinct perso_cod, perso_nom
		from perso
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		inner join ligne_evt on levt_perso_cod1 = perso_cod
		where pos_etage = ' . MARCHE_LENO . '
			and levt_tevt_cod = 91';
    $stmt = $pdo->query($req);

    $contenu_page .= '<p><strong>Liste des persos présents à l’étage et ayant entrepris des actions d’alchimie ces 15 derniers jours.</strong> Cliquez sur un perso pour voir le détail.</p>';
    $contenu_page .= '<p>';
    $nom_du_perso = '-- non participant --';
    while ($result = $stmt->fetch())
    {
        if ($perso == $result['perso_cod']) $contenu_page .= '<strong>';
        $contenu_page .= '- <a href="?perso=' . $result['perso_cod'] . '">' . $result['perso_nom'] . '</a> -';
        if ($perso == $result['perso_cod']) $contenu_page .= '</strong>';
        if ($perso == $result['perso_cod']) $nom_du_perso = $result['perso_nom'];    // On récupère au passage le nom du perso sélectionné
    }
    $contenu_page .= '</p><br /><br />';

    if ($perso > 0)
    {
        $req = "select levt_date, levt_texte from ligne_evt
			where levt_perso_cod1 = $perso and levt_tevt_cod = 91
			order by levt_date desc";
        $stmt = $pdo->query($req);

        $contenu_page .= '
		<table cellspacing="2">
		<tr><td colspan="2" class="titre"><p class="titre">Actions alchimiques</p></td></tr>
		<tr>
			<td class="soustitre3"><p><strong>Date</strong></p></td>
			<td class="soustitre3"><p><strong>Détail</strong></p></td>
		</tr>';
        while ($result = $stmt->fetch())
        {
            $levt_date = $result['levt_date'];
            $levt_texte = str_replace('[perso_cod1]', $nom_du_perso, $result['levt_texte']);
            $levt_texte = str_replace('a fini sa potion !', '<strong>a fini sa potion !</strong>', $levt_texte);
            $contenu_page .= "<tr>";
            $contenu_page .= "<td class=\"soustitre3\"><p>$levt_date</p></td>";
            $contenu_page .= "<td class=\"soustitre3\"><p>$levt_texte</p></td>";
            $contenu_page .= "</tr>";
        }
        $contenu_page .= '</table><br />';
    }
}

$contenu_page .= '<hr /><br /><div class="titre">Missions</div>';
include "mission.php";

include "blocks/_footer_page_jeu.php";
