<?php
$verif_connexion::verif_appel();
$code_monstre  = $_POST['code_monstre'];
$code_etage    = $_POST['etage'];
$adapterNiveau = (isset($_POST['adapter'])) ? 'true' : 'false';
$antres        = (isset($_POST['antres']));
$eparpillement = $_POST['eparpillement'];
$where         = '';

$req_invasion = "select gmon_nom from monstre_generique where gmon_cod = :monstre";
$stmt         = $pdo->prepare($req_invasion);
$stmt         = $pdo->execute(array(":monstre" => $code_monstre), $stmt);
$result       = $stmt->fetch();
$nom_monstre  = $result['gmon_nom'];
