<?php
$verif_connexion::verif_appel();
if ($type_lance == 5)
{   // sort lancé avec un objet, on indique l'objet utilisé (on vérifiera que le sort lancé est bien celui de l'objet)
    $req  = 'select prepare_objets_sorts(:perso_cod,:objsort_cod,:sort_cod) as resultat; ';
    $stmt = $pdo->prepare($req);
    $pdo->execute(
        array(':perso_cod'   => $perso_cod,
              ':objsort_cod' => $_REQUEST["objsort_cod"],
              ':sort_cod'    => $sort_cod), $stmt
    );
}

$req  =
    'select ' . $prefixe . $sort->sort_fonction . '(:perso_cod,:cible,:type_lance) as resultat ';
$stmt = $pdo->prepare($req);
