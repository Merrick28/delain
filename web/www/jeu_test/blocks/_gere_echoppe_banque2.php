<?php
$verif_connexion::verif_appel();
$req    = "select lieu_compte,perso_po ";
$req    = $req . "from lieu,perso ";
$req    = $req . "where lieu_cod = $mag ";
$req    = $req . "and perso_cod = $perso_cod ";
$stmt   = $pdo->query($req);
$result = $stmt->fetch();
$banque = $result['lieu_compte'];
$erreur = 0;
if (!isset($qte))
{
    echo "<p>Erreur ! Quantité non définie !";
    $erreur = 1;
}
if ($qte < 0)
{
    echo "<p>Erreur ! Quantité négative !";
    $erreur = 1;
}
if ($qte > $banque)
{
    echo "<p>Erreur ! Pas assez de brouzoufs à retirer !";
    $erreur = 1;
}
if ($erreur == 0)
{
    // message
    $req     =
        "select perso_nom,nextval('seq_msg_cod') as message from perso where perso_cod = $perso_cod ";
    $stmt    = $pdo->query($req);
    $result  = $stmt->fetch();
    $nom     = str_replace("'", "\'", $result['perso_nom']);
    $message = $result['message'];
    $req     =
        "insert into messages (msg_cod,msg_corps,msg_titre,msg_date,msg_date2) values ($message,'$nom a effectué un retrait de $qte brouzoufs','Retrait',now(),now()) ";
    $stmt    = $pdo->query($req);
    $req     = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod) values ($message,$perso_cod) ";
    $stmt    = $pdo->query($req);
    if ($tab_lieu['lieu']->type_lieu == 11)
    {
        $req =
            "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe = 'O' ";
    }
    if ($tab_lieu['lieu']->type_lieu == 9)
    {
        $req =
            "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe = 'O' ";
    }
    if ($tab_lieu['lieu']->type_lieu == 21)
    {
        $req =
            "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod) select $message,perso_cod from perso where perso_admin_echoppe_noir = 'O' ";
    }
    $stmt = $pdo->query($req);


    $req  = "update lieu set lieu_compte = lieu_compte - $qte where lieu_cod = $mag ";
    $stmt = $pdo->query($req);
    $req  = "update perso set perso_po = perso_po + $qte where perso_cod = $perso_cod ";
    $stmt = $pdo->query($req);
    echo "<p>La transaction a été effectuée.";

}
