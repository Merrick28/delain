<?php
/**
 * Created by PhpStorm.
 * User: pypg670
 * Date: 19/12/2018
 * Time: 15:50
 */
$req_nextval = "select nextval('concours_barde_cbar_cod_seq') as cbar_cod";
$stmt = $pdo->query($req_nextval);
$result = $stmt->fetch();
$cbar_cod = $result['cbar_cod'];

$db->query("INSERT INTO concours_barde (cbar_cod, cbar_saison, cbar_date_ouverture, cbar_date_teaser, cbar_fermeture, cbar_description) VALUES ($cbar_cod, $form_saison $form_date_ouverture $form_date_teaser $form_fermeture $form_description)");

// Modification des jurys
for ($i = 1; $i <= 5; $i++) {
    $form_jury = pg_escape_string($_POST["form_jury$i"]);

    if ($form_jury != '')
        $db->query("INSERT INTO concours_barde_jury(jbar_cbar_cod, jbar_perso_cod) VALUES($cbar_cod, $form_jury)");
}
echo '<p>Création effectuée</p>';
$methode = 'barde_visu';