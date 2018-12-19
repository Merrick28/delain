<?php
include "blocks/_header_page_jeu.php";

$contenu_page .= '<script language="javascript" src="../scripts/cocheCase.js"></SCRIPT>';
//
// initialisation tableau
//
$mess[0] = 'Boite de réception';
$mess[1] = 'Archives';
$mess[2] = 'Nouveau message';
$mess[3] = 'Boite d’envoi';
$mess[4] = 'Listes de diffusion';
$mess[5] = 'Fils de discussion';
$nb = count($mess);
//
// Si pas de parametres passés
//
if (!isset($m))
    $m = 0;
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
    $contenu_page .= '<td class="' . $style . '"><div style="text-align:center">' . $lien . $mess[$cpt] . $f_lien . '</div></td>';
}

$contenu_page .= '</tr><tr>';
$contenu_page .= '<td colspan="' . $nb . '" class="reste_onglet"><center>';
if (($m == 0) || ($m == 1))    // Des messages à afficher
    include "mess_l.php";
else if ($m == 2) {                // Nouveau message
    if (isset($_GET['mavtest']) && $_GET['mavtest'] == 1) {
        include "mess_n2.php";
    } else {
        include "mess_n.php";
    }
} else if ($m == 3)                    // Boite d'envoi
    include "mess_e.php";
else if ($m == 4)                    // Listes de diffusion
    include "mess_liste.php";
else if ($m == 5)
    include "mess_fils.php";
$contenu_page .= '</center></td></tr></table>';

include "blocks/_footer_page_jeu.php";

