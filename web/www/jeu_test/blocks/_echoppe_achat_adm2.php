<?php
$verif_connexion::verif_appel();
$erreur = 0;
$total  = 0;
foreach ($obj as $key => $val)
{
    if ($val < 0)
    {
        echo "<p>Erreur ! Quantité négative !";
        $erreur = 1;
    }
    if ($val > 0)
    {
        $req    = "select gobj_valeur ";
        $req    = $req . "from objet_generique ";
        $req    = $req . "where gobj_cod = $key ";
        $stmt   = $pdo->query($req);
        $result = $stmt->fetch();
        $total  = $total + ($result['gobj_valeur'] * $val);
    }
}
$req    = "select lieu_compte from lieu where lieu_cod = $mag ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
if ($total > $result['lieu_compte'])
{
    echo "<p>Vous n'avez pas assez de brouzoufs pour acheter ce matériel !";
    $erreur = 1;
}
if ($erreur == 0)
{
    foreach ($obj as $key => $val)
    {
        if ($val > 0)
        {
            for ($cpt = 0; $cpt < $val; $cpt++)
            {
                // on crée l'objet
                $req     = "select nextval('seq_obj_cod') as num_objet ";
                $stmt    = $pdo->query($req);
                $result  = $stmt->fetch();
                $num_obj = $result['num_objet'];
                $req     = "insert into objets (obj_cod,obj_gobj_cod) values ($num_obj,$key)";
                $stmt    = $pdo->query($req);
                $req     = "insert into stock_magasin (mstock_obj_cod,mstock_lieu_cod) values ($num_obj,$mag) ";
                $stmt    = $pdo->query($req);
            }
        }
    }
    $req  = "update lieu set lieu_compte = lieu_compte - $total where lieu_cod = $mag ";
    $stmt = $pdo->query($req);
    echo "<p>Achat effectué pour un total de ", $total, " brouzoufs.";

}
