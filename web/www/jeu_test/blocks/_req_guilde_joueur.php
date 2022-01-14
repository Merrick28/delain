<?php
$verif_connexion::verif_appel();
// on cherche la guilde dans laquelle est le joueur
$req_guilde =
    "select guilde_cod,guilde_nom,rguilde_libelle_rang,pguilde_rang_cod,rguilde_admin,pguilde_message from guilde,guilde_perso,guilde_rang 
        where pguilde_perso_cod =:perso_cod
      and pguilde_guilde_cod = guilde_cod 
      and rguilde_guilde_cod = guilde_cod 
      and rguilde_rang_cod = pguilde_rang_cod
      and pguilde_valide = 'O' ";
$stmt       = $pdo->prepare($req_guilde);
$stmt       = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);