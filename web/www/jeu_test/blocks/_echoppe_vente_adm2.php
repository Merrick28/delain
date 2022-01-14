<?php
$verif_connexion::verif_appel();
$erreur = 0;
foreach ($obj as $key => $val)
{
    $req    = "select gobj_nom,count(obj_cod) as nombre ";
    $req    = $req . "from objets,objet_generique,stock_magasin ";
    $req    = $req . "where gobj_cod = $key ";
    $req    = $req . "and obj_gobj_cod = gobj_cod ";
    $req    = $req . "and mstock_obj_cod = obj_cod ";
    $req    = $req . "and mstock_lieu_cod = $mag ";
    $req    = $req . "group by gobj_nom ";
    $stmt   = $pdo->query($req);
    $result = $stmt->fetch();
    if ($val > $result['nombre'])
    {
        echo "<p>Erreur ! Vous essayez de vendre l'objet <strong>" . $result['gobj_nom'] . "</strong> en trop grande quantité !";
        $erreur = 1;
    }
}
if ($erreur == 0)
{
    $gagne = 0;
    foreach ($obj as $key => $val)
    {
        for ($cpt = 0; $cpt < $val; $cpt++)
        {
            // on enlève du magasin
            $req    = "select obj_cod ";
            $req    = $req . "from objets,objet_generique,stock_magasin ";
            $req    = $req . "where gobj_cod = $key ";
            $req    = $req . "and obj_gobj_cod = gobj_cod ";
            $req    = $req . "and mstock_obj_cod = obj_cod ";
            $req    = $req . "and mstock_lieu_cod = $mag ";
            $req    = $req . "limit 1";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $objet  = $result['obj_cod'];
            $req    = "delete from stock_magasin where mstock_obj_cod = $objet ";
            $stmt   = $pdo->query($req);
            $req    = "select f_del_objet($objet) ";
            $stmt   = $pdo->query($req);
            // on ajoute les sous
            $req    = "select gobj_valeur from objet_generique where gobj_cod = $key ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $gagne  = $gagne + $result['gobj_valeur'];
        }

    }
    $req  = "update lieu set lieu_compte = lieu_compte + $gagne where lieu_cod = $mag ";
    $stmt = $pdo->query($req);
    echo "<p>Transacstion effectuée pour $gagne brouzoufs. ";
}
