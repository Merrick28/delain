<?php
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$compt_cod = $verif_connexion->compt_cod;
$compte    = $verif_connexion->compte;


$perso_cible = $_REQUEST['perso'];
$pdo         = new bddpdo();
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://www.jdr-delain.net//css/delain.css" rel="stylesheet">
<head>
    <title>Suppression de perso</title>
</head>
<body background="../images/fond5.gif">
<div class="bordiv">
    <?php

    $erreur          = 0;
    $perso_a_effacer = new perso;
    if (!$perso_a_effacer->charge($perso_cible))
    {
        die('erreur sur le chargement de perso');
    }

    $perso_dcreat = new DateTime($perso_a_effacer->perso_dcreat);
    date_add($perso_dcreat, date_interval_create_from_date_string('1 days'));
    $now = date_create();
    if ($perso_dcreat > $now)
    {
        $erreur = 1;
    }


    $type_perso = $perso_a_effacer->perso_type_perso;

    if ($type_perso == 3)
    {
        $req       =
            "select pcompt_compt_cod from perso_familier,perso_compte where pfam_familier_cod = :perso_cible 
                            and pfam_perso_cod = pcompt_perso_cod";
        $stmt      = $pdo->prepare($req);
        $stmt      = $pdo->execute(array(":perso_cible" => $perso_cible), $stmt);
        $result    = $stmt->fetch();
        $compt_fam = $result['pcompt_compt_cod'];
    } else
    {
        $compt_fam = -1;
    }


    $req        =
        "select pcompt_perso_cod, perso_type_perso 
            from perso_compte, perso 
            where pcompt_perso_cod = :perso_cible
              and pcompt_compt_cod = :compt_cod
              and pcompt_perso_cod = perso_cod";
    $stmt       = $pdo->prepare($req);
    $stmt       = $pdo->execute(array(":perso_cible" => $perso_cible,
                                      ":compt_cod"   => $compt_cod), $stmt);

    //if ($result = $stmt->fetch() and $type_perso != 3) // Non rattaché au compte, pas un familier.
    if (!$compte->autoriseJouePerso($perso_cible))
    {
        echo "Vous êtes en train de tenter de supprimer un personnage qui n’est pas rattaché à votre compte !";
    } else if ($erreur == 1)  // Mois de 24 heures depuis la création du perso
    {
        echo 'Allons allons...  Cet aventurier a moins de 24 heures d’existence, ',
        'laissez-le donc vivre un peu plus...';
    } else if ($type_perso == 2) // Cas d’un monstre contrôlé par le compte. Pas de suppression, juste un détachement.
    {
        $perso_compte = new perso_compte;
        $perso_compte->get_by_perso($perso_cible);

        $perso_dcreat = new DateTime($perso_compte->pcompt_date_attachement);
        date_add($perso_dcreat, date_interval_create_from_date_string('1 days'));

        if ($perso_dcreat > $now)
        {
            echo '<p>Ce monstre vous a été affecté trop récemment, gardez-le au moins 24 heures !</p>';
        }

        {
            $req1   = "select relache_monstre_4e_perso($perso_cible, 2::smallint) as resultat";
            $stmt   = $pdo->prepare($req1);
            $stmt   = $pdo->execute(array(":cible" => $perso_cible), $stmt);
            $result = $stmt->fetch();
            echo '<p>' . $result['resultat'] . '</p>';
        }
    } else
    {
        $perso_a_effacer->perso_nom   = $perso_a_effacer->perso_nom . '_inactif';
        $perso_a_effacer->perso_actif = 'N';
        $perso_a_effacer->stocke();

        $req1   = "update perso set perso_nom = perso_nom||'_inactif' where perso_cod = :perso ";
        $stmt   = $pdo->prepare($req1);
        $stmt   = $pdo->execute(array(":perso" => $perso_cible), $stmt);
        $req1   = "delete from lock_combat where lock_cible = :perso ";
        $stmt   = $pdo->prepare($req1);
        $stmt   = $pdo->execute(array(":perso" => $perso_cible), $stmt);
        $req1   = "delete from lock_combat where lock_attaquant = :perso ";
        $stmt   = $pdo->prepare($req1);
        $stmt   = $pdo->execute(array(":perso" => $perso_cible), $stmt);
        $req1   = "delete from perso_familier where pfam_familier_cod = :perso ";
        $stmt   = $pdo->prepare($req1);
        $stmt   = $pdo->execute(array(":perso" => $perso_cible), $stmt);
        $req1   = "select pfam_familier_cod from perso_familier where pfam_perso_cod = :perso ";
        $stmt   = $pdo->prepare($req1);
        $stmt   = $pdo->execute(array(":perso" => $perso_cible), $stmt);
        $allfam = $stmt->fetchAll();
        if (count($allfam) != 0)
        {
            $req  = "update perso set perso_type_perso = 2 where perso_cod in 
            (select pfam_familier_cod from perso_familier where pfam_perso_cod = :perso) ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso" => $perso_cible), $stmt);
        }

        $levt                  = new ligne_evt();
        $levt->levt_tevt_cod   = 23;
        $levt->levt_perso_cod1 = $perso_cible;
        $levt->levt_texte      = 'Le personnage a été supprimé !';
        $levt->levt_lu         = 'O';
        $levt->levt_visible    = 'O';
        $levt->stocke(true);


        echo("<p>Votre perso a été supprimé !");
    }
    echo "<a class='centrer' href=\"jeu_test/switch.php\">Retour !</a>";

    ?>
</div>
</body>
</html>
