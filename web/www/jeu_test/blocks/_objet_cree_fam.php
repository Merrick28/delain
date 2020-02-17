<?php
$verif_connexion::verif_appel();
$typefam      = mt_rand(0, 1) + 192;    // 192 combat, 193 distance
$contenu_page .= "<p><strong>Un familier sort de l’œuf, il a l’air de vous apprécier et de vous suivre. </strong><br></p>";
$req_monstre  = "select cree_monstre_pos($typefam, $perso_position) as num";
$stmt         = $pdo->query($req_monstre);
$result       = $stmt->fetch();
$num_fam      = $result['num'];
$nom2         = 'Familier de ' . pg_escape_string($perso_nom);
$req_monstre  =
    "update perso set perso_nom = e'$nom2', perso_lower_perso_nom = lower(e'$nom2'), perso_type_perso = 3 "
    . "where perso_cod = $num_fam";
$stmt         = $pdo->query($req_monstre);
$req_etat     =
    "insert into perso_familier (pfam_perso_cod,pfam_familier_cod) values ($perso_cod,$num_fam)";
$stmt         = $pdo->query($req_etat);
// Ajout à la coterie du maître
$req_coterie =
    "select pgroupe_groupe_cod from groupe_perso where pgroupe_perso_cod=$perso_cod and pgroupe_statut = 1";
$stmt        = $pdo->query($req_coterie);
