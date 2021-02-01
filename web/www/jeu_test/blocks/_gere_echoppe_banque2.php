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
    $message             = new message();
    $message->sujet      = 'Retrait';
    $message->corps      = $perso->perso_nom . ' a effectué un retrait de ' . $qte . ' brouzoufs';
    $message->expediteur = $perso_cod;

    if ($tab_lieu['lieu']->type_lieu == 11)
    {
        $req  =
            " select perso_cod from perso where perso_admin_echoppe = 'O'  and perso_cod != 605745 ";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $message->ajouteDestinataire($result['perso_cod']);
        }
    }
    if ($tab_lieu['lieu']->type_lieu == 9)
    {
        $req  =
            " select perso_cod from perso where perso_admin_echoppe = 'O'  and perso_cod != 605745 ";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $message->ajouteDestinataire($result['perso_cod']);
        }
    }
    if ($tab_lieu['lieu']->type_lieu == 21)
    {
        $req  =
            " select perso_cod from perso where perso_admin_echoppe_noir = 'O'  and perso_cod != 605745 ";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $message->ajouteDestinataire($result['perso_cod']);
        }
    }
    $message->envoieMessage();


    $req  = "update lieu set lieu_compte = lieu_compte - $qte where lieu_cod = $mag ";
    $stmt = $pdo->query($req);

    $perso->perso_po = $perso->perso_po + $qte;
    $perso->stocke();
    echo "<p>La transaction a été effectuée.";

}
