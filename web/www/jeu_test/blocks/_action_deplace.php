<?php
$verif_connexion::verif_appel();
$req_deplace  = 'select passage(:perso_cod) as deplace';
$stmt         = $pdo->prepare($req_deplace);
$stmt         = $pdo->execute(array(
                                  ':perso_cod' => intval($perso_cod)
                              ), $stmt);
$retour       = $stmt->fetch();
$result       = explode('#', $retour['deplace']);
$contenu_page .= $result[0];
$contenu_page .= '<br />';
