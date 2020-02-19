<?php
$verif_connexion::verif_appel();
$nb_tobj  = 0;
$req_tobj = "select gobj_cod, gobj_nom, tobj_libelle, gobj_valeur from objet_generique
			inner join type_objet on tobj_cod = gobj_tobj_cod
			order by tobj_libelle, gobj_nom";
$stmt     = $pdo->query($req_tobj);
while ($result = $stmt->fetch())
{
    $gobj_nom     = $result['gobj_nom'];
    $gobj_nom     = str_replace("\"", "", $gobj_nom);
    $tobj_libelle = str_replace("\"", "", $result['tobj_libelle']);
    $gobj_valeur  = $result['gobj_valeur'];
    echo("listeBase[$nb_tobj] = new Array(0); \n");
    echo("listeBase[$nb_tobj][0] = \"" . $result['gobj_cod'] . "\"; \n");
    echo("listeBase[$nb_tobj][1] = \"" . $gobj_nom . "\"; \n");
    echo("listeBase[$nb_tobj][2] = \"" . $tobj_libelle . "\"; \n");
    echo("listeBase[$nb_tobj][3] = \"" . $gobj_valeur . "\"; \n");
    $nb_tobj++;
}
