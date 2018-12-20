<?php
include "blocks/_header_page_jeu.php";
//
// initialisation tableau
//
$mess[0] = 'Caractéristiques';
$mess[1] = 'Compétences';
$mess[2] = 'Bonus / malus';
$mess[3] = 'Combat';
$mess[4] = 'Description';
$mess[5] = 'Quêtes et trophées';
$mess[6] = 'Divers';
$nb = count($mess);
$size = round(100 / $nb);
//
// Si pas de parametres passés
//
if (!isset($m)) {
    $m = 0;
    $req = "select bonus_valeur from bonus where bonus_perso_cod = $perso_cod ";
    $db->query($req);
    if ($db->nf() != 0)
        $m = 2;
    if ($db->is_locked($perso_cod))
        $m = 3;
}

$contenu_page .= '<table cellspacing="0" cellpadding="0" width="100%">
	<tr>';
for ($cpt = 0; $cpt < $nb; $cpt++) {
    $lien = '<a href="' . $PHP_SELF . '?m=' . $cpt . '">';
    $f_lien = '</a>';
    if ($cpt == $m) {
        $style = 'onglet';
    } else {
        $style = 'pas_onglet';

    }
    $contenu_page .= '<td width="' . $size . '%" class="' . $style . '" style="text-align:center">' . $lien . $mess[$cpt] . $f_lien . '</td>';
}

$contenu_page .= '</tr><tr>';
$contenu_page .= '<td colspan="' . $nb . '" class="reste_onglet"><div class="centrer">';
if ($m == 0)    // Caractéristiques
    include "perso2_carac.php";
else if ($m == 1)                    // caracs
    include "perso2_comp.php";
else if ($m == 2)                    // compétences
    include "perso2_bonus.php";
else if ($m == 3)                    // bonus malus
    include "perso2_combat.php";
else if ($m == 4)                    // combat
{
    $visu = $perso_cod;
    include "perso2_description.php";
} else if ($m == 5)                    // Chasse et quêtes
    include "perso3_divers.php";
else if ($m == 6)                    // Divers
    include "perso2_divers.php";

$contenu_page .= '</div></td></tr></table>';
include "blocks/_footer_page_jeu.php";
