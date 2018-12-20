<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 19/12/2018
 * Time: 15:48
 */
$db->query("UPDATE concours_barde SET $form_saison $form_date_ouverture $form_date_teaser $form_fermeture $form_description WHERE cbar_cod=$form_cod");

// Modification des jurys
for ($i = 1; $i <= $nbJury; $i++)
{
    $form_jury = pg_escape_string($_POST["form_jury$i"]);
    $form_jury_cod = (isset($_POST["form_jury_cod$i"])) ? $_POST["form_jury_cod$i"] : '';
    // Cas update
    if ($form_jury != '' && $form_jury_cod != '')
        $db->query("UPDATE concours_barde_jury SET jbar_perso_cod = $form_jury WHERE jbar_cod=$form_jury_cod");
    // Cas delete
    if ($form_jury == '' && $form_jury_cod != '')
        $db->query("DELETE FROM concours_barde_jury WHERE jbar_cod=$form_jury_cod");
    // Cas insert
    if ($form_jury != '' && $form_jury_cod == '')
        $db->query("INSERT INTO concours_barde_jury(jbar_cbar_cod, jbar_perso_cod) VALUES($form_cod, $form_jury)");
}
echo '<p>Modification effectuée</p>';
$methode = 'barde_visu';