<?php
$verif_connexion::verif_appel();
$stmt   = $pdo->prepare($req_deplace);
$stmt   = $pdo->execute(array(
                            ':perso_cod' => intval($perso_cod)
                        ), $stmt);
$retour = $stmt->fetch();
$result = explode(';', $retour['res']);
