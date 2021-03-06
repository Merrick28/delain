<?php
include "blocks/_header_page_jeu.php";
ob_start();

$perso = new perso;
$perso = $verif_connexion->perso;

$erreur = 0;
if ($perso->nb_or_case() < 1)
{
    echo "<p>Il n'y a pas de tas de brouzoufs à regrouper sur cette case !";
    $erreur = 1;
}

$nb_pa = $perso->perso_pa;
if ($nb_pa < $param->getparm(38))
{
    echo "<p>Vous n'avez pas assez de PA pour regrouper les tas de brouzoufs sur cette case !";
    $erreur = 1;
}
if ($erreur == 0)
{
    $req      = "select ppos_pos_cod from perso_position where ppos_perso_cod = $perso_cod";
    $stmt     = $pdo->query($req);
    $result   = $stmt->fetch();
    $position = $result['ppos_pos_cod'];

    $qte  = 0;
    $req  = "select * from or_position where por_pos_cod = $position ";
    $stmt = $pdo->query($req);

    // on compte
    $farfa = 0;
    while ($result = $stmt->fetch())
    {
        if ($result['por_palpable'] == 'O')
        {
            $qte = $qte + $result['por_qte'];
        } else
        {
            $farfa = $farfa + 1;
        }
    }

    // on efface
    $req  = "delete from or_position where por_pos_cod = $position";
    $stmt = $pdo->query($req);

    // on recrée
    $req  = "insert into or_position (por_pos_cod, por_qte) values ($position,$qte) ";
    $stmt = $pdo->query($req);

    $req_ins_evt =
        "select insere_evenement($perso_cod, $perso_cod, 22, '[attaquant] a regroupé les brouzoufs sur sa case', 'O', '[pos_cod, qte]=$position,$qte') ";
    $stmt        = $pdo->query($req_ins_evt);

    $perso->perso_pa = $perso->perso_pa - $param->getparm(38);
    $perso->stocke();

    if ($farfa == 0)
    {
        echo "<p>Vous avez regroupé tous les tas de brouzoufs existants en un tas de $qte brouzoufs. ";
    } else
    {
        echo "<p>Vous avez regroupé les tas de brouzoufs existants en un tas de $qte brouzoufs. Certains tas ont disparu, il devait certainement s'agir de brouzoufs de farfadets....";
    }

}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
