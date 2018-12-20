<?php
include "blocks/_tests_appels_page_externe.php";

if (!isset($cbar_cod))
    $cbar_cod = -1;

echo '<div class="bordiv" style="padding:0; margin-left: 205px;">';
echo '<div class="barrTitle">Concours de barde</div><br />';

// Nombre maximal de membres du jury
$nbJury = 10;

// Validations de formulaire

switch ($methode) {
    case 'barde_modif':    // Modification d’un concours existant
        $form_cod = pg_escape_string($_POST['form_cod']);
        $form_saison = "cbar_saison='" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_saison']))) . "',";
        $form_date_ouverture = "cbar_date_ouverture='" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
        $form_date_teaser = "cbar_date_teaser='" . pg_escape_string($_POST['form_date_teaser']) . "'::timestamp,";
        $form_fermeture = "cbar_fermeture='" . pg_escape_string($_POST['form_fermeture']) . "'::timestamp,";
        $form_description = "cbar_description='" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_description']))) . "'";

        include 'blocks/_admin_concours_barde.php';

        $log = date("d/m/y - H:i") . "\tCompte $compt_cod modifie le concours de bardes $form_saison (id = $form_cod).\n";
        writelog($log, 'animation_concours_barde');
        break;
    case 'barde_creation':    // Création d’un concours
        $form_saison = "'" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_saison']))) . "',";
        $form_date_ouverture = "'" . pg_escape_string($_POST['form_date_ouverture']) . "'::timestamp,";
        $form_date_teaser = "'" . pg_escape_string($_POST['form_date_teaser']) . "'::timestamp,";
        $form_fermeture = "'" . pg_escape_string($_POST['form_fermeture']) . "'::timestamp,";
        $form_description = "'" . pg_escape_string(htmlspecialchars(str_replace('\'', '’', $_POST['form_description']))) . "'";

        include "blocks/_admin_concours_barde2.php";

        $log = date("d/m/y - H:i") . "\tCompte $compt_cod crée le concours de bardes $form_saison.\n";
        writelog($log, 'animation_concours_barde');
        break;
    default:
        break;
}

include "blocks/_admin_concours_barde3.php";