<?php
$verif_connexion::verif_appel();
$fonc_effet =
    !empty($_POST['fonc_effet' . $numero]) ? fonctions::format($_POST['fonc_effet' . $numero]) : '';
if (isset($_POST['fonc_cumulatif' . $numero])) $fonc_effet = $fonc_effet . '+';
$fonc_force        =
    !empty($_POST['fonc_force' . $numero]) ? fonctions::format($_POST['fonc_force' . $numero]) : '';
$fonc_duree        =
    !empty($_POST['fonc_duree' . $numero]) ? fonctions::format($_POST['fonc_duree' . $numero]) : '0';
$fonc_type_cible   =
    !empty($_POST['fonc_cible' . $numero]) ? fonctions::format($_POST['fonc_cible' . $numero]) : '';
$fonc_nombre_cible =
    !empty($_POST['fonc_nombre' . $numero]) ? fonctions::format($_POST['fonc_nombre' . $numero]) : '0';
$fonc_portee       =
    !empty($_POST['fonc_portee' . $numero]) ? fonctions::format($_POST['fonc_portee' . $numero]) : '0';
$fonc_proba        =
    !empty($_POST['fonc_proba' . $numero]) ? fonctions::format($_POST['fonc_proba' . $numero]) : '0';
$fonc_message      =
    !empty($_POST['fonc_message' . $numero]) ? fonctions::format($_POST['fonc_message' . $numero]) : '';

$fonc_proba = str_replace(',', '.', $fonc_proba);

